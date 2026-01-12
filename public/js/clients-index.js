// Data initialized in Blade template

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

// Constants
const MANDATORY_FIELDS = ['client_name', 'client_type', 'mobile_no', 'source', 'status', 'signed_up', 'clid', 'first_name', 'surname'];
const BUSINESS_TYPES = ['Business', 'Company', 'Organization'];

// Pending documents for add mode (will be uploaded after client is created)
let pendingDocuments = [];
// Pending photo for add mode (will be uploaded after client is created)
let pendingPhoto = null;

const PASSPORT_PHOTO_DIMENSIONS = {
  minWidth: 350,
  maxWidth: 650,
  minHeight: 350,
  maxHeight: 650,
  squareRatio: 1.0,
  rectRatio: 0.78,
  tolerance: 0.15
};

// Helper: Remove display:none from inline style
function removeDisplayNone(element) {
  if (!element) return;
  let currentStyle = element.getAttribute('style') || '';
  if (currentStyle.includes('display:none') || currentStyle.includes('display: none')) {
    currentStyle = currentStyle.replace(/display\s*:\s*none[^;]*;?/gi, '').trim();
    currentStyle = currentStyle.replace(/^[\s;]+|[\s;]+$/g, '');
    if (currentStyle) {
      element.setAttribute('style', currentStyle);
    } else {
      element.removeAttribute('style');
    }
  }
  element.style.display = '';
  element.style.removeProperty('display');
}

// Show/hide BOs button based on client_type
function updateBOsButtonVisibility(client) {
  const bosButton = document.querySelector('#clientPageView .nav-tab[data-tab="bos"]');
  if (bosButton && client) {
    if (client.client_type === 'Individual') {
      bosButton.style.setProperty('display', 'none', 'important');
    } else {
      bosButton.style.setProperty('display', 'inline-block', 'important');
    }
  }
}

// Helper: Hide element with !important
function hideElement(element) {
  if (!element) return;
  element.style.display = 'none';
  element.style.setProperty('display', 'none', 'important');
  let currentStyle = element.getAttribute('style') || '';
  if (!currentStyle.includes('display: none')) {
    currentStyle = (currentStyle ? currentStyle + '; ' : '') + 'display: none !important;';
    element.setAttribute('style', currentStyle);
  }
}

// Helper: Show element by removing display restrictions
function showElement(element) {
  if (!element) return;
  // Remove display:none from inline style
  let currentStyle = element.getAttribute('style') || '';
  if (currentStyle.includes('display:none') || currentStyle.includes('display: none')) {
    currentStyle = currentStyle.replace(/display\s*:\s*none[^;]*;?/gi, '').trim();
    // Remove leading/trailing semicolons and spaces
    currentStyle = currentStyle.replace(/^[\s;]+|[\s;]+$/g, '');
    if (currentStyle) {
      element.setAttribute('style', currentStyle);
    } else {
      element.removeAttribute('style');
    }
  }
  // Also set via style property
  element.style.display = '';
  element.style.removeProperty('display');
}

// Helper: Apply function with multiple delays (for DOM readiness)
function applyWithDelays(fn, delays = [10, 50, 100, 200]) {
  fn();
  requestAnimationFrame(fn);
  delays.forEach(delay => setTimeout(fn, delay));
}

// Helper: Check if client type is Individual
function isIndividualType(type) {
  return type === 'Individual';
}

// Helper: Check if client type is Business
function isBusinessType(type) {
  return BUSINESS_TYPES.includes(type);
}

// Helper: Calculate age from DOB
function calculateAge(dob) {
  if (!dob) return '';
  const birthDate = new Date(dob);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  return age;
}

// Helper: Calculate days until expiry
function calculateDaysUntilExpiry(dateStr) {
  if (!dateStr) return '';
  const expiryDate = new Date(dateStr);
  const today = new Date();
  const diffTime = expiryDate - today;
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

// Helper: Format date for display
function formatDate(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
}

// Helper: Validate passport photo dimensions
function validatePassportPhoto(img, onSuccess, onError) {
  const width = img.width;
  const height = img.height;
  const { minWidth, maxWidth, minHeight, maxHeight, squareRatio, rectRatio, tolerance } = PASSPORT_PHOTO_DIMENSIONS;

  if (width < minWidth || width > maxWidth || height < minHeight || height > maxHeight) {
    onError(`Photo must be passport size (approximately 600x600 pixels or 413x531 pixels).\nCurrent dimensions: ${width}x${height} pixels.\nPlease upload a passport-size photo.`);
    return false;
  }

  const aspectRatio = width / height;
  const isSquare = Math.abs(aspectRatio - squareRatio) <= tolerance;
  const isRectangular = Math.abs(aspectRatio - rectRatio) <= tolerance;

  if (!isSquare && !isRectangular) {
    onError(`Photo must have passport size aspect ratio (square 1:1 or rectangular 35:45mm).\nCurrent ratio: ${aspectRatio.toFixed(2)}:1\nPlease upload a passport-size photo.`);
    return false;
  }

  onSuccess();
  return true;
}

// ============================================================================
// FIELD VISIBILITY FUNCTIONS
// ============================================================================

// Helper: Ensure container is valid DOM element
function ensureValidContainer(container) {
  if (!container || typeof container.querySelectorAll !== 'function') {
    return document;
  }
  return container;
}

// Hide all Business fields
function hideBusinessFields(container = document) {
  container = ensureValidContainer(container);
  container.querySelectorAll('[data-field-type="business"]').forEach(field => {
    // Set multiple ways to ensure it's hidden
    field.style.display = 'none';
    field.style.setProperty('display', 'none', 'important');
    // Update inline style attribute
    let currentStyle = field.getAttribute('style') || '';
    if (!currentStyle.includes('display: none')) {
      currentStyle = (currentStyle ? currentStyle + '; ' : '') + 'display: none !important;';
      field.setAttribute('style', currentStyle);
    }
  });
}

// Show all Individual fields
function showIndividualFields(container = document) {
  container = ensureValidContainer(container);
  // Show all Individual fields
  container.querySelectorAll('[data-field-type="individual"]').forEach(field => {
    // Remove display:none from inline style
    let currentStyle = field.getAttribute('style') || '';
    if (currentStyle.includes('display:none') || currentStyle.includes('display: none')) {
      currentStyle = currentStyle.replace(/display\s*:\s*none[^;]*;?/gi, '').trim();
      currentStyle = currentStyle.replace(/^[\s;]+|[\s;]+$/g, '');
      if (currentStyle) {
        field.setAttribute('style', currentStyle);
      } else {
        field.removeAttribute('style');
      }
    }
    // Also set via style property
    field.style.display = '';
    field.style.removeProperty('display');
  });
  // Show DOB row specifically
  container.querySelectorAll('#dob_dor_row').forEach(row => {
    let currentStyle = row.getAttribute('style') || '';
    if (currentStyle.includes('display:none') || currentStyle.includes('display: none')) {
      currentStyle = currentStyle.replace(/display\s*:\s*none[^;]*;?/gi, '').trim();
      currentStyle = currentStyle.replace(/^[\s;]+|[\s;]+$/g, '');
      if (currentStyle) {
        row.setAttribute('style', currentStyle);
      } else {
        row.removeAttribute('style');
      }
    }
    row.style.display = '';
    row.style.removeProperty('display');
  });
}

// Force show Individual fields and hide Business fields
function forceIndividualFieldsVisible(container = document) {
  container = ensureValidContainer(container);
  // First, aggressively hide ALL Business fields everywhere
  container.querySelectorAll('[data-field-type="business"]').forEach(field => {
    // Set multiple ways to ensure it's hidden
    field.style.display = 'none';
    field.style.setProperty('display', 'none', 'important');
    // Update inline style attribute
    let currentStyle = field.getAttribute('style') || '';
    if (!currentStyle.includes('display: none')) {
      currentStyle = (currentStyle ? currentStyle + '; ' : '') + 'display: none !important;';
      field.setAttribute('style', currentStyle);
    }
  });

  // Then show all Individual fields
  container.querySelectorAll('[data-field-type="individual"]').forEach(field => {
    // Remove display:none from inline style
    let currentStyle = field.getAttribute('style') || '';
    if (currentStyle.includes('display:none') || currentStyle.includes('display: none')) {
      currentStyle = currentStyle.replace(/display\s*:\s*none[^;]*;?/gi, '').trim();
      // Remove leading/trailing semicolons and spaces
      currentStyle = currentStyle.replace(/^[\s;]+|[\s;]+$/g, '');
      if (currentStyle) {
        field.setAttribute('style', currentStyle);
      } else {
        field.removeAttribute('style');
      }
    }
    // Also set via style property
    field.style.display = '';
    field.style.removeProperty('display');
  });

  // Show DOB row specifically
  container.querySelectorAll('#dob_dor_row').forEach(row => {
    let currentStyle = row.getAttribute('style') || '';
    if (currentStyle.includes('display:none') || currentStyle.includes('display: none')) {
      currentStyle = currentStyle.replace(/display\s*:\s*none[^;]*;?/gi, '').trim();
      currentStyle = currentStyle.replace(/^[\s;]+|[\s;]+$/g, '');
      if (currentStyle) {
        row.setAttribute('style', currentStyle);
      } else {
        row.removeAttribute('style');
      }
    }
    row.style.display = '';
    row.style.removeProperty('display');
  });
}

// Show Business fields and hide Individual fields
function showBusinessFields(container = document) {
  container = ensureValidContainer(container);
  // Hide Individual fields
  container.querySelectorAll('[data-field-type="individual"]').forEach(hideElement);
  // Hide DOB row for Business
  container.querySelectorAll('#dob_dor_row').forEach(hideElement);
  // Show Business fields
  container.querySelectorAll('[data-field-type="business"]').forEach(showElement);
}

// Hide all conditional fields
function hideAllConditionalFields(container = document) {
  container = ensureValidContainer(container);
  container.querySelectorAll('[data-field-type="individual"], [data-field-type="business"]').forEach(hideElement);
}

// Update required fields based on client type
function updateRequiredFields(container, isIndividual, isBusiness) {
  container = ensureValidContainer(container);
  const firstNameInput = container.querySelector('#first_name') || document.getElementById('first_name');
  const surnameInput = container.querySelector('#surname') || document.getElementById('surname');
  const businessNameInput = container.querySelector('#business_name') || document.getElementById('business_name');

  if (isIndividual) {
    if (firstNameInput) firstNameInput.required = true;
    if (surnameInput) surnameInput.required = true;
    if (businessNameInput) businessNameInput.required = false;
  } else if (isBusiness) {
    if (businessNameInput) businessNameInput.required = true;
    if (firstNameInput) firstNameInput.required = false;
    if (surnameInput) surnameInput.required = false;
  } else {
    if (firstNameInput) firstNameInput.required = false;
    if (surnameInput) surnameInput.required = false;
    if (businessNameInput) businessNameInput.required = false;
  }
}

// ============================================================================
// CLIENT TYPE CHANGE HANDLERS
// ============================================================================

// Handle client type change - main function
function handleClientTypeChange(eventTarget = null) {
  // Use event target if provided, otherwise find by ID
  const clientTypeSelect = eventTarget || document.getElementById('client_type');
  if (!clientTypeSelect) {
    // If no client_type select found, try to show Individual fields by default
    document.querySelectorAll('[data-field-type="individual"]').forEach(field => {
      field.style.setProperty('display', 'flex', 'important');
    });
    document.querySelectorAll('[data-field-type="business"]').forEach(field => {
      field.style.setProperty('display', 'none', 'important');
    });
    return;
  }

  const selectedType = clientTypeSelect.value || 'Individual';
  const isIndividual = isIndividualType(selectedType);
  const isBusiness = isBusinessType(selectedType);

  // Find form container - check both modal and page view
  let formContainer = clientTypeSelect.closest('form') ||
    clientTypeSelect.closest('.modal-body') ||
    clientTypeSelect.closest('.modal-content') ||
    document.querySelector('#clientModal .modal-body') ||
    document.querySelector('#clientFormPageContent') ||
    document;

  // Ensure formContainer is valid
  formContainer = ensureValidContainer(formContainer);

  if (!selectedType) {
    // Hide all conditional fields if no type selected
    document.querySelectorAll('[data-field-type="individual"]').forEach(field => {
      field.style.display = 'none';
    });
    document.querySelectorAll('[data-field-type="business"]').forEach(field => {
      field.style.display = 'none';
    });
    return;
  }

  // Show/hide fields based on client type
  if (isIndividual) {
    // First hide all Business fields aggressively
    hideBusinessFields();
    hideBusinessFields(formContainer);
    // Then show Individual fields
    showIndividualFields();
    showIndividualFields(formContainer);
  } else if (isBusiness) {
    // Hide all Individual fields first
    document.querySelectorAll('[data-field-type="individual"]').forEach(field => {
      field.style.display = 'none';
      field.style.setProperty('display', 'none', 'important');
    });
    // Show all Business fields
    document.querySelectorAll('[data-field-type="business"]').forEach(field => {
      field.style.display = '';
      field.style.removeProperty('display');
    });
    // Also apply to container
    showBusinessFields(formContainer);
  } else {
    // Hide all conditional fields if no type selected
    document.querySelectorAll('[data-field-type="individual"]').forEach(field => {
      field.style.display = 'none';
    });
    hideBusinessFields();
    hideBusinessFields(formContainer);
  }

  // Update required fields
  updateRequiredFields(formContainer, isIndividual, isBusiness);
}

// Handle client type change for cloned form
function handleClientTypeChangeInForm(container) {
  container = ensureValidContainer(container);
  const clientTypeSelect = container.querySelector('#client_type');
  if (!clientTypeSelect) return;

  const selectedType = clientTypeSelect.value;
  const isIndividual = isIndividualType(selectedType);
  const isBusiness = isBusinessType(selectedType);

  if (!selectedType) {
    hideAllConditionalFields(container);
    return;
  }

  if (isIndividual) {
    hideBusinessFields(container);
    showIndividualFields(container);
  } else if (isBusiness) {
    showBusinessFields(container);
  }

  updateRequiredFields(container, isIndividual, isBusiness);
}

// ============================================================================
// FORM FIELD HELPERS
// ============================================================================

// Toggle Alternate No field visibility based on WhatsApp checkbox
function toggleAlternateNoVisibility(waCheckbox, alternateNoRow) {
  if (!waCheckbox || !alternateNoRow) return;
  if (waCheckbox.checked) {
    hideElement(alternateNoRow);
  } else {
    showElement(alternateNoRow);
  }
}

function setupWaToggle(container = document) {
  container = ensureValidContainer(container);

  // Setup individual WhatsApp checkbox
  const waCheckbox = container.querySelector('#wa') || document.getElementById('wa');
  const alternateNoRow = container.querySelector('#alternate_no_row') || document.getElementById('alternate_no_row');
  if (waCheckbox && alternateNoRow) {
    const handler = () => toggleAlternateNoVisibility(waCheckbox, alternateNoRow);
    waCheckbox.removeEventListener('change', handler);
    waCheckbox.addEventListener('change', handler);
    toggleAlternateNoVisibility(waCheckbox, alternateNoRow);
  }

  // Setup business WhatsApp checkbox
  const waBusinessCheckbox = container.querySelector('#wa_business') || document.getElementById('wa_business');
  const alternateNoRowBusiness = container.querySelector('#alternate_no_row_business') || document.getElementById('alternate_no_row_business');
  if (waBusinessCheckbox && alternateNoRowBusiness) {
    const handler = () => toggleAlternateNoVisibility(waBusinessCheckbox, alternateNoRowBusiness);
    waBusinessCheckbox.removeEventListener('change', handler);
    waBusinessCheckbox.addEventListener('change', handler);
    toggleAlternateNoVisibility(waBusinessCheckbox, alternateNoRowBusiness);
  }
}

// Calculate age from DOB input
function calculateAgeFromDOB(eventTarget = null) {
  // If eventTarget is provided, use it to find the corresponding age input
  let dobInput = eventTarget || document.getElementById('dob_dor');
  let ageInput = null;

  if (dobInput) {
    // Try to find age input in the same container
    const container = dobInput.closest('form') || dobInput.closest('.modal-body') || dobInput.closest('div[style*="padding:12px"]') || document;
    ageInput = container.querySelector('#dob_age') || document.getElementById('dob_age');

    if (dobInput && ageInput && dobInput.value) {
      ageInput.value = calculateAge(dobInput.value);
    } else if (ageInput) {
      ageInput.value = '';
    }
  } else {
    // Fallback: try to find both inputs
    dobInput = document.getElementById('dob_dor');
    ageInput = document.getElementById('dob_age');
    if (dobInput && ageInput && dobInput.value) {
      ageInput.value = calculateAge(dobInput.value);
    } else if (ageInput) {
      ageInput.value = '';
    }
  }
}

// Calculate days until ID expiry
function calculateIDExpiryDays(eventTarget = null) {
  // If eventTarget is provided, use it to find the corresponding days input
  let expiryInput = eventTarget || document.getElementById('id_expiry_date');
  let daysInput = null;

  if (expiryInput) {
    // Try to find days input in the same container
    const container = expiryInput.closest('form') || expiryInput.closest('.modal-body') || expiryInput.closest('div[style*="padding:12px"]') || document;
    daysInput = container.querySelector('#id_expiry_days') || document.getElementById('id_expiry_days');

    if (expiryInput && daysInput && expiryInput.value) {
      daysInput.value = calculateDaysUntilExpiry(expiryInput.value);
    } else if (daysInput) {
      daysInput.value = '';
    }
  } else {
    // Fallback: try to find both inputs
    expiryInput = document.getElementById('id_expiry_date');
    daysInput = document.getElementById('id_expiry_days');
    if (expiryInput && daysInput && expiryInput.value) {
      daysInput.value = calculateDaysUntilExpiry(expiryInput.value);
    } else if (daysInput) {
      daysInput.value = '';
    }
  }
}

// Populate form fields from client data
function populateFormFields(container, client, fieldNames) {
  container = ensureValidContainer(container);
  fieldNames.forEach(k => {
    const el = container.querySelector(`#${k}`) || document.getElementById(k);
    if (!el) return;

    if (el.type === 'checkbox') {
      el.checked = !!client[k];
      if (k === 'wa') {
        if (el.id === 'wa') {
          const alternateNoRow = container.querySelector('#alternate_no_row') || document.getElementById('alternate_no_row');
          if (alternateNoRow) {
            if (el.checked) {
              hideElement(alternateNoRow);
            } else {
              showElement(alternateNoRow);
            }
          }
        } else if (el.id === 'wa_business') {
          const alternateNoRowBusiness = container.querySelector('#alternate_no_row_business') || document.getElementById('alternate_no_row_business');
          if (alternateNoRowBusiness) {
            if (el.checked) {
              hideElement(alternateNoRowBusiness);
            } else {
              showElement(alternateNoRowBusiness);
            }
          }
        }
      }
    } else if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
      if (el.type === 'date' && client[k]) {
        const date = new Date(client[k]);
        el.value = date.toISOString().split('T')[0];
      } else {
        el.value = client[k] ?? '';
      }
    }
  });
}

// ============================================================================
// NOTIFICATION SYSTEM
// ============================================================================

function showNotification(message, type = 'success') {
  const banner = document.getElementById('notificationBanner');
  const messageEl = document.getElementById('notificationMessage');
  const closeBtn = banner?.querySelector('button');
  if (!banner || !messageEl) return;

  // Set message
  messageEl.textContent = message;

  // Set color based on type
  if (type === 'success') {
    banner.style.background = '#28a745';
    banner.style.color = '#fff';
    if (closeBtn) closeBtn.style.color = '#fff';
  } else if (type === 'error') {
    banner.style.background = '#dc3545';
    banner.style.color = '#fff';
    if (closeBtn) closeBtn.style.color = '#fff';
  } else if (type === 'warning') {
    banner.style.background = '#ffc107';
    banner.style.color = '#000';
    if (closeBtn) closeBtn.style.color = '#000';
  } else {
    banner.style.background = '#17a2b8';
    banner.style.color = '#fff';
    if (closeBtn) closeBtn.style.color = '#fff';
  }

  // Show banner
  banner.style.display = 'flex';
  banner.style.alignItems = 'center';
  banner.style.justifyContent = 'center';

  // Auto-hide after 5 seconds
  setTimeout(() => {
    closeNotification();
  }, 5000);
}

// Make showNotification globally accessible
window.showNotification = showNotification;

function closeNotification() {
  const banner = document.getElementById('notificationBanner');
  if (banner) {
    banner.style.display = 'none';
  }
}

// Make closeNotification globally accessible
window.closeNotification = closeNotification;

// ============================================================================
// INITIALIZATION
// ============================================================================

document.getElementById('addClientBtn')?.addEventListener('click', () => openClientModal('add'));
document.getElementById('columnBtn')?.addEventListener('click', () => openColumnModal());

function handleFilterToggle(e) {
  const filtersVisible = e.target.checked;
  const columnFilters = document.querySelectorAll('.column-filter');

  columnFilters.forEach(filter => {
    if (filtersVisible) {
      filter.classList.add('visible');
      filter.style.display = 'block';
    } else {
      filter.classList.remove('visible');
      filter.style.display = 'none';
      filter.value = '';
      document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = '';
      });
    }
  });

  // Navigate to follow_up view when toggle is checked, or list all when unchecked
  // Preserve existing URL parameters
  const urlParams = new URLSearchParams(window.location.search);
  if (filtersVisible) {
    urlParams.set('follow_up', 'true');
  } else {
    urlParams.delete('follow_up');
  }
  const queryString = urlParams.toString();
  window.location.href = clientsIndexRoute + (queryString ? '?' + queryString : '');
}

document.getElementById('filterToggle')?.addEventListener('change', handleFilterToggle);

// Use event delegation for client type change to catch all instances
document.addEventListener('change', function (e) {
  if (e.target && e.target.id === 'client_type') {
    handleClientTypeChange(e.target);
    if (e.target.value === 'Individual') {
      forceIndividualFieldsVisible();
      applyWithDelays(forceIndividualFieldsVisible, [10, 50]);
    }
  }
  // Handle DOB change event
  if (e.target && e.target.id === 'dob_dor') {
    calculateAgeFromDOB(e.target);
  }
  // Handle ID Expiry Date change event
  if (e.target && e.target.id === 'id_expiry_date') {
    calculateIDExpiryDays(e.target);
  }
  // Handle WhatsApp checkbox change event (individual)
  if (e.target && e.target.id === 'wa') {
    const container = e.target.closest('form') || e.target.closest('.modal-body') || e.target.closest('div[style*="padding:12px"]') || document;
    const alternateNoRow = container.querySelector('#alternate_no_row') || document.getElementById('alternate_no_row');
    if (alternateNoRow) {
      toggleAlternateNoVisibility(e.target, alternateNoRow);
    }
  }
  // Handle WhatsApp checkbox change event (business)
  if (e.target && e.target.id === 'wa_business') {
    const container = e.target.closest('form') || e.target.closest('.modal-body') || e.target.closest('div[style*="padding:12px"]') || document;
    const alternateNoRowBusiness = container.querySelector('#alternate_no_row_business') || document.getElementById('alternate_no_row_business');
    if (alternateNoRowBusiness) {
      toggleAlternateNoVisibility(e.target, alternateNoRowBusiness);
    }
  }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
  const filterToggle = document.getElementById('filterToggle');
  if (filterToggle?.checked) {
    document.querySelectorAll('.column-filter').forEach(filter => {
      filter.classList.add('visible');
      filter.style.display = 'block';
    });
  }

  const initialClientType = document.getElementById('client_type');
  if (initialClientType && (initialClientType.value === 'Individual' || !initialClientType.value)) {
    showIndividualFields();
  }

  // Also attach direct listener as backup
  const clientTypeSelect = document.getElementById('client_type');
  if (clientTypeSelect) {
    clientTypeSelect.addEventListener('change', function () {
      handleClientTypeChange(this);
      if (this.value === 'Individual') {
        forceIndividualFieldsVisible();
        applyWithDelays(forceIndividualFieldsVisible, [10, 50]);
      }
    });
  }
});

// Radio button selection highlighting
function handleRadioSelection(e) {
  document.querySelectorAll('.action-radio').forEach(r => r.classList.remove('selected'));
  if (e.target.checked) {
    e.target.classList.add('selected');
  }
}

document.querySelectorAll('.action-radio').forEach(radio => {
  radio.addEventListener('change', handleRadioSelection);
});

const followUpBtn = document.getElementById('followUpBtn');
const listAllBtn = document.getElementById('listAllBtn');

if (followUpBtn) {
  followUpBtn.addEventListener('click', () => {
    // Preserve existing URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('follow_up', 'true');
    const queryString = urlParams.toString();
    window.location.href = clientsIndexRoute + (queryString ? '?' + queryString : '');
  });
}

if (listAllBtn) {
  listAllBtn.addEventListener('click', () => {
    // Preserve existing URL parameters except follow_up
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.delete('follow_up');
    const queryString = urlParams.toString();
    window.location.href = clientsIndexRoute + (queryString ? '?' + queryString : '');
  });
}

// ============================================================================
// CLIENT MODAL FUNCTIONS
// ============================================================================

async function openEditClient(id) {
  try {
    const res = await fetch(`/clients/${id}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!res.ok) {
      const errorText = await res.text();
      throw new Error(`HTTP ${res.status}: ${errorText}`);
    }
    const client = await res.json();
    currentClientId = id;

    // Get form container
    const formContainer = document.querySelector('#clientFormPageContent form div[style*="padding:12px"]');
    if (!formContainer) {
      console.error('Form container not found');
      return;
    }

    // Set form action and method
    const form = document.querySelector('#clientFormPageContent form');
    if (form) {
      form.action = `/clients/${id}`;
      form.method = 'POST';
      const methodDiv = form.querySelector('#clientFormMethod');
      if (methodDiv) {
        methodDiv.innerHTML = '@method("PUT")';
      }
    }

    // Show delete button
    const deleteBtn = document.querySelector('#clientFormPageContent .btn-delete');
    if (deleteBtn) {
      deleteBtn.style.display = 'inline-block';
    }

    // Populate edit form with same structure as detail page
    populateClientEditForm(client, formContainer);

    // Set page title (only if elements exist)
    const clientName = `${client.first_name || ''} ${client.surname || ''}`.trim() || 'Unknown';
    const clientPageTitle = document.getElementById('clientPageTitle');
    const clientPageName = document.getElementById('clientPageName');
    const editClientFromPageBtn = document.getElementById('editClientFromPageBtn');
    if (clientPageTitle) clientPageTitle.textContent = 'Edit Client';
    if (clientPageName) clientPageName.textContent = clientName;
    if (editClientFromPageBtn) editClientFromPageBtn.style.display = 'none';

    // Set up documents section
    const editFormDocumentsSection = document.getElementById('editFormDocumentsSection');
    if (editFormDocumentsSection) {
      // Create documents section HTML
      const documentsHTML = `
          <h4 style="font-weight:bold; margin-bottom:10px; color:#000; font-size:13px;">Documents</h4>
          <div id="editClientDocumentsList" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
            ${renderDocumentsList(client.documents || [])}
          </div>
          <div style="display:flex; gap:10px; justify-content:flex-end;">
            <input type="file" id="image" name="image" accept="image/*" style="display:none;" onchange="handleImagePreview(event)">
            <button type="button" class="btn" onclick="document.getElementById('image').click()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Photo</button>
            <button id="addDocumentBtn2" type="button" class="btn" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px; display:inline-block;">Add Document</button>
          </div>
        
        `;
      editFormDocumentsSection.innerHTML = documentsHTML;
      editFormDocumentsSection.style.display = 'block';
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const clientPageView = document.getElementById('clientPageView');
    clientPageView.classList.add('show');
    clientPageView.style.display = 'block';
    document.getElementById('clientDetailsPageContent').style.display = 'none';
    document.getElementById('clientFormPageContent').style.display = 'block';

    // Setup nav tab listeners for page view
    document.querySelectorAll('#clientPageView .nav-tab').forEach(tab => {
      // Remove existing listeners by cloning
      const newTab = tab.cloneNode(true);
      tab.parentNode.replaceChild(newTab, tab);
      // Add click listener
      newTab.addEventListener('click', function (e) {
        e.preventDefault();
        if (!currentClientId) return;
        const baseUrl = this.getAttribute('data-url');
        if (!baseUrl || baseUrl === '#') return;
        window.location.href = baseUrl + '?client_id=' + currentClientId;
      });
    });
  } catch (e) {
    console.error(e);
    alert('Error loading client data: ' + e.message);
  }
}

// Open client details modal
async function openClientDetailsModal(clientId) {
  try {
    const res = await fetch(`/clients/${clientId}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!res.ok) {
      throw new Error(`HTTP ${res.status}`);
    }
    const client = await res.json();
    currentClientId = clientId;

    const clientName = `${client.first_name || ''} ${client.surname || ''}`.trim() || 'Unknown';
    const clientPageName = document.getElementById('clientPageName');
    const clientPageTitle = document.getElementById('clientPageTitle');
    if (clientPageName) clientPageName.textContent = clientName;
    if (clientPageTitle) clientPageTitle.textContent = 'Client';

    populateClientDetailsModal(client);

    document.getElementById('clientsTableView').classList.add('hidden');
    const clientPageView = document.getElementById('clientPageView');
    clientPageView.classList.add('show');
    clientPageView.style.display = 'block';
    document.getElementById('clientDetailsPageContent').style.display = 'block';
    document.getElementById('clientFormPageContent').style.display = 'none';
    document.getElementById('editClientFromPageBtn').style.display = 'inline-block';

    // Show/hide BOs button based on client_type
    updateBOsButtonVisibility(client);

    // Setup nav tab listeners for page view
    document.querySelectorAll('#clientPageView .nav-tab').forEach(tab => {
      // Remove existing listeners by cloning
      const newTab = tab.cloneNode(true);
      tab.parentNode.replaceChild(newTab, tab);
      // Add click listener
      newTab.addEventListener('click', function (e) {
        e.preventDefault();
        if (!currentClientId) return;
        const baseUrl = this.getAttribute('data-url');
        if (!baseUrl || baseUrl === '#') return;
        window.location.href = baseUrl + '?client_id=' + currentClientId;
      });
    });
  } catch (e) {
    console.error(e);
    alert('Error loading client details: ' + e.message);
  }
}

// Populate client details modal with data
// function populateClientDetailsModal(client) {
//   // Try page view first, then modal
//   let content = document.getElementById('clientDetailsContent');
//   if (!content) {
//     content = document.getElementById('clientDetailsContentModal');
//   }
//   if (!content) {
//     console.error('clientDetailsContent element not found');
//     return;
//   }

//   const dob = client.dob_dor ? formatDate(client.dob_dor) : '';
//   const dobAge = client.dob_dor ? calculateAge(client.dob_dor) : '';
//   const idExpiry = client.id_expiry_date ? formatDate(client.id_expiry_date) : '';
//   const idExpiryDays = client.id_expiry_date ? calculateDaysUntilExpiry(client.id_expiry_date) : '';
//   const signedUp = client.signed_up ? formatDate(client.signed_up) : '';
//   const photoUrl = client.image ? (client.image.startsWith('http') ? client.image : `/storage/${client.image}`) : '';

//   console.log(client.client_type);
//   // Column 1: CUSTOMER DETAILS
//   const col1 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">CUSTOMER DETAILS</div>
//         <div class="detail-section-body">
//           <div class="detail-row">
//             <span class="detail-label">Client Type</span>
//             <div class="detail-value">${client.client_type === 'Business' ? 'Business' : 'Individual'}</div>
//           </div>
//           <divclass="detail-row">
//             <span class="detail-label">DOB/DOR</span>
//             <div style="display:flex; gap:5px; align-items:center; flex:1;">
//               <div class="detail-value" style="flex:1;">${dob || '-'}</div>
//               <div class="detail-value" style="width:50px; text-align:center; flex-shrink:0;">${dobAge || '-'}</div>
//             </div>
//           </div>
//           <div  class="detail-row">
//             <span class="detail-label">NIN/BCRN</span>
//             <div class="detail-value">${client.nin_bcrn || '-'}</div>
//           </div>
//           <div class="detail-row">
//             <span class="detail-label">ID Expiry Date</span>
//             <div style="display:flex; gap:5px; align-items:center; flex:1;">
//               <div class="detail-value" style="flex:1;">${idExpiry || '-'}</div>
//               <div class="detail-value" style="width:50px; text-align:center; flex-shrink:0;">${idExpiryDays || '-'}</div>
//             </div>
//           </div>
//           <div  class="detail-row">
//             <span class="detail-label">Client Status</span>
//             <div class="detail-value">
//               ${client.status ? `<span class="badge-status" style="background:${client.status === 'Active' ? '#28a745' : '#6c757d'};">${client.status}</span>` : '-'}
//             </div>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   // Column 2: CONTACT DETAILS
//   const col2 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">CONTACT DETAILS</div>
//         <div class="detail-section-body">
//           <div class="detail-row">
//             <span class="detail-label">Mobile No</span>
//             <div class="detail-value">${client.mobile_no || '-'}</div>
//           </div>
//           <div  ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none !important;"' : ''}   class="detail-row">
//             <span class="detail-label">On Whatsapp</span>
//             <div class="detail-value checkbox">
//               <input type="checkbox" ${client.wa ? 'checked' : ''} disabled>
//             </div>
//           </div>
//           <div class="detail-row">
//             <span class="detail-label">Alternate No</span>
//             <div class="detail-value">${client.alternate_no || '-'}</div>
//           </div>
//           <div  class="detail-row">
//             <span class="detail-label">Email Address</span>
//             <div class="detail-value">${client.email_address || '-'}</div>
//           </div>
//           <div class="detail-row">
//             <span class="detail-label">Contact Person</span>
//             <div class="detail-value">${client.contact_person || '-'}</div>
//           </div>

//           <div  ${client.client_type === 'Individual' ? 'style="display:none !important;"' : ''}  class="detail-row">
//             <span class="detail-label">Designation</span>
//             <div class="detail-value">${client.designation || '-'}</div>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   // Column 3: ADDRESS DETAILS
//   const col3 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">ADDRESS DETAILS</div>
//         <div class="detail-section-body">
//           <div class="detail-row">
//             <span class="detail-label">District</span>
//             <div class="detail-value">${client.district || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Address</span>
//             <div class="detail-value">${client.location || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Island</span>
//             <div class="detail-value">${client.island || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Country</span>
//             <div class="detail-value">${client.country || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">P.O. Box No</span>
//             <div class="detail-value">${client.po_box_no || '-'}</div>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   // Column 4: REGISTRATION DETAILS
//   const col4 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">REGISTRATION DETAILS</div>
//         <div class="detail-section-body">
//           <div class="detail-row">
//             <span class="detail-label">Sign Up Date</span>
//             <div class="detail-value">${signedUp || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Agency</span>
//             <div class="detail-value">${client.agency || 'Keystone'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Agent</span>
//             <div class="detail-value">${client.agent || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Source</span>
//             <div class="detail-value">${client.source || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Source Name</span>
//             <div class="detail-value">${client.source_name || '-'}</div>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   // Column 5: INDIVIDUAL DETAILS
//   const col5 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">${client.client_type === 'Business' ? 'BUSINESS DETAILS' : 'INDIVIDUAL DETAILS'}</div>
//         <div class="detail-section-body">
//           <div style="display:flex; gap:10px; align-items:flex-start;">
//             <div style="flex:1; display:flex; flex-direction:column; gap:8px;">
//               <div class="detail-row" style="margin-bottom:0;">
//                 <span class="detail-label">Salutation</span>
//                 <div class="detail-value" style="flex:1;">${client.salutation || '-'}</div>
//               </div>
//               <div class="detail-row" style="margin-bottom:0;">
//                 <span class="detail-label">First Name</span>
//                 <div class="detail-value" style="flex:1;">${client.first_name || '-'}</div>
//               </div>
//               <div class="detail-row" style="margin-bottom:0;">
//                 <span class="detail-label">Other Names</span>
//                 <div class="detail-value" style="flex:1;">${client.other_names || '-'}</div>
//               </div>
//               <div class="detail-row" style="margin-bottom:0;">
//                 <span class="detail-label">Surname</span>
//                 <div class="detail-value" style="flex:1;">${client.surname || '-'}</div>
//               </div>
//             </div>
//             ${photoUrl ? `
//               <div style="flex-shrink:0; margin-top:13px;">
//                 <img src="${photoUrl}" alt="Photo" class="detail-photo" onclick="previewClientPhotoModal('${photoUrl}')">
//               </div>
//             ` : `
//               <div style="flex-shrink:0; margin-top:13px; width:80px; height:100px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5;"></div>
//             `}
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Other Names</span>
//             <div class="detail-value">${client.other_names || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Surname</span>
//             <div class="detail-value">${client.surname || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Passport No</span>
//             <div style="display:flex; gap:5px; align-items:center; flex:1;">
//               <div class="detail-value" style="flex:1;">${client.passport_no || '-'}</div>
//               <input type="text" value="SEY" readonly style="width:60px; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#fff; text-align:center; font-size:11px; font-family:inherit; box-sizing:border-box; min-height:22px; flex-shrink:0;">
//             </div>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   // Column 6: INCOME DETAILS
//   const col6 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">${client.client_type === 'Business' ? 'BUSINESS INCOME DETAILS' : 'INDIVIDUAL INCOME DETAILS'}</div>
//         <div class="detail-section-body">
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Occupation</span>
//             <div class="detail-value">${client.occupation || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Income Source</span>
//             <div class="detail-value">${client.income_source || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Employer</span>
//             <div class="detail-value">${client.employer || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Monthly Income</span>
//             <div class="detail-value">${client.monthly_income || '-'}</div>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   // Column 7: OTHER DETAILS
//   const col7 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">${client.client_type === 'Business' ? 'BUSINESS OTHER DETAILS' : 'INDIVIDUAL OTHER DETAILS'}</div>
//         <div class="detail-section-body">
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Married</span>
//             <div class="detail-value checkbox">
//               <input type="checkbox" ${client.married ? 'checked' : ''} disabled>
//             </div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Spouse's Name</span>
//             <div class="detail-value">${client.spouses_name || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">PEP</span>
//             <div class="detail-value checkbox">
//               <input type="checkbox" ${client.pep ? 'checked' : ''} disabled>
//             </div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">PEP Details</span>
//             <div class="detail-value" style="min-height:40px; white-space:pre-wrap;">${client.pep_comment || '-'}</div>
//           </div>
//           <div ${['Business', 'Company', 'Organization'].includes(client.client_type) ? 'style="display:none;"' : ''} class="detail-row">
//             <span class="detail-label">Notes</span>
//             <textarea class="detail-value" style="min-height:40px; white-space:pre-wrap; resize:vertical;" disabled>${client.notes || ''}</textarea>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   // Column 8: INSURABLES
//   const col8 = `
//     <div style="display:flex; flex-direction:column; gap:10px;">
//       <div class="detail-section">
//         <div class="detail-section-header">${client.client_type === 'Business' ? 'BUSINESS INSURABLES' : 'INDIVIDUAL INSURABLES'}</div>
//         <div class="detail-section-body">
//           <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:center; margin-bottom:15px;">
//             <div ${client.client_type === 'Business' ? 'style="display:none;"' : ''} style="display:flex; align-items:center; gap:8px;">
//               <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:default;">Vehicle</label>
//               <div class="detail-value checkbox">
//                 <input type="checkbox" ${client.has_vehicle ? 'checked' : ''} disabled>
//               </div>
//             </div>
//             <div ${client.client_type === 'Business' ? 'style="display:none;"' : ''}  style="display:flex; align-items:center; gap:8px;">
//               <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:default;">Home</label>
//               <div class="detail-value checkbox">
//                 <input type="checkbox" ${client.has_house ? 'checked' : ''} disabled>
//               </div>
//             </div>
//             <div ${client.client_type === 'Business' ? 'style="display:none;"' : ''} style="display:flex; align-items:center; gap:8px;">
//               <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:default;">Business</label>
//               <div class="detail-value checkbox">
//                 <input type="checkbox" ${client.has_business ? 'checked' : ''} disabled>
//               </div>
//             </div>
//             <div ${client.client_type === 'Business' ? 'style="display:none;"' : ''} style="display:flex; align-items:center; gap:8px;">
//               <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:default;">Boat</label>
//               <div class="detail-value checkbox">
//                 <input type="checkbox" ${client.has_boat ? 'checked' : ''} disabled>
//               </div>
//             </div>
//           </div>
//           <div ${client.client_type === 'Business' ? 'style="display:none;"' : ''} style="margin-top:15px; border-top:1px solid #ddd; padding-top:8px;">
//             <label style="font-size:10px; color:#555; font-weight:600; display:block; margin-bottom:4px;">Notes</label>
//             <textarea class="detail-value" style="min-height:40px; width:100%; white-space:pre-wrap; resize:vertical; font-size:11px; padding:4px 6px; border:1px solid #ddd; background:#fff; border-radius:2px; box-sizing:border-box;" disabled>${client.notes || ''}</textarea>
//           </div>
//         </div>
//       </div>
//     </div>
//   `;

//   content.innerHTML = col1 + col2 + col3 + col4 + col5 + col6 + col7 + col8;

//   // Load documents
//   const documentsList = document.getElementById('clientDocumentsList');
//   if (documentsList) {
//     documentsList.innerHTML = renderDocumentsList(client.documents || []);
//   }

//   // Show Add Document button if client has documents or is loaded
//   const addDocumentBtn = document.getElementById('addDocumentBtn1');
//   if (addDocumentBtn && currentClientId) {
//     addDocumentBtn.style.display = 'inline-block';
//   }

//   // Set edit button action
//   const editBtn = document.getElementById('editClientFromPageBtn');
//   if (editBtn) {
//     editBtn.onclick = () => openEditClient(currentClientId);
//   }

//   // Tab navigation
//   document.querySelectorAll('.nav-tab').forEach(tab => {
//     tab.addEventListener('click', function(e) {
//       e.preventDefault();
//       const clientId = currentClientId;
//       if (!clientId) return;

//       closeClientDetailsModal();

//       const baseUrl = this.getAttribute('data-url');
//       if (!baseUrl) return;

//       window.location.href = baseUrl + '?client_id=' + clientId;
//     });
//   });
// }
function populateClientDetailsModal(client) {
  // Update BOs button visibility
  updateBOsButtonVisibility(client);

  let content = document.getElementById('clientDetailsContent') || document.getElementById('clientDetailsContentModal');
  if (!content) {
    console.error('clientDetailsContent element not found');
    return;
  }

  const dob = client.dob_dor ? formatDate(client.dob_dor) : '-';
  const dobAge = client.dob_dor ? calculateAge(client.dob_dor) : '-';
  const idExpiry = client.id_expiry_date ? formatDate(client.id_expiry_date) : '-';
  const idExpiryDays = client.id_expiry_date ? calculateDaysUntilExpiry(client.id_expiry_date) : '-';
  const signedUp = client.signed_up ? formatDate(client.signed_up) : '-';
  const photoUrl = client.image ? (client.image.startsWith('http') ? client.image : `/storage/${client.image}`) : '';

  const clientType = (client.client_type || '').trim().toLowerCase();
  const isIndividual = clientType === 'individual';
  const isBusinessLike = ['business', 'company', 'organization'].includes(clientType);

  // Helper to conditionally hide rows
  const hideForBusiness = isBusinessLike ? 'style="display:none;"' : '';
  const hideForIndividual = isIndividual ? 'style="display:none;"' : '';

  // Column 1: CUSTOMER DETAILS
  const col1 = `
      <div style="display:flex; flex-direction:column; gap:10px;">
      <div class="detail-section">
        <div class="detail-section-header">CUSTOMER DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Client Type</span>
              <div class="detail-value">${isBusinessLike ? 'Business' : 'Individual'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
            <span class="detail-label">DOB/DOR</span>
            <div style="display:flex; gap:5px; align-items:center; flex:1;">
              <div class="detail-value" style="flex:1;">${dob}</div>
              <div class="detail-value" style="width:50px; text-align:center; flex-shrink:0;">${dobAge}</div>
            </div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
            <span class="detail-label">NIN/BCRN</span>
            <div class="detail-value">${client.nin_bcrn || '-'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
            <span class="detail-label">ID Expiry Date</span>
            <div style="display:flex; gap:5px; align-items:center; flex:1;">
              <div class="detail-value" style="flex:1;">${idExpiry}</div>
              <div class="detail-value" style="width:50px; text-align:center; flex-shrink:0;">${idExpiryDays}</div>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Client Status</span>
              <div class="detail-value">
                ${client.status ? `<span class="badge-status" style="background:${client.status === 'Active' ? '#28a745' : '#6c757d'};">${client.status}</span>` : '-'}
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 2: CONTACT DETAILS
  const col2 = `
      <div style="display:flex; flex-direction:column; gap:10px;">
      <div class="detail-section">
        <div class="detail-section-header">CONTACT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Mobile No</span>
            <div class="detail-value">${client.mobile_no || '-'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">On Whatsapp</span>
              <div class="detail-value checkbox">
                <input type="checkbox" ${client.wa == "1" ? 'checked' : ''} disabled>
              </div>
            </div>
          <div class="detail-row" style="${client.wa == "1" ? 'display:none;' : ''}" >
            <span class="detail-label">Alternate No</span>
            <div class="detail-value">${client.alternate_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Email Address</span>
            <div class="detail-value">${client.email_address || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Contact Person</span>
            <div class="detail-value">${client.contact_person || '-'}</div>
          </div>
            <div class="detail-row" ${hideForIndividual}>
            <span class="detail-label">Designation</span>
            <div class="detail-value">${client.designation || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 3: ADDRESS DETAILS
  const col3 = `
      <div style="display:flex; flex-direction:column; gap:10px;">
      <div class="detail-section">
        <div class="detail-section-header">ADDRESS DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">District</span>
            <div class="detail-value">${client.districts?.name || '-'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">Address</span>
            <div class="detail-value">${client.location || '-'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">Island</span>
            <div class="detail-value">${client.islands?.name || '-'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
            <span class="detail-label">Country</span>
            <div class="detail-value">${client.countries?.name || '-'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
            <span class="detail-label">P.O. Box No</span>
            <div class="detail-value">${client.po_box_no || '-'}</div>
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 7: OTHER DETAILS
  const col7 = hideForBusiness ? `` : `
      <div style="display:flex; flex-direction:column; gap:10px;">
      <div class="detail-section">
        <div class="detail-section-header">OTHER DETAILS</div>
        <div class="detail-section-body">
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">Married</span>
              <div class="detail-value checkbox">
                <input type="checkbox" ${client.married ? 'checked' : ''} disabled>
          </div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">Spouse's Name</span>
              <div class="detail-value">${client.spouses_name || '-'}</div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">PEP</span>
              <div class="detail-value checkbox">
                <input type="checkbox" ${client.pep ? 'checked' : ''} disabled>
          </div>
          </div>
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">PEP Details</span>
              <div class="detail-value" style="min-height:40px; white-space:pre-wrap;">${client.pep_comment || '-'}</div>
            </div>
            <div class="detail-row" ${hideForBusiness}>
              <span class="detail-label">Notes</span>
              <textarea class="detail-value" style="min-height:40px; white-space:pre-wrap; resize:vertical;" disabled>${client.notes || ''}</textarea>
            </div>
          </div>
        </div>
      </div>
    `;

  // The remaining cards only for Individual
  const col4 = isIndividual ? '' : /* Registration Details */ `<div style="display:flex; flex-direction:column; gap:10px;">
      <div class="detail-section">
        <div class="detail-section-header">REGISTRATION DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row"><span class="detail-label">Sign Up Date</span><div class="detail-value">${signedUp}</div></div>
          <div class="detail-row"><span class="detail-label">Agency</span><div class="detail-value">${client.agencies.name || 'Keystone'}</div></div>
          <div class="detail-row"><span class="detail-label">Agent</span><div class="detail-value">${client.agents.name || '-'}</div></div>
          <div class="detail-row"><span class="detail-label">Source</span><div class="detail-value">${client.sources.name || '-'}</div></div>
          <div class="detail-row"><span class="detail-label">Source Name</span><div class="detail-value">${client.source_name || '-'}</div></div>
        </div>
      </div>
    </div>`;

  const col5 = !isIndividual ? '' : /* Individual Details with photo */ `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">INDIVIDUAL DETAILS</div>
          <div class="detail-section-body">
            <div style="display:flex; gap:10px; align-items:flex-start;">
              <div style="flex:1; display:flex; flex-direction:column; gap:8px;">
                <div class="detail-row" style="margin-bottom:0;"><span class="detail-label">Salutation</span><div class="detail-value">${client.salutations?.name || '-'}</div></div>
                <div class="detail-row" style="margin-bottom:0;"><span class="detail-label">First Name</span><div class="detail-value">${client.first_name || '-'}</div></div>
                <div class="detail-row" style="margin-bottom:0;"><span class="detail-label">Other Names</span><div class="detail-value">${client.other_names || '-'}</div></div>
                <div class="detail-row" style="margin-bottom:0;"><span class="detail-label">Surname</span><div class="detail-value">${client.surname || '-'}</div></div>
              </div>
              ${photoUrl ? `<div style="flex-shrink:0; margin-top:13px;"><img src="${photoUrl}" alt="Photo" class="detail-photo" onclick="previewClientPhotoModal('${photoUrl}')"></div>`
      : `<div style="flex-shrink:0; margin-top:13px; width:80px; height:100px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5;"></div>`}
            </div>
            <div class="detail-row"><span class="detail-label">Passport No</span><div class="detail-value">${client.passport_no || '-'}</div></div>
          </div>
        </div>
      </div>
    `;

  const col6 = !isIndividual ? '' : /* Income Details */ `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">INDIVIDUAL INCOME DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row"><span class="detail-label">Occupation</span><div class="detail-value">${client.occupations?.name || '-'}</div></div>
            <div class="detail-row"><span class="detail-label">Income Source</span><div class="detail-value">${client.income_sources?.name || '-'}</div></div>
            <div class="detail-row"><span class="detail-label">Employer</span><div class="detail-value">${client.employer || '-'}</div></div>
            <div class="detail-row"><span class="detail-label">Monthly Income</span><div class="detail-value">${client.monthly_income || '-'}</div></div>
          </div>
        </div>
      </div>
    `;

  const col8 = !isIndividual ? '' : /* Insurable Details */ `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">INDIVIDUAL INSURABLES</div>
          <div class="detail-section-body">
            <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:center; margin-bottom:15px;">
              <div style="display:flex; align-items:center; gap:8px;"><label style="font-size:11px;">Vehicle</label><div class="detail-value checkbox"><input type="checkbox" ${client.has_vehicle ? 'checked' : ''} disabled></div></div>
              <div style="display:flex; align-items:center; gap:8px;"><label style="font-size:11px;">Home</label><div class="detail-value checkbox"><input type="checkbox" ${client.has_house ? 'checked' : ''} disabled></div></div>
              <div style="display:flex; align-items:center; gap:8px;"><label style="font-size:11px;">Business</label><div class="detail-value checkbox"><input type="checkbox" ${client.has_business ? 'checked' : ''} disabled></div></div>
              <div style="display:flex; align-items:center; gap:8px;"><label style="font-size:11px;">Boat</label><div class="detail-value checkbox"><input type="checkbox" ${client.has_boat ? 'checked' : ''} disabled></div></div>
            </div>
            <div style="margin-top:15px; border-top:1px solid #ddd; padding-top:8px;">
              <label style="font-size:10px; color:#555;">Notes</label>
              <textarea class="detail-value" style="min-height:40px; width:100%; white-space:pre-wrap; resize:vertical;" disabled>${client.notes || ''}</textarea>
            </div>
          </div>
        </div>
      </div>
    `;

  // Assemble HTML
  content.innerHTML = col1 + col2 + col3 + col7 + col4 + col5 + col6 + col8;

  // Documents
  const documentsList = document.getElementById('clientDocumentsList');
  if (documentsList) {
    documentsList.innerHTML = renderDocumentsList(client.documents || []);
  }

  // Add Document button
  const addDocumentBtn = document.getElementById('addDocumentBtn1');
  if (addDocumentBtn && currentClientId) addDocumentBtn.style.display = 'inline-block';

  // Edit button
  const editBtn = document.getElementById('editClientFromPageBtn');
  if (editBtn) editBtn.onclick = () => openEditClient(currentClientId);

  // Tab navigation
  document.querySelectorAll('.nav-tab').forEach(tab => {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      if (!currentClientId) return;
      closeClientDetailsModal();
      const baseUrl = this.getAttribute('data-url');
      if (!baseUrl) return;
      window.location.href = baseUrl + '?client_id=' + currentClientId;
    });
  });
}

// Helper function to format date for date input (YYYY-MM-DD)
function formatDateForInput(dateStr) {
  if (!dateStr) return '';
  // If it's already in YYYY-MM-DD format, return as is
  if (typeof dateStr === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
    return dateStr;
  }
  // Otherwise, parse and format
  const date = new Date(dateStr);
  if (isNaN(date.getTime())) return '';
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

// Populate client edit form with editable fields (same structure as detail page)
function populateClientEditForm(client, formContainer) {
  const dob = formatDateForInput(client.dob_dor);
  const dobAge = client.dob_dor ? calculateAge(client.dob_dor) : '';
  const idExpiry = formatDateForInput(client.id_expiry_date);
  const idExpiryDays = client.id_expiry_date ? calculateDaysUntilExpiry(client.id_expiry_date) : '';
  const signedUp = formatDateForInput(client.signed_up);
  const photoUrl = client.image ? (client.image.startsWith('http') ? client.image : `/storage/${client.image}`) : '';

  // Helper function to create select options
  const createSelectOptions = (options, selectedValue) => {
    console.log(options, selectedValue);
    const selectedInt = parseInt(selectedValue, 0); // convert string to integer

    if (!options || !Array.isArray(options)) return '';
    return options.map(opt =>
      `<option value="${opt.id}" ${opt.id === selectedInt ? 'selected' : ''}>${opt.name}</option>`
    ).join('');
  };
  const createclientTypeSelectOptions = (options, selectedValue) => {
    console.log(options, selectedValue);
    if (!options || !Array.isArray(options)) return '';
    return options.map(opt =>
      `<option value="${opt}" ${opt === selectedValue ? 'selected' : ''}>${opt}</option>`
    ).join('');
  };
  const clientTypes = lookupData?.client_types || ['Individual', 'Business', 'Company', 'Organization'];
  const clientStatuses = lookupData?.client_statuses || ['Active', 'Inactive', 'Suspended', 'Pending', 'Expired'];
  const districts = lookupData?.districts || [];
  const islands = lookupData?.islands || [];
  const countries = lookupData?.countries || [];
  const sources = lookupData?.sources || [];
  const salutations = lookupData?.salutations || [];
  const incomeSources = lookupData?.income_sources || [];
  const occupations = lookupData?.occupations || [];
  const clientType = (client.client_type || '').trim().toLowerCase();
  const isIndividual = clientType === 'individual';
  const isBusinessLike = ['business', 'company', 'organization'].includes(clientType);

  // Helper to conditionally hide rows
  const hideForBusiness = isBusinessLike ? 'style="display:none;"' : '';
  const hideForIndividual = isIndividual ? 'style="display:none;"' : '';

  // Column 1: CUSTOMER DETAILS
  const col1 = `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">CUSTOMER DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Client Type</span>
              <select name="client_type_display" class="detail-value" disabled style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px; background:#f5f5f5; cursor:not-allowed;">
                ${createclientTypeSelectOptions(clientTypes, client.client_type)}
              </select>
              <input type="hidden" name="client_type" value="${client.client_type || ''}">
            </div>
            <div class="detail-row">
              <span class="detail-label">DOB/DOR</span>
              <div style="display:flex; gap:5px; align-items:center; flex:1;">
                <input type="date" name="dob_dor" value="${dob}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <input type="text" id="dob_age" value="${dobAge}" readonly class="detail-value" style="width:50px; text-align:center; flex-shrink:0; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#f5f5f5; font-size:11px;">
              </div>
            </div>
            <div class="detail-row">
              <span class="detail-label">NIN/BCRN</span>
              <input type="text" name="nin_bcrn" value="${client.nin_bcrn || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">ID Expiry Date</span>
              <div style="display:flex; gap:5px; align-items:center; flex:1;">
                <input type="date" name="id_expiry_date" value="${idExpiry}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <input type="text" id="id_expiry_days" value="${idExpiryDays}" readonly class="detail-value" style="width:50px; text-align:center; flex-shrink:0; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#f5f5f5; font-size:11px;">
              </div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Client Status</span>
              <select name="status" class="detail-value" required style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <option value="">Select</option>
                ${createclientTypeSelectOptions(clientStatuses, client.status)}
              </select>
            </div>
          </div>
        </div>
      </div>
    `;

  console.log(client);
  // Column 2: CONTACT DETAILS
  const col2 = `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">CONTACT DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Mobile No</span>
              <input type="text" name="mobile_no" value="${client.mobile_no || ''}" class="detail-value" required style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">On Whatsapp</span>
              <div class="detail-value checkbox">
                <input type="checkbox" name="wa" value="1" ${client.wa == "1" ? 'checked' : ''}>
              </div>
            </div>
            <div class="detail-row" id="alternate_no_row" style="${client.wa == "1" ? 'display:none;' : ''}">
              <span class="detail-label">Alternate No</span>
              <input type="text" name="alternate_no" value="${client.alternate_no || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Email Address</span>
              <input type="email" name="email_address" value="${client.email_address || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Contact Person</span>
              <input type="text" name="contact_person" value="${client.contact_person || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 3: ADDRESS DETAILS
  const col3 = `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">ADDRESS DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">District</span>
              <select name="district" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <option value="">Select</option>
                ${createSelectOptions(districts, client.district)}
              </select>
            </div>
            <div class="detail-row">
              <span class="detail-label">Address</span>
              <input type="text" name="location" value="${client.location || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Island</span>
              <select name="island" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <option value="">Select</option>
                ${createSelectOptions(islands, client.island)}
              </select>
            </div>
            <div class="detail-row">
              <span class="detail-label">Country</span>
              <select name="country" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <option value="">Select</option>
                ${createSelectOptions(countries, client.country)}
              </select>
            </div>
            <div class="detail-row">
              <span class="detail-label">P.O. Box No</span>
              <input type="text" name="po_box_no" value="${client.po_box_no || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 4: REGISTRATION DETAILS
  const col4 = hideForIndividual ? `` : `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">REGISTRATION DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Sign Up Date</span>
              <input type="date" name="signed_up" value="${signedUp}" class="detail-value" required style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Agency</span>
              <input type="text" name="agency" value="${client.agency || 'Keystone'}" readonly class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#f5f5f5; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Agent</span>
              <input type="text" name="agent" value="${client.agent || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Source</span>
              <select name="source" class="detail-value" required style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <option value="">Select</option>
                ${createSelectOptions(sources, client.source)}
              </select>
            </div>
            <div class="detail-row">
              <span class="detail-label">Source Name</span>
              <input type="text" name="source_name" value="${client.source_name || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 5: INDIVIDUAL DETAILS
  const col5 = hideForBusiness ? `` : `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">INDIVIDUAL DETAILS</div>
          <div class="detail-section-body">
            <div style="display:flex; gap:10px; align-items:flex-start;">
              <div style="flex:1; display:flex; flex-direction:column; gap:8px;">
                <div class="detail-row" style="margin-bottom:0;">
                  <span class="detail-label">Salutation</span>
                  <select name="salutation" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                    <option value="">Select</option>
                    ${createSelectOptions(salutations, client.salutation)}
                  </select>
                </div>
                <div class="detail-row" style="margin-bottom:0;">
                  <span class="detail-label">First Name</span>
                  <input type="text" name="first_name" value="${client.first_name || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                </div>
              </div>
              ${photoUrl ? `
                <div style="flex-shrink:0; margin-top:13px; position:relative;">
                  <img src="${photoUrl}" alt="Photo" class="detail-photo" id="clientPhotoImg" style="display:block; cursor:pointer;" onclick="document.querySelector('input[type=\\'file\\'][name=\\'image\\']').click()">
                  <input type="file" name="image" accept="image/*" style="display:none;" onchange="handleImagePreview(event)">
                  <input type="hidden" name="existing_image" id="existing_image" value="${client.image || ''}">
                </div>
              ` : `
                <div style="flex-shrink:0; margin-top:13px; width:80px; height:100px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; cursor:pointer;" id="clientPhotoPreview" onclick="document.getElementById('image').click()">
                  <span style="font-size:10px; color:#999;">Click to upload</span>
                  <input type="file" id="image" name="image" accept="image/*" style="display:none;" onchange="handleImagePreview(event)">
                </div>
              `}
            </div>
            <div class="detail-row">
              <span class="detail-label">Other Names</span>
              <input type="text" name="other_names" value="${client.other_names || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Surname</span>
              <input type="text" name="surname" value="${client.surname || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Passport No</span>
              <div style="display:flex; gap:5px; align-items:center; flex:1;">
                <input type="text" name="passport_no" value="${client.passport_no || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <input type="text" value="SEY" readonly style="width:60px; border:1px solid #ddd; padding:4px 6px; border-radius:2px; background:#fff; text-align:center; font-size:11px; flex-shrink:0;">
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 6: INCOME DETAILS
  const col6 = hideForBusiness ? `` : `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">INCOME DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Occupation</span>
              <select name="occupation" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <option value="">Select</option>
                ${createSelectOptions(occupations, client.occupation)}
              </select>
            </div>
            <div class="detail-row">
              <span class="detail-label">Income Source</span>
              <select name="income_source" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
                <option value="">Select</option>
                ${createSelectOptions(incomeSources, client.income_source)}
              </select>
            </div>
            <div class="detail-row">
              <span class="detail-label">Employer</span>
              <input type="text" name="employer" value="${client.employer || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">Monthly Income</span>
              <input type="text" name="monthly_income" value="${client.monthly_income || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 7: OTHER DETAILS
  const col7 = hideForBusiness ? `` : `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">OTHER DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Married</span>
              <div class="detail-value checkbox">
                <input type="checkbox" name="married" value="1" ${client.married ? 'checked' : ''}>
              </div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Spouse's Name</span>
              <input type="text" name="spouses_name" value="${client.spouses_name || ''}" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; font-size:11px;">
            </div>
            <div class="detail-row">
              <span class="detail-label">PEP</span>
              <div class="detail-value checkbox">
                <input type="checkbox" name="pep" value="1" ${client.pep ? 'checked' : ''}>
              </div>
            </div>
            <div class="detail-row">
              <span class="detail-label">PEP Details</span>
              <textarea name="pep_comment" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; min-height:40px; resize:vertical; font-size:11px;">${client.pep_comment || ''}</textarea>
            </div>
            <div class="detail-row">
              <span class="detail-label">Notes</span>
              <textarea name="notes" class="detail-value" style="flex:1; border:1px solid #ddd; padding:4px 6px; border-radius:2px; min-height:40px; resize:vertical; font-size:11px;">${client.notes || ''}</textarea>
            </div>
          </div>
        </div>
      </div>
    `;

  // Column 8: INSURABLES
  const col8 = hideForBusiness ? `` : `
      <div style="display:flex; flex-direction:column; gap:10px;">
        <div class="detail-section">
          <div class="detail-section-header">INSURABLES</div>
          <div class="detail-section-body">
            <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:center; margin-bottom:15px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Vehicle</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_vehicle" value="1" ${client.has_vehicle ? 'checked' : ''}>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Home</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_house" value="1" ${client.has_house ? 'checked' : ''}>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Business</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_business" value="1" ${client.has_business ? 'checked' : ''}>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Boat</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_boat" value="1" ${client.has_boat ? 'checked' : ''}>
                </div>
              </div>
            </div>
            <div style="margin-top:15px; border-top:1px solid #ddd; padding-top:8px;">
              <label style="font-size:10px; color:#555; font-weight:600; display:block; margin-bottom:4px;">Notes</label>
              <textarea name="insurables_notes" class="detail-value" style="min-height:40px; width:100%; resize:vertical; font-size:11px; padding:4px 6px; border:1px solid #ddd; background:#fff; border-radius:2px; box-sizing:border-box;">${client.insurables_notes || ''}</textarea>
            </div>
          </div>
        </div>
      </div>
    `;

  // Create the grid container
  const gridHTML = `
      <div id="clientEditFormGrid" style="display:grid; grid-template-columns:repeat(4, 1fr) !important; gap:10px !important;">
        ${col1}${col2}${col3}${col4}${col5}${col6}${col7}${col8}
      </div>
    `;

  formContainer.innerHTML = gridHTML;

  // Setup event listeners
  const dobInput = formContainer.querySelector('input[name="dob_dor"]');
  const expiryInput = formContainer.querySelector('input[name="id_expiry_date"]');
  if (dobInput) {
    dobInput.addEventListener('change', calculateAgeFromDOB);
  }
  if (expiryInput) {
    expiryInput.addEventListener('change', calculateIDExpiryDays);
  }

  // Setup WA toggle
  const waCheckbox = formContainer.querySelector('input[name="wa"]');
  if (waCheckbox) {
    waCheckbox.addEventListener('change', function () {
      const alternateNoRow = formContainer.querySelector('#alternate_no_row');
      if (alternateNoRow) {
        alternateNoRow.style.display = this.checked ? 'none' : '';
      }
    });
  }

  // Initial calculations are done above in the event listener setup
}



function closeClientDetailsModal() {
  closeClientPageView();
}

function closeClientPageView() {
  const clientPageView = document.getElementById('clientPageView');
  clientPageView.classList.remove('show');
  clientPageView.style.display = 'none';
  document.getElementById('clientsTableView').classList.remove('hidden');
  document.getElementById('clientDetailsPageContent').style.display = 'none';
  document.getElementById('clientFormPageContent').style.display = 'none';
  currentClientId = null;
}

// ============================================================================
// PHOTO & DOCUMENT UPLOAD
// ============================================================================

// Image preview handler for form
function handleImagePreview(event) {
  const file = event.target.files[0];
  if (!file) return;

  // Validate file type
  if (!file.type.match('image.*')) {
    showNotification('Please select an image file.', 'error');
    event.target.value = '';
    return;
  }

  // Capture the file input reference before async operation
  const fileInput = event.target;

  const reader = new FileReader();
  reader.onload = function (e) {
    const imageDataUrl = e.target.result;

    // In add mode (no currentClientId), add photo to pending documents
    if (!currentClientId) {
      const photoDoc = {
        file: file,
        type: 'Photo',
        name: 'Client Photo',
        size: file.size,
        preview: imageDataUrl,
        isPhoto: true
      };

      // Check if photo already exists in pending documents
      const existingPhotoIndex = pendingDocuments.findIndex(doc => doc.isPhoto);
      if (existingPhotoIndex >= 0) {
        pendingDocuments[existingPhotoIndex] = photoDoc;
      } else {
        pendingDocuments.push(photoDoc);
      }

      // Update pending documents display
      updatePendingDocumentsDisplay();

      // Also show preview in the imagePreviewContainer if it exists
      const imagePreviewContainer = document.getElementById('imagePreviewContainer');
      const imagePreview = document.getElementById('imagePreview');
      if (imagePreviewContainer && imagePreview) {
        imagePreview.src = imageDataUrl;
        imagePreviewContainer.style.display = 'block';
      }
    }

    // Update photo in INDIVIDUAL DETAILS section (for edit mode or form preview)
    const clientPhotoImg = document.getElementById('clientPhotoImg');
    const clientPhotoPreview = document.getElementById('clientPhotoPreview');

    if (clientPhotoImg) {
      // If photo img exists, update its src
      clientPhotoImg.src = imageDataUrl;
      clientPhotoImg.style.display = 'block';
      clientPhotoImg.style.cursor = 'pointer';
      // Make sure click handler allows changing photo
      clientPhotoImg.onclick = function () {
        if (fileInput) fileInput.click();
      };
    } else if (clientPhotoPreview) {
      // If placeholder exists, replace it with image
      const photoContainer = clientPhotoPreview.parentElement;
      if (photoContainer) {
        // Find the file input - it might be inside the placeholder or use the captured one
        let inputElement = clientPhotoPreview.querySelector('input[type="file"]');
        if (!inputElement) {
          inputElement = fileInput;
        }

        // Create new img element
        const newImg = document.createElement('img');
        newImg.src = imageDataUrl;
        newImg.alt = 'Photo';
        newImg.className = 'detail-photo';
        newImg.id = 'clientPhotoImg';
        newImg.style.cssText = 'display:block; cursor:pointer;';
        newImg.onclick = function () {
          if (inputElement) inputElement.click();
        };

        // Replace placeholder with image
        photoContainer.replaceChild(newImg, clientPhotoPreview);

        // Re-add the file input if it was removed or not in container
        if (inputElement) {
          // Remove from old location if needed
          if (inputElement.parentElement && inputElement.parentElement !== photoContainer) {
            inputElement.parentElement.removeChild(inputElement);
          }
          // Add to container if not already there
          if (inputElement.parentElement !== photoContainer) {
            inputElement.style.display = 'none';
            inputElement.onchange = handleImagePreview;
            photoContainer.appendChild(inputElement);
          }
        }
      }
    }
  };
  reader.readAsDataURL(file);
}

// Remove image preview
function removeImagePreview() {
  const imageInput = document.getElementById('image');
  const previewImg = document.getElementById('imagePreview');
  const imagePreviewContainer = document.getElementById('imagePreviewContainer');

  if (imageInput) {
    imageInput.value = '';
  }

  if (previewImg) {
    previewImg.src = '';
  }

  if (imagePreviewContainer) {
    imagePreviewContainer.style.display = 'none';
  }

  // Also remove from pending documents if in add mode
  if (!currentClientId) {
    const photoIndex = pendingDocuments.findIndex(doc => doc.isPhoto);
    if (photoIndex >= 0) {
      pendingDocuments.splice(photoIndex, 1);
      updatePendingDocumentsDisplay();
    }
  }
}

// Make removeImagePreview globally accessible
window.removeImagePreview = removeImagePreview;

// Photo upload handler
async function handlePhotoUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  const img = new Image();
  const reader = new FileReader();

  reader.onload = async function (e) {
    img.onload = async function () {
      const isValid = validatePassportPhoto(img,
        async () => {
          // In add mode, store photo for later upload
          if (!currentClientId) {
            // Store photo preview and file
            pendingPhoto = {
              file: file,
              preview: e.target.result
            };

            // Update photo preview in INDIVIDUAL DETAILS section
            const clientPhotoImg = document.getElementById('clientPhotoImg');
            const clientPhotoPreview = document.getElementById('clientPhotoPreview');

            if (clientPhotoImg) {
              clientPhotoImg.src = e.target.result;
              clientPhotoImg.style.display = 'block';
              clientPhotoImg.style.cursor = 'pointer';
            } else if (clientPhotoPreview) {
              // Replace placeholder with image
              const photoContainer = clientPhotoPreview.parentElement;
              if (photoContainer) {
                const fileInput = clientPhotoPreview.querySelector('input[type="file"]') || event.target;

                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.alt = 'Photo';
                newImg.className = 'detail-photo';
                newImg.id = 'clientPhotoImg';
                newImg.style.cssText = 'display:block; cursor:pointer;';
                newImg.onclick = function () {
                  if (fileInput) fileInput.click();
                };

                photoContainer.replaceChild(newImg, clientPhotoPreview);

                if (fileInput && fileInput.parentElement !== photoContainer) {
                  fileInput.style.display = 'none';
                  fileInput.onchange = handleImagePreview;
                  photoContainer.appendChild(fileInput);
                }
              }
            }

            // Add photo to pending documents list for preview
            const photoDoc = {
              file: file,
              type: 'Photo',
              name: 'Client Photo',
              size: file.size,
              preview: e.target.result,
              isPhoto: true
            };

            // Check if photo already exists in pending documents
            const existingPhotoIndex = pendingDocuments.findIndex(doc => doc.isPhoto);
            if (existingPhotoIndex >= 0) {
              pendingDocuments[existingPhotoIndex] = photoDoc;
            } else {
              pendingDocuments.push(photoDoc);
            }

            updatePendingDocumentsDisplay();

            event.target.value = '';
            return;
          }

          // In edit mode, upload immediately
          const formData = new FormData();
          formData.append('photo', file);

          try {
            const response = await fetch(`/clients/${currentClientId}/upload-photo`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: formData
            });

            const result = await response.json();

            if (result.success) {
              const clientRes = await fetch(`/clients/${currentClientId}`, {
                headers: {
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              });
              const client = await clientRes.json();

              // Refresh the details page if it's open
              const clientDetailsPageContent = document.getElementById('clientDetailsPageContent');
              if (clientDetailsPageContent && clientDetailsPageContent.style.display !== 'none') {
                // Reload the client data and refresh the view
                populateClientDetailsModal(client);
              }

              // Also refresh documents list
              const documentsList = document.getElementById('clientDocumentsList');
              if (documentsList) {
                documentsList.innerHTML = renderDocumentsList(client.documents || []);
              }

              alert('Photo uploaded successfully!');
            } else {
              alert('Error uploading photo: ' + (result.message || 'Unknown error'));
            }
          } catch (error) {
            console.error('Error:', error);
            alert('Error uploading photo: ' + error.message);
          }

          event.target.value = '';
        },
        (errorMsg) => {
          alert(errorMsg);
          event.target.value = '';
        }
      );
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

// Document upload modal functions
function openDocumentUploadModal() {
  document.getElementById('documentUploadModal').classList.add('show');
  document.body.style.overflow = 'hidden';

  // Update modal title based on mode
  const modalTitle = document.querySelector('#documentUploadModal .modal-header h4');
  if (modalTitle) {
    if (!currentClientId) {
      modalTitle.textContent = 'Add Document (Preview) - Will upload after saving client';
    } else {
      modalTitle.textContent = 'Upload Document';
    }
  }
}

function closeDocumentUploadModal() {
  document.getElementById('documentUploadModal').classList.remove('show');
  document.body.style.overflow = '';
  document.getElementById('documentUploadForm').reset();
  const previewContainer = document.getElementById('documentPreviewContainer');
  const previewContent = document.getElementById('documentPreviewContent');
  const previewInfo = document.getElementById('documentPreviewInfo');
  if (previewContainer) previewContainer.style.display = 'none';
  if (previewContent) previewContent.innerHTML = '';
  if (previewInfo) previewInfo.innerHTML = '';
}

// Preview document before upload
function previewDocument(event) {
  const file = event.target.files[0];
  const previewContainer = document.getElementById('documentPreviewContainer');
  const previewContent = document.getElementById('documentPreviewContent');
  const previewInfo = document.getElementById('documentPreviewInfo');

  if (!file || !previewContainer || !previewContent || !previewInfo) return;

  previewContainer.style.display = 'block';
  previewContent.innerHTML = '';
  previewInfo.innerHTML = '';

  const fileType = file.type;
  const fileName = file.name;
  const fileSize = (file.size / 1024 / 1024).toFixed(2);

  previewInfo.innerHTML = `<strong>File:</strong> ${fileName}<br><strong>Size:</strong> ${fileSize} MB<br><strong>Type:</strong> ${fileType || 'Unknown'}`;

  if (fileType.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = function (e) {
      previewContent.innerHTML = `<img src="${e.target.result}" alt="Document Preview" style="max-width:100%; max-height:400px; border:1px solid #ddd; border-radius:4px;">`;
    };
    reader.readAsDataURL(file);
  } else if (fileType === 'application/pdf') {
    const reader = new FileReader();
    reader.onload = function (e) {
      previewContent.innerHTML = `
          <div style="width:100%; text-align:center;">
            <embed src="${e.target.result}" type="application/pdf" width="100%" height="400px" style="border:1px solid #ddd; border-radius:4px;">
            <div style="margin-top:10px; color:#666; font-size:12px;">PDF Preview (scroll to view full document)</div>
          </div>
        `;
    };
    reader.readAsDataURL(file);
  } else {
    const fileExt = fileName.split('.').pop().toUpperCase();
    previewContent.innerHTML = `
        <div class="document-item" style="margin:0 auto;">
          <div class="document-icon" style="width:120px; height:120px; font-size:24px;">${fileExt}</div>
          <div style="font-size:12px; text-align:center; margin-top:10px; color:#666;">${fileName}</div>
        </div>
      `;
  }
}

// Get file preview for pending documents
async function getFilePreview(file) {
  return new Promise((resolve) => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => resolve(e.target.result);
      reader.readAsDataURL(file);
    } else {
      resolve(null);
    }
  });
}

// Update pending documents display
function updatePendingDocumentsDisplay() {
  const editDocumentsList = document.getElementById('editClientDocumentsList');
  if (!editDocumentsList) return;

  if (pendingDocuments.length === 0) {
    editDocumentsList.innerHTML = '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
    return;
  }

  let docsHTML = '';
  pendingDocuments.forEach((doc, index) => {
    // For photos, use the preview directly; for other files, get extension from name or type
    let fileExt = 'DOC';
    let isImage = false;
    let previewUrl = null;

    if (doc.isPhoto && doc.preview) {
      // Photo document
      isImage = true;
      previewUrl = doc.preview;
      fileExt = 'PHOTO';
    } else if (doc.name) {
      // Regular document
      fileExt = doc.name.split('.').pop().toUpperCase();
      isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
      previewUrl = doc.preview;
    } else if (doc.file) {
      // File object - check type
      const fileName = doc.file.name || '';
      fileExt = fileName.split('.').pop().toUpperCase() || 'DOC';
      isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
      previewUrl = doc.preview;
    }

    docsHTML += `
        <div class="document-item" style="position:relative; cursor:default;">
          ${isImage && previewUrl ?
        `<img src="${previewUrl}" alt="${doc.name || 'Photo'}" style="width:60px; height:60px; object-fit:cover; border-radius:4px; opacity:0.7;">` :
        `<div class="document-icon" style="opacity:0.7;">${fileExt}</div>`
      }
          <div style="font-size:11px; text-align:center;">${doc.name || 'Client Photo'}</div>
          <div style="font-size:9px; text-align:center; color:#999;">(Pending)</div>
          <button onclick="removePendingDocument(${index})" style="position:absolute; top:-5px; right:-5px; background:#dc3545; color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; font-size:12px; line-height:1;">×</button>
        </div>
      `;
  });
  editDocumentsList.innerHTML = docsHTML;
}

// Remove pending document (make it globally accessible)
window.removePendingDocument = function (index) {
  const doc = pendingDocuments[index];
  // If removing photo, also clear photo preview
  if (doc && doc.isPhoto) {
    pendingPhoto = null;
    const clientPhotoImg = document.getElementById('clientPhotoImg');
    const photoContainer = clientPhotoImg?.parentElement;
    if (photoContainer && clientPhotoImg) {
      // Replace with placeholder
      const fileInput = photoContainer.querySelector('input[type="file"]');
      const placeholder = document.createElement('div');
      placeholder.id = 'clientPhotoPreview';
      placeholder.style.cssText = 'flex-shrink:0; margin-top:13px; width:80px; height:100px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5; display:flex; align-items:center; justify-content:center; cursor:pointer; position:relative;';
      placeholder.onclick = function () {
        if (fileInput) fileInput.click();
      };
      placeholder.innerHTML = '<span style="font-size:10px; color:#999;">Click to upload</span>';
      if (fileInput) {
        fileInput.style.display = 'none';
        fileInput.onchange = handleImagePreview;
        placeholder.appendChild(fileInput);
      }
      photoContainer.replaceChild(placeholder, clientPhotoImg);
    }

    // Also clear image preview container
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    if (imagePreviewContainer) imagePreviewContainer.style.display = 'none';
    if (imagePreview) imagePreview.src = '';

    // Clear the file input
    const imageInput = document.getElementById('image');
    if (imageInput) imageInput.value = '';
  }
  pendingDocuments.splice(index, 1);
  updatePendingDocumentsDisplay();
};

// Document upload handler
async function handleDocumentUpload() {
  const documentType = document.getElementById('documentType').value;
  const documentFile = document.getElementById('documentFile').files[0];

  if (!documentType) {
    alert('Please select a document type');
    return;
  }

  if (!documentFile) {
    alert('Please select a file');
    return;
  }

  // In add mode, store document for later upload
  if (!currentClientId) {
    // Add to pending documents
    const preview = await getFilePreview(documentFile);
    pendingDocuments.push({
      file: documentFile,
      type: documentType,
      name: documentFile.name,
      size: documentFile.size,
      preview: preview
    });

    // Update pending documents display
    updatePendingDocumentsDisplay();

    closeDocumentUploadModal();
    alert('Document added to pending list. It will be uploaded after you save the client.');
    return;
  }

  const formData = new FormData();
  formData.append('document', documentFile);
  formData.append('document_type', documentType);

  try {
    const response = await fetch(`/clients/${currentClientId}/upload-document`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const clientRes = await fetch(`/clients/${currentClientId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      const client = await clientRes.json();

      updateDocumentsList(client);

      // Refresh the details page if it's open
      const clientDetailsPageContent = document.getElementById('clientDetailsPageContent');
      if (clientDetailsPageContent && clientDetailsPageContent.style.display !== 'none') {
        populateClientDetailsModal(client);
      }

      // Also refresh documents list in details page
      const documentsList = document.getElementById('clientDocumentsList');
      if (documentsList) {
        documentsList.innerHTML = renderDocumentsList(client.documents || []);
      }

      const clientDetailsModal = document.getElementById('clientDetailsModal');
      if (clientDetailsModal?.classList.contains('show')) {
        populateClientDetailsModal(client);
      }

      closeDocumentUploadModal();
      alert('Document uploaded successfully!');
    } else {
      alert('Error uploading document: ' + (result.message || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error uploading document: ' + error.message);
  }
}

// Render documents list HTML
function renderDocumentsList(documents) {
  if (!documents || documents.length === 0) {
    return '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
  }

  let docsHTML = '';
  documents.forEach(doc => {
    if (doc.file_path) {
      const fileExt = doc.format ? doc.format.toUpperCase() : (doc.file_path.split('.').pop().toUpperCase());
      const fileUrl = doc.file_path.startsWith('http') ? doc.file_path : `/storage/${doc.file_path}`;
      const isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
      const docName = doc.name || 'Document';
      docsHTML += `
          <div class="document-item" style="cursor:pointer;" onclick="previewUploadedDocument('${fileUrl}', '${fileExt}', '${docName}')">
            ${isImage ? `<img src="${fileUrl}" alt="${docName}" style="width:60px; height:60px; object-fit:cover; border-radius:4px;">` : `<div class="document-icon">${fileExt}</div>`}
            <div style="font-size:11px; text-align:center;">${docName}</div>
          </div>
        `;
    }
  });
  return docsHTML;
}

// Update documents list in Edit Client modal
function updateDocumentsList(client) {
  const editDocumentsList = document.getElementById('editClientDocumentsList');
  if (editDocumentsList) {
    editDocumentsList.innerHTML = renderDocumentsList(client.documents || []);
  }
}

function editClientFromModal() {
  if (currentClientId) {
    closeClientDetailsModal();
    openEditClient(currentClientId);
  }
}

// Preview client photo and validate passport size
function previewClientPhoto(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('clientPhotoImg');
  const previewContainer = document.getElementById('clientPhotoPreview');
  const imageInput = event.target;

  if (!file || !preview || !previewContainer) return;

  const img = new Image();
  const reader = new FileReader();

  reader.onload = function (e) {
    img.onload = function () {
      validatePassportPhoto(img,
        () => {
          preview.src = e.target.result;
          preview.style.display = 'block';
          const photoSpan = previewContainer.querySelector('span');
          if (photoSpan) photoSpan.style.display = 'none';
        },
        (errorMsg) => {
          alert(errorMsg);
          imageInput.value = '';
          preview.src = '';
          preview.style.display = 'none';
          const photoSpan = previewContainer.querySelector('span');
          if (photoSpan) photoSpan.style.display = 'block';
        }
      );
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

// ============================================================================
// CLIENT MODAL MANAGEMENT
// ============================================================================

function openClientModal(mode, client = null) {
  const modal = document.getElementById('clientModal');
  const modalForm = modal.querySelector('form');
  const formMethod = document.getElementById('clientFormMethod');
  const deleteBtn = document.getElementById('clientDeleteBtn');

  const fieldNames = ['salutation', 'first_name', 'other_names', 'surname', 'client_type', 'nin_bcrn', 'dob_dor', 'id_expiry_date', 'passport_no', 'mobile_no', 'alternate_no', 'email_address', 'occupation', 'employer', 'income_source', 'monthly_income', 'source', 'source_name', 'agent', 'agency', 'status', 'signed_up', 'location', 'district', 'island', 'country', 'po_box_no', 'spouses_name', 'contact_person', 'pep_comment', 'notes'];

  if (mode === 'add') {
    modalForm.action = clientsStoreRoute;
    formMethod.innerHTML = '';
    deleteBtn.style.display = 'none';
    modalForm.reset();

    const clientTypeSelect = document.getElementById('client_type');
    if (clientTypeSelect && !clientTypeSelect.value) {
      clientTypeSelect.value = 'Individual';
    }

    if (clientTypeSelect && (clientTypeSelect.value === 'Individual' || !clientTypeSelect.value)) {
      // Immediately show Individual fields
      forceIndividualFieldsVisible();
      // Also apply with delays to catch any dynamically added fields
      applyWithDelays(forceIndividualFieldsVisible, [10, 50, 100, 200, 300, 500]);
      let checkCount = 0;
      const maxChecks = 20;
      const checkInterval = setInterval(() => {
        if (checkCount >= maxChecks) {
          clearInterval(checkInterval);
          return;
        }
        const currentType = document.getElementById('client_type')?.value;
        if (currentType === 'Individual' || !currentType) {
          forceIndividualFieldsVisible();
        }
        checkCount++;
      }, 100);
    }

    const imageInput = document.getElementById('image');
    if (imageInput) imageInput.required = false;

    // Clear checkboxes
    ['married', 'pep', 'has_vehicle', 'has_house', 'has_business', 'has_boat'].forEach(id => {
      const checkbox = document.getElementById(id);
      if (checkbox) checkbox.checked = false;
    });

    const waCheckbox = document.getElementById('wa');
    if (waCheckbox) {
      waCheckbox.checked = false;
      const alternateNoRow = document.getElementById('alternate_no_row');
      if (alternateNoRow) alternateNoRow.style.display = '';
    }

    // Clear photo preview
    const photoImg = document.getElementById('clientPhotoImg');
    const photoPreview = document.getElementById('clientPhotoPreview');
    if (photoImg) photoImg.style.display = 'none';
    if (photoPreview) {
      const photoSpan = photoPreview.querySelector('span');
      if (photoSpan) photoSpan.style.display = 'block';
    }

    // Clear calculated fields
    ['dob_age', 'id_expiry_days'].forEach(id => {
      const field = document.getElementById(id);
      if (field) field.value = '';
    });

    // Clear documents list
    const editDocumentsList = document.getElementById('editClientDocumentsList');
    if (editDocumentsList) editDocumentsList.innerHTML = '<div style="color:#999; font-size:12px;">No documents uploaded</div>';

    // Clear image preview container
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    if (imagePreviewContainer) {
      imagePreviewContainer.style.display = 'none';
      const imagePreview = document.getElementById('imagePreview');
      if (imagePreview) imagePreview.src = '';
    }

    // Clear pending documents and photo when opening add form
    pendingDocuments = [];
    pendingPhoto = null;
    updatePendingDocumentsDisplay();

    // Show "Add Document" button in add mode
    const addDocumentBtn2 = document.getElementById('addDocumentBtn2');
    if (addDocumentBtn2) {
      addDocumentBtn2.style.display = 'inline-block';
    }
    // Hide other document buttons
    ['addDocumentBtn1', 'addDocumentBtn3'].forEach(btnId => {
      const btn = document.getElementById(btnId);
      if (btn) btn.style.display = 'none';
    });

    // Update pending documents display to show any pending photos/documents
    setTimeout(() => {
      updatePendingDocumentsDisplay();
    }, 100);

    setupWaToggle();
  } else {
    modalForm.action = `/clients/${currentClientId}`;
    formMethod.innerHTML = `@method('PUT')`;
    deleteBtn.style.display = 'inline-block';

    populateFormFields(document, client, fieldNames);

    // Set checkboxes
    ['married', 'pep', 'has_vehicle', 'has_house', 'has_business', 'has_boat'].forEach(id => {
      const checkbox = document.getElementById(id);
      if (checkbox) checkbox.checked = !!client[id];
    });

    const waCheckboxEdit = document.getElementById('wa');
    if (waCheckboxEdit) {
      waCheckboxEdit.checked = (client.wa == "1") ? true : false;
      const alternateNoRow = document.getElementById('alternate_no_row');
      if (alternateNoRow) {
        if (waCheckboxEdit.checked) {
          hideElement(alternateNoRow);
        } else {
          showElement(alternateNoRow);
        }
      }
    }
    const waBusinessCheckboxEdit = document.getElementById('wa_business');
    if (waBusinessCheckboxEdit) {
      waBusinessCheckboxEdit.checked = (client.wa == "1") ? true : false;
      const alternateNoRowBusiness = document.getElementById('alternate_no_row_business');
      if (alternateNoRowBusiness) {
        if (waBusinessCheckboxEdit.checked) {
          hideElement(alternateNoRowBusiness);
        } else {
          showElement(alternateNoRowBusiness);
        }
      }
    }

    setupWaToggle();

    // Set existing image if present
    const imageInput = document.getElementById('image');
    if (client.image) {
      document.getElementById('existing_image').value = client.image;
      const photoImg = document.getElementById('clientPhotoImg');
      const photoPreview = document.getElementById('clientPhotoPreview');
      if (photoImg && photoPreview) {
        photoImg.src = client.image.startsWith('http') ? client.image : `/storage/${client.image}`;
        photoImg.style.display = 'block';
        const photoSpan = photoPreview.querySelector('span');
        if (photoSpan) photoSpan.style.display = 'none';
      }
      if (imageInput) imageInput.required = false;
    } else {
      if (imageInput) imageInput.required = false;
    }

    updateDocumentsList(client);
    calculateAgeFromDOB();
    calculateIDExpiryDays();

    setTimeout(() => handleClientTypeChange(), 150);
  }

  // Add event listeners for calculations
  const dobInput = document.getElementById('dob_dor');
  const expiryInput = document.getElementById('id_expiry_date');
  if (dobInput) {
    dobInput.removeEventListener('change', calculateAgeFromDOB);
    dobInput.addEventListener('change', calculateAgeFromDOB);
  }
  if (expiryInput) {
    expiryInput.removeEventListener('change', calculateIDExpiryDays);
    expiryInput.addEventListener('change', calculateIDExpiryDays);
  }

  setupWaToggle();

  // Setup client type change listener
  const clientTypeSelect = document.getElementById('client_type');
  if (clientTypeSelect) {
    if (mode === 'add' && !clientTypeSelect.value) {
      clientTypeSelect.value = 'Individual';
    }

    // Initialize fields based on current value
    const initializeFields = () => {
      handleClientTypeChange();
      const currentValue = clientTypeSelect.value || 'Individual';
      if (currentValue === 'Individual') {
        forceIndividualFieldsVisible();
      }
    };

    applyWithDelays(initializeFields, [10, 50, 100, 200]);
  }

  // Clone form content from modal to page view
  const pageFormContainer = document.getElementById('clientFormPageContent');
  const pageForm = pageFormContainer?.querySelector('form');
  const formContentDiv = pageForm?.querySelector('div[style*="padding:12px"]');

  if (modalForm && pageForm && pageFormContainer && formContentDiv) {
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody) {
      formContentDiv.innerHTML = '';

      const gridContainer = modalBody.querySelector('div[style*="grid-template-columns"]');
      if (gridContainer && !formContentDiv.querySelector('div[style*="grid-template-columns"]')) {
        const clonedGrid = gridContainer.cloneNode(true);
        formContentDiv.appendChild(clonedGrid);

        setTimeout(() => {
          const clonedClientType = formContentDiv.querySelector('#client_type');
          if (clonedClientType && (clonedClientType.value === 'Individual' || !clonedClientType.value)) {
            hideBusinessFields();
            showIndividualFields();
          }
        }, 10);
      }

      // Clone Insurables section
      const insurablesSection = modalBody.querySelector('#insurablesSection');
      if (insurablesSection && !formContentDiv.querySelector('#insurablesSection')) {
        const clonedInsurables = insurablesSection.cloneNode(true);
        formContentDiv.appendChild(clonedInsurables);
        // Ensure it's always visible
        clonedInsurables.style.display = 'block';
        clonedInsurables.style.setProperty('display', 'block', 'important');
      }

      // Clone documents section
      const editDocumentsList = modalBody.querySelector('#editClientDocumentsList');
      const editFormDocumentsSection = document.getElementById('editFormDocumentsSection');
      if (editDocumentsList && editFormDocumentsSection) {
        let documentsSection = editDocumentsList.closest('div[style*="margin-top"]') ||
          editDocumentsList.parentElement?.parentElement;
        if (documentsSection) {
          editFormDocumentsSection.innerHTML = '';

          const clonedDocs = documentsSection.cloneNode(true);
          const docsTitle = clonedDocs.querySelector('h4');
          const docsList = clonedDocs.querySelector('#editClientDocumentsList');
          const docsButtons = clonedDocs.querySelector('div[style*="justify-content:flex-end"]');

          if (docsTitle) {
            const titleClone = docsTitle.cloneNode(true);
            titleClone.style.marginBottom = '10px';
            titleClone.style.color = '#000';
            titleClone.style.fontSize = '13px';
            titleClone.style.fontWeight = 'bold';
            editFormDocumentsSection.appendChild(titleClone);
          }

          if (docsList) {
            const listClone = docsList.cloneNode(true);
            listClone.style.marginBottom = '10px';
            editFormDocumentsSection.appendChild(listClone);
          }

          if (docsButtons) {
            editFormDocumentsSection.appendChild(docsButtons.cloneNode(true));
          }

          editFormDocumentsSection.style.display = 'block';
        }
      }

      pageForm.method = 'POST';
      pageForm.action = modalForm.action;
      pageForm.enctype = 'multipart/form-data';
      pageForm.setAttribute('novalidate', 'novalidate');

      const pageMethodDiv = pageForm.querySelector('#clientFormMethod');
      if (pageMethodDiv && formMethod) {
        pageMethodDiv.innerHTML = formMethod.innerHTML;
      }

      // Ensure form handler is attached to the cloned form
      if (pageForm && !pageForm.hasAttribute('data-handler-attached')) {
        attachFormSubmitHandler(pageForm);
        pageForm.setAttribute('data-handler-attached', 'true');
      }

      // If editing, populate the cloned form fields
      if (mode === 'edit' && client) {
        populateFormFields(formContentDiv, client, [...fieldNames, 'designation']);

        const businessNameInput = formContentDiv.querySelector('#business_name');
        if (businessNameInput && BUSINESS_TYPES.includes(client.client_type)) {
          businessNameInput.value = client.client_name || '';
        }

        // Set checkboxes in cloned form
        ['married', 'pep', 'wa', 'has_vehicle', 'has_house', 'has_business', 'has_boat'].forEach(id => {
          const checkbox = formContentDiv.querySelector(`#${id}`);
          if (checkbox) checkbox.checked = !!client[id];
        });
        // Also set business wa checkbox if it exists
        const waBusinessCheckbox = formContentDiv.querySelector('#wa_business');
        if (waBusinessCheckbox) {
          waBusinessCheckbox.checked = (client.wa == "1") ? true : false;
        }

        // Set existing image if present
        const imageInput = formContentDiv.querySelector('#image');
        const existingImageInput = formContentDiv.querySelector('#existing_image');
        if (client.image && existingImageInput) {
          existingImageInput.value = client.image;
          const photoImg = formContentDiv.querySelector('#clientPhotoImg');
          const photoPreview = formContentDiv.querySelector('#clientPhotoPreview');
          if (photoImg && photoPreview) {
            photoImg.src = client.image.startsWith('http') ? client.image : `/storage/${client.image}`;
            photoImg.style.display = 'block';
            const photoSpan = photoPreview.querySelector('span');
            if (photoSpan) photoSpan.style.display = 'none';
          }
          if (imageInput) imageInput.required = false;
        } else {
          if (imageInput) imageInput.required = true;
        }

        // Calculate age and expiry days for cloned form
        const dobInput = formContentDiv.querySelector('#dob_dor');
        const ageInput = formContentDiv.querySelector('#dob_age');
        const expiryInput = formContentDiv.querySelector('#id_expiry_date');
        const daysInput = formContentDiv.querySelector('#id_expiry_days');

        if (dobInput && ageInput && dobInput.value) {
          ageInput.value = calculateAge(dobInput.value);
        }

        if (expiryInput && daysInput && expiryInput.value) {
          daysInput.value = calculateDaysUntilExpiry(expiryInput.value);
        }

        // Toggle Alternate No field visibility
        const waCheckbox = formContentDiv.querySelector('#wa');
        if (waCheckbox) {
          const alternateNoRow = formContentDiv.querySelector('#alternate_no_row');
          if (alternateNoRow) {
            alternateNoRow.style.display = waCheckbox.checked ? 'none' : '';
          }
        }

        // Attach event listeners to cloned form elements
        const clonedDobInput = formContentDiv.querySelector('#dob_dor');
        const clonedExpiryInput = formContentDiv.querySelector('#id_expiry_date');
        if (clonedDobInput) {
          clonedDobInput.addEventListener('change', () => {
            const dobInput = formContentDiv.querySelector('#dob_dor');
            const ageInput = formContentDiv.querySelector('#dob_age');
            if (dobInput && ageInput && dobInput.value) {
              ageInput.value = calculateAge(dobInput.value);
            }
          });
        }
        if (clonedExpiryInput) {
          clonedExpiryInput.addEventListener('change', () => {
            const expiryInput = formContentDiv.querySelector('#id_expiry_date');
            const daysInput = formContentDiv.querySelector('#id_expiry_days');
            if (expiryInput && daysInput && expiryInput.value) {
              daysInput.value = calculateDaysUntilExpiry(expiryInput.value);
            }
          });
        }

        // Attach client type change listener to cloned form
        const clonedClientTypeSelect = formContentDiv.querySelector('#client_type');
        if (clonedClientTypeSelect) {
          if (mode === 'add' && !clonedClientTypeSelect.value) {
            clonedClientTypeSelect.value = 'Individual';
          }

          clonedClientTypeSelect.addEventListener('change', function () {
            const selectedType = this.value;
            if (selectedType === 'Individual') {
              hideBusinessFields(formContentDiv);
              showIndividualFields(formContentDiv);
              forceIndividualFieldsVisible(formContentDiv);
            } else if (isBusinessType(selectedType)) {
              showBusinessFields(formContentDiv);
            }
            handleClientTypeChangeInForm(formContentDiv);
          });

          const initClonedFormFields = () => {
            const selectedType = clonedClientTypeSelect.value || 'Individual';
            if (selectedType === 'Individual') {
              hideBusinessFields(formContentDiv);
              showIndividualFields(formContentDiv);
              forceIndividualFieldsVisible(formContentDiv);
            } else if (isBusinessType(selectedType)) {
              showBusinessFields(formContentDiv);
            }
            handleClientTypeChangeInForm(formContentDiv);
          };

          applyWithDelays(initClonedFormFields, [10, 50, 100]);
        } else {
          hideAllConditionalFields(formContentDiv);
        }

        // Attach WA checkbox listener to cloned form (individual)
        const clonedWaCheckbox = formContentDiv.querySelector('#wa');
        const clonedAlternateNoRow = formContentDiv.querySelector('#alternate_no_row');
        if (clonedWaCheckbox && clonedAlternateNoRow) {
          clonedWaCheckbox.addEventListener('change', () => {
            toggleAlternateNoVisibility(clonedWaCheckbox, clonedAlternateNoRow);
          });
        }
        // Attach WA checkbox listener to cloned form (business)
        const clonedWaBusinessCheckbox = formContentDiv.querySelector('#wa_business');
        const clonedAlternateNoRowBusiness = formContentDiv.querySelector('#alternate_no_row_business');
        if (clonedWaBusinessCheckbox && clonedAlternateNoRowBusiness) {
          clonedWaBusinessCheckbox.addEventListener('change', () => {
            toggleAlternateNoVisibility(clonedWaBusinessCheckbox, clonedAlternateNoRowBusiness);
          });
        }

        // Setup WA toggle for cloned form
        setupWaToggle(formContentDiv);
      }
    }
  }

  // Set page title (only if elements exist - they may not exist in modal view)
  const clientPageTitle = document.getElementById('clientPageTitle');
  const clientPageName = document.getElementById('clientPageName');
  const editClientFromPageBtn = document.getElementById('editClientFromPageBtn');

  if (mode === 'add') {
    if (clientPageTitle) clientPageTitle.textContent = 'Client - Add New';
    if (clientPageName) clientPageName.textContent = '';
    if (editClientFromPageBtn) editClientFromPageBtn.style.display = 'none';
  } else {
    const clientName = `${client.first_name || ''} ${client.surname || ''}`.trim() || 'Unknown';
    if (clientPageTitle) clientPageTitle.textContent = 'Edit Client';
    if (clientPageName) clientPageName.textContent = clientName;
    if (editClientFromPageBtn) editClientFromPageBtn.style.display = 'none';
  }

  // Hide table view, show page view
  document.getElementById('clientsTableView').classList.add('hidden');
  const clientPageView = document.getElementById('clientPageView');
  clientPageView.classList.add('show');
  clientPageView.style.display = 'block';
  document.getElementById('clientDetailsPageContent').style.display = 'none';
  document.getElementById('clientFormPageContent').style.display = 'block';

  // Ensure form handler is attached
  setTimeout(initializeFormHandlers, 100);

  // Ensure Insurables section is always visible
  const insurablesSection = document.getElementById('insurablesSection');
  if (insurablesSection) {
    insurablesSection.style.display = 'block';
    insurablesSection.style.setProperty('display', 'block', 'important');
  }

  // Initialize page view fields
  const initializePageViewFields = () => {
    const clientTypeSelect = document.getElementById('client_type');
    if (clientTypeSelect && (clientTypeSelect.value === 'Individual' || !clientTypeSelect.value)) {
      forceIndividualFieldsVisible();
    }
    handleClientTypeChange();

    // Ensure Insurables section is always visible
    const insurablesSection = document.getElementById('insurablesSection');
    if (insurablesSection) {
      insurablesSection.style.display = 'block';
      insurablesSection.style.setProperty('display', 'block', 'important');
    }

    const addDocumentBtn2 = document.getElementById('addDocumentBtn2');
    if (addDocumentBtn2) {
      addDocumentBtn2.style.display = 'inline-block'; // Show in both add and edit modes
    }
  };

  applyWithDelays(initializePageViewFields, [10, 50, 100, 200, 300]);
}

function closeClientModal() {
  closeClientPageView();
}

function deleteClient() {
  if (!currentClientId) return;
  if (!confirm('Delete this client?')) return;
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/clients/${currentClientId}`;
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

// ============================================================================
// COLUMN MODAL FUNCTIONS
// ============================================================================

function openColumnModal() {
  document.getElementById('tableResponsive').classList.add('no-scroll');
  document.querySelectorAll('.column-checkbox').forEach(cb => {
    cb.checked = MANDATORY_FIELDS.includes(cb.value) || selectedColumns.includes(cb.value);
  });
  document.body.style.overflow = 'hidden';
  document.getElementById('columnModal').classList.add('show');
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
    if (!MANDATORY_FIELDS.includes(cb.value)) {
      cb.checked = false;
    }
  });
}

function saveColumnSettings() {
  const items = Array.from(document.querySelectorAll('#columnSelection .column-item'));
  const order = items.map(item => item.dataset.column);
  const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n => n.value);

  MANDATORY_FIELDS.forEach(field => {
    if (!checked.includes(field)) {
      checked.push(field);
    }
  });

  const orderedChecked = order.filter(col => checked.includes(col));

  const form = document.getElementById('columnForm');
  const existing = form.querySelectorAll('input[name="columns[]"]');
  existing.forEach(e => e.remove());

  orderedChecked.forEach(c => {
    const i = document.createElement('input');
    i.type = 'hidden';
    i.name = 'columns[]';
    i.value = c;
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

  const columnItems = columnSelection.querySelectorAll('.column-item');

  columnItems.forEach(item => {
    if (item.dataset.dragInitialized === 'true') return;
    item.dataset.dragInitialized = 'true';

    const checkbox = item.querySelector('.column-checkbox');
    if (checkbox) {
      checkbox.addEventListener('mousedown', e => e.stopPropagation());
      checkbox.addEventListener('click', e => e.stopPropagation());
    }

    const label = item.querySelector('label');
    if (label) {
      label.addEventListener('mousedown', e => {
        if (e.target === label) e.preventDefault();
      });
    }

    item.addEventListener('dragstart', e => {
      draggedElement = item;
      item.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/html', item.outerHTML);
      e.dataTransfer.setData('text/plain', item.dataset.column);
    });

    item.addEventListener('dragend', () => {
      item.classList.remove('dragging');
      columnItems.forEach(i => i.classList.remove('drag-over'));
      if (dragOverElement) {
        dragOverElement.classList.remove('drag-over');
        dragOverElement = null;
      }
      draggedElement = null;
    });

    item.addEventListener('dragover', e => {
      e.preventDefault();
      e.stopPropagation();
      e.dataTransfer.dropEffect = 'move';

      if (draggedElement && item !== draggedElement) {
        if (dragOverElement && dragOverElement !== item) {
          dragOverElement.classList.remove('drag-over');
        }

        item.classList.add('drag-over');
        dragOverElement = item;

        const rect = item.getBoundingClientRect();
        const midpoint = rect.top + (rect.height / 2);
        const next = e.clientY > midpoint;

        if (next) {
          if (item.nextSibling && item.nextSibling !== draggedElement) {
            item.parentNode.insertBefore(draggedElement, item.nextSibling);
          } else if (!item.nextSibling) {
            item.parentNode.appendChild(draggedElement);
          }
        } else {
          if (item.previousSibling !== draggedElement) {
            item.parentNode.insertBefore(draggedElement, item);
          }
        }
      }
    });

    item.addEventListener('dragenter', e => {
      e.preventDefault();
      if (draggedElement && item !== draggedElement) {
        item.classList.add('drag-over');
      }
    });

    item.addEventListener('dragleave', e => {
      if (!item.contains(e.relatedTarget)) {
        item.classList.remove('drag-over');
        if (dragOverElement === item) {
          dragOverElement = null;
        }
      }
    });

    item.addEventListener('drop', e => {
      e.preventDefault();
      e.stopPropagation();
      item.classList.remove('drag-over');
      dragOverElement = null;
      return false;
    });
  });
}

// ============================================================================
// DOCUMENT PREVIEW MODALS
// ============================================================================

function previewUploadedDocument(fileUrl, fileExt, documentName) {
  let previewModal = document.getElementById('documentPreviewModal');
  if (!previewModal) {
    previewModal = document.createElement('div');
    previewModal.id = 'documentPreviewModal';
    previewModal.className = 'modal';
    previewModal.innerHTML = `
        <div class="modal-content" style="max-width:90%; max-height:90vh; overflow:auto;">
          <div class="modal-header">
            <h4>${documentName}</h4>
            <button type="button" class="modal-close" onclick="closeDocumentPreviewModal()">×</button>
          </div>
          <div class="modal-body" style="text-align:center; padding:20px;">
            <div id="uploadedDocumentPreview"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeDocumentPreviewModal()">Close</button>
          </div>
        </div>
      `;
    document.body.appendChild(previewModal);
  }

  const previewContent = document.getElementById('uploadedDocumentPreview');
  const isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
  const isPDF = fileExt === 'PDF';

  if (isImage) {
    previewContent.innerHTML = `<img src="${fileUrl}" alt="${documentName}" style="max-width:100%; max-height:70vh; border:1px solid #ddd; border-radius:4px;">`;
  } else if (isPDF) {
    previewContent.innerHTML = `<embed src="${fileUrl}" type="application/pdf" width="100%" height="600px" style="border:1px solid #ddd; border-radius:4px;">`;
  } else {
    previewContent.innerHTML = `
        <div style="padding:40px;">
          <div class="document-icon" style="width:120px; height:120px; font-size:32px; margin:0 auto;">${fileExt}</div>
          <div style="margin-top:20px; font-size:16px; color:#666;">${documentName}</div>
          <div style="margin-top:10px;">
            <a href="${fileUrl}" target="_blank" class="btn-save" style="display:inline-block; text-decoration:none; padding:8px 16px;">Download</a>
          </div>
        </div>
      `;
  }

  previewModal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeDocumentPreviewModal() {
  const previewModal = document.getElementById('documentPreviewModal');
  if (previewModal) {
    previewModal.classList.remove('show');
    document.body.style.overflow = '';
  }
}

function previewClientPhotoModal(photoUrl) {
  let photoModal = document.getElementById('clientPhotoPreviewModal');
  if (!photoModal) {
    photoModal = document.createElement('div');
    photoModal.id = 'clientPhotoPreviewModal';
    photoModal.className = 'modal';
    photoModal.innerHTML = `
        <div class="modal-content" style="max-width:90%; max-height:90vh; overflow:auto; text-align:center;">
          <div class="modal-header">
            <h4>Client Photo</h4>
            <button type="button" class="modal-close" onclick="closeClientPhotoPreviewModal()">×</button>
          </div>
          <div class="modal-body" style="padding:20px; text-align:center;">
            <img src="${photoUrl}" alt="Client Photo" style="max-width:100%; max-height:70vh; border:1px solid #ddd; border-radius:4px; object-fit:contain;">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeClientPhotoPreviewModal()">Close</button>
          </div>
        </div>
      `;
    document.body.appendChild(photoModal);
  } else {
    const img = photoModal.querySelector('img');
    if (img) img.src = photoUrl;
  }

  photoModal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeClientPhotoPreviewModal() {
  const photoModal = document.getElementById('clientPhotoPreviewModal');
  if (photoModal) {
    photoModal.classList.remove('show');
    document.body.style.overflow = '';
  }
}

// Close modals on ESC or backdrop
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    closeClientModal();
    closeColumnModal();
    closeClientDetailsModal();
    closeDocumentUploadModal();
    closeDocumentPreviewModal();
    closeClientPhotoPreviewModal();
  }
});

document.querySelectorAll('.modal').forEach(m => {
  m.addEventListener('click', e => {
    if (e.target === m) {
      m.classList.remove('show');
      document.body.style.overflow = '';
      if (m.id === 'documentUploadModal') {
        document.getElementById('documentUploadForm').reset();
      }
    }
  });
});

// ============================================================================
// FORM SUBMISSION
// ============================================================================

// Attach form submission handler to all forms with id="clientForm"
function attachFormSubmitHandler(form) {
  if (!form || form.hasAttribute('data-handler-attached')) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = this;
    const clientType = form.querySelector('#client_type')?.value || form.querySelector('select[name="client_type"]')?.value;
    const isIndividual = isIndividualType(clientType);
    const isBusiness = isBusinessType(clientType);

    // Helper function to check if element is visible
    function isElementVisible(element) {
      if (!element) return false;

      // Check computed style
      const style = window.getComputedStyle(element);
      if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
        return false;
      }

      // Check offsetParent (hidden elements have null offsetParent)
      if (element.offsetParent === null && style.position !== 'fixed') {
        return false;
      }

      // Check parent containers
      let parent = element.parentElement;
      while (parent && parent !== form) {
        const parentStyle = window.getComputedStyle(parent);
        if (parentStyle.display === 'none') {
          return false;
        }
        parent = parent.parentElement;
      }

      return true;
    }

    // Remove required attribute from hidden fields to prevent browser validation errors
    const fieldsWithRequiredRemoved = [];
    form.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
      if (!isElementVisible(field)) {
        field.removeAttribute('required');
        fieldsWithRequiredRemoved.push(field);
      }
    });

    // Disable hidden duplicate fields to prevent submission conflicts
    if (isBusiness) {
      form.querySelectorAll('[data-field-type="individual"] input, [data-field-type="individual"] select, [data-field-type="individual"] textarea').forEach(field => {
        if (!isElementVisible(field)) {
          field.disabled = true;
          // Also remove required if not already removed
          if (field.hasAttribute('required') && !fieldsWithRequiredRemoved.includes(field)) {
            field.removeAttribute('required');
            fieldsWithRequiredRemoved.push(field);
          }
        }
      });
    } else if (isIndividual) {
      form.querySelectorAll('[data-field-type="business"] input, [data-field-type="business"] select, [data-field-type="business"] textarea').forEach(field => {
        if (!isElementVisible(field)) {
          field.disabled = true;
          // Also remove required if not already removed
          if (field.hasAttribute('required') && !fieldsWithRequiredRemoved.includes(field)) {
            field.removeAttribute('required');
            fieldsWithRequiredRemoved.push(field);
          }
        }
      });
    }

    // Also handle fields that might be hidden in the new edit form structure
    // Check for fields with same name but different IDs (individual vs business)
    const fieldNames = ['mobile_no', 'signed_up', 'source', 'status', 'district', 'alternate_no', 'email_address', 'location', 'island', 'country', 'po_box_no', 'source_name'];
    fieldNames.forEach(name => {
      const fields = form.querySelectorAll(`[name="${name}"]`);
      if (fields.length > 1) {
        // Multiple fields with same name - disable hidden ones
        fields.forEach(field => {
          if (!isElementVisible(field)) {
            field.disabled = true;
            if (field.hasAttribute('required') && !fieldsWithRequiredRemoved.includes(field)) {
              field.removeAttribute('required');
              fieldsWithRequiredRemoved.push(field);
            }
          }
        });
      }
    });

    // Check required fields (only visible ones)
    const req = form.querySelectorAll('[required]:not([disabled])');
    let ok = true;
    const missingFields = [];

    req.forEach(f => {
      // Double check field is actually visible
      if (!isElementVisible(f)) {
        // Field is hidden, remove required and skip validation
        if (!fieldsWithRequiredRemoved.includes(f)) {
          f.removeAttribute('required');
          fieldsWithRequiredRemoved.push(f);
        }
        return;
      }

      // For business types, skip individual-specific required fields
      if (isBusiness) {
        // Skip first_name, surname, and other individual-only fields
        if (f.id === 'first_name' || f.id === 'surname' || f.name === 'first_name' || f.name === 'surname' ||
          f.id === 'salutation' || f.name === 'salutation' ||
          f.id === 'other_names' || f.name === 'other_names' ||
          f.id === 'passport_no' || f.name === 'passport_no' ||
          f.id === 'dob_dor' || f.name === 'dob_dor' ||
          f.id === 'nin_bcrn' || (f.name === 'nin_bcrn' && f.closest('[data-field-type="individual"]'))) {
          return;
        }
      }

      // For individual types, skip business-specific required fields
      if (isIndividual) {
        // Skip business_name and other business-only fields
        if (f.id === 'business_name' || f.name === 'business_name' ||
          f.id === 'contact_person' || (f.name === 'contact_person' && f.closest('[data-field-type="business"]'))) {
          return;
        }
      }

      const fieldValue = String(f.value || '').trim();
      if (!fieldValue) {
        ok = false;
        f.style.borderColor = 'red';
        const label = f.closest('.detail-row')?.querySelector('.detail-label')?.textContent || f.name || f.id;
        if (!missingFields.includes(label)) {
          missingFields.push(label);
        }
      } else {
        f.style.borderColor = '';
      }
    });

    // Additional validation for business types - check business_name
    if (isBusiness) {
      const businessNameField = form.querySelector('#business_name') || form.querySelector('input[name="business_name"]');
      if (businessNameField && isElementVisible(businessNameField)) {
        const businessName = String(businessNameField.value || '').trim();
        if (!businessName) {
          ok = false;
          businessNameField.style.borderColor = 'red';
          if (!missingFields.includes('Name')) {
            missingFields.push('Name');
          }
        } else {
          businessNameField.style.borderColor = '';
        }
      }
    }

    if (!ok) {
      form.querySelectorAll('input[disabled], select[disabled], textarea[disabled]').forEach(f => f.disabled = false);
      // Restore required attributes for next attempt
      fieldsWithRequiredRemoved.forEach(field => {
        field.setAttribute('required', '');
      });
      const missingFieldsList = missingFields.length > 0 ? '\nMissing: ' + missingFields.join(', ') : '';
      alert('Please fill required fields' + missingFieldsList);
      return;
    }

    const formData = new FormData(form);

    // Re-enable disabled fields after form data is collected
    form.querySelectorAll('input[disabled], select[disabled], textarea[disabled]').forEach(f => f.disabled = false);

    // Restore required attributes that were removed
    fieldsWithRequiredRemoved.forEach(field => {
      field.setAttribute('required', '');
    });

    const isEdit = form.action.includes('/clients/') && form.action !== clientsStoreRoute;
    const url = form.action;

    if (isEdit) {
      formData.append('_method', 'PUT');
    }

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      });

      const contentType = response.headers.get('content-type');
      console.log('Response status:', response.status, 'Content-Type:', contentType);

      if (response.ok) {
        let result;
        try {
          // Check if response is JSON
          if (contentType && contentType.includes('application/json')) {
            result = await response.json();
            console.log('Parsed JSON result:', result);
          } else {
            // Response is not JSON, might be HTML redirect
            console.log('Response is not JSON, assuming success');
            alert(isEdit ? 'Client updated successfully!' : 'Client created successfully!');
            location.reload();
            return;
          }
        } catch (parseError) {
          console.error('Error parsing response:', parseError);
          // If response is not JSON, it might be a redirect or HTML
          alert(isEdit ? 'Client updated successfully!' : 'Client created successfully!');
          location.reload();
          return;
        }

        if (result && result.success) {
          console.log('Success response received:', result);
          if (!isEdit && result.client?.id) {
            currentClientId = result.client.id;

            // Upload pending photo first if any
            if (pendingPhoto) {
              try {
                const photoFormData = new FormData();
                photoFormData.append('photo', pendingPhoto.file);

                await fetch(`/clients/${currentClientId}/upload-photo`, {
                  method: 'POST',
                  headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                  },
                  body: photoFormData
                });
                pendingPhoto = null;
              } catch (error) {
                console.error('Error uploading pending photo:', error);
              }
            }

            // Upload pending documents if any
            const pendingDocsCount = pendingDocuments.length;
            if (pendingDocsCount > 0) {
              try {
                for (const doc of pendingDocuments) {
                  // Skip photo as it's already uploaded
                  if (doc.isPhoto) continue;

                  const docFormData = new FormData();
                  docFormData.append('document', doc.file);
                  docFormData.append('document_type', doc.type);

                  await fetch(`/clients/${currentClientId}/upload-document`, {
                    method: 'POST',
                    headers: {
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken,
                      'X-Requested-With': 'XMLHttpRequest',
                      'Accept': 'application/json'
                    },
                    body: docFormData
                  });
                }
                // Clear pending documents after upload
                pendingDocuments = [];
              } catch (error) {
                console.error('Error uploading pending documents:', error);
              }
            }

            // Close modal and show clients table
            closeClientModal();
            const successMessage = 'Client created successfully!' + (pendingDocsCount > 0 ? ` ${pendingDocsCount} document(s) uploaded.` : '');
            showNotification(successMessage, 'success');
            // Reload after a short delay to show notification
            setTimeout(() => {
              location.reload();
            }, 1500);
          } else {
            // Update success
            console.log('Showing update success message');
            closeClientModal();
            showNotification('Client updated successfully!', 'success');
            setTimeout(() => {
              location.reload();
            }, 1500);
          }
        } else {
          console.log('Response success is false:', result);
          showNotification('Error: ' + (result.message || 'Unknown error'), 'error');
        }
      } else {
        const errorData = await response.json();
        if (errorData.errors) {
          let errorMsg = 'Validation errors: ';
          Object.keys(errorData.errors).forEach(key => {
            errorMsg += errorData.errors[key][0] + ' ';
          });
          showNotification(errorMsg.trim(), 'error');
        } else {
          showNotification('Error saving client: ' + (errorData.message || 'Unknown error'), 'error');
        }
      }
    } catch (error) {
      console.error('Error:', error);
      showNotification('Error saving client: ' + error.message, 'error');
    }
  });
}

// Initialize form handlers
function initializeFormHandlers() {
  // Attach to modal form
  const modalForm = document.getElementById('clientModal')?.querySelector('form');
  if (modalForm && !modalForm.hasAttribute('data-handler-attached')) {
    attachFormSubmitHandler(modalForm);
    modalForm.setAttribute('data-handler-attached', 'true');
  }

  // Attach to page view form
  const pageForm = document.querySelector('#clientFormPageContent form');
  if (pageForm && !pageForm.hasAttribute('data-handler-attached')) {
    attachFormSubmitHandler(pageForm);
    pageForm.setAttribute('data-handler-attached', 'true');
  }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeFormHandlers);
} else {
  initializeFormHandlers();
}

// Re-initialize when page view is shown
const observer = new MutationObserver(function (mutations) {
  const pageForm = document.querySelector('#clientFormPageContent form');
  if (pageForm && !pageForm.hasAttribute('data-handler-attached')) {
    initializeFormHandlers();
  }
});

const pageViewContainer = document.getElementById('clientFormPageContent');
if (pageViewContainer) {
  observer.observe(pageViewContainer, { childList: true, subtree: true });
}

// ============================================================================
// TABLE FUNCTIONS
// ============================================================================

function toggleTableScroll() {
  const table = document.getElementById('clientsTable');
  const wrapper = document.getElementById('tableResponsive');
  if (!table || !wrapper) return;
  const hasHorizontalOverflow = table.offsetWidth > wrapper.offsetWidth;
  const hasVerticalOverflow = table.offsetHeight > wrapper.offsetHeight;
  wrapper.classList.toggle('no-scroll', !hasHorizontalOverflow && !hasVerticalOverflow);
}

window.addEventListener('load', toggleTableScroll);
window.addEventListener('resize', toggleTableScroll);

function applyFilters() {
  const rows = document.querySelectorAll('tbody tr');
  const activeFilters = {};

  document.querySelectorAll('.column-filter.visible').forEach(filter => {
    const column = filter.dataset.column;
    const value = filter.value.trim().toLowerCase();
    if (value) {
      activeFilters[column] = value;
    }
  });

  rows.forEach(row => {
    let shouldShow = true;

    for (const [column, filterValue] of Object.entries(activeFilters)) {
      const cell = row.querySelector(`td[data-column="${column}"]`);
      if (cell) {
        const cellText = cell.textContent.toLowerCase();
        if (!cellText.includes(filterValue)) {
          shouldShow = false;
          break;
        }
      } else {
        shouldShow = false;
        break;
      }
    }

    row.style.display = shouldShow ? '' : 'none';
  });

  const visibleRows = Array.from(document.querySelectorAll('tbody tr')).filter(row => {
    return row.style.display !== 'none' && !row.style.display.includes('none');
  }).length;
  const recordsFound = document.querySelector('.records-found');
  if (recordsFound && Object.keys(activeFilters).length > 0) {
    recordsFound.textContent = `Records Found - ${visibleRows} of ${clientsTotal} (filtered)`;
  } else if (recordsFound) {
    recordsFound.textContent = `Records Found - ${clientsTotal}`;
  }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function printTable() {
  const table = document.getElementById('clientsTable');
  if (!table) return;

  const headers = [];
  const headerCells = table.querySelectorAll('thead th');
  headerCells.forEach(th => {
    let headerText = '';
    const clone = th.cloneNode(true);
    const filterInput = clone.querySelector('.column-filter');
    if (filterInput) filterInput.remove();
    headerText = clone.textContent.trim();
    if (clone.querySelector('svg')) {
      headerText = '🔔';
    }
    if (headerText) {
      headers.push(headerText);
    }
  });

  const rows = [];
  const tableRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
  tableRows.forEach(row => {
    if (row.style.display === 'none') return;

    const cells = [];
    const rowCells = row.querySelectorAll('td');
    rowCells.forEach((cell) => {
      let cellContent = '';

      if (cell.classList.contains('bell-cell')) {
        const radio = cell.querySelector('input[type="radio"]');
        cellContent = radio && radio.checked ? '●' : '○';
      } else if (cell.classList.contains('action-cell')) {
        const icons = [];
        if (cell.querySelector('.action-expand')) icons.push('⤢');
        if (cell.querySelector('.action-clock')) icons.push('🕐');
        if (cell.querySelector('.action-ellipsis')) icons.push('⋯');
        cellContent = icons.join(' ');
      } else if (cell.classList.contains('checkbox-cell')) {
        const checkbox = cell.querySelector('input[type="checkbox"]');
        cellContent = checkbox && checkbox.checked ? '✓' : '';
      } else {
        const link = cell.querySelector('a');
        cellContent = link ? link.textContent.trim() : cell.textContent.trim();
      }

      cells.push(cellContent || '-');
    });
    rows.push(cells);
  });

  const headersHTML = headers.map(h => '<th>' + escapeHtml(h) + '</th>').join('');
  const rowsHTML = rows.map(row => {
    const cellsHTML = row.map(cell => {
      const cellText = escapeHtml(String(cell || '-'));
      return '<td>' + cellText + '</td>';
    }).join('');
    return '<tr>' + cellsHTML + '</tr>';
  }).join('');

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

function initializeFilters() {
  const printBtn = document.getElementById('printBtn');
  if (printBtn) {
    printBtn.addEventListener('click', printTable);
  }

  document.querySelectorAll('.column-filter').forEach(filter => {
    filter.addEventListener('input', applyFilters);
  });

  const filterToggle = document.getElementById('filterToggle');
  if (filterToggle?.checked) {
    document.querySelectorAll('.column-filter').forEach(filter => {
      filter.classList.add('visible');
      filter.style.display = 'block';
    });
  }
}

document.addEventListener('DOMContentLoaded', initializeFilters);
