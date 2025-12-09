<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['incomeSource', 'modeOfPayment'])->orderBy('created_at', 'desc')->paginate(10);

        // Lookup values for selects
        $incomeSources = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Income Source');
        })->where('active', 1)->orderBy('seq')->get();

        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('incomes');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('incomes');

        return view('incomes.index', compact('incomes', 'incomeSources', 'modesOfPayment', 'selectedColumns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_source_id' => 'nullable|exists:lookup_values,id',
            'date_rcvd' => 'nullable|date',
            'amount_received' => 'nullable|numeric',
            'description' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'statement_no' => 'nullable|string|max:255',
            'income_notes' => 'nullable|string',
        ]);

        // Generate unique IncomeID
        $latest = Income::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('INC', '', $latest->income_id) + 1 : 1001;
        $validated['income_id'] = 'INC' . $nextId;

        Income::create($validated);

        return redirect()->route('incomes.index')->with('success', 'Income created successfully.');
    }

    public function show(Request $request, Income $income)
    {
        if ($request->expectsJson()) {
            return response()->json($income->load(['incomeSource', 'modeOfPayment']));
        }
        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        if (request()->expectsJson()) {
            return response()->json($income->load(['incomeSource', 'modeOfPayment']));
        }
        return view('incomes.edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'income_source_id' => 'nullable|exists:lookup_values,id',
            'date_rcvd' => 'nullable|date',
            'amount_received' => 'nullable|numeric',
            'description' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'statement_no' => 'nullable|string|max:255',
            'income_notes' => 'nullable|string',
        ]);

        $income->update($validated);

        return redirect()->route('incomes.index')->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('incomes.index')->with('success', 'Income deleted successfully.');
    }

    public function export(Request $request)
    {
        $incomes = \App\Models\Income::with(['incomeSource', 'modeOfPayment'])->get();

        $fileName = 'incomes_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'IncomeID', 'Income Source', 'Date Rcvd', 'Amount Received', 'Description', 'Category', 'Mode Of Payment (Life)', 'Statement No', 'Income Notes'
        ]);

        foreach ($incomes as $inc) {
            fputcsv($handle, [
                $inc->income_id,
                $inc->incomeSource ? $inc->incomeSource->name : '',
                $inc->date_rcvd,
                $inc->amount_received,
                $inc->description,
                $inc->category,
                $inc->modeOfPayment ? $inc->modeOfPayment->name : '',
                $inc->statement_no,
                $inc->income_notes,
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {}, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['income_columns' => $request->columns ?? []]);
        return redirect()->route('incomes.index')
            ->with('success', 'Column settings saved successfully.');
    }
}
