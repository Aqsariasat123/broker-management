<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\LookupValue;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        // Lookup values for selects
        $insurers = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Insurers');
        })->where('active', 1)->orderBy('seq')->get();

        $paymentStatuses = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Payment Status');
        })->where('active', 1)->orderBy('seq')->get();

        $modesOfPayment = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Mode Of Payment (Life)');
        })->where('active', 1)->orderBy('seq')->get();

        // Filter by insurer if requested
        $insurerFilter = $request->get('insurer');
        $commissions = Commission::with(['insurer', 'paymentStatus', 'modeOfPayment'])
            ->when($insurerFilter, function($q) use ($insurerFilter, $insurers) {
                $insurer = $insurers->firstWhere('name', $insurerFilter);
                if ($insurer) {
                    $q->where('insurer_id', $insurer->id);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('commissions');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('commissions');

        return view('commissions.index', compact('commissions', 'insurers', 'paymentStatuses', 'modesOfPayment', 'insurerFilter', 'selectedColumns'));
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
