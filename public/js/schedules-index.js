
let currentScheduleId = null;

function openScheduleModal(mode, scheduleId = null) {
  const modal = document.getElementById('scheduleModal');
  const form = document.getElementById('scheduleForm');
  const formMethod = document.getElementById('scheduleFormMethod');
  const modalTitle = document.getElementById('scheduleModalTitle');

  currentScheduleId = scheduleId;

  if (mode === 'add') {
    modalTitle.textContent = 'Add Schedule';
    form.reset();
    form.action = schedulesStoreRoute;
    formMethod.innerHTML = '';
    currentScheduleId = null;
  } else if (mode === 'edit' && scheduleId) {
    modalTitle.textContent = 'Edit Schedule';
    form.action = schedulesUpdateRouteTemplate.replace(':id', scheduleId);
    formMethod.innerHTML = '@method("PUT")';

    // Fetch schedule data
    fetch(`/schedules/${scheduleId}/edit`)
      .then(response => response.json())
      .then(data => {
        if (data.schedule) {
          const s = data.schedule;
          document.getElementById('policy_id').value = s.policy_id || '';
          document.getElementById('schedule_no').value = s.schedule_no || '';
          document.getElementById('status').value = s.status || 'draft';
          document.getElementById('issued_on').value = s.issued_on ? s.issued_on.split('T')[0] : '';
          document.getElementById('effective_from').value = s.effective_from ? s.effective_from.split('T')[0] : '';
          document.getElementById('effective_to').value = s.effective_to ? s.effective_to.split('T')[0] : '';
          document.getElementById('notes').value = s.notes || '';
        }
      })
      .catch(error => {
        console.error('Error fetching schedule data:', error);
        alert('Error loading schedule data');
      });
  }

  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeScheduleModal() {
  const modal = document.getElementById('scheduleModal');
  modal.classList.remove('show');
  document.body.style.overflow = '';
  const form = document.getElementById('scheduleForm');
  form.reset();
  currentScheduleId = null;
}

// Close modal on outside click
document.getElementById('scheduleModal').addEventListener('click', function (e) {
  if (e.target === this) {
    closeScheduleModal();
  }
});

// Close modal on ESC key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeScheduleModal();
  }
});

// Handle form submission
document.getElementById('scheduleForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = this;
  const formData = new FormData(form);
  const url = form.action;
  const method = form.querySelector('[name="_method"]') ? form.querySelector('[name="_method"]').value : 'POST';

  // Add method override if needed
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
        closeScheduleModal();
        window.location.reload();
      } else {
        alert(data.message || 'Error saving schedule');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving schedule');
    });
});
// Filter modal handling (safe guards in case elements are missing)
const filterToggle = document.getElementById('filterToggle');
const filterModal = document.getElementById('filterModal');

function openFilterModal() {
  if (!filterModal) return;
  filterModal.classList.add('show');
  document.body.style.overflow = 'hidden';
  if (filterToggle) filterToggle.checked = true;
}

function closeFilterModal() {
  if (!filterModal) return;
  filterModal.classList.remove('show');
  document.body.style.overflow = '';
  if (filterToggle) filterToggle.checked = false;
}

// Expose to global so inline onclick handlers in Blade can call them
window.openFilterModal = openFilterModal;
window.closeFilterModal = closeFilterModal;

if (filterToggle) {
  filterToggle.addEventListener('change', function () {
    if (this.checked) openFilterModal();
    else closeFilterModal();
  });
}

if (filterModal) {
  // Close when clicking outside modal content
  filterModal.addEventListener('click', function (e) {
    if (e.target === this) closeFilterModal();
  });

  // Close on ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeFilterModal();
  });

  // Programmatic apply (submits the GET form so browser navigates with query string)
  function applyFilters() {
    const form = document.getElementById('filterForm');
    if (!form) return;
    form.submit();
  }

  window.applyFilters = applyFilters;
}

// Filter policy options when client is selected
document.addEventListener('DOMContentLoaded', function () {
  const clientSelect = document.getElementById('client_id');
  const policySelect = document.getElementById('filter_policy_id');
  if (!clientSelect || !policySelect) return;

  // Cache all policy options
  const allPolicyOptions = Array.from(policySelect.options).map(opt => ({
    value: opt.value,
    text: opt.text,
    clientId: opt.getAttribute('data-client-id') || ''
  }));

  function populatePolicies(clientId) {
    // Clear
    policySelect.innerHTML = '';
    const emptyOpt = document.createElement('option');
    emptyOpt.value = '';
    emptyOpt.text = 'All';
    policySelect.appendChild(emptyOpt);

    allPolicyOptions.forEach(o => {
      if (!clientId || String(o.clientId) === String(clientId)) {
        const opt = document.createElement('option');
        opt.value = o.value;
        opt.text = o.text;
        policySelect.appendChild(opt);
      }
    });
  }

  // Initialize according to current selection
  populatePolicies(clientSelect.value);

  clientSelect.addEventListener('change', function () {
    populatePolicies(this.value);
  });
});