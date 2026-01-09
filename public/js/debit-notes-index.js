// Data initialized in Blade template

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
window.openDebitNoteDetails = async function (id) {
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
};

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
            <div class="detail-value">
              ${note.documents && note.documents.length > 0
      ? note.documents.map(doc =>
        `<a href="/storage/${doc.file_path}" target="_blank" style="color:#007bff; text-decoration:underline; display:block; margin-bottom:4px;">${doc.name || 'View Document'}</a>`
      ).join('')
      : '-'
    }
            </div>
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

const filterToggle = document.getElementById('filterToggle');

if (filterToggle) {
  const params = new URLSearchParams(window.location.search);

  // Toggle ON if filter=overdue OR status exists
  filterToggle.checked =
    params.get('filter') === 'overdue' ||
    ['pending', 'issued', 'paid', 'overdue', 'cancelled']
      .includes(params.get('status'));

  filterToggle.addEventListener('change', function () {
    const u = new URL(window.location.href);

    if (this.checked) {
      // apply overdue filter
      u.searchParams.set('filter', 'overdue');
      window.location.href = u.toString();
    } else {
      // 🔥 remove ALL query params
      window.location.href = u.origin + u.pathname;
    }
  });
}


document.addEventListener('DOMContentLoaded', function () {
  const params = new URLSearchParams(window.location.search);
  const isOverdue = params.get('filter') === 'overdue';

  if (!isOverdue) return;

  document.querySelectorAll('.debit-note-link').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault(); // stop navigation

      const noteId = this.dataset.noteId;
      if (noteId) {
        openDebitNoteDetails(noteId);
      }
    });
  });
});
// Add Debit Note Button
document.getElementById('addDebitNoteBtn').addEventListener('click', () => openDebitNoteModal('add'));

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
    pageForm.action = debitNotesStoreRoute;
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

    const fields = ['payment_plan_id', 'debit_note_no', 'issued_on', 'amount', 'status'];
    fields.forEach(k => {
      const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
      if (!el) return;
      if (el.type === 'date') {
        el.value = note[k] ? (typeof note[k] === 'string' ? note[k].substring(0, 10) : note[k]) : '';
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
  editBtn.addEventListener('click', function () {
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
  csrf.value = csrfToken;
  form.appendChild(csrf);
  const method = document.createElement('input');
  method.type = 'hidden';
  method.name = '_method';
  method.value = 'DELETE';
  form.appendChild(method);
  document.body.appendChild(form);
  form.submit();
}

function openDebitNoteModal(mode, noteId = null) {
  const modal = document.getElementById('debitNoteModal');
  const form = document.getElementById('debitNoteForm');
  const formMethod = document.getElementById('debitNoteFormMethod');
  const modalTitle = document.getElementById('debitNoteModalTitle');
  const deleteBtn = document.getElementById('debitNoteDeleteBtn');

  if (mode === 'add') {
    modalTitle.textContent = 'Add Debit Note';
    form.reset();
    form.action = debitNotesStoreRoute;
    formMethod.innerHTML = '';
    deleteBtn.style.display = 'none';
    currentDebitNoteId = null;
  } else if (mode === 'edit' && noteId) {
    modalTitle.textContent = 'Edit Debit Note';
    form.action = debitNotesUpdateRouteTemplate.replace(':id', noteId);
    formMethod.innerHTML = '@method("PUT")';
    deleteBtn.style.display = 'inline-block';
    currentDebitNoteId = noteId;

    // Fetch debit note data
    fetch(`/debit-notes/${noteId}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(response => response.json())
      .then(data => {
        if (data.debitNote) {
          const d = data.debitNote;
          document.getElementById('payment_plan_id').value = d.payment_plan_id || '';
          document.getElementById('debit_note_no').value = d.debit_note_no || '';
          document.getElementById('issued_on').value = d.issued_on ? d.issued_on.split('T')[0] : '';
          document.getElementById('amount').value = d.amount || '';
          document.getElementById('status').value = d.status || 'pending';

          // Show existing document preview if available (from documents table)
          const existingPreview = document.getElementById('existingDocumentPreview');
          const existingPreviewContent = document.getElementById('existingDocumentPreviewContent');

          if (d.documents && d.documents.length > 0) {
            existingPreview.style.display = 'block';
            existingPreviewContent.innerHTML = d.documents.map(doc =>
              `<a href="/storage/${doc.file_path}" target="_blank" style="color:#007bff; text-decoration:underline; display:block; margin-bottom:4px; font-size:13px;">${doc.name || 'View Document'}</a>`
            ).join('');
          } else {
            existingPreview.style.display = 'none';
          }
        }
      })
      .catch(error => {
        console.error('Error fetching debit note data:', error);
        alert('Error loading debit note data');
      });
  }

  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeDebitNoteModal() {
  const modal = document.getElementById('debitNoteModal');
  modal.classList.remove('show');
  document.body.style.overflow = '';
  const form = document.getElementById('debitNoteForm');
  form.reset();
  currentDebitNoteId = null;
  // Reset document preview
  const existingPreview = document.getElementById('existingDocumentPreview');
  if (existingPreview) {
    existingPreview.style.display = 'none';
    const existingPreviewContent = document.getElementById('existingDocumentPreviewContent');
    if (existingPreviewContent) {
      existingPreviewContent.innerHTML = '';
    }
  }
}

// Close modal on outside click
document.getElementById('debitNoteModal').addEventListener('click', function (e) {
  if (e.target === this) {
    closeDebitNoteModal();
  }
});

// Handle form submission
document.getElementById('debitNoteForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = this;
  const formData = new FormData(form);
  const url = form.action;
  const method = form.querySelector('[name="_method"]') ? form.querySelector('[name="_method"]').value : 'POST';

  if (method !== 'POST') {
    formData.append('_method', method);
  }

  fetch(url, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        closeDebitNoteModal();
        window.location.reload();
      } else {
        alert(data.message || 'Error saving debit note');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving debit note');
    });
});

// Update openDebitNoteDetails to open modal in edit mode instead of full page view
const originalOpenDebitNoteDetails = window.openDebitNoteDetails;
window.openDebitNoteDetails = function (id) {
  openDebitNoteModal('edit', id);
};
