<?php

namespace App\Http\Controllers;

use App\Models\CommissionStatement;
use App\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatementController extends Controller
{
    
  public function index(Request $request)
        {
            // Lookup values for selects
            $insurers = LookupValue::whereHas('lookupCategory', fn($q) => $q->where('name', 'Insurers'))
                ->where('active', 1)
                ->orderBy('seq')
                ->get();

            $modesOfPayment = LookupValue::whereHas('lookupCategory', fn($q) => $q->where('name', 'Mode Of Payment (Life)'))
                ->where('active', 1)
                ->orderBy('seq')
                ->get();

         
            $incomeCategories = LookupValue::whereHas('lookupCategory', function($q){
                        $q->where('name', 'Income Category');
                    })->where('active', 1)->orderBy('seq')->get();

            // Build query
            $query = CommissionStatement::with([
                'income',
                'commissions.commissionNote.schedule.policy' => function($q) {
                    $q->with(['insurer', 'policyClass']);
                },
            ]);

            $insurerFilter = $request->get('insurer');
            $pageType = $request->get('page');

            if ($insurerFilter) {
                $insurer = $insurers->firstWhere('name', $insurerFilter);

                if ($insurer) {
                    $query->whereHas(
                        'commissions.commissionNote.schedule.policy.insurer',
                        fn ($q) => $q->where('insurer_id', $insurer->id)
                    );
                }
            }
    

           $statements= $query->orderBy('created_at', 'desc')
            ->paginate(10);




            // Selected columns via TableConfigHelper
            $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('statements');

            Log::info('Selected statements: ' . $statements->toJson());

            return view('statements.index', compact(
                'statements', 'insurers', 'modesOfPayment', 'insurerFilter', 'selectedColumns','pageType' , 'incomeCategories'
            ));
        }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'net_comission' => 'required|numeric',
            'tax_withheld' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Generate unique Statement No
        $latest = CommissionStatement::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('CST', '', $latest->com_stat_id) + 1 : 1001;
        $validated['com_stat_id'] = 'CST' . $nextId;

        $statement = CommissionStatement::create($validated);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Statement created successfully.',
                'statement' => $statement->load(['insurer', 'modeOfPayment'])
            ]);
        }

        return redirect()->route('statements.index')->with('success', 'Statement created successfully.');
    }

    public function show(Request $request, CommissionStatement $statement)
    {
      $statement=  $statement->load([
                'income',
                'commissions.modeOfPayment',
                'commissions.commissionNote.schedule.policy.insurer',
                'commissions.commissionNote.schedule.policy.policyClass',
     ]);
                 Log::info('statement: ' . $statement->toJson());

    if (request()->expectsJson()) {
        return response()->json(
           $statement
        );
    }

        return view('statements.show', compact('statement'));
    }

public function edit(CommissionStatement $statement)
{

   $statement=  $statement->load([
                'income',
                'commissions.modeOfPayment',
                'commissions.commissionNote.schedule.policy.insurer',
                'commissions.commissionNote.schedule.policy.policyClass',
     ]);
    if (request()->expectsJson()) {
        return response()->json(
           $statement
        );
    }
            Log::info('Selected edit: ' . $statement->toJson());

    return view('statements.edit', compact('statement'));
}


    public function update(Request $request, CommissionStatement $statement)
    {
        $validated = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'net_comission' => 'required|numeric',
            'tax_withheld' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:255',
        ]);

        $statement->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Statement updated successfully.',
                'statement' => $statement->load(['insurer', 'modeOfPayment'])
            ]);
        }

        return redirect()->route('statements.index')->with('success', 'Statement updated successfully.');
    }

    public function destroy(CommissionStatement $statement)
    {
        $statement->delete();
        return redirect()->route('statements.index')->with('success', 'Statement deleted successfully.');
    }

    public function export(Request $request)
    {
        $statements = \App\Models\CommissionStatement::with([
            'commissions.commissionNote.schedule.policy.insurer',
            'commissions.commissionNote.schedule.policy.policyClass',
            'commissions.modeOfPayment'
        ])->get();

        $fileName = 'statements_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($statements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Statement No', 'Year', 'Insurer', 'Business Category', 'Date Received', 'Amount Received', 'Mode Of Payment', 'Remarks'
            ]);

            foreach ($statements as $st) {
                $commission = $st->commissions->first();
                $year = $commission?->date_received ? \Carbon\Carbon::parse($commission->date_received)->format('Y') : '';
                $insurerName = $commission?->commissionNote?->schedule?->policy?->insurer?->name ?? '';
                $businessCategory = $commission?->commissionNote?->schedule?->policy?->policyClass?->name ?? '';
                $dateReceived = $commission?->date_received ? \Carbon\Carbon::parse($commission->date_received)->format('d-M-y') : '';
                $amountReceived = $commission?->amount_received ?? '';
                $modeOfPayment = $commission?->modeOfPayment?->name ?? '';

                fputcsv($handle, [
                    $st->com_stat_id,
                    $year,
                    $insurerName,
                    $businessCategory,
                    $dateReceived,
                    $amountReceived,
                    $modeOfPayment,
                    $st->remarks ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['statement_columns' => $request->columns ?? []]);
        return redirect()->route('statements.index')
            ->with('success', 'Column settings saved successfully.');
    }
}
