<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Client;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display the document viewer page with smart back button
     */
    public function viewer($id)
    {
        $document = Document::findOrFail($id);
        
        // Track previous URL for smart back button
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        
        if ($previousUrl !== $currentUrl && 
            !str_contains($previousUrl, '/storage/') && 
            !str_contains($previousUrl, '/assets/')) {
            session(['previous_url' => $previousUrl]);
        }
        
        // Try to find associated client
        if ($document->tied_to) {
            $client = Client::where('clid', $document->tied_to)->first();
            if ($client) {
                $document->client_id = $client->id;
                $document->client = $client;
            }
        }
        
        return view('documents.viewer', compact('document'));
    }

    public function index(Request $request)
    {
        $policy = null;

        // Get client information if filtering by client_id
        $client = null;
        if ($request->has('client_id') && $request->client_id) {
            $client = \App\Models\Client::find($request->client_id);
        }
        
        // If tied_to parameter is provided and request expects JSON, return JSON
        if ($request->has('tied_to') && $request->expectsJson()) {
            $documents = Document::where('tied_to', $request->tied_to)
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json($documents);
        }
         
        $query = Document::query();
        
        // Filter by client_id if provided
        if ($request->has('client_id') && $request->client_id) {
            $client = \App\Models\Client::find($request->client_id);
            if ($client && $client->clid) {
                $query->where('tied_to', $client->clid);
            }
        }
        
        if ($request->filled('policy_id')) {
            $policy = \App\Models\Policy::find($request->policy_id);
            if ($policy) {
                $query->where('tied_to', $policy->policy_no); 
            }
        }
        
        $documents = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('documents');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('documents');
        
        return view('documents.index', compact('documents', 'selectedColumns', 'client', 'policy'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tied_to' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'group' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'date_added' => 'nullable|date',
            'year' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        $latest = Document::orderBy('id', 'desc')->first();
        $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
        $validated['doc_id'] = 'DOC' . $nextId;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid('doc_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            $validated['file_path'] = $path;
            $validated['format'] = $file->getClientOriginalExtension();
        } else {
            $validated['format'] = null;
        }

        $document = Document::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document created successfully.',
                'document' => $document
            ]);
        }

        return redirect()->route('documents.index')->with('success', 'Document created successfully.');
    }

    public function show(Request $request, Document $document)
    {
        if ($request->expectsJson()) {
            return response()->json($document);
        }
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        if (request()->expectsJson()) {
            return response()->json($document);
        }
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'tied_to' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'group' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'date_added' => 'nullable|date',
            'year' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        if ($request->hasFile('file')) {
            if ($document->file_path && \Storage::disk('public')->exists($document->file_path)) {
                \Storage::disk('public')->delete($document->file_path);
            }
            $file = $request->file('file');
            $filename = uniqid('doc_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            $validated['file_path'] = $path;
            $validated['format'] = $file->getClientOriginalExtension();
        }

        $document->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully.',
                'document' => $document
            ]);
        }

        return redirect()->route('documents.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(Request $request, Document $document)
    {
        try {
            // Delete file from storage if exists
            if ($document->file_path && \Storage::disk('public')->exists($document->file_path)) {
                \Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document deleted successfully.'
                ]);
            }

            return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting document: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('documents.index')->with('error', 'Error deleting document.');
        }
    }

    public function saveColumnSettings(Request $request)
    {
        session(['document_columns' => $request->columns ?? []]);
        return redirect()->route('documents.index')
            ->with('success', 'Column settings saved successfully.');
    }

    public function export(Request $request)
    {
        $documents = Document::all();

        $fileName = 'documents_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $columns = [
            'DocID', 'Tied To', 'Name', 'Group', 'Type', 'Format', 'Date Added', 'Year', 'File', 'Notes'
        ];

        $callback = function() use ($documents, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($documents as $doc) {
                fputcsv($handle, [
                    $doc->doc_id,
                    $doc->tied_to,
                    $doc->name,
                    $doc->group,
                    $doc->type,
                    $doc->format,
                    $doc->date_added,
                    $doc->year,
                    $doc->file_path ? asset('storage/'.$doc->file_path) : '',
                    $doc->notes,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}