@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/policies-index.css') }}">




@php
  $config = \App\Helpers\TableConfigHelper::getConfig('policies');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('policies');
  $columnDefinitions = $config['column_definitions'];
  $mandatoryColumns = $config['mandatory_columns'];
@endphp

<div class="dashboard">
  <!-- Error/Success Messages -->
  @if(session('error'))
    <div class="alert alert-error" style="background:#fee; border:1px solid #fcc; color:#c33; padding:12px; margin:15px; border-radius:4px;">
      {{ session('error') }}
    </div>
  @endif
  @if(session('success'))
    <div class="alert alert-success" style="background:#efe; border:1px solid #cfc; color:#3c3; padding:12px; margin:15px; border-radius:4px;">
      {{ session('success') }}
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-error" style="background:#fee; border:1px solid #fcc; color:#c33; padding:12px; margin:15px; border-radius:4px;">
      <strong>Please fix the following errors:</strong>
      <ul style="margin:8px 0 0 0; padding-left:20px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  
  <!-- Main Policies Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
            Policies
            @if(isset($client) && $client)
              <span class="client-name" style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $client->client_name }}</span>
            @endif
          </h3>
       
      </div>
    </div>
  <div class="container-table">
    <!-- Policies Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <div class="records-found">Records Found - {{ $policies->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
        <div class="filter-group">
            <label class="toggle-switch">
              <input type="checkbox" id="filterToggle" {{ (request()->get('dfr') == 'true') || request()->has('search_term') || request()->has('client_name') || request()->has('policy_number') || request()->has('insurer_id') || request()->has('policy_class_id') || request()->has('agency_id') || request()->has('agent') || request()->has('policy_status_id') || request()->has('start_date_from') || request()->has('end_date_from') || request()->has('premium_unpaid') || request()->has('comm_unpaid') ? 'checked' : '' }}>
              <span class="toggle-slider"></span>
            </label>
            <label for="filterToggle" style="font-size:14px; color:#2d2d2d; margin:0; cursor:pointer; user-select:none;">Filter</label>
          </div>
        <div class="filter-group">
            @if(request()->get('dfr') == 'true')
              <button class="btn btn-list-all" id="listAllBtn">List ALL</button>
            @else
              <button class="btn btn-follow-up" id="dfrOnlyBtn">Due For Renewal</button>
            @endif
        </div>
        </div>
      </div>
      <div class="action-buttons">
        <button type="button" class="btn btn-add" id="addPolicyBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="policiesTable">
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
          @foreach($policies as $policy)
            @php
              // Get policy status name from relationship
              $policyStatusName = 'N/A';
              if ($policy->policyStatus && is_object($policy->policyStatus) && isset($policy->policyStatus->name)) {
                $policyStatusName = $policy->policyStatus->name;
              } elseif ($policy->policy_status_name) {
                $policyStatusName = $policy->policy_status_name;
              }
              // Determine if policy is DFR (Due For Renewal) - check status name and date range
              $isDFR = stripos($policyStatusName, 'DFR') !== false || 
                       (optional($policy->end_date) && $policy->end_date && $policy->end_date->isBetween(now(), now()->addDays(30)));
              
              // Determine if policy is Expired - check status name and if end date is in the past
              $isExpired = stripos($policyStatusName, 'Expired') !== false || 
                          (optional($policy->end_date) && $policy->end_date && $policy->end_date->isPast());
              
              // Ensure expired takes priority over DFR
              if ($isExpired) {
                $isDFR = false;
              }
            @endphp
            <tr class="{{ $isExpired ? 'expired-row' : ($isDFR ? 'dfr-row' : '') }}">
              <td class="bell-cell {{ $isExpired ? 'expired' : ($isDFR ? 'dfr' : '') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  <div class="status-indicator {{ $isExpired ? 'expired' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $isExpired ? '#dc3545' : '#f3742a' }}; background-color:{{ $isExpired ? '#dc3545' : 'transparent' }};"></div>
                </div>
              </td>
              <td class="action-cell">
                <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openPolicyDetails({{ $policy->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand"> 
              
               
                <svg class="action-clock" onclick="window.location.href='{{ route('policies.index') }}?dfr=true'" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <circle cx="12" cy="12" r="9" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 7V12L15 15" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span class="action-ellipsis" style="cursor:pointer;">⋯</span>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'policy_no')
                  <td data-column="policy_no">{{ $policy->policy_no }}</td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">
                    @php $clientName = $policy->client_name; @endphp
                    @if($clientName){{ $clientName }}@else<span style="color:#999;" title="Client ID: {{ $policy->client_id ?? 'NULL' }}">—</span>@endif
                  </td>
                @elseif($col == 'insurer')
                  <td data-column="insurer">
                    @php $insurerName = $policy->insurer_name; @endphp
                    @if($insurerName){{ $insurerName }}@else<span style="color:#999;" title="Insurer ID: {{ $policy->insurer_id ?? 'NULL' }}">—</span>@endif
                  </td>
                @elseif($col == 'policy_class')
                  <td data-column="policy_class">{{ $policy->policy_class_name ?? '—' }}</td>
                @elseif($col == 'policy_plan')
                  <td data-column="policy_plan">{{ $policy->policy_plan_name ?? '—' }}</td>
                @elseif($col == 'sum_insured')
                  <td data-column="sum_insured">{{ $policy->sum_insured ? number_format($policy->sum_insured,2) : '###########' }}</td>
                @elseif($col == 'start_date')
                  <td data-column="start_date">{{ $policy->start_date ? $policy->start_date->format('d-M-y') : '###########' }}</td>
                @elseif($col == 'end_date')
                  <td data-column="end_date">{{ $policy->end_date ? $policy->end_date->format('d-M-y') : '###########' }}</td>
                @elseif($col == 'insured')
                  <td data-column="insured">{{ $policy->insured ?? '###########' }}</td>
                @elseif($col == 'policy_status')
                  @php
                    $statusColor = '#6c757d';
                    if ($isExpired || stripos($policyStatusName, 'Expired') !== false) {
                      $statusColor = '#dc3545';
                    } elseif ($isDFR || stripos($policyStatusName, 'DFR') !== false || stripos($policyStatusName, 'Due') !== false) {
                      $statusColor = '#ffc107';
                    } elseif (stripos($policyStatusName, 'In Force') !== false) {
                      $statusColor = '#28a745';
                    } elseif (stripos($policyStatusName, 'Cancelled') !== false) {
                      $statusColor = '#dc3545';
                    }
                  @endphp
                  <td data-column="policy_status"><span class="badge-status" style="background:{{ $statusColor }}">{{ $policyStatusName }}</span></td>
                @elseif($col == 'date_registered')
                  <td data-column="date_registered">{{ $policy->date_registered ? $policy->date_registered->format('d-M-y') : '###########' }}</td>
                @elseif($col == 'policy_code')
                  <td data-column="policy_code">{{ $policy->policy_code ?? '' }}</td>
                @elseif($col == 'insured_item')
                  <td data-column="insured_item">{{ $policy->insured_item ?? '-' }}</td>
                @elseif($col == 'renewable')
                  <td data-column="renewable">{{ $policy->renewable ? 'Yes' : 'No' }}</td>
                @elseif($col == 'biz_type')
                  <td data-column="biz_type">{{ $policy->business_type_name ?? 'N/A' }}</td>
                @elseif($col == 'term')
                  <td data-column="term">{{ $policy->term ?? '-' }}</td>
                @elseif($col == 'term_unit')
                  <td data-column="term_unit">{{ $policy->term_unit ?? '-' }}</td>
                @elseif($col == 'base_premium')
                  <td data-column="base_premium">{{ $policy->base_premium ? number_format($policy->base_premium,2) : '###########' }}</td>
                @elseif($col == 'premium')
                  <td data-column="premium">{{ $policy->premium ? number_format($policy->premium,2) : '###########' }}</td>
                @elseif($col == 'frequency')
                  <td data-column="frequency">{{ $policy->frequency_name ?? 'N/A' }}</td>
                @elseif($col == 'pay_plan')
                  <td data-column="pay_plan">{{ $policy->pay_plan_name ?? 'N/A' }}</td>
                @elseif($col == 'agency')
                  <td data-column="agency">{{ $policy->agency_name ?? ($policy->agent ?? '-') }}</td>
                @elseif($col == 'agent')
                  <td data-column="agent">{{ $policy->agent ?? '-' }}</td>
                @elseif($col == 'notes')
                  <td data-column="notes">{{ $policy->notes ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; margin-top:0;">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('policies.export', array_merge(request()->query(), ['page' => $policies->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button">Column</button>
      </div>
        <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $policies->currentPage();
          $last = max(1,$policies->lastPage());
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

  <!-- Policy Page View (Full Page) -->
  <div class="client-page-view" id="policyPageView" style="display:none;">
    <!-- Header Card with Policy Number -->
    <div class="client-page-header" style="display:none;">
      <div class="client-page-title">
        <span id="policyPageTitle">Policy No</span> - <span class="client-name" id="policyPageName">-</span>
      </div>
    </div>
    
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Navigation Tabs and Actions Card -->
      
        
        <!-- Policy Details Content Card - Separate -->
        <div id="policyDetailsContentWrapper" style="display:none; background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:12px; overflow:hidden;">
        <div id="policyDetailsPageContent" style="display:none;">
          
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
              <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
                <div class="client-page-nav">
           
                <button class="policy-tab active" data-tab="schedules" data-url="{{ route('schedules.index') }}">Schedules</button>
                <button class="policy-tab" data-tab="payments" data-url="{{ route('payments.index') }}">Payments</button>
                <button class="policy-tab" data-tab="vehicles" data-url="{{ route('vehicles.index') }}">Vehicles</button>
                <button class="policy-tab" data-tab="claims" data-url="{{ route('claims.index') }}">Claims</button>
                <button class="policy-tab" data-tab="documents" data-url="{{ route('documents.index') }}">Documents</button>
                <button class="policy-tab" data-tab="endorsements" data-url="{{ route('endorsements.index') }}">Endorsements</button>
                <button class="policy-tab" data-tab="commissions" data-url="{{ route('commissions.index') }}">Commission</button>
                <button class="policy-tab" data-tab="nominees" data-url="{{ route('nominees.index') }}">Nominees</button>

                </div>
                <div class="client-page-actions" id="policyHeaderActions">
                  <button class="btn btn-edit" id="editPolicyFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px; display:none;" onclick="if(currentPolicyId) openEditPolicy(currentPolicyId)">Edit</button>
                  <button class="btn" id="renewPolicyBtn" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px; display:none;" onclick="openRenewalModal()">Renew</button>
                  <button class="btn" id="closePolicyPageBtn" onclick=
                  
                  "closePolicyPageView()" style="background:#e0e0e0; color:#000; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px;">Close</button>
                </div>
              </div>
            </div>
          </div> 
        <div id="policyDetailsContent" style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:0; padding:0;">
              <!-- Content will be loaded via JavaScript -->
            </div>
        </div>
        
        <!-- Policy Schedule Card - Separate -->
        <div id="policyScheduleContentWrapper" style="display:none; background:#fff; border:1px solid #ddd; border-radius:4px; padding:12px;  margin-bottom:15px; overflow:hidden;">
          <div style="padding:10px 10px 8px 10px; border-bottom:1px solid #ddd;">
            <h4 style="margin:0; font-size:12px; font-weight:600; color:#333;">Policy Schedule</h4>
          </div>
          <div id="policyScheduleContent" style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:0; padding:0;">
            <!-- Content will be loaded via JavaScript -->
          </div>
        </div>
        
        <!-- Documents Card - Separate -->
        <div id="documentsContentWrapper" style="display:none; background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; border-bottom:1px solid #ddd;">
            <h4 style="margin:0; font-size:12px; font-weight:600; color:#333;">Documents</h4>
            <button class="btn" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px;">Add Document</button>
          </div>
          <div id="documentsContent" style="display:flex; gap:10px; flex-wrap:wrap; padding:10px;">
            <!-- Documents will be loaded via JavaScript -->
          </div>
        </div>
        
        <!-- Policy Add Form -->
        <div id="policyFormPageContent" style="display:none;">
          <!-- Header for Add/Edit Policy -->
          <!-- <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:12px 15px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <h4 id="policyFormTitle" style="margin:0; font-size:16px; font-weight:600; color:#333;">Policy - Add New</h4>
               <div class="client-page-actions" id="policyFormHeaderActions">
                <button type="submit" form="policyForm" class="btn-save" id="policySaveBtnHeader" style="display:inline-block; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px; margin-right:8px;">Save</button>
                <button type="button" class="btn" id="backPolicyFormBtnHeader" style="display:none; background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px; margin-right:8px;" onclick="window.history.back()">Back</button>
                <button type="button" class="btn" id="closePolicyFormBtnHeader" style="display:inline-block; background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px;" onclick="closePolicyPageView()">Close</button>
              </div> 
             </div>
          </div>
           
          <!-- Navigation Tabs (only for Edit mode or Life Proposal generated) -->
          <div id="policyFormTabs" style="background:#fff; border:1px solid #584545ff; border-radius:4px; margin-bottom:15px; overflow:hidden; display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-nav" id="policyFormTabsNav">
                <a href="{{ route('schedules.index') }}" class="policy-tab active">Schedules</a>
                <a href="{{ route('payments.index') }}" class="policy-tab">Payments</a>
                <a href="{{ route('vehicles.index') }}" class="policy-tab">Vehicles</a>
                <a href="{{ route('claims.index') }}" class="policy-tab">Claims</a>
                <a href="{{ route('documents.index') }}" class="policy-tab">Documents</a>
                <a href="#" class="policy-tab" onclick="alert('Endorsements page coming soon'); return false;">Endorsements</a>
                <a href="{{ route('commissions.index') }}" class="policy-tab">Commission</a>
                <a href="{{ route('nominees.index') }}" class="policy-tab">Nominees</a>
              </div>
                <div class="client-page-actions" id="policyFormHeaderActions">
                <button type="submit" form="policyForm" class="btn-save" id="policySaveBtnHeader" style="display:inline-block; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px; margin-right:8px;">Save</button>
                <button type="button" class="btn" id="backPolicyFormBtnHeader" style="display:none; background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px; margin-right:8px;" onclick="window.history.back()">Back</button>
                <button type="button" class="btn" id="closePolicyFormBtnHeader" style="display:inline-block; background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px;" onclick="closePolicyPageView()">Close</button>
              </div>
            </div>
          </div>
          
          <!-- Policy Form - Single form wrapping all fields -->
          <form id="policyForm" method="POST" action="{{ route('policies.store') }}" enctype="multipart/form-data">
              @csrf
              <div id="policyFormMethod" style="display:none;"></div>
            
            <!-- Policy Form Content Card -->
            <div id="policyFormContentWrapper" style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:0; overflow:hidden;">
              <!-- Content will be loaded via JavaScript -->
              <div id="policyFormContent" style="padding:0;">
                <!-- Content will be loaded via JavaScript -->
              </div>
          </div>
            
            <!-- Policy Schedule Card -->
            <div id="policyFormScheduleWrapper" style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:0; overflow:hidden;">
              <div id="policyFormScheduleContent" style="padding:0;">
                <!-- Content will be loaded via JavaScript -->
              </div>
            </div>
            
            <!-- Documents Card -->
            <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
              <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; border-bottom:1px solid #ddd;">
                <h4 style="margin:0; font-size:13px; font-weight:600; color:#333;">Documents</h4>
                <div>
                  <button type="button" class="btn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:12px;" onclick="openPolicyDocumentUploadModal()">Upload Document</button>
                </div>
              </div>
              <div id="policyFormDocumentsContent" style="display:flex; gap:10px; flex-wrap:wrap; padding:12px; min-height:100px;">
                <!-- Documents will be loaded via JavaScript -->
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Policy Modal (hidden, used for form structure) -->
  <div class="modal" id="policyModal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="policyModalTitle">Add Policy</h4>
        <button type="button" class="modal-close" onclick="closePolicyModal()">×</button>
        </div>
      <form id="policyModalForm" method="POST" action="{{ route('policies.store') }}">
        @csrf
        <div id="policyFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="policy_no">Policy No *</label>
              <input id="policy_no" name="policy_no" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="client_id">Client *</label>
              <select id="client_id" name="client_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['clients'] as $client)
                  <option value="{{ $client['id'] }}">{{ $client['client_name'] }} ({{ $client['clid'] ?? '' }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="insurer_id">Insurer *</label>
              <select id="insurer_id" name="insurer_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['insurers'] as $insurer)
                  <option value="{{ $insurer['id'] }}">{{ $insurer['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="policy_class_id">Policy Class *</label>
              <select id="policy_class_id" name="policy_class_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['policy_classes'] as $class)
                  <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="policy_plan_id">Policy Plan *</label>
              <select id="policy_plan_id" name="policy_plan_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['policy_plans'] as $plan)
                  <option value="{{ $plan['id'] }}">{{ $plan['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="sum_insured">Sum Insured</label>
              <input id="sum_insured" name="sum_insured" type="number" step="0.01" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="start_date">Start Date *</label>
              <input id="start_date" name="start_date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="end_date">End Date *</label>
              <input id="end_date" name="end_date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="insured">Insured</label>
              <input id="insured" name="insured" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="policy_status_id">Policy Status</label>
              <select id="policy_status_id" name="policy_status_id" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['policy_statuses'] as $status)
                  <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="date_registered">Date Registered *</label>
              <input id="date_registered" name="date_registered" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="insured_item">Insured Item</label>
              <input id="insured_item" name="insured_item" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="renewable">Renewable</label>
              <input id="renewable" name="renewable" type="checkbox" value="1">
            </div>
            <div class="form-group">
              <label for="business_type_id">Business Type</label>
              <select id="business_type_id" name="business_type_id" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['business_types'] ?? [] as $bizType)
                  <option value="{{ $bizType['id'] }}">{{ $bizType['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="term">Term</label>
              <input id="term" name="term" type="number" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="term_unit">Term Unit</label>
              <select id="term_unit" name="term_unit" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['term_units'] ?? [] as $termUnit)
                  <option value="{{ $termUnit['name'] }}">{{ $termUnit['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="base_premium">Base Premium</label>
              <input id="base_premium" name="base_premium" type="number" step="0.01" class="form-control">
            </div>
            <div class="form-group">
              <label for="premium">Premium</label>
              <input id="premium" name="premium" type="number" step="0.01" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="frequency_id">Frequency *</label>
              <select id="frequency_id" name="frequency_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['frequencies'] as $freq)
                  <option value="{{ $freq['id'] ?? '' }}">{{ $freq['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="pay_plan_lookup_id">Pay Plan *</label>
              <select id="pay_plan_lookup_id" name="pay_plan_lookup_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['pay_plans'] as $payPlan)
                  <option value="{{ $payPlan['id'] ?? '' }}">{{ $payPlan['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="agency_id">Agency</label>
              <select id="agency_id" name="agency_id" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['agencies'] ?? [] as $agency)
                  <option value="{{ $agency['id'] }}">{{ $agency['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="channel_id">Channel</label>
              <select id="channel_id" name="channel_id" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['channels'] ?? [] as $channel)
                  <option value="{{ $channel['id'] }}">{{ $channel['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="agent">Agent</label>
              <input id="agent" name="agent" class="form-control">
            </div>
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="display:flex; gap:8px; justify-content:flex-end;">
          <button type="button" class="btn-cancel" onclick="closePolicyModal()">Close</button>
          <button type="button" class="btn-delete" id="policyModalDeleteBtn" style="display:none;" onclick="deletePolicy()">Delete</button>
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Document Upload Modal -->
  <div class="modal" id="policyDocumentUploadModal">
    <div class="modal-content" style="max-width:600px;">
      <div class="modal-header">
        <h4>Add Document</h4>
        <button type="button" class="modal-close" onclick="closePolicyDocumentUploadModal()">×</button>
      </div>
      <div class="modal-body">
        <form id="policyDocumentUploadForm">
          <div class="form-group" style="margin-bottom:15px;">
            <label for="policyDocumentType" style="display:block; margin-bottom:5px; font-weight:600;">Document Type</label>
            <select id="policyDocumentType" name="document_type" class="form-control" required>
              <option value="">Select Document Type</option>
              @foreach($lookupData['document_types'] ?? [] as $docType)
                <option value="{{ strtolower(str_replace(' ', '_', $docType['name'])) }}">{{ $docType['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label for="policyDocumentFile" style="display:block; margin-bottom:5px; font-weight:600;">Select File</label>
            <input type="file" id="policyDocumentFile" name="document" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required onchange="previewPolicyDocument(event)">
            <small style="color:#666; font-size:11px;">Accepted formats: JPG, PNG, PDF, DOC, DOCX (Max 5MB)</small>
          </div>
          <div id="policyDocumentPreviewContainer" style="display:none; margin-top:15px; padding:15px; border:1px solid #ddd; border-radius:4px; background:#f9f9f9;">
            <div style="font-weight:600; margin-bottom:10px; font-size:13px;">Preview:</div>
            <div id="policyDocumentPreviewContent" style="display:flex; align-items:center; justify-content:center; min-height:200px;">
              <!-- Preview will be shown here -->
            </div>
            <div id="policyDocumentPreviewInfo" style="margin-top:10px; font-size:12px; color:#666;">
              <!-- File info will be shown here -->
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closePolicyDocumentUploadModal()">Close</button>
        <button type="button" class="btn-save" onclick="handlePolicyDocumentUpload()">Upload</button>
      </div>
    </div>
  </div>

  <!-- Renewal Schedule Details Modal -->
  <div class="modal" id="renewalScheduleModal" style="display:none;" onclick="if(event.target === this) closeRenewalModal();">
    <div class="modal-content" style="max-width:800px;" onclick="event.stopPropagation();">
      <div class="modal-header">
        <h4>Renewal Schedule Details</h4>
      </div>
      <form id="renewalScheduleForm">
        <div class="modal-body" style="padding:20px;">
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Year</label>
              <input type="text" id="renewal_year" name="year" class="form-control" style="text-align:right;">
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Policy Plan</label>
              <input type="text" id="renewal_policy_plan" name="policy_plan" class="form-control" style="text-align:right;">
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Sum Insured</label>
              <input type="number" id="renewal_sum_insured" name="sum_insured" step="0.01" class="form-control" style="text-align:right;">
            </div>
          </div>
          
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Term</label>
              <div style="display:flex; gap:5px;">
                <input type="number" id="renewal_term" name="term" class="form-control" style="flex:1; text-align:right;">
                <input type="text" id="renewal_term_unit" name="term_unit" class="form-control" style="width:80px; text-align:right;" placeholder="Years">
              </div>
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Start Date</label>
              <input type="date" id="renewal_start_date" name="start_date" class="form-control" style="text-align:right;">
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">End Date</label>
              <input type="date" id="renewal_end_date" name="end_date" class="form-control" style="text-align:right; background-color:#f5f5f5;" readonly>
            </div>
          </div>
          
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Add Ons</label>
              <input type="text" id="renewal_add_ons" name="add_ons" class="form-control" style="text-align:right;">
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Base Premium</label>
              <input type="number" id="renewal_base_premium" name="base_premium" step="0.01" class="form-control" style="text-align:right;">
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Full Premium</label>
              <input type="number" id="renewal_full_premium" name="full_premium" step="0.01" class="form-control" style="text-align:right;">
            </div>
          </div>
          
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Pay Plan Type</label>
              <input type="text" id="renewal_pay_plan_type" name="pay_plan_type" class="form-control" style="text-align:right;">
            </div>
            <div class="form-group" style="flex:1;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">NOP/Frequency</label>
              <div style="display:flex; gap:5px;">
                <input type="number" id="renewal_nop" name="nop" class="form-control" style="flex:1; text-align:right;" placeholder="NOP">
                <input type="text" id="renewal_frequency" name="frequency" class="form-control" style="width:100px; text-align:right;" placeholder="Frequency">
              </div>
            </div>
          </div>
          
          <div class="form-row" style="margin-bottom:15px;">
            <div class="form-group" style="width:100%;">
              <label style="display:block; margin-bottom:5px; font-weight:600; text-align:left;">Note</label>
              <textarea id="renewal_note" name="note" class="form-control" rows="3" style="text-align:left;"></textarea>
            </div>
          </div>
          

        </div>
        <div class="modal-footer" style="display:flex; gap:8px; justify-content:flex-end; padding:15px 20px; border-top:1px solid #ddd;">
          <button type="button" class="btn-cancel" onclick="closeRenewalModal()" style="background:#000; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer;">Close</button>
          <button type="submit" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer;">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Vehicle Details Modal -->
  <div class="modal" id="vehicleModal" style="display:none;" onclick="if(event.target === this) closeVehicleDialog();">
    <div class="modal-content" style="max-width:600px;" onclick="event.stopPropagation();">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h4 style="margin:0;">Add Vehicle Details</h4>
        <div style="display:flex; gap:8px; align-items:center;">
          <button type="button" onclick="saveVehicle()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Save</button>
          <button type="button" onclick="closeVehicleDialog()" style="background:#000; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Close</button>
        </div>
      </div>
      <form id="vehicleForm">
        <div class="modal-body" style="padding:20px;">
          <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Registration No.</label>
              <input type="text" name="regn_no" id="vehicle_regn_no" class="form-control" required style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Make</label>
              <input type="text" name="make" id="vehicle_make" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Model</label>
              <input type="text" name="model" id="vehicle_model" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Model Year</label>
              <input type="text" name="year" id="vehicle_year" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Type</label>
              <input type="text" name="type" id="vehicle_type" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Engine Type</label>
              <input type="text" name="engine_type" id="vehicle_engine_type" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Engine CC</label>
              <input type="text" name="cc" id="vehicle_cc" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Engine No.</label>
              <input type="text" name="engine_no" id="vehicle_engine_no" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Chassis No.</label>
              <input type="text" name="chassis_no" id="vehicle_chassis_no" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Value</label>
              <input type="number" step="0.01" name="value" id="vehicle_value" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Usage</label>
              <input type="text" name="useage" id="vehicle_useage" class="form-control" style="padding:6px; font-size:12px;">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Comment</label>
            <textarea name="notes" id="vehicle_notes" class="form-control" rows="3" style="padding:6px; font-size:12px;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="display:flex; gap:8px; justify-content:flex-end; padding:15px 20px; border-top:1px solid #ddd;">
          <button type="button" class="btn-save" onclick="saveVehicleAndAddAnother()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Upload VRC</button>
          <button type="button" class="btn-save" onclick="saveVehicleAndAddAnother()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Add Another Vehicle</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Nominee Details Modal -->
  <div class="modal" id="nomineeModal" style="display:none;" onclick="if(event.target === this) closeNomineeDialog();">
    <div class="modal-content" style="max-width:500px;" onclick="event.stopPropagation();">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h4 style="margin:0;">Add Nominee</h4>
        <div style="display:flex; gap:8px; align-items:center;">
          <button type="button" onclick="saveNominee()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Save</button>
          <button type="button" onclick="closeNomineeDialog()" style="background:#000; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Close</button>
        </div>
      </div>
      <form id="nomineeForm">
        <input type="hidden" name="policy_id" id="nominee_policy_id">
        <div class="modal-body" style="padding:20px;">
          <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Full Name</label>
            <input type="text" name="full_name" id="nominee_full_name" class="form-control" required style="padding:6px; font-size:12px;">
          </div>
          <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Date Of Birth</label>
              <input type="date" name="date_of_birth" id="nominee_date_of_birth" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">NIN/Passport No</label>
              <input type="text" name="nin_passport_no" id="nominee_nin_passport_no" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Relationship</label>
              <input type="text" name="relationship" id="nominee_relationship" class="form-control" style="padding:6px; font-size:12px;">
            </div>
            <div class="form-group">
              <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Share</label>
              <input type="number" step="0.01" name="share_percentage" id="nominee_share_percentage" class="form-control" style="padding:6px; font-size:12px;" placeholder="%">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Notes</label>
            <textarea name="notes" id="nominee_notes" class="form-control" rows="3" style="padding:6px; font-size:12px;"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="display:flex; gap:8px; justify-content:flex-end; padding:15px 20px; border-top:1px solid #ddd;">
          <button type="button" class="btn-save" onclick="saveNomineeAndAddAnother()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Upload ID</button>
          <button type="button" class="btn-save" onclick="saveNomineeAndAddAnother()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Add Another</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Filter Modal -->
  <div class="modal" id="policyFilterModal" style="display:none;" onclick="if(event.target === this) closePolicyFilterModal();">
    <div class="modal-content" style="max-width:600px;" onclick="event.stopPropagation();">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd;">
        <h4 style="margin:0; font-size:16px; font-weight:600;">Filters</h4>
        <div style="display:flex; gap:8px;">
          <button type="button" onclick="applyPolicyFilters()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Apply</button>
          <button type="button" onclick="closePolicyFilterModal()" style="background:#999; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Close</button>
        </div>
      </div>
      <form id="policyFilterForm" onsubmit="event.preventDefault(); applyPolicyFilters();">
        <div class="modal-body" style="padding:20px;">
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Set Record Lines</label>
            <input type="number" id="filterRecordLines" name="record_lines" value="{{ request()->get('record_lines', 15) }}" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Search Term</label>
            <input type="text" id="filterSearchTerm" name="search_term" value="{{ request()->get('search_term') }}" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Client Name</label>
            <input type="text" id="filterClientName" name="client_name" value="{{ request()->get('client_name') }}" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Policy Number</label>
            <input type="text" id="filterPolicyNumber" name="policy_number" value="{{ request()->get('policy_number') }}" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Insurer</label>
            <select id="filterInsurer" name="insurer_id" class="form-control" style="padding:6px; font-size:12px;">
              <option value="">Select</option>
              @foreach($lookupData['insurers'] ?? [] as $insurer)
                <option value="{{ $insurer['id'] }}" {{ request()->get('insurer_id') == $insurer['id'] ? 'selected' : '' }}>{{ $insurer['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Insurance Class</label>
            <select id="filterInsuranceClass" name="policy_class_id" class="form-control" style="padding:6px; font-size:12px;">
              <option value="">Select</option>
              @foreach($lookupData['policy_classes'] ?? [] as $class)
                <option value="{{ $class['id'] }}" {{ request()->get('policy_class_id') == $class['id'] || (request()->get('policy_class_id') == '' && $class['name'] == 'Motor') ? 'selected' : '' }}>{{ $class['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Agency</label>
            <select id="filterAgency" name="agency_id" class="form-control" style="padding:6px; font-size:12px;">
              <option value="">Select</option>
              @foreach($lookupData['agencies'] ?? [] as $agency)
                <option value="{{ $agency['id'] }}" {{ request()->get('agency_id') == $agency['id'] ? 'selected' : '' }}>{{ $agency['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Agent</label>
            <input type="text" id="filterAgent" name="agent" value="{{ request()->get('agent', 'Simon') }}" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Status</label>
            <select id="filterStatus" name="policy_status_id" class="form-control" style="padding:6px; font-size:12px;">
              <option value="">Select</option>
              @foreach($lookupData['policy_statuses'] ?? [] as $status)
                <option value="{{ $status['id'] }}" {{ request()->get('policy_status_id') == $status['id'] ? 'selected' : '' }}>{{ $status['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">From Start Date</label>
            <div style="display:flex; gap:8px; align-items:center;">
              <input type="date" id="filterStartDateFrom" name="start_date_from" value="{{ request()->get('start_date_from') }}" class="form-control" style="padding:6px; font-size:12px; flex:1;">
              <span style="font-size:12px;">To</span>
              <input type="date" id="filterStartDateTo" name="start_date_to" value="{{ request()->get('start_date_to') }}" class="form-control" style="padding:6px; font-size:12px; flex:1;">
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">From End Date</label>
            <div style="display:flex; gap:8px; align-items:center;">
              <input type="date" id="filterEndDateFrom" name="end_date_from" value="{{ request()->get('end_date_from') }}" class="form-control" style="padding:6px; font-size:12px; flex:1;">
              <span style="font-size:12px;">To</span>
              <input type="date" id="filterEndDateTo" name="end_date_to" value="{{ request()->get('end_date_to') }}" class="form-control" style="padding:6px; font-size:12px; flex:1;">
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Premium Unpaid</label>
            <input type="number" step="0.01" id="filterPremiumUnpaid" name="premium_unpaid" value="{{ request()->get('premium_unpaid') }}" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px; margin-bottom:15px; align-items:center;">
            <label style="font-size:13px; font-weight:500;">Comm Unpaid</label>
            <input type="number" step="0.01" id="filterCommUnpaid" name="comm_unpaid" value="{{ request()->get('comm_unpaid') }}" class="form-control" style="padding:6px; font-size:12px;">
          </div>
        </div>
      </form>
    </div>
  </div>

@include('partials.column-selection-modal', [
  'selectedColumns' => $selectedColumns,
  'columnDefinitions' => $columnDefinitions,
  'mandatoryColumns' => $mandatoryColumns,
  'columnSettingsRoute' => route('policies.save-column-settings'),
])



@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

<script>
  // Initialize data from Blade
  let currentPolicyId = null;
  let currentPolicyData = null;
  const lookupData = @json($lookupData ?? []);
  const selectedColumns = @json($selectedColumns ?? []);
  const lifeProposalData = @json($lifeProposal ?? null);
  const policiesIndexRoute = '{{ route("policies.index") }}';
  const policiesStoreRoute = '{{ route("policies.store") }}';
  const vehiclesStoreRoute = '{{ route("vehicles.store") }}';
  const nomineesStoreRoute = '{{ route("nominees.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

<script>
    window.routes = {
        nominees: '{{ route('nominees.index') }}',
        payments: '{{ route('payments.index') }}',
        commissions: '{{ route('commissions.index') }}',
        vehicles: '{{ route('vehicles.index') }}',
        claims: '{{ route('claims.index') }}',
        documents: '{{ route('documents.index') }}',
        schedules: '{{ route('schedules.index') }}',
        


    };
</script>
<script src="{{ asset('js/policies-index.js') }}"></script>
@endsection
