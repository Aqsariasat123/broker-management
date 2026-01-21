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

// Current payment/debit note being edited
let currentPaymentId = null;
let currentDebitNoteData = null;

// Open payment side panel for editing
window.openPaymentDetails = async function(debitNoteId) {
  try {
    // Fetch the debit note data with payment info
    const res = await fetch(`/api/debit-notes/${debitNoteId}/payment-info`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    if (!res.ok) {
      // Fallback to fetching from payments endpoint
      const paymentRes = await fetch(`/payments/${debitNoteId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!paymentRes.ok) throw new Error(`HTTP ${paymentRes.status}`);
      const payment = await paymentRes.json();
      openPaymentPanelWithData(payment);
      return;
    }

    const data = await res.json();
    openPaymentPanelWithData(data);
  } catch (e) {
    console.error(e);
    // Try opening with basic debit note info from the select dropdown
    openPaymentPanelForDebitNote(debitNoteId);
  }
};

// Open panel with fetched data
function openPaymentPanelWithData(data) {
  const panel = document.getElementById('paymentSidePanel');
  const overlay = document.getElementById('paymentPanelOverlay');
  const deleteBtn = document.getElementById('paymentDeleteBtn2');
  const form = document.getElementById('paymentPanelForm');
  const formMethod = document.getElementById('paymentPanelFormMethod');

  // Extract data (handle different response formats)
  const debitNote = data.debit_note || data.debitNote || data;
  const payment = data.payment || data.payments?.[0] || data;
  const paymentPlan = debitNote.payment_plan || debitNote.paymentPlan || {};
  const schedule = paymentPlan.schedule || {};
  const policy = schedule.policy || {};

  currentDebitNoteData = {
    id: debitNote.id,
    debit_note_no: debitNote.debit_note_no,
    policy_no: policy.policy_no || 'N/A',
    date_due: paymentPlan.due_date,
    amount_due: debitNote.amount || paymentPlan.amount,
    payment_type: paymentPlan.installment_label?.toLowerCase().includes('full') ? 'Full payment' : 'Instalment'
  };

  // Populate readonly fields
  document.getElementById('panel_debit_note_no').value = currentDebitNoteData.debit_note_no || '';
  document.getElementById('panel_debit_note_id').value = debitNote.id || '';
  document.getElementById('panel_policy_no').value = currentDebitNoteData.policy_no || '';
  document.getElementById('panel_date_due').value = currentDebitNoteData.date_due ? formatDate(currentDebitNoteData.date_due) : '';
  document.getElementById('panel_amount_due').value = currentDebitNoteData.amount_due ? formatNumber(currentDebitNoteData.amount_due) : '';
  document.getElementById('panel_payment_type').value = currentDebitNoteData.payment_type || 'Instalment';

  // Populate editable fields from existing payment
  if (payment && payment.id) {
    currentPaymentId = payment.id;
    document.getElementById('panel_amount').value = payment.amount || '';
    document.getElementById('panel_paid_on').value = payment.paid_on ? payment.paid_on.split('T')[0] : '';
    document.getElementById('panel_mode_of_payment_id').value = payment.mode_of_payment_id || '';
    document.getElementById('panel_cheque_no').value = payment.cheque_no || '';
    document.getElementById('panel_variance').value = payment.variance || '';
    document.getElementById('panel_variance_reason').value = payment.variance_reason || '';
    document.getElementById('panel_notes').value = payment.notes || '';
    document.getElementById('panel_payment_reference').value = payment.payment_reference || '';

    // Set form for update
    form.action = `/payments/${payment.id}`;
    formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    deleteBtn.style.display = 'inline-block';
  } else {
    // New payment
    currentPaymentId = null;
    document.getElementById('panel_amount').value = '';
    document.getElementById('panel_paid_on').value = new Date().toISOString().split('T')[0];
    document.getElementById('panel_mode_of_payment_id').value = '';
    document.getElementById('panel_cheque_no').value = '';
    document.getElementById('panel_variance').value = '';
    document.getElementById('panel_variance_reason').value = '';
    document.getElementById('panel_notes').value = '';
    document.getElementById('panel_payment_reference').value = `PAY-${Date.now()}`;

    // Set form for create
    form.action = paymentsStoreRoute;
    formMethod.innerHTML = '';
    deleteBtn.style.display = 'none';
  }

  // Calculate variance on amount change
  calculateVariance();

  // Show panel
  panel.classList.add('show');
  overlay.classList.add('show');
  document.body.style.overflow = 'hidden';
}

// Fallback: Open panel using debit note select data
function openPaymentPanelForDebitNote(debitNoteId) {
  const select = document.getElementById('debit_note_id');
  if (!select) return;

  const option = select.querySelector(`option[value="${debitNoteId}"]`);
  if (!option) {
    alert('Debit note not found');
    return;
  }

  const panel = document.getElementById('paymentSidePanel');
  const overlay = document.getElementById('paymentPanelOverlay');
  const deleteBtn = document.getElementById('paymentDeleteBtn2');
  const form = document.getElementById('paymentPanelForm');
  const formMethod = document.getElementById('paymentPanelFormMethod');

  currentDebitNoteData = {
    id: debitNoteId,
    debit_note_no: option.dataset.debitNoteNo || option.textContent.split(' - ')[0],
    policy_no: option.dataset.policyNo || 'N/A',
    date_due: option.dataset.dateDue || '',
    amount_due: option.dataset.amountDue || '',
    payment_type: option.dataset.paymentType || 'Instalment'
  };

  // Populate readonly fields
  document.getElementById('panel_debit_note_no').value = currentDebitNoteData.debit_note_no || '';
  document.getElementById('panel_debit_note_id').value = debitNoteId;
  document.getElementById('panel_policy_no').value = currentDebitNoteData.policy_no || '';
  document.getElementById('panel_date_due').value = currentDebitNoteData.date_due ? formatDate(currentDebitNoteData.date_due) : '';
  document.getElementById('panel_amount_due').value = currentDebitNoteData.amount_due ? formatNumber(parseFloat(currentDebitNoteData.amount_due)) : '';
  document.getElementById('panel_payment_type').value = currentDebitNoteData.payment_type || 'Instalment';

  // Clear editable fields for new payment
  currentPaymentId = null;
  document.getElementById('panel_amount').value = '';
  document.getElementById('panel_paid_on').value = new Date().toISOString().split('T')[0];
  document.getElementById('panel_mode_of_payment_id').value = '';
  document.getElementById('panel_cheque_no').value = '';
  document.getElementById('panel_variance').value = '';
  document.getElementById('panel_variance_reason').value = '';
  document.getElementById('panel_notes').value = '';
  document.getElementById('panel_payment_reference').value = `PAY-${Date.now()}`;

  // Set form for create
  form.action = paymentsStoreRoute;
  formMethod.innerHTML = '';
  deleteBtn.style.display = 'none';

  // Show panel
  panel.classList.add('show');
  overlay.classList.add('show');
  document.body.style.overflow = 'hidden';
}

// Calculate variance when amount paid changes
function calculateVariance() {
  const amountDueStr = document.getElementById('panel_amount_due').value.replace(/,/g, '');
  const amountPaid = parseFloat(document.getElementById('panel_amount').value) || 0;
  const amountDue = parseFloat(amountDueStr) || 0;

  if (amountPaid > 0 && amountDue > 0) {
    const variance = amountPaid - amountDue;
    document.getElementById('panel_variance').value = variance.toFixed(2);
  } else {
    document.getElementById('panel_variance').value = '';
  }
}

// Close payment panel
window.closePaymentPanel = function() {
  const panel = document.getElementById('paymentSidePanel');
  const overlay = document.getElementById('paymentPanelOverlay');

  panel.classList.remove('show');
  overlay.classList.remove('show');
  document.body.style.overflow = '';

  currentPaymentId = null;
  currentDebitNoteData = null;
};

// Delete payment
window.deletePayment = function() {
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
};

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Add Payment Button - opens panel for adding new payment
  const addPaymentBtn = document.getElementById('addPaymentBtn');
  if (addPaymentBtn) {
    addPaymentBtn.addEventListener('click', function() {
      // Get first available debit note from select
      const select = document.getElementById('debit_note_id');
      if (select && select.options.length > 1) {
        openPaymentPanelForDebitNote(select.options[1].value);
      } else {
        alert('No debit notes available');
      }
    });
  }

  // Column button
  const columnBtn = document.getElementById('columnBtn2');
  if (columnBtn) {
    columnBtn.addEventListener('click', function() {
      openColumnModal();
    });
  }

  // Calculate variance when amount changes
  const amountInput = document.getElementById('panel_amount');
  if (amountInput) {
    amountInput.addEventListener('input', calculateVariance);
  }

  // Handle panel form submission
  const panelForm = document.getElementById('paymentPanelForm');
  if (panelForm) {
    panelForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const url = this.action;

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
        if (data.success || data.id || data.payment) {
          closePaymentPanel();
          window.location.reload();
        } else {
          alert(data.message || 'Error saving payment');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        // Try submitting normally
        this.submit();
      });
    });
  }

  // Close panel on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closePaymentPanel();
    }
  });
});

// Legacy functions for backward compatibility
function openPaymentModal(mode, paymentId = null) {
  if (mode === 'edit' && paymentId) {
    openPaymentDetails(paymentId);
  } else {
    // Add mode - get first debit note
    const select = document.getElementById('debit_note_id');
    if (select && select.options.length > 1) {
      openPaymentPanelForDebitNote(select.options[1].value);
    }
  }
}

function closePaymentModal() {
  closePaymentPanel();
}

// Populate payment details view (kept for reference)
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

  content.innerHTML = col1 + col2 + col3;
}

function closePaymentPageView() {
  const paymentPageView = document.getElementById('paymentPageView');
  if (paymentPageView) {
    paymentPageView.classList.remove('show');
    paymentPageView.style.display = 'none';
  }
  const clientsTableView = document.getElementById('clientsTableView');
  if (clientsTableView) {
    clientsTableView.classList.remove('hidden');
  }
  currentPaymentId = null;
}
