<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\Client;
use App\Models\LookupValue;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PolicyController extends Controller
{
    public function index(Request $request)
    {
        $query = Policy::query();
        
        // Filter for Due for Renewal
        if ($request->has('dfr') && $request->dfr == 'true') {
            $query->where(function($q) {
                $q->whereHas('policyStatus', function($subQ) {
                    $subQ->where('name', 'LIKE', '%DFR%');
                })->orWhere(function($subQ) {
                    $subQ->whereNotNull('end_date')
                      ->whereBetween('end_date', [now(), now()->addDays(30)]);
                });
            });
        }

        $policies = $query->with([
                'client',
                'insurer',
                'policyClass',
                'policyPlan',
                'policyStatus',
                'businessType',
                'frequency',
                'payPlan',
                'agency',
                'channel'
            ])
            ->orderBy('date_registered', 'desc')
            ->paginate(10);
        
        // Get lookup data for dropdowns
        $lookupData = $this->getLookupData();
        
        return view('policies.index', compact('policies', 'lookupData'));
    }

    public function create(Request $request)
    {
        $lookupData = $this->getLookupData();
        $selectedClientId = $request->get('client_id');
        return view('policies.create', compact('lookupData', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_no' => 'required|string|max:255|unique:policies',
            'client_id' => 'required|exists:clients,id',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'policy_class_id' => 'nullable|exists:lookup_values,id',
            'policy_plan_id' => 'nullable|exists:lookup_values,id',
            'sum_insured' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'insured' => 'nullable|string|max:255',
            'policy_status_id' => 'nullable|exists:lookup_values,id',
            'date_registered' => 'required|date',
            'insured_item' => 'nullable|string|max:255',
            'renewable' => 'nullable|boolean',
            'business_type_id' => 'nullable|exists:lookup_values,id',
            'term' => 'nullable|integer',
            'term_unit' => 'nullable|string|max:50',
            'base_premium' => 'nullable|numeric',
            'premium' => 'nullable|numeric',
            'frequency_id' => 'nullable|exists:lookup_values,id',
            'pay_plan_lookup_id' => 'nullable|exists:lookup_values,id',
            'agency_id' => 'nullable|exists:lookup_values,id',
            'agent' => 'nullable|string|max:255',
            'channel_id' => 'nullable|exists:lookup_values,id',
            'notes' => 'nullable|string'
        ]);

        // Generate unique policy_code if not provided
        if (empty($validated['policy_code'])) {
            $latestPolicy = Policy::orderBy('id', 'desc')->first();
            $nextId = $latestPolicy ? $latestPolicy->id + 1 : 1;
            $validated['policy_code'] = 'POL' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        }

        Policy::create($validated);

        return redirect()->route('policies.index')
            ->with('success', 'Policy created successfully.');
    }

    public function show(Request $request, Policy $policy)
    {
        $policy->load([
            'client',
            'schedules.paymentPlans.debitNotes.payments',
        ]);

        // Calculate coverage details with additional metrics
        $coverageStart = $policy->start_date;
        $coverageEnd = $policy->end_date;
        $daysRemaining = $coverageEnd ? (int) round(now()->diffInDays($coverageEnd, false)) : null;
        $coverageDuration = ($coverageStart && $coverageEnd) ? (int) $coverageStart->diffInDays($coverageEnd) : null;
        $premiumDifference = ($policy->premium && $policy->base_premium) ? ($policy->premium - $policy->base_premium) : 0;

        $coverage = [
            'sum_insured' => $policy->sum_insured,
            'base_premium' => $policy->base_premium,
            'premium' => $policy->premium,
            'premium_difference' => $premiumDifference,
            'start_date' => $policy->start_date,
            'end_date' => $policy->end_date,
            'days_remaining' => $daysRemaining,
            'coverage_duration' => $coverageDuration,
            'term' => $policy->term,
            'term_unit' => $policy->term_unit,
        ];

        // Build payment history with totals
        $paymentHistoryData = $policy->schedules
            ->flatMap(function ($schedule) {
                if (!$schedule->paymentPlans || $schedule->paymentPlans->isEmpty()) {
                    return collect([]);
                }
                return $schedule->paymentPlans->map(function ($plan) use ($schedule) {
                    $payments = collect([]);
                    $totalPaid = 0;
                    
                    if ($plan->debitNotes && $plan->debitNotes->isNotEmpty()) {
                        $payments = $plan->debitNotes->flatMap(function ($note) use (&$totalPaid) {
                            if (!$note->payments || $note->payments->isEmpty()) {
                                return collect([]);
                            }
                            return $note->payments->map(function ($payment) use ($note, &$totalPaid) {
                                $totalPaid += (float) ($payment->amount ?? 0);
                                return [
                                    'id' => $payment->id,
                                    'debit_note_no' => (string) ($note->debit_note_no ?? ''),
                                    'payment_reference' => (string) ($payment->payment_reference ?? ''),
                                    'paid_on' => $payment->paid_on ? $payment->paid_on->toDateString() : null,
                                    'amount' => (float) ($payment->amount ?? 0),
                                ];
                            });
                        })->values();
                    }
                    
                    $planAmount = (float) ($plan->amount ?? 0);
                    $outstanding = max(0, $planAmount - $totalPaid);
                    
                    return [
                        'schedule_no' => (string) ($schedule->schedule_no ?? ''),
                        'installment_label' => (string) ($plan->installment_label ?? ''),
                        'due_date' => $plan->due_date ? $plan->due_date->toDateString() : null,
                        'amount' => $planAmount,
                        'total_paid' => $totalPaid,
                        'outstanding' => $outstanding,
                        'status' => (string) ($plan->status ?? 'pending'),
                        'payments' => $payments->toArray(),
                    ];
                });
            })
            ->values();

        $paymentHistory = $paymentHistoryData->toArray();

        // Calculate payment summary statistics
        $paymentSummary = [
            'total_due' => $paymentHistoryData->sum('amount'),
            'total_paid' => $paymentHistoryData->sum('total_paid'),
            'total_outstanding' => $paymentHistoryData->sum('outstanding'),
            'total_installments' => $paymentHistoryData->count(),
            'paid_installments' => $paymentHistoryData->where('status', 'paid')->count(),
            'pending_installments' => $paymentHistoryData->where('status', 'pending')->count(),
            'overdue_installments' => $paymentHistoryData->filter(function($entry) {
                $dueDate = $entry['due_date'] ?? null;
                if (!$dueDate) return false;
                return \Carbon\Carbon::parse($dueDate)->isPast() && $entry['status'] !== 'paid';
            })->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'policy' => $policy,
                'coverage' => $coverage,
                'payment_history' => $paymentHistory,
            ]);
        }

        return view('policies.show', [
            'policy' => $policy,
            'coverage' => $coverage,
            'paymentHistory' => $paymentHistory,
            'paymentSummary' => $paymentSummary,
        ]);
    }

    public function edit(Policy $policy)
    {
        if (request()->expectsJson()) {
            // Return policy with both old and new field names for backward compatibility
            $policyData = $policy->toArray();
            // Add relationship IDs
            $policyData['client_id'] = $policy->client_id;
            $policyData['insurer_id'] = $policy->insurer_id;
            $policyData['policy_class_id'] = $policy->policy_class_id;
            $policyData['policy_plan_id'] = $policy->policy_plan_id;
            $policyData['policy_status_id'] = $policy->policy_status_id;
            $policyData['business_type_id'] = $policy->business_type_id;
            $policyData['frequency_id'] = $policy->frequency_id;
            $policyData['pay_plan_lookup_id'] = $policy->pay_plan_lookup_id;
            $policyData['agency_id'] = $policy->agency_id;
            $policyData['channel_id'] = $policy->channel_id;
            // Also include old field names for JavaScript compatibility
            $policyData['client_name'] = $policy->client_id;
            $policyData['insurer'] = $policy->insurer_id;
            $policyData['policy_class'] = $policy->policy_class_id;
            $policyData['policy_plan'] = $policy->policy_plan_id;
            $policyData['policy_status'] = $policy->policy_status_id;
            $policyData['biz_type'] = $policy->business_type_id;
            $policyData['frequency'] = $policy->frequency_id;
            $policyData['pay_plan'] = $policy->pay_plan_lookup_id;
            $policyData['agency'] = $policy->agency_id;
            $policyData['policy_id'] = $policy->policy_code;
            return response()->json($policyData);
        }
        // fallback for non-AJAX
        $lookupData = $this->getLookupData();
        return view('policies.edit', compact('policy', 'lookupData'));
    }

    public function update(Request $request, Policy $policy)
    {
        $validated = $request->validate([
            'policy_no' => 'required|string|max:255|unique:policies,policy_no,' . $policy->id,
            'client_id' => 'required|exists:clients,id',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'policy_class_id' => 'nullable|exists:lookup_values,id',
            'policy_plan_id' => 'nullable|exists:lookup_values,id',
            'sum_insured' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'insured' => 'nullable|string|max:255',
            'policy_status_id' => 'nullable|exists:lookup_values,id',
            'date_registered' => 'required|date',
            'insured_item' => 'nullable|string|max:255',
            'renewable' => 'nullable|boolean',
            'business_type_id' => 'nullable|exists:lookup_values,id',
            'term' => 'nullable|integer',
            'term_unit' => 'nullable|string|max:50',
            'base_premium' => 'nullable|numeric',
            'premium' => 'nullable|numeric',
            'frequency_id' => 'nullable|exists:lookup_values,id',
            'pay_plan_lookup_id' => 'nullable|exists:lookup_values,id',
            'agency_id' => 'nullable|exists:lookup_values,id',
            'agent' => 'nullable|string|max:255',
            'channel_id' => 'nullable|exists:lookup_values,id',
            'notes' => 'nullable|string'
        ]);

        $policy->update($validated);

        return redirect()->route('policies.index')
            ->with('success', 'Policy updated successfully.');
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();

        return redirect()->route('policies.index')
            ->with('success', 'Policy deleted successfully.');
    }

    public function export(Request $request)
    {
        // Apply same filters as index method
        $query = Policy::query();
        
        // Filter for Due for Renewal
        if ($request->has('dfr') && $request->dfr == 'true') {
            $query->where(function($q) {
                $q->whereHas('policyStatus', function($subQ) {
                    $subQ->where('name', 'LIKE', '%DFR%');
                })->orWhere(function($subQ) {
                    $subQ->whereNotNull('end_date')
                      ->whereBetween('end_date', [now(), now()->addDays(30)]);
                });
            });
        }

        $policies = $query->with([
                'client',
                'insurer',
                'policyClass',
                'policyPlan',
                'policyStatus',
                'businessType',
                'frequency',
                'payPlan',
                'agency'
            ])
            ->orderBy('date_registered', 'desc')
            ->get();
        
        // Get selected columns from session
        $selectedColumns = session('policy_columns', [
            'policy_no','client_name','insurer','policy_class','policy_plan','sum_insured','start_date','end_date','insured','policy_status','date_registered','policy_id','insured_item','renewable','biz_type','term','term_unit','base_premium','premium','frequency','pay_plan','agency','agent','notes'
        ]);
        
        $fileName = 'policies_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        // Column labels mapping
        $columnLabels = [
            'policy_no' => 'Policy No',
            'client_name' => 'Client Name',
            'insurer' => 'Insurer',
            'policy_class' => 'Policy Class',
            'policy_plan' => 'Policy Plan',
            'sum_insured' => 'Sum Insured',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'insured' => 'Insured',
            'policy_status' => 'Policy Status',
            'date_registered' => 'Date Registered',
            'policy_id' => 'Policy ID',
            'insured_item' => 'Insured Item',
            'renewable' => 'Renewable',
            'biz_type' => 'Biz Type',
            'term' => 'Term',
            'term_unit' => 'Term Unit',
            'base_premium' => 'Base Premium',
            'premium' => 'Premium',
            'frequency' => 'Frequency',
            'pay_plan' => 'Pay Plan',
            'agency' => 'Agency',
            'agent' => 'Agent',
            'notes' => 'Notes'
        ];

        return response()->streamDownload(function() use ($policies, $selectedColumns, $columnLabels) {
            $handle = fopen('php://output', 'w');
            
            // Write header row with selected columns
            $headers = [];
            foreach ($selectedColumns as $col) {
                if (isset($columnLabels[$col])) {
                    $headers[] = $columnLabels[$col];
                }
            }
            fputcsv($handle, $headers);

            // Write data rows
            foreach ($policies as $policy) {
                $row = [];
                foreach ($selectedColumns as $col) {
                    switch ($col) {
                        case 'policy_no':
                            $row[] = $policy->policy_no;
                            break;
                        case 'client_name':
                            $row[] = $policy->client ? $policy->client->client_name : 'N/A';
                            break;
                        case 'insurer':
                            $row[] = $policy->insurer ? $policy->insurer->name : 'N/A';
                            break;
                        case 'policy_class':
                            $row[] = $policy->policyClass ? $policy->policyClass->name : 'N/A';
                            break;
                        case 'policy_plan':
                            $row[] = $policy->policyPlan ? $policy->policyPlan->name : 'N/A';
                            break;
                        case 'sum_insured':
                            $row[] = $policy->sum_insured ? number_format($policy->sum_insured, 2) : '';
                            break;
                        case 'start_date':
                            $row[] = $policy->start_date ? $policy->start_date->format('d-M-y') : '';
                            break;
                        case 'end_date':
                            $row[] = $policy->end_date ? $policy->end_date->format('d-M-y') : '';
                            break;
                        case 'insured':
                            $row[] = $policy->insured ?? '';
                            break;
                        case 'policy_status':
                            $row[] = $policy->policyStatus ? $policy->policyStatus->name : 'N/A';
                            break;
                        case 'date_registered':
                            $row[] = $policy->date_registered ? $policy->date_registered->format('d-M-y') : '';
                            break;
                        case 'policy_id':
                            $row[] = $policy->policy_code ?? $policy->id;
                            break;
                        case 'insured_item':
                            $row[] = $policy->insured_item ?? '';
                            break;
                        case 'renewable':
                            $row[] = $policy->renewable ? 'Yes' : 'No';
                            break;
                        case 'biz_type':
                            $row[] = $policy->businessType ? $policy->businessType->name : 'N/A';
                            break;
                        case 'term':
                            $row[] = $policy->term ?? '';
                            break;
                        case 'term_unit':
                            $row[] = $policy->term_unit ?? '';
                            break;
                        case 'base_premium':
                            $row[] = $policy->base_premium ? number_format($policy->base_premium, 2) : '';
                            break;
                        case 'premium':
                            $row[] = $policy->premium ? number_format($policy->premium, 2) : '';
                            break;
                        case 'frequency':
                            $row[] = $policy->frequency ? $policy->frequency->name : 'N/A';
                            break;
                        case 'pay_plan':
                            $row[] = $policy->payPlan ? $policy->payPlan->name : 'N/A';
                            break;
                        case 'agency':
                            $row[] = $policy->agency ? $policy->agency->name : ($policy->agent ?? 'N/A');
                            break;
                        case 'agent':
                            $row[] = $policy->agent ?? '';
                            break;
                        case 'notes':
                            $row[] = $policy->notes ?? '';
                            break;
                        default:
                            $row[] = '';
                            break;
                    }
                }
                fputcsv($handle, $row);
            }
            
            fclose($handle);
        }, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        // Save column settings to session or database
        session(['policy_columns' => $request->columns ?? []]);
        
        return redirect()->route('policies.index')
            ->with('success', 'Column settings saved successfully.');
    }

    private function getLookupData()
    {
        $getLookupValues = function($categoryName) {
            $category = LookupCategory::where('name', $categoryName)->first();
            if (!$category) return [];
            return $category->values()
                ->where('active', true)
                ->get()
                ->map(function($value) {
                    return ['id' => $value->id, 'name' => $value->name];
                })
                ->toArray();
        };

        return [
            'clients' => Client::orderBy('client_name')->get(['id', 'client_name', 'clid'])->toArray(),
            'insurers' => $getLookupValues('Insurers'),
            'policy_classes' => $getLookupValues('Class'),
            'policy_plans' => $getLookupValues('Policy Plans'),
            'policy_statuses' => $getLookupValues('Policy Status') ?: [
                ['id' => null, 'name' => 'In Force'],
                ['id' => null, 'name' => 'DFR'],
                ['id' => null, 'name' => 'Expired'],
                ['id' => null, 'name' => 'Cancelled']
            ],
            'business_types' => $getLookupValues('Business Type') ?: [
                ['id' => null, 'name' => 'Direct'],
                ['id' => null, 'name' => 'Transfer']
            ],
            'term_units' => [
                ['id' => null, 'name' => 'Year'],
                ['id' => null, 'name' => 'Month'],
                ['id' => null, 'name' => 'Days']
            ],
            'frequencies' => $getLookupValues('Frequency') ?: [
                ['id' => null, 'name' => 'Annually'],
                ['id' => null, 'name' => 'Monthly'],
                ['id' => null, 'name' => 'Quarterly'],
                ['id' => null, 'name' => 'One Off'],
                ['id' => null, 'name' => 'Single']
            ],
            'pay_plans' => $getLookupValues('Payment Plan') ?: [
                ['id' => null, 'name' => 'Full'],
                ['id' => null, 'name' => 'Instalments'],
                ['id' => null, 'name' => 'Regular']
            ],
            'agencies' => $getLookupValues('APL Agency'),
            'channels' => $getLookupValues('Channel')
        ];
    }
}