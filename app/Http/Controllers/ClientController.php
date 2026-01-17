<?php
// app/Http/Controllers/ClientController.php
// COMPLETE FIXED VERSION - Both date filter and CLID issues resolved

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\Client;
use App\Models\Document;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\LookUpHelper;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $filter= null;

        $query = Client::query();
        
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));
        $dateRange = $request->input('date_range', 'month');

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                break;
            case 'quarter':
                $quarter = floor(($month - 1) / 3) + 1;
                $startDate = Carbon::create($year)->firstDayOfQuarter()->addMonths(3 * ($quarter - 1));
                $endDate = $startDate->copy()->addMonths(3)->subDay();
                break;
            case 'year':
                $startDate = Carbon::create($year)->startOfYear();
                $endDate = Carbon::create($year)->endOfYear();
                break;
            default:
                if (str_starts_with($dateRange, 'year-')) {
                    $yearOnly = (int) str_replace('year-', '', $dateRange);
                    $startDate = Carbon::create($yearOnly)->startOfYear();
                    $endDate = Carbon::create($yearOnly)->endOfYear();
                } else {
                    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                    $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                }
                break;
       }
        
        // =====================================================
        // FIX #1: DATE FILTER - Only apply for expired IDs filter
        // REMOVED global date filter that was hiding all clients!
        // =====================================================
        
        // OLD BUGGY CODE (REMOVED):
        // if ($startDate && $endDate) {
        //      $query->whereBetween('id_expiry_date', [$startDate, $endDate]);
        // }
        
        // NEW: Date filter only for expired IDs
        if ($request->has('filter') && $request->filter == 'ids_expired') {
            if ($startDate && $endDate) {
                $query->whereBetween('id_expiry_date', [$startDate, $endDate]);
            }
            $query->where('status','Expired');
        }
      
        // Filter for To Follow Up - show only clients with expired or expiring policies
        if ($request->has('follow_up') && $request->follow_up == 'true') {
            $query->whereHas('policies', function($q) {
                $q->where(function($subQ) {
                    $subQ->whereNotNull('end_date')
                         ->where('end_date', '<', now());
                })->orWhere(function($subQ) {
                    $subQ->whereNotNull('end_date')
                         ->where('end_date', '>=', now())
                         ->where('end_date', '<=', now()->addDays(30));
                });
            });
        }
        
        // Filter for Instalments Overdue
        if ($request->has('filter') && $request->filter == 'overdue') {
            $query->whereHas('paymentPlans', function($q) {
                $q->where('due_date', '<', now()->toDateString())
                  ->where('status', '!=', 'paid');
            });
        }
        
        // Filter for Birthdays Today
        if ($request->has('filter') && $request->filter == 'birthday_today') {
            $query->whereMonth('dob_dor', now()->month)
                  ->whereDay('dob_dor', now()->day);
        }

        // Filter for Birthdays in date range (from calendar)
        if ($request->has('filter') && $request->filter == 'birthdays') {
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = \Carbon\Carbon::parse($request->start_date);
                $endDate = \Carbon\Carbon::parse($request->end_date);

                // Get all clients with birthdays and filter in PHP (same logic as CalendarController)
                $query->whereNotNull('dob_dor')
                      ->where(function($q) use ($startDate, $endDate) {
                          // For same month range (most common case)
                          if ($startDate->month == $endDate->month) {
                              $q->whereRaw('MONTH(dob_dor) = ?', [$startDate->month])
                                ->whereRaw('DAY(dob_dor) >= ?', [$startDate->day])
                                ->whereRaw('DAY(dob_dor) <= ?', [$endDate->day]);
                          } else {
                              // Cross-month range
                              $q->where(function($sub) use ($startDate) {
                                  $sub->whereRaw('MONTH(dob_dor) = ?', [$startDate->month])
                                      ->whereRaw('DAY(dob_dor) >= ?', [$startDate->day]);
                              })->orWhere(function($sub) use ($endDate) {
                                  $sub->whereRaw('MONTH(dob_dor) = ?', [$endDate->month])
                                      ->whereRaw('DAY(dob_dor) <= ?', [$endDate->day]);
                              });
                          }
                      });
            }
        }

        if($request->has('filter')){
          $filter = $request->filter;
        }
        
        // Use paginate instead of get
        $clients = $query->with(['policies','agencies','agents','districts','salutations','sources','occupations','income_sources','islands','countries'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

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
        $lookupData =  LookUpHelper::getLookupData();
        
        return view('clients.index', compact('clients', 'lookupData','filter'));
    }

    public function store(Request $request)
    {
        $rules = [
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
            'salutation' => 'nullable|string|max:50',
            'first_name' => 'nullable|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
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
            'business_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ];
        
        // Conditional validation based on client type
        if ($request->client_type === 'Individual') {
            $rules['first_name'] = 'required|string|max:255';
            $rules['surname'] = 'required|string|max:255';
        } elseif (in_array($request->client_type, ['Business', 'Company', 'Organization'])) {
            $rules['business_name'] = 'required|string|max:255';
        }
        
        $validated = $request->validate($rules);

        // =====================================================
        // FIX #2: CLID GENERATION - Improved to prevent duplicates
        // =====================================================
        
        // OLD BUGGY CODE (REMOVED):
        // $latestClient = Client::orderBy('id', 'desc')->first();
        // $nextId = $latestClient ? (int)str_replace('CL', '', $latestClient->clid) + 1 : 1001;
        // $validated['clid'] = 'CL' . $nextId;
        
        // NEW: Find maximum CLID number across ALL clients
        $maxClidNumber = Client::where('clid', 'LIKE', 'CL%')
            ->selectRaw('MAX(CAST(SUBSTRING(clid, 3) AS UNSIGNED)) as max_num')
            ->value('max_num');

        $nextId = $maxClidNumber ? $maxClidNumber + 1 : 1001;
        $validated['clid'] = 'CL' . $nextId;

        // Safety check: Ensure uniqueness (prevents race conditions)
        $attempts = 0;
        while (Client::where('clid', $validated['clid'])->exists() && $attempts < 10) {
            $nextId++;
            $validated['clid'] = 'CL' . $nextId;
            $attempts++;
        }

        if ($attempts >= 10) {
            return redirect()->back()
                ->withErrors(['clid' => 'Unable to generate unique CLID. Please try again.'])
                ->withInput();
        }
        // =====================================================

        // Set client_name - both Individual and Business use same format
        $validated['client_name'] = trim(($validated['first_name'] ?? '') . ' ' . ($validated['other_names'] ?? '') . ' ' . ($validated['surname'] ?? ''));
        
        if (in_array($request->client_type, ['Business', 'Company', 'Organization'])) {
            $validated['client_name'] = $validated['business_name'];
            $validated['surname'] =  $validated['business_name'];
            $validated['first_name'] = '';
            $validated['other_names'] = '';
        }
        
        $client = Client::create($validated);

        // Handle image upload - store in documents table
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                return redirect()->back()->withErrors(['image' => 'Invalid image file.'])->withInput();
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            $minWidth = 350;
            $maxWidth = 650;
            $minHeight = 350;
            $maxHeight = 650;
            
            if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                return redirect()->back()->withErrors([
                    'image' => 'Photo must be passport size (approximately 600x600 pixels or 413x531 pixels). Current dimensions: ' . $width . 'x' . $height . ' pixels.'
                ])->withInput();
            }
            
            $aspectRatio = $width / $height;
            $squareRatio = 1.0;
            $rectRatio = 0.78;
            $tolerance = 0.15;
            
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

        if ($request->expectsJson() || $request->ajax()) {
            $client->load('documents');
            return response()->json([
                'success' => true,
                'message' => 'Client created successfully.',
                'client' => $client
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    // Rest of the methods remain the same...
    public function create()
    {
        $lookupData = LookUpHelper::getLookupData();
        return view('clients.create', compact('lookupData'));
    }

    public function show(Client $client)
    {
        if (request()->expectsJson() || request()->wantsJson()) {
            $client->load(
                'documents'  ,  
                'agencies',
        'agents',
        'districts',
        'salutations',
        'sources',
        'occupations',
        'income_sources',
        'islands',
        'countries');
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
        if (request()->expectsJson() || request()->wantsJson()) {
            $client->load('documents');
            return response()->json($client);
        }
        
        $lookupData = LookUpHelper::getLookupData();
        return view('clients.edit', compact('client', 'lookupData'));
    }

    public function update(Request $request, Client $client)
    {
        $rules = [
            'client_type' => 'required|string|max:255',
            'nin_bcrn' => 'nullable|string|max:50',
            'dob_dor' => 'nullable|date',
            'mobile_no' => 'required|string|max:20',
            'wa' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
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
            'other_names' => 'nullable|string|max:255',
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
            'business_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ];
        if ($request->client_type === 'Individual') {
            $rules['first_name'] = 'required|string|max:255';
            $rules['surname'] = 'required|string|max:255';
        } else {
            $rules['source'] = 'required|string|max:255';
            $rules['signed_up'] = 'required|date';
        }
        if ($request->client_type === 'Individual' ) {
            $rules['first_name'] = 'required|string|max:255';
            $rules['surname'] = 'required|string|max:255';
        }
        
        $validated = $request->validate($rules);

        $validated['client_name'] = trim(($validated['first_name'] ?? '') . ' ' . ($validated['other_names'] ?? '') . ' ' . ($validated['surname'] ?? ''));
        
        $validated['wa'] = $validated['wa'] ?? '0';

        if ($request->hasFile('image')) {
            if ($client->image && Storage::disk('public')->exists($client->image)) {
                Storage::disk('public')->delete($client->image);
            }
            $file = $request->file('image');
            
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                return redirect()->back()->withErrors(['image' => 'Invalid image file.'])->withInput();
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            $minWidth = 350;
            $maxWidth = 650;
            $minHeight = 350;
            $maxHeight = 650;
            
            if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                return redirect()->back()->withErrors([
                    'image' => 'Photo must be passport size (approximately 600x600 pixels or 413x531 pixels). Current dimensions: ' . $width . 'x' . $height . ' pixels.'
                ])->withInput();
            }
            
            $aspectRatio = $width / $height;
            $squareRatio = 1.0;
            $rectRatio = 0.78;
            $tolerance = 0.15;
            
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
            $validated['image'] = $client->image;
        }

        $validated['client_name'] = trim(($validated['first_name'] ?? '') . ' ' . ($validated['other_names'] ?? '') . ' ' . ($validated['surname'] ?? ''));
        
        if (in_array($request->client_type, ['Business', 'Company', 'Organization'])) {
            $validated['client_name'] = $validated['contact_person'];
            $validated['surname'] =  $validated['contact_person'];
            $validated['first_name'] = $validated['contact_person'];
            $validated['other_names'] = $validated['contact_person'];
        }

        $client->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            $client->load('documents');
            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully.',
                'client' => $client
            ]);
        }

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
        
        $selectedColumns = session('client_columns', [
            'client_name','client_type','nin_bcrn','dob_dor','mobile_no','wa','district','occupation','source','status','signed_up',
            'employer','clid','contact_person','income_source','married','spouses_name','alternate_no','email_address','location',
            'island','country','po_box_no','pep','pep_comment','image','salutation','first_name','other_names','surname','passport_no'
        ]);
        
        $columnDefinitions = [
            'client_name' => ['label' => 'Client Name', 'filter' => true],
            'client_type' => ['label' => 'Client Type', 'filter' => true],
            'nin_bcrn' => ['label' => 'NIN/BCRN', 'filter' => true],
            'dob_dor' => ['label' => 'DOB/DOR', 'filter' => false],
            'mobile_no' => ['label' => 'MobileNo', 'filter' => false],
            'wa' => ['label' => 'WA', 'filter' => false],
            'district' => ['label' => 'District', 'filter' => false],
            'occupation' => ['label' => 'Occupation', 'filter' => false],
            'source' => ['label' => 'Source', 'filter' => false],
            'status' => ['label' => 'Status', 'filter' => false],
            'signed_up' => ['label' => 'Signed Up', 'filter' => false],
            'employer' => ['label' => 'Employer', 'filter' => false],
            'clid' => ['label' => 'CLID', 'filter' => false],
            'contact_person' => ['label' => 'Contact Person', 'filter' => false],
            'income_source' => ['label' => 'Income Source', 'filter' => false],
            'married' => ['label' => 'Married', 'filter' => false],
            'spouses_name' => ['label' => 'Spouses Name', 'filter' => false],
            'alternate_no' => ['label' => 'Alternate No', 'filter' => false],
            'email_address' => ['label' => 'Email Address', 'filter' => false],
            'location' => ['label' => 'Location', 'filter' => false],
            'island' => ['label' => 'Island', 'filter' => false],
            'country' => ['label' => 'Country', 'filter' => false],
            'po_box_no' => ['label' => 'P.O. Box No', 'filter' => false],
            'pep' => ['label' => 'PEP', 'filter' => false],
            'pep_comment' => ['label' => 'PEP Comment', 'filter' => false],
            'image' => ['label' => 'Image', 'filter' => false],
            'salutation' => ['label' => 'Salutation', 'filter' => false],
            'first_name' => ['label' => 'First Name', 'filter' => false],
            'other_names' => ['label' => 'Other Names', 'filter' => false],
            'surname' => ['label' => 'Surname', 'filter' => false],
            'passport_no' => ['label' => 'Passport No', 'filter' => false],
        ];
        
        $fileName = 'clients_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $headers_array = ['Action'];
        foreach ($selectedColumns as $col) {
            if (isset($columnDefinitions[$col])) {
                $headers_array[] = $columnDefinitions[$col]['label'];
            }
        }

        $callback = function() use ($clients, $headers_array, $selectedColumns, $columnDefinitions) {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, $headers_array);
            
            $getValue = function($client, $column) {
                switch ($column) {
                    case 'client_name':
                        return $client->client_name;
                    case 'client_type':
                        return $client->client_type;
                    case 'nin_bcrn':
                        return $client->nin_bcrn ?: '##########';
                    case 'dob_dor':
                        return $client->dob_dor ? $client->dob_dor->format('d-M-y') : '##########';
                    case 'mobile_no':
                        return $client->mobile_no;
                    case 'wa':
                        return $client->wa ? 'Yes' : 'No';
                    case 'district':
                        return $client->district ?: '-';
                    case 'occupation':
                        return $client->occupation ?: '-';
                    case 'source':
                        return $client->source;
                    case 'status':
                        return $client->status == 'Inactive' ? 'Dormant' : ($client->status == 'Active' ? 'Active' : $client->status);
                    case 'signed_up':
                        return $client->signed_up ? $client->signed_up->format('d-M-y') : '##########';
                    case 'employer':
                        return $client->employer ?: '-';
                    case 'clid':
                        return $client->clid;
                    case 'contact_person':
                        return $client->contact_person ?: '-';
                    case 'income_source':
                        return $client->income_source ?: '-';
                    case 'married':
                        return $client->married ? 'Yes' : 'No';
                    case 'spouses_name':
                        return $client->spouses_name ?: '-';
                    case 'alternate_no':
                        return $client->alternate_no ?: '-';
                    case 'email_address':
                        return $client->email_address ?: '-';
                    case 'location':
                        return $client->location ?: '-';
                    case 'island':
                        return $client->island ?: '-';
                    case 'country':
                        return $client->country ?: '-';
                    case 'po_box_no':
                        return $client->po_box_no ?: '-';
                    case 'pep':
                        return $client->pep ? 'Yes' : 'No';
                    case 'pep_comment':
                        return $client->pep_comment ?: '-';
                    case 'image':
                        return $client->image ? 'Yes' : 'No';
                    case 'salutation':
                        return $client->salutation ?: '-';
                    case 'first_name':
                        return $client->first_name;
                    case 'other_names':
                        return $client->other_names ?: '-';
                    case 'surname':
                        return $client->surname;
                    case 'passport_no':
                        return $client->passport_no ?: '-';
                    default:
                        return '-';
                }
            };
            
            foreach ($clients as $client) {
                $row = ['⤢'];
                foreach ($selectedColumns as $col) {
                    if (isset($columnDefinitions[$col])) {
                        $row[] = $getValue($client, $col);
                    }
                }
                fputcsv($handle, $row);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function saveColumnSettings(Request $request)
    {
        session(['client_columns' => $request->columns ?? []]);
        return redirect()->route('clients.index')->with('success', 'Column settings saved successfully.');
    }

    public function uploadPhoto(Request $request, Client $client)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file.'
                ], 422);
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            $minWidth = 350;
            $maxWidth = 650;
            $minHeight = 350;
            $maxHeight = 650;
            
            if ($width < $minWidth || $width > $maxWidth || $height < $minHeight || $height > $maxHeight) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo must be passport size (approximately 600x600 pixels or 413x531 pixels). Current dimensions: ' . $width . 'x' . $height . ' pixels.'
                ], 422);
            }
            
            $aspectRatio = $width / $height;
            $squareRatio = 1.0;
            $rectRatio = 0.78;
            $tolerance = 0.15;
            
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
            
            $documentNames = [
                'id_document' => 'ID Card',
                'poa_document' => 'Proof Of Address',
                'other' => 'Business Document'
            ];
            
            $filename = 'client_' . $client->id . '_' . $documentType . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $filename, 'public');
            
            $latest = Document::orderBy('id', 'desc')->first();
            $nextId = $latest ? (int)str_replace('DOC', '', $latest->doc_id) + 1 : 1001;
            $docId = 'DOC' . $nextId;

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
}