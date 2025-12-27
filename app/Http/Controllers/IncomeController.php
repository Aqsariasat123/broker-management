<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\LookupValue;
use Illuminate\Http\Request;
use App\Models\CommissionStatement;
use Illuminate\Support\Facades\Log; // <-- Add this

class IncomeController extends Controller
{
    public function index()
    {
        // Check permission
        if (!auth()->user()->hasPermission('incomes.view') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view incomes.');
        }

        $incomes = Income::with(['incomeSource', 'modeOfPayment', 'incomeCategory'])->orderBy('created_at', 'desc')->paginate(10);
      
        $comisionlist = CommissionStatement::with(['commissions.commissionNote.schedule'])
            ->orderBy('created_at', 'desc')
            ->get();
        // Lookup values for selects - use Insurers instead of Income Source
        $incomeSources = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Insurers');
        })->where('active', 1)->orderBy('seq')->get();

        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        $incomeCategories = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Income Category');
        })->where('active', 1)->orderBy('seq')->get();

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('incomes');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('incomes');

        return view('incomes.index', compact('incomes', 'incomeSources', 'comisionlist','modesOfPayment', 'incomeCategories', 'selectedColumns'));
    }

    public function store(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('incomes.create') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to create incomes.');
        }

        $validated = $request->validate([
            'income_source_id' => 'nullable|exists:lookup_values,id',
            'date_rcvd' => 'nullable|date',
            'amount_received' => 'nullable|numeric',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:lookup_values,id',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'statement_no' => 'nullable|string|max:255',
            'income_notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        // Generate unique IncomeID
        $latest = Income::orderBy('id', 'desc')->first();
        $nextId = $latest && $latest->income_id ? (int)str_replace('INC', '', $latest->income_id) + 1 : 1001;
        $validated['income_id'] = 'INC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $income = Income::create($validated);

        // Handle document upload - store in documents table
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = 'income_' . $income->id . '_document_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Store in documents table - tie to income using income_id
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $income->income_id,
                'name' => 'Document',
                'group' => 'Income Document',
                'type' => 'document',
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
                'message' => 'Income created successfully.',
                'income' => $income
            ]);
        }

        return redirect()->route('incomes.index')->with('success', 'Income created successfully.');
    }

    public function show(Request $request, Income $income)
    {
        // Check permission
        if (!auth()->user()->hasPermission('incomes.view') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view incomes.');
        }
        
        $income->load(['incomeSource', 'modeOfPayment', 'incomeCategory']);

        if ($request->expectsJson()) {
            $income->load(['incomeSource', 'modeOfPayment', 'incomeCategory']);
            // Load documents for this income
            $documents = \App\Models\Document::where('tied_to', $income->income_id)
                ->where('group', 'Income Document')
                ->get();
            $income->documents = $documents;
            return response()->json($income);
        }
        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        // Check permission
        if (!auth()->user()->hasPermission('incomes.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit incomes.');
        }

        if (request()->expectsJson()) {
            $income->load(['incomeSource', 'modeOfPayment', 'incomeCategory']);
            // Load documents for this income
            $documents = \App\Models\Document::where('tied_to', $income->income_id)
                ->where('group', 'Income Document')
                ->get();
            $income->documents = $documents;
            return response()->json($income);
        }
        return view('incomes.edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        // Check permission
        if (!auth()->user()->hasPermission('incomes.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit incomes.');
        }

        $validated = $request->validate([
            'income_source_id' => 'nullable|exists:lookup_values,id',
            'date_rcvd' => 'nullable|date',
            'amount_received' => 'nullable|numeric',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:lookup_values,id',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'statement_no' => 'nullable|string|max:255',
            'income_notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        $income->update($validated);

        // Handle document upload - store in documents table
        if ($request->hasFile('document')) {
            // Delete old documents if exists
            $oldDocuments = \App\Models\Document::where('tied_to', $income->income_id)
                ->where('group', 'Income Document')
                ->get();
            
            foreach ($oldDocuments as $oldDoc) {
                if ($oldDoc->file_path && \Storage::disk('public')->exists($oldDoc->file_path)) {
                    \Storage::disk('public')->delete($oldDoc->file_path);
                }
                $oldDoc->delete();
            }
            
            $file = $request->file('document');
            $filename = 'income_' . $income->id . '_document_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Store in documents table - tie to income using income_id
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $income->income_id,
                'name' => 'Document',
                'group' => 'Income Document',
                'type' => 'document',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        return redirect()->route('incomes.index')->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('incomes.index')->with('success', 'Income deleted successfully.');
    }

    public function export(Request $request)
    {
        $incomes = \App\Models\Income::with(['incomeSource', 'modeOfPayment', 'incomeCategory'])->get();

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
                $inc->incomeCategory ? $inc->incomeCategory->name : '',
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
