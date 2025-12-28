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
    document.getElementById('editStatementFromPageBtn').style.display = 'inline-block';

  } catch (e) {
    alert('Error loading statement');
    console.error(e);
  }
}

/* ============================================================
   DETAILS HTML
============================================================ */
function populateStatementDetails() {
  const el = document.getElementById('statementDetailsContent');

  el.innerHTML = `
    <div class="statement-container">
      <!-- Summary Title -->
      <div class="summary-title">Commission Statement Summary</div>

      <!-- Summary Bar - Exact match to your screenshot -->
      <div class="summary-bar">
        <div class="summary-item">
          <span class="summary-label">Insurer</span>
          <input type="text" class="summary-input" value="Alliance" readonly>
        </div>

        <div class="summary-item">
          <span class="summary-label">Class</span>
          <input type="text" class="summary-input" value="General" readonly>
        </div>

        <div class="summary-item">
          <span class="summary-label">Total</span>
          <div class="summary-input">2074.89</div>
        </div>

        <div class="summary-item">
          <span class="summary-label">Date Received</span>
          <div class="summary-input">28-Sep-24</div>
        </div>

        <div class="summary-item">
          <span class="summary-label">Mode</span>
          <input type="text" class="summary-input" value="Bank Transfer" readonly>
        </div>

        <div class="summary-item">
          <span class="summary-label">Chq No</span>
          <input type="text" class="summary-input" value="NOU0000000" readonly>
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
          <tr>
            <td>CN23061</td>
            <td>MPV-23-HEA-P0002110</td>
            <td>Kendra Moller</td>
            <td>7,356.40</td>
            <td>10.0%</td>
            <td>735.64</td>
            <td>735.64</td>
            <td>0.00</td>
            <td>-</td>
          </tr>
          <tr>
            <td>CN23059</td>
            <td>FSP-23-HEA-P0002309</td>
            <td>Pierro Leclerc</td>
            <td>8,441.00</td>
            <td>15.0%</td>
            <td>1,266.15</td>
            <td>633.08</td>
            <td>633.08</td>
            <td>-</td>
          </tr>
          <tr>
            <td>CN23055</td>
            <td>HS1-23-HIN-0000088</td>
            <td>Margo Slater</td>
            <td>4,793.50</td>
            <td>15.0%</td>
            <td>719.03</td>
            <td>719.03</td>
            <td>0.00</td>
            <td class="variance-reason-cell">Part commission on instalment</td>
          </tr>
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

  document.getElementById('statementPageTitle').textContent =
    mode === 'add' ? 'Add Statement' : 'Edit Statement';

  document.getElementById('statementPageName').textContent =
    s?.statement_no || '';

  // Clone modal form body
  const modalBody = document.querySelector('#statementModal .modal-body');
  const target = document.querySelector('#statementPageForm > div[style*="padding"]');
  target.innerHTML = modalBody.innerHTML;

  const form = document.getElementById('statementPageForm');
  const method = document.getElementById('statementPageFormMethod');
  method.innerHTML = '';

  if (mode === 'edit') {
    form.action = `/statements/${currentStatementId}`;
    method.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
    document.getElementById('statementDeleteBtn').style.display = 'inline-block';

    ['year','insurer_id','business_category','date_received','amount_received','mode_of_payment_id','remarks']
      .forEach(f => {
        const el = form.querySelector(`#${f}`);
        if (el) el.value = s[f] ?? '';
      });

  } else {
    form.action = statementsStoreRoute;
    document.getElementById('statementDeleteBtn').style.display = 'none';
    form.reset();
  }

  showPageView();
  document.getElementById('statementDetailsPageContent').style.display = 'none';
  document.getElementById('statementFormPageContent').style.display = 'block';
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

/* ============================================================
   EVENTS
============================================================ */
document.getElementById('addStatementBtn')
  ?.addEventListener('click', openAddForm);

document.getElementById('editStatementFromPageBtn')
  ?.addEventListener('click', () => currentStatementId && openEditStatement(currentStatementId));
