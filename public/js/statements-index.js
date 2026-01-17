/* ============================================================
   GLOBAL STATE
============================================================ */
let currentStatementId = null;

/* ============================================================
   HELPERS
============================================================ */
function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: '2-digit'
  }).replace(',', '');
}

function formatNumber(num) {
  if (num === null || num === undefined) return '-';
  return parseFloat(num).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

/* ============================================================
   VIEW SWITCHING
============================================================ */
function showTableView() {
  document.getElementById('clientsTableView').classList.remove('hidden');
  document.getElementById('statementPageView').style.display = 'none';
  document.getElementById('statementDetailsPageContent').style.display = 'none';
  document.getElementById('statementFormPageContent').style.display = 'none';
  currentStatementId = null;
}

function showPageView() {
  document.getElementById('clientsTableView').classList.add('hidden');
  const page = document.getElementById('statementPageView');
  page.style.display = 'block';
  page.classList.add('show');
}

/* ============================================================
   VIEW DETAILS (FULL PAGE – NO MODAL)
============================================================ */
async function openStatementDetails(id) {
  try {
    const res = await fetch(`/statements/${id}`, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) throw new Error('Failed to load');

    const statement = await res.json();
    currentStatementId = id;

    document.getElementById('statementPageTitle').textContent = 'Statement';
    document.getElementById('statementPageName').textContent = statement.statement_no || '';

    populateStatementDetails(statement);

    showPageView();
    document.getElementById('statementDetailsPageContent').style.display = 'block';
    document.getElementById('statementFormPageContent').style.display = 'none';
    // document.getElementById('editStatementFromPageBtn').style.display = 'inline-block';

  } catch (e) {
    alert('Error loading statement');
    console.error(e);
  }
}

/* ============================================================
   DETAILS HTML
============================================================ */
// function populateStatementDetails(statement) {

//   console.log('Statement details:', statement); // For debugging
//   const el = document.getElementById('statementDetailsContent');

//   el.innerHTML = `
//     <div class="statement-container">
//       <!-- Summary Title -->
//       <div class="summary-title">Commission Statement Summary</div>

//       <!-- Summary Bar - Exact match to your screenshot -->
//       <div class="summary-bar">
//         <div class="summary-item">
//           <span class="summary-label">Insurer</span>
//           <input type="text" class="summary-input" value="${statement.commission_note.schedule.policy.insurer.name}" readonly>
//         </div>

//         <div class="summary-item">
//           <span class="summary-label">Class</span>
//           <input type="text" class="summary-input" value="General" readonly>
//         </div>

//         <div class="summary-item">
//           <span class="summary-label">Total</span>
//           <div class="summary-input">2074.89</div>
//         </div>

//         <div class="summary-item">
//           <span class="summary-label">Date Received</span>
//           <div class="summary-input">28-Sep-24</div>
//         </div>

//         <div class="summary-item">
//           <span class="summary-label">Mode</span>
//           <input type="text" class="summary-input" value="Bank Transfer" readonly>
//         </div>

//         <div class="summary-item">
//           <span class="summary-label">Chq No</span>
//           <input type="text" class="summary-input" value="NOU0000000" readonly>
//         </div>
//       </div>

//       <!-- Commission Details Title -->
//       <div class="details-title">Commission Details</div>

//       <!-- Table -->
//       <table class="details-table">
//         <thead>
//           <tr>
//             <th>CNID</th>
//             <th>Policy Number</th>
//             <th>Client Name</th>
//             <th>Basic Premi</th>
//             <th>Rate</th>
//             <th>Amount Due</th>
//             <th>Amount Received</th>
//             <th>Variance</th>
//             <th>Variance Reason</th>
//           </tr>
//         </thead>
//         <tbody>
//           <tr>
//             <td>CN23061</td>
//             <td>MPV-23-HEA-P0002110</td>
//             <td>Kendra Moller</td>
//             <td>7,356.40</td>
//             <td>10.0%</td>
//             <td>735.64</td>
//             <td>735.64</td>
//             <td>0.00</td>
//             <td>-</td>
//           </tr>
//           <tr>
//             <td>CN23059</td>
//             <td>FSP-23-HEA-P0002309</td>
//             <td>Pierro Leclerc</td>
//             <td>8,441.00</td>
//             <td>15.0%</td>
//             <td>1,266.15</td>
//             <td>633.08</td>
//             <td>633.08</td>
//             <td>-</td>
//           </tr>
//           <tr>
//             <td>CN23055</td>
//             <td>HS1-23-HIN-0000088</td>
//             <td>Margo Slater</td>
//             <td>4,793.50</td>
//             <td>15.0%</td>
//             <td>719.03</td>
//             <td>719.03</td>
//             <td>0.00</td>
//             <td class="variance-reason-cell">Part commission on instalment</td>
//           </tr>
//         </tbody>
//       </table>
//     </div>
//   `;
// }
function closeStatementModal() {
  document.getElementById('statementModal').classList.remove('show');
  currentVehicleId = null;
}
function populateStatementDetails(statement) {
  console.log('Statement details:', statement); // For debugging
  const el = document.getElementById('statementDetailsContent');

  // Get first commission (if exists)
const firstCommission = statement.commissions?.[0] ?? null;

const insurerName =
  firstCommission?.commission_note?.schedule?.policy?.insurer?.name ?? '-';

const policyClass =
  firstCommission?.commission_note?.schedule?.policy?.policy_class?.name ?? '-';

  el.innerHTML = `
    <div class="statement-container">
      <!-- Summary Title -->
      <div class="summary-title">Commission Statement Summary</div>

      <!-- Summary Bar -->
      <div class="summary-bar">
        <div class="summary-item">
          <span class="summary-label">Insurer</span>
          <input type="text" class="summary-input" value="${insurerName}" readonly>
        </div>

        <div class="summary-item">
          <span class="summary-label">Class</span>
          <input type="text" class="summary-input" value="General" readonly>
        </div>

        <div class="summary-item">
          <span class="summary-label">Total</span>
          <div class="summary-input">${Number(statement.net_commission).toFixed(2)}</div>
        </div>

        <div class="summary-item">
          <span class="summary-label">Date Received</span>
          <div class="summary-input">${firstCommission && firstCommission.date_received
      ? new Date(firstCommission.date_received).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: '2-digit'
      })
      : '-'
    }</div>
        </div>

        <div class="summary-item">
          <span class="summary-label">Mode</span>
          <input type="text" class="summary-input" value="${firstCommission && firstCommission.mode_of_payment
      ? firstCommission.mode_of_payment.name
      : '-'
    }" readonly>
        </div>

        <div class="summary-item">
          <span class="summary-label">Chq No</span>
          <input type="text" class="summary-input" value="${firstCommission ? firstCommission.commission_code : '-'}" readonly>
        </div>
      </div>

      <!-- Commission Details Title -->
      <div class="details-title">Commission Details</div>

      <!-- Table -->
      <table class="details-table">
        <thead>
          <tr>
            <th>CNID</th>
            <th>Policy Number</th>
            <th>Client Name</th>
            <th>Basic Premi</th>
            <th>Rate</th>
            <th>Amount Due</th>
            <th>Amount Received</th>
            <th>Variance</th>
            <th>Variance Reason</th>
          </tr>
        </thead>
        <tbody>
          ${statement.commissions
      .map(
        (c) => `
            <tr>
              <td>${statement.commission_note?.com_note_id ?? '--' }</td>
              <td>${statement?.commission_note?.schedule?.policy?.policy_no ?? '---'}</td>
              <td>${statement.commission_note?.schedule?.policy?.insured_item || '-'}</td>
              <td>${Number(c.basic_premium).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
              <td>${c.rate}%</td>
              <td>${Number(c.amount_due).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
              <td>${Number(c.amount_received).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
              <td>${c.variance ? Number(c.variance).toFixed(2) : '0.00'}</td>
              <td>${c.variance_reason || '-'}</td>
            </tr>
          `
      )
      .join('')}
        </tbody>
      </table>
    </div>
  `;
}

/* ============================================================
   ADD / EDIT FORM (FULL PAGE)
============================================================ */
function openAddForm() {
  openStatementForm('add');
}

async function openEditStatement(id) {
  const res = await fetch(`/statements/${id}/edit`, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  });
  const s = await res.json();
  openStatementForm('edit', s);
}

function openStatementForm(mode, s = null) {
    currentStatementId = s?.id || null;

    const modal = document.getElementById('statementModal');
    const form = document.getElementById('statementForm');
    const method = document.getElementById('statementFormMethod');

    document.getElementById('statementModalTitle').textContent =
        mode === 'add' ? 'Add Statement' : 'Edit Statement';

    method.innerHTML = '';

    if (mode === 'edit' && s) {
        form.action = `/statements/${currentStatementId}`;
        method.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
        document.getElementById('statementDeleteBtn').style.display = 'inline-block';

        [
            'year',
            'insurer_id',
            'business_category',
            'date_received',
            'amount_received',
            'mode_of_payment_id',
            'remarks'
        ].forEach(f => {
            const el = document.getElementById(f);
            if (el) el.value = s[f] ?? '';
        });

    } else {
        form.reset();
        form.action = statementsStoreRoute;
        document.getElementById('statementDeleteBtn').style.display = 'none';
         const insurerFromUrl = getInsurerFromUrl();
    if (insurerFromUrl) {
        const insurerSelect = document.getElementById('insurer_id');

        if (insurerSelect) {
            [...insurerSelect.options].forEach(option => {
                if (option.text.trim().toLowerCase() === insurerFromUrl.toLowerCase()) {
                    option.selected = true;
                }
            });
        }
    }
    }

    // ✅ SHOW MODAL
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}
function getInsurerFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('insurer')?.trim() || null;
}
/* ============================================================
   CLOSE PAGE
============================================================ */
function closeStatementPageView() {
  showTableView();
}

/* ============================================================
   DELETE
============================================================ */
function deleteStatement() {
  if (!currentStatementId || !confirm('Delete this statement?')) return;

  const f = document.createElement('form');
  f.method = 'POST';
  f.action = `/statements/${currentStatementId}`;
  f.innerHTML = `
    <input type="hidden" name="_token" value="${csrfToken}">
    <input type="hidden" name="_method" value="DELETE">
  `;
  document.body.appendChild(f);
  f.submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('insurerFilterToggle');
    if (!toggle) return;

    const params = new URLSearchParams(window.location.search);
    const currentInsurer = params.get('insurer');

    const hasFilter = !!currentInsurer;

    // Sync toggle
    toggle.checked = hasFilter;

    // Update button states
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active-all', 'active-insurer');

        const btnText = btn.textContent.trim();

         if (btnText === currentInsurer) {
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

/* ============================================================
   EVENTS - Setup on DOMContentLoaded
============================================================ */
document.addEventListener('DOMContentLoaded', function() {
  // Add Statement Button
  const addBtn = document.getElementById('addStatementBtn');
  if (addBtn) {
    addBtn.addEventListener('click', openAddForm);
  }

  // Column Button
  const columnBtn = document.getElementById('columnBtn');
  if (columnBtn) {
    columnBtn.addEventListener('click', function() {
      openColumnModal();
    });
  }
});

/* ============================================================
   COLUMN MODAL FUNCTIONS
============================================================ */
function initializeColumnCheckboxes() {
  const checkboxes = document.querySelectorAll('.column-checkbox');
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectedColumns.includes(checkbox.value);
  });
}

function openColumnModal() {
  initializeColumnCheckboxes();
  document.body.style.overflow = 'hidden';
  document.getElementById('columnModal').classList.add('show');
  setTimeout(initDragAndDrop, 100);
}

function closeColumnModal() {
  document.getElementById('columnModal').classList.remove('show');
  document.body.style.overflow = '';
}

function saveColumnSettings() {
  const mandatoryFields = typeof mandatoryColumns !== 'undefined' ? mandatoryColumns : [];

  const items = Array.from(document.querySelectorAll('#columnSelection .column-item, #columnSelection .column-item-vertical'));
  const order = items.map(item => item.dataset.column);
  const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n=>n.value);

  mandatoryFields.forEach(field => {
    if (!checked.includes(field)) {
      checked.push(field);
    }
  });

  const orderedChecked = order.filter(col => checked.includes(col));

  const form = document.getElementById('columnForm');
  const existing = form.querySelectorAll('input[name="columns[]"]');
  existing.forEach(e=>e.remove());

  orderedChecked.forEach(c => {
    const i = document.createElement('input');
    i.type='hidden';
    i.name='columns[]';
    i.value=c;
    form.appendChild(i);
  });

  form.submit();
}

/* ============================================================
   DRAG AND DROP FUNCTIONS
============================================================ */
// Use var and check if already declared to avoid conflicts with partials-table-scripts
if (typeof draggedElement === 'undefined') {
  var draggedElement = null;
}
if (typeof dragOverElement === 'undefined') {
  var dragOverElement = null;
}

function initDragAndDrop() {
  const columnSelection = document.getElementById('columnSelection');
  if (!columnSelection) return;
  const columnItems = columnSelection.querySelectorAll('.column-item, .column-item-vertical');
  columnItems.forEach(item => {
    if (item.dataset.dragInitialized === 'true') return;
    item.dataset.dragInitialized = 'true';
    item.setAttribute('draggable', 'true');

    const checkbox = item.querySelector('.column-checkbox');
    if (checkbox) {
      checkbox.addEventListener('mousedown', function(e) { e.stopPropagation(); });
      checkbox.addEventListener('click', function(e) { e.stopPropagation(); });
    }

    const label = item.querySelector('label');
    if (label) {
      label.addEventListener('mousedown', function(e) {
        if (e.target === label) e.preventDefault();
      });
    }

    item.addEventListener('dragstart', function(e) {
      draggedElement = this;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/html', this.outerHTML);
      e.dataTransfer.setData('text/plain', this.querySelector('.column-checkbox').value);
    });

    item.addEventListener('dragend', function(e) {
      this.classList.remove('dragging');
      columnItems.forEach(i => i.classList.remove('drag-over'));
      if (dragOverElement) {
        dragOverElement.classList.remove('drag-over');
        dragOverElement = null;
      }
      draggedElement = null;
    });

    item.addEventListener('dragover', function(e) {
      e.preventDefault();
      e.stopPropagation();
      e.dataTransfer.dropEffect = 'move';

      if (draggedElement && this !== draggedElement) {
        if (dragOverElement && dragOverElement !== this) {
          dragOverElement.classList.remove('drag-over');
        }
        this.classList.add('drag-over');
        dragOverElement = this;

        const rect = this.getBoundingClientRect();
        const midpoint = rect.top + (rect.height / 2);
        const next = e.clientY > midpoint;

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

    item.addEventListener('dragenter', function(e) {
      e.preventDefault();
      if (draggedElement && this !== draggedElement) {
        this.classList.add('drag-over');
      }
    });

    item.addEventListener('dragleave', function(e) {
      if (!this.contains(e.relatedTarget)) {
        this.classList.remove('drag-over');
        if (dragOverElement === this) dragOverElement = null;
      }
    });

    item.addEventListener('drop', function(e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.remove('drag-over');
      dragOverElement = null;
      return false;
    });
  });
}

// document.getElementById('editStatementFromPageBtn')
//   ?.addEventListener('click', () => currentStatementId && openEditStatement(currentStatementId));
function filterByInsurer(insurers = null) {

  const url = new URL(window.location);

  if (insurers) {
    url.searchParams.set('insurer', insurers);
  } else {
    url.searchParams.delete('insurer');
  }

  window.location.href = url.toString();
}

// Direct event listener attachment (script loads after DOM)
(function() {
  const columnBtn = document.getElementById('columnBtn');
  if (columnBtn) {
    columnBtn.onclick = function() {
      openColumnModal();
    };
  }

  const addBtn = document.getElementById('addStatementBtn');
  if (addBtn) {
    addBtn.onclick = function() {
      openAddForm();
    };
  }
})();