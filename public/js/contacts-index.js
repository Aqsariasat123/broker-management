// Data initialized in Blade template

// Format date helper function

// Format number helper function
function formatNumber(num) {
  if (!num && num !== 0) return '-';
  return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}


// Event listeners
document.addEventListener('DOMContentLoaded', function () {
  // Add button handler
  const addBtn = document.getElementById('addContactBtn');
  if (addBtn) {
    addBtn.addEventListener('click', function () {
      openContactModal('add');
    });
  }

  // Column button
  const columnBtn = document.getElementById('columnBtn2');
  if (columnBtn) {
    columnBtn.addEventListener('click', function () {
      openColumnModal();
    });
  }

  // Filter toggle handler
  const filterToggle = document.getElementById('filterToggle');
  if (filterToggle) {
    const urlParams = new URLSearchParams(window.location.search);
    filterToggle.checked = urlParams.get('follow_up') === 'true' || urlParams.get('follow_up') === '1';

    filterToggle.addEventListener('change', function (e) {
      const u = new URL(window.location.href);
      if (this.checked) {
        u.searchParams.set('follow_up', '1');
      } else {
        u.searchParams.delete('follow_up');
      }
      window.location.href = u.toString();
    });
  }

  // To Follow Up button handler
  const followUpBtn = document.getElementById('followUpBtn');
  if (followUpBtn) {
    followUpBtn.addEventListener('click', function (e) {
      e.preventDefault();
      const u = new URL(window.location.href);
      u.searchParams.set('follow_up', '1');
      window.location.href = u.toString();
    });
  }

  // List ALL button handler
  const listAllBtn = document.getElementById('listAllBtn');
  if (listAllBtn) {
    listAllBtn.addEventListener('click', function (e) {
      e.preventDefault();
      const u = new URL(window.location.href);
      u.searchParams.delete('follow_up');
      window.location.href = u.toString();
    });
  }

  // Setup form listeners on page load
  setupContactFormListeners(document.getElementById('contactModal'));
});


// Open full page view for add
function openContactModal(mode) {
  if (mode === 'add') {
    // Open full page view for adding new contact
    currentContactId = null;
    const clientsTableView = document.getElementById('clientsTableView');
    const contactPageView = document.getElementById('contactPageView');
    const contactPageName = document.getElementById('contactPageName');
    const contactPageTitleEl = document.getElementById('contactPageTitle');
    const contactDetailsPageContent = document.getElementById('contactDetailsPageContent');
    const contactFormPageContent = document.getElementById('contactFormPageContent');

    if (contactPageTitleEl) contactPageTitleEl.textContent = 'Contact - Add New';
    if (contactPageName) contactPageName.textContent = '';

    // Hide table view, show page view
    clientsTableView.classList.add('hidden');
    contactPageView.style.display = 'block';
    contactPageView.classList.add('show');

    // Show add form
    populateContactDetails({}, 'add');
    contactDetailsPageContent.style.display = 'block';
    document.getElementById('contactDetailsContentWrapper').style.display = 'block';
    document.getElementById('followupsContentWrapper').style.display = 'block';
    contactFormPageContent.style.display = 'none';
  }
}

// Open modal with contact data for editing
function openModalWithContact(mode, contact) {
  const modal = document.getElementById('contactModal');
  if (!modal) return;

  const title = document.getElementById('contactModalTitle');
  const form = document.getElementById('contactForm');
  const deleteBtn = document.getElementById('contactDeleteBtn');
  const formMethod = document.getElementById('contactFormMethod');

  if (mode === 'edit' && contact) {
    if (title) title.textContent = 'Edit Contact';
    if (form) {
      form.action = `/contacts/${currentContactId}`;
    }
    if (formMethod) {
      formMethod.innerHTML = '';
      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'PUT';
      formMethod.appendChild(methodInput);
    }
    if (deleteBtn) deleteBtn.style.display = 'block';

    const fields = ['type', 'contact_name', 'mobile_no', 'contact_no', 'wa', 'occupation', 'employer', 'email_address', 'address', 'location', 'dob', 'acquired', 'source', 'source_name', 'agency', 'agent', 'status', 'rank', 'savings_budget', 'children'];
    fields.forEach(id => {
      const el = form.querySelector(`#${id}`);
      if (!el) return;
      if (el.type === 'checkbox') {
        el.checked = !!contact[id];
      } else if (el.type === 'date') {
        if (contact[id]) {
          let dateValue = contact[id];
          if (typeof dateValue === 'string' && dateValue.match(/^\d{4}-\d{2}-\d{2}/)) {
            el.value = dateValue.substring(0, 10);
          } else if (typeof dateValue === 'string') {
            try {
              const date = new Date(dateValue);
              if (!isNaN(date.getTime())) el.value = date.toISOString().substring(0, 10);
            } catch (e) { }
          }
        }
      } else if (el.tagName === 'SELECT') {
        el.value = contact[id] ?? '';
      } else {
        el.value = contact[id] ?? '';
      }
    });

    const dobField = form.querySelector('#dob');
    const ageDisplay = document.getElementById('age_display');
    if (dobField && ageDisplay && contact.dob) {
      try {
        const dob = new Date(contact.dob);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) age--;
        ageDisplay.value = age;
      } catch (e) { }
    }
  }

  document.body.style.overflow = 'hidden';
  modal.classList.add('show');
  setTimeout(() => setupContactFormListeners(modal), 100);
}

// Setup form event listeners
function setupContactFormListeners(container) {
  if (!container) return;
  const dobField = container.querySelector('#dob');
  const ageDisplay = document.getElementById('age_display');
  if (dobField && ageDisplay) {
    dobField.addEventListener('change', function () {
      if (this.value) {
        try {
          const dob = new Date(this.value);
          const today = new Date();
          let age = today.getFullYear() - dob.getFullYear();
          const monthDiff = today.getMonth() - dob.getMonth();
          if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) age--;
          ageDisplay.value = age;
        } catch (e) {
          ageDisplay.value = '';
        }
      } else {
        ageDisplay.value = '';
      }
    });
  }
}

function closeContactModal() {
  document.getElementById('contactModal').classList.remove('show');
  currentContactId = null;
  document.body.style.overflow = '';
}

// show edit: fetch /contacts/{id}/edit which returns JSON in controller
async function openContactDetails(id) {
  try {
    const res = await fetch(`/contacts/${id}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!res.ok) throw new Error('Network error');
    const contact = await res.json();
    currentContactId = id;
    const contactPageName = document.getElementById('contactPageName');
    const clientsTableView = document.getElementById('clientsTableView');
    const contactPageView = document.getElementById('contactPageView');
    const contactDetailsPageContent = document.getElementById('contactDetailsPageContent');
    const contactFormPageContent = document.getElementById('contactFormPageContent');
    const contactContactPageBtn = document.getElementById('contactContactPageBtn');

    if (!contactPageName || !clientsTableView || !contactPageView ||
      !contactDetailsPageContent || !contactFormPageContent) {
      console.error('Required elements not found');
      console.error('contactPageName:', contactPageName);
      console.error('clientsTableView:', clientsTableView);
      console.error('contactPageView:', contactPageView);
      console.error('contactDetailsPageContent:', contactDetailsPageContent);
      console.error('contactFormPageContent:', contactFormPageContent);
      alert('Error: Page elements not found');
      return;
    }

    // Set contact name in header
    const contactPageTitleEl = document.getElementById('contactPageTitle');
    const contactName = contact.contact_name || 'Unknown';
    if (contactPageTitleEl) contactPageTitleEl.textContent = 'Contact';
    if (contactPageName) contactPageName.textContent = contactName;
    populateContactDetails(contact, 'view');
    // Update documents display
    // if (policy.documents) {
    //   updatePolicyDocumentsList(policy);
    // }

    // Hide table view, show page view
    clientsTableView.classList.add('hidden');
    contactPageView.style.display = 'block';
    contactPageView.classList.add('show');
    contactDetailsPageContent.style.display = 'block';
    document.getElementById('contactDetailsContentWrapper').style.display = 'block';
    document.getElementById('followupsContentWrapper').style.display = 'block';
    contactFormPageContent.style.display = 'none';

    document.querySelectorAll('#contactPageView .contact-tab').forEach(tab => {
      // Remove existing listeners by cloning
      const newTab = tab.cloneNode(true);
      tab.parentNode.replaceChild(newTab, tab);
      // Add click listener

      newTab.addEventListener('click', function (e) {

        console.log(currentContactId);
        e.preventDefault();
        if (!currentContactId) return;
        const baseUrl = this.getAttribute('data-url');
        if (!baseUrl || baseUrl === '#') return;
        const tabType = this.getAttribute('data-tab');

        let actionType = 'view'; // default

        if (tabType === 'life-proposals-view') {
          actionType = 'view';

          window.location.href =
            `${baseUrl}?contact_id=${currentContactId}&action=${actionType}`;
        } else if (tabType === 'life-proposals-add') {
          actionType = 'add';

          window.location.href =
            `${baseUrl}?contact_id=${currentContactId}&action=${actionType}`;
        } else if (tabType === 'life-proposals-follow-up') {
          actionType = '1';

          window.location.href =
            `${baseUrl}?contact_id=${currentContactId}&follow_up=${actionType}`;
        }

      });
    });
    // openModalWithContact('edit', contact);
  } catch (e) {
    console.error(e);
    alert('Error loading contact data');
  }
}
async function openEditContract(id) {
  try {
    const res = await fetch(`/contacts/${id}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!res.ok) throw new Error('Network error');
    const contact = await res.json();

    currentContactId = id;
    const contactPageName = document.getElementById('contactPageName');
    const clientsTableView = document.getElementById('clientsTableView');
    const contactPageView = document.getElementById('contactPageView');
    const contactDetailsPageContent = document.getElementById('contactDetailsPageContent');
    const contactFormPageContent = document.getElementById('contactFormPageContent');
    const contactContactPageBtn = document.getElementById('contactContactPageBtn');

    if (!contactPageName || !clientsTableView || !contactPageView ||
      !contactDetailsPageContent || !contactFormPageContent) {
      console.error('Required elements not found');
      console.error('contactPageName:', contactPageName);
      console.error('clientsTableView:', clientsTableView);
      console.error('contactPageView:', contactPageView);
      console.error('contactDetailsPageContent:', contactDetailsPageContent);
      console.error('contactFormPageContent:', contactFormPageContent);
      alert('Error: Page elements not found');
      return;
    }

    // Set contact name in header
    const contactPageTitleEl = document.getElementById('contactPageTitle');
    const contactName = contact.contact_name || 'Unknown';
    if (contactPageTitleEl) contactPageTitleEl.textContent = 'Contact';
    if (contactPageName) contactPageName.textContent = contactName;
    populateContactDetails(contact, 'edit');
    // Update documents display
    // if (policy.documents) {
    //   updatePolicyDocumentsList(policy);
    // }

    // Hide table view, show page view
    clientsTableView.classList.add('hidden');
    contactPageView.style.display = 'block';
    contactPageView.classList.add('show');
    contactDetailsPageContent.style.display = 'block';
    document.getElementById('contactDetailsContentWrapper').style.display = 'block';
    document.getElementById('followupsContentWrapper').style.display = 'block';
    contactFormPageContent.style.display = 'none';
  } catch (e) {
    console.error(e);
    alert('Error loading contact data');
  }
}


function formatNumber(num) {
  if (!num && num !== 0) return '';
  const numVal = parseFloat(num);
  // If it's a whole number, don't show decimals
  if (numVal % 1 === 0) {
    return numVal.toLocaleString('en-US');
  }
  return numVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
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
function formatDateForInput(dateString) {
  if (!dateString) return '';
  const d = new Date(dateString);
  if (isNaN(d)) return ''; // invalid date check
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function populateContactDetails(contact, type = 'view') {
  const content = document.getElementById('contactDetailsContent');
  const scheduleContent = document.getElementById('contactScheduleContent');
  const followupcontent = document.getElementById('followupcontent');

  if (!content || !scheduleContent || !followupcontent) return;


  // Get contact data
  const isEdit = type === 'edit' || type === 'add';
  const isAdd = type === 'add';
  const ro = isEdit ? '' : 'readonly';
  const dis = isEdit ? '' : 'disabled';


  const selectedTypeId = contact?.type ?? null;

  const contactTypeOptions = lookupData.contact_types
    .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedTypeId ? 'selected' : ''}>
          ${ct.name}
        </option>
      `)
    .join('');

  // Get selected type name for display
  const selectedTypeName = selectedTypeId ?
    (lookupData.contact_types.find(ct => ct.id == selectedTypeId)?.name || '[Select Type]') :
    '[Select Type]';


  const selectedoccupation = contact?.occupation ?? null;
  const occupationOptions = allOccupations
    .map(ct => `
        <option value="${ct}" ${ct == selectedoccupation ? 'selected' : ''}>
          ${ct}
        </option>
      `)
    .join('');

  const tabs = document.querySelectorAll('.contact-bottom-tab');

  tabs.forEach(tab => {
    if (tab.dataset.tab == contact?.status) {
      tab.classList.add('active');
    } else {
      tab.classList.remove('active');
    }
  });

  const selectedstatusId = contact?.status ?? null;

  const contactStatusOptions = lookupData.contact_statuses
    .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedstatusId ? 'selected' : ''}>
          ${ct.name}
        </option>
      `)
    .join('');

  const selectedSalutationId = contact?.salutation ? parseInt(contact.salutation) : null;

  const salvationOptions = lookupData.salutations
    .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedSalutationId ? 'selected' : ''}>
          ${ct.name}
        </option>
      `)
    .join('');

  const selectedranksId = contact?.rank ?? null;

  const contactranksOptions = lookupData.ranks
    .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedranksId ? 'selected' : ''}>
          ${ct.name}
        </option>
      `)
    .join('');


  const selectedEmployerOption = contact?.employer ?? null;
  const EmployerOptions = allEmployers
    .map(ct => `
        <option value="${ct}" ${ct == selectedEmployerOption ? 'selected' : ''}>
          ${ct}
        </option>
      `)
    .join('');


  const selectedSourceId = contact?.source ?? null;

  const sourceOptions = lookupData.sources
    .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedSourceId ? 'selected' : ''}>
          ${ct.name}
        </option>
      `)
    .join('');


  const selectedAgencyId = contact?.agency ?? null;

  const agencyOptions = lookupData.agencies
    .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedAgencyId ? 'selected' : ''}>
          ${ct.name}
        </option>
      `)
    .join('');


  const dob = contact.dob ? formatDateForInput(contact.dob) : '';
  const date_acquired = contact.acquired ? formatDateForInput(contact.acquired) : '';
  const first_contact = contact.first_contact ? formatDateForInput(contact.first_contact) : '';
  const next_follow_up = contact.next_follow_up ? formatDateForInput(contact.next_follow_up) : '';

  const dobAge = contact.dob ? calculateAge(contact.dob) : '';

  // Contact Type Header Row - light gray background like Excel
  const contactTypeHeader = `
  <div style="background:#f5f5f5; padding:12px 15px; margin-bottom:0; border-bottom:1px solid #ddd;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div style="font-size:14px; font-weight:600;">
        <span style="color:#000;">Contact Type - </span>
        <select id="type" name="type" class="form-control" required style="display:inline-block; width:auto; padding:4px 8px; border:none; border-radius:2px; font-size:13px; color:#f3742a; background:transparent;" ${dis}>
          <option value="" style="color:#f3742a;">[Select Type]</option>
          ${contactTypeOptions}
        </select>
      </div>
      <div style="display:flex; gap:8px;">
        <button type="button" class="btn" id="saveContactBtn2" onclick="saveContactFromPage()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:3px; cursor:pointer; font-size:12px;">Save</button>
        <button type="button" class="btn" onclick="closeContactPageView()" style="background:#e0e0e0; color:#333; border:1px solid #ccc; padding:6px 20px; border-radius:3px; cursor:pointer; font-size:12px;">Cancel</button>
      </div>
    </div>
  </div>
  `;

  // CONTACT DETAILS - Full width with left aligned header like Excel
  const contactDetailsSection = `
  <div style="margin-bottom:15px; border:1px solid #ddd;">
    <div style="background:#2d3e50; color:#fff; padding:8px 15px; font-size:12px; font-weight:500; text-align:left;">CONTACT DETAILS</div>
    <div style="padding:15px; background:#fff;">
      <table style="width:100%; border-collapse:collapse; border:none !important; border-spacing:0;">
        <tr style="border:none !important; background:#fff !important;">
          <td style="padding:5px; width:90px; border:none !important; background:#fff !important;"><label style="font-size:11px;">Contact Name</label></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><input type="text" id="contact_name" name="contact_name" class="form-control" value="${contact.contact_name || ''}" ${ro} style="width:100%; padding:5px 8px; border:1px solid #ccc; font-size:11px; background:#f9f9f9;"></td>
          <td style="padding:5px; width:80px; border:none !important; background:#fff !important;"><label style="font-size:11px;">Contact No</label></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><input type="text" name="contact_no" class="form-control" value="${contact.contact_no || ''}" ${ro} style="width:100%; padding:5px 8px; border:1px solid #ccc; font-size:11px; background:#f9f9f9;"></td>
          <td style="padding:5px; width:90px; border:none !important; background:#fff !important;"><label style="font-size:11px;">Email Address</label></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><input type="text" name="email_address" class="form-control" value="${contact.email_address || ''}" ${ro} style="width:100%; padding:5px 8px; border:1px solid #ccc; font-size:11px; background:#f9f9f9;"></td>
          <td style="padding:5px; width:85px; border:none !important; background:#fff !important;"><label style="font-size:11px;">Date Of Birth</label></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><input id="dob" name="dob" type="date" value="${dob}" class="form-control" ${ro} style="width:100%; padding:5px 8px; border:1px solid #ccc; font-size:11px; background:#f9f9f9;"></td>
        </tr>
        <tr style="border:none !important; background:#fff !important;">
          <td style="padding:5px; border:none !important; background:#fff !important;"><label style="font-size:11px;">Occupation</label></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><input type="text" id="occupation_text" name="occupation" class="form-control" value="${contact.occupation || ''}" ${ro} style="width:100%; padding:5px 8px; border:1px solid #ccc; font-size:11px; background:#f9f9f9;"></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><label style="font-size:11px;">Employer</label></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><input type="text" id="employer_text" name="employer" class="form-control" value="${contact.employer || ''}" ${ro} style="width:100%; padding:5px 8px; border:1px solid #ccc; font-size:11px; background:#f9f9f9;"></td>
          <td style="padding:5px; border:none !important; background:#fff !important;"><label style="font-size:11px;">Address</label></td>
          <td colspan="3" style="padding:5px; border:none !important; background:#fff !important;"><input type="text" name="address" class="form-control" value="${contact.address || ''}" ${ro} style="width:100%; padding:5px 8px; border:1px solid #ccc; font-size:11px; background:#f9f9f9;"></td>
        </tr>
      </table>
    </div>
  </div>
  `;

  // Lead / Prospect Details Label with separator line
  const leadProspectLabel = `
  <div style="font-size:14px; font-weight:600; margin-bottom:10px; padding-bottom:8px; border-bottom:2px solid #ddd; color:#333;">Lead / Prospect Details</div>
  `;

  // 4 Subsections in a row - with labels on left like Excel - ALL dark navy blue headers
  const col1 = `
  <div style="border:1px solid #ddd; overflow:hidden;">
    <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">SOURCE DETAILS</div>
    <div style="padding:10px; background:#fff;">
      <div style="display:grid; grid-template-columns: 80px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">Date Aquired</label>
        <input id="date_acquired" name="date_acquired" type="date" value="${date_acquired}" class="form-control" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 80px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">Source</label>
        <select id="source" name="source" class="form-control" ${dis} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
          <option value="">Select</option>
          ${sourceOptions}
        </select>
      </div>
      <div style="display:grid; grid-template-columns: 80px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">Source Name</label>
        <input type="text" name="source_name" class="form-control" value="${contact.source_name || ''}" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 80px 1fr; gap:5px; align-items:center;">
        <label style="font-size:10px;">Agency</label>
        <select id="agency" name="agency" class="form-control" ${dis} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
          <option value="">Select</option>
          ${agencyOptions}
        </select>
      </div>
    </div>
  </div>
  `;

  const col2 = `
  <div style="border:1px solid #ddd; overflow:hidden;">
    <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">FAMILY DETAILS</div>
    <div style="padding:10px; background:#fff;">
      <div style="display:grid; grid-template-columns: 90px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">Spouse's Name</label>
        <input type="text" name="spouses_name" class="form-control" value="${contact.spouses_name || ''}" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 90px 1fr; gap:5px; align-items:start; margin-bottom:8px;">
        <label style="font-size:10px;">Children Details</label>
        <input type="text" name="children_details" class="form-control" value="${contact.children_details || ''}" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 90px 1fr; gap:5px; align-items:center;">
        <label style="font-size:10px;">Savings Budget</label>
        <input id="savings_budget" name="savings_budget" type="text" class="form-control" value="${contact.savings_budget || ''}" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
    </div>
  </div>
  `;

  const col3 = `
  <div style="border:1px solid #ddd; overflow:hidden;">
    <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">INSURABLE ASSETS</div>
    <div style="padding:10px; background:#fff;">
      <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:8px;">
        <label style="font-size:10px; display:flex; align-items:center; gap:4px;">
          <span>Vehicle</span><input type="checkbox" name="vehicle" value="1" ${contact.vehicle == '1' ? 'checked' : ''} ${dis} style="width:14px; height:14px;">
        </label>
        <label style="font-size:10px; display:flex; align-items:center; gap:4px;">
          <span>House</span><input type="checkbox" name="house" value="1" ${contact.house == '1' ? 'checked' : ''} ${dis} style="width:14px; height:14px;">
        </label>
        <label style="font-size:10px; display:flex; align-items:center; gap:4px;">
          <span>Business</span><input type="checkbox" name="business" value="1" ${contact.business == '1' ? 'checked' : ''} ${dis} style="width:14px; height:14px;">
        </label>
      </div>
      <div style="display:grid; grid-template-columns: 50px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">Other</label>
        <input type="text" name="other_assets" class="form-control" value="${contact.other || ''}" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 50px 1fr; gap:5px; align-items:start;">
        <label style="font-size:10px;">Notes</label>
        <textarea name="notes" class="form-control" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; height:40px; resize:none; background:#f9f9f9;">${contact.notes || ''}</textarea>
      </div>
    </div>
  </div>
  `;

  const col4 = `
  <div style="border:1px solid #ddd; overflow:hidden;">
    <div style="background:#2d3e50; color:#fff; padding:8px 12px; font-size:11px; font-weight:600; text-align:center;">LEAD MANAGEMENT</div>
    <div style="padding:10px; background:#fff;">
      <div style="display:grid; grid-template-columns: 90px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">Contact Stage</label>
        <input type="text" id="status_text" name="status_text" class="form-control" value="${contact.status || ''}" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 90px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">1st Contacted</label>
        <input id="first_contact" name="first_contact" type="date" value="${first_contact}" class="form-control" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 90px 1fr; gap:5px; align-items:center; margin-bottom:8px;">
        <label style="font-size:10px;">Rank</label>
        <input type="text" id="rank_text" name="rank_text" class="form-control" value="${contact.rank || ''}" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
      <div style="display:grid; grid-template-columns: 90px 1fr; gap:5px; align-items:center;">
        <label style="font-size:10px;">Next Follow Up</label>
        <input id="next_follow_up" name="next_follow_up" type="date" value="${next_follow_up}" class="form-control" ${ro} style="width:100%; padding:4px 6px; border:1px solid #ccc; font-size:10px; background:#f9f9f9;">
      </div>
    </div>
  </div>
  `;

  // 4 columns grid for subsections
  const subsectionsGrid = `
  <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px; margin-bottom:15px;">
    ${col1}${col2}${col3}${col4}
  </div>
  `;

  // Status Buttons Row - all light gray like Excel
  const statusButtons = `
  <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:15px;">
    <button type="button" class="status-btn" data-status="1" onclick="setContactStatus(1)" style="padding:8px 25px; border:1px solid #ccc; border-radius:3px; background:#fff; color:#333; font-size:11px; cursor:pointer;">Open</button>
    <button type="button" class="status-btn" data-status="2" onclick="setContactStatus(2)" style="padding:8px 25px; border:1px solid #ccc; border-radius:3px; background:#e0e0e0; color:#333; font-size:11px; cursor:pointer;">Not Reached</button>
    <button type="button" class="status-btn" data-status="3" onclick="setContactStatus(3)" style="padding:8px 25px; border:1px solid #ccc; border-radius:3px; background:#e0e0e0; color:#333; font-size:11px; cursor:pointer;">In Discussion</button>
    <button type="button" class="status-btn" data-status="4" onclick="setContactStatus(4)" style="padding:8px 25px; border:1px solid #ccc; border-radius:3px; background:#e0e0e0; color:#333; font-size:11px; cursor:pointer;">Offer Made</button>
    <button type="button" class="status-btn" data-status="5" onclick="setContactStatus(5)" style="padding:8px 25px; border:1px solid #ccc; border-radius:3px; background:#e0e0e0; color:#333; font-size:11px; cursor:pointer;">Converted</button>
    <button type="button" class="status-btn" data-status="6" onclick="setContactStatus(6)" style="padding:8px 25px; border:1px solid #ccc; border-radius:3px; background:#e0e0e0; color:#333; font-size:11px; cursor:pointer;">Keep In View</button>
    <button type="button" class="status-btn" data-status="7" onclick="setContactStatus(7)" style="padding:8px 25px; border:1px solid #ccc; border-radius:3px; background:#e0e0e0; color:#333; font-size:11px; cursor:pointer;">Archived</button>
  </div>
  `;

  content.innerHTML = contactTypeHeader + contactDetailsSection + leadProspectLabel + subsectionsGrid + statusButtons;
  content.style.display = 'block';
  content.style.gridTemplateColumns = '1fr';

  const editBtn = document.getElementById('editContactFromPageBtn');
  const cancelBtn = document.getElementById('contactContactPageBtn');

  const saveBtn = document.getElementById('saveContactFromPageBtn');
  const deleteBtn = document.getElementById('deleteContactFromPageBtn');
  const closebtn = document.getElementById('closeContactFromPageBtn');



  if (isAdd) {
    // Add mode - show Save and Close, hide Edit and Delete
    editBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
    closebtn.style.display = 'inline-block';
    deleteBtn.style.display = 'none';
  } else if (isEdit) {
    // Edit mode - show Save, Close, Delete
    editBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
    closebtn.style.display = 'inline-block';
    deleteBtn.style.display = 'inline-block';
  } else {
    // View mode - show Edit and Cancel
    editBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
    saveBtn.style.display = 'none';
    deleteBtn.style.display = 'none';
  }


  const followups = contact.followups || [];

  // Always create table with header - dark navy blue matching Excel
  const table = document.createElement('table');
  table.className = 'followup-table';
  table.style.width = '100%';
  table.style.borderCollapse = 'collapse';

  const thead = document.createElement('thead');
  thead.innerHTML = `
    <tr style="background:#2d3e50; color:#fff;">
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">FUID</th>
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">Follow Up Date</th>
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">Time</th>
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">Action</th>
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">Next Step</th>
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">Time</th>
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">Status</th>
      <th style="border:1px solid #1d2e40; padding:8px 10px; font-size:11px; font-weight:500;">Status</th>
    </tr>
  `;
  table.appendChild(thead);

  // Table body
  const tbody = document.createElement('tbody');
  if (followups.length === 0) {
    // Empty row when no follow-ups
    const tr = document.createElement('tr');
    tr.innerHTML = `<td colspan="8" style="border:1px solid #ddd; padding:15px; text-align:center; color:#666; font-size:12px;"></td>`;
    tbody.appendChild(tr);
  } else {
    followups.forEach(fu => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.follow_up_code || ''}</td>
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.follow_up_date ? fu.follow_up_date.substring(0, 10) : ''}</td>
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.time || ''}</td>
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.channel || fu.action || ''}</td>
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.next_action || ''}</td>
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.next_time || ''}</td>
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.status || ''}</td>
        <td style="border:1px solid #ddd; padding:6px 8px; font-size:12px;">${fu.done_by || fu.assignee || ''}</td>
      `;
      tbody.appendChild(tr);
    });
  }
  table.appendChild(tbody);

  // Render inside followupcontent
  followupcontent.innerHTML = '';
  followupcontent.appendChild(table);

}

// Save contact details
async function saveContactFromPage() {
  try {
    // Gather values from the form fields
    const contactData = {
      contact_name: document.getElementById('contact_name')?.value || '',
      type: document.getElementById('type')?.value || '',
      mobile_no: document.querySelector('#contactDetailsContent input[name="mobile_no"]')?.value || '',
      contact_no: document.querySelector('#contactDetailsContent input[name="contact_no"]')?.value || '',
      email_address: document.querySelector('#contactDetailsContent input[name="email_address"]')?.value || '',
      address: document.querySelector('#contactDetailsContent input[name="address"]')?.value || '',
      occupation: document.getElementById('occupation')?.value || '',
      employer: document.getElementById('employer')?.value || '',
      source: document.getElementById('source')?.value || '',
      source_name: document.querySelector('#contactDetailsContent input[name="source_name"]')?.value || '',
      dob: document.getElementById('dob')?.value || '',
      savings_budget: document.getElementById('savings_budget')?.value || '',
      children: document.getElementById('children')?.value || '',
      vehicle: document.querySelector('input[name="vehicle"]')?.checked ? "1" : "0",
      house: document.querySelector('input[name="house"]')?.checked ? "1" : "0",
      business: document.querySelector('input[name="business"]')?.checked ? "1" : "0",
      other: document.querySelector('#contactDetailsContent input[name="other"]')?.value || '',
      status: document.getElementById('status')?.value || '',
      rank: document.getElementById('rank')?.value || '',
      first_contact: document.getElementById('first_contact')?.value || '',
      next_follow_up: document.getElementById('next_follow_up')?.value || '',
      acquired: document.getElementById('date_acquired')?.value || ''
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Determine if this is add or edit
    const isAdd = !currentContactId;
    const url = isAdd ? '/contacts' : `/contacts/${currentContactId}`;
    const method = isAdd ? 'POST' : 'PUT';

    const res = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify(contactData)
    });

    if (!res.ok) throw new Error('Failed to save contact');

    const data = await res.json();
    alert(isAdd ? 'Contact created successfully!' : 'Contact saved successfully!');
    location.reload();
  } catch (e) {
    console.error(e);
    alert('Error saving contact. Please try again.');
  }
}
function closeContactPageView() {
  const contactPageView = document.getElementById('contactPageView');
  const clientsTableView = document.getElementById('clientsTableView');
  if (contactPageView && clientsTableView) {
    contactPageView.classList.remove('show');
    contactPageView.style.display = 'none';
    clientsTableView.classList.remove('hidden');
    currentContactId = null;
  }
}



function deleteContact() {
  if (!currentContactId) return;
  if (!confirm('Delete this contact?')) return;
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/contacts/${currentContactId}`;
  const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken; form.appendChild(csrf);
  const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE'; form.appendChild(method);
  document.body.appendChild(form);
  form.submit();
}

// Follow Up modal functions
function openAddFollowUpModal() {
  if (!currentContactId) {
    alert('Please select a contact first');
    return;
  }
  const modal = document.getElementById('followUpModal');
  const form = document.getElementById('followUpForm');
  if (modal && form) {
    // Set the form action to the correct route
    form.action = `/contacts/${currentContactId}/followup`;
    // Reset form
    form.reset();
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('follow_up_date').value = today;
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

function closeFollowUpModal() {
  const modal = document.getElementById('followUpModal');
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
  }
}

// Column modal functions
function openColumnModal() {
  document.getElementById('tableResponsive').classList.add('no-scroll');
  document.querySelectorAll('.column-checkbox').forEach(cb => {
    // Always check mandatory fields, otherwise check if in selectedColumns
    cb.checked = mandatoryFields.includes(cb.value) || selectedColumns.includes(cb.value);
  });

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
    if (!mandatoryFields.includes(cb.value)) {
      cb.checked = false;
    }
  });
}

function saveColumnSettings() {

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

// Drag and drop functionality
let draggedElement = null;
let dragOverElement = null;

// Initialize drag and drop when column modal opens
function initDragAndDrop() {
  const columnSelection = document.getElementById('columnSelection');
  if (!columnSelection) return;

  // Make all column items draggable
  const columnItems = columnSelection.querySelectorAll('.column-item, .column-item-vertical');

  columnItems.forEach(item => {
    // Skip if already initialized
    if (item.dataset.dragInitialized === 'true') {
      return;
    }
    item.dataset.dragInitialized = 'true';
    // Prevent checkbox from interfering with drag
    const checkbox = item.querySelector('.column-checkbox');
    if (checkbox) {
      checkbox.addEventListener('mousedown', function (e) {
        e.stopPropagation();
      });
      checkbox.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    }

    // Prevent label from interfering with drag
    const label = item.querySelector('label');
    if (label) {
      label.addEventListener('mousedown', function (e) {
        // Only prevent if clicking on the label text, not the checkbox
        if (e.target === label) {
          e.preventDefault();
        }
      });
    }

    // Drag start
    item.addEventListener('dragstart', function (e) {
      draggedElement = this;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/html', this.outerHTML);
      e.dataTransfer.setData('text/plain', this.dataset.column);
    });

    // Drag end
    item.addEventListener('dragend', function (e) {
      this.classList.remove('dragging');
      // Remove drag-over from all items
      columnItems.forEach(i => i.classList.remove('drag-over'));
      if (dragOverElement) {
        dragOverElement.classList.remove('drag-over');
        dragOverElement = null;
      }
      draggedElement = null;
    });

    // Drag over
    item.addEventListener('dragover', function (e) {
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

    // Drag enter
    item.addEventListener('dragenter', function (e) {
      e.preventDefault();
      if (draggedElement && this !== draggedElement) {
        this.classList.add('drag-over');
      }
    });

    // Drag leave
    item.addEventListener('dragleave', function (e) {
      // Only remove if we're actually leaving the element
      if (!this.contains(e.relatedTarget)) {
        this.classList.remove('drag-over');
        if (dragOverElement === this) {
          dragOverElement = null;
        }
      }
    });

    // Drop
    item.addEventListener('drop', function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.remove('drag-over');
      dragOverElement = null;
      return false;
    });
  });
}

// Set contact status from button click
function setContactStatus(statusId) {
  const statusSelect = document.getElementById('status');
  if (statusSelect) {
    statusSelect.value = statusId;
  }
  // Update button styles
  document.querySelectorAll('.status-btn').forEach(btn => {
    if (btn.dataset.status == statusId) {
      btn.style.background = '#2d5a6b';
      btn.style.color = '#fff';
    } else {
      btn.style.background = '#f5f5f5';
      btn.style.color = '#333';
    }
  });
}

// close modals on ESC and clicking backdrop
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeContactModal(); closeColumnModal(); closeFollowUpModal(); } });
  document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) { m.classList.remove('show'); document.body.style.overflow = ''; } });
  });

  // Basic client-side validation for contact form (prevent empty required)
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      const req = this.querySelectorAll('[required]');
      let ok = true;
      req.forEach(f => { if (!String(f.value || '').trim()) { ok = false; f.style.borderColor = 'red'; } else { f.style.borderColor = ''; } });
      if (!ok) { e.preventDefault(); alert('Please fill required fields'); }
    });
  }
});
