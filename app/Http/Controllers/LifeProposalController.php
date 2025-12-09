<?php
// app/Http/Controllers/LifeProposalController.php

namespace App\Http\Controllers;

use App\Models\LifeProposal;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LifeProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = LifeProposal::query();
        
        // Filter for Submitted proposals
        if ($request->has('submitted') && $request->submitted == 'true') {
            $query->where('is_submitted', true);
        }
        
        $proposals = $query->orderBy('created_at', 'desc')->paginate(10); // <-- paginate here
        
        // Get lookup data for dropdowns
        $lookupData = $this->getLookupData();
        
        return view('life-proposals.index', compact('proposals', 'lookupData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proposers_name' => 'required|string|max:255',
            'insurer' => 'required|string|max:255',
            'policy_plan' => 'required|string|max:255',
            'sum_assured' => 'nullable|numeric',
            'term' => 'required|integer|min:1',
            'add_ons' => 'nullable|string|max:255',
            'offer_date' => 'required|date',
            'premium' => 'required|numeric',
            'frequency' => 'required|string|max:50',
            'stage' => 'required|string|max:255',
            'date' => 'required|date',
            'age' => 'required|integer|min:1|max:120',
            'status' => 'required|string|max:50',
            'source_of_payment' => 'required|string|max:255',
            'mcr' => 'nullable|string|max:255',
            'doctor' => 'nullable|string|max:255',
            'date_sent' => 'nullable|date',
            'date_completed' => 'nullable|date',
            'notes' => 'nullable|string',
            'agency' => 'nullable|string|max:255',
            'class' => 'required|string|max:255',
            'is_submitted' => 'sometimes|boolean',
        ]);

        // Handle checkbox field
        $validated['is_submitted'] = $request->has('is_submitted') ? (bool)$request->is_submitted : false;

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
            'insurer' => 'required|string|max:255',
            'policy_plan' => 'required|string|max:255',
            'sum_assured' => 'nullable|numeric',
            'term' => 'required|integer|min:1',
            'add_ons' => 'nullable|string|max:255',
            'offer_date' => 'required|date',
            'premium' => 'required|numeric',
            'frequency' => 'required|string|max:50',
            'stage' => 'required|string|max:255',
            'date' => 'required|date',
            'age' => 'required|integer|min:1|max:120',
            'status' => 'required|string|max:50',
            'source_of_payment' => 'required|string|max:255',
            'mcr' => 'nullable|string|max:255',
            'doctor' => 'nullable|string|max:255',
            'date_sent' => 'nullable|date',
            'date_completed' => 'nullable|date',
            'notes' => 'nullable|string',
            'agency' => 'nullable|string|max:255',
            'class' => 'required|string|max:255',
            'is_submitted' => 'sometimes|boolean',
        ]);

        // Handle checkbox field
        $validated['is_submitted'] = $request->has('is_submitted') ? (bool)$request->is_submitted : false;

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

    private function getLookupData()
    {
        return [
            'insurers' => LookupCategory::where('name', 'Insurers')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'policy_plans' => LookupCategory::where('name', 'Policy Plans')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'frequencies' => LookupCategory::where('name', 'Frequency')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'stages' => LookupCategory::where('name', 'Proposal Stage')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'statuses' => LookupCategory::where('name', 'Proposal Status')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'sources_of_payment' => LookupCategory::where('name', 'Source Of Payment')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'agencies' => LookupCategory::where('name', 'APL Agency')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'classes' => LookupCategory::where('name', 'Class')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'add_ons' => ['Critical Illness', 'Accidental Death', 'Waiver of Premium', 'Hospital Cash', 'Total Permanent Disability'],
            'doctors' => ['Dr. Smith', 'Dr. Johnson', 'Dr. Williams', 'Dr. Brown', 'Dr. Jones']
        ];
    }
}