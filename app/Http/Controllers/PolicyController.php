<?php

namespace App\Http\Controllers;
use App\Models\DebitNote;

use App\Models\Policy;
use App\Models\Client;
use App\Models\LookupValue;
use App\Models\LookupCategory;
use App\Models\Schedule;
use App\Models\PaymentPlan;
use App\Models\LifeProposal;
use App\Models\CommissionNote;
use App\Models\CommissionStatement;
use App\Models\Commission;
use App\Models\RenewalNotice;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PolicyController extends Controller
{
    public function index(Request $request)
    {
        $query = Policy::query();
        
        // Filter by client_id if provided
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        
        // Filter by type (life or general)
        if ($request->has('type') && $request->type) {
            if ($request->type == 'life') {
                $query->whereHas('policyClass', function($q) {
                    $q->where('name', 'like', '%life%');
                });
            } elseif ($request->type == 'general') {
                $query->whereDoesntHave('policyClass', function($q) {
                    $q->where('name', 'like', '%life%');
                });
            }
        }
        
        if ($request->has('filter')) {
            if ($request->filter == 'expiring') {
                $today = now()->toDateString();
                $thirtyDaysFromNow = now()->addDays(30)->toDateString();
                $query->whereDate('end_date', '>=', $today)
                      ->whereDate('end_date', '<=', $thirtyDaysFromNow);
            }
        }
        // Filter for Due for Renewal
        if ($request->has('dfr') && $request->dfr == 'true') {
            $today = now()->toDateString();
            $thirtyDaysFromNow = now()->addDays(30)->toDateString();
            
            $query->where(function($q) use ($today, $thirtyDaysFromNow) {
                $q->whereHas('policyStatus', function($subQ) {
                    $subQ->where('name', 'DFR');
                })->orWhere(function($subQ) use ($today, $thirtyDaysFromNow) {
                    $subQ->whereNotNull('end_date')
                         ->whereDate('end_date', '>=', $today)
                         ->whereDate('end_date', '<=', $thirtyDaysFromNow);
                });
            });
        }

        // Filter by search term (searches policy_no, client name, insured)
        if ($request->has('search_term') && $request->search_term) {
            $searchTerm = $request->search_term;
            $query->where(function($q) use ($searchTerm) {
                $q->where('policy_no', 'like', "%{$searchTerm}%")
                  ->orWhere('insured', 'like', "%{$searchTerm}%")
                  ->orWhereHas('client', function($subQ) use ($searchTerm) {
                      $subQ->where('client_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filter by client name
        if ($request->has('client_name') && $request->client_name) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('client_name', 'like', "%{$request->client_name}%");
            });
        }

        // Filter by policy number
        if ($request->has('policy_number') && $request->policy_number) {
            $query->where('policy_no', 'like', "%{$request->policy_number}%");
        }

        // Filter by insurer
        if ($request->has('insurer_id') && $request->insurer_id) {
            $query->where('insurer_id', $request->insurer_id);
        }

        // Filter by policy class (Insurance Class)
        if ($request->has('policy_class_id') && $request->policy_class_id) {
            $query->where('policy_class_id', $request->policy_class_id);
        }

        // Filter by agency
        if ($request->has('agency_id') && $request->agency_id) {
            $query->where('agency_id', $request->agency_id);
        }

        // Filter by agent
        if ($request->has('agent') && $request->agent) {
            $query->where('agent', 'like', "%{$request->agent}%");
        }

        // Filter by policy status
        if ($request->has('policy_status_id') && $request->policy_status_id) {
            $query->where('policy_status_id', $request->policy_status_id);
        }

        // Filter by start date range
        if ($request->has('start_date_from') && $request->start_date_from) {
            $query->whereDate('start_date', '>=', $request->start_date_from);
        }
        if ($request->has('start_date_to') && $request->start_date_to) {
            $query->whereDate('start_date', '<=', $request->start_date_to);
        }

        // Filter by end date range
        if ($request->has('end_date_from') && $request->end_date_from) {
            $query->whereDate('end_date', '>=', $request->end_date_from);
        }
        if ($request->has('end_date_to') && $request->end_date_to) {
            $query->whereDate('end_date', '<=', $request->end_date_to);
        }

        // Filter by premium unpaid (assuming this means premium > 0 and payment status)
        // This would need to check payment records, but for now we'll filter by premium > 0
        if ($request->has('premium_unpaid') && $request->premium_unpaid !== null && $request->premium_unpaid !== '') {
            // This is a simplified check - you may need to join with payments table
            $query->where('premium', '>', 0);
        }

        // Filter by commission unpaid (similar to premium unpaid)
        if ($request->has('comm_unpaid') && $request->comm_unpaid !== null && $request->comm_unpaid !== '') {
            // This would need to check commission records
            // For now, we'll just ensure the query continues
        }

        $perPage = $request->get('record_lines', 10);
        $perPage = max(1, min(100, (int)$perPage)); // Limit between 1 and 100

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
            ->paginate($perPage);
        
        // Get lookup data for dropdowns
        $lookupData = \App\Helpers\LookUpHelper::getLookupData();
        
        // Get life proposal data if generating from proposal
        $lifeProposal = null;
        if ($request->has('life_proposal_id')) {
            $lifeProposal = LifeProposal::find($request->life_proposal_id);
        }
        
        // Get client information if filtering by client_id
        $client = null;
        if ($request->has('client_id') && $request->client_id) {
            $client = Client::find($request->client_id);
        }
        
        return view('policies.index', compact('policies', 'lookupData', 'lifeProposal', 'client'));
    }

    public function create(Request $request)
    {
        $lookupData =  \App\Helpers\LookUpHelper::getLookupData();
        $selectedClientId = $request->get('client_id');
        return view('policies.create', compact('lookupData', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        try {
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
                'wsc' => 'nullable|numeric',
                'lou' => 'nullable|numeric',
                'pa' => 'nullable|numeric',
                'frequency_id' => 'nullable|exists:lookup_values,id',
                'pay_plan_lookup_id' => 'nullable|exists:lookup_values,id',
                'agency_id' => 'nullable|exists:lookup_values,id',
                'agent' => 'nullable|string|max:255',
                'channel_id' => 'nullable|exists:lookup_values,id',
                'cancelled_date' => 'nullable|date',
                'last_endorsement' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'no_of_instalments' => 'nullable|integer' // Allow but don't save if not in table
            ]);

            // Remove fields that don't exist in the table
            unset($validated['no_of_instalments']);

            // Set default values for required NOT NULL fields
            if (!isset($validated['base_premium']) || $validated['base_premium'] === null || $validated['base_premium'] === '') {
                $validated['base_premium'] = 0;
            }
            if (!isset($validated['premium']) || $validated['premium'] === null || $validated['premium'] === '') {
                $validated['premium'] = 0;
            }

            // Generate unique policy_code if not provided
            if (empty($validated['policy_code'])) {
                $validated['policy_code']  = Policy::generatePolicyNo();
                
            }

            // Create policy
            $policy = Policy::create($validated);
            
            if($validated['renewable'] == true){
                    RenewalNotice::create([
                    'policy_id' => $policy->id,
                    'rnid' => RenewalNotice::generateRNID(), // You need to implement
                    'notice_date' => now(),
                    'status' => 'pending',
                    'delivery_method' => 'email', // or from request
                ]);
          
            }
            // Create schedule and payment plans if payment plan data is provided
            $this->createScheduleAndPaymentPlans($policy, $request);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Policy created successfully.',
                    'redirect' => route('policies.index', ['policy_id' => $policy->id])
                ]);
            }

            return redirect()->route('policies.index', ['policy_id' => $policy->id])
                ->with('success', 'Policy created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Policy creation error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating policy: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Error creating policy: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Request $request, Policy $policy)
    {
        $policy->load([
            'client',
            'insurer',
            'policyClass',
            'policyPlan',
            'policyStatus',
            'businessType',
            'frequency',
            'payPlan',
            'agency',
            'channel',
            'schedules.paymentPlans.debitNotes.payments',
        ]);

        // Load documents manually since they're tied by policy_code or policy_no
        $tiedTo = $policy->policy_code ?? $policy->policy_no;
        $documents = \App\Models\Document::where('tied_to', $tiedTo)->get();
        $policy->setRelation('documents', $documents);

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
            // Add client source information to policy data
            $policyData = $policy->toArray();
            if ($policy->client) {
                $policyData['client'] = [
                    'id' => $policy->client->id,
                    'client_name' => $policy->client->client_name,
                    'source' => $policy->client->source,
                    'source_name' => $policy->client->source_name,
                    'first_name' => $policy->client->first_name,
                    'surname' => $policy->client->surname,
                ];
                $policyData['client_name'] = $policy->client->client_name;
                $policyData['source'] = $policy->client->source;
                $policyData['source_name'] = $policy->client->source_name;
            }
            // Add relationship names - use accessors first, then fallback to relationships
            // Helper function to safely get name from relationship
            $getName = function($accessor, $relationship) {
                if ($accessor) return $accessor;
                if ($relationship && is_object($relationship) && isset($relationship->name)) {
                    return $relationship->name;
                }
                return null;
            };
            
            $policyData['insurer_name'] = $getName($policy->insurer_name, $policy->insurer);
            $policyData['policy_class_name'] = $getName($policy->policy_class_name, $policy->policyClass);
            $policyData['policy_plan_name'] = $getName($policy->policy_plan_name, $policy->policyPlan);
            $policyData['policy_status_name'] = $getName($policy->policy_status_name, $policy->policyStatus);
            $policyData['business_type_name'] = $getName($policy->business_type_name, $policy->businessType);
            $policyData['frequency_name'] = $getName($policy->frequency_name, $policy->frequency);
            $policyData['pay_plan_name'] = $getName($policy->pay_plan_name, $policy->payPlan);
            $policyData['agency_name'] = $getName($policy->agency_name, $policy->agency);
            $policyData['channel_name'] = $getName($policy->channel_name, $policy->channel);
            
            // Also include the relationship objects for fallback in JavaScript
            if ($policy->insurer && is_object($policy->insurer) && isset($policy->insurer->name)) {
                $policyData['insurer'] = ['name' => $policy->insurer->name];
            }
            if ($policy->policyClass && is_object($policy->policyClass) && isset($policy->policyClass->name)) {
                $policyData['policyClass'] = ['name' => $policy->policyClass->name];
            }
            if ($policy->policyPlan && is_object($policy->policyPlan) && isset($policy->policyPlan->name)) {
                $policyData['policyPlan'] = ['name' => $policy->policyPlan->name];
            }
            if ($policy->policyStatus && is_object($policy->policyStatus) && isset($policy->policyStatus->name)) {
                $policyData['policyStatus'] = ['name' => $policy->policyStatus->name];
            }
            if ($policy->businessType && is_object($policy->businessType) && isset($policy->businessType->name)) {
                $policyData['businessType'] = ['name' => $policy->businessType->name];
            }
            if ($policy->frequency && is_object($policy->frequency) && isset($policy->frequency->name)) {
                $policyData['frequency'] = ['name' => $policy->frequency->name];
            }
            if ($policy->payPlan && is_object($policy->payPlan) && isset($policy->payPlan->name)) {
                $policyData['payPlan'] = ['name' => $policy->payPlan->name];
            }
            if ($policy->agency && is_object($policy->agency) && isset($policy->agency->name)) {
                $policyData['agency'] = ['name' => $policy->agency->name];
            }
            if ($policy->channel && is_object($policy->channel) && isset($policy->channel->name)) {
                $policyData['channel'] = ['name' => $policy->channel->name];
            }
            
            // Calculate NOP (Number of Payments) from payment plans
            $totalInstalments = 0;
            $paymentPlanFrequency = null;
            if ($policy->schedules && $policy->schedules->isNotEmpty()) {
                foreach ($policy->schedules as $schedule) {
                    if ($schedule->paymentPlans && $schedule->paymentPlans->isNotEmpty()) {
                        $totalInstalments += $schedule->paymentPlans->count();
                        // Get frequency from first payment plan if not already set
                        if (!$paymentPlanFrequency && $schedule->paymentPlans->first()) {
                            $paymentPlanFrequency = $schedule->paymentPlans->first()->frequency;
                        }
                    }
                }
            }
            $policyData['no_of_instalments'] = $totalInstalments;
            $policyData['payment_plan_frequency'] = $paymentPlanFrequency;
            
            // Include schedules with payment plans for frontend
            if ($policy->schedules && $policy->schedules->isNotEmpty()) {
                $policyData['schedules'] = $policy->schedules->map(function($schedule) {
                    return [
                        'id' => $schedule->id,
                        'schedule_no' => $schedule->schedule_no,
                        'payment_plans' => $schedule->paymentPlans ? $schedule->paymentPlans->map(function($plan) {
                            return [
                                'id' => $plan->id,
                                'frequency' => $plan->frequency,
                                'amount' => $plan->amount,
                            ];
                        })->toArray() : []
                    ];
                })->toArray();
            }
            
            // Include documents if they exist
            if ($policy->documents && $policy->documents->count() > 0) {
                $policyData['documents'] = $policy->documents->map(function($doc) {
                    $dateAdded = null;
                    if ($doc->date_added) {
                        // Handle both Carbon instance and string
                        if (is_string($doc->date_added)) {
                            $dateAdded = $doc->date_added;
                        } elseif (is_object($doc->date_added) && method_exists($doc->date_added, 'toDateString')) {
                            $dateAdded = $doc->date_added->toDateString();
                        } else {
                            $dateAdded = (string) $doc->date_added;
                        }
                    }
                    
                    return [
                        'id' => $doc->id,
                        'name' => $doc->name,
                        'file_name' => $doc->name,
                        'type' => $doc->type,
                        'format' => $doc->format,
                        'file_path' => $doc->file_path,
                        'date_added' => $dateAdded,
                    ];
                })->toArray();
            } else {
                $policyData['documents'] = [];
            }
            
            return response()->json([
                'policy' => $policyData,
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
        $policy->load([
            'client',
            'insurer',
            'policyClass',
            'policyPlan',
            'policyStatus',
            'businessType',
            'frequency',
            'payPlan',
            'agency',
            'channel',
        ]);
        
        // Load documents manually since they're tied by policy_code or policy_no
        $tiedTo = $policy->policy_code ?? $policy->policy_no;
        $documents = \App\Models\Document::where('tied_to', $tiedTo)->get();
        $policy->setRelation('documents', $documents);
        
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
            // Add client data
            if ($policy->client) {
                $policyData['client'] = [
                    'id' => $policy->client->id,
                    'client_name' => $policy->client->client_name,
                    'source' => $policy->client->source,
                    'source_name' => $policy->client->source_name,
                    'first_name' => $policy->client->first_name,
                    'surname' => $policy->client->surname,
                ];
                $policyData['client_name'] = $policy->client->client_name;
                $policyData['source'] = $policy->client->source;
                $policyData['source_name'] = $policy->client->source_name;
            }
            // Add relationship names
            $policyData['insurer_name'] = $policy->insurer_name;
            $policyData['policy_class_name'] = $policy->policy_class_name;
            $policyData['policy_plan_name'] = $policy->policy_plan_name;
            $policyData['policy_status_name'] = $policy->policy_status_name;
            $policyData['business_type_name'] = $policy->business_type_name;
            $policyData['frequency_name'] = $policy->frequency_name;
            $policyData['pay_plan_name'] = $policy->pay_plan_name;
            $policyData['agency_name'] = $policy->agency_name;
            $policyData['channel_name'] = $policy->channel_name;
            // Include field IDs for JavaScript compatibility (using _id fields)
            $policyData['insurer'] = $policy->insurer_id;
            $policyData['policy_class'] = $policy->policy_class_id;
            $policyData['policy_plan'] = $policy->policy_plan_id;
            $policyData['policy_status'] = $policy->policy_status_id;
            $policyData['biz_type'] = $policy->business_type_id;
            $policyData['frequency'] = $policy->frequency_id;
            $policyData['pay_plan'] = $policy->pay_plan_lookup_id;
            $policyData['agency'] = $policy->agency_id;
            $policyData['channel'] = $policy->channel_id;
            $policyData['policy_code'] = $policy->policy_code;
            return response()->json($policyData);
        }
        // fallback for non-AJAX
        $lookupData =  \App\Helpers\LookUpHelper::getLookupData();
        return view('policies.edit', compact('policy', 'lookupData'));
    }

    public function update(Request $request, Policy $policy)
    {
        try {
            // Convert empty strings to null for nullable fields before validation
            $requestData = $request->all();
            $nullableForeignKeyFields = ['channel_id', 'insurer_id', 'policy_class_id', 'policy_plan_id', 
                                         'policy_status_id', 'business_type_id', 'frequency_id', 
                                         'pay_plan_lookup_id', 'agency_id'];
            foreach ($nullableForeignKeyFields as $field) {
                if (isset($requestData[$field]) && $requestData[$field] === '') {
                    $requestData[$field] = null;
                }
            }
            // Handle nullable date fields - convert empty strings to null
            if (isset($requestData['cancelled_date']) && $requestData['cancelled_date'] === '') {
                $requestData['cancelled_date'] = null;
            }
            // Merge back into request
            $request->merge($requestData);
            
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
            'wsc' => 'nullable|numeric',
            'lou' => 'nullable|numeric',
            'pa' => 'nullable|numeric',
            'frequency_id' => 'nullable|exists:lookup_values,id',
            'pay_plan_lookup_id' => 'nullable|exists:lookup_values,id',
            'agency_id' => 'nullable|exists:lookup_values,id',
            'agent' => 'nullable|string|max:255',
            'channel_id' => 'nullable|exists:lookup_values,id',
            'cancelled_date' => 'nullable|date',
            'last_endorsement' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
            ]);

            // Set default values for premium and base_premium if they're empty
            if (!isset($validated['base_premium']) || $validated['base_premium'] === null || $validated['base_premium'] === '') {
                $validated['base_premium'] = $policy->base_premium ?? 0;
            }
            if (!isset($validated['premium']) || $validated['premium'] === null || $validated['premium'] === '') {
                $validated['premium'] = $policy->premium ?? 0;
            }

            // Handle term_unit: convert empty string to null, or skip if null to preserve existing value
            if (isset($validated['term_unit']) && $validated['term_unit'] === '') {
                $validated['term_unit'] = null;
            }
            
            // Handle cancelled_date: convert empty string to null, and ensure it's in validated if present in request
            if ($request->has('cancelled_date')) {
                $cancelledDate = $request->input('cancelled_date');
                if ($cancelledDate === '' || $cancelledDate === null) {
                    $validated['cancelled_date'] = null;
                } else {
                    $validated['cancelled_date'] = $cancelledDate;
                }
            } elseif (isset($validated['cancelled_date']) && $validated['cancelled_date'] === '') {
                $validated['cancelled_date'] = null;
            }
            
            // Handle last_endorsement: convert empty string to null, and ensure it's in validated if present in request
            if ($request->has('last_endorsement')) {
                $lastEndorsement = $request->input('last_endorsement');
                if ($lastEndorsement === '' || $lastEndorsement === null) {
                    $validated['last_endorsement'] = null;
                } else {
                    $validated['last_endorsement'] = $lastEndorsement;
                }
            } elseif (isset($validated['last_endorsement']) && $validated['last_endorsement'] === '') {
                $validated['last_endorsement'] = null;
            }
            
            // Handle nullable foreign key fields: convert empty string to null (already done before validation, but double-check)
            $nullableForeignKeyFields = ['channel_id', 'insurer_id', 'policy_class_id', 'policy_plan_id', 
                                         'policy_status_id', 'business_type_id', 'frequency_id', 
                                         'pay_plan_lookup_id', 'agency_id'];
            foreach ($nullableForeignKeyFields as $field) {
                if (isset($validated[$field]) && $validated[$field] === '') {
                    $validated[$field] = null;
                }
            }
            
            // Update or create schedule and payment plans if payment plan data is provided
            $this->updateScheduleAndPaymentPlans($policy, $request);

            // Filter out null values for fields that might have database constraints
            // This preserves existing values when fields are not provided
            // BUT: Always include cancelled_date, last_endorsement, and nullable foreign keys even if null (to allow clearing them)
            // Also include any field that has a non-null value
            $fieldsToAlwaysInclude = ['cancelled_date', 'last_endorsement'];
            $fieldsToAlwaysInclude = array_merge($fieldsToAlwaysInclude, $nullableForeignKeyFields);
            $updateData = [];
            foreach ($validated as $key => $value) {
                // Include if: it's in the always-include list, OR it has a non-null value
                if (in_array($key, $fieldsToAlwaysInclude) || $value !== null) {
                    $updateData[$key] = $value;
                }
            }
            
            // Ensure cancelled_date and last_endorsement are included even if not in validated (check request directly)
            // This handles cases where the field is present in the form but might not pass validation
            if ($request->has('cancelled_date') || array_key_exists('cancelled_date', $request->all())) {
                $cancelledDate = $request->input('cancelled_date');
                $updateData['cancelled_date'] = ($cancelledDate === '' || $cancelledDate === null) ? null : $cancelledDate;
            }
            if ($request->has('last_endorsement') || array_key_exists('last_endorsement', $request->all())) {
                $lastEndorsement = $request->input('last_endorsement');
                $updateData['last_endorsement'] = ($lastEndorsement === '' || $lastEndorsement === null) ? null : $lastEndorsement;
            }
            
            // Set default values for required NOT NULL fields if they're being updated
            if (isset($updateData['base_premium']) && ($updateData['base_premium'] === null || $updateData['base_premium'] === '')) {
                $updateData['base_premium'] = $policy->base_premium ?? 0;
            }
            if (isset($updateData['premium']) && ($updateData['premium'] === null || $updateData['premium'] === '')) {
                $updateData['premium'] = $policy->premium ?? 0;
            }

            // Debug: Log what's being updated (remove in production)
            // \Log::info('Updating policy', ['id' => $policy->id, 'data' => $updateData]);

            $policy->update($updateData);
            if (!empty($validated['renewable']) && $validated['renewable']) {
                        // Check if a RenewalNotice already exists
                        $renewalNotice = $policy->renewalNotices()->first();
                        if (!$renewalNotice) {
                            // Create a new RenewalNotice
                            $policy->renewalNotices()->create([
                                'rnid' => \App\Models\RenewalNotice::generateRNID(),
                                'notice_date' => now(),
                                'status' => 'pending',
                                'delivery_method' => 'email', // Or get from request if available
                            ]);
                        }
                    } else {
                        // Policy no longer renewable: optionally delete or deactivate the existing RenewalNotice
                        $policy->renewalNotices()->delete(); // Or update(['status'=>'cancelled'])
                    }
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Policy updated successfully.',
                    'redirect' => route('policies.index')
                ]);
            }

            return redirect()->route('policies.index')
                ->with('success', 'Policy updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Policy update error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating policy: ' . $e->getMessage(),
                    'errors' => ['general' => $e->getMessage()]
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Error updating policy: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Policy $policy)
    {
        try {
            // Check for related records before deletion
            $relatedRecords = [];
            
            // Check schedules
            if ($policy->schedules()->count() > 0) {
                $relatedRecords[] = 'Schedules (' . $policy->schedules()->count() . ')';
            }
            
            // Check documents tied to this policy
            $tiedTo = $policy->policy_code ?? $policy->policy_no;
            $documentsCount = \App\Models\Document::where('tied_to', $tiedTo)->count();
            if ($documentsCount > 0) {
                $relatedRecords[] = 'Documents (' . $documentsCount . ')';
            }
            
            // Check vehicles (if policy_no is used)
            $vehiclesCount = \App\Models\Vehicle::where('policy_no', $policy->policy_no)->count();
            if ($vehiclesCount > 0) {
                $relatedRecords[] = 'Vehicles (' . $vehiclesCount . ')';
            }
            
            // Check claims (if policy_no is used)
            $claimsCount = \App\Models\Claim::where('policy_no', $policy->policy_no)->count();
            if ($claimsCount > 0) {
                $relatedRecords[] = 'Claims (' . $claimsCount . ')';
            }
            
            // Check commissions (uses policy_number)
            $commissionsCount = \App\Models\Commission::where('policy_number', $policy->policy_no)->count();
            if ($commissionsCount > 0) {
                $relatedRecords[] = 'Commissions (' . $commissionsCount . ')';
            }
            
            // Check nominees (if table exists and uses policy_id)
            if (Schema::hasTable('nominees')) {
                $nomineesCount = DB::table('nominees')->where('policy_id', $policy->id)->count();
                if ($nomineesCount > 0) {
                    $relatedRecords[] = 'Nominees (' . $nomineesCount . ')';
                }
            }
            
            // Check claims by policy_id if column exists
            if (Schema::hasTable('claims') && Schema::hasColumn('claims', 'policy_id')) {
                $claimsByIdCount = DB::table('claims')->where('policy_id', $policy->id)->count();
                if ($claimsByIdCount > 0) {
                    $relatedRecords[] = 'Claims by ID (' . $claimsByIdCount . ')';
                }
            }
            
            if (!empty($relatedRecords)) {
                $message = 'Cannot delete policy. It has related records: ' . implode(', ', $relatedRecords);
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                
                return redirect()->route('policies.index')
                    ->with('error', $message);
            }
            
            $policy->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Policy deleted successfully.'
                ]);
            }

            return redirect()->route('policies.index')
                ->with('success', 'Policy deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Policy deletion error: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting policy: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('policies.index')
                ->with('error', 'Error deleting policy: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // Apply same filters as index method
        $query = Policy::query();
        
        // Filter for Due for Renewal
        if ($request->has('dfr') && $request->dfr == 'true') {
            $today = now()->toDateString();
            $thirtyDaysFromNow = now()->addDays(30)->toDateString();
            
            $query->where(function($q) use ($today, $thirtyDaysFromNow) {
                $q->whereHas('policyStatus', function($subQ) {
                    $subQ->where('name', 'DFR');
                })->orWhere(function($subQ) use ($today, $thirtyDaysFromNow) {
                    $subQ->whereNotNull('end_date')
                         ->whereDate('end_date', '>=', $today)
                         ->whereDate('end_date', '<=', $thirtyDaysFromNow);
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
        
        // Get selected columns from session (using relationship-based field names)
        $selectedColumns = session('policy_columns', [
            'policy_no','client_name','insurer','policy_class','policy_plan','sum_insured','start_date','end_date','insured','policy_status','date_registered','policy_code','insured_item','renewable','biz_type','term','term_unit','base_premium','premium','frequency','pay_plan','agency','agent','notes'
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
            'policy_code' => 'Policy Code',
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
                        case 'policy_code':
                            $row[] = $policy->policy_code ?? '';
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

    public function uploadDocument(Request $request, Policy $policy)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'document_type' => 'required|in:policy_document,certificate,claim_document,other',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentType = $request->document_type;
            
            // Map document types to names
            $documentNames = [
                'policy_document' => 'Policy Document',
                'certificate' => 'Certificate',
                'claim_document' => 'Claim Document',
                'other' => 'Other Document'
            ];
            
            $filename = 'policy_' . $policy->id . '_' . $documentType . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = \App\Models\Document::orderBy('id', 'desc')->first();
            $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . $nextId;

            // Store in documents table - tie to policy using policy_code or policy_no
            $tiedTo = $policy->policy_code ?? $policy->policy_no;
            \App\Models\Document::create([
                'doc_id' => $docId,
                'tied_to' => $tiedTo,
                'name' => $documentNames[$documentType],
                'group' => 'Policy Document',
                'type' => $documentType,
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
            
            // Reload documents
            $documents = \App\Models\Document::where('tied_to', $tiedTo)->get();
            $policy->setRelation('documents', $documents);
        }

        // Load documents for this policy
        $tiedTo = $policy->policy_code ?? $policy->policy_no;
        $documents = \App\Models\Document::where('tied_to', $tiedTo)->get();
        $policy->setRelation('documents', $documents);
        
        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'documents' => $documents,
            'policy' => $policy
        ]);
    }

    // Removed populateLegacyFields() - redundant fields have been removed from database

    private function createScheduleAndPaymentPlans(Policy $policy, Request $request)
    {
        // Check if payment plan data is provided
        $noOfInstalments = $request->input('no_of_instalments');
        $paymentStartDate = $request->input('payment_start_date');
        $paymentEndDate = $request->input('payment_end_date');
        $frequencyId = $request->input('frequency_id');
        $payPlanId = $request->input('pay_plan_lookup_id');
        
        // Check if pay plan is "Full" - if so, create single payment
        $payPlan = $payPlanId ? LookupValue::find($payPlanId) : null;
        $payPlanName = $payPlan ? strtolower($payPlan->name) : '';
        
        // If "Full" payment plan, always create single payment
        if ($payPlanName === 'full') {
            $noOfInstalments = 1;
        }
        
        // Default payment dates to policy dates if not provided
        if (!$paymentStartDate) {
            $paymentStartDate = $policy->start_date?->format('Y-m-d') ?: now()->format('Y-m-d');
        }
        if (!$paymentEndDate && $policy->end_date) {
            $paymentEndDate = $policy->end_date->format('Y-m-d');
        }
        
        // Only create if we have the necessary data
        if (!$paymentStartDate || !$frequencyId) {
            return;
        }
        
        // Default to 1 instalment if not specified
        if (!$noOfInstalments || $noOfInstalments < 1) {
            $noOfInstalments = 1;
        }
        
        DB::beginTransaction();
        try {
            // Get frequency name
            $frequency = LookupValue::find($frequencyId);
            $frequencyName = $frequency ? strtolower($frequency->name) : 'monthly';
            
            // Check if payment plan is "Full" (single payment) or "Instalments" (multiple)
            $payPlanId = $request->input('pay_plan_lookup_id');
            $payPlan = $payPlanId ? LookupValue::find($payPlanId) : null;
            $payPlanName = $payPlan ? strtolower($payPlan->name) : '';
            
            // If payment plan is "Full", create single payment, otherwise use instalments
            if ($payPlanName === 'full') {
                $noOfInstalments = 1;
            }
            
            // Create schedule for this policy
            $latestSchedule = Schedule::orderBy('id', 'desc')->first();
            $nextScheduleId = $latestSchedule ? $latestSchedule->id + 1 : 1;
            $scheduleNo = 'SCH' . str_pad($nextScheduleId, 6, '0', STR_PAD_LEFT);
            
            $schedule = Schedule::create([
                'policy_id' => $policy->id,
                'schedule_no' => $scheduleNo,
                'issued_on' => $policy->date_registered ?? now(),
                'effective_from' => $policy->start_date,
                'effective_to' => $policy->end_date,
                'status' => 'active',
                'notes' => 'Auto-generated from policy creation'
            ]);
            
            // Calculate payment plan details
            $totalPremium = $policy->premium ?? $policy->base_premium ?? 0;
            $amountPerInstalment = $totalPremium / $noOfInstalments;
            
            // Calculate due dates based on frequency
            $dueDate = Carbon::parse($paymentStartDate);
            $endDate = $paymentEndDate ? Carbon::parse($paymentEndDate) : null;
            
            // Create payment plans
            for ($i = 1; $i <= $noOfInstalments; $i++) {
                // Check if we've exceeded the end date
                if ($endDate && $dueDate->gt($endDate)) {
                    break;
                }
                
               $paymentPlan =   PaymentPlan::create([
                    'schedule_id' => $schedule->id,
                    'installment_label' => $noOfInstalments == 1 ? 'Full Payment' : ('Instalment ' . $i . ' of ' . $noOfInstalments),
                    'due_date' => $dueDate->copy(),
                    'amount' => $amountPerInstalment,
                    'frequency' => $frequencyName,
                    'status' => 'pending',
                ]);
                 DebitNote::create([
                'payment_plan_id' => $paymentPlan->id,
                'debit_note_no'   => DebitNote::generateDebitNoteNo(),
                'issued_on'       => $dueDate->copy(),
                'amount'          => $amountPerInstalment,
                'status'          => 'unpaid',
                'document_path'   => null,
                'is_encrypted'    => false,
                ]);

                // Calculate next due date based on frequency (skip if it's the last instalment)
                if ($i < $noOfInstalments) {
                    switch ($frequencyName) {
                        case 'monthly':
                            $dueDate->addMonth();
                            break;
                        case 'quarterly':
                            $dueDate->addMonths(3);
                            break;
                        case 'annually':
                        case 'yearly':
                            $dueDate->addYear();
                            break;
                        case 'single':
                        case 'one off':
                            // Only one payment
                            break;
                        default:
                            $dueDate->addMonth();
                    }
                }
            }
            
            // -------------------------
                // COMMISSION CREATION
                // -------------------------
                $commissionRate = $policy->commission_rate ?? 0; // e.g. 10%
                $expectedCommission = ($totalPremium * $commissionRate) / 100;

                // Commission Note
                $commissionNote = CommissionNote::create([
                    'schedule_id' => $schedule->id,
                    'com_note_id' => 'CN' . str_pad($schedule->id, 6, '0', STR_PAD_LEFT),
                    'issued_on' => now(),
                    'total_premium' => $totalPremium,
                    'expected_commission' => $expectedCommission,
                    'remarks' => 'Auto-generated commission note',
                ]);

                // Commission Statement
                $commissionStatement = CommissionStatement::create([
                    'commission_note_id' => $commissionNote->id,
                    'com_stat_id' => 'CS' . str_pad($schedule->id, 6, '0', STR_PAD_LEFT),
                    'period_start' => $policy->start_date,
                    'period_end' => $policy->end_date,
                    'net_commission' => $expectedCommission,
                    'tax_withheld' => 0,
                    'remarks' => 'Auto-generated commission statement',
                ]);

                // Commission record
                Commission::create([
                    'grouping' => 'Policy Commission',
                    'basic_premium' => $totalPremium,
                    'rate' => $commissionRate,
                    'amount_due' => $expectedCommission,
                    'payment_status_id' => null,
                    'amount_received' => 0,
                    'commission_code' => 'COM' . str_pad($schedule->id, 6, '0', STR_PAD_LEFT),
                    'date_due' => $policy->end_date,
                    'commission_note_id' => $commissionNote->id,
                    'commission_statement_id' => $commissionStatement->id,
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating schedule and payment plans: ' . $e->getMessage());
            // Don't throw - allow policy to be created even if schedule/payment plan creation fails
        }
    }
    
    private function updateScheduleAndPaymentPlans(Policy $policy, Request $request)
    {
        $noOfInstalments   = $request->input('no_of_instalments');
        $paymentStartDate  = $request->input('payment_start_date');
        $paymentEndDate    = $request->input('payment_end_date');
        $frequencyId       = $request->input('frequency_id');
        $payPlanId         = $request->input('pay_plan_lookup_id');

        $payPlan = $payPlanId ? LookupValue::find($payPlanId) : null;
        $payPlanName = $payPlan ? strtolower($payPlan->name) : '';

        if ($payPlanName === 'full') {
            $noOfInstalments = 1;
        }

        if (!$paymentStartDate) {
            $paymentStartDate = $policy->start_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        }

        if (!$paymentEndDate && $policy->end_date) {
            $paymentEndDate = $policy->end_date->format('Y-m-d');
        }

        if (!$paymentStartDate || !$frequencyId) {
            return;
        }

        if (!$noOfInstalments || $noOfInstalments < 1) {
            $noOfInstalments = 1;
        }

        DB::beginTransaction();

        try {
            /** -----------------------------
             * Get or Create Schedule
             * ------------------------------ */
            $schedule = $policy->schedules()->first();

            if (!$schedule) {
                $latest = Schedule::latest('id')->first();
                $scheduleNo = 'SCH' . str_pad(($latest->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

                $schedule = Schedule::create([
                    'policy_id'      => $policy->id,
                    'schedule_no'    => $scheduleNo,
                    'issued_on'      => $policy->date_registered ?? now(),
                    'effective_from' => $policy->start_date,
                    'effective_to'   => $policy->end_date,
                    'status'         => 'active',
                    'notes'          => 'Auto-generated from policy update',
                ]);
            } else {
                $schedule->update([
                    'effective_from' => $policy->start_date,
                    'effective_to'   => $policy->end_date,
                ]);
            }

            /** -----------------------------
             * Delete OLD Debit Notes first
             * ------------------------------ */
            DebitNote::whereIn(
                'payment_plan_id',
                $schedule->paymentPlans()->pluck('id')
            )->delete();

            /** -----------------------------
             * Delete OLD Payment Plans
             * ------------------------------ */
            $schedule->paymentPlans()->delete();

            /** -----------------------------
             * Recreate Payment Plans
             * ------------------------------ */
            $frequency = LookupValue::find($frequencyId);
            $frequencyName = strtolower($frequency->name ?? 'monthly');

            $totalPremium = $policy->premium ?? $policy->base_premium ?? 0;
            $amountPerInstalment = round($totalPremium / $noOfInstalments, 2);

            $dueDate = Carbon::parse($paymentStartDate);
            $endDate = $paymentEndDate ? Carbon::parse($paymentEndDate) : null;

            for ($i = 1; $i <= $noOfInstalments; $i++) {

                if ($endDate && $dueDate->gt($endDate)) {
                    break;
                }

                $paymentPlan = PaymentPlan::create([
                    'schedule_id'       => $schedule->id,
                    'installment_label' => $noOfInstalments === 1
                        ? 'Full Payment'
                        : "Instalment {$i} of {$noOfInstalments}",
                    'due_date' => $dueDate->copy(),
                    'amount'   => $amountPerInstalment,
                    'frequency'=> $frequencyName,
                    'status'   => 'pending',
                ]);

                /** -----------------------------
                 * Create Debit Note
                 * ------------------------------ */
                DebitNote::create([
                    'payment_plan_id' => $paymentPlan->id,
                    'debit_note_no'   => DebitNote::generateDebitNoteNo(),
                    'issued_on'       => $dueDate->copy(),
                    'amount'          => $amountPerInstalment,
                    'status'          => 'unpaid',
                    'document_path'   => null,
                    'is_encrypted'    => false,
                ]);

                if ($i < $noOfInstalments) {
                    match ($frequencyName) {
                        'monthly'   => $dueDate->addMonth(),
                        'quarterly' => $dueDate->addMonths(3),
                        'annually', 'yearly' => $dueDate->addYear(),
                        default     => $dueDate->addMonth(),
                    };
                }
            }

            /** -----------------------------
             * Create/Update Commission
             * ------------------------------ */
            $commissionRate = $policy->commission_rate ?? 0;
            $expectedCommission = ($totalPremium * $commissionRate) / 100;

            // Commission Note
            $commissionNote = $schedule->commissionNotes()->first();
            if (!$commissionNote) {
                $commissionNote = CommissionNote::create([
                    'schedule_id'         => $schedule->id,
                    'com_note_id'         => 'CN' . str_pad($schedule->id, 6, '0', STR_PAD_LEFT),
                    'issued_on'           => now(),
                    'total_premium'       => $totalPremium,
                    'expected_commission' => $expectedCommission,
                    'remarks'             => 'Auto-generated commission note',
                ]);
            } else {
                $commissionNote->update([
                    'total_premium'       => $totalPremium,
                    'expected_commission' => $expectedCommission,
                ]);
            }

            // Commission Statement
            $commissionStatement = $commissionNote->commissionStatements()->first();
            if (!$commissionStatement) {
                $commissionStatement = CommissionStatement::create([
                    'commission_note_id' => $commissionNote->id,
                    'com_stat_id'        => 'CS' . str_pad($schedule->id, 6, '0', STR_PAD_LEFT),
                    'period_start'       => $policy->start_date,
                    'period_end'         => $policy->end_date,
                    'net_commission'     => $expectedCommission,
                    'tax_withheld'       => 0,
                    'remarks'            => 'Auto-generated commission statement',
                ]);
            } else {
                $commissionStatement->update([
                    'period_start'   => $policy->start_date,
                    'period_end'     => $policy->end_date,
                    'net_commission' => $expectedCommission,
                ]);
            }

            // Commission
            $commission = $commissionStatement->commissions()->first();
            if (!$commission) {
                Commission::create([
                    'commission_code' => 'COM' . str_pad($schedule->id, 6, '0', STR_PAD_LEFT),
                    'grouping'                => 'Policy Commission',
                    'basic_premium'           => $totalPremium,
                    'rate'                    => $commissionRate,
                    'amount_due'              => $expectedCommission,
                    'payment_status_id'       => null,
                    'amount_received'         => 0,
                    'date_due'                => $policy->end_date,
                    'commission_note_id'      => $commissionNote->id,
                    'commission_statement_id' => $commissionStatement->id,
                ]);
            } else {
                $commission->update([
                    'basic_premium' => $totalPremium,
                    'rate'          => $commissionRate,
                    'amount_due'    => $expectedCommission,
                    'date_due'      => $policy->end_date,
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating schedule/payment/debit notes/commissions', [
                'policy_id' => $policy->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }


    // private function getLookupData()
    // {
    //     $getLookupValues = function($categoryName) {
    //         $category = LookupCategory::where('name', $categoryName)->first();
    //         if (!$category) return [];
    //         return $category->values()
    //             ->where('active', true)
    //             ->get()
    //             ->map(function($value) {
    //                 return ['id' => $value->id, 'name' => $value->name];
    //             })
    //             ->toArray();
    //     };

    //     return [
    //         'clients' => Client::orderBy('client_name')->get(['id', 'client_name', 'clid'])->toArray(),
    //         'insurers' => $getLookupValues('Insurers'),
    //         'policy_classes' => $getLookupValues('Class'),
    //         'policy_plans' => $getLookupValues('Policy Plans'),
    //         'policy_statuses' => $getLookupValues('Policy Status') ?: [
    //             ['id' => null, 'name' => 'In Force'],
    //             ['id' => null, 'name' => 'DFR'],
    //             ['id' => null, 'name' => 'Expired'],
    //             ['id' => null, 'name' => 'Cancelled']
    //         ],
    //         'business_types' => $getLookupValues('Business Type') ?: [
    //             ['id' => null, 'name' => 'Direct'],
    //             ['id' => null, 'name' => 'Transfer']
    //         ],
    //         'term_units' => $getLookupValues('Term Units') ?: [
    //             ['id' => null, 'name' => 'Year'],
    //             ['id' => null, 'name' => 'Month'],
    //             ['id' => null, 'name' => 'Days']
    //         ],
    //         'frequencies' => $getLookupValues('Frequency') ?: [
    //             ['id' => null, 'name' => 'Annually'],
    //             ['id' => null, 'name' => 'Monthly'],
    //             ['id' => null, 'name' => 'Quarterly'],
    //             ['id' => null, 'name' => 'One Off'],
    //             ['id' => null, 'name' => 'Single']
    //         ],
    //         'pay_plans' => $getLookupValues('Payment Plan') ?: [
    //             ['id' => null, 'name' => 'Full'],
    //             ['id' => null, 'name' => 'Instalments'],
    //             ['id' => null, 'name' => 'Regular']
    //         ],
    //         'document_types' => $getLookupValues('Document Type') ?: [
    //             ['id' => null, 'name' => 'Policy Document'],
    //             ['id' => null, 'name' => 'Certificate'],
    //             ['id' => null, 'name' => 'Claim Document'],
    //             ['id' => null, 'name' => 'Other Document']
    //         ],
    //         'agencies' => $getLookupValues('APL Agency'),
    //         'channels' => $getLookupValues('Channel')
    //     ];
    // }

    public function storeRenewalSchedule(Request $request, Policy $policy)
    {
        try {
            $validated = $request->validate([
                'year' => 'nullable|string|max:255',
                'policy_plan' => 'nullable|string|max:255',
                'sum_insured' => 'nullable|numeric|min:0',
                'term' => 'nullable|numeric|min:0',
                'term_unit' => 'nullable|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'add_ons' => 'nullable|string|max:255',
                'base_premium' => 'nullable|numeric|min:0',
                'full_premium' => 'nullable|numeric|min:0',
                'pay_plan_type' => 'nullable|string|max:255',
                'nop' => 'nullable|integer|min:1',
                'frequency' => 'nullable|string|max:255',
                'note' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Generate schedule number
            $latestSchedule = Schedule::orderBy('id', 'desc')->first();
            $nextScheduleId = $latestSchedule ? $latestSchedule->id + 1 : 1;
            $scheduleNo = 'SCH' . str_pad($nextScheduleId, 6, '0', STR_PAD_LEFT);

            // Build notes with renewal details
            $notes = [];
            if (!empty($validated['year'])) $notes[] = "Year: {$validated['year']}";
            if (!empty($validated['policy_plan'])) $notes[] = "Policy Plan: {$validated['policy_plan']}";
            if (!empty($validated['sum_insured'])) $notes[] = "Sum Insured: " . number_format($validated['sum_insured'], 2);
            if (!empty($validated['term'])) $notes[] = "Term: {$validated['term']} " . ($validated['term_unit'] ?? '');
            if (!empty($validated['add_ons'])) $notes[] = "Add Ons: {$validated['add_ons']}";
            if (!empty($validated['base_premium'])) $notes[] = "Base Premium: " . number_format($validated['base_premium'], 2);
            if (!empty($validated['full_premium'])) $notes[] = "Full Premium: " . number_format($validated['full_premium'], 2);
            if (!empty($validated['pay_plan_type'])) $notes[] = "Pay Plan Type: {$validated['pay_plan_type']}";
            if (!empty($validated['nop'])) $notes[] = "NOP: {$validated['nop']}";
            if (!empty($validated['frequency'])) $notes[] = "Frequency: {$validated['frequency']}";
            if (!empty($validated['note'])) $notes[] = "Note: {$validated['note']}";
            
            $scheduleNotes = "Renewal Schedule Details:\n" . implode("\n", $notes);

            // Create schedule
            $schedule = Schedule::create([
                'policy_id' => $policy->id,
                'schedule_no' => $scheduleNo,
                'issued_on' => now(),
                'effective_from' => $validated['start_date'],
                'effective_to' => $validated['end_date'] ?? $validated['start_date'],
                'status' => 'active',
                'notes' => $scheduleNotes
            ]);

            // Create payment plans if NOP and frequency are provided
            if (!empty($validated['nop']) && !empty($validated['frequency']) && !empty($validated['full_premium'])) {
                $noOfInstalments = (int)$validated['nop'];
                $frequencyName = strtolower($validated['frequency']);
                $totalPremium = (float)$validated['full_premium'];
                $amountPerInstalment = $totalPremium / $noOfInstalments;
                
                $startDate = Carbon::parse($validated['start_date']);
                $endDate = !empty($validated['end_date']) ? Carbon::parse($validated['end_date']) : null;
                $dueDate = $startDate->copy();

                for ($i = 1; $i <= $noOfInstalments; $i++) {
                    // Check if we've exceeded the end date
                    if ($endDate && $dueDate->gt($endDate)) {
                        break;
                    }
                    
                    PaymentPlan::create([
                        'schedule_id' => $schedule->id,
                        'installment_label' => $noOfInstalments == 1 ? 'Full Payment' : ('Instalment ' . $i . ' of ' . $noOfInstalments),
                        'due_date' => $dueDate->copy(),
                        'amount' => $amountPerInstalment,
                        'frequency' => $frequencyName,
                        'status' => 'pending',
                    ]);
                    
                    // Calculate next due date based on frequency
                    if ($i < $noOfInstalments) {
                        switch ($frequencyName) {
                            case 'monthly':
                                $dueDate->addMonth();
                                break;
                            case 'quarterly':
                                $dueDate->addMonths(3);
                                break;
                            case 'annually':
                            case 'yearly':
                                $dueDate->addYear();
                                break;
                            case 'single':
                            case 'one off':
                                break;
                            default:
                                $dueDate->addMonth();
                        }
                    }
                }
            }

            DB::commit();

            // Log activity
            \App\Models\AuditLog::log('create', $schedule, null, $schedule->getAttributes(), 'Renewal schedule created: ' . $schedule->schedule_no);

            return response()->json([
                'success' => true,
                'message' => 'Renewal schedule created successfully.',
                'schedule' => $schedule->load('paymentPlans')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating renewal schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating renewal schedule: ' . $e->getMessage()
            ], 500);
        }
    }
}