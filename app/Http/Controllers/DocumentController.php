<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::orderBy('created_at', 'desc')->paginate(10);
        
        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('documents');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('documents');
        
        return view('documents.index', compact('documents', 'selectedColumns'));
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
            $validated['format'] = $file->getClientOriginalExtension(); // Auto set format
        } else {
            $validated['format'] = null;
        }

        Document::create($validated);

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
            $validated['format'] = $file->getClientOriginalExtension(); // Auto set format
        }

        $document->update($validated);

        return redirect()->route('documents.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
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
