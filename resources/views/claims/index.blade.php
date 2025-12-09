@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('claims');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('claims');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Claims Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Claims Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Claims</h3>
        <div class="records-found">Records Found - {{ $claims->total() }}</div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addClaimBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="claimsTable">
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
          @foreach($claims as $clm)
            <tr>
              <td class="action-cell">
                <svg class="action-expand" onclick="openClaimDetails({{ $clm->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'claim_id')
                  <td data-column="claim_id">
                    <a href="javascript:void(0)" onclick="openClaimDetails({{ $clm->id }})" style="color:#007bff; text-decoration:underline;">{{ $clm->claim_id }}</a>
                  </td>
                @elseif($col == 'policy_no')
                  <td data-column="policy_no">{{ $clm->policy_no ?? '-' }}</td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $clm->client_name ?? '-' }}</td>
                @elseif($col == 'loss_date')
                  <td data-column="loss_date">{{ $clm->loss_date ? \Carbon\Carbon::parse($clm->loss_date)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'claim_date')
                  <td data-column="claim_date">{{ $clm->claim_date ? \Carbon\Carbon::parse($clm->claim_date)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'claim_amount')
                  <td data-column="claim_amount">{{ $clm->claim_amount ? number_format($clm->claim_amount, 2) : '-' }}</td>
                @elseif($col == 'claim_summary')
                  <td data-column="claim_summary">{{ $clm->claim_summary ?? '-' }}</td>
                @elseif($col == 'status')
                  <td data-column="status">{{ $clm->status ?? '-' }}</td>
                @elseif($col == 'close_date')
                  <td data-column="close_date">{{ $clm->close_date ? \Carbon\Carbon::parse($clm->close_date)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'paid_amount')
                  <td data-column="paid_amount">{{ $clm->paid_amount ? number_format($clm->paid_amount, 2) : '-' }}</td>
                @elseif($col == 'settlment_notes')
                  <td data-column="settlment_notes">{{ $clm->settlment_notes ?? '-' }}</td>
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
        <a class="btn btn-export" href="{{ route('claims.export', array_merge(request()->query(), ['page' => $claims->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $claims->currentPage();
          $last = max(1, $claims->lastPage());
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

  <!-- Claim Page View (Full Page) -->
  <div class="client-page-view" id="claimPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="claimPageTitle">Claim</span> - <span class="client-name" id="claimPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editClaimFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeClaimPageBtn" onclick="closeClaimPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Claim Details View -->
        <div id="claimDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="claimDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Claim Edit/Add Form -->
        <div id="claimFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="claimDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteClaim()">Delete</button>
                <button type="submit" form="claimPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeClaimFormBtn" onclick="closeClaimPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="claimPageForm" method="POST" action="{{ route('claims.store') }}">
              @csrf
              <div id="claimPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Claim Modal (hidden, used for form structure) -->
  <div class="modal" id="claimModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="claimModalTitle">Add Claim</h4>
        <button type="button" class="modal-close" onclick="closeClaimModal()">×</button>
      </div>
      <form id="claimForm" method="POST" action="{{ route('claims.store') }}">
        @csrf
        <div id="claimFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="policy_no">Policy No</label>
              <input type="text" class="form-control" name="policy_no" id="policy_no">
            </div>
            <div class="form-group">
              <label for="client_name">Client Name</label>
              <input type="text" class="form-control" name="client_name" id="client_name">
            </div>
            <div class="form-group">
              <label for="loss_date">Loss Date</label>
              <input type="date" class="form-control" name="loss_date" id="loss_date">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="claim_date">Claim Date</label>
              <input type="date" class="form-control" name="claim_date" id="claim_date">
            </div>
            <div class="form-group">
              <label for="claim_amount">Claim Amount</label>
              <input type="number" step="0.01" class="form-control" name="claim_amount" id="claim_amount">
            </div>
            <div class="form-group">
              <label for="claim_summary">Claim Summary</label>
              <input type="text" class="form-control" name="claim_summary" id="claim_summary">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="status">Status</label>
              <input type="text" class="form-control" name="status" id="status">
            </div>
            <div class="form-group">
              <label for="close_date">Close Date</label>
              <input type="date" class="form-control" name="close_date" id="close_date">
            </div>
            <div class="form-group">
              <label for="paid_amount">Paid Amount</label>
              <input type="number" step="0.01" class="form-control" name="paid_amount" id="paid_amount">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="settlment_notes">Settlment Notes</label>
              <textarea class="form-control" name="settlment_notes" id="settlment_notes" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeClaimModal()">Cancel</button>
          <button type="button" class="btn-delete" id="claimDeleteBtn" style="display: none;" onclick="deleteClaim()">Delete</button>
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

        <form id="columnForm" action="{{ route('claims.save-column-settings') }}" method="POST">
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
  let currentClaimId = null;
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

  // Open claim details (full page view) - MUST be defined before HTML onclick handlers
  async function openClaimDetails(id) {
    try {
      const res = await fetch(`/claims/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const claim = await res.json();
      currentClaimId = id;
      
      // Get all required elements
      const claimPageName = document.getElementById('claimPageName');
      const claimPageTitle = document.getElementById('claimPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const claimPageView = document.getElementById('claimPageView');
      const claimDetailsPageContent = document.getElementById('claimDetailsPageContent');
      const claimFormPageContent = document.getElementById('claimFormPageContent');
      const editClaimFromPageBtn = document.getElementById('editClaimFromPageBtn');
      const closeClaimPageBtn = document.getElementById('closeClaimPageBtn');
      
      if (!claimPageName || !claimPageTitle || !clientsTableView || !claimPageView || 
          !claimDetailsPageContent || !claimFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set claim name in header
      const claimName = claim.claim_id || 'Unknown';
      claimPageName.textContent = claimName;
      claimPageTitle.textContent = 'Claim';
      
      populateClaimDetails(claim);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      claimPageView.style.display = 'block';
      claimPageView.classList.add('show');
      claimDetailsPageContent.style.display = 'block';
      claimFormPageContent.style.display = 'none';
      if (editClaimFromPageBtn) editClaimFromPageBtn.style.display = 'inline-block';
      if (closeClaimPageBtn) closeClaimPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading claim details: ' + e.message);
    }
  }

  // Populate claim details view
  function populateClaimDetails(claim) {
    const content = document.getElementById('claimDetailsContent');
    if (!content) return;

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">CLAIM DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Claim ID</span>
            <div class="detail-value">${claim.claim_id || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy No</span>
            <div class="detail-value">${claim.policy_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Client Name</span>
            <div class="detail-value">${claim.client_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Loss Date</span>
            <div class="detail-value">${formatDate(claim.loss_date)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Claim Date</span>
            <div class="detail-value">${formatDate(claim.claim_date)}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">FINANCIAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Claim Amount</span>
            <div class="detail-value">${formatNumber(claim.claim_amount)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Paid Amount</span>
            <div class="detail-value">${formatNumber(claim.paid_amount)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <div class="detail-value">${claim.status || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Close Date</span>
            <div class="detail-value">${formatDate(claim.close_date)}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">ADDITIONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Claim Summary</span>
            <div class="detail-value">${claim.claim_summary || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header">NOTES</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Settlment Notes</span>
            <textarea class="detail-value" style="min-height:120px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${claim.settlment_notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open claim page (Add or Edit)
  async function openClaimPage(mode) {
    if (mode === 'add') {
      openClaimForm('add');
    } else {
      if (currentClaimId) {
        openEditClaim(currentClaimId);
      }
    }
  }

  // Add Claim Button
  document.getElementById('addClaimBtn').addEventListener('click', () => openClaimPage('add'));
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditClaim(id) {
    try {
      const res = await fetch(`/claims/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const claim = await res.json();
      currentClaimId = id;
      openClaimForm('edit', claim);
    } catch (e) {
      console.error(e);
      alert('Error loading claim data');
    }
  }

  function openClaimForm(mode, claim = null) {
    // Clone form from modal
    const modalForm = document.getElementById('claimModal').querySelector('form');
    const pageForm = document.getElementById('claimPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('claimPageFormMethod');
    const deleteBtn = document.getElementById('claimDeleteBtn');
    const editBtn = document.getElementById('editClaimFromPageBtn');
    const closeBtn = document.getElementById('closeClaimPageBtn');
    const closeFormBtn = document.getElementById('closeClaimFormBtn');

    if (mode === 'add') {
      document.getElementById('claimPageTitle').textContent = 'Add Claim';
      document.getElementById('claimPageName').textContent = '';
      pageForm.action = '{{ route("claims.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const claimName = claim.claim_id || 'Unknown';
      document.getElementById('claimPageTitle').textContent = 'Edit Claim';
      document.getElementById('claimPageName').textContent = claimName;
      pageForm.action = `/claims/${currentClaimId}`;
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

      const fields = ['policy_no','client_name','loss_date','claim_date','claim_amount','claim_summary','status','close_date','paid_amount','settlment_notes'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = claim[k] ? (typeof claim[k] === 'string' ? claim[k].substring(0,10) : claim[k]) : '';
        } else if (el.tagName === 'TEXTAREA') {
          el.value = claim[k] ?? '';
        } else {
          el.value = claim[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const claimPageView = document.getElementById('claimPageView');
    claimPageView.style.display = 'block';
    claimPageView.classList.add('show');
    document.getElementById('claimDetailsPageContent').style.display = 'none';
    document.getElementById('claimFormPageContent').style.display = 'block';
  }

  function closeClaimPageView() {
    const claimPageView = document.getElementById('claimPageView');
    claimPageView.classList.remove('show');
    claimPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('claimDetailsPageContent').style.display = 'none';
    document.getElementById('claimFormPageContent').style.display = 'none';
    currentClaimId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editClaimFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentClaimId) {
        openEditClaim(currentClaimId);
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

  function deleteClaim() {
    if (!currentClaimId) return;
    if (!confirm('Delete this claim?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/claims/${currentClaimId}`;
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
  function openClaimModal(mode, claim = null) {
    if (mode === 'add') {
      openClaimPage('add');
    } else if (claim && currentClaimId) {
      openEditClaim(currentClaimId);
    }
  }

  function closeClaimModal() {
    closeClaimPageView();
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
