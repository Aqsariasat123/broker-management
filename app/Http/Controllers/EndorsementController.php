<?php

namespace App\Http\Controllers;
use App\Models\LookupValue;

use App\Models\Endorsement;
use App\Models\Policy;
use Illuminate\Http\Request;

class EndorsementController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('endorsements.view') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view endorsements.');
        }
    
        $query = Endorsement::with('policy')->orderBy('created_at', 'desc');
    
        if ($request->filled('policy_id')) {
            $query->where('policy_id', $request->policy_id);
        }
    
        $endorsements = $query->paginate(10);

        $policies = Policy::orderBy('policy_no')->get();
    
        // Lookup values for selects - use Insurers instead of Income Source
        $types = LookupValue::whereHas('lookupCategory', function($q){
            $q->where('name', 'Endorsements');
        })->where('active', 1)->orderBy('seq')->get();


        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('endorsements');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('endorsements',);


        return view('endorsements.index', compact('endorsements', 'policies', 'types','selectedColumns'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('endorsements.create') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to create endorsements.');
        }

        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'endorsement_no' => 'required|string|unique:endorsements,endorsement_no',
            'type' => 'nullable|string|max:100',
            'effective_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = 'endorsement_' . now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('endorsements', $filename, 'public');
            $validated['document_path'] = $path;
        }

        Endorsement::create($validated);

        return redirect()->route('endorsements.index')->with('success', 'Endorsement created successfully.');
    }

    public function show(Request $request, Endorsement $endorsement)
    {
        if (!auth()->user()->hasPermission('endorsements.view') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view endorsements.');
        }
        $endorsement->load('policy');
        if ($request->expectsJson()) {
            return response()->json($endorsement);
        }
        return view('endorsements.show', compact('endorsement'));
    }

    public function edit(Endorsement $endorsement)
    {
        if (!auth()->user()->hasPermission('endorsements.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit endorsements.');
        }
        $policies = Policy::orderBy('policy_no')->get();
        return view('endorsements.edit', compact('endorsement', 'policies'));
    }

    public function update(Request $request, Endorsement $endorsement)
    {
        if (!auth()->user()->hasPermission('endorsements.edit') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to edit endorsements.');
        }

        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'endorsement_no' => 'required|string|unique:endorsements,endorsement_no,' . $endorsement->id,
            'type' => 'nullable|string|max:100',
            'effective_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('document')) {
            if ($endorsement->document_path && \Storage::disk('public')->exists($endorsement->document_path)) {
                \Storage::disk('public')->delete($endorsement->document_path);
            }
            $file = $request->file('document');
            $filename = 'endorsement_' . now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('endorsements', $filename, 'public');
            $validated['document_path'] = $path;
        }

        $endorsement->update($validated);
        return redirect()->route('endorsements.index')->with('success', 'Endorsement updated successfully.');
    }

    public function destroy(Endorsement $endorsement)
    {
        if (!auth()->user()->hasPermission('endorsements.delete') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to delete endorsements.');
        }
        if ($endorsement->document_path && \Storage::disk('public')->exists($endorsement->document_path)) {
            \Storage::disk('public')->delete($endorsement->document_path);
        }
        $endorsement->delete();
        return redirect()->route('endorsements.index')->with('success', 'Endorsement deleted successfully.');
    }

    public function export(Request $request)
    {
        $endorsements = Endorsement::with('policy')->get();
        $fileName = 'endorsements_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['EndorsementNo', 'Policy No', 'Type', 'Effective Date', 'Status', 'Description']);
        foreach ($endorsements as $e) {
            fputcsv($handle, [
                $e->endorsement_no,
                $e->policy ? $e->policy->policy_no : '',
                $e->type,
                $e->effective_date,
                $e->status,
                $e->description,
            ]);
        }
        fclose($handle);
        return response()->streamDownload(function() use ($handle) {}, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['endorsement_columns' => $request->columns ?? []]);
        return redirect()->route('endorsements.index')->with('success', 'Column settings saved successfully.');
    }
}

