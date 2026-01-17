// ---------------------------------
// 1. INITIAL STATE & URL PARAMS
// ---------------------------------
const urlParams = new URLSearchParams(window.location.search);
const today = new Date();

// Filter & Date Range
let currentFilter = urlParams.get('filter') || 'all';
const dateRange = urlParams.get('date_range') || 'month';

// Calendar state
let currentYear = today.getFullYear();
let currentMonth = today.getMonth(); // 0-indexed

// Lock navigation only for 'today' and 'week'
const isFixedRange = ['today', 'week'].includes(dateRange);

// ---------------------------------
// 2. DATE RANGE HANDLING
// ---------------------------------
switch (dateRange) {
    case 'today':
    case 'week':
    case 'month':
        currentYear = today.getFullYear();
        currentMonth = today.getMonth();
        break;
    case 'quarter':
        const quarter = Math.floor(today.getMonth() / 3);
        currentYear = today.getFullYear();
        currentMonth = quarter * 3;
        break;
    case 'year':
        currentYear = today.getFullYear();
        currentMonth = 0;
        break;
    default:
        if (dateRange.startsWith('year-')) {
            currentYear = parseInt(dateRange.replace('year-', ''), 10);
            currentMonth = 0;
        }
        break;
}

// ---------------------------------
// 3. CONSTANTS
// ---------------------------------
let eventsData = {};
const monthNames = [
    'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE',
    'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
];

// ---------------------------------
// 4. FETCH EVENTS
// ---------------------------------
async function fetchEvents() {
    try {
        const response = await fetch(
            `${calendarEventsRoute}?year=${currentYear}&month=${currentMonth + 1}&filter=${currentFilter}&date_range=${dateRange}`
        );
        eventsData = await response.json();
    } catch (error) {
        console.error('Error fetching events:', error);
        eventsData = {};
    }
    generateCalendar();
}

// ---------------------------------
// 5. UPDATE HEADER + LOAD EVENTS
// ---------------------------------
function updateDisplay() {
    document.getElementById('current-year').textContent = currentYear;
    document.getElementById('current-month').textContent = monthNames[currentMonth];
    fetchEvents();
}

// ---------------------------------
// 6. GENERATE CALENDAR GRID
// ---------------------------------
function generateCalendar() {
    const calendarBody = document.getElementById('calendar-body');
    calendarBody.innerHTML = '';

    const firstDay = new Date(currentYear, currentMonth, 1);
    let startDay = firstDay.getDay(); // Sunday=0
    startDay = startDay === 0 ? 6 : startDay - 1; // Monday=0

    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
    const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
    const daysInPrevMonth = new Date(prevYear, prevMonth + 1, 0).getDate();

    let dayCounter = 1;
    let nextMonthDay = 1;
    let prevMonthDay = daysInPrevMonth - startDay + 1;

    for (let week = 0; week < 6; week++) {
        const row = document.createElement('tr');

        for (let day = 0; day < 7; day++) {
            const cell = document.createElement('td');
            const dayNumber = document.createElement('div');
            dayNumber.className = 'day-number';

            let dateKey;

            if (week === 0 && day < startDay) {
                // Previous month
                cell.classList.add('outside-month');
                dayNumber.classList.add('outside');
                dayNumber.textContent = prevMonthDay++;
                dateKey = `${prevYear}-${String(prevMonth + 1).padStart(2, '0')}-${String(dayNumber.textContent).padStart(2, '0')}`;
            } else if (dayCounter > daysInMonth) {
                // Next month
                cell.classList.add('outside-month');
                dayNumber.classList.add('outside');
                dayNumber.textContent = nextMonthDay++;
                const nextMonth = (currentMonth + 1) % 12;
                const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
                dateKey = `${nextYear}-${String(nextMonth + 1).padStart(2, '0')}-${String(dayNumber.textContent).padStart(2, '0')}`;
            } else {
                // Current month
                dayNumber.textContent = dayCounter;
                dateKey = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(dayCounter).padStart(2, '0')}`;
                dayCounter++;
            }

            // Append day number first
            cell.appendChild(dayNumber);

            // Add events for this day
            const events = eventsData[dateKey] || [];
            events.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = `event ${event.class || event.type}`;
                eventDiv.textContent = event.text;
                eventDiv.title = event.text;
                
                // Add cursor pointer
                eventDiv.style.cursor = 'pointer';
                
                // Store event data
                eventDiv.dataset.eventType = event.type;
                eventDiv.dataset.eventId = event.id || '';
                
                // Add click handler based on event type
                eventDiv.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    console.log('Event clicked:', event.type, event.id);
                    
                    // Handle different event types with modal
                    if (event.type === 'task') {
                        openItemModal('task', event.id);
                    } else if (event.type === 'follow-up') {
                        openItemModal('follow-up', event.id);
                    } else if (event.type === 'renewal') {
                        openItemModal('renewal', event.id);
                    } else if (event.type === 'instalment') {
                        window.location.href = `/payment-plans/${event.id}`;
                    } else if (event.type === 'birthday') {
                        window.location.href = `/clients/${event.id}`;
                    }
                });
                
                cell.appendChild(eventDiv);
            });

            row.appendChild(cell);
        }

        calendarBody.appendChild(row);
    }
}

// ---------------------------------
// 6.2 OPEN ITEM MODAL (UNIVERSAL)
// ---------------------------------
async function openItemModal(itemType, itemId = null) {
    if (!itemId) {
        console.error('No item ID provided');
        return;
    }
    
    console.log('Opening modal for:', itemType, itemId);
    
    try {
        let endpoint = '';
        
        // Determine endpoint based on type
        if (itemType === 'task') {
            endpoint = `/tasks/${itemId}/edit`;
        } else if (itemType === 'follow-up') {
            endpoint = `/followups/${itemId}/edit`;
        } else if (itemType === 'renewal') {
            endpoint = `/policies/${itemId}/edit`;
        }
        
        const res = await fetch(endpoint, { 
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            } 
        });
        
        if (!res.ok) {
            throw new Error('Failed to fetch item data');
        }
        
        const data = await res.json();
        console.log('Item data received:', data);
        
        currentTaskId = itemId;
        openModalWithData(itemType, data);
    } catch (e) {
        console.error('Error loading item:', e);
        alert('Error loading data. Please try again.');
    }
}

// ---------------------------------
// 6.3 OPEN MODAL WITH DATA (DYNAMIC)
// ---------------------------------
function openModalWithData(itemType, data) {
    const modal = document.getElementById('taskModal');
    if (!modal) {
        console.error('Modal not found in DOM!');
        return;
    }
    
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('taskForm');
    const deleteBtn = document.getElementById('deleteBtn');
    const formMethod = document.getElementById('formMethod');
    
    // Set modal title based on type
    if (title) {
        if (itemType === 'task') title.textContent = 'View/Edit Task';
        else if (itemType === 'follow-up') title.textContent = 'View/Edit Follow Up';
        else if (itemType === 'renewal') title.textContent = 'View/Edit Renewal';
    }
    
    // Set form action based on type
    if (form) {
        if (itemType === 'task') {
            form.action = `/tasks/${currentTaskId}`;
        } else if (itemType === 'follow-up') {
            form.action = `/followups/${currentTaskId}`;
        } else if (itemType === 'renewal') {
            form.action = `/policies/${currentTaskId}`;
        }
        form.method = 'POST';
    }
    
    // Use PUT method for update
    if (formMethod) {
        formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    }
    
    if (deleteBtn) {
        deleteBtn.style.display = 'block';
    }
    
    // Setup form fields based on type
    setupFormFields(itemType);
    
    // Populate form fields
    populateFormFields(form, data);
    
    // Show modal
    document.body.style.overflow = 'hidden';
    modal.style.display = 'flex';
    
    console.log('Modal opened successfully');
}

// ---------------------------------
// 6.4 SETUP FORM FIELDS BY TYPE
// ---------------------------------
function setupFormFields(itemType) {
    const form = document.getElementById('taskForm');
    if (!form) return;
    
    // Get all form rows
    const itemRow = form.querySelector('label[for="item"]')?.closest('.form-row-vertical');
    const assigneeRow = form.querySelector('label[for="assignee"]')?.closest('.form-row-vertical');
    const taskStatusRow = form.querySelector('label[for="task_status"]')?.closest('.form-row-vertical');
    const dateDoneRow = form.querySelector('label[for="date_done"]')?.closest('.form-row-vertical');
    const taskNotesRow = form.querySelector('label[for="task_notes"]')?.closest('.form-row-vertical');
    
    // Show all fields by default
    [itemRow, assigneeRow, taskStatusRow, dateDoneRow, taskNotesRow].forEach(row => {
        if (row) row.style.display = 'flex';
    });
    
    // Adjust based on type
    if (itemType === 'follow-up') {
        // Follow ups don't need item field usually
        if (itemRow) itemRow.style.display = 'none';
    } else if (itemType === 'renewal') {
        // Renewals may have different requirements
        // Adjust as needed based on your requirements
    }
}

// ---------------------------------
// 6.5 POPULATE FORM FIELDS
// ---------------------------------
function populateFormFields(form, data) {
    if (!form || !data) return;
    
    const fieldMappings = {
        'category': data.category,
        'item': data.item,
        'description': data.description,
        'name': data.name,
        'contact_no': data.contact_no,
        'due_date': data.due_date,
        'due_time': data.due_time,
        'date_in': data.date_in,
        'assignee': data.assignee,
        'task_status': data.task_status,
        'date_done': data.date_done,
        'task_notes': data.task_notes,
        'frequency': data.frequency,
        'rpt_date': data.rpt_date,
        'rpt_stop_date': data.rpt_stop_date
    };
    
    // Populate each field
    Object.keys(fieldMappings).forEach(fieldId => {
        const el = form.querySelector(`#${fieldId}`);
        if (!el) return;
        
        const value = fieldMappings[fieldId];
        
        if (el.type === 'date') {
            if (value) {
                try {
                    const dateStr = value.toString();
                    if (dateStr.match(/^\d{4}-\d{2}-\d{2}/)) {
                        el.value = dateStr.substring(0, 10);
                    } else {
                        const date = new Date(value);
                        if (!isNaN(date.getTime())) {
                            el.value = date.toISOString().substring(0, 10);
                        }
                    }
                } catch (e) {
                    el.value = '';
                }
            } else {
                el.value = '';
            }
        } else if (el.type === 'time') {
            if (value && typeof value === 'string') {
                el.value = value.substring(0, 5);
            } else {
                el.value = '';
            }
        } else if (el.type === 'checkbox') {
            el.checked = !!value;
        } else {
            el.value = value || '';
        }
    });
    
    // Handle repeat checkbox separately
    const repeatCheckbox = form.querySelector('#repeat');
    if (repeatCheckbox) {
        repeatCheckbox.checked = !!data.repeat;
    }
    
    // Sync item to description if needed
    const itemField = form.querySelector('#item');
    const descField = form.querySelector('#description');
    if (itemField && descField && !data.description && data.item) {
        descField.value = data.item;
    }
}

// ---------------------------------
// 6.6 CLOSE MODAL
// ---------------------------------
function closeModal() {
    const modal = document.getElementById('taskModal');
    if (modal) {
        modal.style.display = 'none';
    }
    const form = document.getElementById('taskForm');
    if (form) {
        form.reset();
    }
    currentTaskId = null;
    document.body.style.overflow = '';
}

// ---------------------------------
// 6.7 DELETE TASK/ITEM
// ---------------------------------
function deleteTask() {
    if (!currentTaskId) {
        alert('No item selected');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }
    
    const modal = document.getElementById('taskModal');
    const title = document.getElementById('modalTitle')?.textContent || '';
    
    let endpoint = `/tasks/${currentTaskId}`;
    if (title.includes('Follow Up')) {
        endpoint = `/followups/${currentTaskId}`;
    } else if (title.includes('Renewal')) {
        endpoint = `/policies/${currentTaskId}`;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = endpoint;
    
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

// ---------------------------------
// 6.8 FORM SUBMISSION (AJAX)
// ---------------------------------
document.addEventListener('DOMContentLoaded', function() {
    const taskForm = document.getElementById('taskForm');
    
    if (taskForm) {
        taskForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            console.log('Form submitted, starting AJAX request...');
            
            // Sync description with item if empty
            const itemField = this.querySelector('#item');
            const descField = this.querySelector('#description');
            
            if (descField && (!descField.value || descField.value.trim() === '') && itemField && itemField.value) {
                descField.value = itemField.value;
            }
            
            // Ensure description is not empty
            if (descField && (!descField.value || descField.value.trim() === '')) {
                descField.value = 'Task';
            }
            
            // Get form data
            const formData = new FormData(taskForm);
            const formAction = taskForm.action;
            
            console.log('Form action URL:', formAction);
            console.log('Current task ID:', currentTaskId);
            
            // Show loading
            const saveBtn = document.querySelector('.btn-save');
            const originalText = saveBtn.textContent;
            saveBtn.textContent = 'Saving...';
            saveBtn.disabled = true;
            
            try {
                // Make AJAX request
                const response = await fetch(formAction, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                console.log('Response received:', {
                    status: response.status,
                    ok: response.ok,
                    statusText: response.statusText,
                    headers: Object.fromEntries(response.headers.entries())
                });
                
                // Check content type
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                // Get response text first
                const responseText = await response.text();
                console.log('Response text (first 500 chars):', responseText.substring(0, 500));
                
                // Try to parse as JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                    console.log('Parsed JSON:', data);
                } catch (parseError) {
                    console.error('Failed to parse JSON:', parseError);
                    console.error('Response was:', responseText);
                    
                    // Show detailed error
                    throw new Error(
                        'Server returned invalid JSON response.\n\n' +
                        'Status: ' + response.status + '\n' +
                        'Content-Type: ' + contentType + '\n\n' +
                        'Response preview:\n' + responseText.substring(0, 500)
                    );
                }
                
                // Check if request was successful
                if (!response.ok) {
                    throw new Error(data.message || 'Server returned error: ' + response.status);
                }
                
                // Check data.success
                if (data.success) {
                    alert('Saved successfully!');
                    closeModal();
                    fetchEvents();
                } else {
                    throw new Error(data.message || 'Failed to save');
                }
                
            } catch (error) {
                console.error('Full error details:', error);
                
                // User-friendly error message
                let errorMessage = 'Error saving:\n\n';
                errorMessage += error.message;
                errorMessage += '\n\nPlease check the browser console for more details.';
                
                alert(errorMessage);
                
            } finally {
                // Reset button
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            }
        });
    }
    
    // Close modal on outside click
    const taskModal = document.getElementById('taskModal');
    if (taskModal) {
        taskModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
});

// ---------------------------------
// 7. NAVIGATION CONTROLS
// ---------------------------------
document.getElementById('year-prev').onclick = () => {
    if (isFixedRange) return;
    currentYear--;
    updateDisplay();
};
document.getElementById('year-next').onclick = () => {
    if (isFixedRange) return;
    currentYear++;
    updateDisplay();
};
document.getElementById('month-prev').onclick = () => {
    if (isFixedRange) return;
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateDisplay();
};
document.getElementById('month-next').onclick = () => {
    if (isFixedRange) return;
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    updateDisplay();
};
document.getElementById('today-btn').onclick = () => {
    currentYear = today.getFullYear();
    currentMonth = today.getMonth();
    updateDisplay();
};
document.getElementById('prev-btn').onclick = () => {
    if (isFixedRange) return;
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateDisplay();
};
document.getElementById('next-btn').onclick = () => {
    if (isFixedRange) return;
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    updateDisplay();
};

// ---------------------------------
// 8. FILTER BUTTONS
// ---------------------------------
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.category-btn').forEach(b =>
            b.classList.remove('selected', 'tasks', 'follow-ups', 'renewals', 'instalments', 'birthdays')
        );
        document.querySelectorAll('.category-dropdown').forEach(d =>
            d.classList.remove('show')
        );

        currentFilter = this.dataset.filter;
        this.classList.add('selected');

        if (currentFilter !== 'all') {
            this.classList.add(currentFilter);
            const dropdown = document.getElementById(`dropdown-${currentFilter}`);
            if (dropdown) dropdown.classList.add('show');
        }

        fetchEvents();
    });
});

// ---------------------------------
// 9. PRESELECT FILTER FROM URL
// ---------------------------------
document.querySelectorAll('.category-btn').forEach(btn => {
    if (btn.dataset.filter === currentFilter) {
        btn.classList.add('selected');
        if (currentFilter !== 'all') {
            btn.classList.add(currentFilter);
            const dropdown = document.getElementById(`dropdown-${currentFilter}`);
            if (dropdown) dropdown.classList.add('show');
        }
    }
});

// ---------------------------------
// 10. LIST DROPDOWN CLICK HANDLERS
// ---------------------------------
document.querySelectorAll('.category-dropdown').forEach(dropdown => {
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        const filter = this.id.replace('dropdown-', '');

        // Calculate date range for current month view
        const startDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-01`;
        const lastDay = new Date(currentYear, currentMonth + 1, 0).getDate();
        const endDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;

        // Navigate to respective list page based on filter
        let url = '';
        switch(filter) {
            case 'tasks':
                url = `/tasks?from_calendar=1&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'follow-ups':
                url = `/followups?from_calendar=1&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'renewals':
                url = `/policies?from_calendar=1&filter=expiring&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'instalments':
                url = `/debit-notes?from_calendar=1&filter=overdue&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'birthdays':
                url = `/clients?from_calendar=1&filter=birthday_today&start_date=${startDate}&end_date=${endDate}`;
                break;
        }

        if (url) {
            window.location.href = url;
        }
    });
});

// ---------------------------------
// 11. INIT
// ---------------------------------
updateDisplay();
