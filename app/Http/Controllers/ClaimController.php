<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Policy;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $query = Claim::query();
        
        // Filter by client_id if provided - use whereIn with subquery to get policy numbers
        if ($request->has('client_id') && $request->client_id) {
            $policyNumbers = Policy::where('client_id', $request->client_id)
                ->pluck('policy_no')
                ->filter()
                ->toArray();
            
            if (!empty($policyNumbers)) {
                $query->whereIn('policy_no', $policyNumbers);
            } else {
                // If no policies found for this client, return empty result
                $query->whereRaw('1 = 0');
            }
        }
        if ($request->filled('policy_id')) {
             $query->where('policy_id', $request->policy_id);
        }
        
        // Filter for pending claims (status is 'Processing' or empty)
        if ($request->has('pending') && ($request->pending == 'true' || $request->pending == '1')) {
            $query->where('status', 'Processing');
        }
        
        $claims = $query->with(['policy' => function($q) {
            $q->with('client');
        }])->orderBy('created_at', 'desc')->paginate(10);
        
        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('claims');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('claims');
        
        // Get lookup data for claim stage and status
        $lookupData = [
            'claim_stages' => \App\Models\LookupValue::whereHas('category', function($q) {
                $q->where('name', 'Claim Stage');
            })->orderBy('seq')->pluck('name')->toArray(),
            'claim_statuses' => \App\Models\LookupValue::whereHas('category', function($q) {
                $q->where('name', 'Claim Status');
            })->orderBy('seq')->pluck('name')->toArray(),
        ];
        
        // Get policies for dropdown
        $policies = Policy::orderBy('policy_no')->get(['id', 'policy_no','policy_code']);
        
        // Get client information if filtering by client_id
        $client = null;
        $policy = null;
        if ($request->has('client_id') && $request->client_id) {
            $client = \App\Models\Client::find($request->client_id);
        }
          if ($request->has('policy_id') && $request->policy_id) {
            $policy = Policy::find($request->policy_id);
        }
        
        return view('claims.index', compact('claims', 'selectedColumns', 'lookupData', 'policies', 'client', 'policy'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'loss_date' => 'nullable|date',
            'claim_date' => 'nullable|date',
            'claim_amount' => 'nullable|numeric',
            'claim_summary' => 'nullable|string',
            'claim_stage' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'close_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric',
            'settlment_notes' => 'nullable|string',
        ]);

        // Get policy to set client_id and policy_no
        $policy = Policy::findOrFail($validated['policy_id']);
        $validated['client_id'] = $policy->client_id;
        $validated['policy_no'] = $policy->policy_no;

        // Auto-generate unique Claim ID
        $latest = Claim::orderBy('id', 'desc')->first();
        if ($latest && $latest->claim_id) {
            // Extract number from claim_id (e.g., "CLM1001" -> 1001)
            preg_match('/CLM(\d+)/', $latest->claim_id, $matches);
            $nextId = isset($matches[1]) ? (int)$matches[1] + 1 : 1001;
        } else {
            $nextId = 1001;
        }
        $validated['claim_id'] = 'CLM' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Claim::create($validated);

        return redirect()->route('claims.index')->with('success', 'Claim created successfully.');
    }

    public function show(Request $request, Claim $claim)
    {
        if ($request->expectsJson()) {
            return response()->json($claim);
        }
        return view('claims.show', compact('claim'));
    }

    public function edit(Claim $claim)
    {
        if (request()->expectsJson()) {
            $claim->load('policy', 'client');
            return response()->json($claim);
        }
        return view('claims.edit', compact('claim'));
    }

    public function update(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'loss_date' => 'nullable|date',
            'claim_date' => 'nullable|date',
            'claim_amount' => 'nullable|numeric',
            'claim_summary' => 'nullable|string',
            'claim_stage' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'close_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric',
            'settlment_notes' => 'nullable|string',
        ]);

        // Get policy to update client_id and policy_no
        $policy = Policy::findOrFail($validated['policy_id']);
        $validated['client_id'] = $policy->client_id;
        $validated['policy_no'] = $policy->policy_no;

        // Don't allow claim_id to be changed - keep existing value
        $validated['claim_id'] = $claim->claim_id;

        $claim->update($validated);

        return redirect()->route('claims.index')->with('success', 'Claim updated successfully.');
    }

    public function destroy(Claim $claim)
    {
        $claim->delete();
        return redirect()->route('claims.index')->with('success', 'Claim deleted successfully.');
    }

    public function saveColumnSettings(Request $request)
    {
        session(['claim_columns' => $request->columns ?? []]);
        return redirect()->route('claims.index')
            ->with('success', 'Column settings saved successfully.');
    }

    public function export(Request $request)
    {
        $claims = Claim::all();

        $fileName = 'claims_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $columns = [
            'Claim ID', 'Policy No', 'Client Name', 'Loss Date', 'Claim Date', 'Claim Amount',
            'Claim Summary', 'Status', 'Close Date', 'Paid Amount', 'Settlment Notes'
        ];

        $callback = function() use ($claims, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($claims as $clm) {
                $clm->load('client');
                fputcsv($handle, [
                    $clm->claim_id,
                    $clm->policy_no,
                    $clm->client ? $clm->client->client_name : '-',
                    $clm->loss_date,
                    $clm->claim_date,
                    $clm->claim_amount,
                    $clm->claim_summary,
                    $clm->status,
                    $clm->close_date,
                    $clm->paid_amount,
                    $clm->settlment_notes,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'document_type' => 'required|in:claim_form,supporting_document,medical_report,police_report,estimate,other',
            'claim_id' => 'required|exists:claims,id',
        ]);

        $claim = Claim::findOrFail($request->claim_id);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentType = $request->document_type;
            
            // Map document types to names
            $documentNames = [
                'claim_form' => 'Claim Form',
                'supporting_document' => 'Supporting Document',
                'medical_report' => 'Medical Report',
                'police_report' => 'Police Report',
                'estimate' => 'Estimate',
                'other' => 'Other Document'
            ];
            
            $filename = 'claim_' . $claim->id . '_' . $documentType . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Store in documents table - tie to claim using claim_id
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $claim->claim_id,
                'name' => $documentNames[$documentType] ?? 'Document',
                'group' => 'Claim Document',
                'type' => $documentType,
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.'
        ]);
    }
}
