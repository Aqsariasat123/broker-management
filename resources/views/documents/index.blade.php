@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('documents');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('documents');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Documents Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Documents Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Documents</h3>
        <div class="records-found">Records Found - {{ $documents->total() }}</div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addDocumentBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="documentsTable">
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
          @foreach($documents as $doc)
            <tr>
              <td class="action-cell">
                <svg class="action-expand" onclick="openDocumentDetails({{ $doc->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'doc_id')
                  <td data-column="doc_id">
                    <a href="javascript:void(0)" onclick="openDocumentDetails({{ $doc->id }})" style="color:#007bff; text-decoration:underline;">{{ $doc->doc_id }}</a>
                  </td>
                @elseif($col == 'tied_to')
                  <td data-column="tied_to">{{ $doc->tied_to ?? '-' }}</td>
                @elseif($col == 'name')
                  <td data-column="name">{{ $doc->name ?? '-' }}</td>
                @elseif($col == 'group')
                  <td data-column="group">{{ $doc->group ?? '-' }}</td>
                @elseif($col == 'type')
                  <td data-column="type">{{ $doc->type ?? '-' }}</td>
                @elseif($col == 'format')
                  <td data-column="format">{{ $doc->format ?? '-' }}</td>
                @elseif($col == 'date_added')
                  <td data-column="date_added">{{ $doc->date_added ? \Carbon\Carbon::parse($doc->date_added)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'year')
                  <td data-column="year">{{ $doc->year ?? '-' }}</td>
                @elseif($col == 'file_path')
                  <td data-column="file_path">
                    @if($doc->file_path)
                      <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" style="color:#007bff; text-decoration:underline;">View</a>
                    @else
                      -
                    @endif
                  </td>
                @elseif($col == 'notes')
                  <td data-column="notes">{{ $doc->notes ?? '-' }}</td>
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
        <a class="btn btn-export" href="{{ route('documents.export') }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $documents->currentPage();
          $last = max(1, $documents->lastPage());
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

  <!-- Document Page View (Full Page) -->
  <div class="client-page-view" id="documentPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="documentPageTitle">Document</span> - <span class="client-name" id="documentPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editDocumentFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeDocumentPageBtn" onclick="closeDocumentPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Document Details View -->
        <div id="documentDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="documentDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Document Edit/Add Form -->
        <div id="documentFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="documentDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteDocument()">Delete</button>
                <button type="submit" form="documentPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeDocumentFormBtn" onclick="closeDocumentPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="documentPageForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
              @csrf
              <div id="documentPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Document Modal (hidden, used for form structure) -->
  <div class="modal" id="documentModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="documentModalTitle">Add Document</h4>
        <button type="button" class="modal-close" onclick="closeDocumentModal()">×</button>
      </div>
      <form id="documentForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="documentFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="tied_to">Tied To</label>
              <input type="text" class="form-control" name="tied_to" id="tied_to">
            </div>
            <div class="form-group">
              <label for="name">Name *</label>
              <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="form-group">
              <label for="group">Group</label>
              <input type="text" class="form-control" name="group" id="group">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="type">Type</label>
              <input type="text" class="form-control" name="type" id="type">
            </div>
            <div class="form-group">
              <label for="date_added">Date Added</label>
              <input type="date" class="form-control" name="date_added" id="date_added">
            </div>
            <div class="form-group">
              <label for="year">Year</label>
              <input type="text" class="form-control" name="year" id="year">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="file">File</label>
              <input type="file" class="form-control" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="notes">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeDocumentModal()">Cancel</button>
          <button type="button" class="btn-delete" id="documentDeleteBtnModal" style="display: none;" onclick="deleteDocument()">Delete</button>
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

        <form id="columnForm" action="{{ route('documents.save-column-settings') }}" method="POST">
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
  let currentDocumentId = null;
  const selectedColumns = @json($selectedColumns);
  const mandatoryColumns = @json($mandatoryColumns);

  // Helper function for date formatting
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
  }

  // Open document details (full page view) - MUST be defined before HTML onclick handlers
  async function openDocumentDetails(id) {
    try {
      const res = await fetch(`/documents/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const doc = await res.json();
      currentDocumentId = id;
      
      // Get all required elements
      const documentPageName = document.getElementById('documentPageName');
      const documentPageTitle = document.getElementById('documentPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const documentPageView = document.getElementById('documentPageView');
      const documentDetailsPageContent = document.getElementById('documentDetailsPageContent');
      const documentFormPageContent = document.getElementById('documentFormPageContent');
      const editDocumentFromPageBtn = document.getElementById('editDocumentFromPageBtn');
      const closeDocumentPageBtn = document.getElementById('closeDocumentPageBtn');
      
      if (!documentPageName || !documentPageTitle || !clientsTableView || !documentPageView || 
          !documentDetailsPageContent || !documentFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set document name in header
      const documentName = doc.name || doc.doc_id || 'Unknown';
      documentPageName.textContent = documentName;
      documentPageTitle.textContent = 'Document';
      
      populateDocumentDetails(doc);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      documentPageView.style.display = 'block';
      documentPageView.classList.add('show');
      documentDetailsPageContent.style.display = 'block';
      documentFormPageContent.style.display = 'none';
      if (editDocumentFromPageBtn) editDocumentFromPageBtn.style.display = 'inline-block';
      if (closeDocumentPageBtn) closeDocumentPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading document details: ' + e.message);
    }
  }

  // Populate document details view
  function populateDocumentDetails(doc) {
    const content = document.getElementById('documentDetailsContent');
    if (!content) return;

    const fileLink = doc.file_path ? `<a href="{{ asset('storage') }}/${doc.file_path}" target="_blank" style="color:#007bff; text-decoration:underline;">View File</a>` : '-';

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">DOCUMENT BASIC INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Document ID</span>
            <div class="detail-value">${doc.doc_id || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Name</span>
            <div class="detail-value">${doc.name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Tied To</span>
            <div class="detail-value">${doc.tied_to || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Group</span>
            <div class="detail-value">${doc.group || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Type</span>
            <div class="detail-value">${doc.type || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">DOCUMENT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Format</span>
            <div class="detail-value">${doc.format || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Added</span>
            <div class="detail-value">${formatDate(doc.date_added)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Year</span>
            <div class="detail-value">${doc.year || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">File</span>
            <div class="detail-value">${fileLink}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">NOTES</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Notes</span>
            <textarea class="detail-value" style="min-height:200px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${doc.notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header"></div>
        <div class="detail-section-body">
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open document page (Add or Edit)
  async function openDocumentPage(mode) {
    if (mode === 'add') {
      openDocumentForm('add');
    } else {
      if (currentDocumentId) {
        openEditDocument(currentDocumentId);
      }
    }
  }

  // Add Document Button
  document.getElementById('addDocumentBtn').addEventListener('click', () => openDocumentPage('add'));
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditDocument(id) {
    try {
      const res = await fetch(`/documents/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const doc = await res.json();
      currentDocumentId = id;
      openDocumentForm('edit', doc);
    } catch (e) {
      console.error(e);
      alert('Error loading document data');
    }
  }

  function openDocumentForm(mode, doc = null) {
    // Clone form from modal
    const modalForm = document.getElementById('documentModal').querySelector('form');
    const pageForm = document.getElementById('documentPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('documentPageFormMethod');
    const deleteBtn = document.getElementById('documentDeleteBtn');
    const editBtn = document.getElementById('editDocumentFromPageBtn');
    const closeBtn = document.getElementById('closeDocumentPageBtn');
    const closeFormBtn = document.getElementById('closeDocumentFormBtn');

    if (mode === 'add') {
      document.getElementById('documentPageTitle').textContent = 'Add Document';
      document.getElementById('documentPageName').textContent = '';
      pageForm.action = '{{ route("documents.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const documentName = doc.name || doc.doc_id || 'Unknown';
      document.getElementById('documentPageTitle').textContent = 'Edit Document';
      document.getElementById('documentPageName').textContent = documentName;
      pageForm.action = `/documents/${currentDocumentId}`;
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

      const fields = ['tied_to','name','group','type','date_added','year','notes'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = doc[k] ? (typeof doc[k] === 'string' ? doc[k].substring(0,10) : doc[k]) : '';
        } else if (el.tagName === 'TEXTAREA') {
          el.value = doc[k] ?? '';
        } else {
          el.value = doc[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const documentPageView = document.getElementById('documentPageView');
    documentPageView.style.display = 'block';
    documentPageView.classList.add('show');
    document.getElementById('documentDetailsPageContent').style.display = 'none';
    document.getElementById('documentFormPageContent').style.display = 'block';
  }

  function closeDocumentPageView() {
    const documentPageView = document.getElementById('documentPageView');
    documentPageView.classList.remove('show');
    documentPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('documentDetailsPageContent').style.display = 'none';
    document.getElementById('documentFormPageContent').style.display = 'none';
    currentDocumentId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editDocumentFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentDocumentId) {
        openEditDocument(currentDocumentId);
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

  function deleteDocument() {
    if (!currentDocumentId) return;
    if (!confirm('Delete this document?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/documents/${currentDocumentId}`;
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
  function openDocumentModal(mode, doc = null) {
    if (mode === 'add') {
      openDocumentPage('add');
    } else if (doc && currentDocumentId) {
      openEditDocument(currentDocumentId);
    }
  }

  function closeDocumentModal() {
    closeDocumentPageView();
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
