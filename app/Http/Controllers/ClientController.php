<?php
// app/Http/Controllers/ClientController.php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();
        
        // Filter for To Follow Up
        if ($request->has('follow_up') && $request->follow_up == 'true') {
            // Add your follow-up logic here
            $query->where('status', 'Active'); // Example filter
        }
        
        // Use paginate instead of get
        $clients = $query->with('policies')->orderBy('created_at', 'desc')->paginate(10);

        // Calculate expiration status for each client
        $clients->getCollection()->transform(function ($client) {
            $expiredPolicies = [];
            $expiringPolicies = [];
            
            foreach ($client->policies as $policy) {
                if ($policy->isExpired()) {
                    $expiredPolicies[] = $policy;
                } elseif ($policy->isDueForRenewal()) {
                    $expiringPolicies[] = $policy;
                }
            }
            
            $client->hasExpired = count($expiredPolicies) > 0;
            $client->hasExpiring = count($expiringPolicies) > 0;
            $client->expiredPolicies = $expiredPolicies;
            $client->expiringPolicies = $expiringPolicies;
            
            return $client;
        });

        // Get lookup data for dropdowns
        $lookupData = $this->getLookupData();
        
        return view('clients.index', compact('clients', 'lookupData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'client_name' => 'required|string|max:255', // REMOVE THIS LINE
            'client_type' => 'required|string|max:255',
            'nin_bcrn' => 'nullable|string|max:50',
            'dob_dor' => 'nullable|date',
            'mobile_no' => 'required|string|max:20',
            'wa' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'signed_up' => 'required|date',
            'employer' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'income_source' => 'nullable|string|max:255',
            'married' => 'boolean',
            'spouses_name' => 'nullable|string|max:255',
            'alternate_no' => 'nullable|string|max:20',
            'email_address' => 'nullable|email',
            'location' => 'nullable|string',
            'island' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'po_box_no' => 'nullable|string|max:50',
            'pep' => 'boolean',
            'pep_comment' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'salutation' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'passport_no' => 'nullable|string|max:50',
        ]);

        // Generate unique CLID
        $latestClient = Client::orderBy('id', 'desc')->first();
        $nextId = $latestClient ? (int)str_replace('CL', '', $latestClient->clid) + 1 : 1001;
        $validated['clid'] = 'CL' . $nextId;

        // Combine names for client_name
        $validated['client_name'] = trim($validated['first_name'] . ' ' . ($validated['other_names'] ?? '') . ' ' . $validated['surname']);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function create()
    {
        $lookupData = $this->getLookupData();
        return view('clients.create', compact('lookupData'));
    }

    public function show(Client $client)
    {
        // If request expects JSON (AJAX), return JSON
        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json($client);
        }
        
        $client->load(['policies' => function($query) {
            $query->with(['insurer', 'policyClass', 'policyPlan', 'policyStatus'])
                  ->orderBy('date_registered', 'desc');
        }]);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        // If request expects JSON (AJAX), return JSON for modal
        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json($client);
        }
        
        $lookupData = $this->getLookupData();
        return view('clients.edit', compact('client', 'lookupData'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            // 'client_name' => 'required|string|max:255', // REMOVE THIS LINE
            'client_type' => 'required|string|max:255',
            'nin_bcrn' => 'nullable|string|max:50',
            'dob_dor' => 'nullable|date',
            'mobile_no' => 'required|string|max:20',
            'wa' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'signed_up' => 'required|date',
            'employer' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'income_source' => 'nullable|string|max:255',
            'married' => 'boolean',
            'spouses_name' => 'nullable|string|max:255',
            'alternate_no' => 'nullable|string|max:20',
            'email_address' => 'nullable|email',
            'location' => 'nullable|string',
            'island' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'po_box_no' => 'nullable|string|max:50',
            'pep' => 'boolean',
            'pep_comment' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'salutation' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'passport_no' => 'nullable|string|max:50',
        ]);

        // Combine names for client_name
        $validated['client_name'] = trim($validated['first_name'] . ' ' . ($validated['other_names'] ?? '') . ' ' . $validated['surname']);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    public function export()
    {
        $clients = Client::all();
        
        $fileName = 'clients_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Action', 'Client Name', 'Client Type', 'NIN/BCRN', 'DOB/DOR', 'MobileNo', 'WA',
            'District', 'Occupation', 'Source', 'Status', 'Signed Up', 'Employer', 'CLID',
            'Contact Person', 'Income Source', 'Married', 'Spouses Name', 'Alternate No',
            'Email Address', 'Location', 'Island', 'Country', 'P.O. Box No', 'PEP', 'PEP Comment',
            'Image', 'Salutation', 'First Name', 'Other Names', 'Surname', 'Passport No'
        ]);

        foreach ($clients as $client) {
            fputcsv($handle, [
                '⤢',
                $client->client_name,
                $client->client_type,
                $client->nin_bcrn ?: '##########',
                $client->dob_dor ? $client->dob_dor->format('d-M-y') : '##########',
                $client->mobile_no,
                $client->wa ?: '-',
                $client->district ?: '-',
                $client->occupation ?: '-',
                $client->source,
                $client->status,
                $client->signed_up ? $client->signed_up->format('d-M-y') : '##########',
                $client->employer ?: '-',
                $client->clid,
                $client->contact_person ?: '-',
                $client->income_source ?: '-',
                $client->married ? 'Yes' : 'No',
                $client->spouses_name ?: '-',
                $client->alternate_no ?: '-',
                $client->email_address ?: '-',
                $client->location ?: '-',
                $client->island ?: '-',
                $client->country ?: '-',
                $client->po_box_no ?: '-',
                $client->pep ? 'Yes' : 'No',
                $client->pep_comment ?: '-',
                $client->image ?: '-',
                $client->salutation ?: '-',
                $client->first_name,
                $client->other_names ?: '-',
                $client->surname,
                $client->passport_no ?: '-'
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {
            //
        }, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['client_columns' => $request->columns ?? []]);
        
        return redirect()->route('clients.index')
            ->with('success', 'Column settings saved successfully.');
    }

    private function getLookupData()
    {
        return [
            'client_types' => ['Individual', 'Business', 'Company', 'Organization'],
            'sources' => LookupCategory::where('name', 'Source')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'client_statuses' => ['Active', 'Inactive', 'Suspended', 'Pending'],
            'districts' => ['Victoria', 'Beau Vallon', 'Mont Fleuri', 'Cascade', 'Providence', 'Grand Anse', 'Anse Aux Pins'],
            'islands' => LookupCategory::where('name', 'Island')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'countries' => LookupCategory::where('name', 'Issuing Country')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'income_sources' => LookupCategory::where('name', 'Income Source')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'salutations' => LookupCategory::where('name', 'Salutation')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'occupations' => ['Accountant', 'Driver', 'Customer Service Officer', 'Real Estate Agent', 'Rock Breaker', 'Payroll Officer', 'Boat Charter', 'Contractor', 'Technician', 'Paymaster', 'Human Resources Manager']
        ];
    }
}