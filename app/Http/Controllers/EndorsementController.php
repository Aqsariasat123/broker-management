<?php

namespace App\Http\Controllers;
use App\Models\LookupValue;
use Illuminate\Support\Facades\Log;

use App\Models\Endorsement;
use App\Models\Policy;
use Illuminate\Http\Request;

class EndorsementController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('endorsements.view') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view endorsements.');
        }
        if (!$request->filled('policy_id')) {
            return redirect()->route('policies.index')->with('error', 'Please select a policy first.');
        }
        $query = Endorsement::select(
            'endorsements.*',
            'policies.policy_code as policy_code',
            'lv.name as type_name'
        )
        ->join('policies', 'endorsements.policy_id', '=', 'policies.id')
        ->leftJoin('lookup_values as lv', 'endorsements.type', '=', 'lv.id')
        ->orderBy('endorsements.created_at', 'desc');
    
        if ($request->filled('policy_id')) {
            $query->where('policy_id', $request->policy_id);
        }
    
        $endorsements = $query->paginate(10);

        $policies = Policy::orderBy('policy_no')->get();
        $policy = Policy::findOrFail($request->policy_id); // current policy

        // Lookup values for selects - use Insurers instead of Income Source
        $types = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Endorsements');
        })->where('active', 1)->orderBy('seq')->get();


        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('endorsements');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('endorsements',);

       

        return view('endorsements.index', compact('endorsements','policy', 'policies', 'types','selectedColumns'));
    }


    public function store(Request $request)
    {
        try {
            if (!auth()->user()->hasPermission('endorsements.create') && !auth()->user()->isAdmin()) {
                abort(403, 'You do not have permission to create endorsements.');
            }
    
            $validated = $request->validate([
                'policy_id' => 'required|exists:policies,id',
                'endorsement_source_id' => 'required|exists:lookup_values,id',
                'date' => 'required|date',
                'description' => 'nullable|string|max:1000',
                'endorsement_notes' => 'nullable|string|max:2000',
                'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            ]);
    
            $data = [
                'policy_id' => $request->policy_id,
                'type' => $validated['endorsement_source_id'],
                'effective_date' => $validated['date'],
                'description' => $validated['description'] ?? null,
                'endorsement_notes' => $validated['endorsement_notes'] ?? null,
                'status' => $request->input('status', 'draft'),
            ];
    
            // Generate endorsement_no
            $latestEndorsement = Endorsement::orderBy('id', 'desc')->first();
            $nextId = $latestEndorsement ? $latestEndorsement->id + 1 : 1;
            $data['endorsement_no'] = 'END' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    
    
            $endorsement =  Endorsement::create($data);

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = 'endorsement_' .  $request->policy_id . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $filename, 'public');
                
                // Generate unique DOC ID
                $latest = \App\Models\Document::orderBy('id', 'desc')->first();
                $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
                $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    
                // Store in documents table - tie to expense using expense_id
                \App\Models\Document::create([
                    'doc_id' => $docId,
                    'tied_to' => $endorsement->id,
                    'name' => 'Endorsement',
                    'group' => 'Endorsement Document',
                    'type' => 'endorsement',
                    'format' => $file->getClientOriginalExtension(),
                    'date_added' => now(),
                    'year' => now()->format('Y'),
                    'file_path' => $path,
                ]);
            }
    

            return redirect()->route('endorsements.index', ['policy_id' => $policyId])
            ->with('success', 'Endorsement created successfully.');
           

    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error storing endorsement', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
    
        } catch (\Exception $e) {
            Log::error('Error storing endorsement', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }
    

 
    public function edit(Endorsement $endorsement)
    {
        if (!auth()->user()->hasPermission('endorsements.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit endorsements.');
        }
    
        // Load related documents if needed
        $endorsement->load('documents');
    
        // Return JSON if AJAX
        if (request()->ajax()) {
            return response()->json($endorsement);
        }
    
        // Fallback for normal requests (optional)
        $policies = Policy::orderBy('policy_no')->get();
        return view('endorsements.edit', compact('endorsement', 'policies'));
    }
    

    public function update(Request $request, Endorsement $endorsement)
    {
        if (!auth()->user()->hasPermission('endorsements.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit endorsements.');
        }

    

        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'endorsement_source_id' => 'required|exists:lookup_values,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'endorsement_notes' => 'nullable|string|max:2000',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $endorsement->update($validated);

        // Handle document upload - store in documents table
        if ($request->hasFile('document')) {
            // Delete old documents if exists
            $oldDocuments = \App\Models\Document::where('tied_to', $endorsement->id)
                ->where('group', 'Endorsement Document')
                ->get();
            
            foreach ($oldDocuments as $oldDoc) {
                if ($oldDoc->file_path && \Storage::disk('public')->exists($oldDoc->file_path)) {
                    \Storage::disk('public')->delete($oldDoc->file_path);
                }
                $oldDoc->delete();
            }
            
            $file = $request->file('document');
            $filename = 'endorsement_' . now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Store in documents table - tie to income using income_id
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $endorsement->id,
                'name' => 'Endorsement',
                'group' => 'Endorsement Document',
                'type' => 'endorsement',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }


         // Redirect back to endorsements page for the same policy
         return redirect()->route('endorsements.index', ['policy_id' => $endorsement->policy_id])
         ->with('success', 'Endorsement update successfully.');


        
    }

    public function destroy(Endorsement $endorsement)
    {
        if (!auth()->user()->hasPermission('endorsements.delete') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to delete endorsements.');
        }
    
        // Delete related documents
        $documents = \App\Models\Document::where('tied_to', $endorsement->id)
                    ->where('group', 'Endorsement Document')
                    ->get();
    
        foreach ($documents as $doc) {
            // Delete file from storage
            if ($doc->file_path && \Storage::disk('public')->exists($doc->file_path)) {
                \Storage::disk('public')->delete($doc->file_path);
            }
            // Delete DB record
            $doc->delete();
        }
    
        // Delete the endorsement itself
        $endorsement->delete();
    
        return redirect()->route('endorsements.index', ['policy_id' => $endorsement->policy_id])
                         ->with('success', 'Endorsement and related documents deleted successfully.');
    }
    
    public function export(Request $request)
    {
        // Get selected columns from session, fallback to default columns
        $selectedColumns = session('endorsements_columns', ['endorsement_no', 'policy_no', 'type', 'effective_date', 'status', 'description']);
    
        // Load endorsements with related policy

        $endorsements = Endorsement::select(
            'endorsements.*',
            'policies.policy_code as policy_code',
            'lv.name as type_name'
        )
        ->join('policies', 'endorsements.policy_id', '=', 'policies.id')
        ->leftJoin('lookup_values as lv', 'endorsements.type', '=', 'lv.id')
        ->orderBy('endorsements.created_at', 'desc')->get();

    
        $fileName = 'endorsements_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
    
        return response()->streamDownload(function() use ($endorsements, $selectedColumns) {
            $handle = fopen('php://output', 'w');
    
            // Write CSV header using selected columns
            fputcsv($handle, $selectedColumns);
    
            // Write data rows
            foreach ($endorsements as $e) {
                $row = [];
                foreach ($selectedColumns as $col) {
                    switch ($col) {
                        case 'policy_no':
                            $row[] = $e->policy_code;
                            break;
                        case 'endorsement_no':
                            $row[] = $e->endorsement_no;
                            break;
                        case 'type':
                            $row[] = $e->type_name;
                            break;
                        case 'effective_date':
                            $row[] = $e->effective_date;
                            break;
                        case 'status':
                            $row[] = $e->status;
                            break;
                        case 'description':
                            $row[] = $e->description;
                            break;
                        default:
                            $row[] = $e->$col ?? '';
                            break;
                    }
                }
                fputcsv($handle, $row);
            }
    
            fclose($handle);
        }, $fileName, $headers);
    }
    

    public function saveColumnSettings(Request $request)
    {
        // Read policy_id from query string (e.g., ?policy_id=27)
        $policyId = $request->policy_id;
    
        // Save selected columns in session
        session(['endorsements_columns' => $request->columns ?? []]);
        Log::info('Endorsement column settings saved', [
            'policy_id' => $policyId,
            'columns' => $request->columns ?? [],
            'user_id' => auth()->id(),
        ]);
    
        // Redirect back to endorsements page for the same policy
        return redirect()->route('endorsements.index', ['policy_id' => $policyId])
                         ->with('success', 'Column settings saved successfully.');
    }
    
}

