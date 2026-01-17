// Followups Index JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Show all toggle
    document.getElementById('showAllToggle').addEventListener('change', function() {
        const params = new URLSearchParams(window.location.search);
        if (this.checked) {
            params.set('show_all', '1');
        } else {
            params.delete('show_all');
        }
        window.location.href = window.location.pathname + '?' + params.toString();
    });

    // Column button click
    document.getElementById('columnBtn').addEventListener('click', function() {
        document.getElementById('columnModal').style.display = 'flex';
    });

    // Initialize column drag and drop
    initColumnDragDrop();
});

// Column Modal Functions
function closeColumnModal() {
    document.getElementById('columnModal').style.display = 'none';
}

function saveColumnSettings() {
    const form = document.getElementById('columnForm');
    const items = document.querySelectorAll('#columnSelection .column-item-vertical');

    // Clear existing hidden inputs
    const existingInputs = form.querySelectorAll('input[name="columns[]"]');
    existingInputs.forEach(input => input.remove());

    // Add selected columns in order
    items.forEach(item => {
        const checkbox = item.querySelector('.column-checkbox');
        if (checkbox.checked || checkbox.disabled) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'columns[]';
            input.value = checkbox.value;
            form.appendChild(input);
        }
    });

    form.submit();
}

// Drag and Drop for Column Selection
function initColumnDragDrop() {
    const container = document.getElementById('columnSelection');
    if (!container) return;

    let draggedItem = null;

    container.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('column-item-vertical')) {
            draggedItem = e.target;
            e.target.style.opacity = '0.5';
        }
    });

    container.addEventListener('dragend', function(e) {
        if (e.target.classList.contains('column-item-vertical')) {
            e.target.style.opacity = '1';
            draggedItem = null;
            updateColumnNumbers();
        }
    });

    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        const afterElement = getDragAfterElement(container, e.clientY);
        if (draggedItem && afterElement == null) {
            container.appendChild(draggedItem);
        } else if (draggedItem && afterElement) {
            container.insertBefore(draggedItem, afterElement);
        }
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.column-item-vertical:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function updateColumnNumbers() {
        const items = container.querySelectorAll('.column-item-vertical');
        items.forEach((item, index) => {
            const numberSpan = item.querySelector('.column-number');
            if (numberSpan) {
                numberSpan.textContent = index + 1;
            }
        });
    }
}

// Back button handler
function handleBack() {
    if (fromCalendar === '1') {
        let url = '/calendar?filter=follow-ups';
        if (startDate) {
            const date = new Date(startDate);
            url += `&year=${date.getFullYear()}&month=${date.getMonth() + 1}`;
        }
        window.location.href = url;
    } else {
        window.location.href = '/calendar';
    }
}

// Open edit followup (for future use)
function openEditFollowup(id, source) {
    // For now just log - can be expanded to show edit modal
    console.log('Edit followup:', id, 'Source:', source);
    // Could redirect to the source page (contact/proposal)
    if (source === 'contact') {
        const contactId = id.replace('c_', '');
        window.location.href = '/contacts/' + contactId + '/edit';
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const columnModal = document.getElementById('columnModal');
    if (e.target === columnModal) {
        closeColumnModal();
    }
});
