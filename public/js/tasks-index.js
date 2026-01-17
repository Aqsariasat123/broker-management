
    let currentTaskId = null;
    // Data initialized in Blade template

    // Format date helper function
    function formatDate(dateStr) {
      if (!dateStr) return '-';
      const date = new Date(dateStr);
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
    }


    // Initialize column checkboxes
    function initializeColumnCheckboxes() {
      const checkboxes = document.querySelectorAll('.column-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectedColumns.includes(checkbox.value);
      });
    }

    // Add Task Button - moved to DOMContentLoaded to ensure button exists
    // Column Button - moved to DOMContentLoaded to ensure button exists


    async function openEditTask(id) {
      try {
        const res = await fetch(`/tasks/${id}/edit`, { 
          headers: { 
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          } 
        });
        if (!res.ok) throw new Error('Network error');
        const task = await res.json();
        currentTaskId = id;
        openModalWithTask('edit', task);
      } catch (e) {
        console.error(e);
        alert('Error loading task data');
      }
    }
    
    // Open modal with task data for editing
    function openModalWithTask(mode, task) {
      const modal = document.getElementById('taskModal');
      if (!modal) {
        console.error('Modal not found');
        return;
      }
      
      const title = document.getElementById('modalTitle');
      const form = document.getElementById('taskForm');
      const deleteBtn = document.getElementById('deleteBtn');
      const formMethod = document.getElementById('formMethod');
      
      if (mode === 'edit' && task) {
        if (title) title.textContent = 'View/Edit Task';
        if (form) {
          form.action = `/tasks/${currentTaskId}`;
          form.method = 'POST';
        }
        if (formMethod) formMethod.innerHTML = '<input type="hidden" name="_method" value="POST">';
        if (deleteBtn) deleteBtn.style.display = 'block';
        
        // Populate form fields
        const fields = ['category','item','description','name','contact_no','due_date','due_time','date_in','assignee','task_status','date_done','task_notes','frequency','rpt_date','rpt_stop_date'];
        fields.forEach(id => {
          const el = form.querySelector(`#${id}`);
          if (!el) return;
          if (el.type === 'checkbox') {
            el.checked = !!task[id];
          } else if (el.type === 'date') {
            // Handle date fields - format to YYYY-MM-DD
            if (task[id]) {
              let dateValue = task[id];
              if (typeof dateValue === 'string') {
                // If it's already in YYYY-MM-DD format, use it directly
                if (dateValue.match(/^\d{4}-\d{2}-\d{2}/)) {
                  el.value = dateValue.substring(0, 10);
                } else {
                  // Try to parse and format the date
                  try {
                    const date = new Date(dateValue);
                    if (!isNaN(date.getTime())) {
                      el.value = date.toISOString().substring(0, 10);
                    }
                  } catch (e) {
                    el.value = '';
                  }
                }
              } else {
                el.value = '';
              }
            } else {
              el.value = '';
            }
          } else if (el.type === 'time') {
            // Handle time fields
            if (task[id]) {
              let timeValue = task[id];
              if (typeof timeValue === 'string') {
                // If it's already in HH:MM format, use it directly
                if (timeValue.match(/^\d{2}:\d{2}/)) {
                  el.value = timeValue.substring(0, 5);
                } else {
                  el.value = timeValue;
                }
              } else {
                el.value = '';
              }
            } else {
              el.value = '';
            }
          } else if (el.tagName === 'SELECT') {
            el.value = task[id] ?? '';
          } else {
            el.value = task[id] ?? '';
          }
        });
        
        // Handle repeat checkbox
        const repeatCheckbox = form.querySelector('#repeat');
        if (repeatCheckbox) {
          repeatCheckbox.checked = !!task.repeat;
        }
        
        // Sync item to description if needed
        const itemField = form.querySelector('#item');
        const descField = form.querySelector('#description');
        if (itemField && descField && !task.description && task.item) {
          descField.value = task.item;
        } else if (itemField && descField && !descField.value && itemField.value) {
          descField.value = itemField.value;
        }
      }
      
      // prevent body scrollbar when modal open
      document.body.style.overflow = 'hidden';
      modal.classList.add('show');
      
      // Setup event listeners for modal form
      setTimeout(() => {
        setupFormEventListeners(modal);
      }, 100);
    }

    // Setup event listeners for form dropdowns
    function setupFormEventListeners(container) {
      if (!container) return;
      
      // Handle name dropdown change to auto-fill contact_no
      const nameSelect = container.querySelector('#name');
      const contactNoInput = container.querySelector('#contact_no');
      if (nameSelect && contactNoInput) {
        nameSelect.addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          if (selectedOption && selectedOption.dataset.contactNo) {
            contactNoInput.value = selectedOption.dataset.contactNo;
          }
        });
      }
      
      // Sync item to description when item changes (since description is required)
      const itemInput = container.querySelector('#item');
      const descInput = container.querySelector('#description');
      if (itemInput && descInput) {
        itemInput.addEventListener('input', function() {
          if (!descInput.value || descInput.value === itemInput.value) {
            descInput.value = this.value;
          }
        });
      }
    }


    // Edit button from details page - moved to DOMContentLoaded

    // Legacy editTask function for backward compatibility
    async function editTask(taskId) {
      openEditTask(taskId);
    }

    // Open Task Modal
    function openModal(mode) {
      const modal = document.getElementById('taskModal');
      if (!modal) {
        console.error('Modal not found');
        return;
      }
      
      const title = document.getElementById('modalTitle');
      const form = document.getElementById('taskForm');
      const deleteBtn = document.getElementById('deleteBtn');
      const formMethod = document.getElementById('formMethod');
      
      if (mode === 'add') {
        if (title) title.textContent = 'Add Task';
        if (form) {
          form.action = tasksStoreRoute;
          form.method = 'POST';
          form.reset();
        }
        if (formMethod) formMethod.innerHTML = '';
        if (deleteBtn) deleteBtn.style.display = 'none';
        currentTaskId = null;
      } else {
        if (title) title.textContent = 'View/Edit Task';
        if (form) {
          form.action = `/tasks/${currentTaskId}`;
          form.method = 'POST';
        }
        if (formMethod) formMethod.innerHTML = '<input type="hidden" name="_method" value="POST">';
        if (deleteBtn) deleteBtn.style.display = 'block';
      }
      
      // prevent body scrollbar when modal open
      document.body.style.overflow = 'hidden';
      modal.classList.add('show');
      
      // Setup event listeners for modal form
      setTimeout(() => {
        setupFormEventListeners(modal);
      }, 100);
    }

    // Close Task Modal
    function closeModal() {
      document.getElementById('taskModal').classList.remove('show');
      currentTaskId = null;
      // restore body scrollbar
      document.body.style.overflow = '';
    }

    // Open Column Modal
    function openColumnModal() {
      initializeColumnCheckboxes();
      // prevent body scrollbar when modal open
      document.body.style.overflow = 'hidden';
      document.getElementById('columnModal').classList.add('show');
         setTimeout(initDragAndDrop, 100);
    }

    // Close Column Modal
    function closeColumnModal() {
      document.getElementById('columnModal').classList.remove('show');
      // restore body scrollbar
      document.body.style.overflow = '';
    }

    // Select All Columns
    function selectAllColumns() {
      const checkboxes = document.querySelectorAll('.column-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = true;
      });
    }

    // Deselect All Columns
    function deselectAllColumns() {
      const mandatoryFields = mandatoryColumns;

        document.querySelectorAll('.column-checkbox').forEach(cb => {
          // Don't uncheck mandatory fields
          if (!mandatoryFields.includes(cb.value)) {
            cb.checked = false;
          }
        });
    }

    // Save Column Settings
    function saveColumnSettings() {
      const mandatoryFields = mandatoryColumns;

      const items = Array.from(document.querySelectorAll('#columnSelection .column-item, #columnSelection .column-item-vertical'));
    const order = items.map(item => item.dataset.column);
    const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n=>n.value);
    
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
    existing.forEach(e=>e.remove());
    
    // Add columns in the order they appear in the DOM (after drag and drop)
    orderedChecked.forEach(c => {
      const i = document.createElement('input'); 
      i.type='hidden'; 
      i.name='columns[]'; 
      i.value=c; 
      form.appendChild(i);
    });
    
    form.submit();
    
    }

    // Drag and drop functionality
    let draggedElement = null;
    let dragOverElement = null;

    function initDragAndDrop() {
      const columnSelection = document.getElementById('columnSelection');
      if (!columnSelection) return;
      const columnItems = columnSelection.querySelectorAll('.column-item, .column-item-vertical');
      columnItems.forEach(item => {
        // Skip if already initialized
        if (item.dataset.dragInitialized === 'true') {
          return;
        }
        item.dataset.dragInitialized = 'true';
        item.setAttribute('draggable', 'true');
        
        // Prevent checkbox from interfering with drag
        const checkbox = item.querySelector('.column-checkbox');
        if (checkbox) {
          checkbox.addEventListener('mousedown', function(e) {
            e.stopPropagation();
          });
          checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
          });
        }
        
        // Prevent label from interfering with drag
        const label = item.querySelector('label');
        if (label) {
          label.addEventListener('mousedown', function(e) {
            // Only prevent if clicking on the label text, not the checkbox
            if (e.target === label) {
              e.preventDefault();
            }
          });
        }
        
        item.addEventListener('dragstart', function(e) {
          draggedElement = this;
          this.classList.add('dragging');
          e.dataTransfer.effectAllowed = 'move';
          e.dataTransfer.setData('text/html', this.outerHTML);
          e.dataTransfer.setData('text/plain', this.querySelector('.column-checkbox').value);
        });
        
        item.addEventListener('dragend', function(e) {
          this.classList.remove('dragging');
          // Remove drag-over from all items
          columnItems.forEach(i => i.classList.remove('drag-over'));
          if (dragOverElement) {
            dragOverElement.classList.remove('drag-over');
            dragOverElement = null;
          }
          draggedElement = null;
        });
        
        item.addEventListener('dragover', function(e) {
          e.preventDefault();
          e.stopPropagation();
          e.dataTransfer.dropEffect = 'move';
          
          if (draggedElement && this !== draggedElement) {
            // Remove drag-over class from previous element
            if (dragOverElement && dragOverElement !== this) {
              dragOverElement.classList.remove('drag-over');
            }
            
            // Add drag-over class to current element
            this.classList.add('drag-over');
            dragOverElement = this;
            
            const rect = this.getBoundingClientRect();
            const midpoint = rect.top + (rect.height / 2);
            const next = e.clientY > midpoint;
            
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
        
        item.addEventListener('dragenter', function(e) {
          e.preventDefault();
          if (draggedElement && this !== draggedElement) {
            this.classList.add('drag-over');
          }
        });
        
        item.addEventListener('dragleave', function(e) {
          // Only remove if we're actually leaving the element
          if (!this.contains(e.relatedTarget)) {
            this.classList.remove('drag-over');
            if (dragOverElement === this) {
              dragOverElement = null;
            }
          }
        });
        
        item.addEventListener('drop', function(e) {
          e.preventDefault();
          e.stopPropagation();
          this.classList.remove('drag-over');
          dragOverElement = null;
          return false;
        });
      });
    }

    // Delete Task
    function deleteTask() {
      if (!currentTaskId) return;
      if (!confirm('Are you sure you want to delete this task?')) return;
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/tasks/${currentTaskId}`;
      const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value = csrfToken; form.appendChild(csrf);
      const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
      document.body.appendChild(form);
      form.submit();
    }

    // Set overdue filter if parameter exists + attach overdue button handler safely after DOM ready
    document.addEventListener('DOMContentLoaded', function() {
      // initialize column checkboxes and other startup code
      initializeColumnCheckboxes();
      
      // Add Task Button
      const addTaskBtn = document.getElementById('addTaskBtn');
      if (addTaskBtn) {
        addTaskBtn.addEventListener('click', function() {
          openModal('add');
        });
      }
      
      // Column Button
      const columnBtn = document.getElementById('columnBtn');
      if (columnBtn) {
        columnBtn.addEventListener('click', function() {
          openColumnModal();
        });
      }
      
      // Setup event listeners for modal form on page load
      setupFormEventListeners(document.getElementById('taskModal'));
      
      // Handle form submission to ensure description is set
      const taskForm = document.getElementById('taskForm');
      if (taskForm) {
        taskForm.addEventListener('submit', function(e) {
          const itemField = this.querySelector('#item');
          const descField = this.querySelector('#description');
          if (descField && (!descField.value || descField.value.trim() === '') && itemField && itemField.value) {
            descField.value = itemField.value;
          }
          // Ensure description is not empty (required field)
          if (descField && (!descField.value || descField.value.trim() === '')) {
            descField.value = 'Task';
          }
        });
      }

      // Print button handler
      const printBtn = document.getElementById('printBtn');
      if (printBtn) {
        printBtn.addEventListener('click', function() {
          printTable();
        });
      }

      // determine whether overdue param is active (supports '1' or 'true')
      const urlParams = new URLSearchParams(window.location.search);
      const overdueActive = urlParams.get('filter') === 'overdue';

      // Filter toggle handler
      const filterToggle = document.getElementById('filterToggle');
      const filterToggleLabel = document.getElementById('filterToggleLabel');
      if (filterToggle) {
        filterToggle.checked = overdueActive;
        if (filterToggleLabel) {
          filterToggleLabel.textContent = overdueActive ? 'ON' : 'OFF';
        }

        filterToggle.addEventListener('change', function(e) {
          const u = new URL(window.location.href);
          if (this.checked) {
            u.searchParams.set('filter', 'overdue');
            if (filterToggleLabel) filterToggleLabel.textContent = 'ON';
          } else {
            u.searchParams.delete('filter');
            if (filterToggleLabel) filterToggleLabel.textContent = 'OFF';
          }
          window.location.href = u.toString();
        });
      }

      // Overdue Only button handler
      const overdueBtn = document.getElementById('overdueOnly');
      if (overdueBtn) {
        if (overdueActive) {
          overdueBtn.style.background = '#000';
          overdueBtn.style.color = '#fff';
        }

        overdueBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const u = new URL(window.location.href);
          u.searchParams.set('filter', 'overdue');
          window.location.href = u.toString();
        });
      }

      // List ALL button handler
      const listAllBtn = document.getElementById('listAllBtn');
      if (listAllBtn) {
        listAllBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const u = new URL(window.location.href);
          u.searchParams.delete('filter');
          window.location.href = u.toString();
        });
      }
    });
  function handleBack() {
      // Get params from current URL
      const params = new URLSearchParams(window.location.search);
      const fromCalendar = params.get('from_calendar');

      // If coming from calendar via List button, go back to calendar
      if (fromCalendar === '1') {
          window.location.href = "/calendar?filter=tasks";
      }
      // Otherwise use browser back or go to dashboard
      else if (window.history.length > 1) {
          window.history.back();
      }
      else {
          window.location.href = "/dashboard";
      }
  }
  function printTable() {
    const table = document.getElementById('tasksTable');
    if (!table) return;
    
    // Get table headers - preserve order
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    headerCells.forEach(th => {
      let headerText = '';
      // Get text, excluding filter input
      const clone = th.cloneNode(true);
      const filterInput = clone.querySelector('.column-filter');
      if (filterInput) filterInput.remove();
      headerText = clone.textContent.trim();
      // Handle bell icon column
      if (clone.querySelector('svg')) {
        headerText = '🔔'; // Bell icon
      }
      if (headerText) {
        headers.push(headerText);
      }
    });
    
    // Get table rows data
    const rows = [];
    const tableRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    tableRows.forEach(row => {
      if (row.style.display === 'none') return; // Skip hidden rows
      
      const cells = [];
      const rowCells = row.querySelectorAll('td');
      rowCells.forEach((cell) => {
        let cellContent = '';
        
        // Handle notification column (bell-cell)
        if (cell.classList.contains('bell-cell')) {
          const statusIndicator = cell.querySelector('.status-indicator');
          if (statusIndicator) {
            if (statusIndicator.classList.contains('expired')) {
              cellContent = '●'; // Red filled circle for overdue
            } else if (cell.classList.contains('expiring')) {
              cellContent = '○'; // Yellow/orange border for expiring
            } else {
              cellContent = ''; // No indicator
            }
          } else {
            cellContent = '';
          }
        } 
        // Handle action column
        else if (cell.classList.contains('action-cell')) {
          const expandIcon = cell.querySelector('.action-expand');
          const clockIcon = cell.querySelector('.action-clock');
          const ellipsis = cell.querySelector('.action-ellipsis');
          const icons = [];
          if (expandIcon) icons.push('⤢');
          if (clockIcon) icons.push('🕐');
          if (ellipsis) icons.push('⋯');
          cellContent = icons.join(' ');
        } 
        // Handle checkbox cells
        else if (cell.classList.contains('checkbox-cell')) {
          const checkbox = cell.querySelector('input[type="checkbox"]');
          cellContent = checkbox && checkbox.checked ? '✓' : '';
        } 
        // Handle regular cells
        else {
          // Get text content, handling links
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
    
    // Escape HTML to prevent XSS and syntax issues
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
    
    // Create print window with minimal delay
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    const printHTML = '<!DOCTYPE html>' +
      '<html>' +
      '<head>' +
      '<title>Clients - Print</title>' +
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
  