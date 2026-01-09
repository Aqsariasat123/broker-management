  // Data initialized in Blade template

  // Helper function for date formatting
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
  }

  // Open document details modal
  async function openDocumentDetails(id) {
    try {
      const res = await fetch(`/documents/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const doc = await res.json();
      currentDocumentId = id;
      
      // Set document name in header
      const documentName = doc.name || doc.doc_id || 'Unknown';
      document.getElementById('documentDetailsModalTitle').textContent = 'Document Details - ' + documentName;
      
      const content = document.getElementById('documentDetailsContent');
  
      const fileLink = doc.file_path ? `<a href="{{ asset('storage') }}/${doc.file_path}" target="_blank" style="color:#007bff; text-decoration:underline;">View File</a>` : '-';
  
      content.innerHTML = `
        <div style="background:#f5f5f5; padding:12px; border-radius:4px;">
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Document ID:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${doc.doc_id || '-'}</div>
          </div>
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Name:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${doc.name || '-'}</div>
          </div>
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Tied To:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${doc.tied_to || '-'}</div>
          </div>
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Group:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${doc.group || '-'}</div>
          </div>
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Type:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${doc.type || '-'}</div>
          </div>
        </div>
        <div style="background:#f5f5f5; padding:12px; border-radius:4px;">
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Format:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${doc.format || '-'}</div>
          </div>
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Date Added:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${formatDate(doc.date_added)}</div>
          </div>
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Year:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${doc.year || '-'}</div>
          </div>
          <div style="margin-bottom:10px;">
            <span style="font-size:12px; color:#666; font-weight:500;">File:</span>
            <div style="font-size:13px; color:#000; margin-top:4px;">${fileLink}</div>
          </div>
          <div style="margin-top:15px;">
            <span style="font-size:12px; color:#666; font-weight:500;">Notes:</span>
            <div style="font-size:13px; color:#000; margin-top:4px; white-space:pre-wrap;">${doc.notes || '-'}</div>
          </div>
        </div>
      `;    
      // Show edit button
      const editBtn = document.getElementById('editDocumentFromDetailsBtn');
      if (editBtn) editBtn.style.display = 'inline-block';
      
      // Show modal
      const modal = document.getElementById('documentDetailsModal');
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    } catch (e) {
      console.error(e);
      alert('Error loading document details: ' + e.message);
    }
  }

  function closeDocumentDetailsModal() {
    const modal = document.getElementById('documentDetailsModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    currentDocumentId = null;
  }

 

  // Open document modal (Add or Edit)
  function openDocumentModal(mode, id = null) {
    const modal = document.getElementById('documentModal');
    const form = document.getElementById('documentForm');
    const formMethod = document.getElementById('documentFormMethod');
    const deleteBtn = document.getElementById('documentDeleteBtnModal');
    const title = document.getElementById('documentModalTitle');
    
    if (mode === 'add') {
      currentDocumentId = null;
      title.textContent = 'Add Document';
      form.action = documentsStoreRoute;
      formMethod.innerHTML = '';
      client ? document.getElementById('tied_to').value = client.clid : document.getElementById('tied_to').value = '';
      client ? document.getElementById('name').value = 'Client Photo' : document.getElementById('name').value = '';
      client ? document.getElementById('group').value = 'Photo' : document.getElementById('group').value = '';
      client ? document.getElementById('type').value = 'Photo' : document.getElementById('type').value = '';
    
       if (deleteBtn) deleteBtn.style.display = 'none';
       if (!client) form.reset();
    } else if (id) {
      // Load document data for edit
      fetch(`/documents/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(doc => {
        currentDocumentId = id;
        title.textContent = 'Edit Document';
        form.action = `/documents/${id}`;
        formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        if (deleteBtn) deleteBtn.style.display = 'inline-block';
        
        // Populate form fields
        document.getElementById('tied_to').value = doc.tied_to || '';
        document.getElementById('name').value = doc.name || '';
        document.getElementById('group').value = doc.group || '';
        document.getElementById('type').value = doc.type || '';
        document.getElementById('date_added').value = doc.date_added ? doc.date_added.substring(0, 10) : '';
        document.getElementById('year').value = doc.year || '';
        document.getElementById('notes').value = doc.notes || '';
        
        // Show modal
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
      })
      .catch(e => {
        console.error(e);
        alert('Error loading document data');
      });
      return;
    }
    
    // Show modal for add mode
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeDocumentModal() {
    const modal = document.getElementById('documentModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    currentDocumentId = null;
  }

  // Add Document Button - wrapped in DOMContentLoaded for reliability
  document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addDocumentBtn');
    if (addBtn) {
      addBtn.addEventListener('click', function() {
        openDocumentModal('add');
      });
    }

    const columnBtn = document.getElementById('columnBtn');
    if (columnBtn) {
      columnBtn.addEventListener('click', function() {
        openColumnModal();
      });
    }
  });

  // Legacy functions for backward compatibility
  async function openDocumentPage(mode) {
    if (mode === 'add') {
      openDocumentModal('add');
    } else {
      if (currentDocumentId) {
        openDocumentModal('edit', currentDocumentId);
      }
    }
  }

  async function openEditDocument(id) {
    openDocumentModal('edit', id);
  }

  function openDocumentForm(mode, doc = null) {
    // Clone form from modal
    const modalForm = document.getElementById('documentModal').querySelector('form');
    const pageForm = document.getElementById('documentPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('documentPageFormMethod');
    const deleteBtn = document.getElementById('documentDeleteBtn');
    const editBtn = document.getElementById('editDocumentFromPageBtn');
    const closeBtn = document.getElementById('closeDocumentPageBtn');
    const closeFormBtn = document.getElementById('closeDocumentFormBtn');

    if (mode === 'add') {
      document.getElementById('documentPageTitle').textContent = 'Add Document';
      document.getElementById('documentPageName').textContent = '';
      pageForm.action = documentsStoreRoute;
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const documentName = doc.name || doc.doc_id || 'Unknown';
      document.getElementById('documentPageTitle').textContent = 'Edit Document';
      document.getElementById('documentPageName').textContent = documentName;
      pageForm.action = `/documents/${currentDocumentId}`;
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

      const fields = ['tied_to','name','group','type','date_added','year','notes'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = doc[k] ? (typeof doc[k] === 'string' ? doc[k].substring(0,10) : doc[k]) : '';
        } else if (el.tagName === 'TEXTAREA') {
          el.value = doc[k] ?? '';
        } else {
          el.value = doc[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const documentPageView = document.getElementById('documentPageView');
    documentPageView.style.display = 'block';
    documentPageView.classList.add('show');
    document.getElementById('documentDetailsPageContent').style.display = 'none';
    document.getElementById('documentFormPageContent').style.display = 'block';
  }

  function closeDocumentPageView() {
    const documentPageView = document.getElementById('documentPageView');
    documentPageView.classList.remove('show');
    documentPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('documentDetailsPageContent').style.display = 'none';
    document.getElementById('documentFormPageContent').style.display = 'none';
    currentDocumentId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editDocumentFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentDocumentId) {
        openEditDocument(currentDocumentId);
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

  function deleteDocument() {
    if (!currentDocumentId) return;
    if (!confirm('Delete this document?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/documents/${currentDocumentId}`;
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

  function deleteDocumentFromTable(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/documents/${id}`;
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

  // Handle form submission
  document.getElementById('documentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const isEdit = form.action.includes('/documents/') && form.action !== documentsStoreRoute;
    
    if (isEdit) {
      formData.append('_method', 'PUT');
    }
    
    try {
      const response = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });
      
      if (response.ok) {
        const result = await response.json();
        if (result.success || response.status === 200) {
          alert(isEdit ? 'Document updated successfully!' : 'Document created successfully!');
          closeDocumentModal();
          location.reload();
        } else {
          alert('Error: ' + (result.message || 'Unknown error'));
        }
      } else {
        const errorData = await response.json();
        if (errorData.errors) {
          let errorMsg = 'Validation errors:\n';
          Object.keys(errorData.errors).forEach(key => {
            errorMsg += errorData.errors[key][0] + '\n';
          });
          alert(errorMsg);
        } else {
          alert('Error saving document: ' + (errorData.message || 'Unknown error'));
        }
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error saving document: ' + error.message);
    }
  });

  // Close modals on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeDocumentModal();
      closeDocumentDetailsModal();
    }
  });

  // Close modals when clicking outside
  document.getElementById('documentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeDocumentModal();
    }
  });

  document.getElementById('documentDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeDocumentDetailsModal();
    }
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
