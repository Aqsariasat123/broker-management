// public/js/vehicles-index.js
// FINAL WORKING VERSION - Copy & Paste Exactly
// public/js/vehicles-index.js
// Fully external - works perfectly with Laravel data

// Access Blade-passed data
const app = window.vehiclesApp || {};
let currentVehicleId = app.currentVehicleId || null;
const selectedColumns = app.selectedColumns || [];
const vehiclesStoreRoute = app.routes?.store || '/vehicles';
const csrfToken = app.csrfToken || '';

// Helper functions
function formatDate(dateStr) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
}

function formatNumber(num) {
  if (!num && num !== 0) return '-';
  return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Print Table
function printTable() {
  const table = document.getElementById('vehiclesTable');
  if (!table) return;

  const headers = [];
  table.querySelectorAll('thead th').forEach(th => {
    let text = '';
    const clone = th.cloneNode(true);
    clone.querySelector('.column-filter')?.remove();
    text = clone.textContent.trim();
    if (clone.querySelector('svg')) text = '🔔';
    if (text) headers.push(text);
  });

  const rows = [];
  table.querySelectorAll('tbody tr').forEach(row => {
    if (row.style.display === 'none') return;
    const cells = [];
    row.querySelectorAll('td').forEach(cell => {
      let content = cell.querySelector('a')?.textContent.trim() || cell.textContent.trim();
      cells.push(content || '-');
    });
    rows.push(cells);
  });

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  const headersHTML = headers.map(h => `<th>${escapeHtml(h)}</th>`).join('');
  const rowsHTML = rows.map(row => `<tr>${row.map(cell => `<td>${escapeHtml(cell)}</td>`).join('')}</tr>`).join('');

  const printWin = window.open('', '_blank');
  printWin.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Vehicles Print</title>
      <style>
        @page { margin: 1cm; size: A4 landscape; }
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #000; color: #fff; padding: 8px; border: 1px solid #333; }
        td { padding: 6px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f8f8f8; }
      </style>
    </head>
    <body>
      <table><thead><tr>${headersHTML}</tr></thead><tbody>${rowsHTML}</tbody></table>
      <script>
        setTimeout(() => window.print(), 200);
        window.onafterprint = () => window.close();
      </script>
    </body>
    </html>
  `);
  printWin.document.close();
}

// Filter functions
function toggleFilter() {
  const toggle = document.getElementById('filterToggle');
  const table = document.getElementById('vehiclesTable');
  if (!toggle || !table) return;

  table.querySelectorAll('thead th').forEach((th, i) => {
    if (i <= 1) return;
    let input = th.querySelector('.column-filter');
    if (toggle.checked) {
      if (!input) {
        input = document.createElement('input');
        input.type = 'text';
        input.className = 'column-filter';
        input.placeholder = 'Filter...';
        input.style.cssText = 'width:100%; padding:4px; margin-top:4px; border:1px solid #ddd; border-radius:2px; font-size:12px;';
        input.addEventListener('input', filterTable);
        th.appendChild(input);
      }
      input.style.display = 'block';
    } else {
      if (input) {
        input.style.display = 'none';
        input.value = '';
        filterTable();
      }
    }
  });
}

function filterTable() {
  const table = document.getElementById('vehiclesTable');
  if (!table) return;

  const rows = table.querySelectorAll('tbody tr');
  const headers = table.querySelectorAll('thead th');

  rows.forEach(row => {
    let visible = true;
    const cells = row.querySelectorAll('td');
    headers.forEach((th, i) => {
      if (i <= 1) return;
      const input = th.querySelector('.column-filter');
      if (input && input.value) {
        const cellText = cells[i]?.textContent.trim().toLowerCase() || '';
        if (!cellText.includes(input.value.toLowerCase())) visible = false;
      }
    });
    row.style.display = visible ? '' : 'none';
  });
}

// Full-page details
async function openVehicleDetails(id) {
  try {
    const res = await fetch(`/vehicles/${id}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error('Failed');
    const vehicle = await res.json();
    currentVehicleId = id;

    document.getElementById('vehiclePageName').textContent = vehicle.regn_no || vehicle.vehicle_id || 'Unknown';
    document.getElementById('vehiclePageTitle').textContent = 'Vehicle';

    populateVehicleDetails(vehicle);

    document.getElementById('clientsTableView').classList.add('hidden');
    const pageView = document.getElementById('vehiclePageView');
    pageView.style.display = 'block';
    pageView.classList.add('show');
    document.getElementById('vehicleDetailsPageContent').style.display = 'block';
    document.getElementById('editVehicleFromPageBtn').style.display = 'inline-block';
    document.getElementById('closeVehiclePageBtn').style.display = 'inline-block';
  } catch (e) {
    alert('Error loading vehicle');
  }
}

function populateVehicleDetails(vehicle) {
  const content = document.getElementById('vehicleDetailsContent');
  if (!content) return;

  content.innerHTML = `
    <div class="detail-section"><div class="detail-section-header">VEHICLE BASIC INFO</div><div class="detail-section-body">
      <div class="detail-row"><span class="detail-label">Registration No</span><div class="detail-value">${vehicle.regn_no || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Make</span><div class="detail-value">${vehicle.make || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Model</span><div class="detail-value">${vehicle.model || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Type</span><div class="detail-value">${vehicle.type || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Usage</span><div class="detail-value">${vehicle.useage || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Year</span><div class="detail-value">${vehicle.year || '-'}</div></div>
    </div></div>

    <div class="detail-section"><div class="detail-section-header">VEHICLE DETAILS</div><div class="detail-section-body">
      <div class="detail-row"><span class="detail-label">Value</span><div class="detail-value">${formatNumber(vehicle.value)}</div></div>
      <div class="detail-row"><span class="detail-label">Policy ID</span><div class="detail-value">${vehicle.policy_id || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Engine</span><div class="detail-value">${vehicle.engine || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Engine Type</span><div class="detail-value">${vehicle.engine_type || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">CC</span><div class="detail-value">${vehicle.cc || '-'}</div></div>
    </div></div>

    <div class="detail-section"><div class="detail-section-header">IDENTIFICATION</div><div class="detail-section-body">
      <div class="detail-row"><span class="detail-label">Engine No</span><div class="detail-value">${vehicle.engine_no || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Chassis No</span><div class="detail-value">${vehicle.chassis_no || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">Vehicle ID</span><div class="detail-value">${vehicle.vehicle_id || '-'}</div></div>
      <div class="detail-row"><span class="detail-label">From</span><div class="detail-value">${formatDate(vehicle.from)}</div></div>
      <div class="detail-row"><span class="detail-label">To</span><div class="detail-value">${formatDate(vehicle.to)}</div></div>
    </div></div>

    <div class="detail-section"><div class="detail-section-header">NOTES</div><div class="detail-section-body">
      <div class="detail-row" style="align-items:flex-start;">
        <span class="detail-label">Notes</span>
        <textarea class="detail-value" readonly style="min-height:200px; resize:vertical; flex:1; padding:6px;">${vehicle.notes || ''}</textarea>
      </div>
    </div></div>
  `;
}
function closeVehiclePageView() {
  document.getElementById('vehiclePageView').style.display = 'none';
  document.getElementById('clientsTableView').classList.remove('hidden');
  document.getElementById('vehicleDetailsPageContent').style.display = 'none';
  currentVehicleId = null;
}


// Modal functions
function openAddVehicleModal() {
  document.getElementById('vehicleModalTitle').textContent = 'Add Vehicle';
  const form = document.getElementById('vehicleForm');
  form.action = vehiclesStoreRoute;
  form.reset();
  document.getElementById('vehicleFormMethod').innerHTML = '';
  document.querySelector('#vehicleModal #vehicleDeleteBtn').style.display = 'none';
  document.getElementById('vehicleModal').classList.add('show');
}


async function openEditVehicleModal(id) {
  try {
    const res = await fetch(`/vehicles/${id}/edit`, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error();
    const vehicle = await res.json();
    currentVehicleId = id;

    const modal = document.getElementById('vehicleModal');
    document.getElementById('vehicleModalTitle').textContent = 'Edit Vehicle';
    const form = document.getElementById('vehicleForm');
    form.action = `/vehicles/${id}`;
    document.getElementById('vehicleFormMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.querySelector('#vehicleModal #vehicleDeleteBtn').style.display = 'inline-block';

    ['regn_no','make','model','type','useage','year','value','policy_id','engine','engine_type','cc','engine_no','chassis_no','from','to','notes'].forEach(key => {
      const el = document.getElementById(key);
      if (el) {
        let val = vehicle[key] ?? '';
        if (el.type === 'date' && val) val = val.substring(0,10);
        el.value = val;
      }
    });

    modal.classList.add('show');
  } catch (e) {
    alert('Error loading data');
  }
}

function closeVehicleModal() {
  document.getElementById('vehicleModal').classList.remove('show');
  currentVehicleId = null;
}

function deleteVehicle() {
  if (!currentVehicleId || !confirm('Delete this vehicle?')) return;
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/vehicles/${currentVehicleId}`;
  form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">
                   <input type="hidden" name="_method" value="DELETE">`;
  document.body.appendChild(form);
  form.submit();
}
// DOM Ready
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('addVehicleBtn')?.addEventListener('click', openAddVehicleModal);
  document.getElementById('editVehicleFromPageBtn')?.addEventListener('click', () => {
    if (currentVehicleId) {
      openEditVehicleModal(currentVehicleId);
      closeVehiclePageView();
    }
  });
  document.getElementById('closeVehiclePageBtn')?.addEventListener('click', closeVehiclePageView);
  document.querySelectorAll('#vehicleModal .modal-close, #vehicleModal .btn-cancel').forEach(btn => {
    btn.addEventListener('click', closeVehicleModal);
  });
  document.getElementById('vehicleModal')?.addEventListener('click', e => {
    if (e.target.id === 'vehicleModal') closeVehicleModal();
  });
  document.getElementById('printBtn')?.addEventListener('click', printTable);
});


