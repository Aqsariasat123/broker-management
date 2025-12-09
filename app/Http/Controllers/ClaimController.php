<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index()
    {
        $claims = Claim::orderBy('created_at', 'desc')->paginate(10);
        
        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('claims');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('claims');
        
        return view('claims.index', compact('claims', 'selectedColumns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_no' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'loss_date' => 'nullable|date',
            'claim_date' => 'nullable|date',
            'claim_amount' => 'nullable|numeric',
            'claim_summary' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'close_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric',
            'settlment_notes' => 'nullable|string',
        ]);

        // Generate unique Claim ID
        $latest = Claim::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('CLM', '', $latest->claim_id) + 1 : 1001;
        $validated['claim_id'] = 'CLM' . $nextId;

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
            return response()->json($claim);
        }
        return view('claims.edit', compact('claim'));
    }

    public function update(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'policy_no' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'loss_date' => 'nullable|date',
            'claim_date' => 'nullable|date',
            'claim_amount' => 'nullable|numeric',
            'claim_summary' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'close_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric',
            'settlment_notes' => 'nullable|string',
        ]);

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
                fputcsv($handle, [
                    $clm->claim_id,
                    $clm->policy_no,
                    $clm->client_name,
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
}
