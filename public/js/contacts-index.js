  // Data initialized in Blade template

  // Format date helper function

  // Format number helper function
  function formatNumber(num) {
    if (!num && num !== 0) return '-';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }


  // Event listeners
  document.addEventListener('DOMContentLoaded', function(){
    // Add button handler
    const addBtn = document.getElementById('addContactBtn');
    if (addBtn) {
      addBtn.addEventListener('click', function() {
        openContactModal('add');
      });
    }

    // Column button
    const columnBtn = document.getElementById('columnBtn2');
    if (columnBtn) {
      columnBtn.addEventListener('click', function() {
        openColumnModal();
      });
    }

    // Filter toggle handler
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
      const urlParams = new URLSearchParams(window.location.search);
      filterToggle.checked = urlParams.get('follow_up') === 'true' || urlParams.get('follow_up') === '1';
      
      filterToggle.addEventListener('change', function(e) {
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
      followUpBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const u = new URL(window.location.href);
        u.searchParams.set('follow_up', '1');
        window.location.href = u.toString();
      });
    }

    // List ALL button handler
    const listAllBtn = document.getElementById('listAllBtn');
    if (listAllBtn) {
      listAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const u = new URL(window.location.href);
        u.searchParams.delete('follow_up');
        window.location.href = u.toString();
      });
    }

    // Setup form listeners on page load
    setupContactFormListeners(document.getElementById('contactModal'));
  });


  // Open modal for add
  function openContactModal(mode) {
    const modal = document.getElementById('contactModal');
    if (!modal) return;
    
    const title = document.getElementById('contactModalTitle');
    const form = document.getElementById('contactForm');
    const deleteBtn = document.getElementById('contactDeleteBtn');
    const formMethod = document.getElementById('contactFormMethod');
    
    if (mode === 'add') {
      if (title) title.textContent = 'Add Contact';
      if (form) {
        form.action = contactsStoreRoute;
        form.reset();
      }
      if (formMethod) formMethod.innerHTML = '';
      if (deleteBtn) deleteBtn.style.display = 'none';
      currentContactId = null;
      const ageDisplay = document.getElementById('age_display');
      if (ageDisplay) ageDisplay.value = '';
    }
    
    document.body.style.overflow = 'hidden';
    modal.classList.add('show');
    setTimeout(() => setupContactFormListeners(modal), 100);
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
      
      const fields = ['type','contact_name','contact_no','wa','occupation','employer','email_address','address','location','dob','acquired','source','source_name','agency','agent','status','rank','savings_budget','children'];
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
              } catch (e) {}
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
        } catch (e) {}
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
      dobField.addEventListener('change', function() {
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

  function closeContactModal(){
    document.getElementById('contactModal').classList.remove('show');
    currentContactId = null;
    document.body.style.overflow = '';
  }

  // show edit: fetch /contacts/{id}/edit which returns JSON in controller
  async function openContactDetails(id){
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
      console.log(contact);
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
      populateContactDetails(contact);
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
      document.getElementById('contactScheduleContentWrapper').style.display = 'flex';
      document.getElementById('followupsContentWrapper').style.display = 'block';
      contactFormPageContent.style.display = 'none';
      const editContactFromPageBtn = document.getElementById('editContactFromPageBtn');
      const renewContactBtn = document.getElementById('renewContactBtn');
      if (editContactFromPageBtn) editContactFromPageBtn.style.display = 'inline-block';
      if (renewContactBtn) renewContactBtn.style.display = 'inline-block';
      if (contactContactPageBtn) contactContactPageBtn.style.display = 'inline-block';
          document.querySelectorAll('#contactPageView .contact-tab').forEach(tab => {
        // Remove existing listeners by cloning
        const newTab = tab.cloneNode(true);
        tab.parentNode.replaceChild(newTab, tab);
        // Add click listener

        newTab.addEventListener('click', function(e) {

          console.log(currentContactId);
          e.preventDefault();
          if (!currentContactId) return;
          const baseUrl = this.getAttribute('data-url');
          if (!baseUrl || baseUrl === '#') return;
          window.location.href = baseUrl + '?contact_id=' + currentContactId;
        });
      });
      // openModalWithContact('edit', contact);
    } catch (e) {
      console.error(e);
      alert('Error loading contact data');
    }
  }
    async function openEditContract(id){
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
      console.log(contact);
      // openContactForm('edit', contact);
    } catch (e) {
      console.error(e);
      alert('Error loading policy data');
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

  function populateContactDetails(contact) {
    const content = document.getElementById('contactDetailsContent');
    const scheduleContent = document.getElementById('contactScheduleContent');
    const documentsContent = document.getElementById('documentsContent');
    if (!content || !scheduleContent || !documentsContent) return;


    // Get contact data

    const selectedTypeId = contact?.type ?? null;

    const contactTypeOptions = lookupData.contact_types
      .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedTypeId ? 'selected' : ''}>
          ${ct.name}
        </option>
      `)
      .join('');
        


    const selectedoccupation = contact?.occupation ?? null;
     const occupationOptions = allOccupations
      .map(ct => `
        <option value="${ct}" ${ct == selectedoccupation ? 'selected' : ''}>
          ${ct}
        </option>
      `)
      .join('');

      
    const selectedstatusId = contact?.status ?? null;

    const contactStatusOptions = lookupData.contact_statuses
      .map(ct => `
        <option value="${ct.id}" ${ct.id == selectedstatusId ? 'selected' : ''}>
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


    const dob = contact.dob ? formatDateForInput(contact.dob) : '-';
    const date_acquired = contact.acquired ? formatDateForInput(contact.acquired) : '-';
    const first_contact = contact.acquired ? formatDateForInput(contact.first_contact) : '-';
    const next_follow_up = contact.acquired ? formatDateForInput(contact.next_follow_up) : '-';

    const dobAge = contact.dob ? calculateAge(contact.dob) : '-';
     console.log('DOB:', dob);
    // Top Section: 4 columns
    const col1 = `
      <div class="detail-section-card">
        <div class="detail-section-header">CONTACT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Contact Type</span>
             <select id="type" name="type" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" disabled>
                <option value="">Select</option>
                 ${contactTypeOptions}
              </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Mobile No</span>
            <input  class="detail-value" value="${contact.contact_no || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Contact No</span>
            <input type="text" class="detail-value" value="${contact.contact_no || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Email Address</span>
            <input type="text" class="detail-value" value="${contact.email_address || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Address</span>
            <input type="text" class="detail-value" value="${contact.address || ''}" readonly>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section-card">
        <div class="detail-section-header">OTHER DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Date Of Birth</span>
            <div style="display:flex; gap:5px; align-items:center; flex:1;">
             <input id="dob" name="dob" type="date" value ="${dob}" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px; width: 75%" readonly>
              <input id="age_display" type="text" value ="${dobAge}"  placeholder="Age" readonly class="form-control" style="width:25%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; background: #f5f5f5; font-size: 12px;">
             
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Occupation</span>
             <select id="occupation" name="occupation" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" disabled>
                <option value="">Select or Type</option>
                ${occupationOptions}
              </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Employer</span>
             <select id="employer" name="employer" class="form-control" style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" disabled>
                <option value="">Select or Type</option>
                ${EmployerOptions}
              </select>   
         </div>
          <div class="detail-row">
            <span class="detail-label">Source</span>
              <select id="source" name="source" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" disabled> 
                <option value="">Select</option>
                  ${sourceOptions}
              </select>
              
            </div>
          <div class="detail-row">
            <span class="detail-label">Source Name</span>
            <div style="display:flex; align-items:center; gap:4px;">
              <input type="text" class="detail-value" value="${contact.source_name || ''}" readonly style="flex:1;">
            </div>
          </div>
       
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PROSPECT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Savings Budget</span>
            <input id="savings_budget" name="savings_budget" type="number" step="0.01" class="detail-value" value="${contact.savings_budget || (policy.agency ? policy.agency.name : '') || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Children</span>
            <input id="children" name="children" type="number" min="0" class="detail-value" value="${contact.children || ''}" readonly>
          </div>
          <div class="detail-row">
           <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:center; margin-bottom:15px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Vehicle</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_vehicle" value="1" ${contact.has_vehicle ? 'checked' : ''} disabled>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Home</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_house" value="1" ${contact.has_house ? 'checked' : ''} disabled>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Business</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_business" value="1" ${contact.has_business ? 'checked' : ''} disabled>
                </div>
              </div>
              <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:11px; color:#000; font-weight:normal; margin:0; cursor:pointer;">Boat</label>
                <div class="detail-value checkbox">
                  <input type="checkbox" name="has_boat" value="1" ${contact.has_boat ? 'checked' : ''} disabled>
                </div>
              </div>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Other</span>
            <input type="text" class="detail-value" value="${contact.other || ''}" readonly>
          </div>
        </div>
      </div>
    `;

    const col4 = `
     <div class="detail-section-card">
        <div class="detail-section-header">LEAD STATUS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Date Acquired</span>
             <input id="date_acquired" name="date_acquired" type="date" value ="${date_acquired}" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Contact Status</span>
               <select id="status" name="status" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" disabled>
                <option value="">Select</option>
                 ${contactStatusOptions}
              </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Rank</span>
               <select id="rank" name="rank" class="form-control" required style="width: 100%; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" disabled>
                <option value="">Select</option>
                 ${contactranksOptions}
              </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">1st Contcat</span>
             <input id="first_contact" name="first_contact" type="date" value ="${first_contact}" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Next Follow Up</span>
             <input id="next_follow_up" name="next_follow_up" type="date" value ="${next_follow_up}" class="form-control" style="flex: 1; padding: 4px 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 12px;" readonly>
             </div>
        </div>
      </div>
     `;
        content.innerHTML = col1 + col2 + col3 + col4;

    
    // // Bottom Section: Documents - Dynamic based on policy documents
    // let documentsHTML = '';
    // // Check if documents exist and is an array
    // const documents = policy.documents || [];
    // if (Array.isArray(documents) && documents.length > 0) {
    //   // If policy has documents array, display them
    //   documents.forEach(doc => {
    //     const docName = doc.name || doc.file_name || (doc.type || 'Document');
    //     const isPDF = docName.toLowerCase().endsWith('.pdf') || (doc.type && doc.type.toLowerCase().includes('pdf')) || (doc.format && doc.format.toLowerCase().includes('pdf'));
    //     const iconColor = isPDF ? '#dc3545' : '#000';
    //     const fileIcon = isPDF ? 
    //       '<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' :
    //       '<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
    //     documentsHTML += `
    //       <div class="document-icon">
    //         <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    //           ${fileIcon}
    //         </svg>
    //         <span>${docName}</span>
    //       </div>
    //     `;
    //   });
    // } else {
    //   // Default document icons if no documents available
    //   documentsHTML = `
    //   <div class="document-icon">
    //     <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    //       <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    //       <path d="M14 2V8H20" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    //     </svg>
    //     <span>Proposal</span>
    //   </div>
    //   <div class="document-icon">
    //     <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    //       <rect x="3" y="3" width="18" height="18" rx="2" stroke="#333" stroke-width="2"/>
    //       <path d="M9 9H15M9 15H15M9 12H15" stroke="#333" stroke-width="2" stroke-linecap="round"/>
    //     </svg>
    //     <span>Debit Note</span>
    //   </div>
    //   <div class="document-icon">
    //     <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    //       <rect x="3" y="3" width="18" height="18" rx="2" stroke="#333" stroke-width="2"/>
    //       <path d="M9 9H15M9 15H15M9 12H15" stroke="#333" stroke-width="2" stroke-linecap="round"/>
    //     </svg>
    //     <span>Receipt</span>
    //   </div>
    //   <div class="document-icon">
    //     <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    //       <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    //       <path d="M14 2V8H20" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    //     </svg>
    //     <span>Schedule</span>
    //   </div>
    //   `;
    // }
    // documentsContent.innerHTML = documentsHTML;
  }
  function deleteContact(){
    if (!currentContactId) return;
    if (!confirm('Delete this contact?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/contacts/${currentContactId}`;
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value = csrfToken; form.appendChild(csrf);
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }

  // Column modal functions
  function openColumnModal(){
    document.getElementById('tableResponsive').classList.add('no-scroll');
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      // Always check mandatory fields, otherwise check if in selectedColumns
      cb.checked = mandatoryFields.includes(cb.value) || selectedColumns.includes(cb.value);
    });
    document.body.style.overflow = 'hidden';
    document.getElementById('columnModal').classList.add('show');
    // Initialize drag and drop after modal is shown
    setTimeout(initDragAndDrop, 100);
  }
  function closeColumnModal(){
    document.getElementById('tableResponsive').classList.remove('no-scroll');
    document.getElementById('columnModal').classList.remove('show');
    document.body.style.overflow = '';
  }
  function selectAllColumns(){ 
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      cb.checked = true;
    });
  }
  function deselectAllColumns(){ 
    document.querySelectorAll('.column-checkbox').forEach(cb => {
      // Don't uncheck mandatory fields
      if (!mandatoryFields.includes(cb.value)) {
        cb.checked = false;
      }
    });
  }

  function saveColumnSettings(){
    
    // Get order from DOM - this preserves the drag and drop order
    const items = Array.from(document.querySelectorAll('#columnSelection .column-item'));
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
  
  // Initialize drag and drop when column modal opens
  function initDragAndDrop() {
    const columnSelection = document.getElementById('columnSelection');
    if (!columnSelection) return;
    
    // Make all column items draggable
    const columnItems = columnSelection.querySelectorAll('.column-item');
    
    columnItems.forEach(item => {
      // Skip if already initialized
      if (item.dataset.dragInitialized === 'true') {
        return;
      }
      item.dataset.dragInitialized = 'true';
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
      
      // Drag start
      item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
        e.dataTransfer.setData('text/plain', this.dataset.column);
      });
      
      // Drag end
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
      
      // Drag over
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
      
      // Drag enter
      item.addEventListener('dragenter', function(e) {
        e.preventDefault();
        if (draggedElement && this !== draggedElement) {
          this.classList.add('drag-over');
        }
      });
      
      // Drag leave
      item.addEventListener('dragleave', function(e) {
        // Only remove if we're actually leaving the element
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
  }

  // close modals on ESC and clicking backdrop
  document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeContactModal(); closeColumnModal(); } });
    document.querySelectorAll('.modal').forEach(m => {
      m.addEventListener('click', e => { if (e.target === m) { m.classList.remove('show'); document.body.style.overflow = ''; } });
    });

    // Basic client-side validation for contact form (prevent empty required)
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
      contactForm.addEventListener('submit', function(e){
        const req = this.querySelectorAll('[required]');
        let ok = true;
        req.forEach(f => { if (!String(f.value||'').trim()) { ok = false; f.style.borderColor='red'; } else { f.style.borderColor=''; } });
        if (!ok) { e.preventDefault(); alert('Please fill required fields'); }
      });
    }
  });
