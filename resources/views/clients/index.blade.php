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

  <!-- Main Clients Table View -->
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
          @if($filter == "ids_expired")
             Expired IDs
          @elseif($filter == "birthday_today")
             Birthdays Today
          @else
             Clients
            <span id="followUpLabel" style="display:{{ request()->get('follow_up') == 'true' && !request()->get('client_id') ? 'inline' : 'none' }}; color:#f3742a; font-size:16px; font-weight:500;"> - To Follow Up</span>
          @endif
          </h3>
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

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; margin-top:0;">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('clients.export', array_merge(request()->query(), ['page' => $clients->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button">Column</button>
        <button class="btn btn-export" id="printBtn" type="button" style="margin-left:10px;">Print</button>
      </div>
      <div class="paginator">
        {{ $clients->links() }}
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
      <button class="nav-tab" data-tab="proposals" data-url="/proposals" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Proposals</button>
      <button class="nav-tab" data-tab="policies" data-url="/policies" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Policies</button>
      <button class="nav-tab" data-tab="payments" data-url="/payments" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 12px; border-radius:2px; cursor:pointer; font-size:13px;">Payments</button>
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
        <button id="addDocumentBtn1" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Add Document</button>
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
  if (tableView) tableView.classList.remove('hidden');
  
  if (typeof currentClientId !== 'undefined') {
    currentClientId = null;
  }
}
</script>

<!-- Add/Edit Client Modal -->
<div class="modal" id="clientModal">
  <div class="modal-content" style="max-width:95%; width:1400px; max-height:95vh; overflow-y:auto;">
    <form id="clientForm" method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" novalidate>
      @csrf
      <div id="clientFormMethod" style="display:none;"></div>
      
      <div class="modal-header" style="background:#fff; color:#000; padding:12px 15px; display:flex; justify-content:flex-end; align-items:center; border-bottom:1px solid #ddd;">
        <div style="display:flex; gap:8px;">
          <button type="button" class="btn-delete" id="clientDeleteBtn" style="display:none; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteClient()">Delete</button>
          <button type="submit" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
          <button type="button" class="modal-close" onclick="closeClientModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
        </div>
      </div>

      <!-- CORRECTED FORM STRUCTURE -->
      <div class="modal-body" style="background:#fff; padding:20px; overflow-x: auto;">
        
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
            border: 1px solid #777;
            padding: 5px 6px;
            font-size: 12px;
            border-radius: 0;
            width: 100%;
            box-sizing: border-box;
            height: 28px;
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
            background-color: #e2efda;
          }
          .grey-bg {
            background-color: #f2f2f2;
          }
          .checkbox-item {
            accent-color: #f3742a;
            width: 16px !important;
            height: 16px !important;
            margin: 0;
            cursor: pointer;
          }
        </style>

        <div class="form-grid">
          
          <!-- ROW 1 -->
          <!-- Column 1: Client Type -->
          <div class="form-item">
            <label>Client Type</label>
            <select id="client_type" name="client_type" onchange="toggleClientFields()">
              <option value="Individual" selected>Individual</option>
              <option value="Business">Business</option>
              <option value="Company">Company</option>
            </select>
          </div>
          
          <!-- Column 2: Name -->
          <div class="form-item" data-field-type="individual">
            <label>Name</label>
            <input id="first_name" name="first_name" type="text">
          </div>
          <!-- Business Name (Hidden by default, spans 2 columns) -->
          <div class="form-item" data-field-type="business" style="display:none; grid-column:span 2;">
            <label>Business Name</label>
            <input id="business_name" name="business_name" type="text">
          </div>

          <!-- Column 3: Surname -->
          <div class="form-item" data-field-type="individual">
            <label>Surname</label>
            <input id="surname" name="surname" type="text" class="green-bg">
          </div>

          <!-- Column 4: Other Names -->
          <div class="form-item" data-field-type="individual">
            <label>Other Names</label>
            <input id="other_names" name="other_names" type="text" class="green-bg">
          </div>

          <!-- Column 5: Salutation -->
          <div class="form-item" data-field-type="individual">
            <label>Salutation</label>
            <select id="salutation" name="salutation" class="green-bg">
              <option value="">Select</option>
              @foreach($lookupData['salutations'] ?? [] as $s) 
                <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- ROW 2 -->
          <!-- Column 1: DOB with Age -->
          <div class="form-item" data-field-type="individual">
            <label>DOB</label>
            <div class="combined-input-group">
              <input id="dob_dor" name="dob_dor" type="date" class="green-bg" style="flex:1;">
              <input id="dob_age" type="text" readonly style="width:40px; text-align:center; border-left:none;" class="green-bg" placeholder="">
            </div>
          </div>

          <!-- Column 2: NIN -->
          <div class="form-item" data-field-type="individual">
            <label>NIN</label>
            <input id="nin_bcrn" name="nin_bcrn" type="text" class="green-bg">
          </div>
          <!-- Business BCRN (Hidden) -->
          <div class="form-item" data-field-type="business" style="display:none;">
            <label>BCRN</label>
            <input id="bcrn_business" name="nin_bcrn" type="text">
          </div>

          <!-- Column 3: ID Document Type -->
          <div class="form-item" data-field-type="individual">
            <label>ID Document Type</label>
            <select id="id_document_type" name="id_document_type" class="green-bg">
              <option value="">Select</option>
              <option value="NIN">NIN</option>
              <option value="Passport">Passport</option>
              <option value="Driver License">Driver License</option>
            </select>
          </div>

          <!-- Column 4: ID Expiry Date -->
          <div class="form-item" data-field-type="individual">
            <label>ID Expiry Date</label>
            <input id="id_expiry_date" name="id_expiry_date" type="date" class="green-bg">
          </div>

          <!-- Column 5: Passport No & Issuing Country -->
          <div class="form-item" data-field-type="individual">
            <div style="display:flex; justify-content:space-between;">
              <label>Passport No</label>
              <label style="margin-left:auto;">Issuing Country</label>
            </div>
            <div class="combined-input-group">
              <input id="passport_no" name="passport_no" type="text" class="green-bg" style="width:60%;">
              <input type="text" value="SEY" readonly style="width:40%; text-align:center; background:#ccffcc; border-left:none; font-weight:bold;">
              <select id="issuing_country" name="issuing_country" style="display:none;">
                <option value="131" selected>Seychelles</option>
              </select>
            </div>
          </div>

          <!-- ROW 3 -->
          <!-- Column 1: Income Source -->
          <div class="form-item" data-field-type="individual">
            <label>Income Source</label>
            <select id="income_source" name="income_source" class="green-bg">
              <option value="">Select</option>
              @foreach($lookupData['income_sources'] ?? [] as $i) 
                <option value="{{ $i['id'] }}">{{ $i['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- Column 2: Monthly Income -->
          <div class="form-item" data-field-type="individual">
            <label>Monthly Income</label>
            <input id="monthly_income" name="monthly_income" type="number" class="green-bg">
          </div>

          <!-- Column 3: Occupation -->
          <div class="form-item" data-field-type="individual">
            <label>Occupation</label>
            <select id="occupation" name="occupation" class="green-bg">
              <option value="">Select</option>
              @foreach($lookupData['occupations'] ?? [] as $o) 
                <option value="{{ $o['id'] }}">{{ $o['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- Column 4: Employer -->
          <div class="form-item" data-field-type="individual">
            <label>Employer</label>
            <input id="employer" name="employer" type="text" class="green-bg">
          </div>

          <!-- Column 5: Married & Spouse's Name -->
          <div class="form-item" data-field-type="individual">
            <div style="display:flex; justify-content:space-between;">
              <label>Married</label>
              <label style="margin-left:auto;">Spouse's Name</label>
            </div>
            <div class="combined-input-group">
              <div style="width:40px; display:flex; align-items:center; justify-content:center; border:1px solid #777; border-right:none; background:#e2efda;">
                <input id="married" name="married" type="checkbox" value="1" class="checkbox-item">
              </div>
              <input id="spouses_name" name="spouses_name" type="text" class="green-bg" style="flex:1;">
            </div>
          </div>

          <!-- ROW 4: PEP -->
          <!-- Column 1: PEP Checkbox -->
          <div class="form-item" data-field-type="individual">
            <label>PEP</label>
            <div style="padding-top:4px;">
              <input id="pep" name="pep" type="checkbox" value="1" class="checkbox-item">
            </div>
          </div>

          <!-- Columns 2-5: PEP Details (spans 4 columns) -->
          <div class="form-item" data-field-type="individual" style="grid-column: span 4;">
            <label>PEP Details</label>
            <input id="pep_comment" name="pep_comment" type="text" class="green-bg">
          </div>

          <!-- ROW 5 -->
          <!-- Column 1: Mobile No -->
          <div class="form-item">
            <label>Mobile No</label>
            <input id="mobile_no" name="mobile_no" type="text">
          </div>

          <!-- Column 2: Wattsapp -->
          <div class="form-item">
            <label style="color:#f3742a;">Wattsapp</label>
            <div style="padding-top:4px;">
              <input id="wa" name="wa" type="checkbox" value="1" class="checkbox-item">
            </div>
          </div>

          <!-- Column 3: Alternate No -->
          <div class="form-item">
            <label>Alternate No</label>
            <input id="alternate_no" name="alternate_no" type="text">
          </div>

          <!-- Column 4: Email Address -->
          <div class="form-item">
            <label>Email Address</label>
            <input id="email_address" name="email_address" type="email">
          </div>

          <!-- Column 5: P.O. Box Number -->
          <div class="form-item">
            <label>P.O. Box Number</label>
            <input id="po_box_no" name="po_box_no" type="text">
          </div>

          <!-- ROW 6 -->
          <!-- Column 1: District -->
          <div class="form-item">
            <label>District</label>
            <select id="district" name="district">
              <option value="">Select</option>
              @foreach($lookupData['districts'] ?? [] as $d) 
                <option value="{{ $d['id'] }}">{{ $d['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- Column 2: Location -->
          <div class="form-item">
            <label>Location</label>
            <input id="location" name="location" type="text">
          </div>

          <!-- Column 3: Island -->
          <div class="form-item">
            <label>Island</label>
            <select id="island" name="island">
              <option value="">Select</option>
              @foreach($lookupData['islands'] ?? [] as $is) 
                <option value="{{ $is['id'] }}">{{ $is['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- Column 4: Country -->
          <div class="form-item">
            <label>Country</label>
            <select id="country" name="country">
              <option value="">Select</option>
              @foreach($lookupData['countries'] ?? [] as $c) 
                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- Column 5: Notes (Spans rows 6-7) -->
          <div class="form-item" style="grid-column: 5; grid-row: 6 / span 2;">
            <label>Notes</label>
            <textarea id="notes" name="notes" style="resize:none; height:100%;"></textarea>
          </div>

          <!-- ROW 7 -->
          <!-- Column 1: Sign Up Date -->
          <div class="form-item">
            <label>Sign Up Date</label>
            <input id="signed_up" name="signed_up" type="date">
          </div>

          <!-- Column 2: Agency -->
          <div class="form-item">
            <label>Agency</label>
            <select id="agency" name="agency">
              <option value="">Select</option>
              @foreach($lookupData['agencies'] ?? [] as $a) 
                <option value="{{ $a['id'] }}">{{ $a['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- Column 3: Agent -->
          <div class="form-item">
            <label>Agent</label>
            <select id="agent" name="agent">
              <option value="">Select</option>
              @foreach($lookupData['agents'] ?? [] as $ag) 
                <option value="{{ $ag['id'] }}">{{ $ag['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- Column 4: Source -->
          <div class="form-item">
            <label>Source</label>
            <select id="source" name="source">
              <option value="">Select</option>
              @foreach($lookupData['sources'] ?? [] as $s) 
                <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> 
              @endforeach
            </select>
          </div>

          <!-- ROW 8 -->
          <!-- Empty columns 1-3 -->
          <div style="grid-column: 1;"></div>
          <div style="grid-column: 2;"></div>
          <div style="grid-column: 3;"></div>

          <!-- Column 4: Source Name (If applicable) - positioned below Source -->
          <div class="form-item" style="grid-column: 4;">
            <label style="color:#999; font-size:10px;">Source Name (If applicable)</label>
            <input id="source_name" name="source_name" type="text" style="border-style:dashed; border-color:#ccc;">
          </div>

          <!-- ROW 9: INSURABLES SECTION -->
          <div style="grid-column: 1 / span 5; margin-top:20px; padding-top:15px; border-top:1px solid #ddd;">
            <div style="display:flex; align-items:center; gap:30px;">
              <h4 style="font-size:13px; font-weight:bold; margin:0;">Insurables</h4>
              
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
        
        <!-- Document Section inside Modal Form -->
        <div style="margin-top:20px; border-top:2px solid #ddd; padding-top:10px;">
           <h4 style="font-weight:bold; font-size:16px;">Documents</h4>
           <div id="editClientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; min-height:50px;">
              <!-- Docs loaded via JS -->
           </div>
           <div style="text-align:right;">
              <button type="button" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px;">+ Add Document</button>
           </div>
        </div>

      </div> <!-- End Modal Body -->
    </form>
  </div>
</div>

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