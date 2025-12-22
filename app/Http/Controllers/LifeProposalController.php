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

class LifeProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = LifeProposal::query();
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status == 'pending') {
                $query->where('status', 'Pending');
            } elseif ($request->status == 'processing') {
                $query->where('status', 'Processing');
            }
        }
        
        // Filter for "To Follow Up" - proposals with offer_date in the past or within next 7 days, and not submitted
        $followUp = $request->input('follow_up');
        if ($followUp && ($followUp == 'true' || $followUp == '1')) {
            $query->whereNotNull('offer_date')
                  ->where('offer_date', '<=', now()->addDays(7))
                  ->where('is_submitted', false);
        }
        
        // Filter for Submitted proposals
        $submitted = $request->input('submitted');
        if ($submitted && ($submitted == 'true' || $submitted == '1')) {
            $query->where('is_submitted', true);
        }
        
        $proposals = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate expiration status for each proposal
        $proposals->getCollection()->transform(function ($proposal) {
            $proposal->hasExpired = $proposal->hasExpired();
            $proposal->hasExpiring = $proposal->hasExpiring();
            return $proposal;
        });
        
        // Get lookup data for dropdowns
        $lookupData = $this->getLookupData();
        
        return view('life-proposals.index', compact('proposals', 'lookupData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proposers_name' => 'required|string|max:255',
            'salutation' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'sex' => 'nullable|string|max:1',
            'anb' => 'nullable|integer',
            'insurer' => 'required|string|max:255',
            'policy_plan' => 'required|string|max:255',
            'sum_assured' => 'nullable|numeric',
            'term' => 'required|integer|min:1',
            'add_ons' => 'nullable|string|max:255',
            'riders' => 'nullable|array',
            'rider_premiums' => 'nullable|array',
            'annual_premium' => 'nullable|numeric',
            'base_premium' => 'nullable|numeric',
            'admin_fee' => 'nullable|numeric',
            'total_premium' => 'nullable|numeric',
            'offer_date' => 'required|date',
            'premium' => 'required|numeric',
            'frequency' => 'required|string|max:50',
            'method_of_payment' => 'nullable|string|max:255',
            'stage' => 'required|string|max:255',
            'date' => 'required|date',
            'age' => 'required|integer|min:1|max:120',
            'status' => 'required|string|max:50',
            'source_of_payment' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'source_name' => 'nullable|string|max:255',
            'mcr' => 'nullable|string|max:255',
            'doctor' => 'nullable|string|max:255',
            'date_sent' => 'nullable|date',
            'date_completed' => 'nullable|date',
            'medical_examination_required' => 'sometimes|boolean',
            'clinic' => 'nullable|string|max:255',
            'date_referred' => 'nullable|date',
            'exam_notes' => 'nullable|string',
            'policy_no' => 'nullable|string|max:255',
            'loading_premium' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'maturity_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'agency' => 'nullable|string|max:255',
            'is_submitted' => 'sometimes|boolean',
        ]);

        // Handle checkbox fields
        $validated['is_submitted'] = $request->has('is_submitted') ? (bool)$request->is_submitted : false;
        $validated['medical_examination_required'] = $request->has('medical_examination_required') ? (bool)$request->medical_examination_required : false;

        // Handle riders arrays
        if ($request->has('riders')) {
            $validated['riders'] = $request->riders;
        }
        if ($request->has('rider_premiums')) {
            $validated['rider_premiums'] = $request->rider_premiums;
        }

        // Generate unique PRID
        $latestProposal = LifeProposal::orderBy('id', 'desc')->first();
        $nextId = $latestProposal ? (int)str_replace('PR', '', $latestProposal->prid) + 1 : 1001;
        $validated['prid'] = 'PR' . $nextId;

        LifeProposal::create($validated);

        return redirect()->route('life-proposals.index')->with('success', 'Life Proposal created successfully.');
    }

    public function show(Request $request, LifeProposal $lifeProposal)
    {
        if ($request->expectsJson()) {
            return response()->json($lifeProposal);
        }
        return view('life-proposals.show', compact('lifeProposal'));
    }

    public function update(Request $request, LifeProposal $lifeProposal)
    {
        $validated = $request->validate([
            'proposers_name' => 'required|string|max:255',
            'salutation' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'sex' => 'nullable|string|max:1',
            'anb' => 'nullable|integer',
            'insurer' => 'required|string|max:255',
            'policy_plan' => 'required|string|max:255',
            'sum_assured' => 'nullable|numeric',
            'term' => 'required|integer|min:1',
            'add_ons' => 'nullable|string|max:255',
            'riders' => 'nullable|array',
            'rider_premiums' => 'nullable|array',
            'annual_premium' => 'nullable|numeric',
            'base_premium' => 'nullable|numeric',
            'admin_fee' => 'nullable|numeric',
            'total_premium' => 'nullable|numeric',
            'offer_date' => 'required|date',
            'premium' => 'required|numeric',
            'frequency' => 'required|string|max:50',
            'method_of_payment' => 'nullable|string|max:255',
            'stage' => 'required|string|max:255',
            'date' => 'required|date',
            'age' => 'required|integer|min:1|max:120',
            'status' => 'required|string|max:50',
            'source_of_payment' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'source_name' => 'nullable|string|max:255',
            'mcr' => 'nullable|string|max:255',
            'doctor' => 'nullable|string|max:255',
            'date_sent' => 'nullable|date',
            'date_completed' => 'nullable|date',
            'medical_examination_required' => 'sometimes|boolean',
            'clinic' => 'nullable|string|max:255',
            'date_referred' => 'nullable|date',
            'exam_notes' => 'nullable|string',
            'policy_no' => 'nullable|string|max:255',
            'loading_premium' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'maturity_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'agency' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:255',
            'is_submitted' => 'sometimes|boolean',
        ]);

        // Handle checkbox fields
        $validated['is_submitted'] = $request->has('is_submitted') ? (bool)$request->is_submitted : false;
        $validated['medical_examination_required'] = $request->has('medical_examination_required') ? (bool)$request->medical_examination_required : false;

        // Handle riders arrays
        if ($request->has('riders')) {
            $validated['riders'] = $request->riders;
        }
        if ($request->has('rider_premiums')) {
            $validated['rider_premiums'] = $request->rider_premiums;
        }

        $lifeProposal->update($validated);

        return redirect()->route('life-proposals.index')->with('success', 'Life Proposal updated successfully.');
    }

    public function destroy(LifeProposal $lifeProposal)
    {
        $lifeProposal->delete();

        return redirect()->route('life-proposals.index')->with('success', 'Life Proposal deleted successfully.');
    }

    public function edit(LifeProposal $lifeProposal)
{
    return response()->json($lifeProposal);
}

    public function export()
    {
        $proposals = LifeProposal::all();
        
        $fileName = 'life_proposals_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Action', 'Proposer\'s Name', 'Insurer', 'Policy Plan', 'Sum Assured', 'Term', 'Add Ons',
            'Offer Date', 'Premium', 'Freq', 'Stage', 'Date', 'Age', 'Status', 'Source Of Payment',
            'MCR', 'Doctor', 'Date Sent', 'Date Completed', 'Notes', 'Agency', 'PRID', 'Class'
        ]);

        foreach ($proposals as $proposal) {
            fputcsv($handle, [
                '⤢',
                $proposal->proposers_name,
                $proposal->insurer,
                $proposal->policy_plan,
                $proposal->sum_assured ? number_format($proposal->sum_assured, 2) : '##########',
                $proposal->term,
                $proposal->add_ons ?: '-',
                $proposal->offer_date ? $proposal->offer_date->format('d-M-y') : '##########',
                number_format($proposal->premium, 2),
                $proposal->frequency,
                $proposal->stage,
                $proposal->date ? $proposal->date->format('d-M-y') : '##########',
                $proposal->age,
                $proposal->status,
                $proposal->source_of_payment,
                $proposal->mcr ?: '-',
                $proposal->doctor ?: '-',
                $proposal->date_sent ? $proposal->date_sent->format('d-M-y') : '##########',
                $proposal->date_completed ? $proposal->date_completed->format('d-M-y') : '##########',
                $proposal->notes ?: '-',
                $proposal->agency ?: '-',
                $proposal->prid,
                $proposal->class
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {
            //
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
        $insurersCategory = LookupCategory::where('name', 'Insurers')->first();
        $policyPlansCategory = LookupCategory::where('name', 'Policy Plans')->first();
        $frequencyCategory = LookupCategory::where('name', 'Frequency')->first();
        $stagesCategory = LookupCategory::where('name', 'Proposal Stage')->first();
        $statusesCategory = LookupCategory::where('name', 'Proposal Status')->first();
        $sourcesOfPaymentCategory = LookupCategory::where('name', 'Source Of Payment')->first();
        $agenciesCategory = LookupCategory::where('name', 'APL Agency')->first();
        $classesCategory = LookupCategory::where('name', 'Class')->first();
        $sourceCategory = LookupCategory::where('name', 'Source')->first();
        
        return [
            'insurers' => $insurersCategory ? $insurersCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'policy_plans' => $policyPlansCategory ? $policyPlansCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'frequencies' => $frequencyCategory ? $frequencyCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'stages' => $stagesCategory ? $stagesCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'statuses' => $statusesCategory ? $statusesCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'sources_of_payment' => $sourcesOfPaymentCategory ? $sourcesOfPaymentCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'agencies' => $agenciesCategory ? $agenciesCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'classes' => $classesCategory ? $classesCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'sources' => $sourceCategory ? $sourceCategory->values()->where('active', true)->pluck('name')->toArray() : [],
            'salutations' => ['Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Prof'],
            'sex_options' => ['M', 'F'],
            'riders' => ['ADB', 'AcDB', 'TPD', 'TPDWoP', 'FIBT', 'FIBD', 'CIB'],
            'add_ons' => ['Critical Illness', 'Accidental Death', 'Waiver of Premium', 'Hospital Cash', 'Total Permanent Disability'],
            'doctors' => ['Dr. Smith', 'Dr. Johnson', 'Dr. Williams', 'Dr. Brown', 'Dr. Jones'],
            'clinics' => ['Jivan', 'Wellkin', 'Apollo Bramwell', 'City Clinic', 'MedPoint'],
            'contacts' => Contact::select('id', 'contact_name', 'contact_id', 'salutation', 'dob')->orderBy('contact_name')->get(),
            'clients' => Client::select('id', 'client_name', 'clid', 'salutation', 'dob_dor as dob')->orderBy('client_name')->get(),
            'method_of_payment_options' => ['Salary Deduction', 'Bank Transfer', 'Cash', 'Cheque', 'Credit Card', 'Debit Card']
        ];
    }
}