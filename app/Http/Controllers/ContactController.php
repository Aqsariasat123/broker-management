<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log; // <-- Add this

use App\Models\Contact;
use App\Models\Followup;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContactController extends Controller
{
    public function index(Request $request)
    {

        $statusfilter = null;
        $query = Contact::query();
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
          $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        // Filter for Archived contacts
        if ($request->has('archived') && $request->archived == 'true') {
            $query->where('status', 'Archived');
        }
        
        // Filter for Open leads
        if ($request->has('status') && $request->status == 'open') {
            $query->where('status', '!=', 'Archived');
            $statusfilter = $request->status ;
        }
        
        // Filter for "To Follow Up" - contacts with next_follow_up in the past or within next 7 days
        if ($request->has('follow_up') && ($request->follow_up == 'true' || $request->follow_up == '1')) {
            $query->whereNotNull('next_follow_up')
                  ->where('next_follow_up', '<=', now()->addDays(7))
                  ->where('status', '!=', 'Archived');
        }
        
        // Filter by contact_id from sidebar
        if ($request->has('contact_id')) {
            $query->where('contact_id', $request->contact_id);
        }
        
        // Use paginate instead of get
        $contacts = $query->with(['contact_types', 'source_value', 'agent_user', 'agency_user','statusRelation'])->orderBy('created_at', 'desc')->paginate(10);

        // Calculate expiration status for each contact
        $contacts->getCollection()->transform(function ($contact) {
            $contact->hasExpired = $contact->hasExpired();
            $contact->hasExpiring = $contact->hasExpiring();
            return $contact;
        });

        $lookupData = \App\Helpers\LookUpHelper::getLookupData();
        
        // Get unique employers from contacts and clients for dropdown
        $employersFromContacts = Contact::whereNotNull('employer')->distinct()->pluck('employer')->filter();
        $employersFromClients = \App\Models\Client::whereNotNull('employer')->distinct()->pluck('employer')->filter();
        $allEmployers = $employersFromContacts->merge($employersFromClients)->unique()->sort()->values();
        
        // Get unique occupations
        $occupationsFromContacts = Contact::whereNotNull('occupation')->distinct()->pluck('occupation')->filter();
        $occupationsFromClients = \App\Models\Client::whereNotNull('occupation')->distinct()->pluck('occupation')->filter();
        $allOccupations = $occupationsFromContacts->merge($occupationsFromClients)->unique()->sort()->values();
        
        // Get users for agents
        $users = \App\Models\User::where('is_active', true)->select('id', 'name')->orderBy('name')->get();

        Log::info('Selected contacts: ', $contacts->toArray());
        
        return view('contacts.index', compact('contacts', 'lookupData', 'allEmployers', 'allOccupations', 'users','statusfilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'mobile_no' => 'nullable|string|max:20',
            'wa' => 'nullable|string|max:20',
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
            'salutation' => 'nullable|string',
            'source_name' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'agent' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
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
         $contact->load([
            'followups'
        ]);
            return response()->json($contact);
        }
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        if (request()->expectsJson()) {
              $contact->load([
                    'followups'
                ]);
            return response()->json($contact);
        }
        return view('contacts.edit', compact('contact'));
    }

  public function update(Request $request, Contact $contact)
{
    $validated = $request->validate([
        'contact_name' => 'required|string|max:255',
        'contact_no' => 'nullable|string|max:20',
        'mobile_no' => 'nullable|string|max:20',
        'wa' => 'nullable|string|max:20',
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
        'salutation' => 'nullable|string',
        'source_name' => 'nullable|string|max:255',
        'agency' => 'nullable|string|max:255',
        'agent' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'location' => 'nullable|string|max:255',
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

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'contact' => $contact,
            'message' => 'Contact updated successfully.'
        ]);
    }

    return redirect()->route('contacts.index')
        ->with('success', 'Contact updated successfully.');
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

    public function storeFollowup(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'follow_up_date' => 'required|date',
            'channel' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'summary' => 'nullable|string',
            'next_action' => 'nullable|string',
        ]);

        // Generate follow_up_code
        $latestFollowup = Followup::orderBy('id', 'desc')->first();
        $nextId = $latestFollowup ? $latestFollowup->id + 1 : 1;
        $validated['follow_up_code'] = 'FU' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        $validated['contact_id'] = $contact->id;
        $validated['user_id'] = auth()->id();

        $followup = Followup::create($validated);

        // Update contact's next_follow_up if needed
        if ($validated['follow_up_date']) {
            $contact->update(['next_follow_up' => $validated['follow_up_date']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'followup' => $followup,
                'message' => 'Follow up added successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Follow up added successfully.');
    }
}