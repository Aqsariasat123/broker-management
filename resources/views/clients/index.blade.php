@extends('layouts.app')
@section('content')
<style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; color: #000; margin: 0; background: #f5f5f5; }
    .container-table { max-width: 100%; margin: 0 auto; background: #fff; padding: 0; }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0; flex-wrap: wrap; gap: 15px; background: #f5f5f5; padding: 15px 20px; border-bottom: 1px solid #ddd; }
    .page-title-section { display: flex; align-items: center; gap: 15px; flex: 1; }
    h3 { background: transparent; padding: 0; margin: 0; font-weight: bold; color: #2d2d2d; font-size: 24px; }
    .records-found { font-size: 14px; color: #2d2d2d; font-weight: normal; }
    .top-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:15px; margin-bottom:15px; }
    .left-group { display:flex; align-items:center; gap:15px; flex:1 1 auto; min-width:220px; }
    .left-buttons { display:flex; gap:10px; align-items:center; }
    .filter-group { display:flex; align-items:center; gap:8px; }
    .filter-panel { display:none; background:#f5f5f5; padding:12px 15px; border-radius:4px; margin-top:10px; border:1px solid #ddd; }
    .filter-panel.active { display:block; }
    .column-filter { display:none; }
    .column-filter.visible { display:block; }
    .action-buttons { margin-left:auto; display:flex; gap:10px; align-items:center; }
    .btn { border:none; cursor:pointer; padding:6px 16px; font-size:13px; border-radius:2px; white-space:nowrap; transition:background-color .2s; text-decoration:none; color:inherit; background:#fff; border:1px solid #ccc; font-weight:normal; }
    .btn-add { background:#f3742a; color:#fff; border-color:#f3742a; }
    .btn-export, .btn-column { background:#fff; color:#000; border:1px solid #ccc; }
    .btn-follow-up { background:#2d2d2d; color:#fff; border-color:#2d2d2d; }
    .btn-list-all { background:#4CAF50; color:#fff; border-color:#4CAF50; }
    .btn-close { background:#e0e0e0; color:#000; border-color:#ccc; }
    .btn-back { background:#ccc; color:#333; border-color:#ccc; }
    .filter-toggle { display:flex; align-items:center; gap:8px; }
    .toggle-switch { position:relative; width:44px; height:24px; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; transition:.4s; border-radius:24px; }
    .toggle-slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background-color:white; transition:.4s; border-radius:50%; }
    .toggle-switch input:checked + .toggle-slider { background-color:#4CAF50; }
    .toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
    .table-responsive { width: 100%; border: none; background: #fff; margin-bottom:0; overflow-x: auto; padding: 0 20px; }
    .table-responsive.no-scroll { overflow: visible; }
    .footer { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; gap:10px; border-top:1px solid #ddd; flex-wrap:wrap; margin-top:0; background:#f5f5f5; }
    .footer-left { display:flex; gap:10px; }
   .paginator {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: #555;
      white-space: nowrap;
      margin: 0 auto;
    }
    .btn-page{
      color: #2d2d2d;
      font-size: 14px;
      width: 32px;
      height: 32px;
      padding: 0;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
      border: 1px solid #ddd;
      border-radius: 2px;
      font-weight: normal;
    }
    .btn-page:hover:not([disabled]) { background: #e9e9e9; }
    .btn-page[disabled] { opacity: 0.5; cursor: not-allowed; background: #f5f5f5; }
    .page-info { padding: 0 12px; color: #555; font-size: 12px; }
    table { width:100%; border-collapse:collapse; font-size:13px; min-width:900px; }
    thead tr { background-color: #000; color: #fff; height:35px; font-weight: normal; }
    thead th { padding:8px 5px; text-align:left; border-right:1px solid #444; white-space:nowrap; font-weight: normal; color: #fff !important; }
    thead th:first-child { text-align:center; }
    thead th:last-child { border-right:none; }
    tbody tr { background-color:#fff; border-bottom:1px solid #ddd; min-height:32px; }
    tbody tr:nth-child(even) { background-color:#f8f8f8; }
    tbody tr.inactive-row { background:#fff3cd !important; }
    tbody tr.inactive-row td { background:#fff3cd !important; }
    tbody tr.has-expired { background-color:#ffebee !important; }
    tbody tr.has-expired td { background-color:#ffebee !important; }
    tbody tr.has-expiring { background-color:#fff8e1 !important; }
    tbody tr.has-expiring td { background-color:#fff8e1 !important; }
    tbody td { padding:8px 5px; border-right:1px solid #ddd; white-space:nowrap; vertical-align:middle; font-size:12px; }
    tbody td:last-child { border-right:none; }
    .bell-cell { text-align:center; padding:8px 5px; vertical-align:middle; min-width:50px; }
    .bell-cell.expired { background-color:#ffebee !important; }
    .bell-cell.expiring { background-color:#fff8e1 !important; }
    .bell-cell:not(.expired):not(.expiring) { background-color:#fff !important; }
    .bell-radio { width:16px; height:16px; cursor:not-allowed; pointer-events:none; opacity:0.6; }
    .bell-radio.expired { accent-color:#dc3545; }
    .bell-radio.expiring { accent-color:#ffc107; }
    .bell-radio.normal { accent-color:#ccc; }
    .bell-cell.expired td { background-color:#ffebee !important; }
    .bell-cell.expiring td { background-color:#fff8e1 !important; }

    .action-cell { display:flex; align-items:center; gap:10px; padding:8px; }
    .action-radio { width:18px; height:18px; cursor:default; pointer-events:none; accent-color:#f3742a; }
    .action-radio.selected { accent-color:#f3742a; }
    .action-radio:checked { accent-color:#f3742a; }
    .action-radio:checked::before { background-color:#f3742a; }
    .bell-cell.expired .action-radio { accent-color:#dc3545; }
    .bell-cell.expiring .action-radio { accent-color:#ffc107; }
    .action-expand { width:22px; height:22px; cursor:pointer; display:inline-block; }
    .action-clock { width:22px; height:22px; cursor:pointer; display:inline-block; }
    .action-ellipsis { width:22px; height:22px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .action-menu { display:none; }
    .checkbox-cell { text-align:center; }
    .checkbox-cell input[type="checkbox"] { 
      width:18px; 
      height:18px; 
      cursor:pointer; 
      accent-color:#f3742a; 
      border-radius:3px;
      appearance:none;
      -webkit-appearance:none;
      -moz-appearance:none;
      border:2px solid #ccc;
      background-color:#fff;
      position:relative;
    }
    .checkbox-cell input[type="checkbox"]:checked {
      background-color:#f3742a;
      border-color:#f3742a;
    }
    .checkbox-cell input[type="checkbox"]:checked::after {
      content:'✓';
      position:absolute;
      top:50%;
      left:50%;
      transform:translate(-50%, -50%);
      color:#fff;
      font-size:12px;
      font-weight:bold;
      line-height:1;
    }
    .icon-expand { cursor:pointer; color:black; text-align:center; width:20px; }
    .btn-action { padding:2px 6px; font-size:11px; margin:1px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:2px; display:inline-block; }
    .badge-status { font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; }
    /* Modal styles (simple, like contacts) */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; }
    .modal.show { display:flex; }
    .modal-content { background:#fff; border-radius:6px; width:92%; max-width:1100px; max-height:calc(100vh - 40px); overflow:auto; box-shadow:0 4px 6px rgba(0,0,0,.1); padding:0; }
    .modal-header { padding:12px 15px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; background:white; }
    .modal-body { padding:15px; }
    .modal-close { background:none; border:none; font-size:18px; cursor:pointer; color:#666; }
    .modal-footer
    { padding:12px 15px; border-top:1px solid #ddd; display:flex; 
      justify-content:flex-end; gap:8px; background:#f9f9f9; }
    /* Client Details Modal Styles */
    .nav-tab { background:#2d2d2d; color:#fff; border:none; padding:8px 16px; cursor:pointer; font-size:13px; border-radius:2px; }
    .nav-tab.active { background:#000; }
    .nav-tab:hover { background:#1a1a1a; }
    .detail-section { background:#fff; border:1px solid #ddd; margin-bottom:10px; border-radius:2px; }
    .detail-section-header { background:#808080; color:#fff; padding:6px 10px; font-weight:bold; font-size:12px; border-bottom:1px solid #ddd; text-transform:uppercase; }
    .detail-section-body { padding:8px; }
    .detail-row { display:flex; flex-direction:column; margin-bottom:8px; }
    .detail-row:last-child { margin-bottom:0; }
    .detail-label { font-size:10px; color:#555; font-weight:600; margin-bottom:3px; }
    .detail-value { font-size:11px; color:#000; padding:4px 6px; border:1px solid #ddd; background:#fff; border-radius:2px; min-height:22px; display:flex; align-items:center; }
    .detail-value.checkbox { border:none; padding:0; background:transparent; }
    .detail-value.checkbox input[type="checkbox"] { 
      width:18px; 
      height:18px;
      appearance:none;
      -webkit-appearance:none;
      -moz-appearance:none;
      border:1px solid #ddd;
      border-radius:3px;
      background:#fff;
      position:relative;
      margin:0;
    }
    .detail-value.checkbox input[type="checkbox"]:checked {
      background-color:#f3742a;
      border-color:#f3742a;
    }
    .detail-value.checkbox input[type="checkbox"]:checked::after {
      content:'✓';
      position:absolute;
      top:50%;
      left:50%;
      transform:translate(-50%, -50%);
      color:#fff;
      font-size:12px;
      font-weight:bold;
      line-height:1;
    }
    .detail-value textarea { width:100%; min-height:40px; resize:vertical; font-family:inherit; font-size:11px; border:1px solid #ddd; background:#fff; border-radius:2px; padding:4px 6px; }
    .detail-photo { width:80px; height:100px; object-fit:cover; border:1px solid #ddd; border-radius:2px; flex-shrink:0; }
    .document-item { display:flex; flex-direction:column; align-items:center; gap:5px; padding:10px; border:1px solid #ddd; border-radius:4px; background:#fff; width:120px; }
    .document-icon { width:60px; height:60px; background:#f0f0f0; border:1px solid #ddd; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:10px; color:#666; }
    /* Client Details Modal Styles */
    .nav-tab { background:#2d2d2d; color:#fff; border:none; padding:8px 16px; cursor:pointer; font-size:13px; border-radius:2px; }
    .nav-tab.active { background:#000; }
    .nav-tab:hover { background:#1a1a1a; }
    .detail-section { background:#fff; border:1px solid #ddd; margin-bottom:10px; border-radius:2px; }
    .detail-section-header { background:#808080; color:#fff; padding:6px 10px; font-weight:bold; font-size:12px; border-bottom:1px solid #ddd; text-transform:uppercase; }
    .detail-section-body { padding:8px; }
    .detail-row { display:flex; flex-direction:column; margin-bottom:8px; }
    .detail-row:last-child { margin-bottom:0; }
    .detail-label { font-size:10px; color:#555; font-weight:600; margin-bottom:3px; }
    .detail-value { font-size:11px; color:#000; padding:4px 6px; border:1px solid #ddd; background:#fff; border-radius:2px; min-height:22px; display:flex; align-items:center; }
    .detail-value.checkbox { border:none; padding:0; background:transparent; }
    .detail-value.checkbox input[type="checkbox"] { 
      width:18px; 
      height:18px;
      appearance:none;
      -webkit-appearance:none;
      -moz-appearance:none;
      border:1px solid #ddd;
      border-radius:3px;
      background:#fff;
      position:relative;
      margin:0;
    }
    .detail-value.checkbox input[type="checkbox"]:checked {
      background-color:#f3742a;
      border-color:#f3742a;
    }
    .detail-value.checkbox input[type="checkbox"]:checked::after {
      content:'✓';
      position:absolute;
      top:50%;
      left:50%;
      transform:translate(-50%, -50%);
      color:#fff;
      font-size:12px;
      font-weight:bold;
      line-height:1;
    }
    .detail-value textarea { width:100%; min-height:40px; resize:vertical; font-family:inherit; font-size:11px; border:1px solid #ddd; background:#fff; border-radius:2px; padding:4px 6px; }
    .detail-photo { width:80px; height:100px; object-fit:cover; border:1px solid #ddd; border-radius:2px; flex-shrink:0; }
    .document-item { display:flex; flex-direction:column; align-items:center; gap:5px; padding:10px; border:1px solid #ddd; border-radius:4px; background:#fff; width:120px; }
    .document-icon { width:60px; height:60px; background:#f0f0f0; border:1px solid #ddd; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:10px; color:#666; }
    .form-row { display:flex; gap:10px; margin-bottom:12px; flex-wrap:wrap; align-items:flex-start; }
    .form-group { flex:0 0 calc((100% - 20px) / 3); }
    .form-group label { display:block; margin-bottom:4px; font-weight:600; font-size:13px; }
    .form-control, select, textarea { width:100%; padding:4px 6px; border:1px solid #ccc; border-radius:2px; font-size:13px; }
    .btn-save { background:#007bff; color:#fff; border:none; padding:6px 12px; border-radius:2px; cursor:pointer; }
    .btn-cancel { background:#6c757d; color:#fff; border:none; padding:6px 12px; border-radius:2px; cursor:pointer; }
    .btn-delete { background:#dc3545; color:#fff; border:none; padding:6px 12px; border-radius:2px; cursor:pointer; }
    .column-selection { display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:8px; margin-bottom:15px; max-height:500px; overflow-y:auto; padding:5px; }
    .column-item { display:flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid #ddd; border-radius:2px; cursor:move; background:#fff; transition:background 0.2s; position:relative; min-height:40px; }
    .column-item:hover { background:#f5f5f5; }
    .column-item.dragging { opacity:0.5; background:#e3f2fd; border-color:#2196F3; }
    .column-item.drag-over { border-top:2px solid #2196F3; }
    @media print {
      /* Hide all page elements except table */
      .page-header, .footer, .action-buttons, .btn, .modal,
      .dashboard > .container-table > .page-header,
      .dashboard > .container-table > .footer,
      .records-found, .filter-group, h3 { display: none !important; }
      
      /* Show only table container */
      .table-responsive { 
        display: block !important; 
        overflow: visible !important; 
        border: none !important; 
        padding: 0 !important; 
        margin: 0 !important;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
      }
      
      /* Table styling */
      table { 
        width: 100% !important; 
        page-break-inside: auto; 
        border-collapse: collapse !important;
        font-size: 11px !important;
      }
      
      /* Header styling */
      thead { display: table-header-group !important; }
      thead th { 
        background-color: #000 !important; 
        color: #fff !important; 
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        border: 1px solid #333 !important;
        padding: 8px 5px !important;
      }
      
      /* Body styling */
      tbody tr { 
        page-break-inside: avoid; 
        page-break-after: auto; 
        border-bottom: 1px solid #ddd !important;
      }
      tbody td { 
        border: 1px solid #ddd !important; 
        padding: 6px 5px !important;
      }
      
      /* Show action and notification columns with icons */
      .action-cell, .bell-cell { 
        display: table-cell !important; 
        width: auto !important;
        padding: 6px 5px !important;
        border: 1px solid #ddd !important;
      }
      .action-cell svg, .action-ellipsis svg { 
        display: inline-block !important; 
        visibility: visible !important;
        width: 22px !important;
        height: 22px !important;
      }
      .action-radio, .bell-cell input[type="radio"] { 
        display: inline-block !important; 
        visibility: visible !important;
        width: 16px !important;
        height: 16px !important;
      }
      .bell-cell svg { 
        display: inline-block !important; 
        visibility: visible !important;
      }
      .column-filter { display: none !important; }
      
      /* Page settings */
      @page { 
        margin: 1cm; 
        size: A4 landscape;
      }
      
      /* Hide sidebar and other layout elements */
      .sidebar, .main-content > .top-header { display: none !important; }
    }
    @media (max-width:768px) { .form-row .form-group { flex:0 0 calc((100% - 20px) / 2); } .table-responsive { max-height:500px; } }
  </style>

@php
  $selectedColumns = session('client_columns', [
    'client_name','client_type','nin_bcrn','dob_dor','mobile_no','wa','district','occupation','source','status','signed_up',
    'employer','clid','contact_person','income_source','married','spouses_name','alternate_no','email_address','location',
    'island','country','po_box_no','pep','pep_comment','image','salutation','first_name','other_names','surname','passport_no'
  ]);
@endphp

<div class="dashboard">
  <div class="container-table">
    <div class="page-header">
      <div class="page-title-section">
        <h3>Clients</h3>
        <div class="records-found">Records Found - {{ $clients->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
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
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addClientBtn">Add</button>
        <button class="btn btn-close" id="closeBtn">Close</button>
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
                  <input type="radio" name="client_select" class="action-radio {{ $client->hasExpired ?? false ? 'expired' : ($client->hasExpiring ?? false ? 'expiring' : '') }}" value="{{ $client->id }}" data-client-id="{{ $client->id }}" disabled {{ ($client->hasExpired ?? false) || ($client->hasExpiring ?? false) ? 'checked' : '' }}>
                </div>
              </td>
              <td class="action-cell">
                <svg class="action-expand" onclick="openClientDetailsModal({{ $client->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="action-clock" onclick="window.location.href='{{ route('clients.index') }}?follow_up=true'" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <circle cx="12" cy="12" r="9" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 7V12L15 15" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'client_name')
                  <td data-column="client_name">
                    <a href="{{ route('clients.show', $client->id) }}" style="color:#007bff; text-decoration:underline;">{{ $client->client_name }}</a>
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
                    <input type="checkbox" {{ $client->wa ? 'checked' : '' }} disabled>
                  </td>
                @elseif($col == 'district')
                  <td data-column="district">{{ $client->district ?? '-' }}</td>
                @elseif($col == 'occupation')
                  <td data-column="occupation">{{ $client->occupation ?? '-' }}</td>
                @elseif($col == 'source')
                  <td data-column="source">{{ $client->source }}</td>
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
                  <td data-column="income_source">{{ $client->income_source ?? '-' }}</td>
                @elseif($col == 'married')
                  <td data-column="married" class="checkbox-cell">
                    <input type="checkbox" {{ $client->married ? 'checked' : '' }} disabled>
                  </td>
                @elseif($col == 'spouses_name')
                  <td data-column="spouses_name">{{ $client->spouses_name ?? '-' }}</td>
                @elseif($col == 'alternate_no')
                  <td data-column="alternate_no">{{ $client->alternate_no ?? '-' }}</td>
                @elseif($col == 'email_address')
                  <td data-column="email_address">{{ $client->email_address ?? '-' }}</td>
                @elseif($col == 'location')
                  <td data-column="location">{{ $client->location ?? '-' }}</td>
                @elseif($col == 'island')
                  <td data-column="island">{{ $client->island ?? '-' }}</td>
                @elseif($col == 'country')
                  <td data-column="country">{{ $client->country ?? '-' }}</td>
                @elseif($col == 'po_box_no')
                  <td data-column="po_box_no">{{ $client->po_box_no ?? '-' }}</td>
                @elseif($col == 'pep')
                  <td data-column="pep" class="checkbox-cell">
                    <input type="checkbox" {{ $client->pep ? 'checked' : '' }} disabled>
                  </td>
                @elseif($col == 'pep_comment')
                  <td data-column="pep_comment">{{ $client->pep_comment ?? '-' }}</td>
                @elseif($col == 'image')
                  <td data-column="image">{{ $client->image ? '📷' : '-' }}</td>
                @elseif($col == 'salutation')
                  <td data-column="salutation">{{ $client->salutation ?? '-' }}</td>
                @elseif($col == 'first_name')
                  <td data-column="first_name">{{ $client->first_name }}</td>
                @elseif($col == 'other_names')
                  <td data-column="other_names">{{ $client->other_names ?? '-' }}</td>
                @elseif($col == 'surname')
                  <td data-column="surname">{{ $client->surname }}</td>
                @elseif($col == 'passport_no')
                  <td data-column="passport_no">{{ $client->passport_no ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="footer">
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

  <!-- Add/Edit Client Modal (single) -->
  <div class="modal" id="clientModal">
    <div class="modal-content" style="max-width:95%; width:1400px; max-height:95vh; overflow-y:auto;">
      <form id="clientForm" method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="clientFormMethod" style="display:none;"></div>
        
        <div class="modal-header" style="background:#fff; color:#000; padding:12px 15px; display:flex; justify-content:flex-end; align-items:center; border-bottom:1px solid #ddd;">
          <div style="display:flex; gap:8px;">
            <button type="button" class="btn-delete" id="clientDeleteBtn" style="display:none; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteClient()">Delete</button>
            <button type="submit" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
            <button type="button" class="modal-close" onclick="closeClientModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
          </div>
        </div>

        <div class="modal-body" style="background:#f5f5f5; padding:12px;">
          <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
            <!-- Column 1: Customer Details & Individual Details -->
            <div>
              <div class="detail-section">
                <div class="detail-section-header">CUSTOMER DETAILS</div>
                <div class="detail-section-body">
                  <div class="detail-row">
                    <span class="detail-label">Client Type</span>
                    <select id="client_type" name="client_type" class="detail-value" required style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      <option value="Individual">Individual</option>
                      <option value="Business">Business</option>
                      <option value="Company">Company</option>
                    </select>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">DOB/DOR</span>
                    <div style="display:flex; gap:5px;">
                      <input id="dob_dor" name="dob_dor" type="date" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <input id="dob_age" type="text" readonly class="detail-value" style="width:50px; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#f5f5f5; font-size:11px;">
                    </div>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">NIN/BCRN</span>
                    <input id="nin_bcrn" name="nin_bcrn" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">ID Expiry Date</span>
                    <div style="display:flex; gap:5px;">
                      <input id="id_expiry_date" name="id_expiry_date" type="date" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <input id="id_expiry_days" type="text" readonly class="detail-value" style="width:50px; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#f5f5f5; font-size:11px;">
                    </div>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Client Status</span>
                    <select id="status" name="status" class="detail-value" required style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['client_statuses'] as $st) <option value="{{ $st }}">{{ $st }}</option> @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="detail-section">
                <div class="detail-section-header">INDIVIDUAL DETAILS</div>
                <div class="detail-section-body">
                  <div class="detail-row">
                    <span class="detail-label">Salutation</span>
                    <select id="salutation" name="salutation" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['salutations'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
                    </select>
                  </div>
                  <div class="detail-row" style="display:flex; flex-direction:row; align-items:flex-start; gap:10px;">
                    <div style="flex:1; display:flex; flex-direction:column;">
                      <span class="detail-label">First Name</span>
                      <input id="first_name" name="first_name" class="detail-value" required style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                    </div>
                    <div style="display:flex; flex-direction:column;">
                      <span class="detail-label" style="visibility:hidden;">Photo</span>
                      <div id="clientPhotoPreview" style="width:80px; height:100px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        <img id="clientPhotoImg" src="" alt="Photo" class="detail-photo" style="display:none; width:100%; height:100%; object-fit:cover;">
                        <span style="font-size:10px; color:#999;">Photo</span>
                      </div>
                      <input id="image" name="image" type="file" accept="image/*" required style="margin-top:5px; font-size:10px; width:80px;" onchange="previewClientPhoto(event)">
                      <input type="hidden" id="existing_image" name="existing_image" value="">
                    </div>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Other Names</span>
                    <input id="other_names" name="other_names" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Surname</span>
                    <input id="surname" name="surname" class="detail-value" required style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Passport No</span>
                    <div style="display:flex; gap:5px;">
                      <input id="passport_no" name="passport_no" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <input type="text" value="SEY" readonly style="width:60px; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#fff; text-align:center; font-size:11px;">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Column 2: Contact Details & Income Details -->
            <div>
              <div class="detail-section">
                <div class="detail-section-header">CONTACT DETAILS</div>
                <div class="detail-section-body">
                  <div class="detail-row">
                    <span class="detail-label">Mobile No</span>
                    <input id="mobile_no" name="mobile_no" class="detail-value" required style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">On Wattsapp</span>
                    <div class="detail-value checkbox">
                      <input id="wa" name="wa" type="checkbox" value="1">
                    </div>
                  </div>
                  <div class="detail-row" id="alternate_no_row">
                    <span class="detail-label">Alternate No</span>
                    <input id="alternate_no" name="alternate_no" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Email Address</span>
                    <input id="email_address" name="email_address" type="email" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Contact Person</span>
                    <input id="contact_person" name="contact_person" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                </div>
              </div>
              <div class="detail-section">
                <div class="detail-section-header">INCOME DETAILS</div>
                <div class="detail-section-body">
                  <div class="detail-row">
                    <span class="detail-label">Occupation</span>
                    <select id="occupation" name="occupation" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['occupations'] as $o) <option value="{{ $o }}">{{ $o }}</option> @endforeach
                    </select>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Income Source</span>
                    <select id="income_source" name="income_source" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['income_sources'] as $i) <option value="{{ $i }}">{{ $i }}</option> @endforeach
                    </select>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Employer</span>
                    <input id="employer" name="employer" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Monthly Income</span>
                    <input id="monthly_income" name="monthly_income" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label"></span>
                    <input type="text" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                </div>
              </div>
            </div>

            <!-- Column 3: Address Details & Other Details -->
            <div>
              <div class="detail-section">
                <div class="detail-section-header">ADDRESS DETAILS</div>
                <div class="detail-section-body">
                  <div class="detail-row">
                    <span class="detail-label">District</span>
                    <select id="district" name="district" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['districts'] as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
                    </select>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Address</span>
                    <input id="location" name="location" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Island</span>
                    <select id="island" name="island" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['islands'] as $is) <option value="{{ $is }}">{{ $is }}</option> @endforeach
                    </select>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Country</span>
                    <select id="country" name="country" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['countries'] as $c) <option value="{{ $c }}">{{ $c }}</option> @endforeach
                    </select>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">P.O. Box No</span>
                    <input id="po_box_no" name="po_box_no" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                </div>
              </div>
              <div class="detail-section">
                <div class="detail-section-header">OTHER DETAILS</div>
                <div class="detail-section-body">
                  <div class="detail-row">
                    <span class="detail-label">Married</span>
                    <div class="detail-value checkbox">
                      <input id="married" name="married" type="checkbox" value="1">
                    </div>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Spouse's Name</span>
                    <input id="spouses_name" name="spouses_name" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">PEP</span>
                    <div style="display:flex; gap:5px; align-items:center;">
                      <div class="detail-value checkbox" style="flex:0; min-width:auto;">
                        <input id="pep" name="pep" type="checkbox" value="1">
                      </div>
                      <input type="text" value="PEP Details" readonly class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#fff; font-size:11px;">
                    </div>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label"></span>
                    <textarea id="pep_comment" name="pep_comment" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; min-height:40px; resize:vertical; font-size:11px;"></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- Column 4: Registration Details & Insurables -->
            <div>
              <div class="detail-section">
                <div class="detail-section-header">REGISTRATION DETAILS</div>
                <div class="detail-section-body">
                  <div class="detail-row">
                    <span class="detail-label">Sign Up Date</span>
                    <input id="signed_up" name="signed_up" type="date" class="detail-value" required style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Agency</span>
                    <input id="agency" name="agency" type="text" value="Keystone" readonly class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#f5f5f5; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Agent</span>
                    <input id="agent" name="agent" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Source</span>
                    <select id="source" name="source" class="detail-value" required style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                      <option value="">Select</option>
                      @foreach($lookupData['sources'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
                    </select>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Source Name</span>
                    <input id="source_name" name="source_name" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                  </div>
                </div>
              </div>
              <div class="detail-section">
                <div class="detail-section-header">INSURABLES</div>
                <div class="detail-section-body">
                  <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px; margin-bottom:12px;">
                    <div class="detail-row" style="margin-bottom:0;">
                      <span class="detail-label">Vehicle</span>
                      <div class="detail-value checkbox">
                        <input id="has_vehicle" name="has_vehicle" type="checkbox" value="1">
                      </div>
                    </div>
                    <div class="detail-row" style="margin-bottom:0;">
                      <span class="detail-label">House</span>
                      <div class="detail-value checkbox">
                        <input id="has_house" name="has_house" type="checkbox" value="1">
                      </div>
                    </div>
                    <div class="detail-row" style="margin-bottom:0;">
                      <span class="detail-label">Business</span>
                      <div class="detail-value checkbox">
                        <input id="has_business" name="has_business" type="checkbox" value="1">
                      </div>
                    </div>
                    <div class="detail-row" style="margin-bottom:0;">
                      <span class="detail-label">Boat</span>
                      <div class="detail-value checkbox">
                        <input id="has_boat" name="has_boat" type="checkbox" value="1">
                      </div>
                    </div>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Notes</span>
                    <textarea id="notes" name="notes" class="detail-value" style="width:100%; border:1px solid #ddd; padding:4px 6px; border-radius:2px; min-height:40px; resize:vertical; font-size:11px;"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Documents Section -->
          <div style="margin-top:15px; padding-top:12px; border-top:2px solid #ddd;">
            <h4 style="font-weight:bold; margin-bottom:10px; color:#000; font-size:13px;">Documents</h4>
            <div id="editClientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
              <!-- Documents will be loaded here -->
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
              <button type="button" class="btn" onclick="document.getElementById('image').click()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Photo</button>
              <button type="button" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Add Document</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Client Details Modal -->
  <div class="modal" id="clientDetailsModal">
    <div class="modal-content" style="max-width:95%; width:1400px; max-height:95vh; overflow-y:auto;">
      <div class="modal-header" style="background:#fff; color:#000; padding:12px 15px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd;">
        <div style="display:flex; gap:8px;">
          <button class="nav-tab active" data-tab="proposals">Proposals</button>
          <button class="nav-tab" data-tab="policies">Policies</button>
          <button class="nav-tab" data-tab="payments">Payments</button>
          <button class="nav-tab" data-tab="vehicles">Vehicles</button>
          <button class="nav-tab" data-tab="claims">Claims</button>
          <button class="nav-tab" data-tab="documents">Documents</button>
        </div>
        <div style="display:flex; gap:8px;">
          <button class="btn btn-edit" id="editClientFromModalBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Edit</button>
          <button class="modal-close" onclick="closeClientDetailsModal()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
        </div>
      </div>
      <div class="modal-body" style="background:#f5f5f5; padding:12px;">
        <div id="clientDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
          <!-- Content will be loaded via JavaScript -->
        </div>
        <div id="clientDocumentsSection" style="margin-top:15px; padding-top:12px; border-top:2px solid #ddd;">
          <h4 style="font-weight:bold; margin-bottom:10px; color:#000; font-size:13px;">Documents</h4>
          <div id="clientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
            <!-- Documents will be loaded here -->
          </div>
          <div style="display:flex; gap:10px; justify-content:flex-end;">
            <input type="file" id="photoUploadInput" accept="image/*" style="display:none;" onchange="handlePhotoUpload(event)">
            <button class="btn" onclick="document.getElementById('photoUploadInput').click()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Photo</button>
            <button class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Add Document</button>
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

  <!-- Column Selection Modal -->
  <div class="modal" id="columnModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Column Select & Sort</h4>
        <button type="button" class="modal-close" onclick="closeColumnModal()">×</button>
      </div>
      <div class="modal-body">
        <div style="display:flex;gap:8px;margin-bottom:12px;">
          <button class="btn" onclick="selectAllColumns()">Select All</button>
          <button class="btn" onclick="deselectAllColumns()">Deselect All</button>
        </div>

        <form id="columnForm" action="{{ route('clients.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @php
              $all = [
                'client_name'=>'Client Name','client_type'=>'Client Type','nin_bcrn'=>'NIN/BCRN','dob_dor'=>'DOB/DOR','mobile_no'=>'MobileNo',
                'wa'=>'WA','district'=>'District','occupation'=>'Occupation','source'=>'Source','status'=>'Status','signed_up'=>'Signed Up',
                'employer'=>'Employer','clid'=>'CLID','contact_person'=>'Contact Person','income_source'=>'Income Source','married'=>'Married',
                'spouses_name'=>'Spouses Name','alternate_no'=>'Alternate No','email_address'=>'Email Address','location'=>'Location',
                'island'=>'Island','country'=>'Country','po_box_no'=>'P.O. Box No','pep'=>'PEP','pep_comment'=>'PEP Comment',
                'image'=>'Image','salutation'=>'Salutation','first_name'=>'First Name','other_names'=>'Other Names','surname'=>'Surname','passport_no'=>'Passport No'
              ];
              // Maintain order based on selectedColumns
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
              // Mandatory fields that should always be checked and disabled
              $mandatoryFields = ['client_name', 'client_type', 'mobile_no', 'source', 'status', 'signed_up', 'clid', 'first_name', 'surname'];
            @endphp
            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryFields);
                $isChecked = in_array($key, $selectedColumns) || $isMandatory;
              @endphp
              <div class="column-item" draggable="true" data-column="{{ $key }}" style="cursor:move;">
                <span style="cursor:move; margin-right:8px; font-size:16px; color:#666;">☰</span>
                <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
                <label for="col_{{ $key }}" style="cursor:pointer; flex:1; user-select:none;">{{ $label }}</label>
              </div>
            @endforeach
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn-cancel" onclick="closeColumnModal()">Cancel</button>
        <button class="btn-save" onclick="saveColumnSettings()">Save Settings</button>
      </div>
    </div>
  </div>

</div>

<script>
  let currentClientId = null;
  const lookupData = @json($lookupData);
  const selectedColumns = @json($selectedColumns);

  document.getElementById('addClientBtn').addEventListener('click', () => openClientModal('add'));
  document.getElementById('columnBtn').addEventListener('click', () => openColumnModal());
  document.getElementById('filterToggle').addEventListener('change', function() {
    const filtersVisible = this.checked;
    const columnFilters = document.querySelectorAll('.column-filter');
    
    columnFilters.forEach(filter => {
      if (filtersVisible) {
        filter.classList.add('visible');
        filter.style.display = 'block';
      } else {
        filter.classList.remove('visible');
        filter.style.display = 'none';
        filter.value = ''; // Clear filter values when hiding
        // Reset table rows visibility
        document.querySelectorAll('tbody tr').forEach(row => {
          row.style.display = '';
        });
      }
    });
  });
  
  // Initialize filter visibility based on toggle state
  document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle && filterToggle.checked) {
      document.querySelectorAll('.column-filter').forEach(filter => {
        filter.classList.add('visible');
        filter.style.display = 'block';
      });
    }
    
    // Ensure all fields are visible when Individual or Entity (Business/Company) is selected
    const clientTypeSelect = document.getElementById('client_type');
    if (clientTypeSelect) {
      clientTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        // Entity types: Business and Company are considered entities
        const entityTypes = ['Business', 'Company'];
        const shouldShowAllFields = selectedType === 'Individual' || entityTypes.includes(selectedType);
        
        if (shouldShowAllFields) {
          // Ensure all form fields are visible
          const allFields = document.querySelectorAll('.detail-row, .detail-section, .detail-section-body');
          allFields.forEach(field => {
            field.style.display = '';
            field.style.visibility = '';
            field.style.opacity = '';
          });
          
          // Also ensure all inputs, selects, and textareas are visible
          const allInputs = document.querySelectorAll('#clientForm input, #clientForm select, #clientForm textarea, #clientForm .detail-section');
          allInputs.forEach(input => {
            if (input.closest('.detail-section')) {
              input.closest('.detail-section').style.display = '';
            }
            input.style.display = '';
            input.style.visibility = '';
            input.disabled = false;
          });
        }
      });
    }
  });
  
  // Radio button selection highlighting
  document.querySelectorAll('.action-radio').forEach(radio => {
    radio.addEventListener('change', function() {
      // Remove previous selections
      document.querySelectorAll('.action-radio').forEach(r => {
        r.classList.remove('selected');
      });
      // Add selected class to current
      if (this.checked) {
        this.classList.add('selected');
      }
    });
  });
  

  const followUpBtn = document.getElementById('followUpBtn');
  const listAllBtn = document.getElementById('listAllBtn');
  
  if (followUpBtn) {
    followUpBtn.addEventListener('click', () => {
      window.location.href = '{{ route("clients.index") }}?follow_up=true';
    });
  }
  
  if (listAllBtn) {
    listAllBtn.addEventListener('click', () => {
      window.location.href = '{{ route("clients.index") }}';
    });
  }
  
  document.getElementById('closeBtn').addEventListener('click', () => {
    window.location.href = '{{ route("clients.index") }}';
  });

  async function openEditClient(id){
    try {
      const res = await fetch(`/clients/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) {
        const errorText = await res.text();
        throw new Error(`HTTP ${res.status}: ${errorText}`);
      }
      const client = await res.json();
      currentClientId = id;
      openClientModal('edit', client);
    } catch (e) {
      console.error(e);
      alert('Error loading client data: ' + e.message);
    }
  }

  // Open client details modal
  async function openClientDetailsModal(clientId) {
    try {
      const res = await fetch(`/clients/${clientId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}`);
      }
      const client = await res.json();
      currentClientId = clientId;
      populateClientDetailsModal(client);
      document.getElementById('clientDetailsModal').classList.add('show');
      document.body.style.overflow = 'hidden';
    } catch (e) {
      console.error(e);
      alert('Error loading client details: ' + e.message);
    }
  }

  // Populate client details modal with data
  function populateClientDetailsModal(client) {
    const content = document.getElementById('clientDetailsContent');
    if (!content) return;

    // Calculate age from DOB
    function calculateAge(dob) {
      if (!dob) return '';
      const birthDate = new Date(dob);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      return age;
    }

    // Format date
    function formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
    }

    // Calculate days until expiry (for ID expiry)
    function daysUntilExpiry(dateStr) {
      if (!dateStr) return '';
      const expiryDate = new Date(dateStr);
      const today = new Date();
      const diffTime = expiryDate - today;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return diffDays;
    }

    const dob = client.dob_dor ? formatDate(client.dob_dor) : '';
    const dobAge = client.dob_dor ? calculateAge(client.dob_dor) : '';
    const idExpiry = client.id_expiry_date ? formatDate(client.id_expiry_date) : '';
    const idExpiryDays = client.id_expiry_date ? daysUntilExpiry(client.id_expiry_date) : '';
    const photoUrl = client.image ? (client.image.startsWith('http') ? client.image : `/storage/${client.image}`) : '';

    // Column 1: Customer Details & Individual Details
    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">CUSTOMER DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Client Type</span>
            <div class="detail-value">${client.client_type || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">DOB/DOR</span>
            <div style="display:flex; gap:5px;">
              <div class="detail-value" style="flex:1;">${dob}</div>
              <div class="detail-value" style="width:50px;">${dobAge}</div>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">NIN/BCRN</span>
            <div class="detail-value">${client.nin_bcrn || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">ID Expiry Date</span>
            <div style="display:flex; gap:5px;">
              <div class="detail-value" style="flex:1;">${idExpiry}</div>
              <div class="detail-value" style="width:50px;">${idExpiryDays}</div>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Client Status</span>
            <div class="detail-value">${client.status || 'Active'}</div>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">INDIVIDUAL DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Salutation</span>
            <div class="detail-value">${client.salutation || '-'}</div>
          </div>
          <div class="detail-row" style="display:flex; flex-direction:row; align-items:flex-start; gap:10px;">
            <div style="flex:1; display:flex; flex-direction:column;">
              <span class="detail-label">First Name</span>
              <div class="detail-value" style="flex:1;">${client.first_name || '-'}</div>
            </div>
            ${photoUrl ? `<div style="display:flex; flex-direction:column;"><span class="detail-label" style="visibility:hidden;">Photo</span><img src="${photoUrl}" alt="Photo" class="detail-photo" style="cursor:pointer;" onclick="previewClientPhotoModal('${photoUrl}')"></div>` : ''}
          </div>
          <div class="detail-row">
            <span class="detail-label">Other Names</span>
            <div class="detail-value">${client.other_names || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Surname</span>
            <div class="detail-value">${client.surname || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Passport No</span>
            <div style="display:flex; gap:5px;">
              <div class="detail-value" style="flex:1;">${client.passport_no || '-'}</div>
              <button class="btn" style="background:#fff; color:#000; border:1px solid #ddd; padding:4px 8px; border-radius:2px; cursor:default; font-size:12px; width:60px;">SEY</button>
            </div>
          </div>
        </div>
      </div>
    `;

    // Column 2: Contact Details & Income Details
    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">CONTACT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Mobile No</span>
            <div class="detail-value">${client.mobile_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">On Wattsapp</span>
            <div class="detail-value checkbox">
              <input type="checkbox" ${client.wa ? 'checked' : ''} disabled>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Alternate No</span>
            <div class="detail-value">${client.alternate_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Email Address</span>
            <div class="detail-value">${client.email_address || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Contact Person</span>
            <div class="detail-value">${client.contact_person || '-'}</div>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">INCOME DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Occupation</span>
            <div class="detail-value">${client.occupation || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Income Source</span>
            <div class="detail-value">${client.income_source || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Employer</span>
            <div class="detail-value">${client.employer || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Monthly Income</span>
            <div class="detail-value">${client.monthly_income || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label"></span>
            <div class="detail-value"></div>
          </div>
        </div>
      </div>
    `;

    // Column 3: Address Details & Other Details
    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">ADDRESS DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">District</span>
            <div class="detail-value">${client.district || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Address</span>
            <div class="detail-value">${client.location || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Island</span>
            <div class="detail-value">${client.island || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Country</span>
            <div class="detail-value">${client.country || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">P.O. Box No</span>
            <div class="detail-value">${client.po_box_no || '-'}</div>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">OTHER DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Married</span>
            <div class="detail-value checkbox">
              <input type="checkbox" ${client.married ? 'checked' : ''} disabled>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Spouse's Name</span>
            <div class="detail-value">${client.spouses_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">PEP</span>
            <div style="display:flex; gap:5px; align-items:center;">
              <div class="detail-value checkbox" style="flex:0; min-width:auto;">
                <input type="checkbox" ${client.pep ? 'checked' : ''} disabled>
              </div>
              <button type="button" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:11px; white-space:nowrap;">PEP Details</button>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label"></span>
            <div class="detail-value">${client.pep_comment || ''}</div>
          </div>
        </div>
      </div>
    `;

    // Column 4: Registration Details & Insurables
    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header">REGISTRATION DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Sign Up Date</span>
            <div class="detail-value">${client.signed_up ? formatDate(client.signed_up) : '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Agency</span>
            <div class="detail-value">Keystone</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Agent</span>
            <div class="detail-value">${client.agent || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Source</span>
            <div class="detail-value">${client.source || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Source Name</span>
            <div class="detail-value">${client.source_name || '-'}</div>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">INSURABLES</div>
        <div class="detail-section-body">
          <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px; margin-bottom:12px;">
            <div class="detail-row" style="margin-bottom:0;">
              <span class="detail-label">Vehicle</span>
              <div class="detail-value checkbox">
                <input type="checkbox" ${client.has_vehicle ? 'checked' : ''} disabled>
              </div>
            </div>
            <div class="detail-row" style="margin-bottom:0;">
              <span class="detail-label">House</span>
              <div class="detail-value checkbox">
                <input type="checkbox" ${client.has_house ? 'checked' : ''} disabled>
              </div>
            </div>
            <div class="detail-row" style="margin-bottom:0;">
              <span class="detail-label">Business</span>
              <div class="detail-value checkbox">
                <input type="checkbox" ${client.has_business ? 'checked' : ''} disabled>
              </div>
            </div>
            <div class="detail-row" style="margin-bottom:0;">
              <span class="detail-label">Boat</span>
              <div class="detail-value checkbox">
                <input type="checkbox" ${client.has_boat ? 'checked' : ''} disabled>
              </div>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Notes</span>
            <textarea class="detail-value" style="min-height:40px; resize:vertical; width:100%; font-size:11px; padding:4px 6px;" readonly>${client.notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;

    // Load documents from documents table
    const documentsList = document.getElementById('clientDocumentsList');
    if (documentsList) {
      let docsHTML = '';
      if (client.documents && client.documents.length > 0) {
        client.documents.forEach(doc => {
          if (doc.file_path) {
            const fileExt = doc.format ? doc.format.toUpperCase() : (doc.file_path.split('.').pop().toUpperCase());
            const fileUrl = doc.file_path.startsWith('http') ? doc.file_path : `/storage/${doc.file_path}`;
            const isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
            const docName = doc.name || 'Document';
            docsHTML += `
              <div class="document-item" style="cursor:pointer;" onclick="previewUploadedDocument('${fileUrl}', '${fileExt}', '${docName}')">
                ${isImage ? `<img src="${fileUrl}" alt="${docName}" style="width:60px; height:60px; object-fit:cover; border-radius:4px;">` : `<div class="document-icon">${fileExt}</div>`}
                <div style="font-size:11px; text-align:center;">${docName}</div>
              </div>
            `;
          }
        });
      }
      documentsList.innerHTML = docsHTML || '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
    }

    // Set edit button action
    const editBtn = document.getElementById('editClientFromModalBtn');
    if (editBtn) {
      editBtn.onclick = function() {
        closeClientDetailsModal();
        openEditClient(currentClientId);
      };
    }

    // Tab navigation - make tabs clickable to navigate to respective pages
    document.querySelectorAll('.nav-tab').forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault();
        const tabType = this.getAttribute('data-tab');
        const clientId = currentClientId;
        
        if (!clientId) return;
        
        // Close the modal first
        closeClientDetailsModal();
        
        // Navigate to the appropriate page with client filter
        let url = '';
        switch(tabType) {
          case 'proposals':
            url = '{{ route("life-proposals.index") }}?client_id=' + clientId;
            break;
          case 'policies':
            url = '{{ route("policies.index") }}?client_id=' + clientId;
            break;
          case 'payments':
            url = '{{ route("payments.index") }}?client_id=' + clientId;
            break;
          case 'vehicles':
            url = '{{ route("vehicles.index") }}?client_id=' + clientId;
            break;
          case 'claims':
            url = '{{ route("claims.index") }}?client_id=' + clientId;
            break;
          case 'documents':
            url = '{{ route("documents.index") }}?client_id=' + clientId;
            break;
          default:
            return;
        }
        
        if (url) {
          window.location.href = url;
        }
      });
    });
  }

  function closeClientDetailsModal() {
    document.getElementById('clientDetailsModal').classList.remove('show');
    document.body.style.overflow = '';
  }

  // Photo upload handler
  async function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!currentClientId) {
      alert('No client selected');
      return;
    }

    // Validate passport photo dimensions before upload
    const img = new Image();
    const reader = new FileReader();
    
    reader.onload = async function(e) {
      img.onload = async function() {
        const width = img.width;
        const height = img.height;
        
        // Passport photo standard dimensions (in pixels at 300 DPI):
        // Square format: 600x600 pixels (2x2 inches) - most common
        // Rectangular format: 413x531 pixels (35x45 mm)
        // Allow some tolerance: ±50 pixels for width/height
        const minWidth = 350;
        const maxWidth = 650;
        const minHeight = 350;
        const maxHeight = 650;
        
        // Check if dimensions are within acceptable range
        if (width < minWidth || width > maxWidth || height < minHeight || height > maxHeight) {
          alert('Photo must be passport size (approximately 600x600 pixels or 413x531 pixels).\nCurrent dimensions: ' + width + 'x' + height + ' pixels.\nPlease upload a passport-size photo.');
          event.target.value = '';
          return;
        }
        
        // Check aspect ratio (should be close to 1:1 for square or 0.78:1 for rectangular)
        const aspectRatio = width / height;
        const squareRatio = 1.0; // 1:1 for square passport photos
        const rectRatio = 0.78; // 35:45 mm ratio
        const tolerance = 0.15; // Allow 15% tolerance
        
        const isSquare = Math.abs(aspectRatio - squareRatio) <= tolerance;
        const isRectangular = Math.abs(aspectRatio - rectRatio) <= tolerance;
        
        if (!isSquare && !isRectangular) {
          alert('Photo must have passport size aspect ratio (square 1:1 or rectangular 35:45mm).\nCurrent ratio: ' + aspectRatio.toFixed(2) + ':1\nPlease upload a passport-size photo.');
          event.target.value = '';
          return;
        }
        
        // If validation passes, proceed with upload
        const formData = new FormData();
        formData.append('photo', file);

        try {
          const response = await fetch(`/clients/${currentClientId}/upload-photo`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          });

          const result = await response.json();
          
          if (result.success) {
            // Reload client data to update the photo
            const clientRes = await fetch(`/clients/${currentClientId}`, {
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const client = await clientRes.json();
            populateClientDetailsModal(client);
            alert('Photo uploaded successfully!');
          } else {
            alert('Error uploading photo: ' + (result.message || 'Unknown error'));
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error uploading photo: ' + error.message);
        }

        // Reset input
        event.target.value = '';
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  // Document upload modal functions
  function openDocumentUploadModal() {
    if (!currentClientId) {
      alert('No client selected');
      return;
    }
    document.getElementById('documentUploadModal').classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeDocumentUploadModal() {
    document.getElementById('documentUploadModal').classList.remove('show');
    document.body.style.overflow = '';
    document.getElementById('documentUploadForm').reset();
    // Clear preview
    const previewContainer = document.getElementById('documentPreviewContainer');
    const previewContent = document.getElementById('documentPreviewContent');
    const previewInfo = document.getElementById('documentPreviewInfo');
    if (previewContainer) previewContainer.style.display = 'none';
    if (previewContent) previewContent.innerHTML = '';
    if (previewInfo) previewInfo.innerHTML = '';
  }

  // Preview document before upload
  function previewDocument(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('documentPreviewContainer');
    const previewContent = document.getElementById('documentPreviewContent');
    const previewInfo = document.getElementById('documentPreviewInfo');

    if (!file || !previewContainer || !previewContent || !previewInfo) return;

    previewContainer.style.display = 'block';
    previewContent.innerHTML = '';
    previewInfo.innerHTML = '';

    const fileType = file.type;
    const fileName = file.name;
    const fileSize = (file.size / 1024 / 1024).toFixed(2); // Size in MB

    // Show file info
    previewInfo.innerHTML = `<strong>File:</strong> ${fileName}<br><strong>Size:</strong> ${fileSize} MB<br><strong>Type:</strong> ${fileType || 'Unknown'}`;

    // Preview based on file type
    if (fileType.startsWith('image/')) {
      // Image preview
      const reader = new FileReader();
      reader.onload = function(e) {
        previewContent.innerHTML = `<img src="${e.target.result}" alt="Document Preview" style="max-width:100%; max-height:400px; border:1px solid #ddd; border-radius:4px;">`;
      };
      reader.readAsDataURL(file);
    } else if (fileType === 'application/pdf') {
      // PDF preview using embed
      const reader = new FileReader();
      reader.onload = function(e) {
        previewContent.innerHTML = `
          <div style="width:100%; text-align:center;">
            <embed src="${e.target.result}" type="application/pdf" width="100%" height="400px" style="border:1px solid #ddd; border-radius:4px;">
            <div style="margin-top:10px; color:#666; font-size:12px;">PDF Preview (scroll to view full document)</div>
          </div>
        `;
      };
      reader.readAsDataURL(file);
    } else {
      // For other file types (DOC, DOCX), show icon
      const fileExt = fileName.split('.').pop().toUpperCase();
      previewContent.innerHTML = `
        <div class="document-item" style="margin:0 auto;">
          <div class="document-icon" style="width:120px; height:120px; font-size:24px;">${fileExt}</div>
          <div style="font-size:12px; text-align:center; margin-top:10px; color:#666;">${fileName}</div>
        </div>
      `;
    }
  }

  // Document upload handler
  async function handleDocumentUpload() {
    const documentType = document.getElementById('documentType').value;
    const documentFile = document.getElementById('documentFile').files[0];

    if (!documentType) {
      alert('Please select a document type');
      return;
    }

    if (!documentFile) {
      alert('Please select a file');
      return;
    }

    if (!currentClientId) {
      alert('No client selected');
      return;
    }

    const formData = new FormData();
    formData.append('document', documentFile);
    formData.append('document_type', documentType);

    try {
      const response = await fetch(`/clients/${currentClientId}/upload-document`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const result = await response.json();
      
      if (result.success) {
        // Reload client data to update documents
        const clientRes = await fetch(`/clients/${currentClientId}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const client = await clientRes.json();
        
        // Update documents in both modals
        updateDocumentsList(client);
        
        // If client details modal is open, refresh it
        const clientDetailsModal = document.getElementById('clientDetailsModal');
        if (clientDetailsModal && clientDetailsModal.classList.contains('show')) {
          populateClientDetailsModal(client);
        }
        
        closeDocumentUploadModal();
        alert('Document uploaded successfully!');
      } else {
        alert('Error uploading document: ' + (result.message || 'Unknown error'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error uploading document: ' + error.message);
    }
  }

  // Update documents list in Edit Client modal
  function updateDocumentsList(client) {
    const editDocumentsList = document.getElementById('editClientDocumentsList');
    if (editDocumentsList) {
      let docsHTML = '';
      // Load from documents table
      if (client.documents && client.documents.length > 0) {
        client.documents.forEach(doc => {
          if (doc.file_path) {
            const fileExt = doc.format ? doc.format.toUpperCase() : (doc.file_path.split('.').pop().toUpperCase());
            const fileUrl = doc.file_path.startsWith('http') ? doc.file_path : `/storage/${doc.file_path}`;
            const isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
            const docName = doc.name || 'Document';
            docsHTML += `
              <div class="document-item" style="cursor:pointer;" onclick="previewUploadedDocument('${fileUrl}', '${fileExt}', '${docName}')">
                ${isImage ? `<img src="${fileUrl}" alt="${docName}" style="width:60px; height:60px; object-fit:cover; border-radius:4px;">` : `<div class="document-icon">${fileExt}</div>`}
                <div style="font-size:11px; text-align:center;">${docName}</div>
              </div>
            `;
          }
        });
      }
      editDocumentsList.innerHTML = docsHTML || '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
    }
  }

  function editClientFromModal() {
    if (currentClientId) {
      closeClientDetailsModal();
      openEditClient(currentClientId);
    }
  }

  // Calculate age from DOB
  function calculateAgeFromDOB() {
    const dobInput = document.getElementById('dob_dor');
    const ageInput = document.getElementById('dob_age');
    if (dobInput && ageInput && dobInput.value) {
      const birthDate = new Date(dobInput.value);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      ageInput.value = age;
    } else if (ageInput) {
      ageInput.value = '';
    }
  }

  // Calculate days until ID expiry
  function calculateIDExpiryDays() {
    const expiryInput = document.getElementById('id_expiry_date');
    const daysInput = document.getElementById('id_expiry_days');
    if (expiryInput && daysInput && expiryInput.value) {
      const expiryDate = new Date(expiryInput.value);
      const today = new Date();
      const diffTime = expiryDate - today;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      daysInput.value = diffDays;
    } else if (daysInput) {
      daysInput.value = '';
    }
  }

  // Preview client photo and validate passport size
  function previewClientPhoto(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('clientPhotoImg');
    const previewContainer = document.getElementById('clientPhotoPreview');
    const imageInput = event.target;
    
    if (file && preview) {
      // Validate passport photo dimensions
      const img = new Image();
      const reader = new FileReader();
      
      reader.onload = function(e) {
        img.onload = function() {
          const width = img.width;
          const height = img.height;
          
          // Passport photo standard dimensions (in pixels at 300 DPI):
          // Square format: 600x600 pixels (2x2 inches) - most common
          // Rectangular format: 413x531 pixels (35x45 mm)
          // Allow some tolerance: ±50 pixels for width/height
          const minWidth = 350;
          const maxWidth = 650;
          const minHeight = 350;
          const maxHeight = 650;
          
          // Check if dimensions are within acceptable range
          if (width < minWidth || width > maxWidth || height < minHeight || height > maxHeight) {
            alert('Photo must be passport size (approximately 600x600 pixels or 413x531 pixels).\nCurrent dimensions: ' + width + 'x' + height + ' pixels.\nPlease upload a passport-size photo.');
            imageInput.value = '';
            preview.src = '';
            preview.style.display = 'none';
            if (previewContainer.querySelector('span')) {
              previewContainer.querySelector('span').style.display = 'block';
            }
            return;
          }
          
          // Check aspect ratio (should be close to 1:1 for square or 0.78:1 for rectangular)
          const aspectRatio = width / height;
          const squareRatio = 1.0; // 1:1 for square passport photos
          const rectRatio = 0.78; // 35:45 mm ratio
          const tolerance = 0.15; // Allow 15% tolerance
          
          const isSquare = Math.abs(aspectRatio - squareRatio) <= tolerance;
          const isRectangular = Math.abs(aspectRatio - rectRatio) <= tolerance;
          
          if (!isSquare && !isRectangular) {
            alert('Photo must have passport size aspect ratio (square 1:1 or rectangular 35:45mm).\nCurrent ratio: ' + aspectRatio.toFixed(2) + ':1\nPlease upload a passport-size photo.');
            imageInput.value = '';
            preview.src = '';
            preview.style.display = 'none';
            if (previewContainer.querySelector('span')) {
              previewContainer.querySelector('span').style.display = 'block';
            }
            return;
          }
          
          // If validation passes, show preview
          preview.src = e.target.result;
          preview.style.display = 'block';
          if (previewContainer.querySelector('span')) {
            previewContainer.querySelector('span').style.display = 'none';
          }
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  function openClientModal(mode, client = null){
    const modal = document.getElementById('clientModal');
    const form = document.getElementById('clientForm');
    const formMethod = document.getElementById('clientFormMethod');
    const deleteBtn = document.getElementById('clientDeleteBtn');

    // Ensure all fields are visible when modal opens
    const allFields = document.querySelectorAll('#clientForm .detail-row, #clientForm .detail-section, #clientForm .detail-section-body');
    allFields.forEach(field => {
      field.style.display = '';
      field.style.visibility = '';
      field.style.opacity = '';
    });
    const allInputs = document.querySelectorAll('#clientForm input, #clientForm select, #clientForm textarea');
    allInputs.forEach(input => {
      input.style.display = '';
      input.style.visibility = '';
    });

    if (mode === 'add') {
      form.action = '{{ route("clients.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      form.reset();
      // Make photo required for new clients
      const imageInput = document.getElementById('image');
      if (imageInput) imageInput.required = true;
      // Clear checkboxes
      document.getElementById('married').checked = false;
      document.getElementById('pep').checked = false;
      document.getElementById('wa').checked = false;
      document.getElementById('has_vehicle').checked = false;
      document.getElementById('has_house').checked = false;
      document.getElementById('has_business').checked = false;
      document.getElementById('has_boat').checked = false;
      // Show Alternate No field by default (since wa is unchecked)
      const alternateNoRow = document.getElementById('alternate_no_row');
      if (alternateNoRow) {
        alternateNoRow.style.display = '';
      }
      // Clear photo preview
      const photoImg = document.getElementById('clientPhotoImg');
      const photoSpan = document.getElementById('clientPhotoPreview').querySelector('span');
      if (photoImg) photoImg.style.display = 'none';
      if (photoSpan) photoSpan.style.display = 'block';
      // Clear calculated fields
      document.getElementById('dob_age').value = '';
      document.getElementById('id_expiry_days').value = '';
      // Clear documents list
      const editDocumentsList = document.getElementById('editClientDocumentsList');
      if (editDocumentsList) editDocumentsList.innerHTML = '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
    } else {
      form.action = `/clients/${currentClientId}`;
      formMethod.innerHTML = `@method('PUT')`;
      deleteBtn.style.display = 'inline-block';

      const fields = ['salutation','first_name','other_names','surname','client_type','nin_bcrn','dob_dor','id_expiry_date','passport_no','mobile_no','alternate_no','email_address','occupation','employer','income_source','monthly_income','source','source_name','agent','agency','status','signed_up','location','district','island','country','po_box_no','spouses_name','contact_person','pep_comment','notes'];
      fields.forEach(k => {
        const el = document.getElementById(k);
        if (!el) return;
        if (el.type === 'checkbox') {
          el.checked = !!client[k];
        } else if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
          if (el.type === 'date' && client[k]) {
            // Format date for date inputs (YYYY-MM-DD)
            const date = new Date(client[k]);
            el.value = date.toISOString().split('T')[0];
          } else {
            el.value = client[k] ?? '';
          }
        }
      });
      document.getElementById('married').checked = !!client.married;
      document.getElementById('pep').checked = !!client.pep;
      document.getElementById('wa').checked = !!client.wa;
      document.getElementById('has_vehicle').checked = !!client.has_vehicle;
      document.getElementById('has_house').checked = !!client.has_house;
      document.getElementById('has_business').checked = !!client.has_business;
      document.getElementById('has_boat').checked = !!client.has_boat;
      
      // Set existing image if present
      const imageInput = document.getElementById('image');
      if (client.image) {
        document.getElementById('existing_image').value = client.image;
        const photoImg = document.getElementById('clientPhotoImg');
        const photoSpan = document.getElementById('clientPhotoPreview').querySelector('span');
        if (photoImg) {
          photoImg.src = client.image.startsWith('http') ? client.image : `/storage/${client.image}`;
          photoImg.style.display = 'block';
          if (photoSpan) photoSpan.style.display = 'none';
        }
        // Photo not required if existing image exists
        if (imageInput) imageInput.required = false;
      } else {
        // Photo required if no existing image
        if (imageInput) imageInput.required = true;
      }

      // Update documents list
      updateDocumentsList(client);

      // Calculate age and expiry days
      calculateAgeFromDOB();
      calculateIDExpiryDays();
    }

    // Add event listeners for calculations
    const dobInput = document.getElementById('dob_dor');
    const expiryInput = document.getElementById('id_expiry_date');
    if (dobInput) {
      dobInput.removeEventListener('change', calculateAgeFromDOB);
      dobInput.addEventListener('change', calculateAgeFromDOB);
    }
    if (expiryInput) {
      expiryInput.removeEventListener('change', calculateIDExpiryDays);
      expiryInput.addEventListener('change', calculateIDExpiryDays);
    }

    // Toggle Alternate No field visibility based on "On Wattsapp" checkbox
    const waCheckbox = document.getElementById('wa');
    const alternateNoRow = document.getElementById('alternate_no_row');
    if (waCheckbox && alternateNoRow) {
      // Function to toggle visibility
      const toggleAlternateNo = function() {
        if (this.checked) {
          // Hide Alternate No if On Wattsapp is checked
          alternateNoRow.style.display = 'none';
        } else {
          // Show Alternate No if On Wattsapp is unchecked
          alternateNoRow.style.display = '';
        }
      };
      
      // Remove existing listener and add new one
      waCheckbox.removeEventListener('change', toggleAlternateNo);
      waCheckbox.addEventListener('change', toggleAlternateNo);
      
      // Set initial state based on checkbox
      toggleAlternateNo.call(waCheckbox);
    }

    // Attach client type change listener to ensure all fields remain visible for Individual and Entity types
    const clientTypeSelect = document.getElementById('client_type');
    if (clientTypeSelect) {
      // Remove existing listeners by cloning
      const newClientTypeSelect = clientTypeSelect.cloneNode(true);
      clientTypeSelect.parentNode.replaceChild(newClientTypeSelect, clientTypeSelect);
      
      newClientTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        // Entity types: Business and Company are considered entities
        const entityTypes = ['Business', 'Company'];
        const shouldShowAllFields = selectedType === 'Individual' || entityTypes.includes(selectedType);
        
        if (shouldShowAllFields) {
          // Ensure all form fields are visible
          const allFields = document.querySelectorAll('#clientForm .detail-row, #clientForm .detail-section, #clientForm .detail-section-body');
          allFields.forEach(field => {
            field.style.display = '';
            field.style.visibility = '';
            field.style.opacity = '';
          });
          
          // Also ensure all inputs, selects, and textareas are visible
          const allInputs = document.querySelectorAll('#clientForm input, #clientForm select, #clientForm textarea');
          allInputs.forEach(input => {
            if (input.closest('.detail-section')) {
              input.closest('.detail-section').style.display = '';
            }
            input.style.display = '';
            input.style.visibility = '';
          });
        }
      });
      
      // Trigger on initial load if a type is already selected
      if (newClientTypeSelect.value === 'Individual' || ['Business', 'Company'].includes(newClientTypeSelect.value)) {
        newClientTypeSelect.dispatchEvent(new Event('change'));
      }
    }

    document.body.style.overflow = 'hidden';
    modal.classList.add('show');
  }

  function closeClientModal(){
    document.getElementById('clientModal').classList.remove('show');
    currentClientId = null;
    document.body.style.overflow = '';
  }

  function deleteClient(){
    if (!currentClientId) return;
    if (!confirm('Delete this client?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/clients/${currentClientId}`;
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }

  // Column modal functions
  function openColumnModal(){
    // Mandatory fields that should always be checked
    const mandatoryFields = ['client_name', 'client_type', 'mobile_no', 'source', 'status', 'signed_up', 'clid', 'first_name', 'surname'];
    
    document.getElementById('tableResponsive').classList.add('no-scroll');
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      // Always check mandatory fields, otherwise check if in selectedColumns
      cb.checked = mandatoryFields.includes(cb.value) || selectedColumns.includes(cb.value);
    });
    document.body.style.overflow = 'hidden';
    document.getElementById('columnModal').classList.add('show');
    // Initialize drag and drop after modal is shown
    setTimeout(initDragAndDrop, 100);
  }
  function closeColumnModal(){
    document.getElementById('tableResponsive').classList.remove('no-scroll');
    document.getElementById('columnModal').classList.remove('show');
    document.body.style.overflow = '';
  }
  function selectAllColumns(){ 
    const mandatoryFields = ['client_name', 'client_type', 'mobile_no', 'source', 'status', 'signed_up', 'clid', 'first_name', 'surname'];
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      cb.checked = true;
    });
  }
  function deselectAllColumns(){ 
    const mandatoryFields = ['client_name', 'client_type', 'mobile_no', 'source', 'status', 'signed_up', 'clid', 'first_name', 'surname'];
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      // Don't uncheck mandatory fields
      if (!mandatoryFields.includes(cb.value)) {
        cb.checked = false;
      }
    });
  }

  function saveColumnSettings(){
    // Mandatory fields that should always be included
    const mandatoryFields = ['client_name', 'client_type', 'mobile_no', 'source', 'status', 'signed_up', 'clid', 'first_name', 'surname'];
    
    // Get order from DOM - this preserves the drag and drop order
    const items = Array.from(document.querySelectorAll('#columnSelection .column-item'));
    const order = items.map(item => item.dataset.column);
    const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n=>n.value);
    
    // Ensure mandatory fields are always included
    mandatoryFields.forEach(field => {
      if (!checked.includes(field)) {
        checked.push(field);
      }
    });
    
    // Maintain order of checked items based on DOM order (drag and drop order)
    const orderedChecked = order.filter(col => checked.includes(col));
    
    const form = document.getElementById('columnForm');
    const existing = form.querySelectorAll('input[name="columns[]"]'); 
    existing.forEach(e=>e.remove());
    
    // Add columns in the order they appear in the DOM (after drag and drop)
    orderedChecked.forEach(c => {
      const i = document.createElement('input'); 
      i.type='hidden'; 
      i.name='columns[]'; 
      i.value=c; 
      form.appendChild(i);
    });
    
    form.submit();
  }

  // Drag and drop functionality
  let draggedElement = null;
  let dragOverElement = null;
  
  // Initialize drag and drop when column modal opens
  function initDragAndDrop() {
    const columnSelection = document.getElementById('columnSelection');
    if (!columnSelection) return;
    
    // Make all column items draggable
    const columnItems = columnSelection.querySelectorAll('.column-item');
    
    columnItems.forEach(item => {
      // Drag start
      item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', ''); // Required for Firefox
        // Create a ghost image
        const dragImage = this.cloneNode(true);
        dragImage.style.opacity = '0.5';
        document.body.appendChild(dragImage);
        e.dataTransfer.setDragImage(dragImage, 0, 0);
        setTimeout(() => document.body.removeChild(dragImage), 0);
      });
      
      // Drag end
      item.addEventListener('dragend', function(e) {
        this.classList.remove('dragging');
        if (dragOverElement) {
          dragOverElement.classList.remove('drag-over');
          dragOverElement = null;
        }
        draggedElement = null;
      });
      
      // Drag over
      item.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        
        if (draggedElement && this !== draggedElement) {
          // Remove drag-over class from previous element
          if (dragOverElement && dragOverElement !== this) {
            dragOverElement.classList.remove('drag-over');
          }
          
          // Add drag-over class to current element
          this.classList.add('drag-over');
          dragOverElement = this;
          
          const rect = this.getBoundingClientRect();
          const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
          
          if (next) {
            if (this.nextSibling && this.nextSibling !== draggedElement) {
              this.parentNode.insertBefore(draggedElement, this.nextSibling);
            } else if (!this.nextSibling) {
              this.parentNode.appendChild(draggedElement);
            }
          } else {
            if (this.previousSibling !== draggedElement) {
              this.parentNode.insertBefore(draggedElement, this);
            }
          }
        }
      });
      
      // Drag leave
      item.addEventListener('dragleave', function(e) {
        // Only remove if we're not entering a child element
        if (!this.contains(e.relatedTarget)) {
          this.classList.remove('drag-over');
          if (dragOverElement === this) {
            dragOverElement = null;
          }
        }
      });
      
      // Drop
      item.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('drag-over');
        dragOverElement = null;
        return false;
      });
    });
  }

  // Preview uploaded document in modal
  function previewUploadedDocument(fileUrl, fileExt, documentName) {
    // Create preview modal
    let previewModal = document.getElementById('documentPreviewModal');
    if (!previewModal) {
      previewModal = document.createElement('div');
      previewModal.id = 'documentPreviewModal';
      previewModal.className = 'modal';
      previewModal.innerHTML = `
        <div class="modal-content" style="max-width:90%; max-height:90vh; overflow:auto;">
          <div class="modal-header">
            <h4>${documentName}</h4>
            <button type="button" class="modal-close" onclick="closeDocumentPreviewModal()">×</button>
          </div>
          <div class="modal-body" style="text-align:center; padding:20px;">
            <div id="uploadedDocumentPreview"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeDocumentPreviewModal()">Close</button>
          </div>
        </div>
      `;
      document.body.appendChild(previewModal);
    }

    const previewContent = document.getElementById('uploadedDocumentPreview');
    const isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
    const isPDF = fileExt === 'PDF';

    if (isImage) {
      previewContent.innerHTML = `<img src="${fileUrl}" alt="${documentName}" style="max-width:100%; max-height:70vh; border:1px solid #ddd; border-radius:4px;">`;
    } else if (isPDF) {
      previewContent.innerHTML = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="600px" style="border:1px solid #ddd; border-radius:4px;">`;
    } else {
      previewContent.innerHTML = `
        <div style="padding:40px;">
          <div class="document-icon" style="width:120px; height:120px; font-size:32px; margin:0 auto;">${fileExt}</div>
          <div style="margin-top:20px; font-size:16px; color:#666;">${documentName}</div>
          <div style="margin-top:10px;">
            <a href="${fileUrl}" target="_blank" class="btn-save" style="display:inline-block; text-decoration:none; padding:8px 16px;">Download</a>
          </div>
        </div>
      `;
    }

    previewModal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeDocumentPreviewModal() {
    const previewModal = document.getElementById('documentPreviewModal');
    if (previewModal) {
      previewModal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  // Preview client photo in modal
  function previewClientPhotoModal(photoUrl) {
    let photoModal = document.getElementById('clientPhotoPreviewModal');
    if (!photoModal) {
      photoModal = document.createElement('div');
      photoModal.id = 'clientPhotoPreviewModal';
      photoModal.className = 'modal';
      photoModal.innerHTML = `
        <div class="modal-content" style="max-width:90%; max-height:90vh; overflow:auto; text-align:center;">
          <div class="modal-header">
            <h4>Client Photo</h4>
            <button type="button" class="modal-close" onclick="closeClientPhotoPreviewModal()">×</button>
          </div>
          <div class="modal-body" style="padding:20px; text-align:center;">
            <img src="${photoUrl}" alt="Client Photo" style="max-width:100%; max-height:70vh; border:1px solid #ddd; border-radius:4px; object-fit:contain;">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeClientPhotoPreviewModal()">Close</button>
          </div>
        </div>
      `;
      document.body.appendChild(photoModal);
    } else {
      const img = photoModal.querySelector('img');
      if (img) img.src = photoUrl;
    }

    photoModal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeClientPhotoPreviewModal() {
    const photoModal = document.getElementById('clientPhotoPreviewModal');
    if (photoModal) {
      photoModal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  // Close modals on ESC or backdrop
  document.addEventListener('keydown', e => { 
    if (e.key === 'Escape') { 
      closeClientModal(); 
      closeColumnModal(); 
      closeClientDetailsModal();
      closeDocumentUploadModal();
      closeDocumentPreviewModal();
      closeClientPhotoPreviewModal();
    } 
  });
  document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { 
      if (e.target === m) { 
        m.classList.remove('show'); 
        document.body.style.overflow = ''; 
        if (m.id === 'documentUploadModal') {
          document.getElementById('documentUploadForm').reset();
        }
      } 
    });
  });

  // Simple validation
  document.getElementById('clientForm').addEventListener('submit', function(e){
    const req = this.querySelectorAll('[required]');
    let ok = true;
    req.forEach(f => { if (!String(f.value||'').trim()) { ok = false; f.style.borderColor='red'; } else { f.style.borderColor=''; } });
    if (!ok) { e.preventDefault(); alert('Please fill required fields'); }
  });

  // Toggle scrollbar helper for responsive table
  function toggleTableScroll() {
    const table = document.getElementById('clientsTable');
    const wrapper = document.getElementById('tableResponsive');
    if (!table || !wrapper) return;
    const hasHorizontalOverflow = table.offsetWidth > wrapper.offsetWidth;
    const hasVerticalOverflow = table.offsetHeight > wrapper.offsetHeight;
    wrapper.classList.toggle('no-scroll', !hasHorizontalOverflow && !hasVerticalOverflow);
  }
  window.addEventListener('load', toggleTableScroll);
  window.addEventListener('resize', toggleTableScroll);

  // Column filter functionality - apply all filters together
  function applyFilters() {
    const rows = document.querySelectorAll('tbody tr');
    const activeFilters = {};
    
    // Collect all active filter values
    document.querySelectorAll('.column-filter.visible').forEach(filter => {
      const column = filter.dataset.column;
      const value = filter.value.trim().toLowerCase();
      if (value) {
        activeFilters[column] = value;
      }
    });
    
    // Apply filters to rows
    rows.forEach(row => {
      let shouldShow = true;
      
      // Check if row matches all active filters
      for (const [column, filterValue] of Object.entries(activeFilters)) {
        const cell = row.querySelector(`td[data-column="${column}"]`);
        if (cell) {
          const cellText = cell.textContent.toLowerCase();
          if (!cellText.includes(filterValue)) {
            shouldShow = false;
            break;
          }
        } else {
          shouldShow = false;
          break;
        }
      }
      
      row.style.display = shouldShow ? '' : 'none';
    });
    
    // Update records count
    const visibleRows = Array.from(document.querySelectorAll('tbody tr')).filter(row => {
      return row.style.display !== 'none' && !row.style.display.includes('none');
    }).length;
    const recordsFound = document.querySelector('.records-found');
    if (recordsFound && Object.keys(activeFilters).length > 0) {
      const total = {{ $clients->total() }};
      recordsFound.textContent = `Records Found - ${visibleRows} of ${total} (filtered)`;
    } else if (recordsFound) {
      recordsFound.textContent = `Records Found - {{ $clients->total() }}`;
    }
  }
  
  // Print table function - creates a new print-friendly table
  function printTable() {
    const table = document.getElementById('clientsTable');
    if (!table) return;
    
    // Get table headers - preserve order
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    headerCells.forEach(th => {
      let headerText = '';
      // Get text, excluding filter input
      const clone = th.cloneNode(true);
      const filterInput = clone.querySelector('.column-filter');
      if (filterInput) filterInput.remove();
      headerText = clone.textContent.trim();
      // Handle bell icon column
      if (clone.querySelector('svg')) {
        headerText = '🔔'; // Bell icon
      }
      if (headerText) {
        headers.push(headerText);
      }
    });
    
    // Get table rows data
    const rows = [];
    const tableRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    tableRows.forEach(row => {
      if (row.style.display === 'none') return; // Skip hidden rows
      
      const cells = [];
      const rowCells = row.querySelectorAll('td');
      rowCells.forEach((cell) => {
        let cellContent = '';
        
        // Handle notification column (bell-cell)
        if (cell.classList.contains('bell-cell')) {
          const radio = cell.querySelector('input[type="radio"]');
          if (radio && radio.checked) {
            cellContent = '●'; // Filled circle for checked
          } else {
            cellContent = '○'; // Empty circle for unchecked
          }
        } 
        // Handle action column
        else if (cell.classList.contains('action-cell')) {
          const expandIcon = cell.querySelector('.action-expand');
          const clockIcon = cell.querySelector('.action-clock');
          const ellipsis = cell.querySelector('.action-ellipsis');
          const icons = [];
          if (expandIcon) icons.push('⤢');
          if (clockIcon) icons.push('🕐');
          if (ellipsis) icons.push('⋯');
          cellContent = icons.join(' ');
        } 
        // Handle checkbox cells
        else if (cell.classList.contains('checkbox-cell')) {
          const checkbox = cell.querySelector('input[type="checkbox"]');
          cellContent = checkbox && checkbox.checked ? '✓' : '';
        } 
        // Handle regular cells
        else {
          // Get text content, handling links
          const link = cell.querySelector('a');
          if (link) {
            cellContent = link.textContent.trim();
          } else {
            cellContent = cell.textContent.trim();
          }
        }
        
        cells.push(cellContent || '-');
      });
      rows.push(cells);
    });
    
    // Escape HTML to prevent XSS and syntax issues
    function escapeHtml(text) {
      if (!text) return '';
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    
    // Build headers HTML
    const headersHTML = headers.map(h => '<th>' + escapeHtml(h) + '</th>').join('');
    
    // Build rows HTML
    const rowsHTML = rows.map(row => {
      const cellsHTML = row.map(cell => {
        const cellText = escapeHtml(String(cell || '-'));
        return '<td>' + cellText + '</td>';
      }).join('');
      return '<tr>' + cellsHTML + '</tr>';
    }).join('');
    
    // Create print window with minimal delay
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    const printHTML = '<!DOCTYPE html>' +
      '<html>' +
      '<head>' +
      '<title>Clients - Print</title>' +
      '<style>' +
      '@page { margin: 1cm; size: A4 landscape; }' +
      'html, body { margin: 0; padding: 0; background: #fff !important; }' +
      'body { font-family: Arial, sans-serif; font-size: 10px; }' +
      'table { width: 100%; border-collapse: collapse; page-break-inside: auto; }' +
      'thead { display: table-header-group; }' +
      'thead th { background-color: #000 !important; color: #fff !important; padding: 8px 5px; text-align: left; border: 1px solid #333; font-weight: normal; -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
      'tbody tr { page-break-inside: avoid; border-bottom: 1px solid #ddd; }' +
      'tbody tr:nth-child(even) { background-color: #f8f8f8; }' +
      'tbody td { padding: 6px 5px; border: 1px solid #ddd; white-space: nowrap; }' +
      '</style>' +
      '</head>' +
      '<body>' +
      '<table>' +
      '<thead><tr>' + headersHTML + '</tr></thead>' +
      '<tbody>' + rowsHTML + '</tbody>' +
      '</table>' +
      '<scr' + 'ipt>' +
      'window.onload = function() {' +
      '  setTimeout(function() {' +
      '    window.print();' +
      '  }, 100);' +
      '};' +
      'window.onafterprint = function() {' +
      '  window.close();' +
      '};' +
      '</scr' + 'ipt>' +
      '</body>' +
      '</html>';
    
    if (printWindow) {
      printWindow.document.open();
      printWindow.document.write(printHTML);
      printWindow.document.close();
    }
  }
  
  // Add event listeners to all column filters
  document.addEventListener('DOMContentLoaded', function() {
    // Print button event listener
    const printBtn = document.getElementById('printBtn');
    if (printBtn) {
      printBtn.addEventListener('click', function() {
        printTable();
      });
    }
    
    document.querySelectorAll('.column-filter').forEach(filter => {
      filter.addEventListener('input', function() {
        applyFilters();
      });
    });
    
    // Initialize filter visibility
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle && filterToggle.checked) {
      document.querySelectorAll('.column-filter').forEach(filter => {
        filter.classList.add('visible');
        filter.style.display = 'block';
      });
    }
  });

</script>

@endsection
