<?php

namespace App\Http\Controllers;

use App\Models\Statement;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class StatementController extends Controller
{
    public function index(Request $request)
    {
        // Lookup values for selects
        $insurers = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Insurers');
        })->where('active', 1)->orderBy('seq')->get();

        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        // Filter by insurer if requested
        $insurerFilter = $request->get('insurer');
        $statements = Statement::with(['insurer', 'modeOfPayment'])
            ->when($insurerFilter, function($q) use ($insurerFilter, $insurers) {
                $insurer = $insurers->firstWhere('name', $insurerFilter);
                if ($insurer) {
                    $q->where('insurer_id', $insurer->id);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('statements');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('statements');

        return view('statements.index', compact('statements', 'insurers', 'modesOfPayment', 'insurerFilter', 'selectedColumns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|string|max:10',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'business_category' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'amount_received' => 'nullable|numeric',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Generate unique Statement No
        $latest = Statement::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('ST', '', $latest->statement_no) + 1 : 1001;
        $validated['statement_no'] = 'ST' . $nextId;

        Statement::create($validated);

        return redirect()->route('statements.index')->with('success', 'Statement created successfully.');
    }

    public function show(Request $request, Statement $statement)
    {
        if ($request->expectsJson()) {
            return response()->json($statement->load(['insurer', 'modeOfPayment']));
        }
        return view('statements.show', compact('statement'));
    }

    public function edit(Statement $statement)
    {
        if (request()->expectsJson()) {
            return response()->json($statement->load(['insurer', 'modeOfPayment']));
        }
        return view('statements.edit', compact('statement'));
    }

    public function update(Request $request, Statement $statement)
    {
        $validated = $request->validate([
            'year' => 'nullable|string|max:10',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'business_category' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'amount_received' => 'nullable|numeric',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'remarks' => 'nullable|string|max:255',
        ]);

        $statement->update($validated);

        return redirect()->route('statements.index')->with('success', 'Statement updated successfully.');
    }

    public function destroy(Statement $statement)
    {
        $statement->delete();
        return redirect()->route('statements.index')->with('success', 'Statement deleted successfully.');
    }

    public function export(Request $request)
    {
        $statements = \App\Models\Statement::with(['insurer', 'modeOfPayment'])->get();

        $fileName = 'statements_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Statement No', 'Year', 'Insurer', 'Business Category', 'Date Received', 'Amount Received', 'Mode Of Payment (Life)', 'Remarks'
        ]);

        foreach ($statements as $st) {
            fputcsv($handle, [
                $st->statement_no,
                $st->year,
                $st->insurer ? $st->insurer->name : '',
                $st->business_category,
                $st->date_received,
                $st->amount_received,
                $st->modeOfPayment ? $st->modeOfPayment->name : '',
                $st->remarks,
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {}, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['statement_columns' => $request->columns ?? []]);
        return redirect()->route('statements.index')
            ->with('success', 'Column settings saved successfully.');
    }
}
