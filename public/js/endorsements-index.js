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

  // Open endorsement details (full page view) - MUST be defined before HTML onclick handlers
  async function openendorsementDetails(id) {
    try {
      const res = await fetch(`/endorsements/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const endorsement = await res.json();
      currentendorsementId = id;
      
      // Get all required elements
      const endorsementPageName = document.getElementById('endorsementPageName');
      const endorsementPageTitle = document.getElementById('endorsementPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const endorsementPageView = document.getElementById('endorsementPageView');
      const endorsementDetailsPageContent = document.getElementById('endorsementDetailsPageContent');
      const endorsementFormPageContent = document.getElementById('endorsementFormPageContent');
      const editendorsementFromPageBtn = document.getElementById('editendorsementFromPageBtn');
      const closeendorsementPageBtn = document.getElementById('closeendorsementPageBtn');
      
      if (!endorsementPageName || !endorsementPageTitle || !clientsTableView || !endorsementPageView || 
          !endorsementDetailsPageContent || !endorsementFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set endorsement name in header
      const endorsementName = endorsement.endorsement_id || 'Unknown';
      endorsementPageName.textContent = endorsementName;
      endorsementPageTitle.textContent = 'endorsement';
      
      populateendorsementDetails(endorsement);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      endorsementPageView.style.display = 'block';
      endorsementPageView.classList.add('show');
      endorsementDetailsPageContent.style.display = 'block';
      endorsementFormPageContent.style.display = 'none';
      if (editendorsementFromPageBtn) editendorsementFromPageBtn.style.display = 'inline-block';
      if (closeendorsementPageBtn) closeendorsementPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading endorsement details: ' + e.message);
    }
  }

  // Populate endorsement details view
  function populateendorsementDetails(endorsement) {
    const content = document.getElementById('endorsementDetailsContent');
    if (!content) return;

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">endorsement DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">endorsementID</span>
            <div class="detail-value">${endorsement.endorsement_id || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">endorsement Source</span>
            <div class="detail-value">${endorsement.endorsement_source ? endorsement.endorsement_source.name : (endorsement.endorsementSource ? endorsement.endorsementSource.name : '-')}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Rcvd</span>
            <div class="detail-value">${formatDate(endorsement.date_rcvd)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Amount Received</span>
            <div class="detail-value">${formatNumber(endorsement.amount_received)}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">ADDITIONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Description</span>
            <div class="detail-value">${endorsement.description || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Category</span>
            <div class="detail-value">${endorsement.endorsement_category ? endorsement.endorsement_category.name : (endorsement.endorsementCategory ? endorsement.endorsementCategory.name : '-')}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Mode Of Payment</span>
            <div class="detail-value">${endorsement.mode_of_payment ? endorsement.mode_of_payment.name : (endorsement.modeOfPayment ? endorsement.modeOfPayment.name : '-')}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Statement No</span>
            <div class="detail-value">${endorsement.statement_no || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">NOTES</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">endorsement Notes</span>
            <textarea class="detail-value" style="min-height:120px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${endorsement.endorsement_notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    const col4 = `
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open endorsement page (Add or Edit)
  async function openendorsementPage(mode) {
    if (mode === 'add') {
      openendorsementForm('add');
    } else {
      if (currentendorsementId) {
        openEditendorsement(currentendorsementId);
      }
    }
  }

  // Add endorsement Button - Open Modal
  document.getElementById('addEndorsementBtn').addEventListener('click', () => openendorsementModal('add'));
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditendorsement(id) {
    try {
      const res = await fetch(`/endorsements/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const endorsement = await res.json();
      currentendorsementId = id;
      openendorsementForm('edit', endorsement);
    } catch (e) {
      console.error(e);
      alert('Error loading endorsement data');
    }
  }

  function openendorsementForm(mode, endorsement = null) {
    // Clone form from modal
    const modalForm = document.getElementById('endorsementModal').querySelector('form');
    const pageForm = document.getElementById('endorsementPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('endorsementPageFormMethod');
    const deleteBtn = document.getElementById('endorsementDeleteBtn');
    const editBtn = document.getElementById('editendorsementFromPageBtn');
    const closeBtn = document.getElementById('closeendorsementPageBtn');
    const closeFormBtn = document.getElementById('closeendorsementFormBtn');

    if (mode === 'add') {
      document.getElementById('endorsementPageTitle').textContent = 'Add endorsement';
      document.getElementById('endorsementPageName').textContent = '';
      pageForm.action = endorsementsStoreRoute;
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const endorsementName = endorsement.endorsement_id || 'Unknown';
      document.getElementById('endorsementPageTitle').textContent = 'Edit endorsement';
      document.getElementById('endorsementPageName').textContent = endorsementName;
      pageForm.action = `/endorsements/${currentendorsementId}`;
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

      const fields = ['endorsement_source_id','date_rcvd','amount_received','description','category_id','mode_of_payment_id','statement_no','endorsement_notes'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = endorsement[k] ? (typeof endorsement[k] === 'string' ? endorsement[k].substring(0,10) : endorsement[k]) : '';
        } else if (el.tagName === 'TEXTAREA') {
          el.value = endorsement[k] ?? '';
        } else {
          el.value = endorsement[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const endorsementPageView = document.getElementById('endorsementPageView');
    endorsementPageView.style.display = 'block';
    endorsementPageView.classList.add('show');
    document.getElementById('endorsementDetailsPageContent').style.display = 'none';
    document.getElementById('endorsementFormPageContent').style.display = 'block';
  }

  function closeendorsementPageView() {
    const endorsementPageView = document.getElementById('endorsementPageView');
    endorsementPageView.classList.remove('show');
    endorsementPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('endorsementDetailsPageContent').style.display = 'none';
    document.getElementById('endorsementFormPageContent').style.display = 'none';
    currentendorsementId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editendorsementFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentendorsementId) {
        openEditendorsement(currentendorsementId);
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

  function deleteendorsement() {
    if (!currentendorsementId) return;
    if (!confirm('Delete this endorsement?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/endorsements/${currentendorsementId}`;
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

  // Open endorsement Modal
  async function openendorsementModal(mode, endorsementId = null) {
    const modal = document.getElementById('endorsementModal');
    const form = document.getElementById('endorsementForm');
    const formMethod = document.getElementById('endorsementFormMethod');
    const title = document.getElementById('endorsementModalTitle');
    const deleteBtn = document.getElementById('endorsementDeleteBtnModal');
    
    if (!modal || !form || !formMethod || !title) {
      console.error('Required modal elements not found');
      alert('Error: Modal elements not found');
      return;
    }
    
    if (mode === 'add') {
      title.textContent = 'Add Endorsement';
      form.action = endorsementsStoreRoute;
      formMethod.innerHTML = '';
      if (deleteBtn) deleteBtn.style.display = 'none';
      form.reset();
      currentendorsementId = null;
      // Reset document preview
      document.getElementById('selectedDocumentPreview').style.display = 'none';
      document.getElementById('documentFileInput').value = '';
    } else if (mode === 'edit' && endorsementId) {
      try {
        const res = await fetch(`/endorsements/${endorsementId}/edit`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        if (!res.ok) throw new Error('Network error');
        const endorsement = await res.json();
        currentendorsementId = endorsementId;
        
        title.textContent = 'Edit endorsement';
        form.action = `/endorsements/${endorsementId}`;
        formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        if (deleteBtn) deleteBtn.style.display = canDeleteendorsement ? 'inline-block' : 'none';
        
        // Populate form fields
        document.getElementById('policy_id').value = endorsement.policy_id || '';
        document.getElementById('date').value = endorsement.effective_date ? (typeof endorsement.effective_date === 'string' ? endorsement.effective_date.substring(0,10) : endorsement.effective_date) : '';
        document.getElementById('endorsement_source_id').value = endorsement.type?? '';
        document.getElementById('description').value = endorsement.description || '';
        document.getElementById('endorsement_notes').value = endorsement.endorsement_notes || '';

        // document.getElementById('endorsement_notes').value = endorsement.endorsement_notes || '';
        
        // Show existing document preview if available (from documents table)
        const previewDiv = document.getElementById('selectedDocumentPreview');
        const docName = document.getElementById('selectedDocumentName');
        const imagePreview = document.getElementById('selectedDocumentImagePreview');
        
        if (endorsement.documents && endorsement.documents.length > 0) {
          const document = endorsement.documents[0];
          if (document && document.file_path) {
            previewDiv.style.display = 'block';
            const fileName = document.file_path.split('/').pop();
            docName.textContent = document.name || fileName;
            // Show link to view document
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
        alert('Error loading endorsement data');
        return;
      }
    }
    
    // Show the modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeendorsementModal() {
    const modal = document.getElementById('endorsementModal');
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = '';
      currentendorsementId = null;
    }
  }

  // Update table row click to open edit modal
  function openendorsementDetails(id) {
    console.log(id);
    openendorsementModal('edit', id);
  }

  // Document Upload Modal Functions
  function openDocumentUploadModal() {
    const modal = document.getElementById('documentUploadModal');
    if (modal) {
      // If in edit mode and endorsement has a document, show existing document
      if (currentendorsementId) {
        // Fetch endorsement to check for existing document
        fetch(`/endorsements/${currentendorsementId}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(res => res.json())
        .then(endorsement => {
          const existingPreview = document.getElementById('existingDocumentPreview');
          const existingPreviewContent = document.getElementById('existingDocumentPreviewContent');
          if (endorsement.documents && endorsement.documents.length > 0) {
            const document = endorsement.documents[0];
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
          console.error('Error loading endorsement:', err);
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
    const hiddenInput = document.getElementById('documentFileInput');
    const previewDiv = document.getElementById('selectedDocumentPreview');
    const docName = document.getElementById('selectedDocumentName');
    const imagePreview = document.getElementById('selectedDocumentImagePreview');
    
    if (!fileInput.files || !fileInput.files[0]) {
      alert('Please select a file first');
      return;
    }

    const file = fileInput.files[0];
    
    // Copy file to hidden input in endorsement form
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    hiddenInput.files = dataTransfer.files;
    
    // Show preview in endorsement form
    docName.textContent = file.name;
    previewDiv.style.display = 'block';
    
    // Show image preview if it's an image
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        imagePreview.innerHTML = `<img src="${e.target.result}" style="max-width:100%; max-height:150px; border:1px solid #ddd; border-radius:4px;" alt="Document preview">`;
      };
      reader.readAsDataURL(file);
    } else {
      imagePreview.innerHTML = '';
    }
    
    // Close modal
    closeDocumentUploadModal();
  }

  function removeSelectedDocument() {
    document.getElementById('documentFileInput').value = '';
    document.getElementById('selectedDocumentPreview').style.display = 'none';
    document.getElementById('selectedDocumentName').textContent = '';
    document.getElementById('selectedDocumentImagePreview').innerHTML = '';
  }

  // Close modal when clicking outside
  document.addEventListener('DOMContentLoaded', function() {
    const endorsementModal = document.getElementById('endorsementModal');
    const documentModal = document.getElementById('documentUploadModal');
    
    if (endorsementModal) {
      endorsementModal.addEventListener('click', function(e) {
        if (e.target === this) {
          closeendorsementModal();
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
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const endorsementModal = document.getElementById('endorsementModal');
        const documentModal = document.getElementById('documentUploadModal');
        if (endorsementModal && endorsementModal.classList.contains('show')) {
          closeendorsementModal();
        } else if (documentModal && documentModal.classList.contains('show')) {
          closeDocumentUploadModal();
        }
      }
    });
  });

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
