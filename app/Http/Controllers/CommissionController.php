<?php

namespace App\Http\Controllers;
use App\Models\Policy;

use App\Models\Commission;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $policy = null;
        $policyId = $request->get('policy_id');

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
            'commissionNote.schedule.policy'
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
                $query->where('insurer_id', $insurer->id);
            }
        }
    
        $commissions = $query
            ->orderByDesc('created_at')
            ->paginate(10);
    
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('commissions');
    
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
                'selectedColumns'
            )
        );
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_number' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'grouping' => 'nullable|string|max:255',
            'basic_premium' => 'nullable|numeric',
            'rate' => 'nullable|numeric',
            'amount_due' => 'nullable|numeric',
            'payment_status_id' => 'nullable|exists:lookup_values,id',
            'amount_rcvd' => 'nullable|numeric',
            'date_rcvd' => 'nullable|date',
            'state_no' => 'nullable|string|max:255',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'variance' => 'nullable|numeric',
            'reason' => 'nullable|string|max:255',
            'date_due' => 'nullable|date',
        ]);

        // Generate unique CNID
        $latest = Commission::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('CN', '', $latest->cnid) + 1 : 1001;
        $validated['cnid'] = 'CN' . $nextId;

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
            return response()->json($commission->load(['insurer', 'paymentStatus', 'modeOfPayment']));
        }
        return view('commissions.edit', compact('commission'));
    }

    public function update(Request $request, Commission $commission)
    {
        $validated = $request->validate([
            'policy_number' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'insurer_id' => 'nullable|exists:lookup_values,id',
            'grouping' => 'nullable|string|max:255',
            'basic_premium' => 'nullable|numeric',
            'rate' => 'nullable|numeric',
            'amount_due' => 'nullable|numeric',
            'payment_status_id' => 'nullable|exists:lookup_values,id',
            'amount_rcvd' => 'nullable|numeric',
            'date_rcvd' => 'nullable|date',
            'state_no' => 'nullable|string|max:255',
            'mode_of_payment_id' => 'nullable|exists:lookup_values,id',
            'variance' => 'nullable|numeric',
            'reason' => 'nullable|string|max:255',
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
