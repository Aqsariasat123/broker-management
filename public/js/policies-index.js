  // Data initialized in Blade template

  // Open policy details (full page view) - MUST be defined before event listeners
  async function openPolicyDetails(id){
    try {
      const res = await fetch(`/policies/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      const policy = data.policy || data;
      currentPolicyId = id;
      currentPolicyData = policy;
      
      // Ensure client data is accessible
      if (policy.client) {
        policy.client_name = policy.client.client_name || policy.client_name;
        policy.source = policy.client.source || policy.source;
        policy.source_name = policy.client.source_name || policy.source_name;
      }
      
      // Get all required elements
      const policyPageName = document.getElementById('policyPageName');
      const clientsTableView = document.getElementById('clientsTableView');
      const policyPageView = document.getElementById('policyPageView');
      const policyDetailsPageContent = document.getElementById('policyDetailsPageContent');
      const policyFormPageContent = document.getElementById('policyFormPageContent');
      const closePolicyPageBtn = document.getElementById('closePolicyPageBtn');
      
      if (!policyPageName || !clientsTableView || !policyPageView || 
          !policyDetailsPageContent || !policyFormPageContent) {
        console.error('Required elements not found');
        console.error('policyPageName:', policyPageName);
        console.error('clientsTableView:', clientsTableView);
        console.error('policyPageView:', policyPageView);
        console.error('policyDetailsPageContent:', policyDetailsPageContent);
        console.error('policyFormPageContent:', policyFormPageContent);
        alert('Error: Page elements not found');
        return;
      }
      
      // Set policy name in header
      const policyPageTitleEl = document.getElementById('policyPageTitle');
      const policyName = policy.policy_no || 'Unknown';
      if (policyPageTitleEl) policyPageTitleEl.textContent = 'Policy No';
      if (policyPageName) policyPageName.textContent = policyName;
      
      // Update navigation tabs to match image (Nominees, Payments, Commission)
//       const policyDetailsNav = document.querySelector('#policyDetailsContentWrapper .client-page-nav');
//       if (policyDetailsNav) {
//         policyDetailsNav.innerHTML = `
//   <button type="button"
//   style="background:#000;color:#fff;border:1px solid #000;padding:6px 16px;border-radius:3px;cursor:pointer;font-size:12px;margin-right:8px;"
//   onmouseover="this.style.background='#fff';this.style.color='#000';"
//   onmouseout="this.style.background='#000';this.style.color='#fff';"
//   onclick="window.location.href = window.routes.schedules">
//   Schedules
// </button>

// <button type="button"
//   style="background:#000;color:#fff;border:1px solid #000;padding:6px 16px;border-radius:3px;cursor:pointer;font-size:12px;margin-right:8px;"
//   onmouseover="this.style.background='#fff';this.style.color='#000';"
//   onmouseout="this.style.background='#000';this.style.color='#fff';"
//   onclick="window.location.href = window.routes.nominees">
//   Nominees
// </button>

// <button type="button"
//   style="background:#000;color:#fff;border:1px solid #000;padding:6px 16px;border-radius:3px;cursor:pointer;font-size:12px;margin-right:8px;"
//   onmouseover="this.style.background='#fff';this.style.color='#000';"
//   onmouseout="this.style.background='#000';this.style.color='#fff';"
//   onclick="window.location.href = window.routes.payments">
//   Payments
// </button>

// <button type="button"
//   style="background:#000;color:#fff;border:1px solid #000;padding:6px 16px;border-radius:3px;cursor:pointer;font-size:12px;margin-right:8px;"
//   onmouseover="this.style.background='#fff';this.style.color='#000';"
//   onmouseout="this.style.background='#000';this.style.color='#fff';"
//   onclick="window.location.href = window.routes.vehicles">
//   Vehicles
// </button>

// <button type="button"
//   style="background:#000;color:#fff;border:1px solid #000;padding:6px 16px;border-radius:3px;cursor:pointer;font-size:12px;margin-right:8px;"
//   onmouseover="this.style.background='#fff';this.style.color='#000';"
//   onmouseout="this.style.background='#000';this.style.color='#fff';"
//   onclick="window.location.href = window.routes.claims">
//   Claims
// </button>

// <button type="button"
//   style="background:#000;color:#fff;border:1px solid #000;padding:6px 16px;border-radius:3px;cursor:pointer;font-size:12px;margin-right:8px;"
//   onmouseover="this.style.background='#fff';this.style.color='#000';"
//   onmouseout="this.style.background='#000';this.style.color='#fff';"
  
//   onclick="window.location.href = window.routes.documents">
//   Documents
// </button>

// <button type="button"
//   style="background:#000;color:#fff;border:1px solid #000;padding:6px 16px;border-radius:3px;cursor:pointer;font-size:12px;"
//   onmouseover="this.style.background='#fff';this.style.color='#000';"
//   onmouseout="this.style.background='#000';this.style.color='#fff';"
//   onclick="window.location.href = window.routes.commissions">
//   Commission
// </button>

//  `;
//       }
      
      populatePolicyDetails(policy);
      
      // Update documents display
      if (policy.documents) {
        updatePolicyDocumentsList(policy);
      }
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      policyPageView.style.display = 'block';
      policyPageView.classList.add('show');
      policyDetailsPageContent.style.display = 'block';
      document.getElementById('policyDetailsContentWrapper').style.display = 'block';
      document.getElementById('policyScheduleContentWrapper').style.display = 'block';
      document.getElementById('documentsContentWrapper').style.display = 'block';
      policyFormPageContent.style.display = 'none';
      const editPolicyFromPageBtn = document.getElementById('editPolicyFromPageBtn');
      const renewPolicyBtn = document.getElementById('renewPolicyBtn');
      if (editPolicyFromPageBtn) editPolicyFromPageBtn.style.display = 'inline-block';
      if (renewPolicyBtn) renewPolicyBtn.style.display = 'none'; // Hide renew button to match image
      if (closePolicyPageBtn) closePolicyPageBtn.style.display = 'inline-block';
      document.querySelectorAll('#policyPageView .policy-tab').forEach(tab => {
        // Remove existing listeners by cloning
        const newTab = tab.cloneNode(true);
        tab.parentNode.replaceChild(newTab, tab);
        // Add click listener

        newTab.addEventListener('click', function(e) {

          console.log(currentPolicyId);
          e.preventDefault();
          if (!currentPolicyId) return;
          const baseUrl = this.getAttribute('data-url');
          if (!baseUrl || baseUrl === '#') return;
          window.location.href = baseUrl + '?policy_id=' + currentPolicyId;
        });
      });

   
    } catch (e) {
      console.error(e);
      alert('Error loading policy details: ' + e.message);
    }
  }
  
  // Edit button from details page
  const editBtn = document.getElementById('editPolicyFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentPolicyId) {
        openEditPolicy(currentPolicyId);
      }
    });
  }
  
  // Wait for DOM to be ready before attaching event listeners
  function initializeEventListeners() {
    const addPolicyBtn = document.getElementById('addPolicyBtn');
    if (addPolicyBtn) {
      addPolicyBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Add button clicked');
        try {
          openPolicyPage('add');
        } catch (error) {
          console.error('Error opening policy page:', error);
          alert('Error opening add policy form: ' + error.message);
        }
      });
    } else {
      console.error('Add policy button not found');
    }
    
    const columnBtn = document.getElementById('columnBtn');
    if (columnBtn) {
      columnBtn.addEventListener('click', () => openColumnModal());
    }
  }
  
  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initializeEventListeners();
      
      // Auto-open add form if life proposal ID is in URL
      const urlParams = new URLSearchParams(window.location.search);
      const lifeProposalId = urlParams.get('life_proposal_id');
      const policyId = urlParams.get('policy_id');
      
      if (policyId) {
        // Auto-open policy detail view
        setTimeout(() => {
          openPolicyDetails(policyId);
        }, 100);
      } else if (lifeProposalId && lifeProposalData) {
        // Open the add form with life proposal data
        setTimeout(() => {
          openPolicyForm('add', lifeProposalData);
        }, 100);
      }
    });
  } else {
    // DOM is already ready
    initializeEventListeners();
    
    // Auto-open add form if life proposal ID is in URL
    const urlParams = new URLSearchParams(window.location.search);
    const lifeProposalId = urlParams.get('life_proposal_id');
    const policyId = urlParams.get('policy_id');
    
    if (policyId) {
      // Auto-open policy detail view
      setTimeout(() => {
        openPolicyDetails(policyId);
      }, 100);
    } else if (lifeProposalId && lifeProposalData) {
      // Open the add form with life proposal data
      setTimeout(() => {
        openPolicyForm('add', lifeProposalData);
      }, 100);
    }
  }
  
  // Tab switching functionality removed - tabs now navigate to separate pages
  // Links handle navigation automatically
  
  // Form submission handler - handle errors and display on page
  const policyForm = document.getElementById('policyForm');
  if (policyForm) {
    policyForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      // Ensure checkbox value is properly set
      const renewableCheckbox = this.querySelector('input[name="renewable"][type="checkbox"]');
      if (renewableCheckbox) {
        if (renewableCheckbox.checked) {
          renewableCheckbox.value = '1';
        } else {
          // Remove the checkbox so it's not submitted (Laravel will treat as false/0)
          renewableCheckbox.disabled = true;
          // Add a hidden input with value 0
          const existingHidden = this.querySelector('input[name="renewable"][type="hidden"]');
          if (!existingHidden) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'renewable';
            hiddenInput.value = '0';
            this.appendChild(hiddenInput);
          }
        }
      }
      
      // Set default values for premium and base_premium if empty
      const premiumInput = this.querySelector('input[name="premium"]');
      const basePremiumInput = this.querySelector('input[name="base_premium"]');
      if (premiumInput && (!premiumInput.value || premiumInput.value === '')) {
        premiumInput.value = '0';
      }
      if (basePremiumInput && (!basePremiumInput.value || basePremiumInput.value === '')) {
        basePremiumInput.value = '0';
      }
      
      // Create FormData
      const formData = new FormData(this);
      const method = this.querySelector('input[name="_method"]')?.value || 'POST';
      const action = this.action;
      
      // Ensure CSRF token is in FormData (should already be there from @csrf, but double-check)
      const csrfInput = this.querySelector('input[name="_token"]');
      if (csrfInput && !formData.has('_token')) {
        formData.append('_token', csrfInput.value);
      }
      
      // Show loading state
      const saveBtn = document.getElementById('policySaveBtnHeader');
      const originalBtnText = saveBtn ? saveBtn.textContent : 'Save';
      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
      }
      
      try {
        // Get CSRF token from meta tag or form
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
          || this.querySelector('input[name="_token"]')?.value 
          || csrfToken;
        
        // Ensure token is in FormData
        if (!formData.has('_token')) {
          formData.append('_token', csrfToken);
        }
        
        const response = await fetch(action, {
          method: method === 'PUT' ? 'POST' : method, // Laravel expects POST for PUT/PATCH with _method
          body: formData,
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success !== false) {
          // Success - redirect or reload
          if (data.redirect) {
            window.location.href = data.redirect;
          } else {
            window.location.reload();
          }
        } else {
          // Error - display on page
          showFormErrors(data.errors || { message: data.message || 'An error occurred' });
          if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = originalBtnText;
          }
        }
      } catch (error) {
        console.error('Form submission error:', error);
        showFormErrors({ message: 'Network error: ' + error.message });
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.textContent = originalBtnText;
        }
      }
    });
  }
  
  // Function to display form errors
  function showFormErrors(errors) {
    // Remove existing error messages
    const existingErrors = document.querySelectorAll('.form-error-message');
    existingErrors.forEach(el => el.remove());
    
    // Create error container
    const errorContainer = document.createElement('div');
    errorContainer.className = 'form-error-message';
    errorContainer.style.cssText = 'background:#fee; border:1px solid #fcc; color:#c33; padding:12px; margin:0 0 15px 0; border-radius:4px;';
    
    let errorHtml = '<strong>Please fix the following errors:</strong><ul style="margin:8px 0 0 0; padding-left:20px;">';
    
    if (typeof errors === 'string') {
      errorHtml += `<li>${errors}</li>`;
    } else if (errors.message) {
      errorHtml += `<li>${errors.message}</li>`;
    } else if (typeof errors === 'object') {
      Object.keys(errors).forEach(key => {
        const errorMessages = Array.isArray(errors[key]) ? errors[key] : [errors[key]];
        errorMessages.forEach(msg => {
          errorHtml += `<li><strong>${key}:</strong> ${msg}</li>`;
        });
      });
    }
    
    errorHtml += '</ul>';
    errorContainer.innerHTML = errorHtml;
    
    // Insert error message at the top of the form
    const formContentWrapper = document.getElementById('policyFormContentWrapper');
    if (formContentWrapper) {
      formContentWrapper.insertBefore(errorContainer, formContentWrapper.firstChild);
    } else {
      // Fallback: insert after header
      const header = document.querySelector('#policyFormPageContent > div:first-child');
      if (header) {
        header.insertAdjacentElement('afterend', errorContainer);
      }
    }
    
    // Scroll to error
    errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
  
  // Handle client selection change to update source fields
  document.addEventListener('change', async function(e) {
    if (e.target.id === 'client_id' && e.target.form && e.target.form.id === 'policyForm') {
      const clientId = e.target.value;
      if (clientId) {
        try {
          const response = await fetch(`/clients/${clientId}`, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          if (response.ok) {
            const client = await response.json();
            const sourceField = document.getElementById('source');
            const sourceNameField = document.getElementById('source_name');
            if (sourceField && client.source) sourceField.value = client.source || '';
            if (sourceNameField && client.source_name) sourceNameField.value = client.source_name || '';
          }
        } catch (error) {
          console.error('Error fetching client data:', error);
        }
      }
    }
  });

  // Filter Toggle Handler - Open filter modal when checked
  (function(){
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
      // Check if filters are active on page load
      const urlParams = new URLSearchParams(window.location.search);
      const hasFilters = urlParams.has('search_term') || urlParams.has('client_name') || 
                         urlParams.has('policy_number') || urlParams.has('insurer_id') || 
                         urlParams.has('policy_class_id') || urlParams.has('agency_id') || 
                         urlParams.has('agent') || urlParams.has('policy_status_id') || 
                         urlParams.has('start_date_from') || urlParams.has('end_date_from') || 
                         urlParams.has('premium_unpaid') || urlParams.has('comm_unpaid');
      
      // Set toggle based on DFR parameter or other filters
      const hasDfr = urlParams.get('dfr') === 'true';
      if (hasDfr || hasFilters) {
        filterToggle.checked = true;
      } else {
        filterToggle.checked = false;
      }

      filterToggle.addEventListener('change', function() {
        if (this.checked) {
          openPolicyFilterModal();
        } else {
          // If unchecked, clear all filter parameters except client_id and dfr
          const urlParams = new URLSearchParams(window.location.search);
          const clientId = urlParams.get('client_id');
          const dfr = urlParams.get('dfr');
          
          // Clear all filter params
          urlParams.delete('record_lines');
          urlParams.delete('search_term');
          urlParams.delete('client_name');
          urlParams.delete('policy_number');
          urlParams.delete('insurer_id');
          urlParams.delete('policy_class_id');
          urlParams.delete('agency_id');
          urlParams.delete('agent');
          urlParams.delete('policy_status_id');
          urlParams.delete('start_date_from');
          urlParams.delete('start_date_to');
          urlParams.delete('end_date_from');
          urlParams.delete('end_date_to');
          urlParams.delete('premium_unpaid');
          urlParams.delete('comm_unpaid');
          
          // Restore client_id and dfr if they existed
          if (clientId) urlParams.set('client_id', clientId);
          if (dfr === 'true') urlParams.set('dfr', dfr);
          
          const queryString = urlParams.toString();
          window.location.href = policiesIndexRoute + (queryString ? '?' + queryString : '');
        }
      });
    }
  })();

  // Open Filter Modal
  function openPolicyFilterModal() {
    const modal = document.getElementById('policyFilterModal');
    if (modal) {
      modal.style.display = 'flex';
    }
  }

  // Close Filter Modal
  function closePolicyFilterModal() {
    const modal = document.getElementById('policyFilterModal');
    if (modal) {
      modal.style.display = 'none';
    }
    // Uncheck filter toggle when closing
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
      filterToggle.checked = false;
    }
  }

  // Apply Policy Filters
  function applyPolicyFilters() {
    const form = document.getElementById('policyFilterForm');
    if (!form) return;

    const urlParams = new URLSearchParams();
    
    // Preserve existing client_id and dfr if they exist
    const currentParams = new URLSearchParams(window.location.search);
    const clientId = currentParams.get('client_id');
    const dfr = currentParams.get('dfr');
    if (clientId) urlParams.set('client_id', clientId);
    if (dfr === 'true') urlParams.set('dfr', dfr);

    // Get form values
    const recordLines = document.getElementById('filterRecordLines')?.value;
    const searchTerm = document.getElementById('filterSearchTerm')?.value;
    const clientName = document.getElementById('filterClientName')?.value;
    const policyNumber = document.getElementById('filterPolicyNumber')?.value;
    const insurerId = document.getElementById('filterInsurer')?.value;
    const policyClassId = document.getElementById('filterInsuranceClass')?.value;
    const agencyId = document.getElementById('filterAgency')?.value;
    const agent = document.getElementById('filterAgent')?.value;
    const statusId = document.getElementById('filterStatus')?.value;
    const startDateFrom = document.getElementById('filterStartDateFrom')?.value;
    const startDateTo = document.getElementById('filterStartDateTo')?.value;
    const endDateFrom = document.getElementById('filterEndDateFrom')?.value;
    const endDateTo = document.getElementById('filterEndDateTo')?.value;
    const premiumUnpaid = document.getElementById('filterPremiumUnpaid')?.value;
    const commUnpaid = document.getElementById('filterCommUnpaid')?.value;

    // Add filter values (include record_lines even if default, and agent even if 'Simon')
    if (recordLines) urlParams.set('record_lines', recordLines);
    if (searchTerm) urlParams.set('search_term', searchTerm);
    if (clientName) urlParams.set('client_name', clientName);
    if (policyNumber) urlParams.set('policy_number', policyNumber);
    if (insurerId) urlParams.set('insurer_id', insurerId);
    if (policyClassId) urlParams.set('policy_class_id', policyClassId);
    if (agencyId) urlParams.set('agency_id', agencyId);
    if (agent) urlParams.set('agent', agent);
    if (statusId) urlParams.set('policy_status_id', statusId);
    if (startDateFrom) urlParams.set('start_date_from', startDateFrom);
    if (startDateTo) urlParams.set('start_date_to', startDateTo);
    if (endDateFrom) urlParams.set('end_date_from', endDateFrom);
    if (endDateTo) urlParams.set('end_date_to', endDateTo);
    if (premiumUnpaid) urlParams.set('premium_unpaid', premiumUnpaid);
    if (commUnpaid) urlParams.set('comm_unpaid', commUnpaid);

    // Close modal
    closePolicyFilterModal();

    // Navigate with filters
    const queryString = urlParams.toString();
    window.location.href = policiesIndexRoute + (queryString ? '?' + queryString : '');
  }

  // Make functions globally accessible
  window.openPolicyFilterModal = openPolicyFilterModal;
  window.closePolicyFilterModal = closePolicyFilterModal;
  window.applyPolicyFilters = applyPolicyFilters;

  // DFR Only Filter
  (function(){
    const btn = document.getElementById('dfrOnlyBtn');
    if (btn) {
      btn.addEventListener('click', () => {
        // Set toggle switch to true
        const filterToggle = document.getElementById('filterToggle');
        if (filterToggle) {
          filterToggle.checked = true;
        }
        
        const u = new URL(window.location.href);
        if (u.searchParams.get('dfr') === 'true') {
          u.searchParams.delete('dfr');
        } else {
          u.searchParams.set('dfr', 'true');
        }
        window.location.href = u.toString();
      });
    }
    const listAllBtn = document.getElementById('listAllBtn');
    if (listAllBtn) {
      listAllBtn.addEventListener('click', () => {
        // Set toggle switch to false
        const filterToggle = document.getElementById('filterToggle');
        if (filterToggle) {
          filterToggle.checked = false;
        }
        
        window.location.href = policiesIndexRoute;
      });
    }
  })();

  // Populate policy details view
  function populatePolicyDetails(policy) {
    const content = document.getElementById('policyDetailsContent');
    const scheduleContent = document.getElementById('policyScheduleContent');
    const documentsContent = document.getElementById('documentsContent');
    if (!content || !scheduleContent || !documentsContent) return;

    function formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      const day = date.getDate();
      const month = months[date.getMonth()];
      const year = String(date.getFullYear()).slice(-2);
      // Format: 7-Dec-25 (no leading zero for day if single digit)
      return `${day}-${month}-${year}`;
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
    
    // Get client data
    const client = policy.client || {};
    let clientName = policy.client_name || '';
    if (!clientName && client) {
      clientName = client.client_name || (client.first_name ? `${client.first_name} ${client.surname || ''}`.trim() : '');
    }
    const source = policy.source || client.source || '';
    const sourceName = policy.source_name || client.source_name || '';
    const applicationDate = formatDate(policy.date_registered);
    const channelName = policy.channel_name || (policy.channel ? policy.channel.name : '');
    
    // Top Section: 4 columns - matching image design
    const col1 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PARTIES AND CLASS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Policyholder</span>
            <input type="text" class="detail-value" value="${clientName || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurer</span>
            <input type="text" class="detail-value" value="${policy.insurer_name || (policy.insurer ? policy.insurer.name : '') || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurance Class</span>
            <input type="text" class="detail-value" value="${policy.policy_class_name || (policy.policyClass ? policy.policyClass.name : '') || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Type</span>
            <input type="text" class="detail-value" value="Fixed Term" readonly>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section-card">
        <div class="detail-section-header">POLICY NOTE</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Application Date</span>
            <input type="text" class="detail-value" value="${applicationDate || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Business Type</span>
            <input type="text" class="detail-value" value="${policy.business_type_name || (policy.businessType ? policy.businessType.name : '') || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Status</span>
            <div style="display:flex; align-items:center; gap:8px;">
              <input type="text" class="detail-value" value="${policy.policy_status_name || (policy.policyStatus ? policy.policyStatus.name : '') || ''}" readonly style="flex:1;">
              <button type="button" style="background:#ffc107; color:#000; border:none; padding:6px 12px; border-radius:3px; cursor:pointer; font-size:11px; white-space:nowrap;">DFR</button>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Renewal Notices</span>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
              <button type="button" style="background:${policy.renewable ? '#fff' : '#f3742a'}; color:${policy.renewable ? '#000' : '#fff'}; border:${policy.renewable ? '1px solid #ddd' : 'none'}; padding:4px 12px; border-radius:3px; font-size:11px; cursor:default;">No</button>
              <button type="button" style="background:${policy.renewable ? '#f3742a' : '#fff'}; color:${policy.renewable ? '#fff' : '#000'}; border:${policy.renewable ? 'none' : '1px solid #ddd'}; padding:4px 12px; border-radius:3px; font-size:11px; cursor:default;">Yes</button>
              <span style="font-size:11px; color:#555;">Channel</span>
              <button type="button" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:3px; font-size:11px; cursor:default;">Email</button>
            </div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section-card">
        <div class="detail-section-header">AGENCY & SOURCE</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Agency</span>
            <input type="text" class="detail-value" value="${policy.agency_name || (policy.agency ? policy.agency.name : '') || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Agent</span>
            <input type="text" class="detail-value" value="${policy.agent || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Source</span>
            <input type="text" class="detail-value" value="${source || ''}" readonly>
            </div>
          <div class="detail-row">
            <span class="detail-label">Source Name</span>
            <input type="text" class="detail-value" value="${sourceName || ''}" readonly>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section-card">
        <div class="detail-section-header">OTHER DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Loading Applied</span>
            <div style="display:flex; gap:8px;">
              <button type="button" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:3px; font-size:11px; cursor:default;">Yes</button>
              <button type="button" style="background:#fff; color:#000; border:1px solid #ddd; padding:4px 12px; border-radius:3px; font-size:11px; cursor:default;">No</button>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Reason</span>
            <input type="text" class="detail-value" value="${policy.loading_reason || ''}" readonly>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
    
    // Middle Section: Policy Schedule
    const currentYear = policy.start_date ? new Date(policy.start_date).getFullYear() : new Date().getFullYear();
    const termNumber = policy.term || '1';
    // Ensure term_unit is a string, not an object
    let termUnit = 'Year';
    if (policy.term_unit) {
      if (typeof policy.term_unit === 'string') {
        termUnit = policy.term_unit;
      } else if (typeof policy.term_unit === 'object' && policy.term_unit.name) {
        termUnit = policy.term_unit.name;
      } else {
        termUnit = String(policy.term_unit);
      }
    }
    const payPlan = policy.pay_plan_name || (policy.payPlan ? policy.payPlan.name : '');
    // Get NOP and interval from policy data
    // NOP comes from payment plans count, interval from payment plan frequency or policy frequency
    let nop = policy.no_of_instalments || '';
    let interval = policy.payment_plan_frequency || policy.frequency_name || '';
    
    // If not found in policy data, calculate from schedules
    if (!nop && policy.schedules && policy.schedules.length > 0) {
      nop = policy.schedules.reduce((total, s) => {
        return total + (s.payment_plans ? s.payment_plans.length : 0);
      }, 0);
    }
    
    // If interval not found, try from frequency relationship
    if (!interval) {
      if (policy.frequency) {
        if (typeof policy.frequency === 'string') {
          interval = policy.frequency;
        } else if (policy.frequency.name) {
          interval = policy.frequency.name;
        }
      }
    }
    
    // Fallback to empty string if still not found
    nop = nop || '';
    interval = interval || '';
    // Handle policy plan name - check both direct field and relationship
    const policyPlanName = policy.policy_plan_name || (policy.policyPlan ? policy.policyPlan.name : '');
    
    const scheduleCol1 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PLAN & VALUE DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Year</span>
            <select class="detail-value" disabled style="appearance:auto; -webkit-appearance:menulist;">
              <option ${policy.start_date ? new Date(policy.start_date).getFullYear() == currentYear ? 'selected' : '' : ''}>${currentYear}</option>
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Plan</span>
            <input type="text" class="detail-value" value="${policyPlanName || (policy.policyPlan ? policy.policyPlan.name : '') || ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Sum Insured</span>
            <input type="text" class="detail-value" value="${policy.sum_insured ? formatNumber(policy.sum_insured) : ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Last Endorsement</span>
            <input type="text" class="detail-value" value="${policy.last_endorsement || ''}" readonly>
          </div>
        </div>
      </div>
    `;

    const scheduleCol2 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PERIOD OF COVER</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Term</span>
            <div style="display:flex; gap:4px; align-items:center;">
              <input type="text" class="detail-value" value="${termNumber}" readonly style="flex:0 0 50px;">
              <span style="font-size:12px; color:#555;">Years</span>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Start Date</span>
            <input type="text" class="detail-value" value="${policy.start_date ? formatDate(policy.start_date) : ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Maturity Date</span>
            <input type="text" class="detail-value" value="${policy.end_date ? formatDate(policy.end_date) : ''}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Cancelled Date</span>
            <input type="text" class="detail-value" value="${policy.cancelled_date ? formatDate(policy.cancelled_date) : ''}" readonly>
          </div>
        </div>
      </div>
    `;

    const scheduleCol3 = `
      <div class="detail-section-card">
        <div class="detail-section-header">ADD ONS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">WSC</span>
            <input type="text" class="detail-value" value="${policy.wsc ? formatNumber(policy.wsc) : '10,000'}" readonly style="text-align:right;">
<<<<<<< HEAD
          </div>
          <div class="detail-row">
            <span class="detail-label">LOU</span>
            <input type="text" class="detail-value" value="${policy.lou ? formatNumber(policy.lou) : '15,000'}" readonly style="text-align:right;">
          </div>
          <div class="detail-row">
=======
          </div>
          <div class="detail-row">
            <span class="detail-label">LOU</span>
            <input type="text" class="detail-value" value="${policy.lou ? formatNumber(policy.lou) : '15,000'}" readonly style="text-align:right;">
          </div>
          <div class="detail-row">
>>>>>>> abdullah_web
            <span class="detail-label">PA</span>
            <input type="text" class="detail-value" value="${policy.pa ? formatNumber(policy.pa) : '250,000'}" readonly style="text-align:right;">
          </div>
        </div>
      </div>
    `;

    const scheduleCol4 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PREMIUM & PAYMENT PLAN</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Base Premium</span>
            <input type="text" class="detail-value" value="${formatNumber(policy.base_premium)}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Premium</span>
            <input type="text" class="detail-value" value="${formatNumber(policy.premium)}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">FOP / MOP</span>
            <div style="display:flex; gap:4px;">
              <input type="text" class="detail-value" value="M" readonly style="width:60px;">
              <input type="text" class="detail-value" value="${policy.method_of_payment || ''}" readonly style="flex:1;">
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">SOP</span>
            <input type="text" class="detail-value" value="${policy.sop || ''}" readonly>
          </div>
        </div>
      </div>
    `;

    scheduleContent.innerHTML = scheduleCol1 + scheduleCol2 + scheduleCol3 + scheduleCol4;
    
    // Bottom Section: Documents - Dynamic based on policy documents
    let documentsHTML = '';
    // Check if documents exist and is an array
    const documents = policy.documents || [];
    if (Array.isArray(documents) && documents.length > 0) {
      // If policy has documents array, display them
      documents.forEach(doc => {
        const docName = doc.name || doc.file_name || (doc.type || 'Document');
        const isPDF = docName.toLowerCase().endsWith('.pdf') || (doc.type && doc.type.toLowerCase().includes('pdf')) || (doc.format && doc.format.toLowerCase().includes('pdf'));
        const iconColor = isPDF ? '#dc3545' : '#000';
        const fileIcon = isPDF ? 
          '<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' :
          '<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        documentsHTML += `
          <div class="document-icon">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              ${fileIcon}
            </svg>
            <span>${docName}</span>
          </div>
        `;
      });
    } else {
      // Default document icons if no documents available
      documentsHTML = `
      <div class="document-icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M14 2V8H20" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Proposal</span>
      </div>
      <div class="document-icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="3" y="3" width="18" height="18" rx="2" stroke="#333" stroke-width="2"/>
          <path d="M9 9H15M9 15H15M9 12H15" stroke="#333" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>Debit Note</span>
      </div>
      <div class="document-icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="3" y="3" width="18" height="18" rx="2" stroke="#333" stroke-width="2"/>
          <path d="M9 9H15M9 15H15M9 12H15" stroke="#333" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>Receipt</span>
      </div>
      <div class="document-icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M14 2V8H20" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Schedule</span>
      </div>
      `;
    }
    documentsContent.innerHTML = documentsHTML;
  }
  
  // Open policy page (Add only)
  function openPolicyPage(mode) {
    if (mode === 'add') {
      openPolicyForm('add');
    }
  }

  async function openEditPolicy(id){
    try {
      const res = await fetch(`/policies/${id}/edit`, { 
        headers: { 
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        } 
      });
      if (!res.ok) throw new Error('Network error');
      const policy = await res.json();
      currentPolicyId = id;
      openPolicyForm('edit', policy);
    } catch (e) {
      console.error(e);
      alert('Error loading policy data');
    }
  }

  function openPolicyForm(mode = 'add', policy = null){
    const pageForm = document.getElementById('policyForm');
    const formMethod = document.getElementById('policyFormMethod');
    const formContent = document.getElementById('policyFormContent');
    const formScheduleContent = document.getElementById('policyFormScheduleContent');
    const formDocumentsContent = document.getElementById('policyFormDocumentsContent');
    
    if (!pageForm || !formMethod || !formContent || !formScheduleContent || !formDocumentsContent) {
      console.error('Required form elements not found');
      alert('Error: Form elements not found. Please refresh the page.');
      return;
    }
    
    const closeBtn = document.getElementById('closePolicyPageBtn');
    const editBtn = document.getElementById('editPolicyFromPageBtn');

    // Hide tabs by default, show only in edit mode
    const policyFormTabs = document.getElementById('policyFormTabs');
    const policyFormTitle = document.getElementById('policyFormTitle');

    if (mode === 'add') {
      // Set header
      const policyPageTitleEl = document.getElementById('policyPageTitle');
      const policyPageNameEl = document.getElementById('policyPageName');
      if (policyPageTitleEl) policyPageTitleEl.textContent = 'Policy No';
      if (policyPageNameEl) policyPageNameEl.textContent = 'Add New';
      if (policyFormTitle) policyFormTitle.innerHTML  = 'Policy No : <span style="color: orange;">Add New</span>';
      
      // Set form action
      pageForm.action = policiesStoreRoute;
      if (formMethod) formMethod.innerHTML = '';
      
      // Hide/show buttons
      if (closeBtn) closeBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      
      // Populate form - if life proposal data exists, use special design
      if (lifeProposalData) {
        // Show tabs for life proposal generated policy
        if (policyFormTabs) policyFormTabs.style.display = 'block';
        // Show Back button
        const backBtn = document.getElementById('backPolicyFormBtnHeader');
        if (backBtn) backBtn.style.display = 'inline-block';
        // Use special form layout for life proposal
        populateLifeProposalPolicyForm(lifeProposalData, formContent, formScheduleContent, formDocumentsContent);
      } else {
        // Hide tabs for regular Add mode
        if (policyFormTabs) policyFormTabs.style.display = 'none';
        populatePolicyForm(null, formContent, formScheduleContent, formDocumentsContent, true);
      }
          } else {
      // Edit mode - use detailed design (keep current design)
      // Set header
      const policyPageTitleEl = document.getElementById('policyPageTitle');
      const policyPageNameEl = document.getElementById('policyPageName');
      if (policyPageTitleEl) policyPageTitleEl.textContent = 'Policy';
      if (policyPageNameEl) policyPageNameEl.textContent = policy.policy_no || 'Edit';
      if (policyFormTitle) policyFormTitle.textContent = `Policy - ${policy.policy_no || 'Edit'}`;
      
      // Show tabs for Edit mode
      if (policyFormTabs) policyFormTabs.style.display = 'block';
      
      // Set form action for update
      pageForm.action = `/policies/${currentPolicyId}`;
      formMethod.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
      
      // Ensure CSRF token is present in the form
      let csrfInput = pageForm.querySelector('input[name="_token"]');
      if (!csrfInput) {
        csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken;
        pageForm.appendChild(csrfInput);
      }
      
      // Hide/show buttons - same as add mode
      if (closeBtn) closeBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      
      // Get client data
      const client = policy.client || {};
      if (policy.client) {
        policy.client_name = policy.client.client_name || policy.client_name;
        policy.source = policy.client.source || policy.source;
        policy.source_name = policy.client.source_name || policy.source_name;
      }
      
      // Populate form with policy data
      populatePolicyForm(policy, formContent, formScheduleContent, formDocumentsContent);
      
      // Load documents if policy has ID
      if (currentPolicyId && policy) {
        // Documents should already be in policy object from the API
        if (policy.documents) {
          updatePolicyDocumentsList(policy);
        }
      }
    }

    // Hide table view, show page view
    const clientsTableView = document.getElementById('clientsTableView');
    const policyPageView = document.getElementById('policyPageView');
    const policyDetailsPageContent = document.getElementById('policyDetailsPageContent');
    const policyDetailsContentWrapper = document.getElementById('policyDetailsContentWrapper');
    const policyScheduleContentWrapper = document.getElementById('policyScheduleContentWrapper');
    const documentsContentWrapper = document.getElementById('documentsContentWrapper');
    const policyFormPageContent = document.getElementById('policyFormPageContent');
    
    if (!clientsTableView || !policyPageView || !policyFormPageContent) {
      console.error('Required page elements not found');
      alert('Error: Page elements not found. Please refresh the page.');
      return;
    }
    
    clientsTableView.classList.add('hidden');
    policyPageView.style.display = 'block';
    policyPageView.classList.add('show');
    
    // Hide all detail view elements
    if (policyDetailsPageContent) policyDetailsPageContent.style.display = 'none';
    if (policyDetailsContentWrapper) policyDetailsContentWrapper.style.display = 'none';
    if (policyScheduleContentWrapper) policyScheduleContentWrapper.style.display = 'none';
    if (documentsContentWrapper) documentsContentWrapper.style.display = 'none';
    
    // Show form view
    policyFormPageContent.style.display = 'block';
    
    // Add form-mode class to prevent scrolling
    policyPageView.classList.add('form-mode');
    document.body.style.overflow = 'hidden';
    
    // Ensure form content wrapper is visible
    const formContentWrapper = document.getElementById('policyFormContentWrapper');
    if (formContentWrapper) {
      formContentWrapper.style.display = 'block';
    }
    
    // Hide schedule wrapper since everything is in formContent now
    const scheduleWrapper = document.getElementById('policyFormScheduleWrapper');
    if (scheduleWrapper) {
      scheduleWrapper.style.display = 'none';
    }
  }
  
  // Life Proposal Generated Policy Form Layout (matches image design)
  function populateLifeProposalPolicyForm(lifeProposal, formContent, formScheduleContent, formDocumentsContent) {
    function formatDateForInput(dateStr) {
      if (!dateStr) return '';
      try {
        let date;
        if (typeof dateStr === 'string') {
          if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
            return dateStr;
          }
          date = new Date(dateStr);
        } else {
          date = new Date(dateStr);
        }
        if (isNaN(date.getTime())) return '';
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      } catch (e) {
        return '';
      }
    }
    
    function createSelectOptions(options, selectedValue, includeEmpty = true) {
      let html = includeEmpty ? '<option value="">Select</option>' : '';
      options.forEach(opt => {
        let value = opt.id !== null && opt.id !== undefined ? opt.id : (opt.name || opt);
        let name = opt.client_name || opt.name || String(value);
        if (opt.clid && opt.client_name) {
          name = `${opt.client_name} (${opt.clid})`;
        }
        const selected = value == selectedValue ? 'selected' : '';
        html += `<option value="${value}" ${selected}>${name}</option>`;
      });
      return html;
    }
    
    // Update tabs to show only Nominees, Payments, Commission
    const tabsNav = document.getElementById('policyFormTabsNav');
    if (tabsNav) {
      tabsNav.innerHTML = `
        <button type="button" class="policy-tab active" style="background:#000; color:#fff; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:12px; margin-right:8px;">Nominees</button>
        <button type="button" class="policy-tab" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:12px; margin-right:8px;">Payments</button>
        <button type="button" class="policy-tab" style="background:#fff; color:#000; border:1px solid #ddd; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:12px;">Commission</button>
      `;
    }
    
    const lp = lifeProposal || {};
    const currentYear = new Date().getFullYear();
    
    // Main Policy Details - 4 columns layout
    const policyDetails = `
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:12px; margin-bottom:12px;">
        <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #eee;">
          <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Policy Number *</label>
          <input type="text" name="policy_no" id="policy_no" class="form-control" required style="width:200px; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
        </div>
        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:12px;">
          <!-- PARTIES AND CLASS -->
          <div>
            <h4 style="margin:0 0 10px 0; font-size:12px; font-weight:600; color:#333; text-transform:uppercase;">PARTIES AND CLASS</h4>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Policyholder</label>
                <select name="client_id" id="client_id" class="form-control" required style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  ${createSelectOptions(lookupData.clients || [])}
                </select>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Insurer</label>
                <select name="insurer_id" id="insurer_id" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  ${createSelectOptions(lookupData.insurers || [])}
                </select>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Insurance Class</label>
                <select name="policy_class_id" id="policy_class_id" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  ${createSelectOptions(lookupData.policy_classes || [])}
                </select>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Policy Type</label>
                <input type="text" name="policy_type" id="policy_type" value="Fixed Term" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
            </div>
          </div>
          
          <!-- POLICY NOTE -->
          <div>
            <h4 style="margin:0 0 10px 0; font-size:12px; font-weight:600; color:#333; text-transform:uppercase;">POLICY NOTE</h4>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Application Date</label>
                <input type="date" name="date_registered" id="date_registered" value="${formatDateForInput(lp.date || lp.offer_date)}" class="form-control" required style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Business Type</label>
                <select name="business_type_id" id="business_type_id" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  ${createSelectOptions(lookupData.business_types || [])}
                </select>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Policy Status</label>
                <div style="display:flex; gap:8px; align-items:center;">
                  <select name="policy_status_id" id="policy_status_id" class="form-control" style="flex:1; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                    ${createSelectOptions(lookupData.policy_statuses || [])}
                  </select>
                  <button type="button" style="background:#ffc107; color:#000; border:none; padding:6px 12px; border-radius:3px; cursor:pointer; font-size:11px; white-space:nowrap;">DFR</button>
                </div>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Renewal Notices</label>
                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                  <button type="button" class="renewal-notice-btn active" data-value="0" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:3px; cursor:pointer; font-size:11px;">No</button>
                  <button type="button" class="renewal-notice-btn" data-value="1" style="background:#fff; color:#000; border:1px solid #ddd; padding:4px 12px; border-radius:3px; cursor:pointer; font-size:11px;">Yes</button>
                  <span style="font-size:11px; color:#555;">Channel</span>
                  <button type="button" class="channel-btn active" data-value="email" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:3px; cursor:pointer; font-size:11px;">Email</button>
                  <input type="hidden" name="renewable" id="renewable" value="0">
                </div>
              </div>
            </div>
          </div>
          
          <!-- BUSINESS DETAILS -->
          <div>
            <h4 style="margin:0 0 10px 0; font-size:12px; font-weight:600; color:#333; text-transform:uppercase;">BUSINESS DETAILS</h4>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Agency</label>
                <select name="agency_id" id="agency_id" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  ${createSelectOptions(lookupData.agencies || [])}
                </select>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Agent</label>
                <input type="text" name="agent" id="agent" value="${lp.agency || ''}" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Source</label>
                <input type="text" name="source" id="source" value="${lp.source_of_payment || ''}" class="form-control" readonly style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px; background:#f5f5f5;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Source Name</label>
                <input type="text" name="source_name" id="source_name" value="${lp.source_name || ''}" class="form-control" readonly style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px; background:#f5f5f5;">
              </div>
            </div>
          </div>
          
          <!-- OTHER DETAIL -->
          <div>
            <h4 style="margin:0 0 10px 0; font-size:12px; font-weight:600; color:#333; text-transform:uppercase;">OTHER DETAIL</h4>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Loading Applied</label>
                <div style="display:flex; gap:8px;">
                  <button type="button" class="loading-btn" data-value="1" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:3px; cursor:pointer; font-size:11px;">Yes</button>
                  <button type="button" class="loading-btn active" data-value="0" style="background:#fff; color:#000; border:1px solid #ddd; padding:4px 12px; border-radius:3px; cursor:pointer; font-size:11px;">No</button>
                  <input type="hidden" name="loading_applied" id="loading_applied" value="0">
                </div>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Reason</label>
                <input type="text" name="loading_reason" id="loading_reason" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Policy Schedule Section - 4 columns
    const scheduleDetails = `
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:12px; margin-bottom:12px;">
        <h4 style="margin:0 0 12px 0; font-size:13px; font-weight:600; color:#333;">Policy Schedule</h4>
        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:12px;">
          <!-- PLAN & VALUE DETAILS -->
          <div>
            <h5 style="margin:0 0 8px 0; font-size:11px; font-weight:600; color:#555; text-transform:uppercase;">PLAN & VALUE DETAILS</h5>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Year</label>
                <input type="text" value="${currentYear}" class="form-control" readonly style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px; background:#f5f5f5;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Policy Plan</label>
                <select name="policy_plan_id" id="policy_plan_id" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  ${createSelectOptions(lookupData.policy_plans || [])}
                </select>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Sum Insured</label>
                <input type="number" step="0.01" name="sum_insured" id="sum_insured" value="${lp.sum_assured || ''}" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Last Endorsement</label>
                <input type="text" name="last_endorsement" id="last_endorsement" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
            </div>
          </div>
          
          <!-- PERIOD OF COVER -->
          <div>
            <h5 style="margin:0 0 8px 0; font-size:11px; font-weight:600; color:#555; text-transform:uppercase;">PERIOD OF COVER</h5>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Term</label>
                <div style="display:flex; gap:4px;">
                  <input type="number" name="term" id="term" value="${lp.term || ''}" class="form-control" style="flex:1; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  <select name="term_unit" id="term_unit" class="form-control" style="width:80px; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                    ${createSelectOptions(lookupData.term_units || [], '', false)}
                  </select>
                </div>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="${formatDateForInput(lp.start_date)}" class="form-control" required style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Maturity Date</label>
                <input type="date" name="end_date" id="end_date" value="${formatDateForInput(lp.maturity_date)}" class="form-control" required style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Cancelled Date</label>
                <input type="date" name="cancelled_date" id="cancelled_date" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
            </div>
          </div>
          
          <!-- ADD ONS -->
          <div>
            <h5 style="margin:0 0 8px 0; font-size:11px; font-weight:600; color:#555; text-transform:uppercase; background:#f5f5f5; padding:6px 8px; border-radius:2px;">ADD ONS</h5>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div style="display:flex; align-items:center; gap:12px;">
                <label style="font-size:11px; font-weight:600; color:#555; min-width:40px; margin:0;">WSC</label>
                <input type="number" step="0.01" name="wsc" id="wsc" class="form-control" value="10000" style="flex:1; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px; text-align:right;">
              </div>
              <div style="display:flex; align-items:center; gap:12px;">
                <label style="font-size:11px; font-weight:600; color:#555; min-width:40px; margin:0;">LOU</label>
                <input type="number" step="0.01" name="lou" id="lou" class="form-control" value="15000" style="flex:1; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px; text-align:right;">
              </div>
              <div style="display:flex; align-items:center; gap:12px;">
                <label style="font-size:11px; font-weight:600; color:#555; min-width:40px; margin:0;">PA</label>
                <input type="number" step="0.01" name="pa" id="pa" class="form-control" value="250000" style="flex:1; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px; text-align:right;">
              </div>
            </div>
          </div>
          
          <!-- PREMIUM & PAYMENT PLAN -->
          <div>
            <h5 style="margin:0 0 8px 0; font-size:11px; font-weight:600; color:#555; text-transform:uppercase;">PREMIUM & PAYMENT PLAN</h5>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Base Premium</label>
                <input type="number" step="0.01" name="base_premium" id="base_premium" value="${lp.base_premium || ''}" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">Premium</label>
                <input type="number" step="0.01" name="premium" id="premium" value="${lp.premium || ''}" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">FOP / MOP</label>
                <div style="display:flex; gap:4px;">
                  <input type="text" name="fop_mop" id="fop_mop" value="M" class="form-control" style="width:60px; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                  <input type="text" name="mop_detail" id="mop_detail" value="${lp.method_of_payment || ''}" class="form-control" style="flex:1; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
                </div>
              </div>
              <div>
                <label style="display:block; font-size:11px; font-weight:600; margin-bottom:4px; color:#555;">SOP</label>
                <input type="text" name="sop" id="sop" class="form-control" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:3px;">
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Set content
    if (formContent) {
      formContent.innerHTML = policyDetails;
      formContent.style.display = 'block';
    }
    
    if (formScheduleContent) {
      formScheduleContent.innerHTML = scheduleDetails;
      formScheduleContent.style.display = 'block';
    }
    
    // Add event listeners for toggle buttons
    setTimeout(() => {
      // Renewal notice buttons
      document.querySelectorAll('.renewal-notice-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          document.querySelectorAll('.renewal-notice-btn').forEach(b => {
            b.classList.remove('active');
            b.style.background = '#fff';
            b.style.color = '#000';
            b.style.border = '1px solid #ddd';
          });
          this.classList.add('active');
          this.style.background = '#f3742a';
          this.style.color = '#fff';
          this.style.border = 'none';
          document.getElementById('renewable').value = this.dataset.value;
        });
      });
      
      // Loading buttons
      document.querySelectorAll('.loading-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          document.querySelectorAll('.loading-btn').forEach(b => {
            b.classList.remove('active');
            b.style.background = '#fff';
            b.style.color = '#000';
            b.style.border = '1px solid #ddd';
          });
          this.classList.add('active');
          this.style.background = '#f3742a';
          this.style.color = '#fff';
          this.style.border = 'none';
          document.getElementById('loading_applied').value = this.dataset.value;
        });
      });
      
      // Channel buttons
      document.querySelectorAll('.channel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          document.querySelectorAll('.channel-btn').forEach(b => {
            b.classList.remove('active');
            b.style.background = '#fff';
            b.style.color = '#000';
            b.style.border = '1px solid #ddd';
          });
          this.classList.add('active');
          this.style.background = '#f3742a';
          this.style.color = '#fff';
          this.style.border = 'none';
        });
      });
      
      // Pre-fill data from life proposal
      if (lp.proposers_name) {
        const clientSelect = document.getElementById('client_id');
        if (clientSelect) {
          const clients = lookupData.clients || [];
          const matchingClient = clients.find(c => 
            c.client_name && c.client_name.toLowerCase().includes(lp.proposers_name.toLowerCase())
          );
          if (matchingClient) {
            clientSelect.value = matchingClient.id;
            clientSelect.dispatchEvent(new Event('change'));
          }
        }
      }
      
      if (lp.insurer) {
        const insurerSelect = document.getElementById('insurer_id');
        if (insurerSelect) {
          const insurers = lookupData.insurers || [];
          const matchingInsurer = insurers.find(i => 
            (i.name || i).toLowerCase() === lp.insurer.toLowerCase()
          );
          if (matchingInsurer) {
            insurerSelect.value = matchingInsurer.id || matchingInsurer;
          }
        }
      }
      
      if (lp.policy_plan) {
        const planSelect = document.getElementById('policy_plan_id');
        if (planSelect) {
          const plans = lookupData.policy_plans || [];
          const matchingPlan = plans.find(p => 
            (p.name || p).toLowerCase() === lp.policy_plan.toLowerCase()
          );
          if (matchingPlan) {
            planSelect.value = matchingPlan.id || matchingPlan;
          }
        }
      }
      
      // Handle client selection to populate source fields
      const clientSelect = document.getElementById('client_id');
      if (clientSelect) {
        clientSelect.addEventListener('change', async function() {
          const clientId = this.value;
          if (clientId) {
            try {
              const response = await fetch(`/clients/${clientId}`, {
                headers: { 'Accept': 'application/json' }
              });
              if (response.ok) {
                const data = await response.json();
                const client = data.client || data;
                const sourceInput = document.getElementById('source');
                const sourceNameInput = document.getElementById('source_name');
                if (sourceInput) sourceInput.value = client.source || '';
                if (sourceNameInput && !sourceNameInput.value) {
                  sourceNameInput.value = client.source_name || '';
                }
              }
            } catch (e) {
              console.error('Error fetching client:', e);
            }
          }
        });
      }
    }, 100);
  }

  // Compact Add Policy Form Layout
  function populateCompactAddForm(formContent, formScheduleContent, formDocumentsContent, lifeProposal = null) {
    function formatDateForInput(dateStr) {
      if (!dateStr) return '';
      try {
        let date;
        if (typeof dateStr === 'string') {
          if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
            return dateStr;
          }
          date = new Date(dateStr);
        } else {
          date = new Date(dateStr);
        }
        if (isNaN(date.getTime())) return '';
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      } catch (e) {
        return '';
      }
    }
    
    function createSelectOptions(options, selectedValue, includeEmpty = true) {
      let html = includeEmpty ? '<option value="">Select</option>' : '';
      options.forEach(opt => {
        let value = opt.id !== null && opt.id !== undefined ? opt.id : (opt.name || opt);
        let name = opt.client_name || opt.name || String(value);
        if (opt.clid && opt.client_name) {
          name = `${opt.client_name} (${opt.clid})`;
        }
        const selected = value == selectedValue ? 'selected' : '';
        html += `<option value="${value}" ${selected}>${name}</option>`;
      });
      return html;
    }
    
    // Combined single card with all sections - compact design
    const currentYear = new Date().getFullYear();
    const combinedForm = `
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:8px; margin-bottom:8px;">
        <!-- Policy Details Section -->
        <div style="border-bottom:1px solid #eee; padding-bottom:6px; margin-bottom:6px;">
          <h4 style="margin:0 0 6px 0; font-size:11px; font-weight:600; color:#333; text-transform:uppercase;">Policy Details</h4>
          <div style="display:grid; grid-template-columns:repeat(6, 1fr); gap:4px 6px;">
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Policy No *</label>
              <input type="text" name="policy_no" id="policy_no" class="form-control" required style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Client *</label>
              <select name="client_id" id="client_id" class="form-control" required style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.clients || [])}
              </select>
            </div>
            <div style="position:relative;">
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Class</label>
              <select name="policy_class_id" id="policy_class_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.policy_classes || [])}
              </select>
              <div style="position:absolute; bottom:0; left:0; right:0; display:flex; gap:2px; height:2px; pointer-events:none;">
                <button type="button" onclick="openVehicleDialog()" style="flex:1; padding:0; margin:0; background:#f3742a; border:none; border-radius:0 0 2px 0; cursor:pointer; height:2px; min-height:2px; max-height:2px; pointer-events:auto;" title="Vehicle"></button>
                <button type="button" onclick="openNomineeDialog()" style="flex:1; padding:0; margin:0; background:#f3742a; border:none; border-radius:0 0 0 2px; cursor:pointer; height:2px; min-height:2px; max-height:2px; pointer-events:auto;" title="Nominee"></button>
              </div>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Insurer</label>
              <select name="insurer_id" id="insurer_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.insurers || [])}
              </select>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Asset/Destination</label>
              <input type="text" name="insured_item" id="insured_item" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">App Date *</label>
              <input type="date" name="date_registered" id="date_registered" class="form-control" required style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Business Type</label>
              <select name="business_type_id" id="business_type_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.business_types || [])}
              </select>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Agency</label>
              <select name="agency_id" id="agency_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.agencies || [])}
              </select>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Agent</label>
              <input type="text" name="agent" id="agent" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Source</label>
              <input type="text" name="source" id="source" class="form-control" readonly style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Source Name</label>
              <input type="text" name="source_name" id="source_name" class="form-control" readonly style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5; height:24px;">
            </div>
            <div style="display:flex; align-items:center; gap:4px; padding-top:18px;">
              <input type="checkbox" name="renewable" id="renewable" value="1" style="margin:0; width:14px; height:14px;">
              <label style="font-size:10px; color:#555; margin:0;">Renewal Required?</label>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Channel</label>
              <select name="channel_id" id="channel_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.channels || [])}
              </select>
            </div>
            <div style="grid-column:span 2;">
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Notes</label>
              <textarea name="notes" id="notes" class="form-control" rows="1" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; resize:vertical; min-height:24px;"></textarea>
            </div>
          </div>
        </div>
        
        <!-- Schedule Details Section -->
        <div style="border-bottom:1px solid #eee; padding-bottom:6px; margin-bottom:6px;">
          <h4 style="margin:0 0 6px 0; font-size:11px; font-weight:600; color:#333; text-transform:uppercase;">Schedule Details</h4>
          <div style="display:grid; grid-template-columns:repeat(6, 1fr); gap:4px 6px;">
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Year</label>
              <input type="text" value="${currentYear}" class="form-control" readonly style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; background:#f5f5f5; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Plan</label>
              <select name="policy_plan_id" id="policy_plan_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.policy_plans || [])}
              </select>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Sum Insured</label>
              <input type="number" step="0.01" name="sum_insured" id="sum_insured" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Term</label>
              <input type="number" name="term" id="term" class="form-control" value="1" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Period</label>
              <select name="term_unit" id="term_unit" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.term_units || [])}
              </select>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Start Date *</label>
              <input type="date" name="start_date" id="start_date" class="form-control" required style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">WSC</label>
              <input type="number" step="0.01" name="wsc" id="wsc" class="form-control" value="10000" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">LOU</label>
              <input type="number" step="0.01" name="lou" id="lou" class="form-control" value="15000" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">PA</label>
              <input type="number" step="0.01" name="pa" id="pa" class="form-control" value="250000" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Base Premium</label>
              <input type="number" step="0.01" name="base_premium" id="base_premium" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Total Premium</label>
              <input type="number" step="0.01" name="premium" id="premium" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">End Date *</label>
              <input type="date" name="end_date" id="end_date" class="form-control" required style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
          </div>
        </div>
        
        <!-- Payment Plan Section -->
        <div>
          <h4 style="margin:0 0 6px 0; font-size:11px; font-weight:600; color:#333; text-transform:uppercase;">Payment Plan</h4>
          <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:4px 6px;">
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Option</label>
              <select name="pay_plan_lookup_id" id="pay_plan_lookup_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.pay_plans || [])}
              </select>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Instalments</label>
              <input type="number" name="no_of_instalments" id="no_of_instalments" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Interval</label>
              <select name="frequency_id" id="frequency_id" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
                ${createSelectOptions(lookupData.frequencies || [])}
              </select>
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">Start Date</label>
              <input type="date" name="payment_start_date" id="payment_start_date" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
            <div>
              <label style="display:block; font-size:10px; font-weight:600; margin-bottom:2px; color:#555;">End Date</label>
              <input type="date" name="payment_end_date" id="payment_end_date" class="form-control" style="width:100%; padding:3px 4px; font-size:11px; border:1px solid #ddd; border-radius:2px; height:24px;">
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Set content - all in one card
    if (formContent) {
      formContent.innerHTML = combinedForm;
      formContent.style.display = 'block';
    }
    
    // Hide schedule content wrapper since everything is in formContent now
    if (formScheduleContent) {
      formScheduleContent.innerHTML = '';
      formScheduleContent.style.display = 'none';
    }
    
    // Pre-fill form with life proposal data if available
    if (lifeProposal) {
      setTimeout(() => {
        // Find client by proposer name
        if (lifeProposal.proposers_name) {
          const clientSelect = document.getElementById('client_id');
          if (clientSelect) {
            const clients = lookupData.clients || [];
            const matchingClient = clients.find(c => 
              c.client_name && c.client_name.toLowerCase().includes(lifeProposal.proposers_name.toLowerCase())
            );
            if (matchingClient) {
              clientSelect.value = matchingClient.id;
              clientSelect.dispatchEvent(new Event('change'));
            }
          }
        }
        
        // Pre-fill insurer
        if (lifeProposal.insurer) {
          const insurerSelect = document.getElementById('insurer_id');
          if (insurerSelect) {
            const insurers = lookupData.insurers || [];
            const matchingInsurer = insurers.find(i => 
              (i.name || i).toLowerCase() === lifeProposal.insurer.toLowerCase()
            );
            if (matchingInsurer) {
              insurerSelect.value = matchingInsurer.id || matchingInsurer;
            }
          }
        }
        
        // Pre-fill policy plan
        if (lifeProposal.policy_plan) {
          const planSelect = document.getElementById('policy_plan_id');
          if (planSelect) {
            const plans = lookupData.policy_plans || [];
            const matchingPlan = plans.find(p => 
              (p.name || p).toLowerCase() === lifeProposal.policy_plan.toLowerCase()
            );
            if (matchingPlan) {
              planSelect.value = matchingPlan.id || matchingPlan;
            }
          }
        }
        
        // Pre-fill other fields
        const sumInsuredInput = document.getElementById('sum_insured');
        if (sumInsuredInput && lifeProposal.sum_assured) {
          sumInsuredInput.value = lifeProposal.sum_assured;
        }
        
        const termInput = document.getElementById('term');
        if (termInput && lifeProposal.term) {
          termInput.value = lifeProposal.term;
        }
        
        const basePremiumInput = document.getElementById('base_premium');
        if (basePremiumInput && lifeProposal.base_premium) {
          basePremiumInput.value = lifeProposal.base_premium;
        }
        
        const premiumInput = document.getElementById('premium');
        if (premiumInput && lifeProposal.premium) {
          premiumInput.value = lifeProposal.premium;
        }
        
        const startDateInput = document.getElementById('start_date');
        if (startDateInput && lifeProposal.start_date) {
          startDateInput.value = formatDateForInput(lifeProposal.start_date);
        }
        
        const endDateInput = document.getElementById('end_date');
        if (endDateInput && lifeProposal.maturity_date) {
          endDateInput.value = formatDateForInput(lifeProposal.maturity_date);
        }
        
        const dateRegisteredInput = document.getElementById('date_registered');
        if (dateRegisteredInput && lifeProposal.date) {
          dateRegisteredInput.value = formatDateForInput(lifeProposal.date);
        }
        
        // Pre-fill frequency
        if (lifeProposal.frequency) {
          const frequencySelect = document.getElementById('frequency_id');
          if (frequencySelect) {
            const frequencies = lookupData.frequencies || [];
            const matchingFreq = frequencies.find(f => 
              (f.name || f).toLowerCase() === lifeProposal.frequency.toLowerCase()
            );
            if (matchingFreq) {
              frequencySelect.value = matchingFreq.id || matchingFreq;
            }
          }
        }
        
        // Pre-fill agency
        if (lifeProposal.agency) {
          const agencySelect = document.getElementById('agency_id');
          if (agencySelect) {
            const agencies = lookupData.agencies || [];
            const matchingAgency = agencies.find(a => 
              (a.name || a).toLowerCase() === lifeProposal.agency.toLowerCase()
            );
            if (matchingAgency) {
              agencySelect.value = matchingAgency.id || matchingAgency;
            }
          }
        }
        
        // Pre-fill source name
        const sourceNameInput = document.getElementById('source_name');
        if (sourceNameInput && lifeProposal.source_name) {
          sourceNameInput.value = lifeProposal.source_name;
        }
        
        // Pre-fill notes
        const notesInput = document.getElementById('notes');
        if (notesInput && lifeProposal.notes) {
          notesInput.value = lifeProposal.notes;
        }
      }, 100);
    }
    
    // Handle client selection to populate source fields
    const clientSelect = document.getElementById('client_id');
    if (clientSelect) {
      clientSelect.addEventListener('change', async function() {
        const clientId = this.value;
        if (clientId) {
          try {
            const response = await fetch(`/clients/${clientId}`, {
              headers: { 'Accept': 'application/json' }
            });
            if (response.ok) {
              const data = await response.json();
              const client = data.client || data;
              const sourceInput = document.getElementById('source');
              const sourceNameInput = document.getElementById('source_name');
              if (sourceInput) sourceInput.value = client.source || '';
              if (sourceNameInput && !sourceNameInput.value) {
                sourceNameInput.value = client.source_name || '';
              }
            }
          } catch (e) {
            console.error('Error fetching client:', e);
          }
        }
      });
    }
  }

  function populatePolicyForm(policy, formContent, formScheduleContent, formDocumentsContent, isCompact = false) {
    if (!formContent || !formScheduleContent || !formDocumentsContent) {
      console.error('Form elements not found');
      return;
    }
    
    if (!lookupData) {
      console.error('lookupData not available');
      return;
    }
    
    // Clear all content first
    formContent.innerHTML = '';
    formScheduleContent.innerHTML = '';
    formDocumentsContent.innerHTML = '';
    
    // If compact mode (for Add), use compact layout
    if (isCompact) {
      populateCompactAddForm(formContent, formScheduleContent, formDocumentsContent, policy);
      return;
    }
    
    function formatDateForInput(dateStr) {
      if (!dateStr) return '';
      try {
        let date;
        // Handle different date formats
        if (typeof dateStr === 'string') {
          // If it's already in YYYY-MM-DD format, return as is
          if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
            return dateStr;
          }
          date = new Date(dateStr);
        } else {
          date = new Date(dateStr);
        }
        if (isNaN(date.getTime())) return '';
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      } catch (e) {
        return '';
      }
    }
    
    function formatNumber(num) {
      if (!num && num !== 0) return '';
      const numVal = parseFloat(num);
      if (numVal % 1 === 0) {
        return numVal.toLocaleString('en-US');
      }
      return numVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    
    const p = policy || {};
    const client = p.client || {};
    let clientName = p.client_name || '';
    if (!clientName && client) {
      clientName = client.client_name || (client.first_name ? `${client.first_name} ${client.surname || ''}`.trim() : '');
    }
    const source = p.source || client.source || '';
    const sourceName = p.source_name || client.source_name || '';
    
    // Helper to create select options
    function createSelectOptions(options, selectedValue, includeEmpty = true, nameField = null) {
      let html = includeEmpty ? '<option value="">Select</option>' : '';
      options.forEach(opt => {
        // Handle value - use id if available, otherwise use name, otherwise use the whole opt
        let value;
        if (opt.id !== null && opt.id !== undefined) {
          value = opt.id;
        } else if (opt.name) {
          value = opt.name;
        } else {
          value = opt;
        }
        
        // Determine the name field - check for client_name first, then name, then try to get a string value
        let name;
        if (nameField) {
          name = opt[nameField];
        } else if (opt.client_name) {
          // For clients, include clid if available
          name = opt.clid ? `${opt.client_name} (${opt.clid})` : opt.client_name;
        } else if (opt.name) {
          name = opt.name;
        } else {
          // Fallback: try to find any string property
          name = Object.values(opt).find(v => typeof v === 'string' && v !== value) || String(value);
        }
        
        // For selection matching: compare both by id and by name (for cases where id is null)
        let selected = '';
        if (selectedValue !== null && selectedValue !== undefined) {
          // Convert selectedValue to string for comparison
          const selectedStr = String(selectedValue);
          // Match by id if available
          if (opt.id !== null && opt.id !== undefined && String(opt.id) === selectedStr) {
            selected = 'selected';
          }
          // Match by name (for term_units and other cases where id is null)
          else if (opt.name && opt.name === selectedStr) {
            selected = 'selected';
          }
          // Match by value (fallback)
          else if (String(value) === selectedStr) {
            selected = 'selected';
          }
        }
        
        html += `<option value="${value}" ${selected}>${name}</option>`;
      });
      return html;
    }
    
    // Top Section: 4 columns - Matching details view exactly
    const col1 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PARTIES AND CLASS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Policyholder</span>
            <select name="client_id" id="client_id" class="detail-value" required>
              ${createSelectOptions(lookupData.clients || [], p.client_id)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurer</span>
            <select name="insurer_id" id="insurer_id" class="detail-value">
              ${createSelectOptions(lookupData.insurers || [], p.insurer_id)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurance Class</span>
            <select name="policy_class_id" id="policy_class_id" class="detail-value">
              ${createSelectOptions(lookupData.policy_classes || [], p.policy_class_id)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insured</span>
            <input type="text" name="insured" id="insured" class="detail-value" value="${p.insured || ''}">
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section-card">
        <div class="detail-section-header">POLICY DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Policy No</span>
            <input type="text" name="policy_no" id="policy_no" class="detail-value" value="${p.policy_no || ''}" ${!p || !p.policy_no ? 'required' : ''}>
          </div>
          <div class="detail-row">
            <span class="detail-label">Application Date</span>
            <input type="date" name="date_registered" id="date_registered" class="detail-value" value="${formatDateForInput(p.date_registered)}" required>
          </div>
          <div class="detail-row">
            <span class="detail-label">Business Type</span>
            <select name="business_type_id" id="business_type_id" class="detail-value">
              ${createSelectOptions(lookupData.business_types || [], p.business_type_id)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Status</span>
            <div style="display:flex; align-items:center; gap:4px;">
              <select name="policy_status_id" id="policy_status_id" class="detail-value" style="flex:1;">
                ${createSelectOptions(lookupData.policy_statuses || [], p.policy_status_id)}
              </select>
              <button type="button" class="btn-dfr">DFR</button>
            </div>
          </div>
          <div class="detail-row">
            <div class="renewal-checkbox">
              <input type="checkbox" name="renewable" id="renewable" value="1" ${p.renewable ? 'checked' : ''} style="margin:0;">
              <span style="font-size:10px; color:#666;">Renewal Notices</span>
              <span style="font-size:10px; color:#666; margin-left:6px;">Channel</span>
              <button type="button" class="btn-sms">SMS</button>
            </div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section-card">
        <div class="detail-section-header">AGENCY & SOURCE</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Agency</span>
            <select name="agency_id" id="agency_id" class="detail-value">
              ${createSelectOptions(lookupData.agencies || [], p.agency_id)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Agent</span>
            <input type="text" name="agent" id="agent" class="detail-value" value="${p.agent || ''}">
          </div>
          <div class="detail-row">
            <span class="detail-label">Channel</span>
            <select name="channel_id" id="channel_id" class="detail-value">
              ${createSelectOptions(lookupData.channels || [], p.channel_id)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Source</span>
            <input type="text" name="source" id="source" class="detail-value" value="${source}" readonly>
          </div>
          <div class="detail-row">
            <span class="detail-label">Source Name</span>
            <input type="text" name="source_name" id="source_name" class="detail-value" value="${sourceName}" readonly>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section-card">
        <div class="detail-section-header">OTHER DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <textarea name="notes" id="notes" class="detail-value" style="min-height:50px; resize:vertical;">${p.notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    // Set form content - ensure it's visible
    if (formContent) {
      formContent.innerHTML = col1 + col2 + col3 + col4;
      formContent.style.display = 'grid';
      formContent.style.gridTemplateColumns = 'repeat(4, minmax(0, 1fr))';
      formContent.style.gap = '0';
      formContent.style.padding = '0';
    }
    
    // Middle Section: Schedule Details - Matching image layout
    const currentYear = p.start_date ? new Date(p.start_date).getFullYear() : new Date().getFullYear();
    const termNumber = p.term || '1';
    // Ensure term_unit is a string, not an object
    let termUnit = 'Year';
    if (p.term_unit) {
      if (typeof p.term_unit === 'string') {
        termUnit = p.term_unit;
      } else if (typeof p.term_unit === 'object' && p.term_unit.name) {
        termUnit = p.term_unit.name;
      } else {
        termUnit = String(p.term_unit);
      }
    }
    const payPlan = p.pay_plan_lookup_id || '';
    const frequency = p.frequency_id || '';
    const interval = lookupData.frequencies?.find(f => f.id == frequency)?.name || '';
    
    const scheduleCol1 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PLAN & VALUE DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Year</span>
            <select class="detail-value" disabled style="appearance:auto; -webkit-appearance:menulist;">
              <option ${p.start_date ? new Date(p.start_date).getFullYear() == currentYear ? 'selected' : '' : ''}>${currentYear}</option>
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Plan</span>
            <select name="policy_plan_id" id="policy_plan_id" class="detail-value">
              ${createSelectOptions(lookupData.policy_plans || [], p.policy_plan_id)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">Sum Insured</span>
            <input type="number" step="0.01" name="sum_insured" id="sum_insured" class="detail-value" value="${p.sum_insured || ''}">
          </div>
          <div class="detail-row">
            <span class="detail-label">Last Endorsement</span>
            <input type="text" name="last_endorsement" id="last_endorsement" class="detail-value" value="${p.last_endorsement || ''}">
          </div>
        </div>
      </div>
    `;

    const scheduleCol2 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PERIOD OF COVER</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Term</span>
            <div style="display:flex; gap:6px; align-items:center;">
              <input type="number" name="term" id="term" class="detail-value" value="${termNumber}" style="flex:0 0 50px;">
              <select name="term_unit" id="term_unit" class="detail-value" style="flex:1;">
                ${createSelectOptions(lookupData.term_units || [], termUnit)}
              </select>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Start Date</span>
            <input type="date" name="start_date" id="start_date" class="detail-value" value="${formatDateForInput(p.start_date)}" required>
          </div>
          <div class="detail-row">
            <span class="detail-label">End Date</span>
            <input type="date" name="end_date" id="end_date" class="detail-value" value="${formatDateForInput(p.end_date)}" required>
          </div>
          <div class="detail-row">
            <span class="detail-label">Cancelled Date</span>
            <input type="date" name="cancelled_date" id="cancelled_date" class="detail-value" value="${formatDateForInput(p.cancelled_date)}">
          </div>
        </div>
      </div>
    `;

    const scheduleCol3 = `
      <div class="detail-section-card">
        <div class="detail-section-header">ADD ONS (Motor)</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">WSC</span>
            <input type="number" step="0.01" name="wsc" id="wsc" class="detail-value" value="${p.wsc || '10000'}">
          </div>
          <div class="detail-row">
            <span class="detail-label">LOU</span>
            <input type="number" step="0.01" name="lou" id="lou" class="detail-value" value="${p.lou || '15000'}">
          </div>
          <div class="detail-row">
            <span class="detail-label">PA</span>
            <input type="number" step="0.01" name="pa" id="pa" class="detail-value" value="${p.pa || '250000'}">
          </div>
        </div>
      </div>
    `;

    const scheduleCol4 = `
      <div class="detail-section-card">
        <div class="detail-section-header">PREMIUM & PAYMENT PLAN</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Base Premium</span>
            <input type="number" step="0.01" name="base_premium" id="base_premium" class="detail-value" value="${p.base_premium || ''}">
          </div>
          <div class="detail-row">
            <span class="detail-label">Total Premium</span>
            <input type="number" step="0.01" name="premium" id="premium" class="detail-value" value="${p.premium || ''}">
          </div>
          <div class="detail-row">
            <span class="detail-label">Payment Plan</span>
            <select name="pay_plan_lookup_id" id="pay_plan_lookup_id" class="detail-value">
              ${createSelectOptions(lookupData.pay_plans || [], payPlan)}
            </select>
          </div>
          <div class="detail-row">
            <span class="detail-label">NOP & Interval</span>
            <div style="display:flex; gap:6px; align-items:center;">
              <input type="number" name="no_of_instalments" id="no_of_instalments" class="detail-value" value="${p.no_of_instalments || '2'}" style="flex:0 0 50px;">
              <select name="frequency_id" id="frequency_id" class="detail-value" style="flex:1;">
                ${createSelectOptions(lookupData.frequencies || [], frequency)}
              </select>
            </div>
          </div>
        </div>
      </div>
    `;

    // Set schedule content - ensure it's visible
    if (formScheduleContent) {
      formScheduleContent.innerHTML = scheduleCol1 + scheduleCol2 + scheduleCol3 + scheduleCol4;
      formScheduleContent.style.display = 'grid';
      formScheduleContent.style.gridTemplateColumns = 'repeat(4, minmax(0, 1fr))';
      formScheduleContent.style.gap = '0';
      formScheduleContent.style.padding = '0';
    }
    
    // Documents Section - Display existing documents if editing, or empty if adding
    if (formDocumentsContent) {
      formDocumentsContent.innerHTML = '';
      formDocumentsContent.style.display = 'flex';
      
      // If editing and policy has documents, display them
      if (p && p.documents && p.documents.length > 0) {
        p.documents.forEach(doc => {
          const docName = doc.name || doc.file_name || doc.type || 'Document';
          const isPDF = docName.toLowerCase().endsWith('.pdf') || doc.type === 'application/pdf';
          const iconColor = isPDF ? '#dc3545' : '#000';
          const fileIcon = isPDF ? 
            '<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' :
            '<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2V8H20" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
          const docDiv = document.createElement('div');
          docDiv.className = 'document-icon';
          docDiv.innerHTML = `
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              ${fileIcon}
            </svg>
            <span>${docName}</span>
          `;
          formDocumentsContent.appendChild(docDiv);
        });
      }
    }
    
    // Clear file input
    const fileInput = document.getElementById('documentUpload');
    if (fileInput) {
      fileInput.value = '';
    }
  }

  function closePolicyPageView(){
    const policyPageView = document.getElementById('policyPageView');
    policyPageView.classList.remove('show');
    policyPageView.classList.remove('form-mode');
    policyPageView.style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('policyDetailsPageContent').style.display = 'none';
    document.getElementById('policyDetailsContentWrapper').style.display = 'none';
    document.getElementById('policyScheduleContentWrapper').style.display = 'none';
    document.getElementById('documentsContentWrapper').style.display = 'none';
    document.getElementById('policyFormPageContent').style.display = 'none';
    currentPolicyId = null;
    currentPolicyData = null;
  }

  function closePolicyModal(){
    closePolicyPageView();
  }

  // Open document upload modal
  function openPolicyDocumentUploadModal() {
    if (!currentPolicyId) {
      alert('Please save the policy first before uploading documents');
      return;
    }
    const modal = document.getElementById('policyDocumentUploadModal');
    if (modal) {
      modal.classList.add('show');
      modal.style.display = 'flex';
    }
  }

  // Close document upload modal
  function closePolicyDocumentUploadModal() {
    const modal = document.getElementById('policyDocumentUploadModal');
    if (modal) {
      modal.classList.remove('show');
      modal.style.display = 'none';
    }
    // Reset form
    const form = document.getElementById('policyDocumentUploadForm');
    if (form) form.reset();
    const previewContainer = document.getElementById('policyDocumentPreviewContainer');
    if (previewContainer) previewContainer.style.display = 'none';
    const previewContent = document.getElementById('policyDocumentPreviewContent');
    if (previewContent) previewContent.innerHTML = '';
    const previewInfo = document.getElementById('policyDocumentPreviewInfo');
    if (previewInfo) previewInfo.innerHTML = '';
  }

  // Preview document before upload
  function previewPolicyDocument(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('policyDocumentPreviewContainer');
    const previewContent = document.getElementById('policyDocumentPreviewContent');
    const previewInfo = document.getElementById('policyDocumentPreviewInfo');

    if (!file || !previewContainer || !previewContent || !previewInfo) return;

    previewContainer.style.display = 'block';
    previewContent.innerHTML = '';
    previewInfo.innerHTML = '';

    const fileType = file.type;
    const fileName = file.name;
    const fileSize = (file.size / 1024 / 1024).toFixed(2); // Size in MB

    // Show file info
    previewInfo.innerHTML = `<strong>File:</strong> ${fileName}<br><strong>Size:</strong> ${fileSize} MB<br><strong>Type:</strong> ${fileType || 'Unknown'}`;

    // Preview based on file type
    if (fileType.startsWith('image/')) {
      // Image preview
      const reader = new FileReader();
      reader.onload = function(e) {
        previewContent.innerHTML = `<img src="${e.target.result}" alt="Document Preview" style="max-width:100%; max-height:400px; border:1px solid #ddd; border-radius:4px;">`;
      };
      reader.readAsDataURL(file);
    } else if (fileType === 'application/pdf') {
      // PDF preview using embed
      const reader = new FileReader();
      reader.onload = function(e) {
        previewContent.innerHTML = `
          <div style="width:100%; text-align:center;">
            <embed src="${e.target.result}" type="application/pdf" width="100%" height="400px" style="border:1px solid #ddd; border-radius:4px;">
            <div style="margin-top:10px; color:#666; font-size:12px;">PDF Preview (scroll to view full document)</div>
          </div>
        `;
      };
      reader.readAsDataURL(file);
    } else {
      // For other file types (DOC, DOCX), show icon
      const fileExt = fileName.split('.').pop().toUpperCase();
      previewContent.innerHTML = `
        <div class="document-item" style="margin:0 auto;">
          <div class="document-icon" style="width:120px; height:120px; font-size:24px;">${fileExt}</div>
          <div style="font-size:12px; text-align:center; margin-top:10px; color:#666;">${fileName}</div>
        </div>
      `;
    }
  }

  // Document upload handler
  async function handlePolicyDocumentUpload() {
    const documentType = document.getElementById('policyDocumentType').value;
    const documentFile = document.getElementById('policyDocumentFile').files[0];

    if (!documentType) {
      alert('Please select a document type');
      return;
    }

    if (!documentFile) {
      alert('Please select a file');
      return;
    }

    if (!currentPolicyId) {
      alert('No policy selected');
      return;
    }

    const formData = new FormData();
    formData.append('document', documentFile);
    formData.append('document_type', documentType);

    try {
      const response = await fetch(`/policies/${currentPolicyId}/upload-document`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || csrfToken,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const result = await response.json();
      
      if (result.success) {
        // Update documents display from result
        if (result.policy && result.policy.documents) {
          updatePolicyDocumentsList(result.policy);
        } else if (result.documents) {
          // Fallback: use documents directly
          const policy = { documents: result.documents };
          updatePolicyDocumentsList(policy);
        } else {
          // Reload policy data to update documents
          const policyRes = await fetch(`/policies/${currentPolicyId}`, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          const policyData = await policyRes.json();
          const policy = policyData.policy || policyData;
          updatePolicyDocumentsList(policy);
        }
        
        closePolicyDocumentUploadModal();
        alert('Document uploaded successfully!');
      } else {
        alert('Error uploading document: ' + (result.message || 'Unknown error'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error uploading document: ' + error.message);
    }
  }

  // Renewal Schedule Modal Functions
  function openRenewalModal() {
    if (!currentPolicyId) {
      alert('No policy selected');
      return;
    }
    
    const modal = document.getElementById('renewalScheduleModal');
    if (modal) {
      // Populate form with current policy data if available
      if (currentPolicyData) {
        const policy = currentPolicyData;
        const startDate = policy.start_date ? new Date(policy.start_date) : new Date();
        const endDate = policy.end_date ? new Date(policy.end_date) : null;
        const nextYear = startDate.getFullYear() + 1;
        
        document.getElementById('renewal_year').value = nextYear;
        document.getElementById('renewal_policy_plan').value = policy.policy_plan_name || (policy.policyPlan ? policy.policyPlan.name : '');
        document.getElementById('renewal_sum_insured').value = policy.sum_insured || '';
        document.getElementById('renewal_term').value = policy.term || '';
        document.getElementById('renewal_term_unit').value = typeof policy.term_unit === 'string' ? policy.term_unit : (policy.term_unit && policy.term_unit.name ? policy.term_unit.name : 'Year');
        
        // Calculate next renewal dates
        const nextStartDate = new Date(startDate);
        nextStartDate.setFullYear(nextStartDate.getFullYear() + 1);
        const nextEndDate = endDate ? new Date(endDate) : new Date(nextStartDate);
        if (endDate) {
          nextEndDate.setFullYear(nextEndDate.getFullYear() + 1);
        } else {
          // Default to 1 year from start
          nextEndDate.setFullYear(nextStartDate.getFullYear() + 1);
          nextEndDate.setMonth(nextStartDate.getMonth());
          nextEndDate.setDate(nextStartDate.getDate() - 1);
        }
        
        document.getElementById('renewal_start_date').value = nextStartDate.toISOString().split('T')[0];
        document.getElementById('renewal_end_date').value = nextEndDate.toISOString().split('T')[0];
        
        document.getElementById('renewal_add_ons').value = '';
        document.getElementById('renewal_base_premium').value = policy.base_premium || '';
        document.getElementById('renewal_full_premium').value = policy.premium || '';
        document.getElementById('renewal_pay_plan_type').value = policy.pay_plan_name || (policy.payPlan ? policy.payPlan.name : '');
        
        // Get NOP and frequency
        let nop = policy.no_of_instalments || '';
        let frequency = policy.payment_plan_frequency || policy.frequency_name || '';
        if (!frequency && policy.frequency) {
          frequency = typeof policy.frequency === 'string' ? policy.frequency : (policy.frequency.name || '');
        }
        document.getElementById('renewal_nop').value = nop;
        document.getElementById('renewal_frequency').value = frequency;
        document.getElementById('renewal_note').value = '';
      }
      
      modal.style.display = 'flex';
    }
  }

  function closeRenewalModal() {
    const modal = document.getElementById('renewalScheduleModal');
    if (modal) {
      modal.style.display = 'none';
      // Reset form
      const form = document.getElementById('renewalScheduleForm');
      if (form) form.reset();
    }
  }

  function handleRenewalDocumentUpload() {
    // This will open the document upload modal for renewal documents
    const documentModal = document.getElementById('policyDocumentUploadModal');
    if (documentModal) {
      documentModal.style.display = 'flex';
    }
  }

  // Handle renewal form submission
  document.addEventListener('DOMContentLoaded', function() {
    const renewalForm = document.getElementById('renewalScheduleForm');
    if (renewalForm) {
      renewalForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!currentPolicyId) {
          alert('No policy selected');
          return;
        }
        
        const formData = new FormData(renewalForm);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
        
        // Disable submit button to prevent double submission
        const submitBtn = renewalForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
        
        try {
          const response = await fetch(`/policies/${currentPolicyId}/renewal-schedule`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            },
            body: formData
          });
          
          const result = await response.json();
          
          if (result.success) {
            alert('Renewal schedule created successfully!');
            closeRenewalModal();
            
            // Optionally reload policy details to show the new schedule
            if (currentPolicyId) {
              // You can reload the policy details here if needed
              // openPolicyDetails(currentPolicyId);
            }
          } else {
            let errorMessage = result.message || 'Error creating renewal schedule';
            if (result.errors) {
              const errorList = Object.values(result.errors).flat().join('\n');
              errorMessage += '\n' + errorList;
            }
            alert(errorMessage);
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Error creating renewal schedule: ' + error.message);
        } finally {
          // Re-enable submit button
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      });
    }
    
    // Auto-calculate end date when start date changes
    const renewalStartDate = document.getElementById('renewal_start_date');
    const renewalEndDate = document.getElementById('renewal_end_date');
    const renewalTerm = document.getElementById('renewal_term');
    const renewalTermUnit = document.getElementById('renewal_term_unit');
    
    if (renewalStartDate && renewalEndDate && renewalTerm && renewalTermUnit) {
      function calculateEndDate() {
        const startDate = renewalStartDate.value;
        const term = parseFloat(renewalTerm.value) || 0;
        const termUnit = renewalTermUnit.value.toLowerCase();
        
        if (startDate && term > 0) {
          const start = new Date(startDate);
          let end = new Date(start);
          
          if (termUnit.includes('year')) {
            end.setFullYear(end.getFullYear() + term);
            end.setDate(end.getDate() - 1); // Subtract 1 day
          } else if (termUnit.includes('month')) {
            end.setMonth(end.getMonth() + term);
            end.setDate(end.getDate() - 1);
          } else if (termUnit.includes('day')) {
            end.setDate(end.getDate() + term - 1);
          }
          
          renewalEndDate.value = end.toISOString().split('T')[0];
        }
      }
      
      renewalStartDate.addEventListener('change', calculateEndDate);
      renewalTerm.addEventListener('input', calculateEndDate);
      renewalTermUnit.addEventListener('change', calculateEndDate);
    }
  });

  // Update documents list
  function updatePolicyDocumentsList(policy) {
    // Update documents in form view
    const formDocumentsContent = document.getElementById('policyFormDocumentsContent');
    // Update documents in detail view
    const detailDocumentsContent = document.getElementById('documentsContent');
    
    let docsHTML = '';
    
    // Load documents from database
    if (policy.documents && policy.documents.length > 0) {
      policy.documents.forEach(doc => {
        if (doc.file_path) {
          const fileExt = doc.format ? doc.format.toUpperCase() : (doc.file_path.split('.').pop().toUpperCase());
          const fileUrl = doc.file_path.startsWith('http') ? doc.file_path : `/storage/${doc.file_path}`;
          const isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
          const docName = doc.name || 'Document';
          docsHTML += `
            <div class="document-item" style="cursor:pointer;" onclick="previewUploadedPolicyDocument('${fileUrl}', '${fileExt}', '${docName}')">
              ${isImage ? `<img src="${fileUrl}" alt="${docName}" style="width:60px; height:60px; object-fit:cover; border-radius:4px;">` : `<div class="document-icon">${fileExt}</div>`}
              <div style="font-size:11px; text-align:center;">${docName}</div>
            </div>
          `;
        }
      });
    }
    
    const noDocsHTML = '<div style="color:#999; font-size:12px;">No documents uploaded</div>';
    
    if (formDocumentsContent) {
      formDocumentsContent.innerHTML = docsHTML || noDocsHTML;
    }
    if (detailDocumentsContent) {
      detailDocumentsContent.innerHTML = docsHTML || noDocsHTML;
    }
  }

  // Preview uploaded document
  function previewUploadedPolicyDocument(fileUrl, fileExt, docName) {
    const isImage = ['JPG', 'JPEG', 'PNG'].includes(fileExt);
    const isPDF = fileExt === 'PDF';
    
    if (isImage) {
      window.open(fileUrl, '_blank');
    } else if (isPDF) {
      window.open(fileUrl, '_blank');
    } else {
      window.open(fileUrl, '_blank');
    }
  }

  // Vehicle Dialog Functions
  function openVehicleDialog() {
    const modal = document.getElementById('vehicleModal');
    if (modal) {
      modal.style.display = 'flex';
      modal.classList.add('show');
      // Get current policy ID if available
      const policyId = currentPolicyId || document.getElementById('policy_id')?.value;
      if (policyId) {
        document.getElementById('vehicleForm').dataset.policyId = policyId;
      }
    }
  }

  function closeVehicleDialog() {
    const modal = document.getElementById('vehicleModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.remove('show');
      document.getElementById('vehicleForm').reset();
    }
  }

  async function saveVehicle(addAnother = false) {
    const form = document.getElementById('vehicleForm');
    const formData = new FormData(form);
    
    // Add policy_id if available
    const policyId = currentPolicyId || form.dataset.policyId || document.getElementById('policy_id')?.value;
    if (policyId) {
      formData.append('policy_id', policyId);
    }

    try {
      const response = await fetch(vehiclesStoreRoute, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const data = await response.json();

      if (response.ok && data.success) {
        alert('Vehicle saved successfully!');
        if (!addAnother) {
          closeVehicleDialog();
        } else {
          form.reset();
        }
      } else {
        alert('Error: ' + (data.message || 'Failed to save vehicle'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error saving vehicle: ' + error.message);
    }
  }

  function saveVehicleAndAddAnother() {
    saveVehicle(true);
  }

  // Nominee Dialog Functions
  function openNomineeDialog() {
    const modal = document.getElementById('nomineeModal');
    if (modal) {
      modal.style.display = 'flex';
      modal.classList.add('show');
      // Get current policy ID if available
      const policyId = currentPolicyId || document.getElementById('policy_id')?.value;
      if (policyId) {
        document.getElementById('nominee_policy_id').value = policyId;
        document.getElementById('nomineeForm').dataset.policyId = policyId;
      }
    }
  }

  function closeNomineeDialog() {
    const modal = document.getElementById('nomineeModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.remove('show');
      document.getElementById('nomineeForm').reset();
    }
  }

  async function saveNominee(addAnother = false) {
    const form = document.getElementById('nomineeForm');
    const formData = new FormData(form);
    
    // Add policy_id if available
    const policyId = currentPolicyId || form.dataset.policyId || document.getElementById('policy_id')?.value;
    if (policyId) {
      formData.append('policy_id', policyId);
    }

    // Add client_id if available
    const clientId = document.getElementById('client_id')?.value;
    if (clientId) {
      formData.append('client_id', clientId);
    }

    try {
      const response = await fetch(nomineesStoreRoute, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      const data = await response.json();

      if (response.ok && data.success) {
        if (!addAnother) {
          closeNomineeDialog();
          window.location.href = `/nominees`;

          // // Redirect to nominees page only if policy_id exists
          // if (policyId) {
          //   window.location.href = `/nominees?policy_id=${policyId}`;
          // } else {
          //   alert('Nominee saved successfully! Note: Please save the policy first to link this nominee to the policy.');
          // }
        } else {
          form.reset();
          if (policyId) {
            document.getElementById('nominee_policy_id').value = policyId;
          }
        }
      } else {
        // Handle validation errors
        if (data.errors) {
          const errorMessages = Object.values(data.errors).flat().join('\n');
          alert('Validation errors:\n' + errorMessages);
        } else {
          alert('Error: ' + (data.message || 'Failed to save nominee'));
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
  
