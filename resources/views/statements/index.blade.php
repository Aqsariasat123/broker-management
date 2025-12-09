@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('statements');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('statements');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Statements Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Statements Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Statements</h3>
        <div class="records-found">Records Found - {{ $statements->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            @foreach(['SACOS','Alliance','Hsavy','MUA'] as $insurerBtn)
              <button class="btn btn-column" onclick="filterByInsurer('{{ $insurerBtn }}')" style="margin-left:5px;{{ isset($insurerFilter) && $insurerFilter==$insurerBtn ? 'background:#007bff;color:#fff;' : '' }}">{{ $insurerBtn }}</button>
            @endforeach
            <button class="btn btn-back" onclick="window.location.href='{{ route('statements.index') }}'">All</button>
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addStatementBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="statementsTable">
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
          @foreach($statements as $st)
            <tr>
              <td class="action-cell">
                <svg class="action-expand" onclick="openStatementDetails({{ $st->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'statement_no')
                  <td data-column="statement_no">
                    <a href="javascript:void(0)" onclick="openStatementDetails({{ $st->id }})" style="color:#007bff; text-decoration:underline;">{{ $st->statement_no }}</a>
                  </td>
                @elseif($col == 'year')
                  <td data-column="year">{{ $st->year ?? '-' }}</td>
                @elseif($col == 'insurer')
                  <td data-column="insurer">{{ $st->insurer ? $st->insurer->name : '-' }}</td>
                @elseif($col == 'business_category')
                  <td data-column="business_category">{{ $st->business_category ?? '-' }}</td>
                @elseif($col == 'date_received')
                  <td data-column="date_received">{{ $st->date_received ? $st->date_received->format('d-M-y') : '-' }}</td>
                @elseif($col == 'amount_received')
                  <td data-column="amount_received">{{ $st->amount_received ? number_format($st->amount_received, 2) : '-' }}</td>
                @elseif($col == 'mode_of_payment')
                  <td data-column="mode_of_payment">{{ $st->modeOfPayment ? $st->modeOfPayment->name : '-' }}</td>
                @elseif($col == 'remarks')
                  <td data-column="remarks">{{ $st->remarks ?? '-' }}</td>
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
        <a class="btn btn-export" href="{{ route('statements.export', array_merge(request()->query(), ['page' => $statements->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $statements->currentPage();
          $last = max(1, $statements->lastPage());
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

  <!-- Statement Page View (Full Page) -->
  <div class="client-page-view" id="statementPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="statementPageTitle">Statement</span> - <span class="client-name" id="statementPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editStatementFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeStatementPageBtn" onclick="closeStatementPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Statement Details View -->
        <div id="statementDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="statementDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Statement Edit/Add Form -->
        <div id="statementFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="statementDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteStatement()">Delete</button>
                <button type="submit" form="statementPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeStatementFormBtn" onclick="closeStatementPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="statementPageForm" method="POST" action="{{ route('statements.store') }}">
              @csrf
              <div id="statementPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Statement Modal (hidden, used for form structure) -->
  <div class="modal" id="statementModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="statementModalTitle">Add Statement</h4>
        <button type="button" class="modal-close" onclick="closeStatementModal()">×</button>
      </div>
      <form id="statementForm" method="POST" action="{{ route('statements.store') }}">
        @csrf
        <div id="statementFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="year">Year</label>
              <input type="text" class="form-control" name="year" id="year">
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
            <div class="form-group">
              <label for="business_category">Business Category</label>
              <input type="text" class="form-control" name="business_category" id="business_category">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="date_received">Date Received</label>
              <input type="date" class="form-control" name="date_received" id="date_received">
            </div>
            <div class="form-group">
              <label for="amount_received">Amount Received</label>
              <input type="number" step="0.01" class="form-control" name="amount_received" id="amount_received">
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
            <div class="form-group" style="flex:1 1 100%;">
              <label for="remarks">Remarks</label>
              <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeStatementModal()">Cancel</button>
          <button type="button" class="btn-delete" id="statementDeleteBtn" style="display: none;" onclick="deleteStatement()">Delete</button>
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

        <form id="columnForm" action="{{ route('statements.save-column-settings') }}" method="POST">
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
  let currentStatementId = null;
  const lookupData = {
    insurers: @json($insurers),
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

  // Open statement details (full page view) - MUST be defined before HTML onclick handlers
  async function openStatementDetails(id) {
    try {
      const res = await fetch(`/statements/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const statement = await res.json();
      currentStatementId = id;
      
      // Get all required elements
      const statementPageName = document.getElementById('statementPageName');
      const statementPageTitle = document.getElementById('statementPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const statementPageView = document.getElementById('statementPageView');
      const statementDetailsPageContent = document.getElementById('statementDetailsPageContent');
      const statementFormPageContent = document.getElementById('statementFormPageContent');
      const editStatementFromPageBtn = document.getElementById('editStatementFromPageBtn');
      const closeStatementPageBtn = document.getElementById('closeStatementPageBtn');
      
      if (!statementPageName || !statementPageTitle || !clientsTableView || !statementPageView || 
          !statementDetailsPageContent || !statementFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set statement name in header
      const statementName = statement.statement_no || 'Unknown';
      statementPageName.textContent = statementName;
      statementPageTitle.textContent = 'Statement';
      
      populateStatementDetails(statement);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      statementPageView.style.display = 'block';
      statementPageView.classList.add('show');
      statementDetailsPageContent.style.display = 'block';
      statementFormPageContent.style.display = 'none';
      if (editStatementFromPageBtn) editStatementFromPageBtn.style.display = 'inline-block';
      if (closeStatementPageBtn) closeStatementPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading statement details: ' + e.message);
    }
  }

  // Populate statement details view
  function populateStatementDetails(statement) {
    const content = document.getElementById('statementDetailsContent');
    if (!content) return;

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">STATEMENT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Statement No</span>
            <div class="detail-value">${statement.statement_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Year</span>
            <div class="detail-value">${statement.year || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurer</span>
            <div class="detail-value">${statement.insurer ? statement.insurer.name : '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Business Category</span>
            <div class="detail-value">${statement.business_category || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">PAYMENT INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Date Received</span>
            <div class="detail-value">${formatDate(statement.date_received)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Amount Received</span>
            <div class="detail-value">${formatNumber(statement.amount_received)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Mode Of Payment</span>
            <div class="detail-value">${statement.mode_of_payment ? statement.mode_of_payment.name : (statement.modeOfPayment ? statement.modeOfPayment.name : '-')}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">ADDITIONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Remarks</span>
            <textarea class="detail-value" style="min-height:60px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${statement.remarks || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    const col4 = `
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open statement page (Add or Edit)
  async function openStatementPage(mode) {
    if (mode === 'add') {
      openStatementForm('add');
    } else {
      if (currentStatementId) {
        openEditStatement(currentStatementId);
      }
    }
  }

  // Add Statement Button
  document.getElementById('addStatementBtn').addEventListener('click', () => openStatementPage('add'));
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditStatement(id) {
    try {
      const res = await fetch(`/statements/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const statement = await res.json();
      currentStatementId = id;
      openStatementForm('edit', statement);
    } catch (e) {
      console.error(e);
      alert('Error loading statement data');
    }
  }

  function openStatementForm(mode, statement = null) {
    // Clone form from modal
    const modalForm = document.getElementById('statementModal').querySelector('form');
    const pageForm = document.getElementById('statementPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('statementPageFormMethod');
    const deleteBtn = document.getElementById('statementDeleteBtn');
    const editBtn = document.getElementById('editStatementFromPageBtn');
    const closeBtn = document.getElementById('closeStatementPageBtn');
    const closeFormBtn = document.getElementById('closeStatementFormBtn');

    if (mode === 'add') {
      document.getElementById('statementPageTitle').textContent = 'Add Statement';
      document.getElementById('statementPageName').textContent = '';
      pageForm.action = '{{ route("statements.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const statementName = statement.statement_no || 'Unknown';
      document.getElementById('statementPageTitle').textContent = 'Edit Statement';
      document.getElementById('statementPageName').textContent = statementName;
      pageForm.action = `/statements/${currentStatementId}`;
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

      const fields = ['year','insurer_id','business_category','date_received','amount_received','mode_of_payment_id','remarks'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = statement[k] ? (typeof statement[k] === 'string' ? statement[k].substring(0,10) : statement[k]) : '';
        } else if (el.tagName === 'TEXTAREA') {
          el.value = statement[k] ?? '';
        } else {
          el.value = statement[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const statementPageView = document.getElementById('statementPageView');
    statementPageView.style.display = 'block';
    statementPageView.classList.add('show');
    document.getElementById('statementDetailsPageContent').style.display = 'none';
    document.getElementById('statementFormPageContent').style.display = 'block';
  }

  function closeStatementPageView() {
    const statementPageView = document.getElementById('statementPageView');
    statementPageView.classList.remove('show');
    statementPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('statementDetailsPageContent').style.display = 'none';
    document.getElementById('statementFormPageContent').style.display = 'none';
    currentStatementId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editStatementFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentStatementId) {
        openEditStatement(currentStatementId);
      }
    });
  }

  function filterByInsurer(insurer) {
    window.location.href = `{{ route('statements.index') }}?insurer=${insurer}`;
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

  function deleteStatement() {
    if (!currentStatementId) return;
    if (!confirm('Delete this statement?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/statements/${currentStatementId}`;
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
  function openStatementModal(mode, statement = null) {
    if (mode === 'add') {
      openStatementPage('add');
    } else if (statement && currentStatementId) {
      openEditStatement(currentStatementId);
    }
  }

  function closeStatementModal() {
    closeStatementPageView();
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
