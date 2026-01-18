<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Policy;
use App\Models\LookupCategory;
use App\Models\Client;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $policy= null;
        
        // Eager load common relations used for filtering/display
        $query = Schedule::with(['policy.client', 'policy.insurer', 'policy.policyClass', 'policy.agency', 'policy.policyPlan', 'policy.payPlan', 'policy.frequency', 'policy.vehicles']);

        // Pagination size (Set Record Lines)
        $perPage = intval($request->get('set_record_lines', 15));
        if ($perPage <= 0) {
            $perPage = 15;
        }

        // Direct filter by policy id
        if ($request->filled('policy_id')) {
            $query->where('policy_id', $request->policy_id);
        }
        if ($request->filled('policy_id')) {
            $policy = \App\Models\Policy::find($request->policy_id);
           
       }
        // Status (support both status and status_filter param)
        $status = $request->get('status') ?? $request->get('status_filter');
        if ($status) {
            $query->where('status', $status);
        }

        // Search across several fields (support search and search_term)
        $search = $request->get('search') ?? $request->get('search_term');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('schedule_no', 'like', "%{$search}%")
                  ->orWhereHas('policy', function($subQ) use ($search) {
                      $subQ->where('policy_no', 'like', "%{$search}%")
                           ->orWhereHas('client', function($c) use ($search) {
                               $c->where('client_name', 'like', "%{$search}%");
                           });
                  });
            });
        }

        // Filter by client id (preferred) or client_name text
        if ($request->filled('client_id')) {
            $clientId = intval($request->client_id);
            $query->whereHas('policy', function($q2) use ($clientId) {
                $q2->where('client_id', $clientId);
            });
        } elseif ($request->filled('client_name')) {
            $clientName = $request->client_name;
            $query->whereHas('policy.client', function($q) use ($clientName) {
                $q->where('client_name', 'like', "%{$clientName}%");
            });
        }

        // Policy number filter
        if ($request->filled('policy_number')) {
            $policyNo = $request->policy_number;
            $query->whereHas('policy', function($q) use ($policyNo) {
                $q->where('policy_no', 'like', "%{$policyNo}%");
            });
        }

        // Insurer filter (accept id or name)
        if ($request->filled('insurer')) {
            $insurer = $request->insurer;
            if (is_numeric($insurer)) {
                $query->whereHas('policy', function($q2) use ($insurer) {
                    $q2->where('insurer_id', $insurer);
                });
            } else {
                $query->whereHas('policy.insurer', function($q) use ($insurer) {
                    $q->where('name', 'like', "%{$insurer}%");
                });
            }
        }

        // Insurance class filter (accept id or name)
        if ($request->filled('insurance_class')) {
            $ic = $request->insurance_class;
            if (is_numeric($ic)) {
                $query->whereHas('policy', function($q2) use ($ic) {
                    $q2->where('policy_class_id', $ic);
                });
            } else {
                $query->whereHas('policy.policyClass', function($q) use ($ic) {
                    $q->where('name', 'like', "%{$ic}%");
                });
            }
        }

        // Agency filter (accept id or name)
        if ($request->filled('agency')) {
            $agency = $request->agency;
            if (is_numeric($agency)) {
                $query->whereHas('policy', function($q2) use ($agency) {
                    $q2->where('agency_id', $agency);
                });
            } else {
                $query->whereHas('policy.agency', function($q) use ($agency) {
                    $q->where('name', 'like', "%{$agency}%");
                });
            }
        }

        // Agent (string on policy)
        if ($request->filled('agent')) {
            $agent = $request->agent;
            $query->whereHas('policy', function($q) use ($agent) {
                $q->where('agent', 'like', "%{$agent}%");
            });
        }

        // Date filters: effective_from and effective_to
        if ($request->filled('from_start_date')) {
            $query->where('effective_from', '>=', $request->from_start_date);
        }
        if ($request->filled('from_end_date')) {
            $query->where('effective_to', '<=', $request->from_end_date);
        }

        // Premium unpaid (approximate using policy.premium)
        if ($request->filled('premium_unpaid')) {
            $val = floatval($request->premium_unpaid);
            $query->whereHas('policy', function($q) use ($val) {
                $q->where('premium', '>=', $val);
            });
        }

        // Commission unpaid (approximate using commission_notes.expected_commission)
        if ($request->filled('commission_unpaid')) {
            $val = floatval($request->commission_unpaid);
            $query->whereHas('commissionNotes', function($q) use ($val) {
                $q->where('expected_commission', '>=', $val);
            });
        }

        $schedules = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        // Get policies and clients for filter
        $policies = Policy::with('client')->orderBy('policy_no')->get();
        $clients = Client::orderBy('client_name')->get(['id', 'client_name']);

        // Load lookup categories and prepare option lists (normalize category names)
        $lookupCategories = LookupCategory::with('values')->get();
        $lookupMap = [];
        foreach ($lookupCategories as $cat) {
            $key = strtolower(str_replace(' ', '_', trim($cat->name)));
            $lookupMap[$key] = $cat->values;
        }

        $insurers = $lookupMap['insurer'] ?? collect();
        $policyClasses = $lookupMap['policy_classes'] ?? ($lookupMap['policy_classes'] ?? collect());
        $agencies = $lookupMap['apl_agency'] ?? collect();
        $statuses = $lookupMap['policy_status'] ?? ($lookupMap['status'] ?? collect());

        // Agents list (distinct agent names from policies)
        $agents = Policy::select('agent')->whereNotNull('agent')->distinct()->orderBy('agent')->pluck('agent');
        Log::info('Lookup Map:',$agencies->toArray());

        return view('schedules.index', compact('schedules', 'policies', 'clients', 'insurers', 'policyClasses', 'agencies', 'statuses', 'agents', 'policy'));
    }

    public function create()
    {
        $policies = Policy::with('client')->orderBy('policy_no')->get();
        return view('schedules.create', compact('policies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'schedule_no' => 'required|string|max:255|unique:schedules,schedule_no',
            'issued_on' => 'nullable|date',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date',
            'status' => 'required|in:draft,active,expired,cancelled',
            'debit_note_path' => 'nullable|string|max:255',
            'receipt_path' => 'nullable|string|max:255',
            'policy_schedule_path' => 'nullable|string|max:255',
            'renewal_notice_path' => 'nullable|string|max:255',
            'payment_agreement_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $schedule = Schedule::create($validated);

        // Log activity
        \App\Models\AuditLog::log('create', $schedule, null, $schedule->getAttributes(), 'Schedule created: ' . $schedule->schedule_no);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully.',
                'schedule' => $schedule
            ]);
        }

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['policy.client', 'paymentPlans.debitNotes.payments']);
        return view('schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $policies = Policy::with('client')->orderBy('policy_no')->get();
        
        if (request()->ajax()) {
            return response()->json([
                'schedule' => $schedule,
                'policies' => $policies
            ]);
        }
        
        return view('schedules.edit', compact('schedule', 'policies'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'schedule_no' => 'required|string|max:255|unique:schedules,schedule_no,' . $schedule->id,
            'issued_on' => 'nullable|date',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date',
            'status' => 'required|in:draft,active,expired,cancelled',
            'debit_note_path' => 'nullable|string|max:255',
            'receipt_path' => 'nullable|string|max:255',
            'policy_schedule_path' => 'nullable|string|max:255',
            'renewal_notice_path' => 'nullable|string|max:255',
            'payment_agreement_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $schedule->getAttributes();
        $schedule->update($validated);

        // Log activity
        \App\Models\AuditLog::log('update', $schedule, $oldValues, $schedule->getChanges(), 'Schedule updated: ' . $schedule->schedule_no);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully.',
                'schedule' => $schedule
            ]);
        }

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $scheduleNo = $schedule->schedule_no;
        $schedule->delete();

        // Log activity
        \App\Models\AuditLog::log('delete', $schedule, $schedule->getAttributes(), null, 'Schedule deleted: ' . $scheduleNo);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
