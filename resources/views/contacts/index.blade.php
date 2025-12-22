@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/contacts-index.css') }}">




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
    <!-- To Follow Up Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden; margin-bottom:15px;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0; padding:15px 20px;">
        <div class="page-title-section">
          <h3>Contacts{{ request()->has('follow_up') && request()->follow_up ? ' - To Follow Up' : '' }}</h3>
        </div>
      </div>
    </div>

    <!-- Contacts Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        @if(request()->has('follow_up') && request()->follow_up)
          <div class="records-found">Records Found - {{ $contacts->total() }}</div>
          <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
            <div class="filter-group" style="display:flex; align-items:center; gap:10px;">
              <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
                <span style="font-size:13px;">Filter</span>
                <input type="checkbox" id="filterToggle" checked>
              </label>
              <button class="btn" id="listAllBtn" type="button" style="background:#28a745; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">List ALL</button>
            </div>
          </div>
        @else
          <div class="records-found">Records Found - {{ $contacts->total() }}</div>
          <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
            <div class="filter-group" style="display:flex; align-items:center; gap:10px;">
              <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
                <span style="font-size:13px;">Filter</span>
                <input type="checkbox" id="filterToggle">
              </label>
              <button class="btn btn-follow-up" id="followUpBtn" type="button" style="background:#2d2d2d; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">To Follow Up</button>
            </div>
          </div>
        @endif
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addContactBtn">Add</button>
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
      <table id="contactsTable">
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
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($contacts as $contact)
            <tr class="{{ $contact->status === 'Archived' ? 'archived-row' : '' }}">
              <td class="bell-cell {{ $contact->hasExpired ? 'expired' : ($contact->hasExpiring ? 'expiring' : '') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  @php
                    $isExpired = $contact->hasExpired;
                    $isExpiring = $contact->hasExpiring;
                    $hasFollowUp = $contact->next_follow_up && $contact->status !== 'Archived';
                    
                    // Determine color based on status
                    if ($isExpired) {
                      $dotColor = '#dc3545'; // Red - expired
                      $dotFill = '#dc3545';
                    } elseif ($isExpiring) {
                      $dotColor = '#f3742a'; // Yellow/Orange - expiring soon
                      $dotFill = 'transparent';
                    } elseif ($hasFollowUp) {
                      $dotColor = '#007bff'; // Blue - has follow-up
                      $dotFill = 'transparent';
                    } else {
                      $dotColor = '#ccc'; // Gray - no follow-up
                      $dotFill = 'transparent';
                    }
                  @endphp
                  <div class="status-indicator" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $dotColor }}; background-color:{{ $dotFill }};"></div>
                </div>
              </td>
              <td class="action-cell">
              <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openEditContact({{ $contact->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">

               
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'contact_name')
                  <td data-column="contact_name">
                  {{ $contact->contact_name }}
                  </td>
                @elseif($col == 'contact_id')
                  <td data-column="contact_id">
                    {{ $contact->contact_id }}
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

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
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

  <!-- Add/Edit Contact Modal -->
  <div class="modal" id="contactModal">
    <div class="modal-content">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; border-bottom: 1px solid #ddd;">
        <h4 id="contactModalTitle" style="margin: 0; font-size: 16px; font-weight: bold;">Add Contact</h4>
        <div style="display: flex; gap: 8px;">
          <button type="submit" form="contactForm" class="btn-save" style="background: #f3742a; color: #fff; border: none; padding: 5px 12px; border-radius: 2px; cursor: pointer; font-size: 12px;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeContactModal()" style="background: #000; color: #fff; border: none; padding: 5px 12px; border-radius: 2px; cursor: pointer; font-size: 12px;">Cancel</button>
        </div>
      </div>

      <form id="contactForm" method="POST" action="{{ route('contacts.store') }}">
        @csrf
        <div id="contactFormMethod" style="display:none;"></div>

        <div class="modal-body" style="padding: 12px;">
          <h5 style="color: #f3742a; margin: 0 0 10px 0; font-size: 13px; font-weight: bold;">Contact Details</h5>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 12px; align-items: center; margin-bottom: 6px;">
            <div>
              <label for="type" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Contact Type</label>
              <select id="type" name="type" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select</option>
                @foreach($lookupData['contact_types'] as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="contact_name" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Contact Name</label>
              <input id="contact_name" name="contact_name" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
            </div>
            <div>
              <label for="occupation" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Occupation</label>
              <select id="occupation" name="occupation" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select or Type</option>
                @foreach($allOccupations as $occ) <option value="{{ $occ }}">{{ $occ }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="employer" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Employer</label>
              <select id="employer" name="employer" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select or Type</option>
                @foreach($allEmployers as $emp) <option value="{{ $emp }}">{{ $emp }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="contact_no" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Contact No.</label>
              <div style="display: flex; gap: 6px;">
                <input id="contact_no" name="contact_no" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <input id="wa" name="wa" placeholder="WA" class="form-control" style="width: 70px; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <div class="checkbox-cell" style="display: flex; align-items: center; margin-top: 2px;">
                  <input type="checkbox" id="wa_checkbox" class="checkbox-style">
                </div>
              </div>
            </div>
            <div>
              <label for="email_address" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Email Address</label>
              <input id="email_address" name="email_address" type="email" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
            </div>
            <div>
              <label for="address" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Address / Location</label>
              <div style="display: flex; gap: 6px;">
                <input id="address" name="address" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <input id="location" name="location" placeholder="PR" class="form-control" style="width: 70px; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
              </div>
            </div>
            <div>
              <label for="dob" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Date Of Birth / Age</label>
              <div style="display: flex; gap: 6px;">
                <input id="dob" name="dob" type="date" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <input id="age_display" type="text" placeholder="Age" readonly class="form-control" style="width: 70px; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; background: #f5f5f5; font-size: 12px;">
              </div>
            </div>
            <div>
              <label for="acquired" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Date Acquired</label>
              <div style="display: flex; gap: 6px;">
                <input id="acquired" name="acquired" type="date" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <input type="text" value="-" readonly class="form-control" style="width: 70px; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; background: #f5f5f5; font-size: 12px;">
              </div>
            </div>
            <div>
              <label for="source" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Source</label>
              <select id="source" name="source" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select</option>
                @foreach($lookupData['sources'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="source_name" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Source Name</label>
              <input id="source_name" name="source_name" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
            </div>
            <div>
              <label for="agency" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Agency</label>
              <select id="agency" name="agency" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select</option>
                @foreach($lookupData['agencies'] as $a) <option value="{{ $a }}">{{ $a }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="agent" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Agent</label>
              <select id="agent" name="agent" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select</option>
                @foreach($lookupData['agents'] as $a) <option value="{{ $a }}">{{ $a }}</option> @endforeach
                @foreach($users as $user) <option value="{{ $user->name }}">{{ $user->name }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="status" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Status</label>
              <select id="status" name="status" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select</option>
                @foreach($lookupData['contact_statuses'] as $st) <option value="{{ $st }}">{{ $st }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="rank" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Ranking</label>
              <select id="rank" name="rank" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
                <option value="">Select</option>
                @foreach($lookupData['ranks'] as $r) <option value="{{ $r }}">{{ $r }}</option> @endforeach
              </select>
            </div>
            <div>
              <label for="savings_budget" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Savings Budget</label>
              <input id="savings_budget" name="savings_budget" type="number" step="0.01" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
            </div>
            <div>
              <label for="children" style="font-size: 12px; font-weight: 500; display: block; margin-bottom: 3px;">Children</label>
              <input id="children" name="children" type="number" min="0" class="form-control" value="0" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;">
            </div>

          <input type="hidden" id="salutation" name="salutation" value="Mr">
          <input type="hidden" id="first_contact" name="first_contact">
          <input type="hidden" id="next_follow_up" name="next_follow_up">
          <input type="hidden" id="coid" name="coid">
          <input type="hidden" id="married" name="married" value="0">
          <input type="hidden" id="children_details" name="children_details">
          <input type="hidden" id="vehicle" name="vehicle">
          <input type="hidden" id="house" name="house">
          <input type="hidden" id="business" name="business">
          <input type="hidden" id="other" name="other">
        </div>

        <div class="modal-footer" style="display: none;">
          <button type="button" class="btn-delete" id="contactDeleteBtn" style="display:none;" onclick="deleteContact()">Delete</button>
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
  // Initialize data from Blade
  let currentContactId = null;
  const lookupData = @json($lookupData ?? []);
  const selectedColumns = @json($selectedColumns ?? []);
  const mandatoryFields = @json($mandatoryColumns ?? []);
  const contactsStoreRoute = '{{ route("contacts.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/contacts-index.js') }}"></script>
@endsection
