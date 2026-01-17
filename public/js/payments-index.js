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

  // Open payment details (full page view) - MUST be defined before HTML onclick handlers
  window.openPaymentDetails = async function(id) {
    try {
      const res = await fetch(`/payments/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const payment = await res.json();
      currentPaymentId = id;
      
      // Get all required elements
      const paymentPageName = document.getElementById('paymentPageName');
      const paymentPageTitle = document.getElementById('paymentPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const paymentPageView = document.getElementById('paymentPageView');
      const paymentDetailsPageContent = document.getElementById('paymentDetailsPageContent');
      const paymentFormPageContent = document.getElementById('paymentFormPageContent');
      const editPaymentFromPageBtn = document.getElementById('editPaymentFromPageBtn');
      const closePaymentPageBtn = document.getElementById('closePaymentPageBtn');
      
      if (!paymentPageName || !paymentPageTitle || !clientsTableView || !paymentPageView || 
          !paymentDetailsPageContent || !paymentFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set payment name in header
      const paymentName = payment.payment_reference || 'Unknown';
      paymentPageName.textContent = paymentName;
      paymentPageTitle.textContent = 'Payment';
      
      populatePaymentDetails(payment);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      paymentPageView.style.display = 'block';
      paymentPageView.classList.add('show');
      paymentDetailsPageContent.style.display = 'block';
      paymentFormPageContent.style.display = 'none';
      if (editPaymentFromPageBtn) editPaymentFromPageBtn.style.display = 'inline-block';
      if (closePaymentPageBtn) closePaymentPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading payment details: ' + e.message);
    }
  };

  // Populate payment details view
  function populatePaymentDetails(payment) {
    const content = document.getElementById('paymentDetailsContent');
    if (!content) return;

    const debitNote = payment.debit_note || payment.debitNote || {};
    const paymentPlan = debitNote.payment_plan || debitNote.paymentPlan || {};
    const schedule = paymentPlan.schedule || {};
    const policy = schedule.policy || {};
    const client = policy.client || {};
    const modeOfPayment = payment.mode_of_payment || payment.modeOfPayment || {};

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">PAYMENT DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Payment Reference</span>
            <div class="detail-value">${payment.payment_reference || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Debit Note No</span>
            <div class="detail-value">${debitNote.debit_note_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy No</span>
            <div class="detail-value">${policy.policy_no || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Client Name</span>
            <div class="detail-value">${client.client_name || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">FINANCIAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Paid On</span>
            <div class="detail-value">${formatDate(payment.paid_on)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Amount</span>
            <div class="detail-value">${formatNumber(payment.amount)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Mode Of Payment</span>
            <div class="detail-value">${modeOfPayment.name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Receipt</span>
            <div class="detail-value">${payment.receipt_path ? '<a href="#" target="_blank" style="color:#007bff;">View Receipt</a>' : '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">NOTES</div>
        <div class="detail-section-body">
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Notes</span>
            <textarea class="detail-value" style="min-height:120px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${payment.notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    const col4 = `
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open payment page (Add or Edit)
  async function openPaymentPage(mode) {
    if (mode === 'add') {
      openPaymentForm('add');
    } else {
      if (currentPaymentId) {
        openEditPayment(currentPaymentId);
      }
    }
  }

  // Add Payment Button
  document.getElementById('addPaymentBtn').addEventListener('click', () => openPaymentModal('add'));
  document.getElementById('columnBtn2').addEventListener('click', () => openColumnModal());

  async function openEditPayment(id) {
    try {
      const res = await fetch(`/payments/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const payment = await res.json();
      currentPaymentId = id;
      openPaymentForm('edit', payment);
    } catch (e) {
      console.error(e);
      alert('Error loading payment data');
    }
  }

  function openPaymentForm(mode, payment = null) {
    // Clone form from modal
    const modalForm = document.getElementById('paymentModal').querySelector('form');
    const pageForm = document.getElementById('paymentPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('paymentPageFormMethod');
    const deleteBtn = document.getElementById('paymentDeleteBtn');
    const editBtn = document.getElementById('editPaymentFromPageBtn');
    const closeBtn = document.getElementById('closePaymentPageBtn');
    const closeFormBtn = document.getElementById('closePaymentFormBtn');

    if (mode === 'add') {
      document.getElementById('paymentPageTitle').textContent = 'Add Payment';
      document.getElementById('paymentPageName').textContent = '';
      pageForm.action = paymentsStoreRoute;
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const paymentName = payment.payment_reference || 'Unknown';
      document.getElementById('paymentPageTitle').textContent = 'Edit Payment';
      document.getElementById('paymentPageName').textContent = paymentName;
      pageForm.action = `/payments/${currentPaymentId}`;
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

      const fields = ['debit_note_id','payment_reference','paid_on','amount','mode_of_payment_id','notes'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'date') {
          el.value = payment[k] ? (typeof payment[k] === 'string' ? payment[k].substring(0,10) : payment[k]) : '';
        } else if (el.tagName === 'TEXTAREA') {
          el.value = payment[k] ?? '';
        } else {
          el.value = payment[k] ?? '';
        }
      });
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const paymentPageView = document.getElementById('paymentPageView');
    paymentPageView.style.display = 'block';
    paymentPageView.classList.add('show');
    document.getElementById('paymentDetailsPageContent').style.display = 'none';
    document.getElementById('paymentFormPageContent').style.display = 'block';
  }

  function closePaymentPageView() {
    const paymentPageView = document.getElementById('paymentPageView');
    paymentPageView.classList.remove('show');
    paymentPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('paymentDetailsPageContent').style.display = 'none';
    document.getElementById('paymentFormPageContent').style.display = 'none';
    currentPaymentId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editPaymentFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentPaymentId) {
        openEditPayment(currentPaymentId);
      }
    });
  }

  // Column modal functions
  // Column modal functions are provided by partials-table-scripts.js

  function deletePayment() {
    if (!currentPaymentId) return;
    if (!confirm('Delete this payment?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/payments/${currentPaymentId}`;
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

  // Legacy function for backward compatibility
  function openPaymentModal(mode, paymentId = null) {
    const modal = document.getElementById('paymentModal');
    const form = document.getElementById('paymentForm');
    const formMethod = document.getElementById('paymentFormMethod');
    const modalTitle = document.getElementById('paymentModalTitle');
    const deleteBtn = document.getElementById('paymentDeleteBtn');
    
    if (mode === 'add') {
      modalTitle.textContent = 'Add Payment';
      form.reset();
      form.action = paymentsStoreRoute;
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      currentPaymentId = null;
    } else if (mode === 'edit' && paymentId) {
      modalTitle.textContent = 'Edit Payment';
      form.action = paymentsUpdateRouteTemplate.replace(':id', paymentId);
      formMethod.innerHTML = '@method("PUT")';
      deleteBtn.style.display = 'inline-block';
      currentPaymentId = paymentId;
      
      // Fetch payment data
      fetch(`/payments/${paymentId}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data.payment) {
            const p = data.payment;
            document.getElementById('debit_note_id').value = p.debit_note_id || '';
            document.getElementById('payment_reference').value = p.payment_reference || '';
            document.getElementById('paid_on').value = p.paid_on ? p.paid_on.split('T')[0] : '';
            document.getElementById('amount').value = p.amount || '';
            document.getElementById('mode_of_payment_id').value = p.mode_of_payment_id || '';
            document.getElementById('notes').value = p.notes || '';
          }
        })
        .catch(error => {
          console.error('Error fetching payment data:', error);
          alert('Error loading payment data');
        });
    }
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    const form = document.getElementById('paymentForm');
    form.reset();
    currentPaymentId = null;
  }

  // Close modal on outside click
  document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closePaymentModal();
    }
  });

  // Handle form submission
  document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const url = form.action;
    const method = form.querySelector('[name="_method"]') ? form.querySelector('[name="_method"]').value : 'POST';
    
    if (method !== 'POST') {
      formData.append('_method', method);
    }
    
    fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        closePaymentModal();
        window.location.reload();
      } else {
        alert(data.message || 'Error saving payment');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving payment');
    });
  });

  // Update openPaymentDetails to open modal in edit mode instead of full page view
  const originalOpenPaymentDetails = window.openPaymentDetails;
  window.openPaymentDetails = function(id) {
    openPaymentModal('edit', id);
  };
