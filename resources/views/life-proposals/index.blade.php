@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/life-proposals-index.css') }}">




@php
  $config = \App\Helpers\TableConfigHelper::getConfig('life-proposals');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('life-proposals');
  $columnDefinitions = $config['column_definitions'];
  $mandatoryColumns = $config['mandatory_columns'];
@endphp

<div class="dashboard">
  <!-- Main Life Proposals Table View -->
  <div class="clients-table-view" id="clientsTableView">
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
            Life Proposals
            @if(isset(request()->follow_up) || request()->follow_up === 1 ||  isset($contactid))
              <span class="client-name" style="color:#f3742a; font-size:16px; font-weight:500;"> -  To Follow Up</span>
            @endif
            
          </h3>
       
      </div>
    </div>
  <div class="container-table">
    <!-- Life Proposals Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
          <div class="records-found" style="font-size:14px; font-weight:600; color:#333; white-space:nowrap;">
              Records Found - {{ $proposals->total() }}
          </div>
        <div class="page-title-section">
          <div style="display:flex; align-items:center; gap:15px; flex:1; justify-content:center;">
              <div class="filter-group" style="display:flex; align-items:center; gap:10px;">
                  <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
                    <span style="font-size:13px;">Filter</span>
                    @php
                      $hasFollowUp = request()->has('follow_up') && (request()->follow_up == 'true' || request()->follow_up == '1');
                      $hasSubmitted = request()->has('submitted') && (request()->submitted == 'true' || request()->submitted == '1');
                    @endphp
                    <input type="checkbox" id="filterToggle" {{ $hasFollowUp || $hasSubmitted ? 'checked' : '' }}>
                  </label>
                  <button class="btn btn-follow-up" id="followUpBtn" type="button" style="border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">To Follow Up</button>
                  <button class="btn btn-submitted" id="submittedBtn" type="button" style="border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Submitted</button>
                </div>
              </div>
          </div>
            <div class="action-buttons" style="display:flex; align-items:center; gap:10px; white-space:nowrap;">
                <button class="btn btn-add" id="addProposalBtn">Add</button>
                <button class="btn btn-close" onclick="window.history.back()">Close</button>
            </div>
        </div>
      @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
        @if(session('success'))
          <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
          </div>
        @endif

        <div class="table-responsive" id="tableResponsive">
          <table id="proposalsTable">
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
              @foreach($proposals as $index => $proposal)
                <tr class="{{ $proposal->is_submitted ? 'submitted-row' : '' }}">
                  <td class="bell-cell {{ $proposal->hasExpired ? 'expired' : ($proposal->hasExpiring ? 'expiring' : '') }}">
                    <div style="display:flex; align-items:center; justify-content:center;">
                      @php
                        $isExpired = $proposal->hasExpired;
                        $isExpiring = $proposal->hasExpiring;
                      
                      $radioChecked = false;
                      $radioDotColor = 'transparent';
                      if ($index === 0 && ($isExpired || $isExpiring)) {
                        $radioChecked = true;
                        $radioDotColor = '#f3742a'; // Yellow
                      } elseif ($isExpired) {
                        $radioDotColor = '#dc3545'; // Red
                      } elseif ($isExpiring) {
                        $radioDotColor = '#f3742a'; // Yellow
                      } elseif ($proposal->offer_date && !$proposal->is_submitted) {
                        $radioDotColor = '#007bff'; // Blue
                      }
                    @endphp
                    <div style="position:relative; display:inline-block;">
                      <input type="radio" name="proposal_select" class="action-radio" value="{{ $proposal->id }}" data-proposal-id="{{ $proposal->id }}" data-dot-color="{{ $radioDotColor }}" style="width:16px; height:16px; cursor:pointer; opacity:0; position:absolute; z-index:2;" {{ $radioChecked ? 'checked' : '' }}>
                      <div class="radio-dot" style="width:16px; height:16px; border-radius:50%; border:2px solid #2d2d2d; background-color:{{ $radioDotColor !== 'transparent' ? $radioDotColor : 'transparent' }}; position:relative; z-index:1;"></div>
                    </div>
                  </td>
                  <td class="action-cell">
          
                    <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openProposalDetails({{ $proposal->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
                  
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                      <circle cx="12" cy="12" r="10" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                      <path d="M12 6V12L16 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
              
                  </td>
                  @foreach($selectedColumns as $col)
                    @if($col == 'proposers_name')
                      <td data-column="proposers_name">
                      {{ $proposal->proposers_name }}
                      </td>
                    @elseif($col == 'prid')
                      <td data-column="prid">
                      {{ $proposal->prid }}
                      </td>
                    @elseif($col == 'insurer')
                      <td data-column="insurer">{{ $proposal->insurer->name }}</td>
                    @elseif($col == 'policy_plan')
                      <td data-column="policy_plan">{{ $proposal->policy_plan }}</td>
                    @elseif($col == 'sum_assured')
                      <td data-column="sum_assured">{{ $proposal->sum_assured ? number_format($proposal->sum_assured,2) : '##########' }}</td>
                    @elseif($col == 'term')
                      <td data-column="term">{{ $proposal->term }}</td>
                    @elseif($col == 'add_ons')
                      <td data-column="add_ons">{{ $proposal->add_ons ?? '-' }}</td>
                    @elseif($col == 'offer_date')
                      <td data-column="offer_date">{{ $proposal->offer_date ? $proposal->offer_date->format('d-M-y') : '##########' }}</td>
                    @elseif($col == 'premium')
                      <td data-column="premium">{{ number_format($proposal->premium,2) }}</td>
                    @elseif($col == 'frequency')
                      <td data-column="frequency">{{ $proposal->frequency->name }}</td>
                    @elseif($col == 'stage')
                      <td data-column="stage">{{ $proposal->stage->name  }}</td>
                    @elseif($col == 'date')
                      <td data-column="date">{{ $proposal->date ? $proposal->date->format('d-M-y') : '##########' }}</td>
                    @elseif($col == 'age')
                      <td data-column="age">{{ $proposal->age }}</td>
                    @elseif($col == 'status')
                      <td data-column="status"><span class="badge-status" style="background:{{ $proposal->status->name == 'Approved' ? '#28a745' : ($proposal->status->name=='Pending' ? '#ffc107' : ($proposal->status->name=='Declined' ? '#dc3545' : '#6c757d')) }}">{{ $proposal->status->name }}</span></td>
                    @elseif($col == 'source_of_payment')
                      <td data-column="source_of_payment">{{ $proposal->sourceOfPayment->name }}</td>
                    @elseif($col == 'mcr')
                      <td data-column="mcr">{{ $proposal->mcr ?? '-' }}</td>
                    @elseif($col == 'doctor')
                      <td data-column="doctor">{{ $proposal->doctor ?? '-' }}</td>
                    @elseif($col == 'date_sent')
                      <td data-column="date_sent">{{ $proposal->date_sent ? $proposal->date_sent->format('d-M-y') : '##########' }}</td>
                    @elseif($col == 'date_completed')
                      <td data-column="date_completed">{{ $proposal->date_completed ? $proposal->date_completed->format('d-M-y') : '##########' }}</td>
                    @elseif($col == 'notes')
                      <td data-column="notes">{{ $proposal->notes ?? '-' }}</td>
                    @elseif($col == 'agency')
                      <td data-column="agency">{{ $proposal->agencies->name ?? '-' }}</td>
                    @elseif($col == 'class')
                      <td data-column="class">{{ $proposal->class }}</td>
                    @elseif($col == 'is_submitted')
                      <td data-column="is_submitted">{{ $proposal->is_submitted ? 'Yes' : 'No' }}</td>
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
          <a class="btn btn-export" href="{{ route('life-proposals.export') }}">Export</a>
          <button class="btn btn-column" id="columnBtn" type="button">Column</button>
        </div>
        <div class="paginator">
          @php
            $base = url()->current();
            $q = request()->query();
            $current = $proposals->currentPage();
            $last = max(1,$proposals->lastPage());
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

  <!-- Proposal Page View (Full Page) -->
  <div class="client-page-view" id="proposalPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="proposalPageTitle">Life Proposal</span> - <span class="client-name" id="proposalPageName"></span>
      </div>
    </div>
    <div class="client-page-body">
      
       <div class="client-page-content">
         <!-- Proposal Details View -->
         <div id="proposalDetailsPageContent" style="display:none;">
           <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
             <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; ">
               <div class="proposal-nav-tabs" id="proposalNavTabs" style="display:none;">
                  <button class="proposal-nav-tab active" data-tab="nominee" data-url="{{ route('nominees.index') }}">Nominees</button>
                  <button class="proposal-nav-tab" data-tab="life-proposals-follow-up" data-url="{{ route('life-proposals.index') }}">Follow Ups</button>
               </div>
               <div class="client-page-actions">
                 <button class="btn" id="generatePolicyBtn" onclick="generatePolicyFromProposal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Generate Policy</button>
                 <button class="btn btn-edit" id="editProposalFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Update</button>
                 <button class="btn" id="closeProposalPageBtn" onclick="closeProposalPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
               </div>
             </div>
             <div class="proposal-detail-grid" id="proposalDetailsContent">
               <!-- Content will be loaded via JavaScript -->
             </div>
           </div>
           <!-- Documents Section -->
           <div class="documents-section" style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
             <div class="documents-header">
               <div class="documents-title">Documents</div>
               <button class="btn-add-document" onclick="openDocumentUpload()">Add Document</button>
             </div>
             <div class="documents-list" id="documentsList">
               <!-- Documents will be loaded here -->
             </div>
           </div>
         </div>
        
        <!-- Proposal Edit/Add Form -->
          <div id="proposalFormPageContent" style="display:none;">
            <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
              <div style="display:flex; justify-content:flex-end; align-items:center; padding:6px 12px; border-bottom:1px solid #ddd; background:#fff;">
                <div class="client-page-actions">
                  <button type="button" class="btn-delete" id="proposalDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteProposal()">Delete</button>
                  <button type="submit" form="proposalPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                  <button type="button" class="btn" id="closeProposalFormBtn" onclick="closeProposalPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
                </div>
              </div>
              <form id="proposalPageForm" method="POST" action="{{ route('life-proposals.store') }}">
                @csrf
                <div id="proposalPageFormMethod" style="display:none;"></div>
                <div id="proposalPageFormContent" style="padding:6px 12px;">
                  <!-- Form content will be cloned from modal -->
                </div>
              </form>
            </div>
          </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Proposal Modal (hidden, used for form structure) -->
  <div class="modal" id="proposalModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="proposalModalTitle">Life Proposal - New/Update</h4>
        <div style="display:flex; gap:10px;">
          <button type="button" class="btn-cancel" onclick="closeProposalModal()">Cancel</button>
          <button type="submit" form="proposalForm" class="btn-save">Save</button>
        </div>
      </div>
      <form id="proposalForm" method="POST" action="{{ route('life-proposals.store') }}">
        @csrf
        <div id="proposalFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <!-- Proposer's Details Section -->
          <div class="form-section">
            <div class="form-section-title">Proposer's Details</div>
            <div class="form-row proposer-fields-row">
              <div class="form-group grow">
                <label for="proposers_name">Proposer's Name *</label>
                <input type="text" id="proposers_name" name="proposers_name" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="salutation_id">Salutation</label>
                <select id="salutation_id" name="salutation_id" class="form-control">
                  <option value="">Select</option>
                  @foreach($lookupData['salutations'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="dob">DOB</label>
                <input id="dob" name="dob" type="date" class="form-control">
              </div>
              <div class="form-group">
                <label for="insurer_id">Insurer *</label>
                <select id="insurer_id" name="insurer_id" class="form-control" required>
                  <option value="">Select</option>
                  @foreach($lookupData['insurers'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
              </div>
                 <div class="form-group grow">
                <label for="policy_plan_id">Policy Plan *</label>
                <select id="policy_plan_id" name="policy_plan_id" class="form-control" required>
                  <option value="">Select</option>
                  @foreach($lookupData['policy_plans'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="term">Term *</label>
                <input id="term" name="term" type="number" min="1" class="form-control" required>
              </div>
                 <div class="form-group grow">
                <label for="sum_assured">Sum Assured</label>
                <input id="sum_assured" name="sum_assured" type="number" step="0.01" class="form-control" oninput="formatNumberInput(this)">
              </div>
              <div class="form-group small-grow">
                <label for="age">Age</label>
                <input id="age" name="age" type="number" min="1" max="120" class="form-control" >
              </div>
                 <div class="form-group small-grow">
                 <label for="sex">Sex</label>
                <select id="sex" name="sex" class="form-control">
                  <option value="">Select</option>
                  @foreach($lookupData['sex_options'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group small-grow">
                <label for="anb">ANB</label>
                <input id="anb" name="anb" type="number" class="form-control">
              </div>
            </div>

          <!-- Additional Riders Section -->
         <div class="form-section">
           
            <div class="rider-grid">
                 <div class="flex-1 ">
               <div class="form-section-title">Additional Riders</div>
                <div class="form-section-title "style="margin-top:18px">Rider Premiums</div></div>
              @foreach($lookupData['riders'] as $rider)
                <div class="rider-item">
                  <div class="rider-lable-checkbox">
                     <label for="rider_{{ $rider['id'] }}" style="margin:0; font-weight:normal;">{{ $rider['name'] }}</label>
                  <input type="checkbox" class="rider-checkbox" id="rider_{{ $rider['id'] }}" name="riders[]" value="{{ $rider['id'] }}" data-rider="{{ $rider['id'] }}">
                 </div>
                  <input type="number" step="0.01" class="form-control rider-premium" id="rider_premium_{{ $rider['id'] }}" name="rider_premiums[{{ $rider['id'] }}]" style="width:80px; padding:4px; background: transparent !important; margin-top: 8px;" placeholder="0.00" disabled>
                </div>
              @endforeach
            </div>
            <div class="form-row" style="margin-top:6px; ">
              <div class="form-group">
                <label for="annual_premium">Annual Premium</label>
                <input id="annual_premium" name="annual_premium" type="number" step="0.01" class="form-control" oninput="calculateTotalPremium()" style="background: transparent !important;">
              </div>
              <div class="form-group">
                <label for="total_rider_premium">Total</label>
                <input id="total_rider_premium" type="number" step="0.01" class="form-control readonly-field" readonly style="background: transparent !important;">
              </div>
            </div>
          </div>

          <!-- Proposal & Payment Details -->
          <div class="form-section">
            <div class="form-row proposer-fields-row">
              <div class="form-group grow">
                <label for="offer_date">Offer Date *</label>
                <input id="offer_date" name="offer_date" type="date" class="form-control" required>
              </div>
              <div class="form-group grow">
                <label for="proposal_stage_id">Proposal Stage *</label>
                <select id="proposal_stage_id" name="proposal_stage_id" class="form-control" required>
                  <option value="">Select</option>
                  @foreach($lookupData['stages'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group grow">
                <label for="agency">Agency</label>
                <select id="agency" name="agency" class="form-control">
                  <option value="">Select</option>
                  @foreach($lookupData['agencies'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group grow">
                <label for="source_name">Source</label>
                <select id="source_name" name="source_name" class="form-control">
                  <option value="">Select</option>
                  @foreach($lookupData['sources'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>

              </div>

              
                <div class="form-group grow">
                <label for="contact_id">Contact Name</label>
                <select id="contact_id"  name="contact_id"  class="form-control field-required">
                  <option value="">Select Contact</option>
                  @foreach($lookupData['contacts'] as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->contact_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
           
            <div class="form-section-title " style="margin-top:6px;">Payment Plan</div>
            <div class="form-row proposer-fields-row">
              <div class="form-group grow">
                <label for="frequency">Frequency Of Payment *</label>
                <select id="frequency_id" name="frequency_id" class="form-control" required>
                  <option value="">Select</option>
                  @foreach($lookupData['frequencies'] as $s) <option value="{{ $s['id'] }}">{{  $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group grow">
                <label for="method_of_payment">Method Of Payment</label>
                <select id="method_of_payment" name="method_of_payment" class="form-control">
                  <option value="">Select</option>
                  @foreach($lookupData['method_of_payment_options'] as $s) <option value="{{  $s['id'] }}">{{  $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group grow">
                <label for="source_of_payment_id">Source Of Payment *</label>
                <select id="source_of_payment_id" name="source_of_payment_id" class="form-control" required>
                  <option value="">Select</option>
                  @foreach($lookupData['sources_of_payment'] as $s) <option value="{{ $s['id'] }}">{{  $s['name'] }}</option> @endforeach
                </select>
              </div>
                <div class="form-group grow ">
                 <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
              <label for="base_premium" style="font-size: 11px; font-weight: bold; color: #2d2d2d; margin-bottom: 4px; display: block;">
                Base Premium
              </label>
              <input 
                id="base_premium" 
                name="base_premium" 
                type="number" 
                step="0.01" 
                class="form-control" 
                oninput="calculateTotalPremium()"
                style="width: 100%; background: transparent !important;"
              >
            </div>

            <div style="flex: 1;">
              <label for="admin_fee" style="font-size: 11px; font-weight: bold; color: #2d2d2d; margin-bottom: 4px; display: block;">
                Admin Fee
              </label>
              <input 
                id="admin_fee" 
                name="admin_fee" 
                type="number" 
                step="0.01" 
                class="form-control" 
                oninput="calculateTotalPremium()"
                style="width: 100%; background: transparent !important;"
              >
            </div>
          </div>
                </div>
                   <div class="form-group grow">
                <label for="total_premium">Total Premium</label>
                <input id="total_premium" name="total_premium" type="number" step="0.01" class="form-control readonly-field" readonly  style="width: 100%; background: transparent !important;">
              </div>
            </div>
          
          </div>

          <!-- Medical Examination Required Section -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px;">
                  <input type="checkbox" id="medical_examination_required" name="medical_examination_required" value="1" class="rider-checkbox" onchange="toggleMedicalFields()" 
                  >
                  <span>Medical Examination Required?</span>
                </label>
              </div>
            </div>
            <div id="medicalFields" style="display:none;">
              <div class="form-row">
                <div class="form-group">
                  <label for="clinic">Clinic</label>
                  <select id="clinic" name="clinic" class="form-control field-required">
                    <option value="">Select</option>
                    @foreach($lookupData['clinics'] as $s) <option value="{{ $s['id'] }}">{{ $s['name']  }}</option> @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="medical_type_id">Medical Type</label>
                  <select id="medical_type_id" name="medical_type_id" class="form-control field-required">
                    <option value="">Select</option>
                    @foreach($lookupData['medical_types'] as $s) <option value="{{ $s['id'] }}">{{ $s['name']  }}</option> @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="medical_status_id">Medical Status </label>
                  <select id="medical_status_id" name="medical[status_id]"  class="form-control field-required">
                    <option value="">Select</option>
                    @foreach($lookupData['medical_statuses'] as $s) <option value="{{ $s['id'] }}">{{ $s['name']  }}</option> @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="date_referred">Date Referred</label>
                  <input id="date_referred" name="date_referred" type="date" class="form-control field-required">
                </div>
                <div class="form-group">
                  <label for="date_completed">Date Completed</label>
                  <input id="date_completed" name="date_completed" type="date" class="form-control field-required">
                </div>
                 <div class="form-group full-width">
                  <label for="exam_notes">Exam Notes</label>
                  <textarea id="exam_notes" name="exam_notes" class="form-control field-required" rows="1"></textarea>
                </div>
              </div>
       
            </div>
          </div>

          <!-- Application Details Section -->
          <div class="form-section">
            <div class="form-section-title">Application Details</div>
            <div class="form-row proposer-fields-row">
              <div class="form-group grow">
                <label for="date">Date *</label>
                <input id="date" name="date" type="date" class="form-control" required>
              </div>
              <div class="form-group grow">
                <label for="status_id">Proposal Status *</label>
                <select id="status_id" name="status_id" class="form-control" required>
                  <option value="">Select</option>
                  @foreach($lookupData['statuses'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
              </div>
              <div class="form-group grow">
                <label for="policy_no">Policy No</label>
                <input id="policy_no" name="policy_no" class="form-control">
              </div>
              <div class="form-group grow">
                <label for="loading_premium">Loading Premium</label>
                <input id="loading_premium" name="loading_premium" type="number" step="0.01" class="form-control">
              </div>
                <div class="form-group grow">
                <label for="start_date">Start Date</label>
                <input id="start_date" name="start_date" type="date" class="form-control">
              </div>
               <div class="form-group grow">
                <label for="maturity_date">Maturity Date</label>
                <input id="maturity_date" name="maturity_date" type="date" class="form-control">
              </div>
            </div>
          </div>

        

          <!-- Hidden Fields -->
          <input type="hidden" id="prid" name="prid">
          <input type="hidden" id="premium" name="premium">
          <input type="hidden" id="class" name="class" value="">
          <input type="hidden" id="is_submitted" name="is_submitted" value="0">
        </div>
      </form>
    </div>
  </div>
  </div>
@include('partials.column-selection-modal', [
  'selectedColumns' => $selectedColumns,
  'columnDefinitions' => $columnDefinitions,
  'mandatoryColumns' => $mandatoryColumns,
  'columnSettingsRoute' => route('life-proposals.save-column-settings'),
])

<!-- Document Upload Modal -->
<div class="modal" id="documentUploadModal" style="display:none;">
  <div class="modal-content" style="max-width:500px;">
    <div class="modal-header">
      <h4>Upload Document</h4>
      <button class="modal-close" onclick="closeDocumentUploadModal()">&times;</button>
    </div>
    <form id="documentUploadForm" onsubmit="uploadDocument(event)">
      @csrf
      <input type="hidden" name="proposal_id" id="documentProposalId">
      <div class="modal-body">
        <div class="form-group">
          <label>Document Type *</label>
          <select name="document_type" class="form-control" required>
            <option value="">Select Type</option>
            <option value="proposal_document">Proposal Document</option>
            <option value="medical_report">Medical Report</option>
            <option value="id_document">ID Document</option>
            <option value="other">Other Document</option>
          </select>
        </div>
        <div class="form-group">
          <label>Document File *</label>
          <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
        </div>
      </div>
      <div style="padding:10px 15px; border-top:1px solid #ddd; display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn-cancel" onclick="closeDocumentUploadModal()">Cancel</button>
        <button type="submit" class="btn-save">Upload</button>
      </div>
    </form>
  </div>
</div>




<script>
  // Initialize data from Blade
  let currentProposalId = null;
  const lookupData = @json($lookupData);
  const selectedColumns = @json($selectedColumns);
  const mandatoryFields = @json($mandatoryColumns ?? []);
  const lifeProposalsStoreRoute = '{{ route("life-proposals.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/life-proposals-index.js') }}"></script>
@endsection
