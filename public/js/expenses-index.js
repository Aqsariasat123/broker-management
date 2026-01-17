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

  // Open expense details - opens edit modal
  function openExpenseDetails(id) {
    openExpenseModal('edit', id);
  }

  // Populate expense details view
  function populateExpenseDetails(expense) {
    const content = document.getElementById('expenseDetailsContent');
    if (!content) return;

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">EXPENSE DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Expense ID</span>
            <div class="detail-value">${expense.expense_id || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Payee</span>
            <div class="detail-value">${expense.payee || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Paid</span>
            <div class="detail-value">${formatDate(expense.date_paid)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Amount Paid</span>
            <div class="detail-value">${formatNumber(expense.amount_paid)}</div>
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
            <div class="detail-value">${expense.description || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Category</span>
            <div class="detail-value">${expense.expense_category ? expense.expense_category.name : (expense.category || '-')}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Mode Of Payment</span>
            <div class="detail-value">${expense.mode_of_payment ? expense.mode_of_payment.name : (expense.modeOfPayment ? expense.modeOfPayment.name : '-')}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">NOTES</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Expense Notes</span>
            <textarea class="detail-value" style="min-height:120px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${expense.expense_notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    const col4 = `
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open expense page (Add or Edit)
  async function openExpensePage(mode) {
    if (mode === 'add') {
      openExpenseForm('add');
    } else {
      if (currentExpenseId) {
        openEditExpense(currentExpenseId);
      }
    }
  }

  // Add Expense Button - Open Modal
  document.getElementById('addExpenseBtn').addEventListener('click', () => openExpenseModal('add'));
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditExpense(id) {
    try {
      const res = await fetch(`/expenses/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const expense = await res.json();
      currentExpenseId = id;
      openExpenseForm('edit', expense);
    } catch (e) {
      console.error(e);
      alert('Error loading expense data');
    }
  }

  function openExpenseForm(mode, expense = null) {
    // Clone form from modal
    const modalForm = document.getElementById('expenseModal').querySelector('form');
    const pageForm = document.getElementById('expensePageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('expensePageFormMethod');
    const deleteBtn = document.getElementById('expenseDeleteBtn');
    const editBtn = document.getElementById('editExpenseFromPageBtn');
    const closeBtn = document.getElementById('closeExpensePageBtn');
    const closeFormBtn = document.getElementById('closeExpenseFormBtn');

    if (mode === 'add') {
      document.getElementById('expensePageTitle').textContent = 'Add Expense';
      document.getElementById('expensePageName').textContent = '';
      pageForm.action = expensesStoreRoute;
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const expenseName = expense.expense_id || 'Unknown';
      document.getElementById('expensePageTitle').textContent = 'Edit Expense';
      document.getElementById('expensePageName').textContent = expenseName;
      pageForm.action = `/expenses/${currentExpenseId}`;
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

      // Populate form fields
      if (expense.payee) document.getElementById('payee').value = expense.payee;
      if (expense.date_paid) document.getElementById('date_paid').value = expense.date_paid ? (typeof expense.date_paid === 'string' ? expense.date_paid.substring(0,10) : expense.date_paid) : '';
      if (expense.amount_paid) document.getElementById('amount_paid').value = expense.amount_paid;
      if (expense.description) document.getElementById('description').value = expense.description;
      if (expense.category_id) document.getElementById('category_id').value = expense.category_id;
      if (expense.mode_of_payment_id) document.getElementById('mode_of_payment_id').value = expense.mode_of_payment_id;
      if (expense.receipt_no) document.getElementById('receipt_no').value = expense.receipt_no;
      if (expense.expense_notes) document.getElementById('expense_notes').value = expense.expense_notes;
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const expensePageView = document.getElementById('expensePageView');
    expensePageView.style.display = 'block';
    expensePageView.classList.add('show');
    document.getElementById('expenseDetailsPageContent').style.display = 'none';
    document.getElementById('expenseFormPageContent').style.display = 'block';
  }

  function closeExpensePageView() {
    const expensePageView = document.getElementById('expensePageView');
    expensePageView.classList.remove('show');
    expensePageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('expenseDetailsPageContent').style.display = 'none';
    document.getElementById('expenseFormPageContent').style.display = 'none';
    currentExpenseId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editExpenseFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentExpenseId) {
        openEditExpense(currentExpenseId);
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

  function deleteExpense() {
    if (!currentExpenseId) return;
    if (!confirm('Delete this expense?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/expenses/${currentExpenseId}`;
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

  // Open Expense Modal
  async function openExpenseModal(mode, expenseId = null) {
    const modal = document.getElementById('expenseModal');
    const form = document.getElementById('expenseForm');
    const formMethod = document.getElementById('expenseFormMethod');
    const title = document.getElementById('expenseModalTitle');
    const deleteBtn = document.getElementById('expenseDeleteBtnModal');
    
    if (!modal || !form || !formMethod || !title) {
      console.error('Required modal elements not found');
      alert('Error: Modal elements not found');
      return;
    }
    
    if (mode === 'add') {
      title.textContent = 'Add Expense';
      form.action = expensesStoreRoute;
      formMethod.innerHTML = '';
      if (deleteBtn) deleteBtn.style.display = 'none';
      form.reset();
      currentExpenseId = null;
      // Reset receipt preview
      document.getElementById('selectedReceiptPreview').style.display = 'none';
      document.getElementById('receiptFileInput').value = '';
    } else if (mode === 'edit' && expenseId) {
      try {
        const res = await fetch(`/expenses/${expenseId}/edit`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        if (!res.ok) throw new Error('Network error');
        const expense = await res.json();
        currentExpenseId = expenseId;
        
        title.textContent = 'Edit Expense';
        form.action = `/expenses/${expenseId}`;
        formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        if (deleteBtn) deleteBtn.style.display = canDeleteExpense ? 'inline-block' : 'none';
        
        // Populate form fields
        document.getElementById('payee').value = expense.payee || '';
        document.getElementById('date_paid').value = expense.date_paid ? (typeof expense.date_paid === 'string' ? expense.date_paid.substring(0,10) : expense.date_paid) : '';
        document.getElementById('amount_paid').value = expense.amount_paid || '';
        document.getElementById('category_id').value = expense.category_id || (expense.expense_category ? expense.expense_category.id : '');
        document.getElementById('description').value = expense.description || '';
        document.getElementById('mode_of_payment_id').value = expense.mode_of_payment_id || (expense.mode_of_payment ? expense.mode_of_payment.id : (expense.modeOfPayment ? expense.modeOfPayment.id : ''));
        document.getElementById('receipt_no').value = expense.receipt_no || '';
        document.getElementById('expense_notes').value = expense.expense_notes || '';
        
        // Show existing receipt preview if available (from documents table)
        const previewDiv = document.getElementById('selectedReceiptPreview');
        const receiptName = document.getElementById('selectedReceiptName');
        const imagePreview = document.getElementById('selectedReceiptImagePreview');
        
        if (expense.documents && expense.documents.length > 0) {
          const receipt = expense.documents.find(doc => doc.type === 'receipt') || expense.documents[0];
          if (receipt && receipt.file_path) {
            previewDiv.style.display = 'block';
            const fileName = receipt.file_path.split('/').pop();
            receiptName.textContent = receipt.name || fileName;
            // Show link to view receipt
            imagePreview.innerHTML = `
              <a href="/storage/${receipt.file_path}" target="_blank" style="color:#007bff; text-decoration:underline; font-size:12px;">
                View Current Receipt
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
        alert('Error loading expense data');
        return;
      }
    }
    
    // Show the modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeExpenseModal() {
    const modal = document.getElementById('expenseModal');
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = '';
      currentExpenseId = null;
    }
  }

  // Close modal when clicking outside
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('expenseModal');
    if (modal) {
      modal.addEventListener('click', function(e) {
        if (e.target === this) {
          closeExpenseModal();
        }
      });
    }
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const expenseModal = document.getElementById('expenseModal');
        const receiptModal = document.getElementById('receiptUploadModal');
        if (expenseModal && expenseModal.classList.contains('show')) {
          closeExpenseModal();
        } else if (receiptModal && receiptModal.classList.contains('show')) {
          closeReceiptUploadModal();
        }
      }
    });

    // Close receipt upload modal when clicking outside
    const receiptModal = document.getElementById('receiptUploadModal');
    if (receiptModal) {
      receiptModal.addEventListener('click', function(e) {
        if (e.target === this) {
          closeReceiptUploadModal();
        }
      });
    }
  });

  // Receipt Upload Modal
  function openReceiptUploadModal() {
    const modal = document.getElementById('receiptUploadModal');
    if (modal) {
      // If in edit mode and expense has a receipt, show existing receipt
      if (currentExpenseId) {
        // Fetch expense to check for existing receipt
        fetch(`/expenses/${currentExpenseId}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(res => res.json())
        .then(expense => {
          const existingPreview = document.getElementById('existingReceiptPreview');
          const existingPreviewContent = document.getElementById('existingReceiptPreviewContent');
          if (expense.documents && expense.documents.length > 0) {
            const receipt = expense.documents.find(doc => doc.type === 'receipt') || expense.documents[0];
            if (receipt && receipt.file_path) {
              existingPreview.style.display = 'block';
              existingPreviewContent.innerHTML = `
                <a href="/storage/${receipt.file_path}" target="_blank" style="color:#007bff; text-decoration:underline; font-size:12px;">
                  View Current Receipt
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
          console.error('Error loading expense:', err);
        });
      } else {
        // Add mode - no existing receipt
        document.getElementById('existingReceiptPreview').style.display = 'none';
      }
      
      // Reset file input
      document.getElementById('receiptFile').value = '';
      document.getElementById('receiptPreview').style.display = 'none';
      
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeReceiptUploadModal() {
    const modal = document.getElementById('receiptUploadModal');
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = '';
      document.getElementById('receiptFile').value = '';
      document.getElementById('receiptPreview').style.display = 'none';
    }
  }

  function handleReceiptFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    const preview = document.getElementById('receiptPreview');
    const previewContent = document.getElementById('receiptPreviewContent');
    
    // Show preview
    preview.style.display = 'block';
    
    // Check if it's an image
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        previewContent.innerHTML = `
          <img src="${e.target.result}" style="max-width:100%; max-height:300px; border:1px solid #ddd; border-radius:4px;" alt="Receipt preview">
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

  function confirmReceiptSelection() {
    const fileInput = document.getElementById('receiptFile');
    const hiddenInput = document.getElementById('receiptFileInput');
    const previewDiv = document.getElementById('selectedReceiptPreview');
    const receiptName = document.getElementById('selectedReceiptName');
    const imagePreview = document.getElementById('selectedReceiptImagePreview');
    
    if (!fileInput.files || !fileInput.files[0]) {
      alert('Please select a file first');
      return;
    }

    const file = fileInput.files[0];
    
    // Copy file to hidden input in expense form
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    hiddenInput.files = dataTransfer.files;
    
    // Show preview in expense form
    receiptName.textContent = file.name;
    previewDiv.style.display = 'block';
    
    // Show image preview if it's an image
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        imagePreview.innerHTML = `<img src="${e.target.result}" style="max-width:100%; max-height:150px; border:1px solid #ddd; border-radius:4px;" alt="Receipt preview">`;
      };
      reader.readAsDataURL(file);
    } else {
      imagePreview.innerHTML = '';
    }
    
    // Close modal
    closeReceiptUploadModal();
  }

  function removeSelectedReceipt() {
    document.getElementById('receiptFileInput').value = '';
    document.getElementById('selectedReceiptPreview').style.display = 'none';
    document.getElementById('selectedReceiptName').textContent = '';
    document.getElementById('selectedReceiptImagePreview').innerHTML = '';
  }

  // Handle expense form submission to include receipt
  document.addEventListener('DOMContentLoaded', function() {
    const expenseForm = document.getElementById('expenseForm');
    if (expenseForm) {
      expenseForm.addEventListener('submit', function(e) {
        // Form will submit normally with receipt file included if selected
        // The receipt will be saved along with the expense in the store/update method
      });
    }
  });

  // Update table row click to open edit modal
  function openExpenseDetails(id) {
    openExpenseModal('edit', id);
  }

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
