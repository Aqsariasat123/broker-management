@extends('layouts.app')
@section('content')
@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/clients-index.css') }}">

@php
  $selectedColumns = session('client_columns', [
    'client_name','client_type','nin_bcrn','dob_dor','mobile_no','wa','district','occupation','source','status','signed_up',
    'employer','clid','contact_person','income_source','married','spouses_name','alternate_no','email_address','location',
    'island','country','po_box_no','pep','pep_comment','image','salutation','first_name','other_names','surname','passport_no'
  ]);
@endphp

<div class="dashboard">
  <!-- Success/Error Notification Banner -->
  <div id="notificationBanner" style="display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%); z-index:10000; background:#28a745; color:#fff; padding:12px 24px; border-radius:4px; box-shadow:0 4px 6px rgba(0,0,0,0.1); font-size:14px; font-weight:500; max-width:500px; text-align:center; align-items:center; justify-content:center;">
    <span id="notificationMessage"></span>
    <button onclick="closeNotification()" style="background:transparent; border:none; color:#fff; font-size:20px; font-weight:bold; cursor:pointer; margin-left:15px; padding:0; line-height:1; width:20px; height:20px; display:flex; align-items:center; justify-content:center;">×</button>
  </div>

  <!-- Main Clients Header -->
  <div id="clientsPageHeader" style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
          @if($filter == "ids_expired")
             Expired IDs
          @elseif($filter == "birthday_today")
             Birthdays Today
          @elseif($filter == "birthdays")
             Birthday List - {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('F') : now()->format('F') }}
          @else
             Clients
            <span id="followUpLabel" style="display:{{ request()->get('follow_up') == 'true' && !request()->get('client_id') ? 'inline' : 'none' }}; color:#f3742a; font-size:16px; font-weight:500;"> - To Follow Up</span>
          @endif
          </h3>
          @include('partials.page-header-right')
      </div>
    </div>
   
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Clients Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
        <div class="records-found">Records Found - {{ $clients->total() }}</div>

      <div class="page-title-section">
        <div style="display:flex; align-items:center; gap:15px;">
          @if($filter != "ids_expired" && $filter != "birthday_today" && $filter != "birthdays")
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
          @if($filter != "ids_expired" && $filter != "birthday_today" && $filter != "birthdays")
            <button class="btn btn-add" id="addClientBtn">Add</button>
          @endif
          <button class="btn btn-close" onclick="window.history.back()">Close</button>
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
            @foreach($selectedColumns as $col)
               <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th> 
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
                  <td data-column="{{ $col }}">{{ $client->$col }}</td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
      <div class="footer-left" style="display:flex; gap:8px;">
        <a class="btn btn-export" href="{{ route('clients.export', array_merge(request()->query(), ['page' => $clients->currentPage()])) }}" style="background:#fff; border:1px solid #ddd; padding:6px 16px; border-radius:2px; cursor:pointer; text-decoration:none; color:#333;">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button" style="background:#fff; border:1px solid #ddd; padding:6px 16px; border-radius:2px; cursor:pointer;">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $clients->currentPage();
          $last = max(1, $clients->lastPage());
          function clients_page_url($base, $q, $p) {
            $params = array_merge($q, ['page' => $p]);
            return $base . '?' . http_build_query($params);
          }
        @endphp

        <a class="btn-page" href="{{ $current > 1 ? clients_page_url($base, $q, 1) : '#' }}" @if($current <= 1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current > 1 ? clients_page_url($base, $q, $current - 1) : '#' }}" @if($current <= 1) disabled @endif>&lsaquo;</a>

        <span style="padding:0 8px;">Page {{ $current }} of {{ $last }}</span>

        <a class="btn-page" href="{{ $current < $last ? clients_page_url($base, $q, $current + 1) : '#' }}" @if($current >= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current < $last ? clients_page_url($base, $q, $last) : '#' }}" @if($current >= $last) disabled @endif>&raquo;</a>
      </div>
    </div>
  </div>
  </div>
</div>  

<!-- ============================================================================ -->
<!-- CLIENT PAGE VIEW - COMPLETE FIXED VERSION -->
<!-- ============================================================================ -->
<div class="client-page-view" id="clientPageView" style="display:none;">
  <!-- Header with Title and Buttons -->
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:15px 20px; margin-bottom:10px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h3 id="clientPageTitle" style="margin:0; font-size:18px; font-weight:600;">
        Client
        <span id="clientPageName" style="color:#f3742a; font-size:16px;"></span>
      </h3>
      <div style="display:flex; gap:8px;">
        <button id="editClientFromPageBtn" onclick="openEditClient(currentClientId)" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Edit</button>
        <button onclick="closeClientPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
  </div>

  <!-- Nav Tabs -->
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:12px 15px; margin-bottom:15px;">
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
      <button class="nav-tab active" data-tab="details" data-url="#" style="background:#f3742a; color:#fff; border:none; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Details</button>
      <button class="nav-tab" data-tab="proposals" data-url="/life-proposals" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Proposals</button>
      <button class="nav-tab" data-tab="policies" data-url="/policies" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Policies</button>
      <button class="nav-tab" data-tab="payments" data-url="/payment-plans" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Payments</button>
      <button class="nav-tab" data-tab="vehicles" data-url="/vehicles" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Vehicles</button>
      <button class="nav-tab" data-tab="claims" data-url="/claims" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Claims</button>
      <button class="nav-tab" data-tab="documents" data-url="#" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Documents</button>
    </div>
  </div>

  <!-- Details Content -->
  <div id="clientDetailsPageContent" style="display:block; background:#fff; border:1px solid #ddd; border-radius:4px; padding:20px;">
    <div id="clientDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
      <!-- Content loaded by JavaScript -->
    </div>
    
    <!-- Documents Section -->
    <div style="margin-top:20px; border-top:2px solid #ddd; padding-top:15px;">
      <h4 style="font-weight:bold; margin-bottom:10px; font-size:14px;">Documents</h4>
      <div id="clientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
        <!-- Documents loaded by JS -->
      </div>
      <div style="display:flex; gap:10px; justify-content:flex-end;">
        <input type="file" id="photoUploadInput" accept="image/*" style="display:none;" onchange="handlePhotoUpload(event)">
        <button class="btn" onclick="document.getElementById('photoUploadInput').click()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Upload Photo</button>
        <button id="addDocumentBtn1" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Upload Document</button>
      </div>
    </div>
  </div>

  <!-- Form Content (for editing) -->
  <div id="clientFormPageContent" style="display:none; background:#fff; border:1px solid #ddd; border-radius:4px; padding:20px;">
    <form id="clientEditFormPage" method="POST" enctype="multipart/form-data">
      @csrf
      <div id="clientEditFormMethod"></div>
      
      <div style="padding:12px;">
        <div id="clientEditFormGrid"></div>
        
        <div id="editFormDocumentsSection" style="margin-top:20px; border-top:2px solid #ddd; padding-top:10px; display:none;">
          <h4 style="font-weight:bold; font-size:16px;">Documents</h4>
          <div id="editClientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; min-height:50px;">
            <!-- Docs loaded via JS -->
          </div>
          <div style="text-align:right;">
            <button type="button" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px;">+ Add Document</button>
          </div>
        </div>
      </div>
      
      <div style="display:flex; justify-content:flex-end; gap:8px; padding:12px; border-top:1px solid #ddd; margin-top:20px;">
        <button type="button" class="btn-delete" id="clientDeleteBtnPage" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteClient()">Delete</button>
        <button type="submit" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
        <button type="button" class="btn-cancel" onclick="closeClientPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Cancel</button>
      </div>
    </form>
  </div>
</div>

<style>
.nav-tab {
  transition: all 0.2s;
}
.nav-tab:hover {
  background: #f5f5f5 !important;
  color: #000 !important;
}
.nav-tab.active {
  background: #f3742a !important;
  color: #fff !important;
}
.detail-section {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
}
.detail-section-header {
  background: #000;
  color: #fff;
  padding: 8px 12px;
  font-size: 11px;
  font-weight: 600;
}
.detail-section-body {
  padding: 12px;
}
.detail-row {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  gap: 8px;
}
.detail-label {
  font-size: 11px;
  color: #666;
  min-width: 100px;
  font-weight: 600;
}
.detail-value {
  flex: 1;
  font-size: 11px;
  color: #000;
  padding: 4px 6px;
  border: 1px solid #ddd;
  border-radius: 2px;
  background: #fff;
  min-height: 22px;
  display: flex;
  align-items: center;
}
.detail-photo {
  width: 80px;
  height: 100px;
  object-fit: cover;
  border: 1px solid #ddd;
  border-radius: 2px;
  cursor: pointer;
}
.badge-status {
  padding: 3px 8px;
  border-radius: 2px;
  color: #fff;
  font-size: 10px;
  font-weight: 600;
}
</style>

<script>
function closeClientPageView() {
  const pageView = document.getElementById('clientPageView');
  if (pageView) pageView.style.display = 'none';

  const tableView = document.getElementById('clientsTableView');
  if (tableView) {
    tableView.classList.remove('hidden');
    tableView.style.display = 'block';
  }

  const pageHeader = document.getElementById('clientsPageHeader');
  if (pageHeader) pageHeader.style.display = 'block';

  if (typeof currentClientId !== 'undefined') {
    currentClientId = null;
  }
}
</script>

<!-- Add/Edit Client Form View (inline, not overlay) -->
<div id="clientModal" style="display:none;">
  <form id="clientForm" method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" novalidate>
    @csrf
    <div id="clientFormMethod" style="display:none;"></div>

    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h3 id="clientModalTitle" style="margin:0; font-size:18px; font-weight:600;">Client - Add New Individual</h3>
        <div style="display:flex; gap:8px;">
          <button type="button" class="btn-delete" id="clientDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteClient()">Delete</button>
          <button type="submit" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
          <button type="button" onclick="closeClientModal()" style="background:#333; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Cancel</button>
        </div>
      </div>
    </div>

      <!-- FORM CONTENT -->
      <div style="padding:0; overflow-x: auto;">
        
        <style>
          .form-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px 10px;
            font-family: 'Segoe UI', sans-serif;
          }
          .form-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
          }
          .form-item label {
            font-size: 11px;
            font-weight: 600;
            color: #333;
            margin: 0;
            white-space: nowrap;
          }
          .form-item input,
          .form-item select,
          .form-item textarea {
            border: 1px solid #999;
            padding: 5px 6px;
            font-size: 12px;
            border-radius: 0;
            width: 100%;
            box-sizing: border-box;
            height: 28px;
            background-color: #d9d9d9;
          }
          .form-item textarea {
            height: 100%;
          }
          .form-item input:focus, .form-item select:focus, .form-item textarea:focus {
            outline: 2px solid #f3742a;
            border-color: #f3742a;
          }
          .combined-input-group {
            display: flex;
            gap: 0;
            width: 100%;
          }
          .green-bg {
            background-color: #d9d9d9;
          }
          .grey-bg {
            background-color: #d9d9d9;
          }
          .checkbox-item {
            accent-color: #f3742a;
            width: 16px !important;
            height: 16px !important;
            margin: 0;
            cursor: pointer;
          }
        </style>

        <div style="background:#fff; padding:20px 30px; border:1px solid #ddd; border-radius:4px;">
        <div class="form-grid">

          <!-- ROW 1: Client Type | First Name | Surname | Other Names | Salutation -->
          <div class="form-item">
            <label>Client Type</label>
            <select id="client_type" name="client_type" onchange="toggleClientFields(); updateClientModalTitle();">
              <option value="Individual" selected>Individual</option>
              <option value="Business">Business</option>
              <option value="Company">Company</option>
            </select>
          </div>
          <div class="form-item" data-field-type="individual">
            <label>First Name</label>
            <input id="first_name" name="first_name" type="text">
          </div>
          <div class="form-item" data-field-type="business" style="display:none; grid-column:span 4;">
            <label>Business Name</label>
            <input id="business_name" name="business_name" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Surname</label>
            <input id="surname" name="surname" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Other Names</label>
            <input id="other_names" name="other_names" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Salutation</label>
            <input id="salutation" name="salutation" type="text">
          </div>

          <!-- ROW 2: DOB | NIN | ID Document Type | ID Expiry Date | Income Source -->
          <div class="form-item" data-field-type="individual">
            <label>DOB</label>
            <input id="dob_dor" name="dob_dor" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>NIN</label>
            <input id="nin_bcrn" name="nin_bcrn" type="text">
          </div>
          <div class="form-item" data-field-type="business" style="display:none;">
            <label>BCRN</label>
            <input id="bcrn_business" name="nin_bcrn" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>ID Document Type</label>
            <input id="id_document_type" name="id_document_type" type="text" value="ID Card">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>ID Expiry Date</label>
            <input id="id_expiry_date" name="id_expiry_date" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Income Source</label>
            <input id="income_source" name="income_source" type="text">
          </div>

          <!-- ROW 3: Monthly Income | Occupation | Employer | Married | Spouse's Name -->
          <div class="form-item" data-field-type="individual">
            <label>Monthly Income</label>
            <input id="monthly_income" name="monthly_income" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Occupation</label>
            <input id="occupation" name="occupation" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Employer</label>
            <input id="employer" name="employer" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Married</label>
            <input id="married" name="married" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Spouse's Name</label>
            <input id="spouses_name" name="spouses_name" type="text">
          </div>

          <!-- ROW 4: PEP | PEP Details | Passport No | Issuing Country -->
          <div class="form-item" data-field-type="individual">
            <label>PEP</label>
            <input id="pep" name="pep" type="text">
          </div>
          <div class="form-item" data-field-type="individual" style="grid-column: span 2;">
            <label>PEP Details</label>
            <input id="pep_comment" name="pep_comment" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Passport No</label>
            <input id="passport_no" name="passport_no" type="text">
          </div>
          <div class="form-item" data-field-type="individual">
            <label>Issuing Country</label>
            <div style="display:flex; gap:0;">
              <input type="text" value="Seychelles" readonly style="flex:1; border:1px dashed #999; background:#d9d9d9;">
              <input type="text" value="SEY" readonly style="width:50px; text-align:center; border:1px dashed #999; border-left:none; background:#d9d9d9; font-weight:bold;">
            </div>
            <input id="issuing_country" name="issuing_country" type="hidden" value="131">
          </div>

          <!-- ROW 5: Mobile No + Wattsapp | Alternate No + Wattsapp | Home No | Email Address | P.O. Box Number -->
          <div class="form-item">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <label>Mobile No</label>
              <label style="color:#f3742a; font-size:10px;">Wattsapp</label>
            </div>
            <div style="display:flex; gap:0;">
              <input id="mobile_no" name="mobile_no" type="text" style="flex:1;">
              <div style="width:32px; display:flex; align-items:center; justify-content:center; background:#fff; border:1px solid #999; border-left:none;">
                <input id="wa" name="wa" type="checkbox" value="1" class="checkbox-item" style="width:16px !important; height:16px !important;">
              </div>
            </div>
          </div>
          <div class="form-item">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <label>Alternate No</label>
              <label style="color:#f3742a; font-size:10px;">Wattsapp</label>
            </div>
            <div style="display:flex; gap:0;">
              <input id="alternate_no" name="alternate_no" type="text" style="flex:1;">
              <div style="width:32px; display:flex; align-items:center; justify-content:center; background:#fff; border:1px solid #999; border-left:none;">
                <input id="wa2" name="wa2" type="checkbox" value="1" class="checkbox-item" style="width:16px !important; height:16px !important;">
              </div>
            </div>
          </div>
          <div class="form-item">
            <label>Home No</label>
            <input id="home_no" name="home_no" type="text">
          </div>
          <div class="form-item">
            <label>Email Address</label>
            <input id="email_address" name="email_address" type="email">
          </div>
          <div class="form-item">
            <label>P.O. Box Number</label>
            <input id="po_box_no" name="po_box_no" type="text">
          </div>

          <!-- ROW 6: Location | District | Island | Country | Notes (spans 3 rows) -->
          <div class="form-item">
            <label>Location</label>
            <input id="location" name="location" type="text">
          </div>
          <div class="form-item">
            <label>District</label>
            <input id="district" name="district" type="text">
          </div>
          <div class="form-item">
            <label>Island</label>
            <input id="island" name="island" type="text">
          </div>
          <div class="form-item">
            <label>Country</label>
            <input id="country" name="country" type="text">
          </div>
          <div class="form-item" style="grid-row: span 3;">
            <label>Notes</label>
            <textarea id="notes" name="notes" style="resize:none; height:100%; min-height:80px;"></textarea>
          </div>

          <!-- ROW 7: Sign Up Date | Agency | Agent | Source -->
          <div class="form-item">
            <label>Sign Up Date</label>
            <input id="signed_up" name="signed_up" type="text">
          </div>
          <div class="form-item">
            <label>Agency</label>
            <input id="agency" name="agency" type="text">
          </div>
          <div class="form-item">
            <label>Agent</label>
            <input id="agent" name="agent" type="text">
          </div>
          <div class="form-item">
            <label>Source</label>
            <input id="source" name="source" type="text">
          </div>

          <!-- ROW 8: Source Name -->
          <div class="form-item" style="grid-column: 1;"></div>
          <div class="form-item" style="grid-column: 2;"></div>
          <div class="form-item" style="grid-column: 3;"></div>
          <div class="form-item" style="grid-column: 4;">
            <label>Source Name</label>
            <input id="source_name" name="source_name" type="text">
          </div>

          <!-- ROW 9: INSURABLES SECTION -->
          <div style="grid-column: 1 / span 5; margin-top:15px; padding-top:15px; border-top:1px solid #ddd;">
            <div style="display:flex; align-items:center; gap:20px;">
              <h4 style="font-size:13px; font-weight:bold; margin:0; min-width:80px;">Insurables</h4>

              <div style="display:flex; align-items:center; gap:5px;">
                <label for="has_vehicle" style="font-size:12px; margin:0; cursor:pointer;">Vehicle</label>
                <input id="has_vehicle" name="has_vehicle" type="checkbox" value="1" class="checkbox-item">
              </div>

              <div style="display:flex; align-items:center; gap:5px;">
                <label for="has_house" style="font-size:12px; margin:0; cursor:pointer;">House</label>
                <input id="has_house" name="has_house" type="checkbox" value="1" class="checkbox-item">
              </div>

              <div style="display:flex; align-items:center; gap:5px;">
                <label for="has_business" style="font-size:12px; margin:0; cursor:pointer;">Business</label>
                <input id="has_business_check" name="has_business" type="checkbox" value="1" class="checkbox-item">
              </div>

              <div style="display:flex; align-items:center; gap:5px;">
                <label for="has_boat" style="font-size:12px; margin:0; cursor:pointer;">Boat</label>
                <input id="has_boat" name="has_boat" type="checkbox" value="1" class="checkbox-item">
              </div>
            </div>
          </div>

          <!-- Hidden default fields -->
          <input type="hidden" name="status" id="status" value="Active">

        </div> <!-- End Form Grid -->
        </div> <!-- End Form White Box -->

        <!-- Document Section -->
        <div style="background:#fff; padding:20px; border:1px solid #ddd; border-radius:4px; margin-top:15px;">
           <h4 style="font-weight:bold; font-size:16px; margin:0 0 15px 0;">Documents</h4>
           <div id="editClientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; min-height:50px;">
              <!-- Docs loaded via JS -->
           </div>
           <div style="text-align:right; margin-top:15px;">
              <button type="button" class="btn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; margin-right:8px;">Upload Photo</button>
              <button type="button" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px;">Upload Document</button>
           </div>
        </div>

      </div> <!-- End Form Content -->
  </form>
</div>
<!-- End Client Form View -->

<!-- Client Details Modal -->
<div class="modal" id="clientDetailsModal">
    <div class="modal-content" style="max-width:95%; width:1400px; max-height:95vh; overflow-y:auto;">
      <div class="modal-header" style="background:#fff; color:#000; padding:12px 15px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd;">
        <div style="display:flex; gap:8px;">
          <button class="nav-tab" data-tab="proposals">Proposals</button>
          <button class="nav-tab" data-tab="policies">Policies</button>
          <button class="nav-tab" data-tab="payments">Payments</button>
          <button class="nav-tab" data-tab="vehicles">Vehicles</button>
          <button class="nav-tab" data-tab="claims">Claims</button>
          <button class="nav-tab active" data-tab="documents">Documents</button>
        </div>
        <div style="display:flex; gap:8px;">
          <button class="btn btn-edit" id="editClientFromModalBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Edit</button>
          <button class="modal-close" onclick="closeClientDetailsModal()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
        </div>
      </div>
      <div class="modal-body" style="background:#f5f5f5; padding:12px; overflow-x: auto; white-space: nowrap;">
        <div id="clientDetailsContentModal" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; align-items:start;">
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
                'passport_no'=>'Passport No'
              ];
              $ordered = [];
              foreach($selectedColumns as $col) {
                if(isset($all[$col])) {
                  $ordered[$col] = $all[$col];
                  unset($all[$col]);
                }
              }
              $ordered = array_merge($ordered, $all);
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
@endsection