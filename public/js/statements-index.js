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
function populateStatementDetails(s) {
  const el = document.getElementById('statementDetailsContent');

  el.innerHTML = `
    <div class="detail-section">
      <div class="detail-section-header">STATEMENT DETAILS</div>
      <div class="detail-section-body">
        <div class="detail-row"><span class="detail-label">Statement No</span><div>${s.statement_no || '-'}</div></div>
        <div class="detail-row"><span class="detail-label">Year</span><div>${s.year || '-'}</div></div>
        <div class="detail-row"><span class="detail-label">Insurer</span><div>${s.insurer?.name || '-'}</div></div>
        <div class="detail-row"><span class="detail-label">Business Category</span><div>${s.business_category || '-'}</div></div>
      </div>
    </div>

    <div class="detail-section">
      <div class="detail-section-header">PAYMENT INFO</div>
      <div class="detail-section-body">
        <div class="detail-row"><span class="detail-label">Date Received</span><div>${formatDate(s.date_received)}</div></div>
        <div class="detail-row"><span class="detail-label">Amount</span><div>${formatNumber(s.amount_received)}</div></div>
        <div class="detail-row"><span class="detail-label">Payment Mode</span><div>${s.mode_of_payment?.name || '-'}</div></div>
      </div>
    </div>

    <div class="detail-section">
      <div class="detail-section-header">REMARKS</div>
      <div class="detail-section-body">
        <textarea readonly style="width:100%;min-height:80px;">${s.remarks || ''}</textarea>
      </div>
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
