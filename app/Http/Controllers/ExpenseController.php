<?php


namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('expenses.view') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view expenses.');
        }

        $query = Expense::with(['expenseCategory', 'modeOfPayment']);

        // Filter logic if needed (e.g., by category_id)
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $expenses = $query->orderBy('date_paid', 'desc')->paginate(10);

        // Lookup values for dropdowns
        $expenseCategories = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Expense Category');
        })->where('active', 1)->orderBy('seq')->get();

        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        // Lookup data for backward compatibility
        $lookupData = [
            'categories' => $expenseCategories->pluck('name')->toArray(),
        ];

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('expenses');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('expenses');

        return view('expenses.index', compact('expenses', 'expenseCategories', 'modesOfPayment', 'lookupData', 'selectedColumns'));
    }

    public function store(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('expenses.create') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to create expenses.');
        }

        $validated = $request->validate([
            'payee' => 'required|string|max:255',
            'date_paid' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:lookup_values,id',
            'mode_of_payment_id' => 'required|exists:lookup_values,id',
            'receipt_no' => 'nullable|string|max:255',
            'expense_notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        // Generate unique expense ID
        $latest = Expense::orderBy('id', 'desc')->first();
        $nextId = $latest && $latest->expense_id ? (int)str_replace('EX', '', $latest->expense_id) + 1 : 1001;
        $validated['expense_id'] = 'EX' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $validated['expense_code'] = $validated['expense_id']; // Use same value as expense_id

        $expense = Expense::create($validated);

        // Handle receipt upload - store in documents table
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = 'expense_' . $expense->id . '_receipt_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Store in documents table - tie to expense using expense_id
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $expense->expense_id,
                'name' => 'Receipt',
                'group' => 'Expense Document',
                'type' => 'receipt',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        // Load category relationship
        $expense->load('expenseCategory');

        // If this is an AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense added successfully.',
                'expense' => $expense
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function show(Request $request, Expense $expense)
    {
        // Check permission
        if (!auth()->user()->hasPermission('expenses.view') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view expenses.');
        }

        // Load relationships
        $expense->load(['expenseCategory', 'modeOfPayment']);

        if ($request->expectsJson()) {
            // Load documents for this expense
            $documents = \App\Models\Document::where('tied_to', $expense->expense_id)
                ->where('group', 'Expense Document')
                ->get();
            $expense->documents = $documents;
            return response()->json($expense);
        }
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        // Check permission
        if (!auth()->user()->hasPermission('expenses.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit expenses.');
        }

        // Load relationships
        $expense->load(['expenseCategory', 'modeOfPayment']);

        if (request()->expectsJson()) {
            // Load documents for this expense
            $documents = \App\Models\Document::where('tied_to', $expense->expense_id)
                ->where('group', 'Expense Document')
                ->get();
            $expense->documents = $documents;
            return response()->json($expense);
        }
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Check permission
        if (!auth()->user()->hasPermission('expenses.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit expenses.');
        }

        $validated = $request->validate([
            'payee' => 'required|string|max:255',
            'date_paid' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:lookup_values,id',
            'mode_of_payment_id' => 'required|exists:lookup_values,id',
            'receipt_no' => 'nullable|string|max:255',
            'expense_notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        $expense->update($validated);

        // Load relationships
        $expense->load(['expenseCategory', 'modeOfPayment']);

        // Handle receipt upload - store in documents table
        if ($request->hasFile('receipt')) {
            // Delete old receipt documents if exists
            $oldDocuments = \App\Models\Document::where('tied_to', $expense->expense_id)
                ->where('group', 'Expense Document')
                ->where('type', 'receipt')
                ->get();
            
            foreach ($oldDocuments as $oldDoc) {
                if ($oldDoc->file_path && \Storage::disk('public')->exists($oldDoc->file_path)) {
                    \Storage::disk('public')->delete($oldDoc->file_path);
                }
                $oldDoc->delete();
            }
            
            $file = $request->file('receipt');
            $filename = 'expense_' . $expense->id . '_receipt_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Store in documents table - tie to expense using expense_id
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $expense->expense_id,
                'name' => 'Receipt',
                'group' => 'Expense Document',
                'type' => 'receipt',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        // If this is an AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully.',
                'expense' => $expense
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        // Check permission
        if (!auth()->user()->hasPermission('expenses.delete') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to delete expenses.');
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function export()
    {
        $expenses = Expense::with(['expenseCategory', 'modeOfPayment'])->get();
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
                $expense->expenseCategory ? $expense->expenseCategory->name : '-',
                $expense->modeOfPayment ? $expense->modeOfPayment->name : '-',
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

    public function uploadReceipt(Request $request)
    {
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'expense_id' => 'required|exists:expenses,id',
        ]);

        $expense = Expense::findOrFail($request->expense_id);

        if ($request->hasFile('receipt')) {
            // Delete old receipt documents if exists
            $oldDocuments = \App\Models\Document::where('tied_to', $expense->expense_id)
                ->where('group', 'Expense Document')
                ->where('type', 'receipt')
                ->get();
            
            foreach ($oldDocuments as $oldDoc) {
                if ($oldDoc->file_path && \Storage::disk('public')->exists($oldDoc->file_path)) {
                    \Storage::disk('public')->delete($oldDoc->file_path);
                }
                $oldDoc->delete();
            }
            
            $file = $request->file('receipt');
            $filename = 'expense_' . $expense->id . '_receipt_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Store in documents table - tie to expense using expense_id
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $expense->expense_id,
                'name' => 'Receipt',
                'group' => 'Expense Document',
                'type' => 'receipt',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Receipt uploaded successfully.',
            'expense' => $expense
        ]);
    }
}