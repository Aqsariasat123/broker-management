<?php

namespace App\Http\Controllers;

use App\Models\Nominee;
use App\Models\Policy;
use App\Models\Document;
use Illuminate\Http\Request;

class NomineeController extends Controller
{
    public function index(Request $request)
    {
        $policyId = $request->get('policy_id');
        $policy = null;
        $nomineesQuery = Nominee::orderBy('created_at', 'desc');

        if ($policyId) {
            $policy = Policy::with('client')->findOrFail($policyId);
            $nomineesQuery->where('policy_id', $policyId);
        } else {
            $nomineesQuery->whereNull('policy_id');
        }

        $nominees = $nomineesQuery->paginate(10);

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('nominees');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('nominees');
        $columnDefinitions = $config['column_definitions'] ?? [];
        $mandatoryColumns = $config['mandatory_columns'] ?? [];

        return view('nominees.index', compact('nominees', 'policy', 'policyId', 'selectedColumns', 'columnDefinitions', 'mandatoryColumns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'nin_passport_no' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'share_percentage' => 'nullable|numeric|min:0|max:100',
            'policy_id' => 'nullable|exists:policies,id',
            'client_id' => 'nullable|exists:clients,id',
            'notes' => 'nullable|string',
        ]);

        // Generate unique Nominee Code
        $latest = Nominee::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('NM', '', $latest->nominee_code ?? 'NM0') + 1 : 1001;
        $validated['nominee_code'] = 'NM' . $nextId;

        $nominee = Nominee::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Nominee created successfully.',
                'nominee' => $nominee
            ]);
        }

        if (isset($validated['policy_id']) && $validated['policy_id']) {
            return redirect()->route('nominees.index', ['policy_id' => $validated['policy_id']])
                ->with('success', 'Nominee created successfully.');
        }

        // Redirect to nominees index without policy_id to show unlinked nominees
        return redirect()->route('nominees.index')
            ->with('success', 'Nominee created successfully. Please save the policy to link this nominee.');
    }

    public function update(Request $request, Nominee $nominee)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'nin_passport_no' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'share_percentage' => 'nullable|numeric|min:0|max:100',
            'date_removed' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $nominee->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Nominee updated successfully.',
                'nominee' => $nominee
            ]);
        }

        $redirectParams = $nominee->policy_id 
            ? ['policy_id' => $nominee->policy_id] 
            : [];
        
        return redirect()->route('nominees.index', $redirectParams)
            ->with('success', 'Nominee updated successfully.');
    }

    public function destroy(Request $request, Nominee $nominee)
    {
        $policyId = $nominee->policy_id;
        $nominee->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Nominee deleted successfully.'
            ]);
        }

        $redirectParams = $policyId ? ['policy_id' => $policyId] : [];
        
        return redirect()->route('nominees.index', $redirectParams)
            ->with('success', 'Nominee deleted successfully.');
    }

    public function show(Request $request, Nominee $nominee)
    {
        if ($request->expectsJson()) {
            return response()->json($nominee);
        }
        return view('nominees.show', compact('nominee'));
    }

    public function export(Request $request)
    {
        $policyId = $request->get('policy_id');
        $nomineesQuery = Nominee::orderBy('created_at', 'desc');

        if ($policyId) {
            $nomineesQuery->where('policy_id', $policyId);
        } else {
            $nomineesQuery->whereNull('policy_id');
        }

        $nominees = $nomineesQuery->get();

        $fileName = 'nominees_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $columns = [
            'Nominee Code', 'Full Name', 'Date Of Birth', 'Age', 'NIN/Passport No', 'Relationship',
            'Share Percentage', 'Date Added', 'Date Removed', 'Notes', 'Policy ID'
        ];

        $callback = function() use ($nominees, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($nominees as $nominee) {
                $age = $nominee->date_of_birth ? \Carbon\Carbon::parse($nominee->date_of_birth)->age : null;
                fputcsv($handle, [
                    $nominee->nominee_code ?? '',
                    $nominee->full_name ?? '',
                    $nominee->date_of_birth ? \Carbon\Carbon::parse($nominee->date_of_birth)->format('Y-m-d') : '',
                    $age ?? '',
                    $nominee->nin_passport_no ?? '',
                    $nominee->relationship ?? '',
                    $nominee->share_percentage ?? '',
                    $nominee->created_at ? $nominee->created_at->format('Y-m-d') : '',
                    $nominee->date_removed ? \Carbon\Carbon::parse($nominee->date_removed)->format('Y-m-d') : '',
                    $nominee->notes ?? '',
                    $nominee->policy_id ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        $columns = $request->columns ?? [];
        
        // Handle JSON string if sent as string
        if (is_string($columns)) {
            $columns = json_decode($columns, true) ?? [];
        }
        
        // Ensure it's an array
        if (!is_array($columns)) {
            $columns = [];
        }
        
        session(['nominee_columns' => $columns]);
        
        $redirectParams = $request->get('policy_id') ? ['policy_id' => $request->get('policy_id')] : [];
        
        return redirect()->route('nominees.index', $redirectParams)
            ->with('success', 'Column settings saved successfully.');
    }

    public function getDocuments(Request $request)
    {
        $policyId = $request->get('policy_id');
        $nomineeId = $request->get('nominee_id');
        
        $query = Document::query();
        
        if ($nomineeId) {
            $nominee = Nominee::find($nomineeId);
            if ($nominee) {
                $query->where('tied_to', $nominee->nominee_code);
            }
        } elseif ($policyId) {
            $policy = Policy::find($policyId);
            if ($policy) {
                // Get documents tied to policy or any nominees of this policy
                $nomineeCodes = Nominee::where('policy_id', $policyId)->pluck('nominee_code')->toArray();
                $query->where(function($q) use ($policy, $nomineeCodes) {
                    $q->where('tied_to', $policy->policy_no)
                      ->orWhere('tied_to', $policy->policy_code)
                      ->orWhereIn('tied_to', $nomineeCodes);
                });
            }
        }
        
        $documents = $query->orderBy('date_added', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'document_type' => 'required|in:nominee_document,id_document,other',
            'policy_id' => 'nullable|exists:policies,id',
            'nominee_id' => 'nullable|exists:nominees,id',
        ]);

        $tiedTo = null;
        $group = 'Nominee Document';
        
        if ($request->nominee_id) {
            $nominee = Nominee::find($request->nominee_id);
            if ($nominee && $nominee->nominee_code) {
                $tiedTo = $nominee->nominee_code;
            }
        } elseif ($request->policy_id) {
            $policy = Policy::find($request->policy_id);
            if ($policy) {
                // Try policy_no first, then policy_code, then use policy ID as fallback
                if (!empty($policy->policy_no)) {
                    $tiedTo = $policy->policy_no;
                } elseif (!empty($policy->policy_code)) {
                    $tiedTo = $policy->policy_code;
                } else {
                    // Fallback: use policy ID formatted as policy number
                    $tiedTo = 'POL-' . str_pad($policy->id, 6, '0', STR_PAD_LEFT);
                }
                $group = 'Policy Document';
            } else {
                // Policy ID was provided but policy not found
                return response()->json([
                    'success' => false,
                    'message' => 'Policy not found.'
                ], 422);
            }
        }

        // If still no tiedTo, use a generic identifier for documents not tied to specific policy/nominee
        if (!$tiedTo) {
            $tiedTo = 'NOMINEES_GENERAL';
            $group = 'Nominee Document';
        }

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentType = $request->document_type;
            
            // Map document types to names
            $documentNames = [
                'nominee_document' => 'Nominee Document',
                'id_document' => 'ID Document',
                'other' => 'Other Document'
            ];
            
            $filename = 'nominee_' . ($request->nominee_id ?? 'policy_' . $request->policy_id) . '_' . $documentType . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = Document::orderBy('id', 'desc')->first();
            $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id ?? 'DOC0') + 1 : 1001;
            $docId = 'DOC' . $nextId;

            // Store in documents table
            Document::create([
                'doc_id' => $docId,
                'tied_to' => $tiedTo,
                'name' => $documentNames[$documentType] ?? 'Document',
                'group' => $group,
                'type' => $documentType,
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        // Reload documents
        $query = Document::query();
        if ($request->nominee_id) {
            $nominee = Nominee::find($request->nominee_id);
            if ($nominee) {
                $query->where('tied_to', $nominee->nominee_code);
            }
        } elseif ($request->policy_id) {
            $policy = Policy::find($request->policy_id);
            if ($policy) {
                $nomineeCodes = Nominee::where('policy_id', $request->policy_id)->pluck('nominee_code')->toArray();
                $query->where(function($q) use ($policy, $nomineeCodes) {
                    $q->where('tied_to', $policy->policy_no)
                      ->orWhere('tied_to', $policy->policy_code)
                      ->orWhereIn('tied_to', $nomineeCodes);
                });
            }
        }
        
        $documents = $query->orderBy('date_added', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'documents' => $documents
        ]);
    }
}
