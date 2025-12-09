@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('commissions');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('commissions');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Commissions Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Commissions Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Commissions</h3>
        <div class="records-found">Records Found - {{ $commissions->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            @foreach(['SACOS','Alliance','Hsavy','MUA'] as $insurerBtn)
              <button class="btn btn-column" onclick="filterByInsurer('{{ $insurerBtn }}')" style="margin-left:5px;{{ isset($insurerFilter) && $insurerFilter==$insurerBtn ? 'background:#007bff;color:#fff;' : '' }}">{{ $insurerBtn }}</button>
            @endforeach
            <button class="btn btn-back" onclick="window.location.href='{{ route('commissions.index') }}'">All</button>
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addCommissionBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="commissionsTable">
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
          @foreach($commissions as $com)
            <tr>
              <td class="action-cell">
                <svg class="action-expand" onclick="openCommissionDetails({{ $com->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'policy_number')
                  <td data-column="policy_number">
                    <a href="javascript:void(0)" onclick="openCommissionDetails({{ $com->id }})" style="color:#007bff; text-decoration:underline;">{{ $com->policy_number }}</a>
                  </td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $com->client_name }}</td>
                @elseif($col == 'insurer')
                  <td data-column="insurer">{{ $com->insurer ? $com->insurer->name : '-' }}</td>
                @elseif($col == 'grouping')
                  <td data-column="grouping">{{ $com->grouping ?? '-' }}</td>
                @elseif($col == 'basic_premium')
                  <td data-column="basic_premium">{{ $com->basic_premium ? number_format($com->basic_premium, 2) : '-' }}</td>
                @elseif($col == 'rate')
                  <td data-column="rate">{{ $com->rate ? number_format($com->rate, 2) : '-' }}</td>
                @elseif($col == 'amount_due')
                  <td data-column="amount_due">{{ $com->amount_due ? number_format($com->amount_due, 2) : '-' }}</td>
                @elseif($col == 'payment_status')
                  <td data-column="payment_status">{{ $com->paymentStatus ? $com->paymentStatus->name : '-' }}</td>
                @elseif($col == 'amount_rcvd')
                  <td data-column="amount_rcvd">{{ $com->amount_rcvd ? number_format($com->amount_rcvd, 2) : '-' }}</td>
                @elseif($col == 'date_rcvd')
                  <td data-column="date_rcvd">{{ $com->date_rcvd ? $com->date_rcvd->format('d-M-y') : '-' }}</td>
                @elseif($col == 'state_no')
                  <td data-column="state_no">{{ $com->state_no ?? '-' }}</td>
                @elseif($col == 'mode_of_payment')
                  <td data-column="mode_of_payment">{{ $com->modeOfPayment ? $com->modeOfPayment->name : '-' }}</td>
                @elseif($col == 'variance')
                  <td data-column="variance">{{ $com->variance ? number_format($com->variance, 2) : '-' }}</td>
                @elseif($col == 'reason')
                  <td data-column="reason">{{ $com->reason ?? '-' }}</td>
                @elseif($col == 'date_due')
                  <td data-column="date_due">{{ $com->date_due ? $com->date_due->format('d-M-y') : '-' }}</td>
                @elseif($col == 'cnid')
                  <td data-column="cnid">
                    <a href="javascript:void(0)" onclick="openCommissionDetails({{ $com->id }})" style="color:#007bff; text-decoration:underline;">{{ $com->cnid }}</a>
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
        <a class="btn btn-export" href="{{ route('commissions.export', array_merge(request()->query(), ['page' => $commissions->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $commissions->currentPage();
          $last = max(1, $commissions->lastPage());
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

  <!-- Commission Page View (Full Page) -->
  <div class="client-page-view" id="commissionPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="commissionPageTitle">Commission</span> - <span class="client-name" id="commissionPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editCommissionFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeCommissionPageBtn" onclick="closeCommissionPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Commission Details View -->
        <div id="commissionDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="commissionDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Commission Edit/Add Form -->
        <div id="commissionFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="commissionDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteCommission()">Delete</button>
                <button type="submit" form="commissionPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeCommissionFormBtn" onclick="closeCommissionPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="commissionPageForm" method="POST" action="{{ route('commissions.store') }}">
              @csrf
              <div id="commissionPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Commission Modal (hidden, used for form structure) -->
  <div class="modal" id="commissionModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="commissionModalTitle">Add Commission</h4>
        <button type="button" class="modal-close" onclick="closeCommissionModal()">×</button>
      </div>
      <form id="commissionForm" method="POST" action="{{ route('commissions.store') }}">
        @csrf
        <div id="commissionFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="policy_number">Policy Number</label>
              <input type="text" class="form-control" name="policy_number" id="policy_number">
            </div>
            <div class="form-group">
              <label for="client_name">Client's Name</label>
              <input type="text" class="form-control" name="client_name" id="client_name">
            </div>
            <div class="form-group">
              <label for="insurer_id">Insurer</label>
              <select class="form-control" name="insurer_id" id="insurer_id">
                <option value="">Select</option>
                @foreach($insurers as $ins)
                  <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="grouping">Grouping</label>
              <input type="text" class="form-control" name="grouping" id="grouping">
            </div>
            <div class="form-group">
              <label for="basic_premium">Basic Premium</label>
              <input type="number" step="0.01" class="form-control" name="basic_premium" id="basic_premium">
            </div>
            <div class="form-group">
              <label for="rate">Rate</label>
              <input type="number" step="0.01" class="form-control" name="rate" id="rate">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="amount_due">Amount Due</label>
              <input type="number" step="0.01" class="form-control" name="amount_due" id="amount_due">
            </div>
            <div class="form-group">
              <label for="payment_status_id">Payment Status</label>
              <select class="form-control" name="payment_status_id" id="payment_status_id">
                <option value="">Select</option>
                @foreach($paymentStatuses as $ps)
                  <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="amount_rcvd">Amount Rcvd</label>
              <input type="number" step="0.01" class="form-control" name="amount_rcvd" id="amount_rcvd">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="date_rcvd">Date Rcvd</label>
              <input type="date" class="form-control" name="date_rcvd" id="date_rcvd">
            </div>
            <div class="form-group">
              <label for="state_no">State No</label>
              <input type="text" class="form-control" name="state_no" id="state_no">
            </div>
            <div class="form-group">
              <label for="mode_of_payment_id">Mode Of Payment (Life)</label>
              <select class="form-control" name="mode_of_payment_id" id="mode_of_payment_id">
                <option value="">Select</option>
                @foreach($modesOfPayment as $mode)
                  <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="variance">Variance</label>
              <input type="number" step="0.01" class="form-control" name="variance" id="variance">
            </div>
            <div class="form-group">
              <label for="reason">Reason</label>
              <input type="text" class="form-control" name="reason" id="reason">
            </div>
            <div class="form-group">
              <label for="date_due">Date Due</label>
              <input type="date" class="form-control" name="date_due" id="date_due">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeCommissionModal()">Cancel</button>
          <button type="button" class="btn-delete" id="commissionDeleteBtn" style="display: none;" onclick="deleteCommission()">Delete</button>
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

        <form id="columnForm" action="{{ route('commissions.save-column-settings') }}" method="POST">
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
  let currentCommissionId = null;
  const lookupData = {
    insurers: @json($insurers),
    paymentStatuses: @json($paymentStatuses),
    modesOfPayment: @json($modesOfPayment)
  };
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

  // Open commission details (full page view) - MUST be defined before HTML onclick handlers
  async function openCommissionDetails(id) {
    try {
      const res = await fetch(`/commissions/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const commission = await res.json();
      currentCommissionId = id;
      
      // Get all required elements
      const commissionPageName = document.getElementById('commissionPageName');
      const commissionPageTitle = document.getElementById('commissionPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const commissionPageView = document.getElementById('commissionPageView');
      const commissionDetailsPageContent = document.getElementById('commissionDetailsPageContent');
      const commissionFormPageContent = document.getElementById('commissionFormPageContent');
      const editCommissionFromPageBtn = document.getElementById('editCommissionFromPageBtn');
      const closeCommissionPageBtn = document.getElementById('closeCommissionPageBtn');
      
      if (!commissionPageName || !commissionPageTitle || !clientsTableView || !commissionPageView || 
          !commissionDetailsPageContent || !commissionFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set commission name in header
      const commissionName = commission.policy_number || commission.cnid || 'Unknown';
      commissionPageName.textContent = commissionName;
      commissionPageTitle.textContent = 'Commission';
      
      populateCommissionDetails(commission);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      commissionPageView.style.display = 'block';
      commissionPageView.classList.add('show');
      commissionDetailsPageContent.style.display = 'block';
      commissionFormPageContent.style.display = 'none';
      if (editCommissionFromPageBtn) editCommissionFromPageBtn.style.display = 'inline-block';
      if (closeCommissionPageBtn) closeCommissionPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading commission details: ' + e.message);
    }
  }

  // Populate commission details view
  function populateCommissionDetails(commission) {
    const content = document.getElementById('commissionDetailsContent');
    if (!content) return;

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">COMMISSION DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">CNID</span>
            <div class="detail-value">${commission.cnid || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Number</span>
            <div class="detail-value">${commission.policy_number || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Client's Name</span>
            <div class="detail-value">${commission.client_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurer</span>
            <div class="detail-value">${commission.insurer ? commission.insurer.name : '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Grouping</span>
            <div class="detail-value">${commission.grouping || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">AMOUNTS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Basic Premium</span>
            <div class="detail-value">${formatNumber(commission.basic_premium)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Rate</span>
            <div class="detail-value">${formatNumber(commission.rate)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Amount Due</span>
            <div class="detail-value">${formatNumber(commission.amount_due)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Amount Rcvd</span>
            <div class="detail-value">${formatNumber(commission.amount_rcvd)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Variance</span>
            <div class="detail-value">${formatNumber(commission.variance)}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">PAYMENT INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Payment Status</span>
            <div class="detail-value">${commission.payment_status ? commission.payment_status.name : (commission.paymentStatus ? commission.paymentStatus.name : '-')}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Rcvd</span>
            <div class="detail-value">${formatDate(commission.date_rcvd)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Due</span>
            <div class="detail-value">${formatDate(commission.date_due)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Mode Of Payment</span>
            <div class="detail-value">${commission.mode_of_payment ? commission.mode_of_payment.name : (commission.modeOfPayment ? commission.modeOfPayment.name : '-')}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">State No</span>
            <div class="detail-value">${commission.state_no || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header">ADDITIONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Reason</span>
            <textarea class="detail-value" style="min-height:40px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${commission.reason || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open commission page (Add or Edit)
  async function openCommissionPage(mode) {
    if (mode === 'add') {
      openCommissionForm('add');
    } else {
      if (currentCommissionId) {
        openEditCommission(currentCommissionId);
      }
    }
  }

  // Add Commission Button
  document.getElementById('addCommissionBtn').addEventListener('click', () => openCommissionPage('add'));
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditCommission(id) {
    try {
      const res = await fetch(`/commissions/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const commission = await res.json();
      currentCommissionId = id;
      openCommissionForm('edit', commission);
    } catch (e) {
      console.error(e);
      alert('Error loading commission data');
    }
  }

  function openCommissionForm(mode, commission = null) {
    // Clone form from modal
    const modalForm = document.getElementById('commissionModal').querySelector('form');
    const pageForm = document.getElementById('commissionPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('commissionPageFormMethod');
    const deleteBtn = document.getElementById('commissionDeleteBtn');
    const editBtn = document.getElementById('editCommissionFromPageBtn');
    const closeBtn = document.getElementById('closeCommissionPageBtn');
    const closeFormBtn = document.getElementById('closeCommissionFormBtn');

    if (mode === 'add') {
      document.getElementById('commissionPageTitle').textContent = 'Add Commission';
      document.getElementById('commissionPageName').textContent = '';
      pageForm.action = '{{ route("commissions.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const commissionName = commission.policy_number || commission.cnid || 'Unknown';
      document.getElementById('commissionPageTitle').textContent = 'Edit Commission';
      document.getElementById('commissionPageName').textContent = commissionName;
      pageForm.action = `/commissions/${currentCommissionId}`;
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

      const fields = ['policy_number','client_name','insurer_id','grouping','basic_premium','rate','amount_due','payment_status_id','amount_rcvd','date_rcvd','state_no','mode_of_payment_id','variance','reason','date_due'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = commission[k] ? (typeof commission[k] === 'string' ? commission[k].substring(0,10) : commission[k]) : '';
        } else {
          el.value = commission[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const commissionPageView = document.getElementById('commissionPageView');
    commissionPageView.style.display = 'block';
    commissionPageView.classList.add('show');
    document.getElementById('commissionDetailsPageContent').style.display = 'none';
    document.getElementById('commissionFormPageContent').style.display = 'block';
  }

  function closeCommissionPageView() {
    const commissionPageView = document.getElementById('commissionPageView');
    commissionPageView.classList.remove('show');
    commissionPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('commissionDetailsPageContent').style.display = 'none';
    document.getElementById('commissionFormPageContent').style.display = 'none';
    currentCommissionId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editCommissionFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentCommissionId) {
        openEditCommission(currentCommissionId);
      }
    });
  }

  function filterByInsurer(insurer) {
    window.location.href = `{{ route('commissions.index') }}?insurer=${insurer}`;
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

  function deleteCommission() {
    if (!currentCommissionId) return;
    if (!confirm('Delete this commission?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/commissions/${currentCommissionId}`;
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
  function openCommissionModal(mode, commission = null) {
    if (mode === 'add') {
      openCommissionPage('add');
    } else if (commission && currentCommissionId) {
      openEditCommission(currentCommissionId);
    }
  }

  function closeCommissionModal() {
    closeCommissionPageView();
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
