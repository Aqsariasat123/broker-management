@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('payment-plans');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('payment-plans');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Payment Plans Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Payment Plans Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Payment Plans</h3>
        <div class="records-found">Records Found - {{ $paymentPlans->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            <form method="GET" action="{{ route('payment-plans.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
              <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
              <select name="status" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
              <label style="display:flex; align-items:center; gap:4px; font-size:13px;"><input type="checkbox" name="due_soon" value="true" {{ request('due_soon') == 'true' ? 'checked' : '' }}> Due Soon</label>
              <button type="submit" class="btn btn-column" style="background:#fff; color:#000; border:1px solid #ccc;">Filter</button>
              @if(request()->hasAny(['search', 'status', 'due_soon']))
                <a href="{{ route('payment-plans.index') }}" class="btn btn-back" style="background:#ccc; color:#333; border-color:#ccc;">Clear</a>
              @endif
            </form>
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addPaymentPlanBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="paymentPlansTable">
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
          @foreach($paymentPlans as $plan)
            <tr>
              <td class="action-cell">
                <svg class="action-expand" onclick="openPaymentPlanDetails({{ $plan->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'installment_label')
                  <td data-column="installment_label">
                    <a href="javascript:void(0)" onclick="openPaymentPlanDetails({{ $plan->id }})" style="color:#007bff; text-decoration:underline;">{{ $plan->installment_label ?? 'Instalment #' . $plan->id }}</a>
                  </td>
                @elseif($col == 'policy_no')
                  <td data-column="policy_no">{{ $plan->schedule->policy->policy_no ?? '-' }}</td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $plan->schedule->policy->client->client_name ?? '-' }}</td>
                @elseif($col == 'due_date')
                  <td data-column="due_date">{{ $plan->due_date ? $plan->due_date->format('d-M-y') : '-' }}</td>
                @elseif($col == 'amount')
                  <td data-column="amount">{{ $plan->amount ? number_format($plan->amount, 2) : '-' }}</td>
                @elseif($col == 'frequency')
                  <td data-column="frequency">{{ $plan->frequency ?? '-' }}</td>
                @elseif($col == 'status')
                  <td data-column="status">
                    <span class="badge-status badge-{{ $plan->status }}" style="font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; background:{{ $plan->status == 'pending' ? '#ffc107' : ($plan->status == 'active' ? '#17a2b8' : ($plan->status == 'paid' ? '#28a745' : ($plan->status == 'overdue' ? '#dc3545' : '#6c757d'))) }};">
                      {{ ucfirst($plan->status) }}
                    </span>
                  </td>
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
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $paymentPlans->currentPage();
          $last = max(1, $paymentPlans->lastPage());
          function page_url($base, $q, $p) {
            $params = array_merge($q, ['page' => $p]);
            return $base . '?' . http_build_query($params);
          }
        @endphp

        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, 1) : '#' }}" @if($current <= 1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, $current - 1) : '#' }}" @if($current <= 1) disabled @endif>&lsaquo;</a>

        <span style="padding:0 8px;">Page {{ $current }} of {{ $last }}</span>

        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $current + 1) : '#' }}" @if($current >= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $last) : '#' }}" @if($current >= $last) disabled @endif>&raquo;</a>
      </div>
    </div>
    </div>
  </div>

  <!-- Payment Plan Page View (Full Page) -->
  <div class="client-page-view" id="paymentPlanPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="paymentPlanPageTitle">Payment Plan</span> - <span class="client-name" id="paymentPlanPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editPaymentPlanFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closePaymentPlanPageBtn" onclick="closePaymentPlanPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Payment Plan Details View -->
        <div id="paymentPlanDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="paymentPlanDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Payment Plan Edit/Add Form -->
        <div id="paymentPlanFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="paymentPlanDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deletePaymentPlan()">Delete</button>
                <button type="submit" form="paymentPlanPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closePaymentPlanFormBtn" onclick="closePaymentPlanPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="paymentPlanPageForm" method="POST" action="{{ route('payment-plans.store') }}">
              @csrf
              <div id="paymentPlanPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Payment Plan Modal (hidden, used for form structure) -->
  <div class="modal" id="paymentPlanModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="paymentPlanModalTitle">Add Payment Plan</h4>
        <button type="button" class="modal-close" onclick="closePaymentPlanModal()">×</button>
      </div>
      <form id="paymentPlanForm" method="POST" action="{{ route('payment-plans.store') }}">
        @csrf
        <div id="paymentPlanFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="schedule_id">Schedule *</label>
              <select class="form-control" name="schedule_id" id="schedule_id" required>
                <option value="">Select Schedule</option>
                @foreach($schedules as $schedule)
                  <option value="{{ $schedule->id }}">
                    {{ $schedule->policy->policy_no ?? 'N/A' }} - 
                    {{ $schedule->policy->client->client_name ?? 'N/A' }} - 
                    Schedule #{{ $schedule->schedule_no ?? $schedule->id }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="installment_label">Instalment Label</label>
              <input type="text" class="form-control" name="installment_label" id="installment_label" placeholder="e.g., Instalment 1 of 4">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="due_date">Due Date *</label>
              <input type="date" class="form-control" name="due_date" id="due_date" required>
            </div>
            <div class="form-group">
              <label for="amount">Amount *</label>
              <input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="frequency">Frequency</label>
              <select class="form-control" name="frequency" id="frequency">
                <option value="">Select Frequency</option>
                @php
                  $frequencies = \App\Models\LookupValue::whereHas('lookupCategory', function($q) {
                    $q->where('name', 'Frequency');
                  })->where('active', 1)->orderBy('seq')->get();
                @endphp
                @foreach($frequencies as $freq)
                  <option value="{{ $freq->name }}">{{ $freq->name }}</option>
                @endforeach
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Annually">Annually</option>
              </select>
            </div>
            <div class="form-group">
              <label for="status">Status *</label>
              <select class="form-control" name="status" id="status" required>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closePaymentPlanModal()">Cancel</button>
          <button type="button" class="btn-delete" id="paymentPlanDeleteBtn" style="display: none;" onclick="deletePaymentPlan()">Delete</button>
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
          <button type="button" class="btn-select-all" onclick="selectAllColumns()">Select All</button>
          <button type="button" class="btn-deselect-all" onclick="deselectAllColumns()">Deselect All</button>
        </div>

        <form id="columnForm" action="{{ route('payment-plans.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @php
              $all = $config['column_definitions'];
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

            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryColumns);
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
  let currentPaymentPlanId = null;
  const selectedColumns = @json($selectedColumns);
  const mandatoryColumns = @json($mandatoryColumns);

  // Helper function for date formatting
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
  }

  // Helper function for number formatting
  function formatNumber(num) {
    if (!num && num !== 0) return '-';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Open payment plan details (full page view) - MUST be defined before HTML onclick handlers
  async function openPaymentPlanDetails(id) {
    try {
      const res = await fetch(`/payment-plans/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const plan = await res.json();
      currentPaymentPlanId = id;
      
      // Get all required elements
      const paymentPlanPageName = document.getElementById('paymentPlanPageName');
      const paymentPlanPageTitle = document.getElementById('paymentPlanPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const paymentPlanPageView = document.getElementById('paymentPlanPageView');
      const paymentPlanDetailsPageContent = document.getElementById('paymentPlanDetailsPageContent');
      const paymentPlanFormPageContent = document.getElementById('paymentPlanFormPageContent');
      const editPaymentPlanFromPageBtn = document.getElementById('editPaymentPlanFromPageBtn');
      const closePaymentPlanPageBtn = document.getElementById('closePaymentPlanPageBtn');
      
      if (!paymentPlanPageName || !paymentPlanPageTitle || !clientsTableView || !paymentPlanPageView || 
          !paymentPlanDetailsPageContent || !paymentPlanFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set payment plan name in header
      const planName = plan.installment_label || 'Instalment #' + plan.id;
      paymentPlanPageName.textContent = planName;
      paymentPlanPageTitle.textContent = 'Payment Plan';
      
      populatePaymentPlanDetails(plan);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      paymentPlanPageView.style.display = 'block';
      paymentPlanPageView.classList.add('show');
      paymentPlanDetailsPageContent.style.display = 'block';
      paymentPlanFormPageContent.style.display = 'none';
      if (editPaymentPlanFromPageBtn) editPaymentPlanFromPageBtn.style.display = 'inline-block';
      if (closePaymentPlanPageBtn) closePaymentPlanPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading payment plan details: ' + e.message);
    }
  }

  // Populate payment plan details view
  function populatePaymentPlanDetails(plan) {
    const content = document.getElementById('paymentPlanDetailsContent');
    if (!content) return;

    const schedule = plan.schedule || {};
    const policy = schedule.policy || {};
    const client = policy.client || {};

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">PAYMENT PLAN DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Instalment Label</span>
            <div class="detail-value">${plan.installment_label || 'Instalment #' + plan.id}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy No</span>
            <div class="detail-value">${policy.policy_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Client Name</span>
            <div class="detail-value">${client.client_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Due Date</span>
            <div class="detail-value">${formatDate(plan.due_date)}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">FINANCIAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Amount</span>
            <div class="detail-value">${formatNumber(plan.amount)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Frequency</span>
            <div class="detail-value">${plan.frequency || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <div class="detail-value">${plan.status ? plan.status.charAt(0).toUpperCase() + plan.status.slice(1) : '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
    `;

    const col4 = `
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open payment plan page (Add or Edit)
  async function openPaymentPlanPage(mode) {
    if (mode === 'add') {
      openPaymentPlanForm('add');
    } else {
      if (currentPaymentPlanId) {
        openEditPaymentPlan(currentPaymentPlanId);
      }
    }
  }

  // Add Payment Plan Button
  document.getElementById('addPaymentPlanBtn').addEventListener('click', () => {
    window.location.href = '{{ route("payment-plans.create") }}';
  });
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditPaymentPlan(id) {
    try {
      const res = await fetch(`/payment-plans/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const plan = await res.json();
      currentPaymentPlanId = id;
      openPaymentPlanForm('edit', plan);
    } catch (e) {
      console.error(e);
      alert('Error loading payment plan data');
    }
  }

  function openPaymentPlanForm(mode, plan = null) {
    // Clone form from modal
    const modalForm = document.getElementById('paymentPlanModal').querySelector('form');
    const pageForm = document.getElementById('paymentPlanPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('paymentPlanPageFormMethod');
    const deleteBtn = document.getElementById('paymentPlanDeleteBtn');
    const editBtn = document.getElementById('editPaymentPlanFromPageBtn');
    const closeBtn = document.getElementById('closePaymentPlanPageBtn');
    const closeFormBtn = document.getElementById('closePaymentPlanFormBtn');

    if (mode === 'add') {
      document.getElementById('paymentPlanPageTitle').textContent = 'Add Payment Plan';
      document.getElementById('paymentPlanPageName').textContent = '';
      pageForm.action = '{{ route("payment-plans.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const planName = plan.installment_label || 'Instalment #' + plan.id;
      document.getElementById('paymentPlanPageTitle').textContent = 'Edit Payment Plan';
      document.getElementById('paymentPlanPageName').textContent = planName;
      pageForm.action = `/payment-plans/${currentPaymentPlanId}`;
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

      const fields = ['schedule_id','installment_label','due_date','amount','frequency','status'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = plan[k] ? (typeof plan[k] === 'string' ? plan[k].substring(0,10) : plan[k]) : '';
        } else {
          el.value = plan[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const paymentPlanPageView = document.getElementById('paymentPlanPageView');
    paymentPlanPageView.style.display = 'block';
    paymentPlanPageView.classList.add('show');
    document.getElementById('paymentPlanDetailsPageContent').style.display = 'none';
    document.getElementById('paymentPlanFormPageContent').style.display = 'block';
  }

  function closePaymentPlanPageView() {
    const paymentPlanPageView = document.getElementById('paymentPlanPageView');
    paymentPlanPageView.classList.remove('show');
    paymentPlanPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('paymentPlanDetailsPageContent').style.display = 'none';
    document.getElementById('paymentPlanFormPageContent').style.display = 'none';
    currentPaymentPlanId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editPaymentPlanFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentPaymentPlanId) {
        openEditPaymentPlan(currentPaymentPlanId);
      }
    });
  }

  // Column modal functions
  function openColumnModal() {
    document.getElementById('tableResponsive').classList.add('no-scroll');
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      // Always check mandatory fields, otherwise check if in selectedColumns
      cb.checked = mandatoryColumns.includes(cb.value) || selectedColumns.includes(cb.value);
    });
    document.body.style.overflow = 'hidden';
    document.getElementById('columnModal').classList.add('show');
    // Initialize drag and drop after modal is shown
    setTimeout(initDragAndDrop, 100);
  }

  function closeColumnModal() {
    document.getElementById('tableResponsive').classList.remove('no-scroll');
    document.getElementById('columnModal').classList.remove('show');
    document.body.style.overflow = '';
  }

  function selectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      cb.checked = true;
    });
  }

  function deselectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      // Don't uncheck mandatory fields
      if (!mandatoryColumns.includes(cb.value)) {
        cb.checked = false;
      }
    });
  }

  function saveColumnSettings() {
    // Mandatory fields that should always be included
    const mandatoryFields = mandatoryColumns;

    // Get order from DOM - this preserves the drag and drop order
    const items = Array.from(document.querySelectorAll('#columnSelection .column-item'));
    const order = items.map(item => item.dataset.column);
    const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n => n.value);

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
    existing.forEach(e => e.remove());

    // Add columns in the order they appear in the DOM (after drag and drop)
    orderedChecked.forEach(c => {
      const i = document.createElement('input');
      i.type = 'hidden';
      i.name = 'columns[]';
      i.value = c;
      form.appendChild(i);
    });

    form.submit();
  }

  function deletePaymentPlan() {
    if (!currentPaymentPlanId) return;
    if (!confirm('Delete this payment plan?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/payment-plans/${currentPaymentPlanId}`;
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }

  // Legacy function for backward compatibility
  function openPaymentPlanModal(mode, plan = null) {
    if (mode === 'add') {
      openPaymentPlanPage('add');
    } else if (plan && currentPaymentPlanId) {
      openEditPaymentPlan(currentPaymentPlanId);
    }
  }

  function closePaymentPlanModal() {
    closePaymentPlanPageView();
  }

  let draggedElement = null;
  let dragOverElement = null;

  // Initialize drag and drop when column modal opens
  let dragInitialized = false;

  function initDragAndDrop() {
    const columnSelection = document.getElementById('columnSelection');
    if (!columnSelection) return;

    // Only initialize once to avoid duplicate event listeners
    if (dragInitialized) {
      // Re-enable draggable on all items
      const columnItems = columnSelection.querySelectorAll('.column-item');
      columnItems.forEach(item => {
        item.setAttribute('draggable', 'true');
      });
      return;
    }

    // Make all column items draggable
    const columnItems = columnSelection.querySelectorAll('.column-item');

    columnItems.forEach(item => {
      // Ensure draggable attribute is set
      item.setAttribute('draggable', 'true');
      item.style.cursor = 'move';

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
        setTimeout(() => {
          if (document.body.contains(dragImage)) {
            document.body.removeChild(dragImage);
          }
        }, 0);
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
          if (dragOverElement && dragOverElement !== this) {
            dragOverElement.classList.remove('drag-over');
          }

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

    dragInitialized = true;
  }
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

@endsection
