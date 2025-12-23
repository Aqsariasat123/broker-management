
  // Data initialized in Blade template

  // Format number input with commas
  function formatNumberInput(input) {
    if (!input.value) return;
    let value = input.value.replace(/,/g, '');
    input.value = parseFloat(value).toLocaleString('en-US');
  }

  // Calculate age from DOB
  function calculateAge(dob) {
    if (!dob) return null;
    const today = new Date();
    const birthDate = new Date(dob);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  }

  // Calculate ANB (Age Next Birthday)
  function calculateANB(age) {
    if (!age) return null;
    return parseInt(age) + 1;
  }

  // Calculate total rider premium
  function calculateTotalRiderPremium() {
    let total = 0;
    document.querySelectorAll('.rider-premium:not([disabled])').forEach(input => {
      const value = parseFloat(input.value) || 0;
      total += value;
    });
    document.getElementById('total_rider_premium').value = total.toFixed(2);
    calculateTotalPremium();
  }

  // Calculate total premium
  function calculateTotalPremium() {
    const basePremium = parseFloat(document.getElementById('base_premium')?.value || 0);
    const adminFee = parseFloat(document.getElementById('admin_fee')?.value || 0);
    const totalRider = parseFloat(document.getElementById('total_rider_premium')?.value || 0);
    const total = basePremium + adminFee + totalRider;
    document.getElementById('total_premium').value = total.toFixed(2);
    document.getElementById('premium').value = total.toFixed(2);
  }

  // Toggle medical fields
  function toggleMedicalFields() {
    const formContentDiv = document.getElementById('proposalPageFormContent');
    const searchScope = formContentDiv || document;
    const checkbox = searchScope.querySelector('#medical_examination_required');
    const fields = searchScope.querySelector('#medicalFields');
    if (checkbox && fields) {
      if (checkbox.checked) {
        fields.style.display = 'block';
        fields.querySelectorAll('input, select, textarea').forEach(field => {
          field.required = true;
        });
      } else {
        fields.style.display = 'none';
        fields.querySelectorAll('input, select, textarea').forEach(field => {
          field.required = false;
          field.value = '';
        });
      }
    }
  }

  // Toggle rider premium input
  function toggleRiderPremium(checkbox) {
    const riderName = checkbox.dataset.rider;
    const premiumInput = document.getElementById('rider_premium_' + riderName);
    if (checkbox.checked) {
      premiumInput.disabled = false;
      premiumInput.required = true;
    } else {
      premiumInput.disabled = true;
      premiumInput.required = false;
      premiumInput.value = '';
      calculateTotalRiderPremium();
    }
  }

  // Calculate maturity date from start date and term
  function calculateMaturityDate() {
    const startDate = document.getElementById('start_date')?.value;
    const term = parseInt(document.getElementById('term')?.value || 0);
    if (startDate && term) {
      const start = new Date(startDate);
      start.setFullYear(start.getFullYear() + term);
      document.getElementById('maturity_date').value = start.toISOString().split('T')[0];
    }
  }

  // Format date helper function
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
  }

  // Format number helper function
  function formatNumber(num) {
    if (!num && num !== 0) return '-';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Open proposal details (full page view) - MUST be defined before HTML onclick handlers
  async function openProposalDetails(id) {
    try {
      const res = await fetch(`/life-proposals/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const proposal = await res.json();
      currentProposalId = id;
      
      // Get all required elements
      const proposalPageName = document.getElementById('proposalPageName');
      const proposalPageTitle = document.getElementById('proposalPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const proposalPageView = document.getElementById('proposalPageView');
      const proposalDetailsPageContent = document.getElementById('proposalDetailsPageContent');
      const proposalFormPageContent = document.getElementById('proposalFormPageContent');
      const editProposalFromPageBtn = document.getElementById('editProposalFromPageBtn');
      const closeProposalPageBtn = document.getElementById('closeProposalPageBtn');
      
      if (!proposalPageName || !proposalPageTitle || !clientsTableView || !proposalPageView || 
          !proposalDetailsPageContent || !proposalFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set proposal name in header
      const proposalName = proposal.proposers_name || proposal.prid || 'Unknown';
      proposalPageName.textContent = proposalName;
      proposalPageTitle.textContent = 'Life Proposal';
      
      populateProposalDetails(proposal);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      proposalPageView.style.display = 'block';
      proposalPageView.classList.add('show');
      proposalDetailsPageContent.style.display = 'block';
      proposalFormPageContent.style.display = 'none';
      
      // Show all buttons in detail view - Generate Policy, Update, and Close
      const generateBtn = document.getElementById('generatePolicyBtn');
      if (generateBtn) generateBtn.style.display = 'inline-block';
      if (editProposalFromPageBtn) editProposalFromPageBtn.style.display = 'inline-block';
      if (closeProposalPageBtn) closeProposalPageBtn.style.display = 'inline-block';
      
      // Show navigation tabs
      const navTabs = document.getElementById('proposalNavTabs');
      if (navTabs) navTabs.style.display = 'flex';
      
      // Store PRID for document loading
      if (proposalPageView) {
        proposalPageView.setAttribute('data-prid', proposal.prid);
      }
      
      // Load documents
      loadProposalDocuments(proposal.prid);
    } catch (e) {
      console.error(e);
      alert('Error loading proposal details: ' + e.message);
    }
  }

  // Populate proposal details view
  function populateProposalDetails(proposal) {
    const content = document.getElementById('proposalDetailsContent');
    if (!content) return;

    // PROPOSED PLAN (Column 1)
    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">PROPOSED PLAN</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Insurer</span>
            <div class="detail-value">${proposal.insurer || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Plan</span>
            <div class="detail-value">${proposal.policy_plan || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Term</span>
            <div class="detail-value">${proposal.term || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Sum Assured</span>
            <div class="detail-value">${formatNumber(proposal.sum_assured)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Add Ons</span>
            <div class="detail-value">${proposal.add_ons || '-'}</div>
          </div>
        </div>
      </div>
    `;

    // PROVISIONAL PAYMENT PLAN (Column 2)
    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">PROVISIONAL PAYMENT PLAN</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Pay Plan</span>
            <div class="detail-value">${proposal.frequency || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Base Premium</span>
            <div class="detail-value">${formatNumber(proposal.base_premium || 0)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Total Premium</span>
            <div class="detail-value">${formatNumber(proposal.premium || 0)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Payment Method</span>
            <div class="detail-value">${proposal.method_of_payment || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Premium Source</span>
            <div class="detail-value">${proposal.source_of_payment || '-'}</div>
          </div>
        </div>
      </div>
    `;

    // PROPOSAL STATUS (Column 3)
    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">PROPOSAL STATUS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Offer Date</span>
            <div class="detail-value">${formatDate(proposal.offer_date)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Proposal Stage</span>
            <div class="detail-value">${proposal.stage || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Submitted Date</span>
            <div class="detail-value">${proposal.is_submitted && proposal.date ? formatDate(proposal.date) : '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Application Status</span>
            <div class="detail-value">${proposal.status || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Close Date</span>
            <div class="detail-value">${proposal.maturity_date ? formatDate(proposal.maturity_date) : '-'}</div>
          </div>
        </div>
      </div>
    `;

    // MEDICAL DETAILS (Column 4)
    const medicalRequired = proposal.medical_examination_required || false;
    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header">MEDICAL DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Medical Required</span>
            <div class="detail-value">
              <input type="checkbox" ${medicalRequired ? 'checked' : ''} disabled>
            </div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Clinic</span>
            <div class="detail-value">${proposal.clinic || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Referred Date</span>
            <div class="detail-value">${formatDate(proposal.date_referred)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Completion Date</span>
            <div class="detail-value">${formatDate(proposal.date_completed)}</div>
          </div>
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Exam Notes</span>
            <div class="detail-value">
              <textarea readonly style="width:100%; min-height:40px; padding:3px 6px; font-size:11px; border:1px solid #ddd; border-radius:2px; resize:vertical;">${proposal.exam_notes || ''}</textarea>
            </div>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open proposal page (Add or Edit)
  async function openProposalPage(mode) {
    if (mode === 'add') {
      openProposalForm('add');
    } else {
      if (currentProposalId) {
        openEditProposal(currentProposalId);
      }
    }
  }

  // Event listeners
  document.addEventListener('DOMContentLoaded', function() {
    // Add Proposal Button
    const addBtn = document.getElementById('addProposalBtn');
    if (addBtn) {
      addBtn.addEventListener('click', () => openProposalPage('add'));
    }

    // Column button
    const columnBtn = document.getElementById('columnBtn');
    if (columnBtn) {
      columnBtn.addEventListener('click', () => openColumnModal());
    }

    // Filter toggle handler - updates button colors and clears filters when unchecked
    const filterToggle = document.getElementById('filterToggle');
    const followUpBtn = document.getElementById('followUpBtn');
    const submittedBtn = document.getElementById('submittedBtn');

    function updateButtonColors() {
      const urlParams = new URLSearchParams(window.location.search);
      const hasFollowUp = urlParams.get('follow_up') === 'true' || urlParams.get('follow_up') === '1';
      const hasSubmitted = urlParams.get('submitted') === 'true' || urlParams.get('submitted') === '1';

      if (followUpBtn) {
        if (hasFollowUp) {
          followUpBtn.classList.add('filter-active');
          followUpBtn.classList.remove('inactive');
        } else {
          followUpBtn.classList.remove('filter-active');
        }
      }
      if (submittedBtn) {
        if (hasSubmitted) {
          submittedBtn.classList.add('filter-active');
        } else {
          submittedBtn.classList.remove('filter-active');
        }
      }
    }
    
    if (filterToggle) {
      const urlParams = new URLSearchParams(window.location.search);
      const hasFollowUp = urlParams.get('follow_up') === 'true' || urlParams.get('follow_up') === '1';
      const hasSubmitted = urlParams.get('submitted') === 'true' || urlParams.get('submitted') === '1';
      filterToggle.checked = hasFollowUp || hasSubmitted;
      
      // Update button colors on page load
      updateButtonColors();
      
      filterToggle.addEventListener('change', function(e) {
        e.preventDefault();
        e.stopPropagation();
        updateButtonColors();
        if (!this.checked) {
          // Clear all filters when toggle is unchecked
          const u = new URL(window.location.href);
          u.searchParams.delete('follow_up');
          u.searchParams.delete('submitted');
          window.location.href = u.toString();
        } else {
          // If checked but no filters active, activate "To Follow Up" by default
          if (!hasFollowUp && !hasSubmitted) {
            const u = new URL(window.location.href);
            u.searchParams.set('follow_up', '1');
            window.location.href = u.toString();
          }
        }
      });
    }

    // To Follow Up button handler
    if (followUpBtn) {
      followUpBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const u = new URL(window.location.href);
        const currentFollowUp = u.searchParams.get('follow_up');
        if (currentFollowUp === 'true' || currentFollowUp === '1') {
          // Deactivate filter
          u.searchParams.delete('follow_up');
        } else {
          // Activate filter
          u.searchParams.set('follow_up', '1');
          u.searchParams.delete('submitted');
        }
        // Ensure filter toggle is checked when activating filter
        if (filterToggle) {
          filterToggle.checked = true;
          updateButtonColors();
        }
        window.location.href = u.toString();
      });
    }

    // Submitted button handler
    if (submittedBtn) {
      submittedBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const u = new URL(window.location.href);
        const currentSubmitted = u.searchParams.get('submitted');
        if (currentSubmitted === 'true' || currentSubmitted === '1') {
          // Deactivate filter
          u.searchParams.delete('submitted');
        } else {
          // Activate filter
          u.searchParams.set('submitted', '1');
          u.searchParams.delete('follow_up');
        }
        // Ensure filter toggle is checked when activating filter
        if (filterToggle) {
          filterToggle.checked = true;
          updateButtonColors();
        }
        window.location.href = u.toString();
      });
    }

    // Radio button click handler - update visual dot
    document.querySelectorAll('.action-radio').forEach(radio => {
      radio.addEventListener('change', function() {
        // Update all radio dots
        document.querySelectorAll('.action-radio').forEach(r => {
          const dot = r.nextElementSibling;
          if (dot && dot.classList.contains('radio-dot')) {
            const dotColor = r.dataset.dotColor || 'transparent';
            if (r.checked) {
              dot.style.backgroundColor = dotColor !== 'transparent' ? dotColor : 'transparent';
            } else {
              dot.style.backgroundColor = 'transparent';
            }
          }
        });
      });
    });

    // Contact selection handler removed - Proposer's Name is now a text input

    // DOB change handler
    const dobInput = document.getElementById('dob');
    if (dobInput) {
      dobInput.addEventListener('change', function() {
        const age = calculateAge(this.value);
        if (age !== null) {
          document.getElementById('age').value = age;
          document.getElementById('anb').value = calculateANB(age);
        }
      });
    }

    // Rider checkbox handlers
    document.querySelectorAll('.rider-checkbox[data-rider]').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        toggleRiderPremium(this);
        if (this.checked) {
          calculateTotalRiderPremium();
        }
      });
    });

    // Rider premium input handlers
    document.querySelectorAll('.rider-premium').forEach(input => {
      input.addEventListener('input', calculateTotalRiderPremium);
    });

    // Client selection handler
    const clientSelect = document.getElementById('client_id');
    if (clientSelect) {
      clientSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
          document.getElementById('source_name').value = option.text;
        }
      });
    }

    // Term and start date handlers for maturity date
    const termInput = document.getElementById('term');
    const startDateInput = document.getElementById('start_date');
    if (termInput) termInput.addEventListener('change', calculateMaturityDate);
    if (startDateInput) startDateInput.addEventListener('change', calculateMaturityDate);
  });

  async function openEditProposal(id) {
    try {
      const res = await fetch(`/life-proposals/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const proposal = await res.json();
      currentProposalId = id;
      openProposalForm('edit', proposal);
    } catch (e) {
      console.error(e);
      alert('Error loading proposal data');
    }
  }

  // Reattach event listeners after cloning form
  function reattachFormEventListeners() {
    const formContentDiv = document.getElementById('proposalPageFormContent');
    if (!formContentDiv) return;

    // Contact selection handler removed - Proposer's Name is now a text input

    // DOB change handler
    const dobInput = formContentDiv.querySelector('#dob');
    if (dobInput) {
      dobInput.addEventListener('change', function() {
        const age = calculateAge(this.value);
        if (age !== null) {
          const ageEl = formContentDiv.querySelector('#age');
          const anbEl = formContentDiv.querySelector('#anb');
          if (ageEl) ageEl.value = age;
          if (anbEl) anbEl.value = calculateANB(age);
        }
      });
    }

    // Rider checkbox handlers
    formContentDiv.querySelectorAll('.rider-checkbox[data-rider]').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        toggleRiderPremium(this);
        if (this.checked) {
          calculateTotalRiderPremium();
        }
      });
    });

    // Rider premium input handlers
    formContentDiv.querySelectorAll('.rider-premium').forEach(input => {
      input.addEventListener('input', calculateTotalRiderPremium);
    });

    // Client selection handler
    const clientSelect = formContentDiv.querySelector('#client_id');
    if (clientSelect) {
      clientSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
          const sourceNameEl = formContentDiv.querySelector('#source_name');
          if (sourceNameEl) sourceNameEl.value = option.text;
        }
      });
    }

    // Term and start date handlers for maturity date
    const termInput = formContentDiv.querySelector('#term');
    const startDateInput = formContentDiv.querySelector('#start_date');
    if (termInput) termInput.addEventListener('change', calculateMaturityDate);
    if (startDateInput) startDateInput.addEventListener('change', calculateMaturityDate);

    // Medical examination checkbox
    const medicalCheckbox = formContentDiv.querySelector('#medical_examination_required');
    if (medicalCheckbox) {
      medicalCheckbox.addEventListener('change', toggleMedicalFields);
    }

    // Premium calculation inputs
    const basePremiumInput = formContentDiv.querySelector('#base_premium');
    const adminFeeInput = formContentDiv.querySelector('#admin_fee');
    const annualPremiumInput = formContentDiv.querySelector('#annual_premium');
    if (basePremiumInput) basePremiumInput.addEventListener('input', calculateTotalPremium);
    if (adminFeeInput) adminFeeInput.addEventListener('input', calculateTotalPremium);
    if (annualPremiumInput) annualPremiumInput.addEventListener('input', calculateTotalPremium);
  }

  function openProposalForm(mode, proposal = null) {
    // Clone form from modal
    const modalForm = document.getElementById('proposalModal').querySelector('form');
    const pageForm = document.getElementById('proposalPageForm');
    const formContentDiv = document.getElementById('proposalPageFormContent');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
      // Reattach event listeners after cloning
      reattachFormEventListeners();
    }

    const formMethod = document.getElementById('proposalPageFormMethod');
    const deleteBtn = document.getElementById('proposalDeleteBtn');
    const editBtn = document.getElementById('editProposalFromPageBtn');
    const closeBtn = document.getElementById('closeProposalPageBtn');
    const closeFormBtn = document.getElementById('closeProposalFormBtn');

    if (mode === 'add') {
      document.getElementById('proposalPageTitle').textContent = 'Add Life Proposal';
      document.getElementById('proposalPageName').textContent = '';
      pageForm.action = lifeProposalsStoreRoute;
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'none';
      if (closeFormBtn) closeFormBtn.style.display = 'inline-block';
      pageForm.reset();
    } else {
      // EDIT MODE
      const proposalName = proposal.proposers_name || proposal.prid || 'Unknown';
      document.getElementById('proposalPageTitle').textContent = 'Edit Life Proposal';
      document.getElementById('proposalPageName').textContent = proposalName;
      pageForm.action = `/life-proposals/${currentProposalId}`;

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

      // === 1. Populate simple input/text/date/number fields ===
      const simpleFields = [
        'proposers_name', 'sum_assured', 'term', 'add_ons', 'offer_date',
        'date', 'age', 'source_of_payment', 'mcr', 'doctor', 'date_sent',
        'date_completed', 'notes', 'agency', 'class', 'prid', 'policy_no',
        'loading_premium', 'start_date', 'maturity_date', 'base_premium',
        'admin_fee', 'annual_premium', 'exam_notes', 'date_referred'
      ];

      simpleFields.forEach(key => {
        const el = formContentDiv.querySelector(`#${key}`);
        if (!el) return;

        if (el.type === 'checkbox') {
          el.checked = !!proposal[key];
        } else if (el.type === 'date') {
          el.value = proposal[key] ? proposal[key].substring(0, 10) : '';
        } else {
          el.value = proposal[key] ?? '';
        }
      });

      // === 2. Rebuild and populate ALL <select> dropdowns using lookupData ===
      const selectMappings = {
        salutation: 'salutations',
        insurer: 'insurers',
        policy_plan: 'policy_plans',
        stage: 'stages',
        frequency: 'frequencies',
        status: 'statuses',
        source_of_payment: 'sources_of_payment',
        source: 'sources',
        agency: 'agencies',
        sex: 'sex_options',
        clinic: 'clinics',
        method_of_payment: 'method_of_payment_options'
      };

      Object.keys(selectMappings).forEach(fieldId => {
        const selectEl = formContentDiv.querySelector(`#${fieldId}`);
        if (!selectEl) return;

        const optionsArray = lookupData[selectMappings[fieldId]] || [];
        const savedValue = proposal[fieldId] || '';

        // Clear and rebuild options
        selectEl.innerHTML = '<option value="">Select</option>';
        optionsArray.forEach(opt => {
          const option = document.createElement('option');
          option.value = opt;
          option.textContent = opt;
          if (opt === savedValue) option.selected = true;
          selectEl.appendChild(option);
        });

        // Fallback: force value if exact match not found
        if (savedValue && selectEl.value !== savedValue) {
          selectEl.value = savedValue;
        }
      });

      // === 3. Special: Client select (uses ID, shows name) ===
      const clientSelect = formContentDiv.querySelector('#client_id');
      const sourceNameInput = formContentDiv.querySelector('#source_name');
      if (clientSelect && lookupData.clients) {
        clientSelect.innerHTML = '<option value="">Select Client</option>';
        lookupData.clients.forEach(client => {
          const option = document.createElement('option');
          option.value = client.id;
          option.textContent = client.client_name;
          if (String(client.id) === String(proposal.client_id)) {
            option.selected = true;
          }
          clientSelect.appendChild(option);
        });
      }
      if (sourceNameInput && proposal.source_name) {
        sourceNameInput.value = proposal.source_name;
      }

      // === 4. Riders: checkboxes + premium inputs ===
      if (proposal.riders && Array.isArray(proposal.riders)) {
        proposal.riders.forEach(rider => {
          const checkbox = formContentDiv.querySelector(`#rider_${rider}`);
          const premiumInput = formContentDiv.querySelector(`#rider_premium_${rider}`);
          if (checkbox) {
            checkbox.checked = true;
            if (premiumInput) {
              premiumInput.disabled = false;
              premiumInput.value = proposal.rider_premiums?.[rider] || '';
            }
          }
        });
      }

      // === 5. Medical Examination Required ===
      const medicalCheckbox = formContentDiv.querySelector('#medical_examination_required');
      if (medicalCheckbox) {
        medicalCheckbox.checked = !!proposal.medical_examination_required;
        toggleMedicalFields(); // This will show/hide and set required correctly
      }

      // === 6. Is Submitted checkbox ===
      const isSubmittedCheckbox = formContentDiv.querySelector('#is_submitted');
      if (isSubmittedCheckbox) {
        isSubmittedCheckbox.checked = !!proposal.is_submitted;
        isSubmittedCheckbox.value = proposal.is_submitted ? '1' : '0';
      }

      // === 7. Trigger calculations ===
      calculateTotalRiderPremium();
      calculateTotalPremium();
      calculateMaturityDate();
    }
    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const proposalPageView = document.getElementById('proposalPageView');
    proposalPageView.style.display = 'block';
    proposalPageView.classList.add('show');
    document.getElementById('proposalDetailsPageContent').style.display = 'none';
    document.getElementById('proposalFormPageContent').style.display = 'block';
    
    // Hide navigation tabs when in edit/add mode
    const navTabs = document.getElementById('proposalNavTabs');
    if (navTabs) navTabs.style.display = 'none';
  }

  function closeProposalPageView() {
    const proposalPageView = document.getElementById('proposalPageView');
    proposalPageView.classList.remove('show');
    proposalPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('proposalDetailsPageContent').style.display = 'none';
    document.getElementById('proposalFormPageContent').style.display = 'none';
    const navTabs = document.getElementById('proposalNavTabs');
    if (navTabs) navTabs.style.display = 'none';
    currentProposalId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editProposalFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentProposalId) {
        openEditProposal(currentProposalId);
      }
    });
  }

  // Legacy function for backward compatibility
  function openProposalModal(mode, proposal = null) {
    if (mode === 'add') {
      openProposalPage('add');
    } else if (proposal && currentProposalId) {
      openEditProposal(currentProposalId);
    }
  }

  function closeProposalModal() {
    closeProposalPageView();
  }

  // Load proposal documents
  async function loadProposalDocuments(prid) {
    if (!prid) return;
    try {
      const res = await fetch(`/documents?tied_to=${prid}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (res.ok) {
        const documents = await res.json();
        const documentsList = document.getElementById('documentsList');
        if (documentsList) {
          if (documents.length === 0) {
            documentsList.innerHTML = '<div style="text-align:center; color:#999; padding:40px 20px; background:#f8f8f8;">No documents uploaded</div>';
          } else {
            documentsList.innerHTML = documents.map(doc => {
              const dateStr = doc.date_added ? (typeof doc.date_added === 'string' ? doc.date_added : doc.date_added.date || doc.date_added) : '';
              const formattedDate = dateStr ? formatDate(dateStr) : '';
              return `
              <div class="document-item">
                <div class="document-info">
                  <div class="document-name">${doc.name || 'Document'}</div>
                  <div class="document-meta">${doc.doc_id || ''}${formattedDate ? ' - ' + formattedDate : ''}</div>
                </div>
                <div class="document-actions">
                  <a href="/storage/${doc.file_path}" target="_blank" class="btn-view-doc">View</a>
                  <button onclick="deleteDocument(${doc.id})" class="btn-delete-doc">Delete</button>
                </div>
              </div>
            `;
            }).join('');
          }
        }
      }
    } catch (e) {
      console.error('Error loading documents:', e);
    }
  }

  // Open document upload modal
  function openDocumentUpload() {
    const modal = document.getElementById('documentUploadModal');
    const proposalIdInput = document.getElementById('documentProposalId');
    if (modal) {
      if (proposalIdInput && currentProposalId) {
        proposalIdInput.value = currentProposalId;
      }
      modal.style.display = 'flex';
      modal.classList.add('show');
    }
  }

  // Close document upload modal
  function closeDocumentUploadModal() {
    const modal = document.getElementById('documentUploadModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.remove('show');
      document.getElementById('documentUploadForm').reset();
    }
  }

  // Upload document
  async function uploadDocument(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const proposalPageView = document.getElementById('proposalPageView');
    const prid = proposalPageView ? proposalPageView.getAttribute('data-prid') : null;
    
    if (!prid) {
      alert('Proposal PRID not found');
      return;
    }

    formData.append('prid', prid);

    try {
      const res = await fetch('/life-proposals/upload-document', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        }
      });

      if (res.ok) {
        const data = await res.json();
        alert('Document uploaded successfully');
        closeDocumentUploadModal();
        // Reload documents
        loadProposalDocuments(prid);
      } else {
        const error = await res.json();
        alert('Error uploading document: ' + (error.message || 'Unknown error'));
      }
    } catch (e) {
      console.error(e);
      alert('Error uploading document: ' + e.message);
    }
  }

  // Delete document
  async function deleteDocument(docId) {
    if (!confirm('Delete this document?')) return;
    try {
      const res = await fetch(`/documents/${docId}`, {
        method: 'DELETE',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        }
      });

      if (res.ok) {
        alert('Document deleted successfully');
        // Reload documents
        const proposalPageView = document.getElementById('proposalPageView');
        const prid = proposalPageView ? proposalPageView.getAttribute('data-prid') : null;
        if (prid) loadProposalDocuments(prid);
      } else {
        alert('Error deleting document');
      }
    } catch (e) {
      console.error(e);
      alert('Error deleting document: ' + e.message);
    }
  }

  function deleteProposal() {
    if (!currentProposalId) return;
    if (!confirm('Delete this proposal?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/life-proposals/${currentProposalId}`;
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value = csrfToken; form.appendChild(csrf);
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }

  // Generate Policy from Life Proposal
  function generatePolicyFromProposal() {
    if (!currentProposalId) {
      alert('No proposal selected');
      return;
    }
    // Redirect to policy creation page with life proposal ID
    window.location.href = `/life-proposals/${currentProposalId}/generate-policy`;
  }
