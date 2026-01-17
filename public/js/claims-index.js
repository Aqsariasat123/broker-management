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

// Open Claim Modal - MUST be defined before event listeners
async function openClaimModal(mode, claimId = null) {
  console.log('openClaimModal called with mode:', mode, 'claimId:', claimId);
  const modal = document.getElementById('claimModal');
  if (!modal) {
    console.error('Modal element not found');
    alert('Error: Modal element not found');
    return;
  }
  console.log('Modal element found:', modal);

  const form = document.getElementById('claimForm');
  const formMethod = document.getElementById('claimFormMethod');
  const title = document.getElementById('claimModalTitle');
  const deleteBtn = document.getElementById('claimDeleteBtnModal');
  const uploadBtn = document.getElementById('claimUploadBtnModal');

  if (!form || !formMethod || !title) {
    console.error('Required form elements not found', { form, formMethod, title });
    alert('Error: Form elements not found');
    return;
  }

  if (mode === 'add') {
    title.textContent = 'Add Claim';
    form.action = claimsStoreRoute;
    formMethod.innerHTML = '';
    if (deleteBtn) deleteBtn.style.display = 'none';
    if (uploadBtn) uploadBtn.style.display = 'none';
    form.reset();

    // Enable all fields for add mode
    const allFields = form.querySelectorAll('input, select, textarea');
    allFields.forEach(field => {
      field.removeAttribute('readonly');
      field.removeAttribute('disabled');
      field.style.backgroundColor = '#fff';
      field.style.cursor = 'text';
    });
    const policyIdFromUrl = getQueryParam('policy_id');
    const policySelect = document.getElementById('policy_id');

    if (policyIdFromUrl && policySelect) {
      policySelect.value = policyIdFromUrl;
      policySelect.setAttribute('disabled', 'disabled');

      // 🔥 Important: ensure disabled value is submitted
      let hiddenPolicy = document.getElementById('hidden_policy_id');
      if (!hiddenPolicy) {
        hiddenPolicy = document.createElement('input');
        hiddenPolicy.type = 'hidden';
        hiddenPolicy.name = 'policy_id';
        hiddenPolicy.id = 'hidden_policy_id';
        form.appendChild(hiddenPolicy);
      }
      hiddenPolicy.value = policyIdFromUrl;
    }
    currentClaimId = null;
  } else if (mode === 'edit' && claimId) {
    try {
      const res = await fetch(`/claims/${claimId}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const claim = await res.json();
      currentClaimId = claimId;

      console.log('Fetched claim data:', claim);
      title.textContent = 'View/Edit Claim';
      form.action = `/claims/${claimId}`;
      formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
      if (deleteBtn) deleteBtn.style.display = 'inline-block';
      if (uploadBtn) uploadBtn.style.display = 'inline-block';

      // Enable all fields for edit mode
      const allFields = form.querySelectorAll('input, select, textarea');
      allFields.forEach(field => {
        field.removeAttribute('readonly');
        field.removeAttribute('disabled');
        field.style.backgroundColor = '#fff';
        field.style.cursor = 'text';
      });

      // Populate form fields
      const policyIdField = document.getElementById('policy_id');
      if (policyIdField) {
        policyIdField.value = claim.policy_id || '';
      }
      document.getElementById('loss_date').value = claim.loss_date ? (typeof claim.loss_date === 'string' ? claim.loss_date.substring(0, 10) : claim.loss_date) : '';
      document.getElementById('claim_date').value = claim.claim_date ? (typeof claim.claim_date === 'string' ? claim.claim_date.substring(0, 10) : claim.claim_date) : '';
      document.getElementById('claim_amount').value = claim.claim_amount || '';
      if (document.getElementById('claim_stage')) {
        document.getElementById('claim_stage').value = claim.claim_stage || claim.status || '';
      }
      document.getElementById('status').value = claim.status || '';
      document.getElementById('close_date').value = claim.close_date ? (typeof claim.close_date === 'string' ? claim.close_date.substring(0, 10) : claim.close_date) : '';
      document.getElementById('paid_amount').value = claim.paid_amount || '';
      document.getElementById('claim_summary').value = claim.claim_summary || '';
    } catch (e) {
      console.error(e);
      alert('Error loading claim data');
      return;
    }
  }

  // Show the modal
  console.log('Adding show class to modal');
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
  console.log('Modal classes:', modal.className);
  console.log('Modal display style:', window.getComputedStyle(modal).display);
}
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}
function closeClaimModal() {
  const modal = document.getElementById('claimModal');
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
    currentClaimId = null;
  }
}


// Event listeners - wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function () {
  // Add Claim Button - Open Modal
  const addBtn = document.getElementById('addClaimBtn');
  if (addBtn) {
    addBtn.addEventListener('click', () => openClaimModal('add'));
  }

  const columnBtn = document.getElementById('columnBtn2');
  if (columnBtn) {
    columnBtn.addEventListener('click', () => openColumnModal());
  }

  // Filter toggle handler
  const filterToggle = document.getElementById('filterToggle');
  if (filterToggle) {
    const urlParams = new URLSearchParams(window.location.search);
    const hasPending = urlParams.get('pending') === 'true' || urlParams.get('pending') === '1';
    filterToggle.checked = hasPending;

    filterToggle.addEventListener('change', function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (!this.checked) {
        // Clear filter when toggle is unchecked
        const u = new URL(window.location.href);
        u.searchParams.delete('pending');
        window.location.href = u.toString();
      } else {
        // Activate pending filter
        const u = new URL(window.location.href);
        u.searchParams.set('pending', '1');
        window.location.href = u.toString();
      }
    });
  }

  // Show Pending button handler
  const showPendingBtn = document.getElementById('showPendingBtn');
  if (showPendingBtn) {
    showPendingBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const u = new URL(window.location.href);
      u.searchParams.set('pending', '1');
      window.location.href = u.toString();
    });
  }

  // List ALL button handler
  const listAllBtn = document.getElementById('listAllBtn');
  if (listAllBtn) {
    listAllBtn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const u = new URL(window.location.href);
      u.searchParams.delete('pending');
      window.location.href = u.toString();
    });
  }

  // Close modal on backdrop click
  const claimModal = document.getElementById('claimModal');
  if (claimModal) {
    claimModal.addEventListener('click', function (e) {
      if (e.target === this) {
        closeClaimModal();
      }
    });
  }

  // Close modal on ESC key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeClaimModal();
    }
  });
});


function deleteClaim() {
  if (!currentClaimId) return;
  if (!confirm('Delete this claim?')) return;
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/claims/${currentClaimId}`;
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

// Document Upload Modal
function openDocumentUploadModal() {
  if (!currentClaimId) {
    alert('Please save the claim first before uploading documents');
    return;
  }
  const modal = document.getElementById('claimDocumentUploadModal');
  if (modal) {
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

function closeDocumentUploadModal() {
  const modal = document.getElementById('claimDocumentUploadModal');
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.getElementById('claimDocumentUploadForm').reset();
  }
}

async function uploadDocument(event) {
  event.preventDefault();
  if (!currentClaimId) {
    alert('No claim selected');
    return;
  }

  const form = document.getElementById('claimDocumentUploadForm');
  const formData = new FormData(form);
  formData.append('claim_id', currentClaimId);

  try {
    const response = await fetch('/claims/upload-document', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      alert('Document uploaded successfully!');
      closeDocumentUploadModal();
    } else {
      alert('Error uploading document: ' + (result.message || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error uploading document: ' + error.message);
  }
}
