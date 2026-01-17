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

// Open commission details (full page view) - MUST be defined before HTML onclick handlers
// async function openCommissionDetails(id) {
//   try {
//     const res = await fetch(`/commissions/${id}`, {
//       headers: {
//         'Accept': 'application/json',
//         'X-Requested-With': 'XMLHttpRequest'
//       }
//     });
//     if (!res.ok) throw new Error(`HTTP ${res.status}`);
//     const commission = await res.json();
//     console.log(commission);
//     currentCommissionId = id;

//     // Get all required elements
//     const commissionPageName = document.getElementById('commissionPageName');
//     const commissionPageTitle = document.getElementById('commissionPageTitle');
//     const clientsTableView = document.getElementById('clientsTableView');
//     const commissionPageView = document.getElementById('commissionPageView');
//     const commissionDetailsPageContent = document.getElementById('commissionDetailsPageContent');
//     const commissionFormPageContent = document.getElementById('commissionFormPageContent');
//     const editCommissionFromPageBtn = document.getElementById('editCommissionFromPageBtn');
//     const closeCommissionPageBtn = document.getElementById('closeCommissionPageBtn');

//     if (!commissionPageName || !commissionPageTitle || !clientsTableView || !commissionPageView ||
//       !commissionDetailsPageContent || !commissionFormPageContent) {
//       console.error('Required elements not found');
//       alert('Error: Page elements not found');
//       return;
//     }

//     // Set commission name in header
//     const commissionName = commission.policy_number || commission.cnid || 'Unknown';
//     commissionPageName.textContent = commissionName;
//     commissionPageTitle.textContent = 'Commission';

//     populateCommissionDetails(commission);

//     // Hide table view, show page view
//     clientsTableView.classList.add('hidden');
//     commissionPageView.style.display = 'block';
//     commissionPageView.classList.add('show');
//     commissionDetailsPageContent.style.display = 'block';
//     commissionFormPageContent.style.display = 'none';
//     if (editCommissionFromPageBtn) editCommissionFromPageBtn.style.display = 'inline-block';
//     if (closeCommissionPageBtn) closeCommissionPageBtn.style.display = 'inline-block';
//   } catch (e) {
//     console.error(e);
//     alert('Error loading commission details: ' + e.message);
//   }
// }

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
document.addEventListener('DOMContentLoaded', function () {
  const addBtn = document.getElementById('addCommissionBtn');

  if (addBtn) {
    addBtn.addEventListener('click', function () {
      openCommissionModal('add');
    });
  }
});
document.getElementById('addPreviewStatement')?.addEventListener('click', function () {
  const params = new URLSearchParams(window.location.search);
  const insurer = params.get('insurer') || '';

  const target = new URL('/statements', window.location.origin);
  target.searchParams.set('insurer', insurer);
  target.searchParams.set('page', 'commission');

  window.location.href = target.toString();
});

document.addEventListener('DOMContentLoaded', function () {
  const addBtn = document.getElementById('addCommissionBtn');

  if (addBtn) {
    addBtn.addEventListener('click', function () {
      openCommissionModal('add');
    });
  }
});
// Add Commission Button
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
    pageForm.action = commissionsStoreRoute;
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

    const fields = ['policy_number', 'client_name', 'insurer_id', 'grouping', 'basic_premium', 'rate', 'amount_due', 'payment_status_id', 'amount_rcvd', 'date_rcvd', 'state_no', 'mode_of_payment_id', 'variance', 'reason', 'date_due'];
    fields.forEach(k => {
      const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
      if (!el) return;
      if (el.type === 'date') {
        el.value = commission[k] ? (typeof commission[k] === 'string' ? commission[k].substring(0, 10) : commission[k]) : '';
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
  editBtn.addEventListener('click', function () {
    if (currentCommissionId) {
      openEditCommission(currentCommissionId);
    }
  });
}

// function filterByInsurer(insurer) {
//   window.location.href = `${commissionsIndexRoute}?insurer=${insurer}`;
// }
function filterByInsurer(insurer = null) {
  const url = new URL(window.location.href);
  const params = url.searchParams;

  const currentInsurer = params.get('insurer');

  // If same insurer clicked again → reset
  if (!insurer || currentInsurer === insurer) {
    params.delete('insurer');
  } else {
    params.set('insurer', insurer);
  }

  window.location.href = url.toString();
}


function filterByPaidStatus(paid_status = null) {
  const url = new URL(window.location.href);
  const params = url.searchParams;

  const currentStatus = params.get('paid_status');

  // If same filter clicked again OR "All"
  if (!paid_status || currentStatus === paid_status) {
    params.delete('paid_status');
  } else {
    params.set('paid_status', paid_status);
  }

  window.location.href = url.toString();
}


// Update UI on page load
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('insurerFilterToggle');
  if (!toggle) return;

  const params = new URLSearchParams(window.location.search);
  const currentInsurer = params.get('insurer');
  const currentPaidStatus = params.get('paid_status');

  const hasFilter = !!currentInsurer || !!currentPaidStatus;

  // Sync toggle
  toggle.checked = hasFilter;

  // Update button states
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.classList.remove('active-all', 'active-insurer');

    const btnText = btn.textContent.trim();

    if (btnText === 'All' && currentPaidStatus === 'Unpaid') {
      btn.classList.add('active-insurer');
    }
    else if (btnText === currentInsurer) {
      btn.classList.add('active-insurer');
    }
  });
  toggle.addEventListener('change', function () {
    if (!this.checked) {
      // Clear all query params
      const url = new URL(window.location.href);
      url.search = '';

      // Reload page with clean URL
      window.location.href = url.toString();
    }
  });
});

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
  const items = Array.from(document.querySelectorAll('#columnSelection .column-item, #columnSelection .column-item-vertical'));
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

function openCommissionModal(mode, commissionId = null) {
  const modal = document.getElementById('commissionModal');
  const form = document.getElementById('commissionForm');
  const formMethod = document.getElementById('commissionFormMethod');
  const modalTitle = document.getElementById('commissionModalTitle');
  const deleteBtn = document.getElementById('commissionDeleteBtn');

  if (mode === 'add') {
    modalTitle.textContent = 'Add Commission';
    form.reset();
    form.action = commissionsStoreRoute;
    formMethod.innerHTML = '';
    deleteBtn.style.display = 'none';
    currentCommissionId = null;
  } else if (mode === 'edit' && commissionId) {
    modalTitle.textContent = 'Edit Commission';
    form.action = commissionsUpdateRouteTemplate.replace('__ID__', commissionId);
    formMethod.innerHTML = '@method("PUT")';
    const existingMethodInput = form.querySelector('input[name="_method"]');
    if (existingMethodInput) existingMethodInput.remove();

    // Add PUT method spoofing
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);
    deleteBtn.style.display = 'inline-block';
    currentCommissionId = commissionId;

    // Fetch commission data
    fetch(`/commissions/${commissionId}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(response => response.json())
      .then(data => {
        console.log(data);
        if (data) {
          const c = data;
          console.log(c);
          document.getElementById('commission_note_id').value = c.commission_note_id || '';
          document.getElementById('commission_statement_id').value = c.commission_statement_id || '';
          document.getElementById('basic_premium').value = c.basic_premium || '';
          document.getElementById('rate').value = c.rate || '';
          document.getElementById('amount_due').value = c.amount_due || '';
          document.getElementById('payment_status_id').value = c.payment_status_id || '';
          document.getElementById('amount_received').value = c.amount_received || '';
          document.getElementById('date_received').value = c.date_received ? c.date_received.split('T')[0] : '';
          document.getElementById('date_due').value = c.date_due ? c.date_due.split('T')[0] : '';
          document.getElementById('mode_of_payment_id').value = c.mode_of_payment_id || '';
          document.getElementById('variance').value = c.variance || '';
          document.getElementById('variance_reason').value = c.variance_reason || '';
          // document.getElementById('date_due').value = c.date_due ? c.date_due.split('T')[0] : '';
        }
      })
      .catch(error => {
        console.error('Error fetching commission data:', error);
        alert('Error loading commission data');
      });
  }

  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeCommissionModal() {
  const modal = document.getElementById('commissionModal');
  modal.classList.remove('show');
  document.body.style.overflow = '';
  const form = document.getElementById('commissionForm');
  form.reset();
  currentCommissionId = null;
}

// Close modal on outside click
document.getElementById('commissionModal').addEventListener('click', function (e) {
  if (e.target === this) {
    closeCommissionModal();
  }
});

// Handle form submission
document.getElementById('commissionForm').addEventListener('submit', function (e) {
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
        closeCommissionModal();
        window.location.reload();
      } else {
        alert(data.message || 'Error saving commission');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving commission');
    });
});

// Update openCommissionDetails to open modal in edit mode
// const originalOpenCommissionDetails = window.openCommissionDetails;
// window.openCommissionDetails = function (id) {
// };
// openCommissionModal('edit', id);

// Only declare if not already declared (to avoid duplicate declaration errors)
if (typeof draggedElement === 'undefined') {
  var draggedElement = null;
}
if (typeof dragOverElement === 'undefined') {
  var dragOverElement = null;
}

// Initialize drag and drop when column modal opens
let dragInitialized = false;

function initDragAndDrop() {
  const columnSelection = document.getElementById('columnSelection');
  if (!columnSelection) return;

  // Only initialize once to avoid duplicate event listeners
  if (dragInitialized) {
    // Re-enable draggable on all items
    const columnItems = columnSelection.querySelectorAll('.column-item, .column-item-vertical');
    columnItems.forEach(item => {
      item.setAttribute('draggable', 'true');
    });
    return;
  }

  // Make all column items draggable
  const columnItems = columnSelection.querySelectorAll('.column-item, .column-item-vertical');

  columnItems.forEach(item => {
    // Ensure draggable attribute is set
    item.setAttribute('draggable', 'true');
    item.style.cursor = 'move';

    // Drag start
    item.addEventListener('dragstart', function (e) {
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
    item.addEventListener('dragend', function (e) {
      this.classList.remove('dragging');
      if (dragOverElement) {
        dragOverElement.classList.remove('drag-over');
        dragOverElement = null;
      }
      draggedElement = null;
    });

    // Drag over
    item.addEventListener('dragover', function (e) {
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
    item.addEventListener('dragleave', function (e) {
      if (!this.contains(e.relatedTarget)) {
        this.classList.remove('drag-over');
        if (dragOverElement === this) {
          dragOverElement = null;
        }
      }
    });

    // Drop
    item.addEventListener('drop', function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.remove('drag-over');
      dragOverElement = null;
      return false;
    });
  });

  dragInitialized = true;
}
