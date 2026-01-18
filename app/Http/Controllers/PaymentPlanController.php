<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use App\Models\Schedule;
use App\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentPlan::with(['schedule.policy.client','lookuFrequency','debitNotes.payments.modeOfPayment']);

         $dateRange = $request->get('date_range', 'month'); // default = 'month'
            $now = Carbon::now();
            $startDate = null;
            $endDate = null;

            switch ($dateRange) {
                case 'today':
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    break;

                case 'week':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    break;

                case 'month':
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    break;

                case 'quarter':
                    $startDate = $now->copy()->firstOfQuarter();
                    $endDate = $now->copy()->lastOfQuarter();
                    break;

                case 'year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfYear();
                    break;

                default:
                    if (str_starts_with($dateRange, 'year-')) {
                        $selectedYear = (int) str_replace('year-', '', $dateRange);
                        $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
                        $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
                    }
                    break;
            }

            if ($startDate && $endDate) {
                $query->whereBetween('due_date', [$startDate, $endDate]);
            }
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by schedule
        if ($request->has('schedule_id') && $request->schedule_id) {
            $query->where('schedule_id', $request->schedule_id);
        }

        // Filter by due date
        if ($request->has('due_soon') && $request->due_soon == 'true') {
            $query->whereBetween('due_date', [now(), now()->addDays(30)]);
        }
        
        // Filter for Overdue instalments
        if ($request->has('filter') && $request->filter == 'overdue') {
            $query->where('due_date', '<', now()->toDateString())
                  ->where('status', '!=', 'paid');
        }
        
        // Filter for Outstanding payments
        if ($request->has('filter') && $request->filter == 'outstanding') {
            $query->where('status', '!=', 'paid');
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('installment_label', 'like', "%{$search}%")
                  ->orWhereHas('schedule.policy', function($subQ) use ($search) {
                      $subQ->where('policy_no', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by client_id
        $client = null;
        if ($request->has('client_id') && $request->client_id) {
            $client = \App\Models\Client::find($request->client_id);
            if ($client) {
                $query->whereHas('schedule.policy', function($q) use ($request) {
                    $q->where('client_id', $request->client_id);
                });
            }
        }

        $paymentPlans = $query->orderBy('due_date', 'asc')->paginate(15);

        // Get schedules for filter and dropdown
        $schedules = Schedule::with('policy.client')->orderBy('created_at', 'desc')->get();
        
        // Get frequencies from lookup
        $frequencies = LookupValue::whereHas('lookupCategory', function($q) {
            $q->where('name', 'Frequency');
        })->where('active', 1)->orderBy('seq')->get();

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('payment-plans');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('payment-plans');

        return view('payment-plans.index', compact('paymentPlans', 'schedules', 'frequencies', 'selectedColumns', 'client'));
    }

    public function create(Request $request)
    {
        $query = Schedule::with(['policy.client'])->orderBy('created_at', 'desc');
        
        // If schedule_id is provided in query, include it even if it doesn't match other filters
        if ($request->has('schedule_id') && $request->schedule_id) {
            $scheduleId = $request->schedule_id;
            $query->where(function($q) use ($scheduleId) {
                $q->where('id', $scheduleId);
            });
        }
        
        $schedules = $query->get();
        
        // Get frequencies from lookup
        $frequencies = LookupValue::whereHas('lookupCategory', function($q) {
            $q->where('name', 'Frequency');
        })->where('active', 1)->orderBy('seq')->get();

        return view('payment-plans.create', compact('schedules', 'frequencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'installment_label' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'nullable|string|max:255',
            'status' => 'required|in:pending,active,paid,overdue,cancelled',
        ]);

        $paymentPlan = PaymentPlan::create($validated);

        // Log activity
        \App\Models\AuditLog::log('create', $paymentPlan, null, $paymentPlan->getAttributes(), 'Payment plan created: ' . ($paymentPlan->installment_label ?? 'Instalment #' . $paymentPlan->id));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment plan created successfully.',
                'paymentPlan' => $paymentPlan->load(['schedule.policy.client'])
            ]);
        }

        return redirect()->route('payment-plans.index')
            ->with('success', 'Payment plan created successfully.');
    }

    public function show(Request $request, PaymentPlan $paymentPlan)
    {
        $paymentPlan->load(['schedule.policy.client', 'debitNotes.payments']);
        
        if ($request->expectsJson()) {
            return response()->json($paymentPlan);
        }
        return view('payment-plans.show', compact('paymentPlan'));
    }

    public function edit(PaymentPlan $paymentPlan)
    {
        $paymentPlan->load(['schedule.policy.client']);
        
        if (request()->expectsJson() || request()->ajax()) {
            $schedules = Schedule::with(['policy.client'])->orderBy('created_at', 'desc')->get();
            $frequencies = LookupValue::whereHas('lookupCategory', function($q) {
                $q->where('name', 'Frequency');
            })->where('active', 1)->orderBy('seq')->get();
            return response()->json([
                'paymentPlan' => $paymentPlan,
                'schedules' => $schedules,
                'frequencies' => $frequencies
            ]);
        }
        
        $schedules = Schedule::with(['policy.client'])->orderBy('created_at', 'desc')->get();
        
        // Get frequencies from lookup
        $frequencies = LookupValue::whereHas('lookupCategory', function($q) {
            $q->where('name', 'Frequency');
        })->where('active', 1)->orderBy('seq')->get();

        return view('payment-plans.edit', compact('paymentPlan', 'schedules', 'frequencies'));
    }

    public function update(Request $request, PaymentPlan $paymentPlan)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'installment_label' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'nullable|string|max:255',
            'status' => 'required|in:pending,active,paid,overdue,cancelled',
        ]);

        $oldValues = $paymentPlan->getAttributes();
        $paymentPlan->update($validated);

        // Log activity
        \App\Models\AuditLog::log('update', $paymentPlan, $oldValues, $paymentPlan->getChanges(), 'Payment plan updated: ' . ($paymentPlan->installment_label ?? 'Instalment #' . $paymentPlan->id));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment plan updated successfully.',
                'paymentPlan' => $paymentPlan->load(['schedule.policy.client'])
            ]);
        }

        return redirect()->route('payment-plans.index')
            ->with('success', 'Payment plan updated successfully.');
    }

    public function destroy(PaymentPlan $paymentPlan)
    {
        $label = $paymentPlan->installment_label ?? 'Instalment #' . $paymentPlan->id;
        $paymentPlan->delete();

        // Log activity
        \App\Models\AuditLog::log('delete', $paymentPlan, $paymentPlan->getAttributes(), null, 'Payment plan deleted: ' . $label);

        return redirect()->route('payment-plans.index')
            ->with('success', 'Payment plan deleted successfully.');
    }

    /**
     * Create multiple instalments for a schedule
     */
    public function createInstalments(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'total_amount' => 'required|numeric|min:0',
            'number_of_instalments' => 'required|integer|min:1|max:12',
            'first_due_date' => 'required|date',
            'frequency' => 'required|string|max:255',
        ]);

        $schedule = Schedule::findOrFail($validated['schedule_id']);
        $amountPerInstalment = $validated['total_amount'] / $validated['number_of_instalments'];
        $dueDate = \Carbon\Carbon::parse($validated['first_due_date']);

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $validated['number_of_instalments']; $i++) {
                PaymentPlan::create([
                    'schedule_id' => $validated['schedule_id'],
                    'installment_label' => 'Instalment ' . $i . ' of ' . $validated['number_of_instalments'],
                    'due_date' => $dueDate->copy(),
                    'amount' => $amountPerInstalment,
                    'frequency' => $validated['frequency'],
                    'status' => 'pending',
                ]);

                // Calculate next due date based on frequency
                switch (strtolower($validated['frequency'])) {
                    case 'monthly':
                        $dueDate->addMonth();
                        break;
                    case 'quarterly':
                        $dueDate->addMonths(3);
                        break;
                    case 'annually':
                        $dueDate->addYear();
                        break;
                    default:
                        $dueDate->addMonth();
                }
            }

            DB::commit();

            return redirect()->route('payment-plans.index')
                ->with('success', $validated['number_of_instalments'] . ' instalments created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create instalments: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function saveColumnSettings(Request $request)
    {
        // Use different session key for client view
        if ($request->has('client_id') && $request->client_id) {
            session(['payment_plan_client_columns' => $request->columns ?? []]);
            return redirect()->route('payment-plans.index', ['client_id' => $request->client_id])
                ->with('success', 'Column settings saved successfully.');
        }

        session(['payment_plan_columns' => $request->columns ?? []]);
        return redirect()->route('payment-plans.index')
            ->with('success', 'Column settings saved successfully.');
    }

    public function export(Request $request)
    {
        $query = PaymentPlan::with(['schedule.policy.client', 'lookuFrequency', 'debitNotes.payments.modeOfPayment']);

        // Apply same filters as index
        $dateRange = $request->get('date_range', 'month');
        $now = Carbon::now();
        $startDate = null;
        $endDate = null;

        switch ($dateRange) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'quarter':
                $startDate = $now->copy()->firstOfQuarter();
                $endDate = $now->copy()->lastOfQuarter();
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            default:
                if (str_starts_with($dateRange, 'year-')) {
                    $selectedYear = (int) str_replace('year-', '', $dateRange);
                    $startDate = Carbon::create($selectedYear, 1, 1)->startOfDay();
                    $endDate = Carbon::create($selectedYear, 12, 31)->endOfDay();
                }
                break;
        }

        if ($startDate && $endDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id') && $request->client_id) {
            $query->whereHas('schedule.policy', function($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        $paymentPlans = $query->orderBy('due_date', 'asc')->get();

        $fileName = 'payment_plans_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($paymentPlans) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Debit Note',
                'Payment Type',
                'Due Date',
                'Amount Due',
                'Status',
                'Amount Paid',
                'Date Paid',
                'Payment Mode',
                'Cheque No',
                'Policy Number',
                'Client Name',
                'Comments'
            ]);

            foreach ($paymentPlans as $plan) {
                $latestPayment = $plan->debitNotes->first()?->payments->first();

                fputcsv($file, [
                    $plan->debitNotes->first()->debit_note_no ?? '',
                    $plan->installment_label ?? 'Instalment',
                    $plan->due_date ? $plan->due_date->format('d-M-Y') : '',
                    $plan->amount ? number_format($plan->amount, 2) : '',
                    ucfirst($plan->status),
                    $latestPayment ? number_format($latestPayment->amount, 2) : '',
                    $latestPayment && $latestPayment->paid_on ? Carbon::parse($latestPayment->paid_on)->format('d-M-Y') : '',
                    $latestPayment?->modeOfPayment?->name ?? '',
                    $latestPayment?->cheque_no ?? '',
                    $plan->schedule->policy->policy_no ?? '',
                    $plan->schedule->policy->client->client_name ?? '',
                    $plan->comments ?? $plan->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
