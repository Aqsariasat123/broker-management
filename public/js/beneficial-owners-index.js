// Data initialized in Blade template

// Helper function for date formatting
function formatDate(dateStr) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
}

// Open beneficial owner details modal - MUST be defined before event listeners
async function openBeneficialOwnerDetails(id) {
  try {
    const res = await fetch(`/beneficial-owners/${id}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const bo = await res.json();
    currentBeneficialOwnerId = id;
    
    // Get modal elements
    const detailModal = document.getElementById('beneficialOwnerDetailModal');
    const detailModalTitle = document.getElementById('beneficialOwnerDetailModalTitle');
    const detailModalContent = document.getElementById('beneficialOwnerDetailModalContent');
    const editBtn = document.getElementById('editBeneficialOwnerFromDetailModalBtn');
    
    if (!detailModal) {
      console.error('Detail modal element not found');
      alert('Error: Detail modal not found');
      return;
    }
    
    if (!detailModalTitle || !detailModalContent) {
      console.error('Required modal elements not found');
      alert('Error: Modal elements not found');
      return;
    }
    
    // Set beneficial owner name in header
    const boName = bo.full_name || 'Unknown';
    detailModalTitle.textContent = `Beneficial Owner - ${boName}`;
    
    // Populate detail content
    populateBeneficialOwnerDetailModal(bo, detailModalContent);
    
    // Show edit button
    if (editBtn) {
      editBtn.style.display = 'inline-block';
      editBtn.onclick = () => {
        closeBeneficialOwnerDetailModal();
        openBeneficialOwnerModal('edit', id);
      };
    }
    
    // Show the modal - ensure it's visible
    detailModal.classList.add('show');
    detailModal.style.display = 'flex';
    detailModal.style.zIndex = '10000';
    document.body.style.overflow = 'hidden';
    
    console.log('Modal should be visible now', detailModal);
  } catch (e) {
    console.error(e);
    alert('Error loading beneficial owner details: ' + e.message);
  }
}

// Close beneficial owner detail modal
function closeBeneficialOwnerDetailModal() {
  const detailModal = document.getElementById('beneficialOwnerDetailModal');
  if (detailModal) {
    detailModal.classList.remove('show');
    detailModal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

// Populate beneficial owner detail modal content (matching add dialog design)
function populateBeneficialOwnerDetailModal(bo, content) {
  if (!content) return;

  const html = `
    <div class="form-row" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:15px;">
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Full Name *</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${bo.full_name || '-'}</div>
      </div>
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">DOB</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${formatDate(bo.dob)}</div>
      </div>
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">NIN/Passport No</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${bo.nin_passport_no || '-'}</div>
      </div>
    </div>
    <div class="form-row" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:15px;">
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Country *</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${bo.country || '-'}</div>
      </div>
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Expiry Date</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${formatDate(bo.expiry_date)}</div>
      </div>
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Status *</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${bo.status || '-'}</div>
      </div>
    </div>
    <div class="form-row" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:15px;">
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Position *</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${bo.position || '-'}</div>
      </div>
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Shares (%)</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${bo.shares ? bo.shares + '%' : '-'}</div>
      </div>
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">PEP</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${bo.pep ? 'Y' : 'N'}</div>
      </div>
    </div>
    <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
      <div class="form-group" style="flex:1;">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">PEP Details</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:60px; display:flex; align-items:flex-start;">${bo.pep_details || '-'}</div>
      </div>
    </div>
    <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
      <div class="form-group">
        <label style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Date Added</label>
        <div style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; background:#f9f9f9; min-height:36px; display:flex; align-items:center;">${formatDate(bo.date_added)}</div>
      </div>
    </div>
  `;

  content.innerHTML = html;
}

// Populate beneficial owner details view
function populateBeneficialOwnerDetails(bo) {
  const content = document.getElementById('beneficialOwnerDetailsContent');
  if (!content) return;

  const col1 = `
    <div class="detail-section">
      <div class="detail-section-header">PERSONAL INFO</div>
      <div class="detail-section-body">
        <div class="detail-row">
          <span class="detail-label">Full Name</span>
          <div class="detail-value">${bo.full_name || '-'}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">DOB</span>
          <div class="detail-value">${formatDate(bo.dob)}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">Age</span>
          <div class="detail-value">${bo.age || '-'}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">NIN/Passport No</span>
          <div class="detail-value">${bo.nin_passport_no || '-'}</div>
        </div>
      </div>
    </div>
  `;

  const col2 = `
    <div class="detail-section">
      <div class="detail-section-header">LOCATION & STATUS</div>
      <div class="detail-section-body">
        <div class="detail-row">
          <span class="detail-label">Country</span>
          <div class="detail-value">${bo.country || '-'}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">Expiry Date</span>
          <div class="detail-value">${formatDate(bo.expiry_date)}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">Status</span>
          <div class="detail-value">${bo.status || '-'}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">Date Added</span>
          <div class="detail-value">${formatDate(bo.date_added)}</div>
        </div>
      </div>
    </div>
  `;

  const col3 = `
    <div class="detail-section">
      <div class="detail-section-header">OWNERSHIP</div>
      <div class="detail-section-body">
        <div class="detail-row">
          <span class="detail-label">Position</span>
          <div class="detail-value">${bo.position || '-'}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">Shares</span>
          <div class="detail-value">${bo.shares ? bo.shares + '%' : '-'}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">PEP</span>
          <div class="detail-value">${bo.pep ? 'Y' : 'N'}</div>
        </div>
        <div class="detail-row">
          <span class="detail-label">PEP Details</span>
          <div class="detail-value">${bo.pep_details || '-'}</div>
        </div>
      </div>
    </div>
  `;

  const col4 = `
  `;

  content.innerHTML = col1 + col2 + col3 + col4;
}

// Open beneficial owner page (Add or Edit)
async function openBeneficialOwnerPage(mode) {
  if (mode === 'add') {
    openBeneficialOwnerForm('add');
  } else {
    if (currentBeneficialOwnerId) {
      openEditBeneficialOwner(currentBeneficialOwnerId);
    }
  }
}

// Add Beneficial Owner Button - Open Modal
document.getElementById('addBeneficialOwnerBtn')?.addEventListener('click', () => openBeneficialOwnerModal('add'));

// Column modal functions (if columnBtn exists, it's handled by table-scripts partial)
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

async function openEditBeneficialOwner(id) {
  try {
    const res = await fetch(`/beneficial-owners/${id}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!res.ok) throw new Error('Network error');
    const bo = await res.json();
    currentBeneficialOwnerId = id;
    openBeneficialOwnerForm('edit', bo);
  } catch (e) {
    console.error(e);
    alert('Error loading beneficial owner data');
  }
}

function openBeneficialOwnerForm(mode, bo = null) {
  // Clone form from modal
  const modalForm = document.getElementById('beneficialOwnerModal')?.querySelector('form');
  const pageForm = document.getElementById('beneficialOwnerPageForm');
  const formContentDiv = pageForm?.querySelector('div[style*="padding:12px"]');
  
  // Clone the modal form body
  const modalBody = modalForm?.querySelector('.modal-body');
  if (modalBody && formContentDiv) {
    formContentDiv.innerHTML = modalBody.innerHTML;
  }

  const formMethod = document.getElementById('beneficialOwnerPageFormMethod');
  const deleteBtn = document.getElementById('beneficialOwnerDeleteBtn');
  const editBtn = document.getElementById('editBeneficialOwnerFromPageBtn');
  const closeBtn = document.getElementById('closeBeneficialOwnerPageBtn');
  const closeFormBtn = document.getElementById('closeBeneficialOwnerFormBtn');

  if (mode === 'add') {
    document.getElementById('beneficialOwnerPageTitle').textContent = 'Add Beneficial Owner';
    document.getElementById('beneficialOwnerPageName').textContent = '';
    pageForm.action = beneficialOwnersStoreRoute;
    formMethod.innerHTML = '';
    deleteBtn.style.display = 'none';
    if (editBtn) editBtn.style.display = 'none';
    if (closeBtn) closeBtn.style.display = 'inline-block';
    if (closeFormBtn) closeFormBtn.style.display = 'none';
    pageForm.reset();
  } else {
    const boName = bo.full_name || 'Unknown';
    document.getElementById('beneficialOwnerPageTitle').textContent = 'Edit Beneficial Owner';
    document.getElementById('beneficialOwnerPageName').textContent = boName;
    pageForm.action = `/beneficial-owners/${currentBeneficialOwnerId}`;
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

    const fields = ['full_name','dob','nin_passport_no','country','expiry_date','status','position','shares','pep','pep_details','date_added'];
    fields.forEach(k => {
      const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
      if (!el) return;
      if (el.type === 'date') {
        el.value = bo[k] ? (typeof bo[k] === 'string' ? bo[k].substring(0,10) : bo[k]) : '';
      } else if (el.tagName === 'SELECT') {
        el.value = bo[k] ?? '';
      } else if (el.tagName === 'TEXTAREA') {
        el.value = bo[k] ?? '';
      } else {
        el.value = bo[k] ?? '';
      }
    });
    
    // Handle PEP dropdown
    const pepSelect = formContentDiv ? formContentDiv.querySelector('#pep') : null;
    if (pepSelect) {
      pepSelect.value = bo.pep ? '1' : '0';
    }
  }

  // Hide table view, show page view
  document.getElementById('clientsTableView').classList.add('hidden');
  const boPageView = document.getElementById('beneficialOwnerPageView');
  boPageView.style.display = 'block';
  boPageView.classList.add('show');
  document.getElementById('beneficialOwnerDetailsPageContent').style.display = 'none';
  document.getElementById('beneficialOwnerFormPageContent').style.display = 'block';
}

function closeBeneficialOwnerPageView() {
  const boPageView = document.getElementById('beneficialOwnerPageView');
  boPageView.classList.remove('show');
  boPageView.style.display = 'none';
  document.getElementById('clientsTableView').classList.remove('hidden');
  document.getElementById('beneficialOwnerDetailsPageContent').style.display = 'none';
  document.getElementById('beneficialOwnerFormPageContent').style.display = 'none';
  currentBeneficialOwnerId = null;
}

// Edit button from details page
const editBtn = document.getElementById('editBeneficialOwnerFromPageBtn');
if (editBtn) {
  editBtn.addEventListener('click', function() {
    if (currentBeneficialOwnerId) {
      openEditBeneficialOwner(currentBeneficialOwnerId);
    }
  });
}

// Load documents for a specific beneficial owner (table view)
function loadDocuments(ownerCode) {
  if (!ownerCode) return;
  
  fetch(`/documents?tied_to=${ownerCode}`, {
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(res => res.json())
  .then(docs => {
    const container = document.getElementById('documentsContainer');
    if (!container) return;
    
    container.innerHTML = '';
    if (docs.length === 0) {
      container.innerHTML = '<div style="color:#999; font-size:13px;">No documents found</div>';
      return;
    }
    
    docs.forEach(doc => {
      const docDiv = document.createElement('div');
      docDiv.style.cssText = 'text-align:center; padding:10px; border:1px solid #ddd; border-radius:4px; background:#f9f9f9; min-width:100px; max-width:120px;';
      
      // Determine icon based on file format
      let icon = '📄';
      if (doc.format) {
        const format = doc.format.toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(format)) icon = '🖼️';
        else if (format === 'pdf') icon = '📕';
        else if (['doc', 'docx'].includes(format)) icon = '📝';
      }
      
      docDiv.innerHTML = `
        <div style="font-size:32px; margin-bottom:5px;">${icon}</div>
        <div style="font-size:12px; color:#666; word-wrap:break-word;">${doc.name || 'Document'}</div>
      `;
      container.appendChild(docDiv);
    });
  })
  .catch(err => console.error('Error loading documents:', err));
}

// Load documents for detail modal
function loadDocumentsForDetailModal(ownerCode) {
  if (!ownerCode) return;
  
  fetch(`/documents?tied_to=${ownerCode}`, {
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(res => res.json())
  .then(docs => {
    const container = document.getElementById('documentsContainerDetailModal');
    if (!container) return;
    
    container.innerHTML = '';
    if (docs.length === 0) {
      container.innerHTML = '<div style="color:#999; font-size:13px;">No documents found</div>';
      return;
    }
    
    docs.forEach(doc => {
      const docDiv = document.createElement('div');
      docDiv.style.cssText = 'text-align:center; padding:10px; border:1px solid #ddd; border-radius:4px; background:#f9f9f9; min-width:100px; max-width:120px;';
      
      // Determine icon based on file format
      let icon = '📄';
      if (doc.format) {
        const format = doc.format.toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(format)) icon = '🖼️';
        else if (format === 'pdf') icon = '📕';
        else if (['doc', 'docx'].includes(format)) icon = '📝';
      }
      
      docDiv.innerHTML = `
        <div style="font-size:32px; margin-bottom:5px;">${icon}</div>
        <div style="font-size:12px; color:#666; word-wrap:break-word;">${doc.name || 'Document'}</div>
      `;
      container.appendChild(docDiv);
    });
  })
  .catch(err => console.error('Error loading documents:', err));
}

// Load documents when a beneficial owner is selected via radio button
function loadDocumentsForSelectedBO(radio) {
  const ownerCode = radio.getAttribute('data-owner-code');
  if (ownerCode) {
    loadDocuments(ownerCode);
  }
}

// Open beneficial owner modal
async function openBeneficialOwnerModal(mode, boId = null) {
  const modal = document.getElementById('beneficialOwnerModal');
  const form = document.getElementById('beneficialOwnerForm');
  const formMethod = document.getElementById('beneficialOwnerFormMethod');
  const title = document.getElementById('beneficialOwnerModalTitle');
  const deleteBtn = document.getElementById('beneficialOwnerDeleteBtnModal');
  
  if (!modal || !form || !formMethod || !title) {
    console.error('Required modal elements not found');
    alert('Error: Modal elements not found');
    return;
  }
  
  if (mode === 'add') {
    title.textContent = 'Add Beneficial Owner';
    form.action = beneficialOwnersStoreRoute;
    formMethod.innerHTML = '';
    if (deleteBtn) deleteBtn.style.display = 'none';
    form.reset();
    currentBeneficialOwnerId = null;
    // Reset document preview
    const previewDiv = document.getElementById('selectedDocumentPreview');
    if (previewDiv) previewDiv.style.display = 'none';
    const fileInput = document.getElementById('documentFileInput');
    if (fileInput) fileInput.value = '';
  } else if (mode === 'edit' && boId) {
    try {
      const res = await fetch(`/beneficial-owners/${boId}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const bo = await res.json();
      currentBeneficialOwnerId = boId;
      
      title.textContent = 'Edit Beneficial Owner';
      form.action = `/beneficial-owners/${boId}`;
      formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
      if (deleteBtn) deleteBtn.style.display = 'inline-block';
      
      // Populate form fields
      document.getElementById('full_name').value = bo.full_name || '';
      document.getElementById('dob').value = bo.dob ? (typeof bo.dob === 'string' ? bo.dob.substring(0,10) : bo.dob) : '';
      document.getElementById('nin_passport_no').value = bo.nin_passport_no || '';
      document.getElementById('country').value = bo.country || '';
      document.getElementById('expiry_date').value = bo.expiry_date ? (typeof bo.expiry_date === 'string' ? bo.expiry_date.substring(0,10) : bo.expiry_date) : '';
      document.getElementById('status').value = bo.status || '';
      document.getElementById('position').value = bo.position || '';
      document.getElementById('shares').value = bo.shares || '';
      document.getElementById('pep').value = bo.pep ? '1' : '0';
      document.getElementById('pep_details').value = bo.pep_details || '';
      document.getElementById('date_added').value = bo.date_added ? (typeof bo.date_added === 'string' ? bo.date_added.substring(0,10) : bo.date_added) : '';
      
      // Show existing document preview if available
      const previewDiv = document.getElementById('selectedDocumentPreview');
      const docName = document.getElementById('selectedDocumentName');
      const imagePreview = document.getElementById('selectedDocumentImagePreview');
      
      if (bo.documents && bo.documents.length > 0) {
        const document = bo.documents[0];
        if (document && document.file_path) {
          previewDiv.style.display = 'block';
          const fileName = document.file_path.split('/').pop();
          docName.textContent = document.name || fileName;
          imagePreview.innerHTML = `
            <a href="/storage/${document.file_path}" target="_blank" style="color:#007bff; text-decoration:underline; font-size:12px;">
              View Current Document
            </a>
          `;
        } else {
          previewDiv.style.display = 'none';
        }
      } else {
        previewDiv.style.display = 'none';
      }
    } catch (e) {
      console.error(e);
      alert('Error loading beneficial owner data');
      return;
    }
  }
  
  // Show the modal
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeBeneficialOwnerModal() {
  const modal = document.getElementById('beneficialOwnerModal');
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
    currentBeneficialOwnerId = null;
  }
}

// Document Upload Modal Functions
function openDocumentUploadModal() {
  const modal = document.getElementById('documentUploadModal');
  if (modal) {
    // Set higher z-index to appear above other modals
    modal.style.zIndex = '10001';
    
    // If in edit mode and beneficial owner has a document, show existing document
    if (currentBeneficialOwnerId) {
      // Fetch beneficial owner to check for existing document
      fetch(`/beneficial-owners/${currentBeneficialOwnerId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(bo => {
        const existingPreview = document.getElementById('existingDocumentPreview');
        const existingPreviewContent = document.getElementById('existingDocumentPreviewContent');
        if (bo.documents && bo.documents.length > 0) {
          const document = bo.documents[0];
          if (document && document.file_path) {
            existingPreview.style.display = 'block';
            existingPreviewContent.innerHTML = `
              <a href="/storage/${document.file_path}" target="_blank" style="color:#007bff; text-decoration:underline; font-size:12px;">
                View Current Document
              </a>
            `;
          } else {
            existingPreview.style.display = 'none';
          }
        } else {
          existingPreview.style.display = 'none';
        }
      })
      .catch(err => {
        console.error('Error loading beneficial owner:', err);
      });
    } else {
      // Add mode - no existing document
      document.getElementById('existingDocumentPreview').style.display = 'none';
    }
    
    // Reset file input
    document.getElementById('documentFile').value = '';
    document.getElementById('documentPreview').style.display = 'none';
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

function closeDocumentUploadModal() {
  const modal = document.getElementById('documentUploadModal');
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.getElementById('documentFile').value = '';
    document.getElementById('documentPreview').style.display = 'none';
  }
}

function handleDocumentFileSelect(event) {
  const file = event.target.files[0];
  if (!file) return;

  const preview = document.getElementById('documentPreview');
  const previewContent = document.getElementById('documentPreviewContent');
  
  // Show preview
  preview.style.display = 'block';
  
  // Check if it's an image
  if (file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewContent.innerHTML = `
        <img src="${e.target.result}" style="max-width:100%; max-height:300px; border:1px solid #ddd; border-radius:4px;" alt="Document preview">
        <p style="margin-top:5px; font-size:11px; color:#666;">${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>
      `;
    };
    reader.readAsDataURL(file);
  } else {
    // For PDF and other files, show file info
    previewContent.innerHTML = `
      <div style="padding:20px; text-align:center; border:1px solid #ddd; border-radius:4px; background:#f9f9f9;">
        <p style="margin:0; font-size:14px; color:#666;">📄 ${file.name}</p>
        <p style="margin:5px 0 0 0; font-size:11px; color:#999;">${(file.size / 1024).toFixed(2)} KB</p>
      </div>
    `;
  }
}

function confirmDocumentSelection() {
  const fileInput = document.getElementById('documentFile');
  const documentType = document.getElementById('documentType').value;
  
  if (!documentType) {
    alert('Please select a document type first');
    return;
  }
  
  if (!fileInput.files || !fileInput.files[0]) {
    alert('Please select a file first');
    return;
  }

  const file = fileInput.files[0];
  
  // Determine which form is active (modal or page form)
  const modalForm = document.getElementById('beneficialOwnerModal');
  const pageForm = document.getElementById('beneficialOwnerPageView');
  const isPageFormActive = pageForm && pageForm.style.display !== 'none' && pageForm.classList.contains('show');
  
  let hiddenInput, hiddenTypeInput, previewDiv, docName, imagePreview;
  
  if (isPageFormActive) {
    // Page form is active
    hiddenInput = document.getElementById('documentFileInputPage');
    const formContentDiv = document.getElementById('beneficialOwnerPageForm')?.querySelector('div[style*="padding:12px"]');
    previewDiv = formContentDiv?.querySelector('#selectedDocumentPreview');
    docName = formContentDiv?.querySelector('#selectedDocumentName');
    imagePreview = formContentDiv?.querySelector('#selectedDocumentImagePreview');
    // Create or get document type hidden input
    hiddenTypeInput = document.getElementById('documentTypeInputPage');
    if (!hiddenTypeInput) {
      hiddenTypeInput = document.createElement('input');
      hiddenTypeInput.type = 'hidden';
      hiddenTypeInput.name = 'document_type';
      hiddenTypeInput.id = 'documentTypeInputPage';
      document.getElementById('beneficialOwnerPageForm').appendChild(hiddenTypeInput);
    }
  } else {
    // Modal form is active
    hiddenInput = document.getElementById('documentFileInput');
    previewDiv = document.getElementById('selectedDocumentPreview');
    docName = document.getElementById('selectedDocumentName');
    imagePreview = document.getElementById('selectedDocumentImagePreview');
    // Create or get document type hidden input
    hiddenTypeInput = document.getElementById('documentTypeInput');
    if (!hiddenTypeInput) {
      hiddenTypeInput = document.createElement('input');
      hiddenTypeInput.type = 'hidden';
      hiddenTypeInput.name = 'document_type';
      hiddenTypeInput.id = 'documentTypeInput';
      document.getElementById('beneficialOwnerForm').appendChild(hiddenTypeInput);
    }
  }
  
  if (!hiddenInput) {
    alert('Form not found');
    return;
  }
  
  // Set document type
  if (hiddenTypeInput) {
    hiddenTypeInput.value = documentType;
  }
  
  // Copy file to hidden input in beneficial owner form
  const dataTransfer = new DataTransfer();
  dataTransfer.items.add(file);
  hiddenInput.files = dataTransfer.files;
  
  // Show preview in beneficial owner form
  if (previewDiv && docName) {
    const typeNames = {
      'id_card': 'ID Card',
      'passport': 'Passport',
      'proof_of_address': 'Proof Of Address',
      'other': 'Other Document'
    };
    docName.textContent = `${typeNames[documentType] || 'Document'}: ${file.name}`;
    previewDiv.style.display = 'block';
    
    // Show image preview if it's an image
    if (file.type.startsWith('image/') && imagePreview) {
      const reader = new FileReader();
      reader.onload = function(e) {
        imagePreview.innerHTML = `<img src="${e.target.result}" style="max-width:100%; max-height:150px; border:1px solid #ddd; border-radius:4px;" alt="Document preview">`;
      };
      reader.readAsDataURL(file);
    } else if (imagePreview) {
      imagePreview.innerHTML = '';
    }
  }
  
  // Close modal
  closeDocumentUploadModal();
}

// Document Upload Modal for Detail Modal Functions
function openDocumentUploadModalForDetail() {
  const modal = document.getElementById('documentUploadModalForDetail');
  if (modal) {
    // Reset form
    document.getElementById('documentUploadFormForDetail').reset();
    document.getElementById('documentPreviewForDetail').style.display = 'none';
    
    // Set higher z-index to appear above detail modal
    modal.style.zIndex = '10001';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

function closeDocumentUploadModalForDetail() {
  const modal = document.getElementById('documentUploadModalForDetail');
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.getElementById('documentUploadFormForDetail').reset();
    document.getElementById('documentPreviewForDetail').style.display = 'none';
  }
}

function handleDocumentFileSelectForDetail(event) {
  const file = event.target.files[0];
  if (!file) return;

  const preview = document.getElementById('documentPreviewForDetail');
  const previewContent = document.getElementById('documentPreviewContentForDetail');
  
  // Show preview
  preview.style.display = 'block';
  
  // Check if it's an image
  if (file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewContent.innerHTML = `
        <img src="${e.target.result}" style="max-width:100%; max-height:300px; border:1px solid #ddd; border-radius:4px;" alt="Document preview">
        <p style="margin-top:5px; font-size:11px; color:#666;">${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>
      `;
    };
    reader.readAsDataURL(file);
  } else {
    // For PDF and other files, show file info
    previewContent.innerHTML = `
      <div style="padding:20px; text-align:center; border:1px solid #ddd; border-radius:4px; background:#f9f9f9;">
        <p style="margin:0; font-size:14px; color:#666;">📄 ${file.name}</p>
        <p style="margin:5px 0 0 0; font-size:11px; color:#999;">${(file.size / 1024).toFixed(2)} KB</p>
      </div>
    `;
  }
}

async function uploadDocumentForDetail() {
  const documentType = document.getElementById('documentTypeForDetail').value;
  const fileInput = document.getElementById('documentFileForDetail');
  
  if (!documentType) {
    alert('Please select a document type');
    return;
  }
  
  if (!fileInput.files || !fileInput.files[0]) {
    alert('Please select a file');
    return;
  }
  
  const file = fileInput.files[0];
  
  let ownerCode;
  
  // Check if owner code is stored in modal (for table view)
  const modal = document.getElementById('documentUploadModalForDetail');
  const storedOwnerCode = modal?.getAttribute('data-owner-code');
  
  if (storedOwnerCode) {
    // Use stored owner code from table view
    ownerCode = storedOwnerCode;
    modal.removeAttribute('data-owner-code');
  } else if (currentBeneficialOwnerId) {
    // Fetch beneficial owner to get owner_code
    try {
      const boRes = await fetch(`/beneficial-owners/${currentBeneficialOwnerId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      
      if (!boRes.ok) throw new Error('Failed to load beneficial owner');
      
      const bo = await boRes.json();
      ownerCode = bo.owner_code;
    } catch (err) {
      console.error(err);
      alert('Error loading beneficial owner: ' + err.message);
      return;
    }
  } else {
    alert('No beneficial owner selected');
    return;
  }
  
  if (!ownerCode) {
    alert('Owner code not found');
    return;
  }
  
  const typeNames = {
    'id_card': 'ID Card',
    'passport': 'Passport',
    'proof_of_address': 'Proof Of Address',
    'other': 'Other Document'
  };
  
  try {
    const formData = new FormData();
    formData.append('file', file); // DocumentController expects 'file' not 'document'
    formData.append('tied_to', ownerCode);
    formData.append('group', 'Beneficial Owner Document');
    formData.append('name', typeNames[documentType] || 'Document');
    formData.append('type', documentType);
    
    const res = await fetch('/documents', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData
    });
    
    const result = await res.json();
    
    if (result.success) {
      alert('Document uploaded successfully!');
      closeDocumentUploadModalForDetail();
      
      // Reload beneficial owner to get updated documents
      if (currentBeneficialOwnerId) {
        const updatedBoRes = await fetch(`/beneficial-owners/${currentBeneficialOwnerId}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const updatedBo = await updatedBoRes.json();
        
        // Update detail modal content if it's open
        const detailModalContent = document.getElementById('beneficialOwnerDetailModalContent');
        if (detailModalContent) {
          populateBeneficialOwnerDetailModal(updatedBo, detailModalContent);
        }
      }
      
      // Reload documents in table view
      loadDocuments(ownerCode);
      
      // Reload documents in detail modal if container exists
      loadDocumentsForDetailModal(ownerCode);
    } else {
      alert('Error uploading document: ' + (result.message || 'Unknown error'));
    }
  } catch (err) {
    console.error(err);
    alert('Error uploading document: ' + err.message);
  }
}

// Remove selected document
function removeSelectedDocument() {
  document.getElementById('documentFileInput').value = '';
  document.getElementById('selectedDocumentPreview').style.display = 'none';
  document.getElementById('selectedDocumentName').textContent = '';
  document.getElementById('selectedDocumentImagePreview').innerHTML = '';
}

function removeSelectedDocumentDetailModal() {
  const preview = document.getElementById('selectedDocumentPreviewDetailModal');
  if (preview) preview.style.display = 'none';
  const name = document.getElementById('selectedDocumentNameDetailModal');
  if (name) name.textContent = '';
  const imagePreview = document.getElementById('selectedDocumentImagePreviewDetailModal');
  if (imagePreview) imagePreview.innerHTML = '';
}

// Delete beneficial owner
function deleteBeneficialOwner() {
  if (!currentBeneficialOwnerId) return;
  if (!confirm('Delete this beneficial owner?')) return;
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/beneficial-owners/${currentBeneficialOwnerId}`;
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

// Form submission handler for page form
document.addEventListener('DOMContentLoaded', function() {
  const pageForm = document.getElementById('beneficialOwnerPageForm');
  if (pageForm) {
    pageForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      // Use native FormData first, then manually add any missing fields
      const formData = new FormData(pageForm);
      
      // Required fields to ensure are present
      const requiredFields = ['full_name', 'country', 'status', 'position'];
      const fieldNames = ['full_name', 'dob', 'nin_passport_no', 'country', 'expiry_date', 'status', 'position', 'shares', 'pep', 'pep_details', 'date_added', 'client_id'];
      
      // Manually collect all fields to ensure they're included
      fieldNames.forEach(fieldName => {
        const field = pageForm.querySelector(`[name="${fieldName}"]`);
        if (field) {
          if (field.type === 'file') {
            // File inputs are handled separately
            if (field.files && field.files.length > 0 && !formData.has(fieldName)) {
              formData.set(fieldName, field.files[0]);
            }
          } else if (field.type === 'checkbox' || field.type === 'radio') {
            if (field.checked) {
              formData.set(fieldName, field.value);
            }
          } else {
            // Set the value (will overwrite if already exists)
            formData.set(fieldName, field.value || '');
          }
        }
      });
      
      // Ensure CSRF token is present
      if (!formData.has('_token')) {
        const csrfInput = pageForm.querySelector('[name="_token"]');
        if (csrfInput) {
          formData.set('_token', csrfInput.value);
        } else {
          formData.set('_token', csrfToken);
        }
      }
      
      // Ensure method override is present
      const methodInput = pageForm.querySelector('[name="_method"]');
      if (methodInput && !formData.has('_method')) {
        formData.set('_method', methodInput.value);
      }
      
      // Debug: log FormData contents
      console.log('FormData contents before submit:');
      for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
      }
      
      // Check for missing required fields
      const missingFields = [];
      requiredFields.forEach(fieldName => {
        if (!formData.has(fieldName) || !formData.get(fieldName)) {
          missingFields.push(fieldName);
        }
      });
      
      if (missingFields.length > 0) {
        alert('Please fill in all required fields: ' + missingFields.join(', '));
        return;
      }
      
      const url = pageForm.action;
      const method = methodInput?.value || 'POST';
      
      try {
        // For PUT/PATCH requests, use POST method with _method field (Laravel method spoofing)
        // This ensures FormData is properly parsed by Laravel
        const fetchMethod = (method === 'PUT' || method === 'PATCH') ? 'POST' : method;
        
        const res = await fetch(url, {
          method: fetchMethod,
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        });
        
        if (res.ok) {
          const data = await res.json();
          alert(data.message || 'Beneficial Owner saved successfully!');
          closeBeneficialOwnerPageView();
          location.reload();
        } else {
          const error = await res.json();
          console.error('Server error:', error);
          alert('Error: ' + (error.message || 'Unknown error'));
        }
      } catch (e) {
        console.error(e);
        alert('Error saving beneficial owner: ' + e.message);
      }
    });
  }
  
  // Form submission handler for modal form
  const modalForm = document.getElementById('beneficialOwnerForm');
  if (modalForm) {
    modalForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      // Use native FormData first, then manually add any missing fields
      const formData = new FormData(modalForm);
      
      // Required fields to ensure are present
      const requiredFields = ['full_name', 'country', 'status', 'position'];
      const fieldNames = ['full_name', 'dob', 'nin_passport_no', 'country', 'expiry_date', 'status', 'position', 'shares', 'pep', 'pep_details', 'date_added', 'client_id'];
      
      // Manually collect all fields to ensure they're included
      fieldNames.forEach(fieldName => {
        const field = modalForm.querySelector(`[name="${fieldName}"]`);
        if (field) {
          if (field.type === 'file') {
            // File inputs are handled separately
            if (field.files && field.files.length > 0 && !formData.has(fieldName)) {
              formData.set(fieldName, field.files[0]);
            }
          } else if (field.type === 'checkbox' || field.type === 'radio') {
            if (field.checked) {
              formData.set(fieldName, field.value);
            }
          } else {
            // Set the value (will overwrite if already exists)
            formData.set(fieldName, field.value || '');
          }
        }
      });
      
      // Ensure CSRF token is present
      if (!formData.has('_token')) {
        const csrfInput = modalForm.querySelector('[name="_token"]');
        if (csrfInput) {
          formData.set('_token', csrfInput.value);
        } else {
          formData.set('_token', csrfToken);
        }
      }
      
      // Ensure method override is present
      const methodInput = modalForm.querySelector('[name="_method"]');
      if (methodInput && !formData.has('_method')) {
        formData.set('_method', methodInput.value);
      }
      
      // Check for missing required fields
      const missingFields = [];
      requiredFields.forEach(fieldName => {
        if (!formData.has(fieldName) || !formData.get(fieldName)) {
          missingFields.push(fieldName);
        }
      });
      
      if (missingFields.length > 0) {
        alert('Please fill in all required fields: ' + missingFields.join(', '));
        return;
      }
      
      const url = modalForm.action;
      const method = methodInput?.value || 'POST';
      
      try {
        // For PUT/PATCH requests, use POST method with _method field (Laravel method spoofing)
        // This ensures FormData is properly parsed by Laravel
        const fetchMethod = (method === 'PUT' || method === 'PATCH') ? 'POST' : method;
        
        const res = await fetch(url, {
          method: fetchMethod,
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        });
        
        const responseData = await res.json();
        
        if (res.ok) {
          alert(responseData.message || 'Beneficial Owner saved successfully!');
          closeBeneficialOwnerModal();
          location.reload();
        } else {
          // Handle validation errors
          let errorMessage = responseData.message || 'Unknown error';
          
          if (responseData.errors) {
            const errorMessages = [];
            for (const field in responseData.errors) {
              errorMessages.push(...responseData.errors[field]);
            }
            if (errorMessages.length > 0) {
              errorMessage = errorMessages.join('\n');
            }
          }
          
          alert('Error: ' + errorMessage);
        }
      } catch (e) {
        console.error(e);
        alert('Error saving beneficial owner: ' + e.message);
      }
    });
  }
  
  // Load documents for first beneficial owner on page load if client_id is present
  if (clientId) {
    const firstRadio = document.querySelector('input[name="selected_bo"]');
    if (firstRadio) {
      firstRadio.checked = true;
      const ownerCode = firstRadio.getAttribute('data-owner-code');
      if (ownerCode) {
        loadDocuments(ownerCode);
      }
    }
  }
  
  // Add document button handler for table view
  const addDocBtn = document.getElementById('addDocumentBtn');
  if (addDocBtn) {
    addDocBtn.addEventListener('click', () => {
      const selectedRadio = document.querySelector('input[name="selected_bo"]:checked');
      if (!selectedRadio) {
        alert('Please select a beneficial owner first');
        return;
      }
      const boId = selectedRadio.value;
      const ownerCode = selectedRadio.getAttribute('data-owner-code');
      
      // Store the selected beneficial owner ID temporarily for the modal
      const tempCurrentId = currentBeneficialOwnerId;
      currentBeneficialOwnerId = boId;
      
      // Open the document upload modal for detail
      openDocumentUploadModalForDetail();
      
      // Store owner code in a data attribute for the upload function
      const modal = document.getElementById('documentUploadModalForDetail');
      if (modal) {
        modal.setAttribute('data-owner-code', ownerCode);
      }
      
      // Restore current ID after modal closes (if needed)
      setTimeout(() => {
        if (!document.getElementById('documentUploadModalForDetail').classList.contains('show')) {
          currentBeneficialOwnerId = tempCurrentId;
        }
      }, 100);
    });
  }
  
  // Also handle the page view add document button
  const addDocBtnPageView = document.getElementById('addDocumentBtnPageView');
  if (addDocBtnPageView) {
    addDocBtnPageView.addEventListener('click', () => {
      if (!currentBeneficialOwnerId) {
        alert('No beneficial owner selected');
        return;
      }
      openDocumentUploadModalForDetail();
    });
  }
  
  // Close modal when clicking outside
  const beneficialOwnerModal = document.getElementById('beneficialOwnerModal');
  const documentModal = document.getElementById('documentUploadModal');
  
  if (beneficialOwnerModal) {
    beneficialOwnerModal.addEventListener('click', function(e) {
      if (e.target === this) {
        closeBeneficialOwnerModal();
      }
    });
  }
  
  if (documentModal) {
    documentModal.addEventListener('click', function(e) {
      if (e.target === this) {
        closeDocumentUploadModal();
      }
    });
  }
  
  const documentModalForDetail = document.getElementById('documentUploadModalForDetail');
  if (documentModalForDetail) {
    documentModalForDetail.addEventListener('click', function(e) {
      if (e.target === this) {
        closeDocumentUploadModalForDetail();
      }
    });
  }
  
  // Close modal on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const beneficialOwnerModal = document.getElementById('beneficialOwnerModal');
      const documentModal = document.getElementById('documentUploadModal');
      const documentModalForDetail = document.getElementById('documentUploadModalForDetail');
      if (beneficialOwnerModal && beneficialOwnerModal.classList.contains('show')) {
        closeBeneficialOwnerModal();
      } else if (documentModal && documentModal.classList.contains('show')) {
        closeDocumentUploadModal();
      } else if (documentModalForDetail && documentModalForDetail.classList.contains('show')) {
        closeDocumentUploadModalForDetail();
      }
    }
  });
});

// Make functions globally accessible
window.openBeneficialOwnerDetails = openBeneficialOwnerDetails;
window.closeBeneficialOwnerDetailModal = closeBeneficialOwnerDetailModal;
window.closeBeneficialOwnerPageView = closeBeneficialOwnerPageView;
window.openBeneficialOwnerModal = openBeneficialOwnerModal;
window.closeBeneficialOwnerModal = closeBeneficialOwnerModal;
window.deleteBeneficialOwner = deleteBeneficialOwner;
window.handleDocumentFileSelect = handleDocumentFileSelect;
window.handleDocumentFileSelectDetailModal = handleDocumentFileSelectDetailModal;
window.removeSelectedDocument = removeSelectedDocument;
window.removeSelectedDocumentDetailModal = removeSelectedDocumentDetailModal;
window.loadDocumentsForSelectedBO = loadDocumentsForSelectedBO;
window.openDocumentUploadModal = openDocumentUploadModal;
window.closeDocumentUploadModal = closeDocumentUploadModal;
window.confirmDocumentSelection = confirmDocumentSelection;
window.openDocumentUploadModalForDetail = openDocumentUploadModalForDetail;
window.closeDocumentUploadModalForDetail = closeDocumentUploadModalForDetail;
window.handleDocumentFileSelectForDetail = handleDocumentFileSelectForDetail;
window.uploadDocumentForDetail = uploadDocumentForDetail;
