<?php

namespace App\Http\Controllers;

use App\Models\BeneficialOwner;
use App\Models\Client;
use App\Models\LookupValue;
use App\Models\LookupCategory;
use Illuminate\Http\Request;

class BeneficialOwnerController extends Controller
{
    public function index(Request $request)
    {
        // Always redirect to clients page if client_id is null
        if (!$request->has('client_id') || !$request->client_id) {
            return redirect()->route('clients.index');
        }

        $query = BeneficialOwner::query();

        // Filter by client_id
        $query->where('client_id', $request->client_id);

        // Check if 'removed' column exists before filtering
        if (\Illuminate\Support\Facades\Schema::hasColumn('beneficial_owners', 'removed')) {
            $query->where('removed', false);
        }
        
        $beneficialOwners = $query->with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Use TableConfigHelper for selected columns
        $config = \App\Helpers\TableConfigHelper::getConfig('beneficial_owners');
        $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('beneficial_owners');

        // Get client information
        $client = Client::find($request->client_id);
        if (!$client) {
            return redirect()->route('clients.index')->with('error', 'Client not found.');
        }

        // Get lookup data for dropdowns
        $lookupData = $this->getLookupData();

        return view('beneficial-owners.index', compact('beneficialOwners', 'selectedColumns', 'client', 'lookupData'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'nullable|exists:clients,id',
                'full_name' => 'required|string|max:255',
                'dob' => 'nullable|date',
                'nin_passport_no' => 'nullable|string|max:255',
                'country' => 'required|string|max:255',
                'expiry_date' => 'nullable|date',
                'status' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'shares' => 'nullable|numeric|min:0|max:100',
                'pep' => 'nullable|boolean',
                'pep_details' => 'nullable|string',
                'date_added' => 'nullable|date',
            ]);

            // Generate unique owner_code
            $latest = BeneficialOwner::orderBy('id', 'desc')->first();
            $nextId = $latest && $latest->owner_code ? (int)str_replace('BO', '', $latest->owner_code) + 1 : 1001;
            $validated['owner_code'] = 'BO' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Set date_added to today if not provided
            if (!isset($validated['date_added'])) {
                $validated['date_added'] = now();
            }

            $beneficialOwner = BeneficialOwner::create($validated);

            // Handle document upload - store in documents table
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $documentType = $request->input('document_type', 'id_card');
                
                // Map document types to names
                $documentNames = [
                    'id_card' => 'ID Card',
                    'passport' => 'Passport',
                    'proof_of_address' => 'Proof Of Address',
                    'other' => 'Other Document'
                ];
                
                $filename = 'bo_' . $beneficialOwner->id . '_document_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $filename, 'public');
                
                // Generate unique DOC ID
                $latest = \App\Models\Document::orderBy('id', 'desc')->first();
                $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
                $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                // Store in documents table
                \App\Models\Document::create([
                    'doc_id' => $docId,
                    'tied_to' => $beneficialOwner->owner_code,
                    'name' => $documentNames[$documentType] ?? 'Document',
                    'group' => 'Beneficial Owner Document',
                    'type' => $documentType,
                    'format' => $file->getClientOriginalExtension(),
                    'date_added' => now(),
                    'year' => now()->format('Y'),
                    'file_path' => $path,
                ]);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Beneficial Owner created successfully.',
                    'beneficial_owner' => $beneficialOwner
                ]);
            }

            return redirect()->route('beneficial-owners.index')->with('success', 'Beneficial Owner created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Beneficial Owner creation error: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating beneficial owner: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Error creating beneficial owner: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Request $request, BeneficialOwner $beneficialOwner)
    {
        $beneficialOwner->load('client');
        
        // Load documents
        $documents = \App\Models\Document::where('tied_to', $beneficialOwner->owner_code)
            ->where('group', 'Beneficial Owner Document')
            ->get();
        $beneficialOwner->documents = $documents;

        if ($request->expectsJson()) {
            return response()->json($beneficialOwner);
        }
        return view('beneficial-owners.show', compact('beneficialOwner'));
    }

    public function edit(BeneficialOwner $beneficialOwner)
    {
        $beneficialOwner->load('client');
        
        // Load documents
        $documents = \App\Models\Document::where('tied_to', $beneficialOwner->owner_code)
            ->where('group', 'Beneficial Owner Document')
            ->get();
        $beneficialOwner->documents = $documents;

        if (request()->expectsJson()) {
            return response()->json($beneficialOwner);
        }
        return view('beneficial-owners.edit', compact('beneficialOwner'));
    }

    public function update(Request $request, BeneficialOwner $beneficialOwner)
    {
        try {
            // For PUT requests with FormData, ensure all data is accessible
            // Merge all request data to ensure FormData fields are available
            $requestData = $request->all();
            
            // Handle empty strings for nullable fields
            $nullableFields = ['dob', 'nin_passport_no', 'expiry_date', 'shares', 'pep_details', 'date_added'];
            foreach ($nullableFields as $field) {
                if (isset($requestData[$field]) && $requestData[$field] === '') {
                    $requestData[$field] = null;
                }
            }
            
            // Handle PEP boolean conversion
            if (isset($requestData['pep'])) {
                $requestData['pep'] = filter_var($requestData['pep'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
            }
            
            // Merge back into request to ensure validation can access all fields
            $request->merge($requestData);
            
            $validated = $request->validate([
                'client_id' => 'nullable|exists:clients,id',
                'full_name' => 'required|string|max:255',
                'dob' => 'nullable|date',
                'nin_passport_no' => 'nullable|string|max:255',
                'country' => 'required|string|max:255',
                'expiry_date' => 'nullable|date',
                'status' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'shares' => 'nullable|numeric|min:0|max:100',
                'pep' => 'nullable|boolean',
                'pep_details' => 'nullable|string',
                'date_added' => 'nullable|date',
            ]);

            $beneficialOwner->update($validated);

            // Handle document upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $documentType = $request->input('document_type', 'id_card');
                
                // Map document types to names
                $documentNames = [
                    'id_card' => 'ID Card',
                    'passport' => 'Passport',
                    'proof_of_address' => 'Proof Of Address',
                    'other' => 'Other Document'
                ];
                
                $filename = 'bo_' . $beneficialOwner->id . '_document_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $filename, 'public');
                
                // Generate unique DOC ID
                $latest = \App\Models\Document::orderBy('id', 'desc')->first();
                $nextId = $latest && $latest->doc_id ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
                $docId = 'DOC' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                // Store in documents table
                \App\Models\Document::create([
                    'doc_id' => $docId,
                    'tied_to' => $beneficialOwner->owner_code,
                    'name' => $documentNames[$documentType] ?? 'Document',
                    'group' => 'Beneficial Owner Document',
                    'type' => $documentType,
                    'format' => $file->getClientOriginalExtension(),
                    'date_added' => now(),
                    'year' => now()->format('Y'),
                    'file_path' => $path,
                ]);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Beneficial Owner updated successfully.',
                    'beneficial_owner' => $beneficialOwner
                ]);
            }

            return redirect()->route('beneficial-owners.index')->with('success', 'Beneficial Owner updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Beneficial Owner update error: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating beneficial owner: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Error updating beneficial owner: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(BeneficialOwner $beneficialOwner)
    {
        // Soft delete by marking as removed
        $beneficialOwner->update(['removed' => true]);
        return redirect()->route('beneficial-owners.index')->with('success', 'Beneficial Owner deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = BeneficialOwner::with('client');
        
        // Check if 'removed' column exists before filtering
        if (\Illuminate\Support\Facades\Schema::hasColumn('beneficial_owners', 'removed')) {
            $query->where('removed', false);
        }
        
        $beneficialOwners = $query->get();

        $fileName = 'beneficial_owners_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Owner Code', 'Full Name', 'DOB', 'Age', 'NIN/Passport No', 'Country', 
            'Expiry Date', 'Status', 'Position', 'Shares', 'PEP', 'PEP Details', 'Date Added'
        ]);

        foreach ($beneficialOwners as $bo) {
            fputcsv($handle, [
                $bo->owner_code,
                $bo->full_name,
                $bo->dob ? $bo->dob->format('d-M-y') : '',
                $bo->age,
                $bo->nin_passport_no,
                $bo->country,
                $bo->expiry_date ? $bo->expiry_date->format('d-M-y') : '',
                $bo->status,
                $bo->position,
                $bo->shares ? $bo->shares . '%' : '',
                $bo->pep ? 'Y' : 'N',
                $bo->pep_details,
                $bo->date_added ? $bo->date_added->format('d-M-y') : '',
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {}, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['beneficial_owner_columns' => $request->columns ?? []]);
        return redirect()->route('beneficial-owners.index')
            ->with('success', 'Column settings saved successfully.');
    }

    private function getLookupData()
    {
        $getLookupValues = function($categoryName) {
            $category = LookupCategory::where('name', $categoryName)->first();
            if (!$category) return [];
            return $category->values()
                ->where('active', true)
                ->orderBy('seq')
                ->get()
                ->map(function($value) {
                    return ['id' => $value->id, 'name' => $value->name];
                })
                ->toArray();
        };

        return [
            'countries' => $getLookupValues('Issuing Country') ?: [
                ['id' => null, 'name' => 'Seychelles'],
                ['id' => null, 'name' => 'Sweden'],
                ['id' => null, 'name' => 'Holland'],
                ['id' => null, 'name' => 'United Kingdom'],
                ['id' => null, 'name' => 'United States'],
            ],
            'positions' => [
                ['id' => null, 'name' => 'Director'],
                ['id' => null, 'name' => 'Shareholder'],
                ['id' => null, 'name' => 'CEO'],
                ['id' => null, 'name' => 'CFO'],
                ['id' => null, 'name' => 'Secretary'],
                ['id' => null, 'name' => 'Treasurer'],
            ],
            'statuses' => [
                ['id' => null, 'name' => 'Active'],
                ['id' => null, 'name' => 'Expired'],
                ['id' => null, 'name' => 'Inactive'],
                ['id' => null, 'name' => 'Pending'],
            ],
        ];
    }
}
