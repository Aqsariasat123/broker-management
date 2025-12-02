<?php
// app/Http/Controllers/ClientController.php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();
        
        // Filter for To Follow Up - show only clients with expired or expiring policies
        if ($request->has('follow_up') && $request->follow_up == 'true') {
            $query->whereHas('policies', function($q) {
                $q->where(function($subQ) {
                    // Expired policies (end_date is in the past)
                    $subQ->whereNotNull('end_date')
                         ->where('end_date', '<', now());
                })->orWhere(function($subQ) {
                    // Expiring policies (end_date is within next 30 days)
                    $subQ->whereNotNull('end_date')
                         ->where('end_date', '>=', now())
                         ->where('end_date', '<=', now()->addDays(30));
                });
            });
        }
        
        // Use paginate instead of get
        $clients = $query->with('policies')->orderBy('created_at', 'desc')->paginate(10);

        // Calculate expiration status for each client
        $clients->getCollection()->transform(function ($client) {
            $expiredPolicies = [];
            $expiringPolicies = [];
            
            foreach ($client->policies as $policy) {
                if ($policy->isExpired()) {
                    $expiredPolicies[] = $policy;
                } elseif ($policy->isDueForRenewal()) {
                    $expiringPolicies[] = $policy;
                }
            }
            
            $client->hasExpired = count($expiredPolicies) > 0;
            $client->hasExpiring = count($expiringPolicies) > 0;
            $client->expiredPolicies = $expiredPolicies;
            $client->expiringPolicies = $expiringPolicies;
            
            return $client;
        });

        // Get lookup data for dropdowns
        $lookupData = $this->getLookupData();
        
        return view('clients.index', compact('clients', 'lookupData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'client_name' => 'required|string|max:255', // REMOVE THIS LINE
            'client_type' => 'required|string|max:255',
            'nin_bcrn' => 'nullable|string|max:50',
            'dob_dor' => 'nullable|date',
            'mobile_no' => 'required|string|max:20',
            'wa' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'signed_up' => 'required|date',
            'employer' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'income_source' => 'nullable|string|max:255',
            'married' => 'boolean',
            'spouses_name' => 'nullable|string|max:255',
            'alternate_no' => 'nullable|string|max:20',
            'email_address' => 'nullable|email',
            'location' => 'nullable|string',
            'island' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'po_box_no' => 'nullable|string|max:50',
            'pep' => 'boolean',
            'pep_comment' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'salutation' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'passport_no' => 'nullable|string|max:50',
            'id_expiry_date' => 'nullable|date',
            'monthly_income' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'agent' => 'nullable|string|max:255',
            'source_name' => 'nullable|string|max:255',
            'has_vehicle' => 'boolean',
            'has_house' => 'boolean',
            'has_business' => 'boolean',
            'has_boat' => 'boolean',
            'notes' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        // Generate unique CLID
        $latestClient = Client::orderBy('id', 'desc')->first();
        $nextId = $latestClient ? (int)str_replace('CL', '', $latestClient->clid) + 1 : 1001;
        $validated['clid'] = 'CL' . $nextId;

        // Combine names for client_name
        $validated['client_name'] = trim($validated['first_name'] . ' ' . ($validated['other_names'] ?? '') . ' ' . $validated['surname']);

        $client = Client::create($validated);

        // Handle image upload - store in documents table
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Validate passport photo dimensions
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                return redirect()->back()->withErrors(['image' => 'Invalid image file.'])->withInput();
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            // Passport photo standard dimensions (in pixels at 300 DPI):
            // Square format: 600x600 pixels (2x2 inches) - most common
            // Rectangular format: 413x531 pixels (35x45 mm)
            // Allow some tolerance: ±50 pixels for width/height
            $minWidth = 350;
            $maxWidth = 650;
            $minHeight = 350;
            $maxHeight = 650;
            
            // Check if dimensions are within acceptable range
            if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                return redirect()->back()->withErrors([
                    'image' => 'Photo must be passport size (approximately 600x600 pixels or 413x531 pixels). Current dimensions: ' . $width . 'x' . $height . ' pixels.'
                ])->withInput();
            }
            
            // Check aspect ratio (should be close to 1:1 for square or 0.78:1 for rectangular)
            $aspectRatio = $width / $height;
            $squareRatio = 1.0; // 1:1 for square passport photos
            $rectRatio = 0.78; // 35:45 mm ratio
            $tolerance = 0.15; // Allow 15% tolerance
            
            $isSquare = abs($aspectRatio - $squareRatio) <= $tolerance;
            $isRectangular = abs($aspectRatio - $rectRatio) <= $tolerance;
            
            if (!$isSquare && !$isRectangular) {
                return redirect()->back()->withErrors([
                    'image' => 'Photo must have passport size aspect ratio (square 1:1 or rectangular 35:45mm). Current ratio: ' . round($aspectRatio, 2) . ':1'
                ])->withInput();
            }
            
            $filename = 'client_photo_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            $client->update(['image' => $path]);
            
            // Also store in documents table
            $latest = Document::orderBy('id', 'desc')->first();
            $nextDocId = $latest ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . $nextDocId;
            
            Document::create([
                'doc_id' => $docId,
                'tied_to' => $client->clid,
                'name' => 'Client Photo',
                'group' => 'Photo',
                'type' => 'Photo',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function create()
    {
        $lookupData = $this->getLookupData();
        return view('clients.create', compact('lookupData'));
    }

    public function show(Client $client)
    {
        // If request expects JSON (AJAX), return JSON
        if (request()->expectsJson() || request()->wantsJson()) {
            $client->load('documents');
            return response()->json($client);
        }
        
        $client->load(['policies' => function($query) {
            $query->with(['insurer', 'policyClass', 'policyPlan', 'policyStatus'])
                  ->orderBy('date_registered', 'desc');
        }, 'documents']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        // If request expects JSON (AJAX), return JSON for modal
        if (request()->expectsJson() || request()->wantsJson()) {
            $client->load('documents');
            return response()->json($client);
        }
        
        $lookupData = $this->getLookupData();
        return view('clients.edit', compact('client', 'lookupData'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            // 'client_name' => 'required|string|max:255', // REMOVE THIS LINE
            'client_type' => 'required|string|max:255',
            'nin_bcrn' => 'nullable|string|max:50',
            'dob_dor' => 'nullable|date',
            'mobile_no' => 'required|string|max:20',
            'wa' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'signed_up' => 'required|date',
            'employer' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'income_source' => 'nullable|string|max:255',
            'married' => 'boolean',
            'spouses_name' => 'nullable|string|max:255',
            'alternate_no' => 'nullable|string|max:20',
            'email_address' => 'nullable|email',
            'location' => 'nullable|string',
            'island' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'po_box_no' => 'nullable|string|max:50',
            'pep' => 'boolean',
            'pep_comment' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'salutation' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'passport_no' => 'nullable|string|max:50',
            'id_expiry_date' => 'nullable|date',
            'monthly_income' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'agent' => 'nullable|string|max:255',
            'source_name' => 'nullable|string|max:255',
            'has_vehicle' => 'boolean',
            'has_house' => 'boolean',
            'has_business' => 'boolean',
            'has_boat' => 'boolean',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        // Validate that image is required if no existing image
        if (!$client->image && !$request->hasFile('image')) {
            return redirect()->back()->withErrors(['image' => 'Passport size photo is required.'])->withInput();
        }

        // Handle image upload if new file provided - store in documents table
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($client->image && Storage::disk('public')->exists($client->image)) {
                Storage::disk('public')->delete($client->image);
            }
            $file = $request->file('image');
            
            // Validate passport photo dimensions
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                return redirect()->back()->withErrors(['image' => 'Invalid image file.'])->withInput();
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            // Passport photo standard dimensions (in pixels at 300 DPI):
            // Square format: 600x600 pixels (2x2 inches) - most common
            // Rectangular format: 413x531 pixels (35x45 mm)
            // Allow some tolerance: ±50 pixels for width/height
            $minWidth = 350;
            $maxWidth = 650;
            $minHeight = 350;
            $maxHeight = 650;
            
            // Check if dimensions are within acceptable range
            if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                return redirect()->back()->withErrors([
                    'image' => 'Photo must be passport size (approximately 600x600 pixels or 413x531 pixels). Current dimensions: ' . $width . 'x' . $height . ' pixels.'
                ])->withInput();
            }
            
            // Check aspect ratio (should be close to 1:1 for square or 0.78:1 for rectangular)
            $aspectRatio = $width / $height;
            $squareRatio = 1.0; // 1:1 for square passport photos
            $rectRatio = 0.78; // 35:45 mm ratio
            $tolerance = 0.15; // Allow 15% tolerance
            
            $isSquare = abs($aspectRatio - $squareRatio) <= $tolerance;
            $isRectangular = abs($aspectRatio - $rectRatio) <= $tolerance;
            
            if (!$isSquare && !$isRectangular) {
                return redirect()->back()->withErrors([
                    'image' => 'Photo must have passport size aspect ratio (square 1:1 or rectangular 35:45mm). Current ratio: ' . round($aspectRatio, 2) . ':1'
                ])->withInput();
            }
            
            $filename = 'client_' . $client->id . '_photo_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            $validated['image'] = $path;
            
            // Also store in documents table
            $latest = Document::orderBy('id', 'desc')->first();
            $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . $nextId;
            
            Document::create([
                'doc_id' => $docId,
                'tied_to' => $client->clid,
                'name' => 'Client Photo',
                'group' => 'Photo',
                'type' => 'Photo',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        } elseif ($request->has('existing_image')) {
            // Keep existing image
            $validated['image'] = $client->image;
        }

        // Combine names for client_name
        $validated['client_name'] = trim($validated['first_name'] . ' ' . ($validated['other_names'] ?? '') . ' ' . $validated['surname']);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    public function export()
    {
        $clients = Client::all();
        
        $fileName = 'clients_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Action', 'Client Name', 'Client Type', 'NIN/BCRN', 'DOB/DOR', 'MobileNo', 'WA',
            'District', 'Occupation', 'Source', 'Status', 'Signed Up', 'Employer', 'CLID',
            'Contact Person', 'Income Source', 'Married', 'Spouses Name', 'Alternate No',
            'Email Address', 'Location', 'Island', 'Country', 'P.O. Box No', 'PEP', 'PEP Comment',
            'Image', 'Salutation', 'First Name', 'Other Names', 'Surname', 'Passport No'
        ]);

        foreach ($clients as $client) {
            fputcsv($handle, [
                '⤢',
                $client->client_name,
                $client->client_type,
                $client->nin_bcrn ?: '##########',
                $client->dob_dor ? $client->dob_dor->format('d-M-y') : '##########',
                $client->mobile_no,
                $client->wa ?: '-',
                $client->district ?: '-',
                $client->occupation ?: '-',
                $client->source,
                $client->status,
                $client->signed_up ? $client->signed_up->format('d-M-y') : '##########',
                $client->employer ?: '-',
                $client->clid,
                $client->contact_person ?: '-',
                $client->income_source ?: '-',
                $client->married ? 'Yes' : 'No',
                $client->spouses_name ?: '-',
                $client->alternate_no ?: '-',
                $client->email_address ?: '-',
                $client->location ?: '-',
                $client->island ?: '-',
                $client->country ?: '-',
                $client->po_box_no ?: '-',
                $client->pep ? 'Yes' : 'No',
                $client->pep_comment ?: '-',
                $client->image ?: '-',
                $client->salutation ?: '-',
                $client->first_name,
                $client->other_names ?: '-',
                $client->surname,
                $client->passport_no ?: '-'
            ]);
        }

        fclose($handle);
        return response()->streamDownload(function() use ($handle) {
            //
        }, $fileName, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['client_columns' => $request->columns ?? []]);
        
        return redirect()->route('clients.index')
            ->with('success', 'Column settings saved successfully.');
    }

    public function uploadPhoto(Request $request, Client $client)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            // Validate passport photo dimensions
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file.'
                ], 422);
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            // Passport photo standard dimensions (in pixels at 300 DPI):
            // Square format: 600x600 pixels (2x2 inches) - most common
            // Rectangular format: 413x531 pixels (35x45 mm)
            // Allow some tolerance: ±50 pixels for width/height
            $minWidth = 350;
            $maxWidth = 650;
            $minHeight = 350;
            $maxHeight = 650;
            
            // Check if dimensions are within acceptable range
            if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo must be passport size (approximately 600x600 pixels or 413x531 pixels). Current dimensions: ' . $width . 'x' . $height . ' pixels.'
                ], 422);
            }
            
            // Check aspect ratio (should be close to 1:1 for square or 0.78:1 for rectangular)
            $aspectRatio = $width / $height;
            $squareRatio = 1.0; // 1:1 for square passport photos
            $rectRatio = 0.78; // 35:45 mm ratio
            $tolerance = 0.15; // Allow 15% tolerance
            
            $isSquare = abs($aspectRatio - $squareRatio) <= $tolerance;
            $isRectangular = abs($aspectRatio - $rectRatio) <= $tolerance;
            
            if (!$isSquare && !$isRectangular) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo must have passport size aspect ratio (square 1:1 or rectangular 35:45mm). Current ratio: ' . round($aspectRatio, 2) . ':1'
                ], 422);
            }
            
            $filename = 'client_' . $client->id . '_photo_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = Document::orderBy('id', 'desc')->first();
            $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . $nextId;

            // Store in documents table
            Document::create([
                'doc_id' => $docId,
                'tied_to' => $client->clid,
                'name' => 'Client Photo',
                'group' => 'Photo',
                'type' => 'Photo',
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);

            // Also update client image field for backward compatibility
            $client->update(['image' => $path]);
        }

        $client->load('documents');
        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully.',
            'client' => $client
        ]);
    }

    public function uploadDocument(Request $request, Client $client)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            'document_type' => 'required|in:id_document,poa_document,other',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentType = $request->document_type;
            
            // Map document types to names
            $documentNames = [
                'id_document' => 'ID Card',
                'poa_document' => 'Proof Of Address',
                'other' => 'Business Document'
            ];
            
            $filename = 'client_' . $client->id . '_' . $documentType . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            // Generate unique DOC ID
            $latest = Document::orderBy('id', 'desc')->first();
            $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . $nextId;

            // Store in documents table
            Document::create([
                'doc_id' => $docId,
                'tied_to' => $client->clid,
                'name' => $documentNames[$documentType],
                'group' => 'Client Document',
                'type' => $documentType,
                'format' => $file->getClientOriginalExtension(),
                'date_added' => now(),
                'year' => now()->format('Y'),
                'file_path' => $path,
            ]);
        }

        $client->load('documents');
        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'client' => $client
        ]);
    }

    private function getLookupData()
    {
        return [
            'client_types' => ['Individual', 'Business', 'Company', 'Organization'],
            'sources' => LookupCategory::where('name', 'Source')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'client_statuses' => ['Active', 'Inactive', 'Suspended', 'Pending'],
            'districts' => ['Victoria', 'Beau Vallon', 'Mont Fleuri', 'Cascade', 'Providence', 'Grand Anse', 'Anse Aux Pins'],
            'islands' => LookupCategory::where('name', 'Island')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'countries' => LookupCategory::where('name', 'Issuing Country')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'income_sources' => LookupCategory::where('name', 'Income Source')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'salutations' => LookupCategory::where('name', 'Salutation')->first()->values()->where('active', true)->pluck('name')->toArray(),
            'occupations' => ['Accountant', 'Driver', 'Customer Service Officer', 'Real Estate Agent', 'Rock Breaker', 'Payroll Officer', 'Boat Charter', 'Contractor', 'Technician', 'Paymaster', 'Human Resources Manager']
        ];
    }
}