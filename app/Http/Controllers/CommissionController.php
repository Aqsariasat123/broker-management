<?php

namespace App\Http\Controllers;
use App\Models\Policy;
use App\Models\DebitNote;

use App\Models\Commission;
use App\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CommissionNote;
use App\Models\CommissionStatement;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $policy = null;
        $policyId = $request->get('policy_id');
        $commissionNote = CommissionNote::with('schedule')->orderBy('created_at', 'desc')->get();
        $commissionstatements = CommissionStatement::orderBy('created_at', 'desc')->get();

        // Lookup values
        $insurers = LookupValue::whereHas('lookupCategory', fn ($q) =>
            $q->where('name', 'Insurers')
        )->where('active', 1)->orderBy('seq')->get();
    
        $paymentStatuses = LookupValue::whereHas('lookupCategory', fn ($q) =>
            $q->where('name', 'Payment Status')
        )->where('active', 1)->orderBy('seq')->get();
    
        $modesOfPayment = LookupValue::whereHas('lookupCategory', fn ($q) =>
            $q->where('name', 'Mode Of Payment (Life)')
        )->where('active', 1)->orderBy('seq')->get();
    
        // ✅ BASE QUERY (THIS WAS MISSING)
        $query = Commission::with([
            'insurer',
            'paymentStatus',
            'modeOfPayment',
            'commissionNote.schedule.policy',
             'commissionNote.schedule.policy.insurer',
        ]);
        $policies = Policy::with('client')->get(); // all policies

        // ✅ POLICY FILTER (CORRECT RELATIONSHIP PATH)
        if ($request->filled('policy_id')) {
            $policy = Policy::with('client')->findOrFail($policyId);

            $query->whereHas('commissionNote.schedule.policy', function ($q) use ($request) {
                $q->where('id', $request->policy_id);
            });
        }
    
        // ✅ INSURER FILTER
            $insurerFilter = $request->get('insurer');

            if ($insurerFilter) {
                $insurer = $insurers->firstWhere('name', $insurerFilter);

                if ($insurer) {
                    $query->whereHas(
                        'commissionNote.schedule.policy',
                        fn ($q) => $q->where('insurer_id', $insurer->id)
                    );
                }
            }

            $statusFilter = $request->get('paid_status');
            if ($statusFilter) {
                $paymentStatusess = $paymentStatuses->firstWhere('name', $statusFilter);

                if ($paymentStatusess) {
                    $query->where('payment_status_id', $paymentStatusess->id);
                    
               } else {
                    // 🔒 Force empty result if status does not exist
                    $query->whereRaw('1 = 0');
                }
            }

    
        $commissions = $query
            ->orderByDesc('created_at')
            ->paginate(10);
    
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('commissions');
    
        Log::info('Selected Columns: ', $commissions->toArray());
        return view(
            'commissions.index',
            compact(
                'commissions',
                'insurers',
                'policy',
                'policies',
                'paymentStatuses',
                'modesOfPayment',
                'insurerFilter',
                'selectedColumns',
                'commissionNote',
                'commissionstatements'
                
            )
        );
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
             'commission_note_id'=> 'required|exists:commission_notes,id',
             'commission_statement_id'=> 'required|exists:commission_statements,id',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'grouping' => 'nullable|string|max:255',
            'basic_premium' => 'nullable|numeric',
            'rate' => 'nullable|numeric',
            'amount_due' => 'nullable|numeric',
            'payment_status_id' => 'nullable|exists:lookup_values,id',
            'amount_received' => 'nullable|numeric',
            'date_received' => 'nullable|date',
            'statement_no' => 'nullable|string|max:255',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'variance' => 'nullable|numeric',
            'variance_reason' => 'nullable|string|max:255',
            'date_due' => 'nullable|date',
        ]);

        // // Generate unique CNID
        $latest = Commission::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('CN', '', $latest->commission_code) + 1 : 1001;
        $validated['commission_code'] = 'CN' . $nextId;
        $validated['grouping'] = 'Commision note and statement';

        $commission = Commission::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Commission created successfully.',
                'commission' => $commission->load(['insurer', 'paymentStatus', 'modeOfPayment'])
            ]);
        }

        return redirect()->route('commissions.index')->with('success', 'Commission created successfully.');
    }

    public function show(Request $request, Commission $commission)
    {
        if ($request->expectsJson()) {
            return response()->json($commission->load(['insurer', 'paymentStatus', 'modeOfPayment']));
        }
        return view('commissions.show', compact('commission'));
    }

        public function edit(Commission $commission)
        {
            if (request()->expectsJson()) {
                // Load the same nested relationships as in index
                $commission->load([
                    'commissionNote.schedule.policy',   // for client_name
                    'commissionNote.schedule.policy.client',   // for client_name
                    'commissionNote.schedule.policy.insurer',  // for insurer
                    'paymentStatus',
                    'modeOfPayment',
                ]);

                return response()->json($commission);
            }

            return view('commissions.edit', compact('commission'));
        }


    public function update(Request $request, Commission $commission)
    {
        $validated = $request->validate([
            'commission_note_id'=> 'required|exists:commission_notes,id',
             'commission_statement_id'=> 'required|exists:commission_statements,id',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'grouping' => 'nullable|string|max:255',
            'basic_premium' => 'nullable|numeric',
            'rate' => 'nullable|numeric',
            'amount_due' => 'nullable|numeric',
            'payment_status_id' => 'nullable|exists:lookup_values,id',
            'amount_received' => 'nullable|numeric',
            'date_received' => 'nullable|date',
            'statement_no' => 'nullable|string|max:255',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'variance' => 'nullable|numeric',
            'variance_reason' => 'nullable|string|max:255',
            'date_due' => 'nullable|date',
        ]);

        $commission->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Commission updated successfully.',
                'commission' => $commission->load(['insurer', 'paymentStatus', 'modeOfPayment'])
            ]);
        }

        return redirect()->route('commissions.index')->with('success', 'Commission updated successfully.');
    }

    public function destroy(Commission $commission)
    {
        $commission->delete();
        return redirect()->route('commissions.index')->with('success', 'Commission deleted successfully.');
    }

    public function export(Request $request)
    {
        $commissions = \App\Models\Commission::with(['insurer', 'paymentStatus', 'modeOfPayment'])->get();

        $fileName = 'commissions_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Action', 'Policy Number', "Client's Name", 'Insurer', 'Grouping', 'Basic Premium', 'Rate', 'Amount Due',
            'Payment Status', 'Amount Rcvd', 'Date Rcvd', 'State No', 'Mode Of Payment (Life)', 'Variance', 'Reason', 'Date Due', 'CNID'
        ]);

        foreach ($commissions as $com) {
            fputcsv($handle, [
                '⤢',
                $com->policy_number,
                $com->client_name,
                $com->insurer ? $com->insurer->name : '',
                $com->grouping,
                $com->basic_premium,
                $com->rate,
                $com->amount_due,
                $com->paymentStatus ? $com->paymentStatus->name : '',
                $com->amount_rcvd,
                $com->date_rcvd,
                $com->state_no,
                $com->modeOfPayment ? $com->modeOfPayment->name : '',
                $com->variance,
                $com->reason,
                $com->date_due,
                $com->cnid,
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {
            //
        }, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['commission_columns' => $request->columns ?? []]);
        return redirect()->route('commissions.index')
            ->with('success', 'Column settings saved successfully.');
    }
}
