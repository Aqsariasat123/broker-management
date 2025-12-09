@extends('layouts.app')
@section('content')

@include('partials.table-styles')

<style>
    * { box-sizing: border-box; }
    .dashboard { padding-left:0 !important; }
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
    .btn-archived { background:#2d2d2d; color:#fff; border-color:#2d2d2d; }
    .btn-archived.active { background:#f3742a; border-color:#f3742a; }
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
    tbody tr.archived-row { background:#fff3cd !important; }
    tbody tr.archived-row td { background:#fff3cd !important; }
    tbody td { padding:8px 5px; border-right:1px solid #ddd; white-space:nowrap; vertical-align:middle; font-size:12px; }
    tbody td:last-child { border-right:none; }
    .action-cell { display:flex; align-items:center; gap:10px; padding:8px; }
    .action-expand { width:22px; height:22px; cursor:pointer; display:inline-block; }
    .icon-expand { cursor:pointer; color:black; text-align:center; width:20px; }
    .btn-action { padding:2px 6px; font-size:11px; margin:1px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:2px; display:inline-block; }
    .badge-status { font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; }
    /* Modal styles */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; }
    .modal.show { display:flex; }
    .modal-content { background:#fff; border-radius:6px; width:92%; max-width:1100px; max-height:calc(100vh - 40px); overflow:auto; box-shadow:0 4px 6px rgba(0,0,0,.1); padding:0; }
    .modal-header { padding:12px 15px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; background:white; }
    .modal-body { padding:15px; }
    .modal-close { background:none; border:none; font-size:18px; cursor:pointer; color:#666; }
    .modal-footer { padding:12px 15px; border-top:1px solid #ddd; display:flex; justify-content:flex-end; gap:8px; background:#f9f9f9; }
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; margin-bottom: 4px; font-weight: bold; font-size: 13px; }
    .form-control { width: 100%; padding: 6px 8px; border: 1px solid #ccc; border-radius: 2px; font-size: 13px; }
    .form-row { display: flex; gap: 10px; margin-bottom: 12px; flex-wrap: wrap; align-items: flex-start; }
    .form-row .form-group { flex: 0 0 calc((100% - 20px) / 3); margin-bottom: 0; }
    .btn-save { background: #007bff; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; }
    .btn-cancel { background: #6c757d; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; }
    .btn-delete { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; }
    .alert { padding: 8px 12px; margin-bottom: 12px; border-radius: 2px; font-size: 13px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    /* Column Selection Styles */
    .column-selection { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; margin-bottom: 15px; }
    .column-item { display: flex; align-items: center; gap: 8px; padding: 6px 8px; border: 1px solid #ddd; border-radius: 2px; cursor: move; transition: all 0.2s; }
    .column-item:hover { background: #f5f5f5; }
    .column-item.selected { background: #007bff; color: white; border-color: #007bff; }
    .column-item input[type="checkbox"] { margin: 0; }
    .column-actions { display: flex; gap: 8px; margin-bottom: 15px; }
    .btn-select-all { background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; font-size: 12px; }
    .btn-deselect-all { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 2px; cursor: pointer; font-size: 12px; }
    /* Drag and drop styles */
    .column-item.dragging { opacity:0.5; background:#e3f2fd; border-color:#2196F3; }
    .column-item.drag-over { border-top:2px solid #2196F3; }
    @media (max-width: 768px) { .form-row .form-group { flex: 0 0 calc((100% - 20px) / 2); } }
  </style>

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('contacts');
  $selectedColumns = session('contact_columns', $config['default_columns'] ?? []);
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Contacts Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Contacts Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Contacts</h3>
        <div class="records-found">Records Found - {{ $contacts->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            <button class="btn btn-archived" id="archivedOnlyBtn" type="button">Archived Only</button>
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addContactBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="contactsTable">
        <thead>
          <tr>
            <th>Action</th>
            @foreach($selectedColumns as $col)
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($contacts as $contact)
            <tr class="{{ $contact->status === 'Archived' ? 'archived-row' : '' }}">
              <td class="action-cell">
                <svg class="action-expand" onclick="openContactDetails({{ $contact->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'contact_name')
                  <td data-column="contact_name">
                    <a href="javascript:void(0)" onclick="openContactDetails({{ $contact->id }})" style="color:#007bff; text-decoration:underline;">{{ $contact->contact_name }}</a>
                  </td>
                @elseif($col == 'contact_id')
                  <td data-column="contact_id">
                    <a href="javascript:void(0)" onclick="openContactDetails({{ $contact->id }})" style="color:#007bff; text-decoration:underline;">{{ $contact->contact_id }}</a>
                  </td>
                @elseif($col == 'contact_no')
                  <td data-column="contact_no">{{ $contact->contact_no ?? '##########' }}</td>
                @elseif($col == 'type')
                  <td data-column="type">{{ $contact->type }}</td>
                @elseif($col == 'occupation')
                  <td data-column="occupation">{{ $contact->occupation ?? '-' }}</td>
                @elseif($col == 'employer')
                  <td data-column="employer">{{ $contact->employer ?? '-' }}</td>
                @elseif($col == 'acquired')
                  <td data-column="acquired">{{ $contact->acquired ? $contact->acquired->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'source')
                  <td data-column="source">{{ $contact->source }}</td>
                @elseif($col == 'status')
                  <td data-column="status"><span class="badge-status" style="background:{{ $contact->status == 'Archived' ? '#343a40' : ($contact->status=='Proposal Made' ? '#28a745' : ($contact->status=='In Discussion' ? '#ffc107' : '#6c757d')) }}">{{ $contact->status }}</span></td>
                @elseif($col == 'rank')
                  <td data-column="rank">{{ $contact->rank ?? '-' }}</td>
                @elseif($col == 'first_contact')
                  <td data-column="first_contact">{{ $contact->first_contact ? $contact->first_contact->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'next_follow_up')
                  <td data-column="next_follow_up">{{ $contact->next_follow_up ? $contact->next_follow_up->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'coid')
                  <td data-column="coid">{{ $contact->coid ?? '##########' }}</td>
                @elseif($col == 'dob')
                  <td data-column="dob">{{ $contact->dob ? $contact->dob->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'salutation')
                  <td data-column="salutation">{{ $contact->salutation }}</td>
                @elseif($col == 'source_name')
                  <td data-column="source_name">{{ $contact->source_name ?? '-' }}</td>
                @elseif($col == 'agency')
                  <td data-column="agency">{{ $contact->agency ?? '-' }}</td>
                @elseif($col == 'agent')
                  <td data-column="agent">{{ $contact->agent ?? '-' }}</td>
                @elseif($col == 'address')
                  <td data-column="address">{{ $contact->address ?? '-' }}</td>
                @elseif($col == 'email_address')
                  <td data-column="email_address">{{ $contact->email_address ?? '-' }}</td>
                @elseif($col == 'savings_budget')
                  <td data-column="savings_budget">{{ $contact->savings_budget ? number_format($contact->savings_budget,2) : '##########' }}</td>
                @elseif($col == 'married')
                  <td data-column="married">{{ $contact->married ? 'Yes' : 'No' }}</td>
                @elseif($col == 'children')
                  <td data-column="children">{{ $contact->children ?? '0' }}</td>
                @elseif($col == 'children_details')
                  <td data-column="children_details">{{ $contact->children_details ?? '-' }}</td>
                @elseif($col == 'vehicle')
                  <td data-column="vehicle">{{ $contact->vehicle ?? '-' }}</td>
                @elseif($col == 'house')
                  <td data-column="house">{{ $contact->house ?? '-' }}</td>
                @elseif($col == 'business')
                  <td data-column="business">{{ $contact->business ?? '-' }}</td>
                @elseif($col == 'other')
                  <td data-column="other">{{ $contact->other ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

    <div class="footer">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('contacts.export', array_merge(request()->query(), ['page' => $contacts->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $contacts->currentPage();
          $last = max(1,$contacts->lastPage());
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

  <!-- Contact Page View (Full Page) -->
  <div class="client-page-view" id="contactPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="contactPageTitle">Contact</span> - <span class="client-name" id="contactPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editContactFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeContactPageBtn" onclick="closeContactPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Contact Details View -->
        <div id="contactDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="contactDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>
        
        <!-- Contact Edit/Add Form -->
        <div id="contactFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="contactDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteContact()">Delete</button>
                <button type="submit" form="contactPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeContactFormBtn" onclick="closeContactPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="contactPageForm" method="POST" action="{{ route('contacts.store') }}">
              @csrf
              <div id="contactPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Contact Modal (hidden, used for form structure) -->
  <div class="modal" id="contactModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="contactModalTitle">Add Contact</h4>
        <button type="button" class="modal-close" onclick="closeContactModal()">×</button>
      </div>

      <form id="contactForm" method="POST" action="{{ route('contacts.store') }}">
        @csrf
        <div id="contactFormMethod" style="display:none;"></div>

        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="salutation">Salutation</label>
              <select id="salutation" name="salutation" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['salutations'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="contact_name">Contact Name *</label>
              <input id="contact_name" name="contact_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="contact_no">Contact No</label>
              <input id="contact_no" name="contact_no" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email_address">Email</label>
              <input id="email_address" name="email_address" type="email" class="form-control">
            </div>
            <div class="form-group">
              <label for="type">Type *</label>
              <select id="type" name="type" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['contact_types'] as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="occupation">Occupation</label>
              <input id="occupation" name="occupation" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="employer">Employer</label>
              <input id="employer" name="employer" class="form-control">
            </div>
            <div class="form-group">
              <label for="acquired">Acquired</label>
              <input id="acquired" name="acquired" type="date" class="form-control">
            </div>
            <div class="form-group">
              <label for="source">Source *</label>
              <select id="source" name="source" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['sources'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="status">Status *</label>
              <select id="status" name="status" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['contact_statuses'] as $st) <option value="{{ $st }}">{{ $st }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="rank">Rank</label>
              <select id="rank" name="rank" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['ranks'] as $r) <option value="{{ $r }}">{{ $r }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="first_contact">First Contact</label>
              <input id="first_contact" name="first_contact" type="date" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="next_follow_up">Next Follow Up</label>
              <input id="next_follow_up" name="next_follow_up" type="date" class="form-control">
            </div>
            <div class="form-group">
              <label for="coid">COID</label>
              <input id="coid" name="coid" class="form-control">
            </div>
            <div class="form-group">
              <label for="dob">DOB</label>
              <input id="dob" name="dob" type="date" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="agency">Agency</label>
              <select id="agency" name="agency" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['agencies'] as $a) <option value="{{ $a }}">{{ $a }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="agent">Agent</label>
              <select id="agent" name="agent" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['agents'] as $a) <option value="{{ $a }}">{{ $a }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="savings_budget">Savings Budget</label>
              <input id="savings_budget" name="savings_budget" type="number" step="0.01" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group" style="flex:1">
              <label for="address">Address</label>
              <textarea id="address" name="address" class="form-control" rows="2"></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="married" style="display:block;">Married</label>
              <input id="married" name="married" type="checkbox" value="1">
            </div>
            <div class="form-group">
              <label for="children">Children</label>
              <input id="children" name="children" type="number" min="0" class="form-control" value="0">
            </div>
            <div class="form-group">
              <label for="children_details">Children Details</label>
              <input id="children_details" name="children_details" class="form-control">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="vehicle">Vehicle</label>
              <input id="vehicle" name="vehicle" class="form-control">
            </div>
            <div class="form-group">
              <label for="house">House</label>
              <input id="house" name="house" class="form-control">
            </div>
            <div class="form-group">
              <label for="business">Business</label>
              <input id="business" name="business" class="form-control">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeContactModal()">Cancel</button>
          <button type="button" class="btn-delete" id="contactDeleteBtn" style="display:none;" onclick="deleteContact()">Delete</button>
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
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
        <div class="column-actions">
          <button class="btn-select-all" onclick="selectAllColumns()">Select All</button>
          <button class="btn-deselect-all" onclick="deselectAllColumns()">Deselect All</button>
        </div>

        <form id="columnForm" action="{{ route('contacts.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @php
              $all = [
                'contact_name'=>'Contact Name','contact_no'=>'Contact No','type'=>'Type','occupation'=>'Occupation','employer'=>'Employer',
                'acquired'=>'Acquired','source'=>'Source','status'=>'Status','rank'=>'Rank','first_contact'=>'1st Contact',
                'next_follow_up'=>'Next FU','coid'=>'COID','dob'=>'DOB','salutation'=>'Salutation','source_name'=>'Source Name',
                'agency'=>'Agency','agent'=>'Agent','address'=>'Address','email_address'=>'Email Address','contact_id'=>'Contact ID',
                'savings_budget'=>'Savings Budget','married'=>'Married','children'=>'Children','children_details'=>'Children Details',
                'vehicle'=>'Vehicle','house'=>'House','business'=>'Business','other'=>'Other'
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
              // Use mandatory columns from config
              $mandatoryFields = $mandatoryColumns;
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
  let currentContactId = null;
  const lookupData = @json($lookupData);
  const selectedColumns = @json($selectedColumns);
  const mandatoryFields = @json($mandatoryColumns);

  // Format date helper function
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
  }

  // Format number helper function
  function formatNumber(num) {
    if (!num && num !== 0) return '-';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Open contact details (full page view) - MUST be defined before HTML onclick handlers
  async function openContactDetails(id) {
    try {
      const res = await fetch(`/contacts/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const contact = await res.json();
      currentContactId = id;
      
      // Get all required elements
      const contactPageName = document.getElementById('contactPageName');
      const contactPageTitle = document.getElementById('contactPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const contactPageView = document.getElementById('contactPageView');
      const contactDetailsPageContent = document.getElementById('contactDetailsPageContent');
      const contactFormPageContent = document.getElementById('contactFormPageContent');
      const editContactFromPageBtn = document.getElementById('editContactFromPageBtn');
      const closeContactPageBtn = document.getElementById('closeContactPageBtn');
      
      if (!contactPageName || !contactPageTitle || !clientsTableView || !contactPageView || 
          !contactDetailsPageContent || !contactFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set contact name in header
      const contactName = contact.contact_name || contact.contact_id || 'Unknown';
      contactPageName.textContent = contactName;
      contactPageTitle.textContent = 'Contact';
      
      populateContactDetails(contact);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      contactPageView.style.display = 'block';
      contactPageView.classList.add('show');
      contactDetailsPageContent.style.display = 'block';
      contactFormPageContent.style.display = 'none';
      if (editContactFromPageBtn) editContactFromPageBtn.style.display = 'inline-block';
      if (closeContactPageBtn) closeContactPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading contact details: ' + e.message);
    }
  }

  // Populate contact details view
  function populateContactDetails(contact) {
    const content = document.getElementById('contactDetailsContent');
    if (!content) return;

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">CONTACT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Contact ID</span>
            <div class="detail-value">${contact.contact_id || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Salutation</span>
            <div class="detail-value">${contact.salutation || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Contact Name</span>
            <div class="detail-value">${contact.contact_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Contact No</span>
            <div class="detail-value">${contact.contact_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Email Address</span>
            <div class="detail-value">${contact.email_address || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">TYPE & SOURCE</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Type</span>
            <div class="detail-value">${contact.type || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Occupation</span>
            <div class="detail-value">${contact.occupation || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Employer</span>
            <div class="detail-value">${contact.employer || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Source</span>
            <div class="detail-value">${contact.source || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Source Name</span>
            <div class="detail-value">${contact.source_name || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">STATUS & DATES</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <div class="detail-value">${contact.status || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Rank</span>
            <div class="detail-value">${contact.rank || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Acquired</span>
            <div class="detail-value">${formatDate(contact.acquired)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">1st Contact</span>
            <div class="detail-value">${formatDate(contact.first_contact)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Next Follow Up</span>
            <div class="detail-value">${formatDate(contact.next_follow_up)}</div>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header">ADDITIONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">COID</span>
            <div class="detail-value">${contact.coid || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">DOB</span>
            <div class="detail-value">${formatDate(contact.dob)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Agency</span>
            <div class="detail-value">${contact.agency || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Agent</span>
            <div class="detail-value">${contact.agent || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Savings Budget</span>
            <div class="detail-value">${formatNumber(contact.savings_budget)}</div>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">PERSONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Married</span>
            <div class="detail-value">
              <input type="checkbox" ${contact.married ? 'checked' : ''} disabled>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Children</span>
            <div class="detail-value">${contact.children || '0'}</div>
          </div>
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Address</span>
            <textarea class="detail-value" style="min-height:40px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${contact.address || ''}</textarea>
          </div>
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Children Details</span>
            <textarea class="detail-value" style="min-height:40px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${contact.children_details || ''}</textarea>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">ASSETS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Vehicle</span>
            <div class="detail-value">${contact.vehicle || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">House</span>
            <div class="detail-value">${contact.house || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Business</span>
            <div class="detail-value">${contact.business || '-'}</div>
          </div>
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Other</span>
            <textarea class="detail-value" style="min-height:40px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${contact.other || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open contact page (Add or Edit)
  async function openContactPage(mode) {
    if (mode === 'add') {
      openContactForm('add');
    } else {
      if (currentContactId) {
        openEditContact(currentContactId);
      }
    }
  }

  // open add button - use full-page view
  document.getElementById('addContactBtn').addEventListener('click', () => {
    openContactPage('add');
  });

  // column modals for both buttons
  document.getElementById('columnBtn2').addEventListener('click', () => {
    openColumnModal();
  });

  // archived filter toggles query param 'archived'
  document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('archivedOnlyBtn');
    const params = new URLSearchParams(window.location.search);
    const active = params.get('archived') === 'true' || params.get('archived') === '1';
    if (active) { btn.classList.add('active'); }
    btn.addEventListener('click', () => {
      const u = new URL(window.location.href);
      const val = u.searchParams.get('archived');
      if (val === 'true' || val === '1') { u.searchParams.delete('archived'); } else { u.searchParams.set('archived','1'); }
      window.location.href = u.toString();
    });

  });

  // show edit: fetch /contacts/{id}/edit which returns JSON in controller
  async function openEditContact(id){
    try {
      const res = await fetch(`/contacts/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const contact = await res.json();
      currentContactId = id;
      openContactForm('edit', contact);
    } catch (e) {
      console.error(e);
      alert('Error loading contact data');
    }
  }

  function openContactForm(mode, contact = null) {
    // Clone form from modal
    const modalForm = document.getElementById('contactModal').querySelector('form');
    const pageForm = document.getElementById('contactPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('contactPageFormMethod');
    const deleteBtn = document.getElementById('contactDeleteBtn');
    const editBtn = document.getElementById('editContactFromPageBtn');
    const closeBtn = document.getElementById('closeContactPageBtn');
    const closeFormBtn = document.getElementById('closeContactFormBtn');

    if (mode === 'add') {
      document.getElementById('contactPageTitle').textContent = 'Add Contact';
      document.getElementById('contactPageName').textContent = '';
      pageForm.action = '{{ route("contacts.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
      // reset checkboxes
      const marriedCheckbox = formContentDiv ? formContentDiv.querySelector('#married') : null;
      if (marriedCheckbox) marriedCheckbox.checked = false;
    } else {
      const contactName = contact.contact_name || contact.contact_id || 'Unknown';
      document.getElementById('contactPageTitle').textContent = 'Edit Contact';
      document.getElementById('contactPageName').textContent = contactName;
      pageForm.action = `/contacts/${currentContactId}`;
      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'PUT';
      formMethod.innerHTML = '';
      formMethod.appendChild(methodInput);
      deleteBtn.style.display = 'inline-block';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'none';
      if (closeFormBtn) closeFormBtn.style.display = 'inline-block';

      // populate fields safely
      const fields = ['salutation','contact_name','contact_no','email_address','type','occupation','employer','acquired','source','status','rank','first_contact','next_follow_up','coid','dob','source_name','agency','agent','savings_budget','address','children','children_details','vehicle','house','business','other'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'checkbox') {
          el.checked = !!contact[k];
        } else if (el.type === 'date') {
          el.value = contact[k] ? (typeof contact[k] === 'string' ? contact[k].substring(0,10) : contact[k]) : '';
        } else {
          el.value = contact[k] ?? '';
        }
      });
      const marriedCheckbox = formContentDiv ? formContentDiv.querySelector('#married') : null;
      if (marriedCheckbox) marriedCheckbox.checked = !!contact.married;
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const contactPageView = document.getElementById('contactPageView');
    contactPageView.style.display = 'block';
    contactPageView.classList.add('show');
    document.getElementById('contactDetailsPageContent').style.display = 'none';
    document.getElementById('contactFormPageContent').style.display = 'block';
  }

  function closeContactPageView() {
    const contactPageView = document.getElementById('contactPageView');
    contactPageView.classList.remove('show');
    contactPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('contactDetailsPageContent').style.display = 'none';
    document.getElementById('contactFormPageContent').style.display = 'none';
    currentContactId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editContactFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentContactId) {
        openEditContact(currentContactId);
      }
    });
  }

  // Legacy function for backward compatibility - redirects to details view
  function openContactModal(mode, contact = null){
    if (mode === 'add') {
      openContactPage('add');
    } else if (contact && currentContactId) {
      openEditContact(currentContactId);
    }
  }

  function closeContactModal(){
    // Also close page view if open
    closeContactPageView();
    document.getElementById('contactModal').classList.remove('show');
    currentContactId = null;
    document.body.style.overflow = '';
  }

  function deleteContact(){
    if (!currentContactId) return;
    if (!confirm('Delete this contact?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/contacts/${currentContactId}`;
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }

  // Column modal functions
  function openColumnModal(){
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
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      cb.checked = true;
    });
  }
  function deselectAllColumns(){ 
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      // Don't uncheck mandatory fields
      if (!mandatoryFields.includes(cb.value)) {
        cb.checked = false;
      }
    });
  }

  function saveColumnSettings(){
    
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
      // Skip if already initialized
      if (item.dataset.dragInitialized === 'true') {
        return;
      }
      item.dataset.dragInitialized = 'true';
      // Prevent checkbox from interfering with drag
      const checkbox = item.querySelector('.column-checkbox');
      if (checkbox) {
        checkbox.addEventListener('mousedown', function(e) {
          e.stopPropagation();
        });
        checkbox.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }
      
      // Prevent label from interfering with drag
      const label = item.querySelector('label');
      if (label) {
        label.addEventListener('mousedown', function(e) {
          // Only prevent if clicking on the label text, not the checkbox
          if (e.target === label) {
            e.preventDefault();
          }
        });
      }
      
      // Drag start
      item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
        e.dataTransfer.setData('text/plain', this.dataset.column);
      });
      
      // Drag end
      item.addEventListener('dragend', function(e) {
        this.classList.remove('dragging');
        // Remove drag-over from all items
        columnItems.forEach(i => i.classList.remove('drag-over'));
        if (dragOverElement) {
          dragOverElement.classList.remove('drag-over');
          dragOverElement = null;
        }
        draggedElement = null;
      });
      
      // Drag over
      item.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
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
          const midpoint = rect.top + (rect.height / 2);
          const next = e.clientY > midpoint;
          
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
      
      // Drag enter
      item.addEventListener('dragenter', function(e) {
        e.preventDefault();
        if (draggedElement && this !== draggedElement) {
          this.classList.add('drag-over');
        }
      });
      
      // Drag leave
      item.addEventListener('dragleave', function(e) {
        // Only remove if we're actually leaving the element
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

  // close modals on ESC and clicking backdrop
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeContactModal(); closeColumnModal(); } });
  document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) { m.classList.remove('show'); document.body.style.overflow = ''; } });
  });

  // Basic client-side validation for contact form (prevent empty required)
  document.getElementById('contactForm').addEventListener('submit', function(e){
    const req = this.querySelectorAll('[required]');
    let ok = true;
    req.forEach(f => { if (!String(f.value||'').trim()) { ok = false; f.style.borderColor='red'; } else { f.style.borderColor=''; } });
    if (!ok) { e.preventDefault(); alert('Please fill required fields'); }
  });
</script>

@endsection
