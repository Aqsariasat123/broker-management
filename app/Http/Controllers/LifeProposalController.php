<?php
// app/Http/Controllers/LifeProposalController.php

namespace App\Http\Controllers;

use App\Models\LifeProposal;
use App\Models\LookupCategory;
use App\Models\Contact;
use App\Models\Client;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Medical;
use App\Models\Followup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- Add this
use Carbon\Carbon;

class LifeProposalController extends Controller
{
  public function index(Request $request)
{
    $query = LifeProposal::with([
        'contact',
        'insurer',
        'policyPlan',
        'frequency',
        'agencies',
        'stage',
        'status',
        'sourceOfPayment',
        'medical',
        'followups',
        'proposalClass',
    ]);

    $actionType = $request->input('action', 'view');
    $contactId  = $request->input('contact_id');

    $hasFollowUpFilter = false;
             $year = $request->input('year', date('Y'));

        $month = $request->input('month', date('n'));
        
        $dateRange = $request->input('date_range', 'month');

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek(); // Monday
                $endDate = Carbon::now()->endOfWeek(); // Sunday
                break;
            case 'month':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                break;
            case 'quarter':
                $quarter = floor(($month - 1) / 3) + 1;
                $startDate = Carbon::create($year)->firstDayOfQuarter()->addMonths(3 * ($quarter - 1));
                $endDate = $startDate->copy()->addMonths(3)->subDay();
                break;
            case 'year':
                $startDate = Carbon::create($year)->startOfYear();
                $endDate = Carbon::create($year)->endOfYear();
                break;
            default:
                if (str_starts_with($dateRange, 'year-')) {
                    $yearOnly = (int) str_replace('year-', '', $dateRange);
                    $startDate = Carbon::create($yearOnly)->startOfYear();
                    $endDate = Carbon::create($yearOnly)->endOfYear();
                } else {
                    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                    $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                }
                break;
       }

     if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                // Example 1: Filter by client DOB within range
                $q->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()]);
            
            });
        }
    /* ===============================
     | FOLLOW-UP FILTER (TABLE)
     |===============================*/
    if ($request->boolean('follow_up')) {
        $hasFollowUpFilter = true;

        $query->whereHas('followups', function ($q) {
            $q->where('status', 'Open'); // optional
        });
    }

    /* ===============================
     | EXISTING FILTERS
     |===============================*/
    if ($request->filled('status')) {
        if ($request->status === 'pending') {
            $query->whereHas('status', fn ($q) => $q->where('name', 'Pending'));
        } elseif ($request->status === 'processing') {
            $query->whereHas('status', fn ($q) => $q->where('name', 'Processing'));
        }
    }

    if ($request->boolean('submitted')) {
        $query->where('is_submitted', true);
    }

    if ($contactId) {
        $query->where('contact_id', $contactId);
    }

    /* ===============================
     | PAGINATION
     |===============================*/
    $proposals = $query
        ->orderByDesc('created_at')
        ->paginate(10);

    /* ===============================
     | FORCE EMPTY RESULT
     |===============================*/
    if ($hasFollowUpFilter && $proposals->isEmpty()) {
        $proposals = LifeProposal::whereRaw('1 = 0')->paginate(10);
    }

    /* ===============================
     | FLAGS
     |===============================*/
    $proposals->getCollection()->transform(function ($proposal) {
        $proposal->hasExpired  = $proposal->hasExpired();
        $proposal->hasExpiring = $proposal->hasExpiring();
        return $proposal;
    });

    $lookupData = $this->getLookupData();

    // Return JSON if requested via AJAX
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'proposals' => $proposals->items(),
            'total' => $proposals->total(),
            'per_page' => $proposals->perPage(),
            'current_page' => $proposals->currentPage(),
        ]);
    }

    return view(
        'life-proposals.index',
        compact('proposals', 'lookupData', 'actionType', 'contactId')
    );
}

    /**
     * Get proposals by client ID (AJAX)
     */
    public function getByClient(Request $request, $clientId)
    {
        $query = LifeProposal::with([
            'contact',
            'insurer',
            'policyPlan',
            'frequency',
            'agencies',
            'stage',
            'status',
            'sourceOfPayment',
            'medical',
            'proposalClass',
        ]);

        // Get client to match proposals by proposer name
        $client = Client::find($clientId);
        if ($client) {
            $clientName = trim(($client->first_name ?? '') . ' ' . ($client->surname ?? ''));
            if ($clientName) {
                $query->where('proposers_name', 'LIKE', '%' . $clientName . '%');
            }
        }

        $proposals = $query->orderByDesc('created_at')->get();

        // Transform proposals for JSON response
        $data = $proposals->map(function ($proposal) {
            return [
                'id' => $proposal->id,
                'proposers_name' => $proposal->proposers_name,
                'insurer' => $proposal->insurer->name ?? '',
                'policy_plan' => $proposal->policyPlan->name ?? '',
                'sum_assured' => $proposal->sum_assured,
                'term' => $proposal->term,
                'add_ons' => $proposal->add_ons,
                'offer_date' => $proposal->offer_date ? $proposal->offer_date->format('d-M-y') : '',
                'premium' => $proposal->premium,
                'frequency' => $proposal->frequency->name ?? '',
                'stage' => $proposal->stage->name ?? '',
                'is_submitted' => $proposal->is_submitted,
                'age' => $proposal->age,
                'status' => $proposal->status->name ?? '',
                'source_of_payment' => $proposal->sourceOfPayment->name ?? '',
                'mcr' => $proposal->mcr,
                'doctor' => $proposal->medical->doctor ?? '',
                'date_sent' => $proposal->medical->date_sent ?? '',
                'date_completed' => $proposal->medical->date_completed ?? '',
                'notes' => $proposal->medical->notes ?? '',
                'agency' => $proposal->agency,
                'prid' => $proposal->prid,
                'class' => $proposal->proposalClass->name ?? '',
                'hasExpired' => $proposal->hasExpired(),
                'hasExpiring' => $proposal->hasExpiring(),
            ];
        });

        return response()->json([
            'proposals' => $data,
            'total' => $proposals->count(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'proposers_name' => 'required|string|max:255',
                'contact_id' => 'required|exists:contacts,id',
                'insurer_id' => 'required|exists:lookup_values,id',
                'policy_plan_id' => 'required|exists:lookup_values,id',
                 'salutation_id'=> 'required|exists:lookup_values,id',
                'sum_assured' => 'nullable|numeric',
                'term' => 'required|integer|min:1',
                'add_ons' => 'nullable|string|max:255',
                'offer_date' => 'required|date',
                'premium' => 'required|numeric',
                'frequency_id' => 'required|exists:lookup_values,id',
                'proposal_stage_id' => 'required|exists:lookup_values,id',
                'age' => 'required|integer|min:1|max:120',
                'status_id' => 'required|exists:lookup_values,id',
                'source_of_payment_id' => 'required|exists:lookup_values,id',
                'mcr' => 'nullable|string|max:255',
                'policy_no' => 'nullable|string|max:255',
                'loading_premium' => 'nullable|numeric',
                'start_date' => 'nullable|date',
                'maturity_date' => 'nullable|date',
                'method_of_payment' => 'nullable|string|max:255',
                'agency' => 'nullable|string|max:255',
                'medical_type_id' => 'nullable|exists:lookup_values,id',
                'medical.status_id' => 'nullable|exists:lookup_values,id',

                'is_submitted' => 'sometimes|boolean',
                'sex' => 'nullable|string|max:1',
                'anb' => 'nullable|integer',
                'riders' => 'nullable|array',
                'rider_premiums' => 'nullable|array',
                'annual_premium' => 'nullable|numeric',
                'base_premium' => 'nullable|numeric',
                'admin_fee' => 'nullable|numeric',
                'total_premium' => 'nullable|numeric',
                'medical_examination_required' => 'sometimes|boolean',
                'source_name' => 'nullable|string|max:255',
                'clinic' => 'nullable|string|max:255',
                'exam_notes' => 'nullable|string',
            ]);

            // Checkbox flags
            $validated['is_submitted'] = $request->has('is_submitted') ? (bool)$request->is_submitted : false;
            $validated['medical_examination_required'] = $request->has('medical_examination_required') ? (bool)$request->medical_examination_required : false;

            DB::transaction(function () use ($validated, $request) {
                // Generate PRID
                $latestProposal = LifeProposal::orderBy('id', 'desc')->first();
                $nextId = $latestProposal ? (int) str_replace('PR', '', $latestProposal->prid) + 1 : 1001;
                $validated['prid'] = 'PR' . $nextId;

                Log::info('Selected Columns for lookupData:', $validated);
                $lifeProposal = LifeProposal::create($validated);

                Followup::create([
                    'follow_up_code'   => 'FU-' . now()->format('Ymd') . '-' . $lifeProposal->id,
                    'life_proposal_id' => $lifeProposal->id,
                    'contact_id'       => $request->contact_id ?? null,
                    'client_id'        => $request->client_id ?? null,
                    'user_id'          => Auth::id(),
                    'follow_up_date'   => $request->date,
                    'channel'          => 'System',
                    'status'           => 'Open',
                    'summary'          => 'Life proposal created',
                    'next_action'      => 'Review proposal',
                ]);

                if ($validated['medical_examination_required']) {
                    Medical::create([
                        'life_proposal_id' => $lifeProposal->id,
                        'medical_code'     => 'MED-' . now()->format('Ymd') . '-' . $lifeProposal->id,
                        'medical_type_id'     => $request->medical_type_id,
                        'clinic'         => $request->clinic,
                        'ordered_on'       => $request->date_referred,
                        'completed_on'       => $request->date_completed,
                        'status_id'           => $request->input('medical.status_id'),
                        'notes'            => $request->exam_notes,
                    ]);
                }
            });

            return redirect()->route('life-proposals.index')->with('success', 'Life Proposal created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors separately if needed
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Error creating Life Proposal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Something went wrong while creating the Life Proposal.')->withInput();
        }
    }


    public function show(Request $request, LifeProposal $lifeProposal)
    {
        if ($request->expectsJson()) {
             $lifeProposal->load([
            'contact',
            'insurer',
            'policyPlan',
            'frequency',
            'stage',
            'status',
            'sourceOfPayment',
            'medical',
             'medical.clinic',
            'followups',
        ]);

        return response()->json($lifeProposal);
        }
        return view('life-proposals.show', compact('lifeProposal'));
    }

    
    public function update(Request $request, LifeProposal $lifeProposal)
    {
        $validated = $request->validate([
               'proposers_name' => 'required|string|max:255',
                'contact_id' => 'required|exists:contacts,id',
                'client_id' => 'nullable|exists:clients,id',

                'insurer_id' => 'required|exists:lookup_values,id',
                'policy_plan_id' => 'required|exists:lookup_values,id',
                'salutation_id'=> 'required|exists:lookup_values,id',

                'sum_assured' => 'nullable|numeric',
                'term' => 'required|integer|min:1',
                'add_ons' => 'nullable|string|max:255',

                'offer_date' => 'required|date',
                'premium' => 'required|numeric',
                'frequency_id' => 'required|exists:lookup_values,id',
                'proposal_stage_id' => 'required|exists:lookup_values,id',

                'age' => 'required|integer|min:1|max:120',
                'status_id' => 'required|exists:lookup_values,id',
                'source_of_payment_id' => 'required|exists:lookup_values,id',

                'mcr' => 'nullable|string|max:255',
                'policy_no' => 'nullable|string|max:255',
                'loading_premium' => 'nullable|numeric',
                'start_date' => 'nullable|date',
                'maturity_date' => 'nullable|date',
                'method_of_payment' => 'nullable|string|max:255',
                'agency' => 'nullable|string|max:255',

                'medical_type_id' => 'nullable|exists:lookup_values,id',

                'is_submitted' => 'sometimes|boolean',
                'sex' => 'nullable|string|max:1',
                'anb' => 'nullable|integer',

                'riders' => 'nullable|array',
                'rider_premiums' => 'nullable|array',

                'annual_premium' => 'nullable|numeric',
                'base_premium' => 'nullable|numeric',
                'admin_fee' => 'nullable|numeric',
                'total_premium' => 'nullable|numeric',

                'medical_examination_required' => 'sometimes|boolean',
                'source_name' => 'nullable|string|max:255',

                'clinic' => 'nullable|string|max:255',
                'exam_notes' => 'nullable|string',
        ]);

        // Ensure boolean flags
        $validated['is_submitted'] = $request->boolean('is_submitted');
        $validated['medical_examination_required'] = $request->boolean('medical_examination_required');

        DB::transaction(function () use ($validated, $request, $lifeProposal) {

            // -------------------------
            // Update Life Proposal
            // -------------------------
            $lifeProposal->update($validated);

            // -------------------------
            // Handle Medical Record
            // -------------------------
            if ($validated['medical_examination_required']) {
                Medical::updateOrCreate(
                    ['life_proposal_id' => $lifeProposal->id],
                    [
                        'medical_code' => $lifeProposal->medical->medical_code
                            ?? 'MED-' . now()->format('Ymd') . '-' . $lifeProposal->id,
                        'medical_type_id' => $request->medical_type_id,
                        'clinic' => $request->clinic,
                        'ordered_on'       => $request->date_referred,
                        'completed_on'       => $request->date_completed,
                        'status_id'           => $request->input('medical.status_id'),
                        'notes' => $request->exam_notes,

                    ]
                );
            } else {
                // If medical no longer required, remove medical record
                $lifeProposal->medical()?->delete();
            }

            // -------------------------
            // Add Followup Entry
            // -------------------------
        
            $followUp = $lifeProposal->followups()->first(); // or ->find($id) if you have ID
            if ($followUp) {
                $followUp->update([
                    'follow_up_code' => $followUp->follow_up_code, // keep existing code
                    'contact_id'     => $request->contact_id ?? null,
                    'client_id'      => $request->client_id ?? null,
                    'user_id'        => Auth::id(),
                    'follow_up_date' => $request->date,
                    'channel'        => 'System',
                    'status'         => 'Open',
                    'summary'        => 'Life proposal updated',
                    'next_action'    => 'Review proposal',
                ]);
            }

        });

        return redirect()
            ->route('life-proposals.index')
            ->with('success', 'Life Proposal updated successfully.');
    }


    public function destroy(LifeProposal $lifeProposal)
    {
        $lifeProposal->delete();

        return redirect()->route('life-proposals.index')->with('success', 'Life Proposal deleted successfully.');
    }

    public function edit(LifeProposal $lifeProposal)
    {
        $lifeProposal->load([
            'contact',
            'insurer',
            'policyPlan',
            'frequency',
            'stage',
            'status',
            'sourceOfPayment',
            'medical',
            'followups',
        ]);

        return response()->json($lifeProposal);
    }


    public function export()
    {
        $proposals = LifeProposal::with([
            'insurer',
            'policyPlan',
            'frequency',
            'stage',
            'status',
            'sourceOfPayment',
            'class' // if you have a class relationship in LifeProposal
        ])->get();

        $fileName = 'life_proposals_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->streamDownload(function () use ($proposals) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, [
                'Action', 'Proposer\'s Name', 'Insurer', 'Policy Plan', 'Sum Assured', 'Term', 'Add Ons',
                'Offer Date', 'Premium', 'Frequency', 'Stage', 'Date', 'Age', 'Status', 'Source Of Payment',
                'MCR', 'Doctor', 'Date Sent', 'Date Completed', 'Notes', 'Agency', 'PRID', 'Class'
            ]);

            foreach ($proposals as $proposal) {
                fputcsv($handle, [
                    '⤢',
                    $proposal->proposers_name,
                    $proposal->insurer->name ?? '-',        // lookup relation
                    $proposal->policyPlan->name ?? '-',     // lookup relation
                    $proposal->sum_assured ? number_format($proposal->sum_assured, 2) : '##########',
                    $proposal->term ?? '-',
                    $proposal->add_ons ?: '-',
                    $proposal->offer_date ? $proposal->offer_date->format('d-M-y') : '##########',
                    $proposal->premium ? number_format($proposal->premium, 2) : '##########',
                    $proposal->frequency->name ?? '-',      // lookup relation
                    $proposal->stage->name ?? '-',          // lookup relation
                    $proposal->date ? $proposal->date->format('d-M-y') : '##########',
                    $proposal->age ?? '-',
                    $proposal->status->name ?? '-',         // lookup relation
                    $proposal->sourceOfPayment->name ?? '-',// lookup relation
                    $proposal->mcr ?: '-',
                    $proposal->doctor ?? '-',
                    $proposal->date_sent ? $proposal->date_sent->format('d-M-y') : '##########',
                    $proposal->date_completed ? $proposal->date_completed->format('d-M-y') : '##########',
                    $proposal->notes ?: '-',
                    $proposal->agency ?: '-',
                    $proposal->prid,
                    $proposal->class->name ?? '-',          // if class relationship exists
                ]);
            }

            fclose($handle);
        }, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['life_proposal_columns' => $request->columns ?? []]);
        
        return redirect()->route('life-proposals.index')
            ->with('success', 'Column settings saved successfully.');
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'document_type' => 'required|in:proposal_document,medical_report,id_document,other',
            'prid' => 'required|string',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentType = $request->document_type;
            $prid = $request->prid;
            
            // Map document types to names
            $documentNames = [
                'proposal_document' => 'Proposal Document',
                'medical_report' => 'Medical Report',
                'id_document' => 'ID Document',
                'other' => 'Other Document'
            ];
            
            $filename = 'life_proposal_' . $prid . '_' . $documentType . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = Document::orderBy('id', 'desc')->first();
            $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id ?? 'DOC0') + 1 : 1001;
            $docId = 'DOC' . $nextId;

            // Store in documents table
            Document::create([
                'doc_id' => $docId,
                'tied_to' => $prid,
                'name' => $documentNames[$documentType] ?? 'Document',
                'group' => 'Life Proposal Document',
                'type' => $documentType,
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.'
        ]);
    }

    public function generatePolicy(LifeProposal $lifeProposal)
    {
        // Redirect to policies index page with life proposal ID to pre-fill the form
        return redirect()->route('policies.index', ['life_proposal_id' => $lifeProposal->id]);
    }
private function getLookupData()
{
    $getValues = function (string $categoryName, array $default = [], bool $useSeq = true) {
        $category = LookupCategory::where('name', $categoryName)->first();

        if (!$category) {
            return $default;
        }

        $query = $category->values()->where('active', true);

        if ($useSeq) {
            $query->orderBy('seq');
        }

        return $query->get(['id', 'name'])->toArray();
    };

    return [

        /* ================= CONTACT LOOKUPS ================= */
        'contact_types' => $getValues('Contact Type'),
        'sources' => $getValues('Source'),
        'agents' => $getValues('Agent'),
        'agencies' => $getValues('APL Agency'),
        'salutations' => $getValues('Salutation'),
        'contact_statuses' => $getValues('Contact Status', [
            ['id' => 1, 'name' => 'Not Contacted'],
            ['id' => 2, 'name' => 'In Discussion'],
            ['id' => 3, 'name' => 'Proposal Made'],
            ['id' => 4, 'name' => 'Keep In View'],
            ['id' => 5, 'name' => 'Archived'],
            ['id' => 6, 'name' => 'RNR'],
            ['id' => 7, 'name' => 'Differed'],
        ]),
        'ranks' => $getValues('Rank', [
            ['id' => 1, 'name' => 'VIP'],
            ['id' => 2, 'name' => 'High'],
            ['id' => 3, 'name' => 'Medium'],
            ['id' => 4, 'name' => 'Low'],
            ['id' => 5, 'name' => 'Warm'],
        ]),
        'districts' => $getValues('District'),
        'occupations' => $getValues('Occupation'),
        'islands' => $getValues('Island'),
        'countries' => $getValues('Issuing Country'),
        'income_sources' => $getValues('Income Source'),
        'statuses' => $getValues('Proposal Status'),

        /* ================= POLICY LOOKUPS ================= */
        'clients' => Client::orderBy('client_name')->get(['id', 'client_name', 'clid'])->toArray(),
        'insurers' => $getValues('Insurers', [], false),
        'policy_classes' => $getValues('Class', [], false),
        'policy_plans' => $getValues('Policy Plans', [], false),
        'policy_statuses' => $getValues('Policy Status', [
            ['id' => null, 'name' => 'In Force'],
            ['id' => null, 'name' => 'DFR'],
            ['id' => null, 'name' => 'Expired'],
            ['id' => null, 'name' => 'Cancelled'],
        ], false),
        'business_types' => $getValues('Business Type', [
            ['id' => null, 'name' => 'Direct'],
            ['id' => null, 'name' => 'Transfer'],
        ], false),
        'medical_statuses' => $getValues('Medical Status', [
            ['id' => 1, 'name' => 'Pending'],
            ['id' => 2, 'name' => 'Scheduled'],
            ['id' => 3, 'name' => 'Completed'],
            ['id' => 4, 'name' => 'Reports Received'],
            ['id' => 5, 'name' => 'Approved'],
            ['id' => 6, 'name' => 'Deferred'],
            ['id' => 7, 'name' => 'Cancelled'],
        ], false),

        'term_units' => $getValues('Term Units', [
            ['id' => null, 'name' => 'Year'],
            ['id' => null, 'name' => 'Month'],
            ['id' => null, 'name' => 'Days'],
        ], false),
        'frequencies' => $getValues('Frequency', [
            ['id' => null, 'name' => 'Annually'],
            ['id' => null, 'name' => 'Monthly'],
            ['id' => null, 'name' => 'Quarterly'],
            ['id' => null, 'name' => 'One Off'],
            ['id' => null, 'name' => 'Single'],
        ], false),
        'pay_plans' => $getValues('Payment Plan', [
            ['id' => null, 'name' => 'Full'],
            ['id' => null, 'name' => 'Instalments'],
            ['id' => null, 'name' => 'Regular'],
        ], false),
        'document_types' => $getValues('Document Type', [
            ['id' => null, 'name' => 'Policy Document'],
            ['id' => null, 'name' => 'Certificate'],
            ['id' => null, 'name' => 'Claim Document'],
            ['id' => null, 'name' => 'Other Document'],
        ], false),
           'stages' => $getValues('Proposal Stage', [
            ['id' => 1, 'name' => 'Not Contacted'],
            ['id' => 2, 'name' => 'RNR'],
            ['id' => 3, 'name' => 'In Discussion'],
            ['id' => 4, 'name' => 'Offer Made'],
            ['id' => 5, 'name' => 'Proposal Filled'],
        ]),

        'channels' => $getValues('Channel', [], false),
        'sources_of_payment' => $getValues('Source Of Payment'),
        'agencies' => $getValues('APL Agency'),
        'classes' => $getValues('Class'),
        'sources' => $getValues('Source'),

        /* ================= STATIC OPTIONS FROM DB ================= */
        'riders' => $getValues('Riders', [
            ['id' => 1, 'name' => 'ADB'],
            ['id' => 2, 'name' => 'AcDB'],
            ['id' => 3, 'name' => 'TPD'],
            ['id' => 4, 'name' => 'TPDWoP'],
            ['id' => 5, 'name' => 'FIBT'],
            ['id' => 6, 'name' => 'CIB'] ] ),
        'add_ons' => $getValues('Add Ons', []),
        'doctors' => $getValues('Doctors', []),
        'clinics' => $getValues('Clinics', [
             ['id' => 1, 'name' => 'Jivan'],
            ['id' => 2, 'name' => 'Wellkin'],
            ['id' => 3, 'name' => 'Apollo Bramwell'],
            ['id' => 4, 'name' => 'City Clinic'],
            ['id' => 5, 'name' => 'MedPoint']]
    ),
         'sex_options' => $getValues('Sex', [
            ['id' => 1, 'name' => 'M'],
            ['id' => 2, 'name' => 'F'],
        ]),
        'method_of_payment_options' => $getValues('Method Of Payment', [  
            ['id' => 1, 'name' => 'Salary Deduction'],
            ['id' => 2, 'name' => 'Bank Transfer'],
            ['id' => 3, 'name' => 'Cash'],
            ['id' => 4, 'name' => 'Cheque'],
            ['id' => 5, 'name' => 'Credit Card'],
           ['id' => 6, 'name' => 'Debit Card'],]),
         'medical_types' => $getValues('Medical Type', [
            ['id' => 1, 'name' => 'Initial Medical'],
            ['id' => 2, 'name' => 'Full Medical'],
            ['id' => 3, 'name' => 'Non-Medical'],
            ['id' => 4, 'name' => 'Tele Medical'],
            ['id' => 5, 'name' => 'Specialist Medical'],
            ['id' => 6, 'name' => 'Medical Recheck'],
            ]),
        /* ================= CONTACTS & CLIENTS ================= */
        'contacts' => Contact::select('id', 'contact_name', 'contact_id', 'salutation', 'dob')
            ->orderBy('contact_name')
            ->get(),
        'clients' => Client::select('id', 'client_name', 'clid', 'salutation', 'dob_dor as dob')
            ->orderBy('client_name')
            ->get(),
          
    ];
}



}