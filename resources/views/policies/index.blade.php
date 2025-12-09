@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('policies');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('policies');
  $columnDefinitions = $config['column_definitions'];
  $mandatoryColumns = $config['mandatory_columns'];
@endphp

<div class="dashboard">
  <!-- Main Policies Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Policies Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Policies</h3>
        <div class="records-found">Records Found - {{ $policies->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
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
        <button class="btn btn-add" id="addPolicyBtn">Add</button>
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
              $policyStatusName = $policy->policy_status_name ?? 'N/A';
              $isDFR = stripos($policyStatusName, 'DFR') !== false || (optional($policy->end_date) && $policy->end_date->isBetween(now(), now()->addDays(30)));
              $isExpired = stripos($policyStatusName, 'Expired') !== false || (optional($policy->end_date) && $policy->end_date->isPast());
            @endphp
            <tr class="{{ $isExpired ? 'expired-row' : ($isDFR ? 'dfr-row' : '') }}">
              <td class="bell-cell {{ $isExpired ? 'expired' : ($isDFR ? 'dfr' : '') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  <input type="radio" name="policy_select" class="bell-radio {{ $isExpired ? 'expired' : ($isDFR ? 'dfr' : 'normal') }}" value="{{ $policy->id }}" disabled {{ ($isExpired || $isDFR) ? 'checked' : '' }}>
                </div>
              </td>
              <td class="action-cell">
                <svg class="action-expand" onclick="openPolicyDetails({{ $policy->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="action-clock" onclick="window.location.href='{{ route('policies.index') }}?dfr=true'" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <circle cx="12" cy="12" r="9" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 7V12L15 15" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span class="action-ellipsis" style="cursor:pointer;">⋯</span>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'policy_no')
                  <td data-column="policy_no"><a href="javascript:void(0)" onclick="openPolicyDetails({{ $policy->id }})" style="color:#007bff; text-decoration:underline;">{{ $policy->policy_no }}</a></td>
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
                @elseif($col == 'policy_id')
                  <td data-column="policy_id">{{ $policy->policy_code ?? $policy->id }}</td>
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

    <div class="footer">
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
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="policyPageTitle">Policy</span> - <span class="client-name" id="policyPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editPolicyFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closePolicyPageBtn" onclick="closePolicyPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Policy Details View -->
        <div id="policyDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="policyDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>
        
        <!-- Policy Edit/Add Form -->
        <div id="policyFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="policyDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deletePolicy()">Delete</button>
                <button type="submit" form="policyForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closePolicyFormBtn" onclick="closePolicyPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="policyForm" method="POST" action="{{ route('policies.store') }}">
              @csrf
              <div id="policyFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Policy Modal (hidden, used for form structure) -->
  <div class="modal" id="policyModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="policyModalTitle">Add Policy</h4>
        <button type="button" class="modal-close" onclick="closePolicyModal()">×</button>
        </div>
      <form id="policyForm" method="POST" action="{{ route('policies.store') }}">
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
              <input id="term_unit" name="term_unit" class="form-control">
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
              <label for="agent">Agent</label>
              <input id="agent" name="agent" class="form-control">
            </div>
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closePolicyModal()">Cancel</button>
          <button type="button" class="btn-delete" id="policyDeleteBtn" style="display:none;" onclick="deletePolicy()">Delete</button>
          <button type="submit" class="btn-save">Save</button>
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

<script>
  let currentPolicyId = null;
  const lookupData = @json($lookupData);
  const selectedColumns = @json($selectedColumns);

  // Open policy details (full page view) - MUST be defined before event listeners
  async function openPolicyDetails(id){
    try {
      const res = await fetch(`/policies/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      const policy = data.policy || data;
      currentPolicyId = id;
      
      // Get all required elements
      const policyPageName = document.getElementById('policyPageName');
      const policyPageTitle = document.getElementById('policyPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const policyPageView = document.getElementById('policyPageView');
      const policyDetailsPageContent = document.getElementById('policyDetailsPageContent');
      const policyFormPageContent = document.getElementById('policyFormPageContent');
      const editPolicyFromPageBtn = document.getElementById('editPolicyFromPageBtn');
      const closePolicyPageBtn = document.getElementById('closePolicyPageBtn');
      
      if (!policyPageName || !policyPageTitle || !clientsTableView || !policyPageView || 
          !policyDetailsPageContent || !policyFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set policy name in header
      const policyName = policy.policy_no || 'Unknown';
      policyPageName.textContent = policyName;
      policyPageTitle.textContent = 'Policy';
      
      populatePolicyDetails(policy);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      policyPageView.style.display = 'block';
      policyPageView.classList.add('show');
      policyDetailsPageContent.style.display = 'block';
      policyFormPageContent.style.display = 'none';
      if (editPolicyFromPageBtn) editPolicyFromPageBtn.style.display = 'inline-block';
      if (closePolicyPageBtn) closePolicyPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading policy details: ' + e.message);
    }
  }

  document.getElementById('addPolicyBtn').addEventListener('click', () => openPolicyPage('add'));
  document.getElementById('columnBtn').addEventListener('click', () => openColumnModal());
  
  // Edit button from details page
  const editBtn = document.getElementById('editPolicyFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentPolicyId) {
        openEditPolicy(currentPolicyId);
      }
    });
  }

  // DFR Only Filter
  (function(){
    const btn = document.getElementById('dfrOnlyBtn');
    if (btn) {
      btn.addEventListener('click', () => {
        const u = new URL(window.location.href);
        if (u.searchParams.get('dfr') === 'true') {
          u.searchParams.delete('dfr');
        } else {
          u.searchParams.set('dfr', 'true');
        }
        window.location.href = u.toString();
      });
    }
    const listAllBtn = document.getElementById('listAllBtn');
    if (listAllBtn) {
      listAllBtn.addEventListener('click', () => {
        window.location.href = '{{ route("policies.index") }}';
      });
    }
  })();

  // Populate policy details view
  function populatePolicyDetails(policy) {
    const content = document.getElementById('policyDetailsContent');
    if (!content) return;

    function formatDate(dateStr) {
      if (!dateStr) return '-';
      const date = new Date(dateStr);
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
    }

    function formatNumber(num) {
      if (!num && num !== 0) return '-';
      return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">POLICY DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Policy No</span>
            <div class="detail-value">${policy.policy_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Client</span>
            <div class="detail-value">${policy.client_name || (policy.client ? (policy.client.first_name + ' ' + policy.client.surname) : '-')}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurer</span>
            <div class="detail-value">${policy.insurer_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Class</span>
            <div class="detail-value">${policy.policy_class_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Plan</span>
            <div class="detail-value">${policy.policy_plan_name || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">COVERAGE</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Sum Insured</span>
            <div class="detail-value">${formatNumber(policy.sum_insured)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Start Date</span>
            <div class="detail-value">${formatDate(policy.start_date)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">End Date</span>
            <div class="detail-value">${formatDate(policy.end_date)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insured</span>
            <div class="detail-value">${policy.insured || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insured Item</span>
            <div class="detail-value">${policy.insured_item || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">STATUS & DATES</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Policy Status</span>
            <div class="detail-value">${policy.policy_status_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Registered</span>
            <div class="detail-value">${formatDate(policy.date_registered)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Renewable</span>
            <div class="detail-value checkbox">
              <input type="checkbox" ${policy.renewable ? 'checked' : ''} disabled>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Business Type</span>
            <div class="detail-value">${policy.business_type_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Term</span>
            <div class="detail-value">${policy.term ? policy.term + (policy.term_unit ? ' ' + policy.term_unit : '') : '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header">PREMIUM & PAYMENT</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Base Premium</span>
            <div class="detail-value">${formatNumber(policy.base_premium)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Premium</span>
            <div class="detail-value">${formatNumber(policy.premium)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Frequency</span>
            <div class="detail-value">${policy.frequency_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Pay Plan</span>
            <div class="detail-value">${policy.pay_plan_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Agency</span>
            <div class="detail-value">${policy.agency_name || policy.agent || '-'}</div>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">ADDITIONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Agent</span>
            <div class="detail-value">${policy.agent || '-'}</div>
          </div>
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Notes</span>
            <textarea class="detail-value" style="min-height:40px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${policy.notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open policy page (Add or Edit)
  async function openPolicyPage(mode) {
    if (mode === 'add') {
      openPolicyForm('add');
          } else {
      if (currentPolicyId) {
        openEditPolicy(currentPolicyId);
      }
    }
  }

  async function openEditPolicy(id){
    try {
      const res = await fetch(`/policies/${id}/edit`, { 
        headers: { 
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        } 
      });
      if (!res.ok) throw new Error('Network error');
      const policy = await res.json();
      currentPolicyId = id;
      openPolicyForm('edit', policy);
    } catch (e) {
      console.error(e);
      alert('Error loading policy data');
    }
  }

  function openPolicyForm(mode, policy = null){
    // Clone form from modal
    const modalForm = document.getElementById('policyModal').querySelector('form');
    const pageForm = document.getElementById('policyForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('policyFormMethod');
    const deleteBtn = document.getElementById('policyDeleteBtn');
    const editBtn = document.getElementById('editPolicyFromPageBtn');
    const closeBtn = document.getElementById('closePolicyPageBtn');
    const closeFormBtn = document.getElementById('closePolicyFormBtn');

    if (mode === 'add') {
      document.getElementById('policyPageTitle').textContent = 'Add Policy';
      document.getElementById('policyPageName').textContent = '';
      pageForm.action = '{{ route("policies.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'none';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
          } else {
      const policyName = policy.policy_no || 'Unknown';
      document.getElementById('policyPageTitle').textContent = 'Edit Policy';
      document.getElementById('policyPageName').textContent = policyName;
      pageForm.action = `/policies/${currentPolicyId}`;
      formMethod.innerHTML = `@method('PUT')`;
      deleteBtn.style.display = 'inline-block';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'none';
      if (closeFormBtn) closeFormBtn.style.display = 'inline-block';

      const fieldMap = {
        'policy_no': 'policy_no', 'client_name': 'client_id', 'insurer': 'insurer_id',
        'policy_class': 'policy_class_id', 'policy_plan': 'policy_plan_id', 'sum_insured': 'sum_insured',
        'start_date': 'start_date', 'end_date': 'end_date', 'insured': 'insured',
        'policy_status': 'policy_status_id', 'date_registered': 'date_registered', 'policy_id': 'policy_code',
        'insured_item': 'insured_item', 'renewable': 'renewable', 'biz_type': 'business_type_id',
        'term': 'term', 'term_unit': 'term_unit', 'base_premium': 'base_premium',
        'premium': 'premium', 'frequency': 'frequency_id', 'pay_plan': 'pay_plan_lookup_id',
        'agency': 'agency_id', 'agent': 'agent', 'notes': 'notes'
      };

      Object.keys(fieldMap).forEach(oldKey => {
        const newKey = fieldMap[oldKey];
        const el = formContentDiv ? formContentDiv.querySelector(`#${newKey}`) : null;
        if (!el) return;
        let value = policy[oldKey] ?? policy[newKey] ?? '';
        if (el.type === 'checkbox') {
          el.checked = !!value;
        } else if (el.type === 'date') {
          el.value = value ? (typeof value === 'string' ? value.substring(0,10) : value) : '';
        } else if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
          el.value = value;
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const policyPageView = document.getElementById('policyPageView');
    policyPageView.style.display = 'block';
    policyPageView.classList.add('show');
    document.getElementById('policyDetailsPageContent').style.display = 'none';
    document.getElementById('policyFormPageContent').style.display = 'block';
  }

  function closePolicyPageView(){
    const policyPageView = document.getElementById('policyPageView');
    policyPageView.classList.remove('show');
    policyPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('policyDetailsPageContent').style.display = 'none';
    document.getElementById('policyFormPageContent').style.display = 'none';
    currentPolicyId = null;
  }

  function closePolicyModal(){
    closePolicyPageView();
  }

  function deletePolicy(){
    if (!currentPolicyId) return;
    if (!confirm('Delete this policy?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/policies/${currentPolicyId}`;
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

@endsection
