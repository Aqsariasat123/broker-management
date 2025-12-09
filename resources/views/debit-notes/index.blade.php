@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('debit-notes');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('debit-notes');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Debit Notes Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Debit Notes Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Debit Notes</h3>
        <div class="records-found">Records Found - {{ $debitNotes->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            <form method="GET" action="{{ route('debit-notes.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
              <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
              <select name="status" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
              <button type="submit" class="btn btn-column" style="background:#fff; color:#000; border:1px solid #ccc;">Filter</button>
              @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('debit-notes.index') }}" class="btn btn-back" style="background:#ccc; color:#333; border-color:#ccc;">Clear</a>
              @endif
            </form>
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addDebitNoteBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="debitNotesTable">
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
          @foreach($debitNotes as $note)
            <tr>
              <td class="action-cell">
                <svg class="action-expand" onclick="openDebitNoteDetails({{ $note->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'debit_note_no')
                  <td data-column="debit_note_no">
                    <a href="javascript:void(0)" onclick="openDebitNoteDetails({{ $note->id }})" style="color:#007bff; text-decoration:underline;">{{ $note->debit_note_no }}</a>
                  </td>
                @elseif($col == 'policy_no')
                  <td data-column="policy_no">{{ $note->paymentPlan->schedule->policy->policy_no ?? '-' }}</td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $note->paymentPlan->schedule->policy->client->client_name ?? '-' }}</td>
                @elseif($col == 'issued_on')
                  <td data-column="issued_on">{{ $note->issued_on ? $note->issued_on->format('d-M-y') : '-' }}</td>
                @elseif($col == 'amount')
                  <td data-column="amount">{{ $note->amount ? number_format($note->amount, 2) : '-' }}</td>
                @elseif($col == 'status')
                  <td data-column="status">
                    <span class="badge-status badge-{{ $note->status }}" style="font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; background:{{ $note->status == 'pending' ? '#ffc107' : ($note->status == 'issued' ? '#17a2b8' : ($note->status == 'paid' ? '#28a745' : ($note->status == 'overdue' ? '#dc3545' : '#6c757d'))) }};">
                      {{ ucfirst($note->status) }}
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
          $current = $debitNotes->currentPage();
          $last = max(1, $debitNotes->lastPage());
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

  <!-- Debit Note Page View (Full Page) -->
  <div class="client-page-view" id="debitNotePageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="debitNotePageTitle">Debit Note</span> - <span class="client-name" id="debitNotePageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editDebitNoteFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeDebitNotePageBtn" onclick="closeDebitNotePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Debit Note Details View -->
        <div id="debitNoteDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="debitNoteDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Debit Note Edit/Add Form -->
        <div id="debitNoteFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="debitNoteDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteDebitNote()">Delete</button>
                <button type="submit" form="debitNotePageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeDebitNoteFormBtn" onclick="closeDebitNotePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="debitNotePageForm" method="POST" action="{{ route('debit-notes.store') }}" enctype="multipart/form-data">
              @csrf
              <div id="debitNotePageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Debit Note Modal (hidden, used for form structure) -->
  <div class="modal" id="debitNoteModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="debitNoteModalTitle">Add Debit Note</h4>
        <button type="button" class="modal-close" onclick="closeDebitNoteModal()">×</button>
      </div>
      <form id="debitNoteForm" method="POST" action="{{ route('debit-notes.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="debitNoteFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="payment_plan_id">Payment Plan *</label>
              <select class="form-control" name="payment_plan_id" id="payment_plan_id" required>
                <option value="">Select Payment Plan</option>
                @foreach($paymentPlans as $plan)
                  <option value="{{ $plan->id }}">
                    {{ $plan->schedule->policy->policy_no ?? 'N/A' }} - 
                    {{ $plan->schedule->policy->client->client_name ?? 'N/A' }} - 
                    {{ $plan->installment_label ?? 'Instalment #' . $plan->id }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="debit_note_no">Debit Note No *</label>
              <input type="text" class="form-control" name="debit_note_no" id="debit_note_no" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="issued_on">Issued On *</label>
              <input type="date" class="form-control" name="issued_on" id="issued_on" required>
            </div>
            <div class="form-group">
              <label for="amount">Amount *</label>
              <input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="status">Status *</label>
              <select class="form-control" name="status" id="status" required>
                <option value="pending">Pending</option>
                <option value="issued">Issued</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="form-group">
              <label for="document">Document</label>
              <input type="file" class="form-control" name="document" id="document" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeDebitNoteModal()">Cancel</button>
          <button type="button" class="btn-delete" id="debitNoteDeleteBtn" style="display: none;" onclick="deleteDebitNote()">Delete</button>
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

        <form id="columnForm" action="{{ route('debit-notes.save-column-settings') }}" method="POST">
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
  let currentDebitNoteId = null;
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

  // Open debit note details (full page view) - MUST be defined before HTML onclick handlers
  async function openDebitNoteDetails(id) {
    try {
      const res = await fetch(`/debit-notes/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const note = await res.json();
      currentDebitNoteId = id;
      
      // Get all required elements
      const debitNotePageName = document.getElementById('debitNotePageName');
      const debitNotePageTitle = document.getElementById('debitNotePageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const debitNotePageView = document.getElementById('debitNotePageView');
      const debitNoteDetailsPageContent = document.getElementById('debitNoteDetailsPageContent');
      const debitNoteFormPageContent = document.getElementById('debitNoteFormPageContent');
      const editDebitNoteFromPageBtn = document.getElementById('editDebitNoteFromPageBtn');
      const closeDebitNotePageBtn = document.getElementById('closeDebitNotePageBtn');
      
      if (!debitNotePageName || !debitNotePageTitle || !clientsTableView || !debitNotePageView || 
          !debitNoteDetailsPageContent || !debitNoteFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set debit note name in header
      const noteName = note.debit_note_no || 'Unknown';
      debitNotePageName.textContent = noteName;
      debitNotePageTitle.textContent = 'Debit Note';
      
      populateDebitNoteDetails(note);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      debitNotePageView.style.display = 'block';
      debitNotePageView.classList.add('show');
      debitNoteDetailsPageContent.style.display = 'block';
      debitNoteFormPageContent.style.display = 'none';
      if (editDebitNoteFromPageBtn) editDebitNoteFromPageBtn.style.display = 'inline-block';
      if (closeDebitNotePageBtn) closeDebitNotePageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading debit note details: ' + e.message);
    }
  }

  // Populate debit note details view
  function populateDebitNoteDetails(note) {
    const content = document.getElementById('debitNoteDetailsContent');
    if (!content) return;

    const paymentPlan = note.payment_plan || note.paymentPlan || {};
    const schedule = paymentPlan.schedule || {};
    const policy = schedule.policy || {};
    const client = policy.client || {};

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">DEBIT NOTE DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Debit Note No</span>
            <div class="detail-value">${note.debit_note_no || '-'}</div>
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
            <span class="detail-label">Issued On</span>
            <div class="detail-value">${formatDate(note.issued_on)}</div>
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
            <div class="detail-value">${formatNumber(note.amount)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <div class="detail-value">${note.status ? note.status.charAt(0).toUpperCase() + note.status.slice(1) : '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Document</span>
            <div class="detail-value">${note.document_path ? '<a href="#" target="_blank" style="color:#007bff;">View Document</a>' : '-'}</div>
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

  // Open debit note page (Add or Edit)
  async function openDebitNotePage(mode) {
    if (mode === 'add') {
      openDebitNoteForm('add');
    } else {
      if (currentDebitNoteId) {
        openEditDebitNote(currentDebitNoteId);
      }
    }
  }

  // Add Debit Note Button
  document.getElementById('addDebitNoteBtn').addEventListener('click', () => {
    window.location.href = '{{ route("debit-notes.create") }}';
  });
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditDebitNote(id) {
    try {
      const res = await fetch(`/debit-notes/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const note = await res.json();
      currentDebitNoteId = id;
      openDebitNoteForm('edit', note);
    } catch (e) {
      console.error(e);
      alert('Error loading debit note data');
    }
  }

  function openDebitNoteForm(mode, note = null) {
    // Clone form from modal
    const modalForm = document.getElementById('debitNoteModal').querySelector('form');
    const pageForm = document.getElementById('debitNotePageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('debitNotePageFormMethod');
    const deleteBtn = document.getElementById('debitNoteDeleteBtn');
    const editBtn = document.getElementById('editDebitNoteFromPageBtn');
    const closeBtn = document.getElementById('closeDebitNotePageBtn');
    const closeFormBtn = document.getElementById('closeDebitNoteFormBtn');

    if (mode === 'add') {
      document.getElementById('debitNotePageTitle').textContent = 'Add Debit Note';
      document.getElementById('debitNotePageName').textContent = '';
      pageForm.action = '{{ route("debit-notes.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const noteName = note.debit_note_no || 'Unknown';
      document.getElementById('debitNotePageTitle').textContent = 'Edit Debit Note';
      document.getElementById('debitNotePageName').textContent = noteName;
      pageForm.action = `/debit-notes/${currentDebitNoteId}`;
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

      const fields = ['payment_plan_id','debit_note_no','issued_on','amount','status'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = note[k] ? (typeof note[k] === 'string' ? note[k].substring(0,10) : note[k]) : '';
        } else {
          el.value = note[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const debitNotePageView = document.getElementById('debitNotePageView');
    debitNotePageView.style.display = 'block';
    debitNotePageView.classList.add('show');
    document.getElementById('debitNoteDetailsPageContent').style.display = 'none';
    document.getElementById('debitNoteFormPageContent').style.display = 'block';
  }

  function closeDebitNotePageView() {
    const debitNotePageView = document.getElementById('debitNotePageView');
    debitNotePageView.classList.remove('show');
    debitNotePageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('debitNoteDetailsPageContent').style.display = 'none';
    document.getElementById('debitNoteFormPageContent').style.display = 'none';
    currentDebitNoteId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editDebitNoteFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentDebitNoteId) {
        openEditDebitNote(currentDebitNoteId);
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

  function deleteDebitNote() {
    if (!currentDebitNoteId) return;
    if (!confirm('Delete this debit note?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/debit-notes/${currentDebitNoteId}`;
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
  function openDebitNoteModal(mode, note = null) {
    if (mode === 'add') {
      openDebitNotePage('add');
    } else if (note && currentDebitNoteId) {
      openEditDebitNote(currentDebitNoteId);
    }
  }

  function closeDebitNoteModal() {
    closeDebitNotePageView();
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
