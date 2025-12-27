

  async function openNomineeDialog() {
    currentNomineeId = null;
    document.getElementById('nomineeModalTitle').textContent = 'Add Nominee';
    document.getElementById('nomineeForm').reset();
    document.getElementById('nominee_id').value = '';
    document.getElementById('nominee_policy_id').value = policyId || '';
    const modal = document.getElementById('nomineeModal');
    modal.style.display = 'flex';
    modal.classList.add('show');
  }

  function closeNomineeDialog() {
    const modal = document.getElementById('nomineeModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.getElementById('nomineeForm').reset();
    currentNomineeId = null;
  }

  async function editNominee(id) {
    try {
      const response = await fetch(`/nominees/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      const nominee = await response.json();
      
      currentNomineeId = id;
      document.getElementById('nomineeModalTitle').textContent = 'Edit Nominee';
      document.getElementById('nominee_id').value = id;
      document.getElementById('nominee_full_name').value = nominee.full_name || '';
      document.getElementById('nominee_date_of_birth').value = nominee.date_of_birth || '';
      document.getElementById('nominee_nin_passport_no').value = nominee.nin_passport_no || '';
      document.getElementById('nominee_relationship').value = nominee.relationship || '';
      document.getElementById('nominee_share_percentage').value = nominee.share_percentage || '';
      document.getElementById('nominee_date_removed').value = nominee.date_removed || '';
      document.getElementById('nominee_notes').value = nominee.notes || '';
      document.getElementById('nominee_policy_id').value = nominee.policy_id || (policyId || '');
      
      const modal = document.getElementById('nomineeModal');
      modal.style.display = 'flex';
      modal.classList.add('show');
    } catch (error) {
      console.error('Error loading nominee:', error);
      alert('Error loading nominee details');
    }
  }

  async function saveNominee(addAnother = false) {
    const form = document.getElementById('nomineeForm');
    
    // Validate form before submission
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    
    // Build form data explicitly to ensure all fields are included
    const formData = new FormData();
    
    // Get all form field values
    const fullName = document.getElementById('nominee_full_name').value;
    if (!fullName || fullName.trim() === '') {
      alert('Full Name is required');
      document.getElementById('nominee_full_name').focus();
      return;
    }
    
    // Add all form fields explicitly
    formData.append('full_name', fullName);
    formData.append('date_of_birth', document.getElementById('nominee_date_of_birth').value || '');
    formData.append('nin_passport_no', document.getElementById('nominee_nin_passport_no').value || '');
    formData.append('relationship', document.getElementById('nominee_relationship').value || '');
    formData.append('share_percentage', document.getElementById('nominee_share_percentage').value || '');
    formData.append('date_removed', document.getElementById('nominee_date_removed').value || '');
    formData.append('notes', document.getElementById('nominee_notes').value || '');
    
    // Add policy_id if it exists
    const policyIdValue = document.getElementById('nominee_policy_id').value;
    if (policyIdValue) {
      formData.append('policy_id', policyIdValue);
    }
    
    // Always use POST method, Laravel will handle method spoofing
    const url = currentNomineeId 
      ? `/nominees/${currentNomineeId}`
      : '/nominees';
    const method = 'POST';

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    formData.append('_token', csrfToken);
    
    // Add method spoofing for PUT requests
    if (currentNomineeId) {
      formData.append('_method', 'PUT');
    }

    try {
      const response = await fetch(url, {
        method: method,
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const data = await response.json();

      if (response.ok && data.success) {
        if (!addAnother) {
          closeNomineeDialog();
          window.location.reload();
        } else {
          form.reset();
          document.getElementById('nominee_policy_id').value = policyId || '';
          currentNomineeId = null;
          document.getElementById('nomineeModalTitle').textContent = 'Add Nominee';
        }
      } else {
        // Handle validation errors
        if (data.errors) {
          const errorMessages = Object.values(data.errors).flat().join('\n');
          alert('Validation errors:\n' + errorMessages);
        } else if (data.message) {
          alert('Error: ' + data.message);
        } else {
          alert('Error: Failed to save nominee');
        }
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error saving nominee: ' + error.message);
    }
  }

  function saveNomineeAndAddAnother() {
    saveNominee(true);
  }

  function toggleAllNominees(checkbox) {
    const checkboxes = document.querySelectorAll('input[name="selected_nominees[]"]');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
  }

  async function removeSelectedNominees() {
    const selected = document.querySelectorAll('input[name="selected_nominees[]"]:checked');
    if (selected.length === 0) {
      alert('Please select at least one nominee to remove');
      return;
    }

    const count = selected.length;
    const message = count === 1 
      ? 'Are you sure you want to remove this nominee?'
      : `Are you sure you want to remove ${count} nominees?`;
    
    if (!confirm(message)) {
      return;
    }

    const nomineeIds = Array.from(selected).map(cb => cb.value);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Delete nominees one by one
    let successCount = 0;
    let errorCount = 0;
    const errors = [];
    
    for (const nomineeId of nomineeIds) {
      try {
        const response = await fetch(`/nominees/${nomineeId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (response.ok) {
          const data = await response.json();
          if (data.success) {
            successCount++;
          } else {
            errorCount++;
            errors.push(`Nominee ${nomineeId}: ${data.message || 'Unknown error'}`);
          }
        } else {
          errorCount++;
          const errorData = await response.json().catch(() => ({}));
          errors.push(`Nominee ${nomineeId}: ${errorData.message || 'HTTP ' + response.status}`);
        }
      } catch (error) {
        console.error('Error removing nominee:', error);
        errorCount++;
        errors.push(`Nominee ${nomineeId}: ${error.message || 'Network error'}`);
      }
    }

    // Build success message with policy_id for redirect
    // policyId is already initialized in Blade template
    let redirectUrl = '/nominees';
    if (policyId) {
      redirectUrl += '?policy_id=' + policyId;
    }
    
    if (successCount > 0) {
      // Add success message to URL
      const message = successCount === 1 
        ? 'Nominee deleted successfully.'
        : `${successCount} nominee(s) deleted successfully.`;
      redirectUrl += (policyId ? '&' : '?') + 'success=' + encodeURIComponent(message);
    }

    if (errorCount === 0) {
      // All successful
      window.location.href = redirectUrl;
    } else if (successCount > 0) {
      // Some successful, some failed
      alert(`Removed ${successCount} nominee(s). ${errorCount} error(s) occurred.\n\nErrors:\n${errors.join('\n')}`);
      window.location.href = redirectUrl;
    } else {
      // All failed
      alert(`Failed to remove nominees. ${errorCount} error(s) occurred.\n\nErrors:\n${errors.join('\n')}`);
    }
  }

  function openDocumentUpload() {
    const modal = document.getElementById('documentUploadModal');
    if (modal) {
      modal.style.display = 'flex';
      modal.classList.add('show');
      document.getElementById('documentUploadForm').reset();
    }
  }

  function closeDocumentUploadModal() {
    const modal = document.getElementById('documentUploadModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.remove('show');
    }
  }

  async function loadDocuments() {
    // policyId is already initialized in Blade template
    const documentsContent = document.getElementById('documentsContent');
    if (!documentsContent) return;

    try {
      const params = new URLSearchParams();
      if (policyId) {
        params.append('policy_id', policyId);
      }

      const response = await fetch(`/nominees/documents?${params.toString()}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await response.json();
      
      if (data.success && data.documents) {
        displayDocuments(data.documents);
      } else {
        documentsContent.innerHTML = '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
      }
    } catch (error) {
      console.error('Error loading documents:', error);
      documentsContent.innerHTML = '<div style="color:#999; font-size:12px;">Error loading documents</div>';
    }
  }

  function displayDocuments(documents) {
    const documentsContent = document.getElementById('documentsContent');
    if (!documentsContent) return;

    if (documents.length === 0) {
      documentsContent.innerHTML = '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
      return;
    }

    const documentsHTML = documents.map(doc => {
      const fileUrl = doc.file_path ? `/storage/${doc.file_path}` : '#';
      const icon = getFileIcon(doc.format);
      const dateAdded = doc.date_added ? new Date(doc.date_added).toLocaleDateString() : 'N/A';
      
      return `
        <div style="background:#f8f9fa; border:1px solid #ddd; border-radius:4px; padding:10px; min-width:150px; max-width:200px;">
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
            <span style="font-size:20px;">${icon}</span>
            <div style="flex:1; min-width:0;">
              <div style="font-weight:600; font-size:12px; color:#333; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${doc.name}">${doc.name}</div>
              <div style="font-size:10px; color:#666;">${doc.doc_id}</div>
            </div>
          </div>
          <div style="font-size:10px; color:#666; margin-bottom:8px;">
            <div>Date: ${dateAdded}</div>
            ${doc.format ? `<div>Format: ${doc.format.toUpperCase()}</div>` : ''}
          </div>
          <div style="display:flex; gap:5px;">
            <a href="${fileUrl}" target="_blank" style="flex:1; background:#f3742a; color:#fff; border:none; padding:4px 8px; border-radius:2px; cursor:pointer; text-decoration:none; font-size:11px; text-align:center;">View</a>
            <button onclick="deleteDocument(${doc.id})" style="background:#dc3545; color:#fff; border:none; padding:4px 8px; border-radius:2px; cursor:pointer; font-size:11px;">Delete</button>
          </div>
        </div>
      `;
    }).join('');

    documentsContent.innerHTML = documentsHTML;
  }

  function getFileIcon(format) {
    const icons = {
      'pdf': '📄',
      'doc': '📝',
      'docx': '📝',
      'jpg': '🖼️',
      'jpeg': '🖼️',
      'png': '🖼️',
      'xls': '📊',
      'xlsx': '📊'
    };
    return icons[format?.toLowerCase()] || '📎';
  }

  async function uploadDocument() {
    const form = document.getElementById('documentUploadForm');
    const formData = new FormData(form);
    
    // policyId is already initialized in Blade template
    if (policyId) {
      formData.append('policy_id', policyId);
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    formData.append('_token', csrfToken);

    try {
      const response = await fetch('/nominees/upload-document', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        closeDocumentUploadModal();
        await loadDocuments();
        alert('Document uploaded successfully.');
      } else {
        alert('Error: ' + (data.message || 'Failed to upload document'));
      }
    } catch (error) {
      console.error('Error uploading document:', error);
      alert('Error uploading document: ' + error.message);
    }
  }

  async function deleteDocument(documentId) {
    if (!confirm('Are you sure you want to delete this document?')) {
      return;
    }

    try {
      const response = await fetch(`/documents/${documentId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (response.ok) {
        await loadDocuments();
        alert('Document deleted successfully.');
      } else {
        alert('Error deleting document');
      }
    } catch (error) {
      console.error('Error deleting document:', error);
      alert('Error deleting document: ' + error.message);
    }
  }

  // Load documents on page load
  document.addEventListener('DOMContentLoaded', function() {
    loadDocuments();
  });

  // Print function
  function printTable() {
    const table = document.getElementById('nomineesTable');
    if (!table) return;
    
    // Get table headers - preserve order
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    headerCells.forEach(th => {
      let headerText = '';
      const clone = th.cloneNode(true);
      const filterInput = clone.querySelector('.column-filter');
      if (filterInput) filterInput.remove();
      headerText = clone.textContent.trim();
      // Handle bell icon column
      if (clone.querySelector('svg')) {
        headerText = '🔔';
      }
      if (headerText) {
        headers.push(headerText);
      }
    });
    
    // Get table rows data
    const rows = [];
    const tableRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    tableRows.forEach(row => {
      if (row.style.display === 'none') return;
      
      const cells = [];
      const rowCells = row.querySelectorAll('td');
      rowCells.forEach((cell) => {
        let cellContent = '';
        
        // Handle checkbox column (for selection)
        if (cell.querySelector('input[type="checkbox"][name="selected_nominees[]"]')) {
          const checkbox = cell.querySelector('input[type="checkbox"]');
          cellContent = checkbox && checkbox.checked ? '✓' : '';
        } 
        // Handle action column
        else if (cell.classList.contains('action-cell')) {
          const expandIcon = cell.querySelector('.action-expand');
          if (expandIcon) cellContent = '⤢';
        } 
        // Handle regular cells
        else {
          const link = cell.querySelector('a');
          if (link) {
            cellContent = link.textContent.trim();
          } else {
            cellContent = cell.textContent.trim();
          }
        }
        
        cells.push(cellContent || '-');
      });
      rows.push(cells);
    });
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
      if (!text) return '';
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    
    // Build headers HTML
    const headersHTML = headers.map(h => '<th>' + escapeHtml(h) + '</th>').join('');
    
    // Build rows HTML
    const rowsHTML = rows.map(row => {
      const cellsHTML = row.map(cell => {
        const cellText = escapeHtml(String(cell || '-'));
        return '<td>' + cellText + '</td>';
      }).join('');
      return '<tr>' + cellsHTML + '</tr>';
    }).join('');
    
    // Create print window
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    const printHTML = '<!DOCTYPE html>' +
      '<html>' +
      '<head>' +
      '<title>Nominees - Print</title>' +
      '<style>' +
      '@page { margin: 1cm; size: A4 landscape; }' +
      'html, body { margin: 0; padding: 0; background: #fff !important; }' +
      'body { font-family: Arial, sans-serif; font-size: 10px; }' +
      'table { width: 100%; border-collapse: collapse; page-break-inside: auto; }' +
      'thead { display: table-header-group; }' +
      'thead th { background-color: #000 !important; color: #fff !important; padding: 8px 5px; text-align: left; border: 1px solid #333; font-weight: normal; -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
      'tbody tr { page-break-inside: avoid; border-bottom: 1px solid #ddd; }' +
      'tbody tr:nth-child(even) { background-color: #f8f8f8; }' +
      'tbody td { padding: 6px 5px; border: 1px solid #ddd; white-space: nowrap; }' +
      '</style>' +
      '</head>' +
      '<body>' +
      '<table>' +
      '<thead><tr>' + headersHTML + '</tr></thead>' +
      '<tbody>' + rowsHTML + '</tbody>' +
      '</table>' +
      '<scr' + 'ipt>' +
      'window.onload = function() {' +
      '  setTimeout(function() {' +
      '    window.print();' +
      '  }, 100);' +
      '};' +
      'window.onafterprint = function() {' +
      '  window.close();' +
      '};' +
      '</scr' + 'ipt>' +
      '</body>' +
      '</html>';
    
    if (printWindow) {
      printWindow.document.open();
      printWindow.document.write(printHTML);
      printWindow.document.close();
    }
  }

  // Column modal functions
  function openColumnModal() {
    const modal = document.getElementById('columnModal');
    if (modal) {
      modal.style.display = 'flex';
      modal.classList.add('show');
      initDragAndDrop();
    }
  }

  function closeColumnModal() {
    const modal = document.getElementById('columnModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.remove('show');
    }
  }

  function selectAllColumns() {
    const checkboxes = document.querySelectorAll('#columnSelection .column-checkbox:not(:disabled)');
    checkboxes.forEach(cb => cb.checked = true);
  }

  function deselectAllColumns() {
    const checkboxes = document.querySelectorAll('#columnSelection .column-checkbox:not(:disabled)');
    checkboxes.forEach(cb => cb.checked = false);
  }

  function saveColumnSettings() {
    const form = document.getElementById('columnForm');
    const checkboxes = document.querySelectorAll('#columnSelection .column-checkbox:checked');
    const columns = Array.from(checkboxes).map(cb => cb.value);
    
    // Create hidden input for columns
    let columnsInput = document.getElementById('columnsInput');
    if (!columnsInput) {
      columnsInput = document.createElement('input');
      columnsInput.type = 'hidden';
      columnsInput.name = 'columns';
      columnsInput.id = 'columnsInput';
      form.appendChild(columnsInput);
    }
    columnsInput.value = JSON.stringify(columns);
    
    // Add policy_id if exists
    // policyId is already initialized in Blade template
    if (policyId) {
      let policyIdInput = document.getElementById('policyIdInput');
      if (!policyIdInput) {
        policyIdInput = document.createElement('input');
        policyIdInput.type = 'hidden';
        policyIdInput.name = 'policy_id';
        policyIdInput.id = 'policyIdInput';
        form.appendChild(policyIdInput);
      }
      policyIdInput.value = policyId;
    }
    
    form.submit();
  }

 

  function initDragAndDrop() {
    const columnSelection = document.getElementById('columnSelection');
    if (!columnSelection) return;

    if (dragInitialized) {
      const columnItems = columnSelection.querySelectorAll('.column-item');
      columnItems.forEach(item => {
        item.setAttribute('draggable', 'true');
      });
      return;
    }

    const columnItems = columnSelection.querySelectorAll('.column-item');
    columnItems.forEach(item => {
      item.setAttribute('draggable', 'true');

      item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
      });

      item.addEventListener('dragend', function(e) {
        this.style.opacity = '1';
        const items = columnSelection.querySelectorAll('.column-item');
        items.forEach(item => {
          item.classList.remove('drag-over');
        });
      });

      item.addEventListener('dragover', function(e) {
        if (e.preventDefault) {
          e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        this.classList.add('drag-over');
        dragOverElement = this;
        return false;
      });

      item.addEventListener('dragenter', function(e) {
        this.classList.add('drag-over');
      });

      item.addEventListener('dragleave', function(e) {
        this.classList.remove('drag-over');
      });

      item.addEventListener('drop', function(e) {
        if (e.stopPropagation) {
          e.stopPropagation();
        }

        if (draggedElement !== this) {
          const allItems = Array.from(columnSelection.querySelectorAll('.column-item'));
          const draggedIndex = allItems.indexOf(draggedElement);
          const targetIndex = allItems.indexOf(this);

          if (draggedIndex < targetIndex) {
            columnSelection.insertBefore(draggedElement, this.nextSibling);
          } else {
            columnSelection.insertBefore(draggedElement, this);
          }
        }

        this.classList.remove('drag-over');
        dragOverElement = null;
        return false;
      });
    });

    dragInitialized = true;
  }

  // Add event listeners
  document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.getElementById('printBtn');
    if (printBtn) {
      printBtn.addEventListener('click', printTable);
    }

    const columnBtn = document.getElementById('columnBtn');
    if (columnBtn) {
      columnBtn.addEventListener('click', openColumnModal);
    }
  });
