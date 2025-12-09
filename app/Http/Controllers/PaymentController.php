<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\DebitNote;
use App\Models\LookupValue;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['debitNote.paymentPlan.schedule.policy.client']);

        // Filter by debit note
        if ($request->has('debit_note_id') && $request->debit_note_id) {
            $query->where('debit_note_id', $request->debit_note_id);
        }

        // Filter by mode of payment
        if ($request->has('mode_of_payment_id') && $request->mode_of_payment_id) {
            $query->where('mode_of_payment_id', $request->mode_of_payment_id);
        }

        // Search by payment reference
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('debitNote.paymentPlan.schedule.policy', function($subQ) use ($search) {
                      $subQ->where('policy_no', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('paid_on', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('paid_on', '<=', $request->date_to);
        }

        $payments = $query->orderBy('paid_on', 'desc')->paginate(15);

        // Get debit notes for filter
        $debitNotes = DebitNote::with('paymentPlan.schedule.policy')->orderBy('created_at', 'desc')->get();

        // Get modes of payment
        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q) {
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('payments');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('payments');

        return view('payments.index', compact('payments', 'debitNotes', 'modesOfPayment', 'selectedColumns'));
    }

    public function create(Request $request)
    {
        $query = DebitNote::with(['paymentPlan.schedule.policy.client'])
            ->where('status', '!=', 'paid')
            ->orderBy('created_at', 'desc');
        
        // If debit_note_id is provided in query, pre-select it
        if ($request->has('debit_note_id')) {
            $query->where('id', $request->debit_note_id);
        }
        
        $debitNotes = $query->get();

        // Get modes of payment
        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q) {
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        return view('payments.create', compact('debitNotes', 'modesOfPayment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'debit_note_id' => 'required|exists:debit_notes,id',
            'payment_reference' => 'required|string|max:255|unique:payments,payment_reference',
            'paid_on' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'receipt' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload with encryption
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            // Store encrypted file in secure location
            $path = EncryptionService::storeEncryptedFile($file, 'encrypted', 'receipts');
            $validated['receipt_path'] = $path;
            $validated['is_encrypted'] = true; // Flag to indicate encrypted storage
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create($validated);

            // Update debit note status if fully paid
            $debitNote = DebitNote::findOrFail($validated['debit_note_id']);
            $totalPaid = $debitNote->payments()->sum('amount');
            
            if ($totalPaid >= $debitNote->amount) {
                $debitNote->update(['status' => 'paid']);
                
                // Update payment plan status
                $paymentPlan = $debitNote->paymentPlan;
                if ($paymentPlan) {
                    $allDebitNotesPaid = $paymentPlan->debitNotes()->where('status', '!=', 'paid')->count() == 0;
                    if ($allDebitNotesPaid) {
                        $paymentPlan->update(['status' => 'paid']);
                    }
                }
            } else {
                $debitNote->update(['status' => 'partial']);
            }

            DB::commit();

            // Log activity
            \App\Models\AuditLog::log('create', $payment, null, $payment->getAttributes(), 'Payment recorded: ' . $payment->payment_reference);

            return redirect()->route('payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Request $request, Payment $payment)
    {
        $payment->load(['debitNote.paymentPlan.schedule.policy.client', 'modeOfPayment']);
        
        if ($request->expectsJson()) {
            return response()->json($payment);
        }
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load(['debitNote.paymentPlan.schedule.policy.client', 'modeOfPayment']);
        
        if (request()->expectsJson()) {
            return response()->json($payment);
        }
        
        $debitNotes = DebitNote::with(['paymentPlan.schedule.policy.client'])->orderBy('created_at', 'desc')->get();

        // Get modes of payment
        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q) {
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        return view('payments.edit', compact('payment', 'debitNotes', 'modesOfPayment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'debit_note_id' => 'required|exists:debit_notes,id',
            'payment_reference' => 'required|string|max:255|unique:payments,payment_reference,' . $payment->id,
            'paid_on' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'receipt' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload with encryption
        if ($request->hasFile('receipt')) {
            // Delete old encrypted file if exists
            if ($payment->receipt_path) {
                if ($payment->is_encrypted ?? false) {
                    EncryptionService::deleteEncryptedFile($payment->receipt_path, 'encrypted');
                } else {
                    // Legacy unencrypted file
                    if (Storage::disk('public')->exists($payment->receipt_path)) {
                        Storage::disk('public')->delete($payment->receipt_path);
                    }
                }
            }
            
            $file = $request->file('receipt');
            // Store encrypted file in secure location
            $path = EncryptionService::storeEncryptedFile($file, 'encrypted', 'receipts');
            $validated['receipt_path'] = $path;
            $validated['is_encrypted'] = true;
        }

        $oldValues = $payment->getAttributes();
        $payment->update($validated);

        // Recalculate debit note status
        $debitNote = $payment->debitNote;
        $totalPaid = $debitNote->payments()->sum('amount');
        
        if ($totalPaid >= $debitNote->amount) {
            $debitNote->update(['status' => 'paid']);
            
            // Update payment plan status
            $paymentPlan = $debitNote->paymentPlan;
            if ($paymentPlan) {
                $allDebitNotesPaid = $paymentPlan->debitNotes()->where('status', '!=', 'paid')->count() == 0;
                if ($allDebitNotesPaid) {
                    $paymentPlan->update(['status' => 'paid']);
                } else {
                    $paymentPlan->update(['status' => 'active']);
                }
            }
        } else if ($totalPaid > 0) {
            // Check if overdue (30 days after issued date)
            if ($debitNote->issued_on && $debitNote->issued_on->copy()->addDays(30) < now()) {
                $debitNote->update(['status' => 'overdue']);
            } else {
                $debitNote->update(['status' => 'partial']);
            }
            
            // Check if overdue
            if ($debitNote->issued_on && $debitNote->issued_on->addDays(30) < now()) {
                $debitNote->update(['status' => 'overdue']);
            }
        } else {
            $debitNote->update(['status' => 'pending']);
        }

        // Log activity
        \App\Models\AuditLog::log('update', $payment, $oldValues, $payment->getChanges(), 'Payment updated: ' . $payment->payment_reference);

        return redirect()->route('payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $paymentRef = $payment->payment_reference;
        $debitNote = $payment->debitNote;
        
        $payment->delete();

        // Recalculate debit note status
        $totalPaid = $debitNote->payments()->sum('amount');
        if ($totalPaid >= $debitNote->amount) {
            $debitNote->update(['status' => 'paid']);
            
            // Update payment plan status
            $paymentPlan = $debitNote->paymentPlan;
            if ($paymentPlan) {
                $allDebitNotesPaid = $paymentPlan->debitNotes()->where('status', '!=', 'paid')->count() == 0;
                if ($allDebitNotesPaid) {
                    $paymentPlan->update(['status' => 'paid']);
                } else {
                    $paymentPlan->update(['status' => 'active']);
                }
            }
        } else if ($totalPaid > 0) {
            // Check if overdue
            if ($debitNote->issued_on && $debitNote->issued_on->copy()->addDays(30) < now()) {
                $debitNote->update(['status' => 'overdue']);
            } else {
                $debitNote->update(['status' => 'partial']);
            }
        } else {
            $debitNote->update(['status' => 'pending']);
        }

        // Log activity
        \App\Models\AuditLog::log('delete', $payment, $payment->getAttributes(), null, 'Payment deleted: ' . $paymentRef);

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    /**
     * Financial reporting
     */
    public function report(Request $request)
    {
        $query = Payment::with(['debitNote.paymentPlan.schedule.policy.client']);

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('paid_on', '>=', $request->date_from);
        } else {
            $query->whereDate('paid_on', '>=', now()->startOfMonth());
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('paid_on', '<=', $request->date_to);
        } else {
            $query->whereDate('paid_on', '<=', now()->endOfMonth());
        }

        $payments = $query->orderBy('paid_on', 'desc')->get();

        // Calculate statistics
        $totalAmount = $payments->sum('amount');
        $totalCount = $payments->count();
        
        // Group by mode of payment
        $byModeOfPayment = $payments->groupBy('mode_of_payment_id')->map(function($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        // Group by date
        $byDate = $payments->groupBy(function($payment) {
            return $payment->paid_on->format('Y-m-d');
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        })->sortKeys();

        // Group by client
        $byClient = $payments->groupBy(function($payment) {
            return $payment->debitNote->paymentPlan->schedule->policy->client_id ?? 'unknown';
        })->map(function($group) {
            $firstPayment = $group->first();
            $clientName = $firstPayment->debitNote->paymentPlan->schedule->policy->client->client_name ?? 'Unknown';
            return [
                'client_name' => $clientName,
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        })->sortByDesc('amount')->take(10);

        // Status summary
        $debitNotes = DebitNote::with('payments')->get();
        $statusSummary = [
            'paid' => $debitNotes->where('status', 'paid')->count(),
            'partial' => $debitNotes->where('status', 'partial')->count(),
            'overdue' => $debitNotes->where('status', 'overdue')->count(),
            'pending' => $debitNotes->where('status', 'pending')->count(),
            'issued' => $debitNotes->where('status', 'issued')->count(),
        ];

        return view('payments.report', compact('payments', 'totalAmount', 'totalCount', 'byModeOfPayment', 'byDate', 'byClient', 'statusSummary'));
    }

    public function saveColumnSettings(Request $request)
    {
        session(['payment_columns' => $request->columns ?? []]);
        return redirect()->route('payments.index')
            ->with('success', 'Column settings saved successfully.');
    }
}
