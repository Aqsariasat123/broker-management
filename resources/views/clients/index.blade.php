@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/clients-index.css') }}">
@include('partials.table-styles')

@php
  $selectedColumns = session('client_columns', [
    'client_name','client_type','nin_bcrn','dob_dor','mobile_no','wa','district','occupation','source','status','signed_up',
    'employer','clid','contact_person','income_source','married','spouses_name','children','children_details','alternate_no','email_address','location',
    'island','country','po_box_no','pep','pep_comment','image','salutation','first_name','other_names','surname','passport_no','pic','industry'
  ]);
@endphp

<div class="dashboard">
  <!-- Success/Error Notification Banner -->
  <div id="notificationBanner" style="display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%); z-index:10000; background:#28a745; color:#fff; padding:12px 24px; border-radius:4px; box-shadow:0 4px 6px rgba(0,0,0,0.1); font-size:14px; font-weight:500; max-width:500px; text-align:center; align-items:center; justify-content:center;">
    <span id="notificationMessage"></span>
    <button onclick="closeNotification()" style="background:transparent; border:none; color:#fff; font-size:20px; font-weight:bold; cursor:pointer; margin-left:15px; padding:0; line-height:1; width:20px; height:20px; display:flex; align-items:center; justify-content:center;">×</button>
  </div>

  <!-- Main Clients Table View -->

  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 id="clientPageTitle" style="margin:0; font-size:18px; font-weight:600;">
          @if($filter == "ids_expired")
             Expired IDs
          @elseif($filter == "birthday_today")
             Birthdays Today
          @else
             Clients
            <span id="followUpLabel" style="display:{{ request()->get('follow_up') == 'true' && !request()->get('client_id') ? 'inline' : 'none' }}; color:#f3742a; font-size:16px; font-weight:500;"> - To Follow Up</span>
            <span class="client-name" id="clientPageName" style="color:#f3742a; font-size:16px; font-weight:500;"></span>
          @endif
          </h3>

      </div>
    </div>
   
  <div class="clients-table-view" id="clientsTableView" @if(request()->has('client_id') && request()->client_id) style="display:none;" @endif>
  <div class="container-table">
    <!-- Clients Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
                <div class="records-found">Records Found - {{ $clients->total() }}</div>

      <div class="page-title-section">
        <div style="display:flex; align-items:center; gap:15px;">
          @if($filter != "ids_expired" &&  $filter != "birthday_today"  )
            <div class="filter-group">
              <label class="toggle-switch">
                <input type="checkbox" id="filterToggle" {{ request()->get('follow_up') == 'true' ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
              </label>
              <label for="filterToggle" style="font-size:14px; color:#2d2d2d; margin:0; cursor:pointer; user-select:none;">Filter</label>
            </div>
            @if(request()->get('follow_up') == 'true')
              <button class="btn btn-list-all" id="listAllBtn">List ALL</button>
            @else
              <button class="btn btn-follow-up" id="followUpBtn">To Follow Up</button>
            @endif
          @endif
        </div>
      </div>
      <div class="action-buttons">
          @if($filter != "ids_expired"  &&  $filter != "birthday_today" )
            <button class="btn btn-add" id="addClientBtn">Add</button>
          @endif
          @if(request()->has('from_calendar') && request()->from_calendar == '1')
            <button class="btn btn-back" onclick="window.location.href='/calendar?filter=birthdays'">Back</button>
          @else
            <button class="btn btn-close" onclick="window.history.back()">Close</button>
          @endif
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="clientsTable">
        <thead>
          <tr>
            <th style="text-align:center;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle;">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 2 16 2 16H22C22 16 19 14.25 19 9C19 5.13 15.87 2 12 2Z" fill="#fff" stroke="#fff" stroke-width="1.5"/>
                <path d="M9 21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21H9Z" fill="#fff"/>
              </svg>
            </th>
            <th>Action</th>
            @php
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
                'children' => ['label' => 'Children', 'filter' => false],
                'children_details' => ['label' => 'Children Details', 'filter' => false],
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
                'pic' => ['label' => 'PIC', 'filter' => false],
                'industry' => ['label' => 'Industry', 'filter' => false],
              ];
            @endphp
            @foreach($selectedColumns as $col)
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">
                  {{ $columnDefinitions[$col]['label'] }}
                  @if($columnDefinitions[$col]['filter'])
                    <input type="text" class="column-filter" data-column="{{ $col }}" placeholder="Filter {{ $columnDefinitions[$col]['label'] }}..." style="width:100%; margin-top:4px; padding:4px 6px; font-size:11px; border:1px solid #666; background:#000; color:#fff; border-radius:2px; transition:all 0.2s;">
                  @endif
                </th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($clients as $client)
            <tr class="{{ $client->status === 'Inactive' ? 'inactive-row' : '' }} {{ $client->hasExpired ?? false ? 'has-expired' : ($client->hasExpiring ?? false ? 'has-expiring' : '') }}">
              <td class="bell-cell {{ $client->hasExpired ?? false ? 'expired' : ($client->hasExpiring ?? false ? 'expiring' : '') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  @php
                    $isExpired = $client->hasExpired ?? false;
                    $isExpiring = $client->hasExpiring ?? false;
                  @endphp
                  <div class="status-indicator {{ $isExpired ? 'expired' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid #000; background-color:{{ $isExpired ? '#dc3545' : 'transparent' }};"></div>
                </div>
              </td>
              <td class="action-cell">
                <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openClientDetailsModal({{ $client->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
                <svg class="action-clock" onclick="window.location.href='{{ route('clients.index') }}?follow_up=true'" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <circle cx="12" cy="12" r="9" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 7V12L15 15" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'client_name')
                  <td data-column="client_name">
                   {{ $client->client_name }}
                  </td>
                @elseif($col == 'client_type')
                  <td data-column="client_type">{{ $client->client_type }}</td>
                @elseif($col == 'nin_bcrn')
                  <td data-column="nin_bcrn">{{ $client->nin_bcrn ?? '##########' }}</td>
                @elseif($col == 'dob_dor')
                  <td data-column="dob_dor">{{ $client->dob_dor ? $client->dob_dor->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'mobile_no')
                  <td data-column="mobile_no">{{ $client->mobile_no }}</td>
                @elseif($col == 'wa')
                  <td data-column="wa" class="checkbox-cell">
                    <input type="checkbox" {{( $client->wa=='1' ||  $client->wa==1 )? 'checked' : '' }} style="cursor:pointer;" onchange="updateClientWA({{ $client->id }}, this.checked)">
                  </td>
                @elseif($col == 'district')
                  <td data-column="district">{{ $client->districts?->name ?? '-' }}</td>
                @elseif($col == 'occupation')
                  <td data-column="occupation">{{ $client->occupations?->name ?? '-' }}</td>
                @elseif($col == 'source')
                  <td data-column="source">{{ $client->sources?->name }}</td>
                @elseif($col == 'status')
                  <td data-column="status">{{ $client->status == 'Inactive' ? 'Dormant' : ($client->status == 'Active' ? 'Active' : $client->status) }}</td>
                @elseif($col == 'signed_up')
                  <td data-column="signed_up">{{ $client->signed_up ? $client->signed_up->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'employer')
                  <td data-column="employer">{{ $client->employer ?? '-' }}</td>
                @elseif($col == 'clid')
                  <td data-column="clid">{{ $client->clid }}</td>
                @elseif($col == 'contact_person')
                  <td data-column="contact_person">{{ $client->contact_person ?? '-' }}</td>
                @elseif($col == 'income_source')
                  <td data-column="income_source">{{ $client->income_sources?->name ?? '-' }}</td>
                @elseif($col == 'married')
                  <td data-column="married">{{ $client->married ? 'Yes' : 'No' }}</td>
                @elseif($col == 'spouses_name')
                  <td data-column="spouses_name">{{ $client->spouses_name ?? '-' }}</td>
                @elseif($col == 'children')
                  <td data-column="children">{{ $client->children ?? '-' }}</td>
                @elseif($col == 'children_details')
                  <td data-column="children_details">{{ $client->children_details ?? '-' }}</td>
                @elseif($col == 'alternate_no')
                  <td data-column="alternate_no">{{ $client->alternate_no ?? '-' }}</td>
                @elseif($col == 'email_address')
                  <td data-column="email_address">{{ $client->email_address ?? '-' }}</td>
                @elseif($col == 'location')
                  <td data-column="location">{{ $client->location ?? '-' }}</td>
                @elseif($col == 'island')
                  <td data-column="island">{{ $client->islands?->name ?? '-' }}</td>
                @elseif($col == 'country')
                  <td data-column="country">{{ $client->countries?->name ?? '-' }}</td>
                @elseif($col == 'po_box_no')
                  <td data-column="po_box_no">{{ $client->po_box_no ?? '-' }}</td>
                @elseif($col == 'pep')
                  <td data-column="pep">{{ $client->pep ? 'Yes' : 'No' }}</td>
                @elseif($col == 'pep_comment')
                  <td data-column="pep_comment">{{ $client->pep_comment ?? '-' }}</td>
                @elseif($col == 'image')
                  <td data-column="image">{{ $client->image ? '📷' : '-' }}</td>
                @elseif($col == 'salutation')
                  <td data-column="salutation">{{ $client->salutations?->name ?? '-' }}</td>
                @elseif($col == 'first_name')
                  <td data-column="first_name">{{ $client->first_name }}</td>
                @elseif($col == 'other_names')
                  <td data-column="other_names">{{ $client->other_names ?? '-' }}</td>
                @elseif($col == 'surname')
                  <td data-column="surname">{{ $client->surname }}</td>
                @elseif($col == 'passport_no')
                  <td data-column="passport_no">{{ $client->passport_no ?? '-' }}</td>
                @elseif($col == 'pic')
                  <td data-column="pic">{{ $client->pic ?? '-' }}</td>
                @elseif($col == 'industry')
                  <td data-column="industry">{{ $client->industry ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

      <div class="footer" style="background:#fff; border-top:1px solid #ddd; margin-top:0;">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('clients.export', array_merge(request()->query(), ['page' => $clients->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button">Column</button>
        <button class="btn btn-export" id="printBtn" type="button" style="margin-left:10px;">Print</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $clients->currentPage();
          $last = max(1,$clients->lastPage());
          function page_url($base,$q,$p){ $params = array_merge($q,['page'=>$p]); return $base . '?' . http_build_query($params); }
        @endphp

        <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,1) : '#' }}" @if($current<=1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,$current-1) : '#' }}" @if($current<=1) disabled @endif>&lsaquo;</a>
        <span class="page-info">Page {{ $current }} of {{ $last }}</span>
        <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$current+1) : '#' }}" @if($current>= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$last) : '#' }}" @if($current>=$last) disabled @endif>&raquo;</a>
      </div>
    </div>
  </div>
  </div>
</div>  
  <!-- Client Page View (Full Page) -->
  <div class="client-page-view @if(request()->has('client_id') && request()->client_id) show @endif" id="clientPageView">
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Client Details View -->
        <div id="clientDetailsPageContent" @if(request()->has('client_id') && request()->client_id) style="display:block;" @else style="display:none;" @endif>
      
          <!-- Client Details Card -->
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-nav" style="display:flex; gap:8px;">
                 <button class="nav-tab active" data-tab="life-proposals" data-url="{{ route('life-proposals.index') }}" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Proposals</button>
                <button class="nav-tab" data-tab="policies" data-url="{{ route('policies.index') }}" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Policies</button>
                <button class="nav-tab" data-tab="payments" data-url="{{ route('payments.index') }}" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Payments</button>
                <button class="nav-tab" data-tab="vehicles" data-url="{{ route('vehicles.index') }}" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Vehicles</button>
                <button class="nav-tab" data-tab="claims" data-url="{{ route('claims.index') }}" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Claims</button>
                <button class="nav-tab" data-tab="documents" data-url="{{ route('documents.index') }}" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Documents</button>
                <button class="nav-tab" data-tab="bos" data-url="{{ route('beneficial-owners.index') }}" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">BOs</button>
              </div>
              <div class="client-page-actions" style="display:flex; gap:8px;">
                <button class="btn btn-edit" id="editClientFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Edit</button>
                <button class="btn" onclick="closeClientPageView()" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
              </div>
            </div>
          
            <div id="clientDetailsContent" style="padding:12px; background:#f5f5f5;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>

          <!-- Documents Card -->
          <div id="clientDocumentsSection" style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:15px;">
            <h4 style="font-weight:bold; margin-bottom:10px; color:#000; font-size:13px;">Documents</h4>
            <div id="clientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
              <!-- Documents will be loaded here -->
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
              <input type="file" id="photoUploadInput" accept="image/*" style="display:none;" onchange="handlePhotoUpload(event)">
              <button class="btn" onclick="document.getElementById('photoUploadInput').click()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Photo</button>
              <button id="addDocumentBtn1" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Add Document</button>
            </div>
          </div>
        </div>
        
        <!-- Client Edit/Add Form -->
        <div id="clientFormPageContent" style="display:none;">
          <!-- Client Form Card -->
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="clientDeleteBtn" style="display:none; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteClient()">Delete</button>
                <button type="submit" form="clientForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" onclick="closeClientPageView()" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
              </div>
            </div>
            
            <form id="clientForm" method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" novalidate>
              @csrf
              <div id="clientFormMethod" style="display:none;"></div>
              <div id="formContentDiv" style="padding:12px; overflow-x: auto;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
          
          <!-- Documents Card (will be cloned from modal) -->
          <div id="editFormDocumentsSection" style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:15px; display:none;">
            <!-- Documents section will be cloned here -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- Add/Edit Client Modal (hidden, used for form structure) -->
  <div class="modal" id="clientModal">
    <div class="modal-content" style="max-width:95%; width:1400px; max-height:95vh; overflow-y:auto;">
      <form id="clientForm" method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <div id="clientFormMethod" style="display:none;"></div>
        
        <div class="modal-header" style="background:#fff; color:#000; padding:12px 15px; display:flex; justify-content:flex-end; align-items:center; border-bottom:1px solid #ddd;">
          <div style="display:flex; gap:8px;">
            <button type="button" class="btn-delete" id="clientDeleteBtn2" style="display:none; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteClient()">Delete</button>
            <button type="submit" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
            <button type="button" class="modal-close" onclick="closeClientModal()" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
          </div>
        </div>


        <div class="modal-body" style="background:#f5f5f5; padding:12px; overflow-x: auto; overflow-y: auto; max-height: calc(95vh - 60px);" >
          <!-- ROW 1: 4 Sections -->
          <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; margin-bottom:10px;">

            <!-- Section 1: CUSTOMER DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">CUSTOMER DETAILS</div>
              <div style="padding:10px;">
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:90px; border:none !important; background:#fff !important;"><label style="font-size:10px;">Client Type</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="client_type" name="client_type" class="form-control" required style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @if(isset($lookupData['client_types']))
                          @foreach($lookupData['client_types'] as $clientType)
                            <option value="{{ $clientType }}" {{ $clientType === 'Individual' ? 'selected' : '' }}>{{ $clientType }}</option>
                          @endforeach
                        @else
                          <option value="Individual" selected>Individual</option>
                          <option value="Business">Business</option>
                          <option value="Company">Company</option>
                          <option value="Organization">Organization</option>
                        @endif
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">DOB/DOR</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:5px;">
                        <input type="date" id="dob_dor" name="dob_dor" class="form-control" style="flex:1; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <input type="text" id="dob_age" readonly style="width:40px; padding:4px; border:1px solid #ccc; font-size:10px; text-align:center; background:#f9f9f9;">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">NIN/BCRN</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="nin_bcrn" name="nin_bcrn" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">ID Expiry Date</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:5px;">
                        <input type="date" id="id_expiry_date" name="id_expiry_date" class="form-control" style="flex:1; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <input type="text" id="id_expiry_days" readonly style="width:40px; padding:4px; border:1px solid #ccc; font-size:10px; text-align:center; background:#f9f9f9;">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Client Status</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="status" name="status" class="form-control" required style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @if(isset($lookupData['client_statuses']))
                          @foreach($lookupData['client_statuses'] as $status)
                            <option value="{{ $status }}" {{ $status === 'Active' ? 'selected' : '' }}>{{ $status }}</option>
                          @endforeach
                        @else
                          <option value="Active" selected>Active</option>
                          <option value="Inactive">Inactive</option>
                          <option value="Suspended">Suspended</option>
                          <option value="Pending">Pending</option>
                          <option value="Expired">Expired</option>
                        @endif
                      </select>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Section 2: CONTACT DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">CONTACT DETAILS</div>
              <div style="padding:10px;">
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:90px; border:none !important; background:#fff !important;"><label style="font-size:10px;">Mobile No</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:5px; align-items:center;">
                        <input type="text" id="mobile_no" name="mobile_no" class="form-control" required style="flex:1; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <input type="checkbox" id="wa" name="wa" value="1" style="width:18px; height:18px;">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Contact No</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="contact_no" name="contact_no" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Home No</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="home_no" name="home_no" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Email Address</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="email" id="email_address" name="email_address" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Contact Person</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="contact_person" name="contact_person" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Section 3: ADDRESS DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">ADDRESS DETAILS</div>
              <div style="padding:10px;">
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:90px; border:none !important; background:#fff !important;"><label style="font-size:10px;">District</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="district" name="district" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @foreach($lookupData['districts'] as $d) <option value="{{ $d['id'] }}">{{ $d['name'] }}</option> @endforeach
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Address</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="location" name="location" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Island</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="island" name="island" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @foreach($lookupData['islands'] as $is) <option value="{{ $is['id'] }}">{{ $is['name'] }}</option> @endforeach
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Country</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="country" name="country" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @foreach($lookupData['countries'] as $c) <option value="{{ $c['id'] }}">{{ $c['name'] }}</option> @endforeach
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">P.O. Box No</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="po_box_no" name="po_box_no" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Section 4: REGISTRATION DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">REGISTRATION DETAILS</div>
              <div style="padding:10px;">
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:90px; border:none !important; background:#fff !important;"><label style="font-size:10px;">Sign Up Date</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:5px;">
                        <input type="date" id="signed_up" name="signed_up" class="form-control" required style="flex:1; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <input type="text" id="signed_up_years" readonly style="width:40px; padding:4px; border:1px solid #ccc; font-size:10px; text-align:center; background:#f9f9f9;">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Source</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="source" name="source" class="form-control" required style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @foreach($lookupData['sources'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Source Name</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="source_name" name="source_name" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">PC Channel</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="pc_channel" name="pc_channel" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Agency</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="agency" name="agency" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @foreach($lookupData['agencies'] as $a) <option value="{{ $a['id'] }}">{{ $a['name'] }}</option> @endforeach
                      </select>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <!-- ROW 2: 4 Sections -->
          <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; margin-top:10px;">

            <!-- Section 5: INDIVIDUAL DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">INDIVIDUAL DETAILS</div>
              <div style="padding:10px;">
                <!-- Salutation & First Name with Photo -->
                <div style="display:flex; gap:8px; margin-bottom:4px;">
                  <div style="flex:1; min-width:0;">
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                      <tr>
                        <td style="padding:4px 0; width:75px; border:none !important; background:#fff !important;"><label style="font-size:10px;">Salutation</label></td>
                        <td style="padding:4px 0; border:none !important; background:#fff !important;">
                          <select id="salutation" name="salutation" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#fff;">
                            <option value="">Select</option>
                            @foreach($lookupData['salutations'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">First Name</label></td>
                        <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="first_name" name="first_name" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#fff;"></td>
                      </tr>
                    </table>
                  </div>
                  <!-- Photo column -->
                  <div style="width:55px; flex-shrink:0;">
                    <label for="image" style="display:block; cursor:pointer;">
                      <div id="clientPhotoPreview" style="width:55px; height:55px; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; background:#fff; cursor:pointer; overflow:hidden;">
                        <img id="clientPhotoImg" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                        <span id="clientPhotoText" style="font-size:8px; color:#999;">Photo</span>
                      </div>
                    </label>
                  </div>
                </div>
                <!-- Other Names, Surname, Passport No - Full Width -->
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:75px; border:none !important; background:#fff !important;"><label style="font-size:10px;">Other Names</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="other_names" name="other_names" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#fff;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Surname</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="surname" name="surname" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#fff;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Passport No</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:5px;">
                        <input type="text" id="passport_country" name="passport_country" class="form-control" placeholder="SEY" style="width:45px; padding:4px 6px; border:1px solid #ccc; font-size:10px; text-align:center; background:#fff;">
                        <input type="text" id="passport_no" name="passport_no" class="form-control" style="flex:1; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#fff;">
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Section 6: INCOME DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">INCOME DETAILS</div>
              <div style="padding:10px;">
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:90px; border:none !important; background:#fff !important;"><label style="font-size:10px;">Occupation</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="occupation" name="occupation" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @foreach($lookupData['occupations'] as $o) <option value="{{ $o['id'] }}">{{ $o['name'] }}</option> @endforeach
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Income Source</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <select id="income_source" name="income_source" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <option value="">Select</option>
                        @foreach($lookupData['income_sources'] as $i) <option value="{{ $i['id'] }}">{{ $i['name'] }}</option> @endforeach
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Employer</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="employer" name="employer" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Monthly Income</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="monthly_income" name="monthly_income" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Savings Budget</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="savings_budget" name="savings_budget" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Section 7: FAMILY DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">FAMILY DETAILS</div>
              <div style="padding:10px;">
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:90px; border:none !important; background:#fff !important;"><label style="font-size:10px;">Married</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="checkbox" id="married" name="married" value="1" style="width:18px; height:18px;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Spouse's Name</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="spouses_name" name="spouses_name" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Children</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:5px; align-items:center;">
                        <input type="checkbox" id="has_children" style="width:18px; height:18px;">
                        <input type="number" id="children" name="children" class="form-control" style="flex:1; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">Details</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><textarea id="children_details" name="children_details" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9; min-height:50px; resize:vertical;"></textarea></td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Section 8: OTHER DETAILS -->
            <div style="border:1px solid #ddd; overflow:hidden; background:#fff; min-width:0;">
              <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">OTHER DETAILS</div>
              <div style="padding:10px;">
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; min-width:auto !important;">
                  <tr>
                    <td style="padding:4px 0; width:80px; border:none !important; background:#fff !important;"><label style="font-size:10px;">PEP</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <input type="checkbox" id="pep" name="pep" value="1" style="width:18px; height:18px;">
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="padding:4px 0; border:none !important; background:#fff !important;"><input type="text" id="pep_comment" name="pep_comment" placeholder="PEP Details" class="form-control" style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;"></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="padding:8px 0 4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                        <label style="font-size:10px; display:flex; align-items:center; gap:4px;">
                          Vehicle <input type="checkbox" id="has_vehicle" name="has_vehicle" value="1" style="width:16px; height:16px;">
                        </label>
                        <label style="font-size:10px; display:flex; align-items:center; gap:4px;">
                          House <input type="checkbox" id="has_house" name="has_house" value="1" style="width:16px; height:16px;">
                        </label>
                        <label style="font-size:10px; display:flex; align-items:center; gap:4px;">
                          Business <input type="checkbox" id="has_business" name="has_business" value="1" style="width:16px; height:16px;">
                        </label>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;"><label style="font-size:10px;">ID Expiry Date</label></td>
                    <td style="padding:4px 0; border:none !important; background:#fff !important;">
                      <div style="display:flex; gap:5px;">
                        <input type="date" id="id_expiry_date2" class="form-control" style="flex:1; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
                        <input type="text" id="id_expiry_days2" readonly style="width:40px; padding:4px; border:1px solid #ccc; font-size:10px; text-align:center; background:#f9f9f9;">
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <!-- Hidden fields -->
          <input type="file" id="image" name="image" accept="image/*" style="display:none;" onchange="handleImagePreview(event)">

          <!-- Documents Section -->
          <div style="margin-top:15px; padding:15px; background:#f5f5f5; border:1px solid #ddd;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <h4 style="margin:0; font-size:14px; font-weight:bold;">Documents</h4>
              <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button id="uploadPhotoBtn" type="button" class="btn" onclick="document.getElementById('image').click()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Upload Photo</button>
                <button id="addDocumentBtn2" type="button" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Add Document</button>
              </div>
            </div>
            <div id="editClientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; min-height:60px;">
              <!-- Documents will appear here -->
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal" id="clientDetailsModal">
    <div class="modal-content" style="max-width:95%; width:1400px; max-height:95vh; overflow-y:auto;">
      <div class="modal-header" style="background:#fff; color:#000; padding:12px 15px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd;">
        <div style="display:flex; gap:8px;">
          <button class="nav-tab" data-tab="proposals" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Proposals</button>
          <button class="nav-tab" data-tab="policies" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Policies</button>
          <button class="nav-tab" data-tab="payments" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Payments</button>
          <button class="nav-tab" data-tab="vehicles" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Vehicles</button>
          <button class="nav-tab" data-tab="claims" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Claims</button>
          <button class="nav-tab active" data-tab="documents" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:12px;">Documents</button>
        </div>
        <div style="display:flex; gap:8px;">
          <button class="btn btn-edit" id="editClientFromModalBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Edit</button>
          <button class="modal-close" onclick="closeClientDetailsModal()" style="background:#808080; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
        </div>
      </div>
      <div class="modal-body" style="background:#f5f5f5; padding:12px; overflow-x: auto;">
        <div id="clientDetailsContentModal" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; align-items:start; min-width:1200px;">
          <!-- Content will be loaded via JavaScript -->
        </div>
        <div id="clientDocumentsSection" style="margin-top:15px; padding-top:12px; border-top:2px solid #ddd; background:#f5f5f5;">
          <h4 style="font-weight:bold; margin-bottom:10px; color:#000; font-size:13px; padding:0 12px;">Documents</h4>
          <div id="clientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px; padding:0 12px;">
            <!-- Documents will be loaded here -->
          </div>
          <div style="display:flex; gap:10px; justify-content:flex-end; padding:0 12px 12px;">
            <input type="file" id="photoUploadInput" accept="image/*" style="display:none;" onchange="handlePhotoUpload(event)">
            <button class="btn" onclick="document.getElementById('photoUploadInput').click()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Photo</button>
            <button id="addDocumentBtn3" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px; display:none;">Add Document</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Document Upload Modal -->
  <div class="modal" id="documentUploadModal">
    <div class="modal-content" style="max-width:600px;">
      <div class="modal-header">
        <h4>Add Document</h4>
        <button type="button" class="modal-close" onclick="closeDocumentUploadModal()">×</button>
      </div>
      <div class="modal-body">
        <form id="documentUploadForm">
          <div class="form-group" style="margin-bottom:15px;">
            <label for="documentType" style="display:block; margin-bottom:5px; font-weight:600;">Document Type</label>
            <select id="documentType" name="document_type" class="form-control" required>
              <option value="">Select Document Type</option>
              <option value="id_document">ID Card</option>
              <option value="poa_document">Proof Of Address</option>
              <option value="other">Other Document</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label for="documentFile" style="display:block; margin-bottom:5px; font-weight:600;">Select File</label>
            <input type="file" id="documentFile" name="document" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required onchange="previewDocument(event)">
            <small style="color:#666; font-size:11px;">Accepted formats: JPG, PNG, PDF, DOC, DOCX (Max 5MB)</small>
          </div>
          <div id="documentPreviewContainer" style="display:none; margin-top:15px; padding:15px; border:1px solid #ddd; border-radius:4px; background:#f9f9f9;">
            <div style="font-weight:600; margin-bottom:10px; font-size:13px;">Preview:</div>
            <div id="documentPreviewContent" style="display:flex; align-items:center; justify-content:center; min-height:200px;">
              <!-- Preview will be shown here -->
            </div>
            <div id="documentPreviewInfo" style="margin-top:10px; font-size:12px; color:#666;">
              <!-- File info will be shown here -->
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeDocumentUploadModal()">Cancel</button>
        <button type="button" class="btn-save" onclick="handleDocumentUpload()">Upload</button>
      </div>
    </div>
  </div>

  <!-- Photo Preview Modal -->
  <div class="modal" id="photoPreviewModal">
    <div class="modal-content" style="max-width:600px;">
      <div class="modal-header">
        <h4>Client Photo</h4>
        <button type="button" class="modal-close" onclick="closeClientPhotoPreviewModal()">×</button>
      </div>
      <div class="modal-body" style="text-align:center; padding:20px;">
        <img id="photoPreviewImg" src="" alt="Client Photo" style="max-width:100%; max-height:70vh; border:1px solid #ddd; border-radius:4px;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-back" onclick="closeClientPhotoPreviewModal()">Close</button>
      </div>
    </div>
  </div>

  <!-- Column Selection Modal -->
  <div class="modal" id="columnModal">
    <div class="modal-content column-modal-vertical">
      <div class="modal-header">
        <h4>Column Select & Sort</h4>
        <div class="modal-header-buttons">
          <button class="btn-save-orange" onclick="saveColumnSettings()">Save</button>
          <button class="btn-cancel-gray" onclick="closeColumnModal()">Cancel</button>
        </div>
      </div>
      <div class="modal-body">
        <form id="columnForm" action="{{ route('clients.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection-vertical" id="columnSelection">
            @php
              $all = [
                'client_name'=>'Client Name',
                'client_type'=>'Client Type',
                'nin_bcrn'=>'NIN/BCRN',
                'dob_dor'=>'DOB/DOR',
                'mobile_no'=>'MobileNo',
                'wa'=>'WA',
                'district'=>'District',
                'occupation'=>'Occupation',
                'source'=>'Source',
                'status'=>'Status',
                'signed_up'=>'Signed Up',
                'employer'=>'Employer',
                'clid'=>'CLID',
                'contact_person'=>'Contact Person',
                'income_source'=>'Income Source',
                'married'=>'Married',
                'spouses_name'=>'Spouses Name',
                'children'=>'Children',
                'children_details'=>'Children Details',
                'alternate_no'=>'Alternate No',
                'email_address'=>'Email Address',
                'location'=>'Location',
                'island'=>'Island',
                'country'=>'Country',
                'po_box_no'=>'P.O. Box No',
                'pep'=>'PEP',
                'pep_comment'=>'PEP Comment',
                'image'=>'Image',
                'salutation'=>'Salutation',
                'first_name'=>'First Name',
                'other_names'=>'Other Names',
                'surname'=>'Surname',
                'passport_no'=>'Passport No',
                'pic'=>'PIC',
                'industry'=>'Industry'
              ];
              $ordered = [];
              foreach($selectedColumns as $col) {
                if(isset($all[$col])) {
                  $ordered[$col] = $all[$col];
                  unset($all[$col]);
                }
              }
              $ordered = array_merge($ordered, $all);
            @endphp

            @php
              $mandatoryFields = ['client_name', 'client_type', 'mobile_no', 'source', 'status', 'signed_up', 'clid', 'first_name', 'surname'];
              $counter = 1;
            @endphp
            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryFields);
                $isChecked = in_array($key, $selectedColumns) || $isMandatory;
              @endphp
              <div class="column-item-vertical" draggable="true" data-column="{{ $key }}">
                <span class="column-number">{{ $counter }}</span>
                <label class="column-label-wrapper">
                  <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
                  <span class="column-label-text">{{ $label }}</span>
                </label>
              </div>
              @php $counter++; @endphp
            @endforeach
          </div>
          <div class="column-drag-hint">Drag and Select to position and display</div>
        </form>
      </div>
    </div>
  </div>

</div>



<script>
  // Initialize data from Blade
  let currentClientId = null;
  const lookupData = @json($lookupData ?? []);
  const selectedColumns = @json($selectedColumns ?? []);
  const clientsIndexRoute = '{{ route("clients.index") }}';
  const clientsStoreRoute = '{{ route("clients.store") }}';
  const csrfToken = '{{ csrf_token() }}';
  const clientsTotal = {{ $clients->total() }};
</script>
<script src="{{ asset('js/clients-index.js') }}"></script>
<script>
  // Auto-open client details if client_id is in URL
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const clientIdFromUrl = urlParams.get('client_id');
    if (clientIdFromUrl && typeof openClientDetailsModal === 'function') {
      openClientDetailsModal(clientIdFromUrl);
    }
  });
</script>
@endsection
