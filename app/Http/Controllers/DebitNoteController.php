<?php

namespace App\Http\Controllers;

use App\Models\DebitNote;
use App\Models\PaymentPlan;
use App\Models\LookupValue;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = DebitNote::with(['paymentPlan.schedule.policy.client']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment plan
        if ($request->has('payment_plan_id') && $request->payment_plan_id) {
            $query->where('payment_plan_id', $request->payment_plan_id);
        }

        // Search by debit note number
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('debit_note_no', 'like', "%{$search}%")
                  ->orWhereHas('paymentPlan.schedule.policy', function($subQ) use ($search) {
                      $subQ->where('policy_no', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('issued_on', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('issued_on', '<=', $request->date_to);
        }

        $debitNotes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get payment plans for filter
        $paymentPlans = PaymentPlan::with('schedule.policy')->orderBy('created_at', 'desc')->get();

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('debit-notes');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('debit-notes');

        return view('debit-notes.index', compact('debitNotes', 'paymentPlans', 'selectedColumns'));
    }

    public function create(Request $request)
    {
        $query = PaymentPlan::with(['schedule.policy.client'])->orderBy('created_at', 'desc');
        
        // If payment_plan_id is provided in query, include it even if it doesn't match other filters
        if ($request->has('payment_plan_id') && $request->payment_plan_id) {
            $paymentPlanId = $request->payment_plan_id;
            $query->where(function($q) use ($paymentPlanId) {
                $q->where('id', $paymentPlanId);
            });
        }
        
        $paymentPlans = $query->get();
        return view('debit-notes.create', compact('paymentPlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_plan_id' => 'required|exists:payment_plans,id',
            'debit_note_no' => 'required|string|max:255|unique:debit_notes,debit_note_no',
            'issued_on' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,issued,paid,overdue,cancelled',
            'document' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        // Handle file upload with encryption
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            // Store encrypted file in secure location
            $path = EncryptionService::storeEncryptedFile($file, 'encrypted', 'debit-notes');
            $validated['document_path'] = $path;
            $validated['is_encrypted'] = true; // Flag to indicate encrypted storage
        }

        $debitNote = DebitNote::create($validated);

        // Log activity
        \App\Models\AuditLog::log('create', $debitNote, null, $debitNote->getAttributes(), 'Debit note created: ' . $debitNote->debit_note_no);

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit note created successfully.');
    }

    public function show(Request $request, DebitNote $debitNote)
    {
        $debitNote->load(['paymentPlan.schedule.policy.client', 'payments']);
        
        if ($request->expectsJson()) {
            return response()->json($debitNote);
        }
        return view('debit-notes.show', compact('debitNote'));
    }

    public function edit(DebitNote $debitNote)
    {
        $debitNote->load(['paymentPlan.schedule.policy.client']);
        
        if (request()->expectsJson()) {
            return response()->json($debitNote);
        }
        
        $paymentPlans = PaymentPlan::with(['schedule.policy.client'])->orderBy('created_at', 'desc')->get();
        return view('debit-notes.edit', compact('debitNote', 'paymentPlans'));
    }

    public function update(Request $request, DebitNote $debitNote)
    {
        $validated = $request->validate([
            'payment_plan_id' => 'required|exists:payment_plans,id',
            'debit_note_no' => 'required|string|max:255|unique:debit_notes,debit_note_no,' . $debitNote->id,
            'issued_on' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,issued,paid,overdue,cancelled',
            'document' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        // Handle file upload with encryption
        if ($request->hasFile('document')) {
            // Delete old encrypted file if exists
            if ($debitNote->document_path) {
                if ($debitNote->is_encrypted ?? false) {
                    EncryptionService::deleteEncryptedFile($debitNote->document_path, 'encrypted');
                } else {
                    // Legacy unencrypted file
                    if (Storage::disk('public')->exists($debitNote->document_path)) {
                        Storage::disk('public')->delete($debitNote->document_path);
                    }
                }
            }
            
            $file = $request->file('document');
            // Store encrypted file in secure location
            $path = EncryptionService::storeEncryptedFile($file, 'encrypted', 'debit-notes');
            $validated['document_path'] = $path;
            $validated['is_encrypted'] = true;
        }

        $oldValues = $debitNote->getAttributes();
        $debitNote->update($validated);

        // Log activity
        \App\Models\AuditLog::log('update', $debitNote, $oldValues, $debitNote->getChanges(), 'Debit note updated: ' . $debitNote->debit_note_no);

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit note updated successfully.');
    }

    public function destroy(DebitNote $debitNote)
    {
        $debitNoteNo = $debitNote->debit_note_no;
        $debitNote->delete();

        // Log activity
        \App\Models\AuditLog::log('delete', $debitNote, $debitNote->getAttributes(), null, 'Debit note deleted: ' . $debitNoteNo);

        return redirect()->route('debit-notes.index')
            ->with('success', 'Debit note deleted successfully.');
    }

    public function saveColumnSettings(Request $request)
    {
        session(['debit_note_columns' => $request->columns ?? []]);
        return redirect()->route('debit-notes.index')
            ->with('success', 'Column settings saved successfully.');
    }
}
