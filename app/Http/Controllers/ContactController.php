<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();
        
        // Filter for Archived contacts
        if ($request->has('archived') && $request->archived == 'true') {
            $query->where('status', 'Archived');
        }
        
        // Use paginate instead of get
        $contacts = $query->orderBy('created_at', 'desc')->paginate(10);

        $lookupData = $this->getLookupData();
        
        return view('contacts.index', compact('contacts', 'lookupData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'type' => 'required|string',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'acquired' => 'nullable|date',
            'source' => 'required|string',
            'status' => 'required|string',
            'rank' => 'nullable|string',
            'first_contact' => 'nullable|date',
            'next_follow_up' => 'nullable|date',
            'coid' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'salutation' => 'required|string',
            'source_name' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'agent' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email_address' => 'nullable|email',
            'savings_budget' => 'nullable|numeric',
            'married' => 'boolean',
            'children' => 'nullable|integer|min:0',
            'children_details' => 'nullable|string',
            'vehicle' => 'nullable|string|max:255',
            'house' => 'nullable|string|max:255',
            'business' => 'nullable|string|max:255',
            'other' => 'nullable|string',
        ]);

        // Generate unique contact ID
        $latestContact = Contact::orderBy('id', 'desc')->first();
        $nextId = $latestContact ? (int)str_replace('CT', '', $latestContact->contact_id) + 1 : 166;
        $validated['contact_id'] = 'CT' . $nextId;

        Contact::create($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        if (request()->expectsJson()) {
            return response()->json($contact);
        }
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        if (request()->expectsJson()) {
            return response()->json($contact);
        }
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'type' => 'required|string',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'acquired' => 'nullable|date',
            'source' => 'required|string',
            'status' => 'required|string',
            'rank' => 'nullable|string',
            'first_contact' => 'nullable|date',
            'next_follow_up' => 'nullable|date',
            'coid' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'salutation' => 'required|string',
            'source_name' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'agent' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email_address' => 'nullable|email',
            'savings_budget' => 'nullable|numeric',
            'married' => 'boolean',
            'children' => 'nullable|integer|min:0',
            'children_details' => 'nullable|string',
            'vehicle' => 'nullable|string|max:255',
            'house' => 'nullable|string|max:255',
            'business' => 'nullable|string|max:255',
            'other' => 'nullable|string',
        ]);

        $contact->update($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function export()
    {
        $contacts = Contact::all();
        
        $fileName = 'contacts_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Action', 'Contact Name', 'Contact No', 'Type', 'Occupation', 'Employer',
            'Acquired', 'Source', 'Status', 'Rank', '1st Contact', 'Next FU',
            'COID', 'DOB', 'Salutation', 'Source Name', 'Agency', 'Agent',
            'Address', 'Email Address', 'Contact ID', 'Savings Budget', 'Married', 'Children',
            'Children Details', 'Vehicle', 'House', 'Business', 'Other'
        ]);

        foreach ($contacts as $contact) {
            fputcsv($handle, [
                '⤢',
                $contact->contact_name,
                $contact->contact_no ?: '##########',
                $contact->type,
                $contact->occupation ?: '-',
                $contact->employer ?: '-',
                $contact->acquired ? $contact->acquired->format('d-M-y') : '##########',
                $contact->source,
                $contact->status,
                $contact->rank ?: '-',
                $contact->first_contact ? $contact->first_contact->format('d-M-y') : '##########',
                $contact->next_follow_up ? $contact->next_follow_up->format('d-M-y') : '##########',
                $contact->coid ?: '##########',
                $contact->dob ? $contact->dob->format('d-M-y') : '##########',
                $contact->salutation,
                $contact->source_name ?: '-',
                $contact->agency ?: '-',
                $contact->agent ?: '-',
                $contact->address ?: '-',
                $contact->email_address ?: '-',
                $contact->contact_id,
                $contact->savings_budget ? number_format($contact->savings_budget, 2) : '##########',
                $contact->married ? 'Yes' : 'No',
                $contact->children ?: '0',
                $contact->children_details ?: '-',
                $contact->vehicle ?: '-',
                $contact->house ?: '-',
                $contact->business ?: '-',
                $contact->other ?: '-'
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {
            //
        }, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        // Save column settings to session or database
        session(['contact_columns' => $request->columns ?? []]);
        
        return redirect()->route('contacts.index')
            ->with('success', 'Column settings saved successfully.');
    }

    private function getLookupData()
    {
        return [
            'contact_types' => LookupCategory::where('name', 'Contact Type')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'sources' => LookupCategory::where('name', 'Source')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'agents' => LookupCategory::where('name', 'Agent')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'contact_statuses' => ['Not Contacted', 'In Discussion', 'Proposal Made', 'Keep In View', 'Archived', 'RNR', 'Differed'],
            'ranks' => ['VIP', 'High', 'Medium', 'Low', 'Warm'],
            'agencies' => LookupCategory::where('name', 'APL Agency')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'salutations' => LookupCategory::where('name', 'Salutation')->first()->values()->where('active', true)->pluck('name')->toArray(),
        ];
    }
}