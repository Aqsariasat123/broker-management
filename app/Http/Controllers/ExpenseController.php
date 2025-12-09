<?php


namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        // Filter logic if needed (e.g., by category)
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $expenses = $query->orderBy('date_paid', 'desc')->paginate(10);

        // Lookup data for dropdowns
        $lookupData = [
            'categories' => ['Office', 'Travel', 'Utilities', 'Misc'],
            'modes' => ['Cash', 'Bank Transfer', 'Credit Card', 'Cheque']
        ];

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('expenses');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('expenses');

        return view('expenses.index', compact('expenses', 'lookupData', 'selectedColumns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payee' => 'required|string|max:255',
            'date_paid' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'mode_of_payment' => 'required|string|max:100',
            'expense_notes' => 'nullable|string'
        ]);

        // Generate unique expense ID
        $latest = Expense::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('EX', '', $latest->expense_id) + 1 : 1001;
        $validated['expense_id'] = 'EX' . $nextId;

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function show(Request $request, Expense $expense)
    {
        if ($request->expectsJson()) {
            return response()->json($expense);
        }
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        if (request()->expectsJson()) {
            return response()->json($expense);
        }
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'payee' => 'required|string|max:255',
            'date_paid' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'category' => 'required|string|max:100',
            'mode_of_payment' => 'required|string|max:100',
            'expense_notes' => 'nullable|string'
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function export()
    {
        $expenses = Expense::all();
        $fileName = 'expenses_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Expense ID', 'Payee', 'Date Paid', 'Amount Paid', 'Description', 'Category', 'Mode Of Payment', 'Expense Notes'
        ]);

        foreach ($expenses as $expense) {
            fputcsv($handle, [
                $expense->expense_id,
                $expense->payee,
                $expense->date_paid ? $expense->date_paid->format('d-M-Y') : '',
                number_format($expense->amount_paid, 2),
                $expense->description,
                $expense->category,
                $expense->mode_of_payment,
                $expense->expense_notes
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {}, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['expense_columns' => $request->columns ?? []]);
        return redirect()->route('expenses.index')
            ->with('success', 'Column settings saved successfully.');
    }
}