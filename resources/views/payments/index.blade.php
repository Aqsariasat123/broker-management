@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('payments');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('payments');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];

  // Context-aware column filtering
  $contextColumns = $selectedColumns;
  if ($context == 'policy') {
      // Hide policy_no when viewing from Policy context
      $contextColumns = array_filter($contextColumns, fn($col) => $col != 'policy_no');
  } elseif ($context == 'client') {
      // Hide client_name when viewing from Client context
      $contextColumns = array_filter($contextColumns, fn($col) => $col != 'client_name');
  }
@endphp

<div class="dashboard">
  <!-- Main Payments Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
            @if($context == 'policy' && $policy)
              {{ $policy->policy_code ?? $policy->policy_no }} -
              <span style="color:#f3742a; font-size:20px; font-weight:500;">Payments</span>
            @elseif($context == 'client' && $client)
              <span style="color:#f3742a; font-size:20px; font-weight:500;">Payments</span>
              <span style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $client->client_name }}</span>
            @elseif(request()->get('filter') == 'overdue')
              <span>Premium Instalments Due</span>
            @else
              <span>Payments</span>
            @endif
          </h3>
      </div>
    </div>
  <div class="container-table">
    <!-- Payments Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <div class="records-found">Records Found - {{ $debitNotes->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            <form method="GET" action="{{ route('payments.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
              @if($context == 'policy' && $policy)
                <input type="hidden" name="policy_id" value="{{ $policy->id }}">
              @endif
              @if($context == 'client' && $client)
                <input type="hidden" name="client_id" value="{{ $client->id }}">
              @endif
              <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
              <select name="status" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
              </select>
              <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
              <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
              <button type="submit" class="btn btn-column" style="background:#fff; color:#000; border:1px solid #ccc;">Filter</button>
              @if(request()->hasAny(['search', 'date_from', 'date_to', 'status']))
                <a href="{{ route('payments.index', array_filter(['policy_id' => $policy->id ?? null, 'client_id' => $client->id ?? null])) }}" class="btn btn-back" style="background:#ccc; color:#333; border-color:#ccc;">Clear</a>
              @endif
            </form>
          </div>
        </div>
      </div>
      <div class="action-buttons">
        @if(request()->has('from_calendar') && request()->from_calendar == '1')
          <button class="btn btn-back" onclick="window.location.href='/calendar?filter=instalments'">Back</button>
        @else
          <button class="btn btn-back" onclick="window.history.back()">Back</button>
        @endif
        <button class="btn btn-add" id="addPaymentBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="paymentsTable">
        <thead>
          <tr>
            <th>Action</th>
            @foreach($contextColumns as $col)
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($debitNotes as $note)
            @php
              $paymentPlan = $note->paymentPlan;
              $schedule = $paymentPlan->schedule ?? null;
              $policyRecord = $schedule->policy ?? null;
              $clientRecord = $policyRecord->client ?? null;

              // Get the latest payment for this debit note
              $latestPayment = $note->payments->first();
              $totalPaid = $note->payments->sum('amount');

              // Calculate due_in days
              $dueDate = $paymentPlan->due_date ?? null;
              $dueIn = null;
              if ($dueDate) {
                  $dueIn = \Carbon\Carbon::parse($dueDate)->diffInDays(now(), false) * -1;
              }

              // Determine payment type
              $paymentType = $paymentPlan->installment_label ?? 'Instalment';
              if (stripos($paymentType, 'full') !== false || $paymentPlan->frequency == 'single') {
                  $paymentType = 'Full payment';
              } else {
                  $paymentType = 'Instalment';
              }

              // Determine status display
              $status = ucfirst($note->status);
              if ($note->status == 'paid') {
                  $statusColor = '#28a745';
              } elseif ($note->status == 'partial') {
                  $statusColor = '#ffc107';
              } elseif ($note->status == 'overdue' || ($dueIn !== null && $dueIn < 0 && $note->status != 'paid')) {
                  $statusColor = '#dc3545';
                  $status = 'Overdue';
              } else {
                  $statusColor = '#6c757d';
                  $status = 'Unpaid';
              }
            @endphp
            <tr>
              <td class="action-cell">
                <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openPaymentDetails({{ $note->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
              </td>
              @foreach($contextColumns as $col)
                @if($col == 'debit_note_no')
                  <td data-column="debit_note_no">
                    <a href="javascript:void(0)" onclick="openPaymentDetails({{ $note->id }})" style="color:#007bff; text-decoration:underline;">{{ $note->debit_note_no }}</a>
                  </td>
                @elseif($col == 'payment_type')
                  <td data-column="payment_type">{{ $paymentType }}</td>
                @elseif($col == 'installment_no')
                  <td data-column="installment_no">{{ $paymentPlan->installment_label ?? '-' }}</td>
                @elseif($col == 'date_due')
                  <td data-column="date_due">{{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'due_in')
                  <td data-column="due_in" style="{{ $dueIn !== null && $dueIn < 0 ? 'color:#dc3545;' : '' }}">
                    {{ $dueIn !== null ? $dueIn : '-' }}
                  </td>
                @elseif($col == 'amount_due')
                  <td data-column="amount_due">{{ $note->amount ? number_format($note->amount, 2) : ($paymentPlan->amount ? number_format($paymentPlan->amount, 2) : '-') }}</td>
                @elseif($col == 'status')
                  <td data-column="status">
                    <span style="font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; background:{{ $statusColor }};">
                      {{ $status }}
                    </span>
                  </td>
                @elseif($col == 'amount_paid')
                  <td data-column="amount_paid">{{ $totalPaid > 0 ? number_format($totalPaid, 2) : '-' }}</td>
                @elseif($col == 'date_paid')
                  <td data-column="date_paid">{{ $latestPayment && $latestPayment->paid_on ? $latestPayment->paid_on->format('d-M-y') : '-' }}</td>
                @elseif($col == 'payment_mode')
                  <td data-column="payment_mode">{{ $latestPayment && $latestPayment->modeOfPayment ? $latestPayment->modeOfPayment->name : '-' }}</td>
                @elseif($col == 'cheque_no')
                  <td data-column="cheque_no">{{ $latestPayment->cheque_no ?? '-' }}</td>
                @elseif($col == 'policy_no')
                  <td data-column="policy_no">
                    @if($policyRecord)
                      <a href="{{ route('policies.show', $policyRecord->id) }}" style="color:#007bff; text-decoration:underline;">{{ $policyRecord->policy_no }}</a>
                    @else
                      -
                    @endif
                  </td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">
                    @if($clientRecord)
                      <a href="{{ route('clients.show', $clientRecord->id) }}" style="color:#007bff; text-decoration:underline;">{{ $clientRecord->client_name }}</a>
                    @else
                      -
                    @endif
                  </td>
                @elseif($col == 'comments')
                  <td data-column="comments">{{ $latestPayment->notes ?? $note->notes ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
      <div class="footer-left">
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $debitNotes->currentPage();
          $last = max(1, $debitNotes->lastPage());
          function page_url_payments($base, $q, $p) {
            $params = array_merge($q, ['page' => $p]);
            return $base . '?' . http_build_query($params);
          }
        @endphp

        <a class="btn-page" href="{{ $current > 1 ? page_url_payments($base, $q, 1) : '#' }}" @if($current <= 1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current > 1 ? page_url_payments($base, $q, $current - 1) : '#' }}" @if($current <= 1) disabled @endif>&lsaquo;</a>

        <span style="padding:0 8px;">Page {{ $current }} of {{ $last }}</span>

        <a class="btn-page" href="{{ $current < $last ? page_url_payments($base, $q, $current + 1) : '#' }}" @if($current >= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current < $last ? page_url_payments($base, $q, $last) : '#' }}" @if($current >= $last) disabled @endif>&raquo;</a>
      </div>
    </div>
    </div>
  </div>

  <!-- Payment Page View (Full Page) -->
  <div class="client-page-view" id="paymentPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="paymentPageTitle">Payment</span> - <span class="client-name" id="paymentPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editPaymentFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closePaymentPageBtn" onclick="closePaymentPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Payment Details View -->
        <div id="paymentDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="paymentDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Payment Edit/Add Form -->
        <div id="paymentFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="paymentDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deletePayment()">Delete</button>
                <button type="submit" form="paymentPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closePaymentFormBtn" onclick="closePaymentPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="paymentPageForm" method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
              @csrf
              <div id="paymentPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Payment Side Panel -->
  <div class="side-panel" id="paymentSidePanel">
    <div class="side-panel-header">
      <h4 id="paymentPanelTitle">Add/Edit Payment</h4>
      <div class="side-panel-actions">
        <button type="button" class="btn-delete" id="paymentDeleteBtn2" style="display:none;" onclick="deletePayment()">Delete</button>
        <button type="submit" form="paymentPanelForm" class="btn-save">Save</button>
        <button type="button" class="btn-cancel" onclick="closePaymentPanel()">Cancel</button>
      </div>
    </div>
    <form id="paymentPanelForm" method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
      @csrf
      <div id="paymentPanelFormMethod" style="display:none;"></div>
      <div class="side-panel-body">
        <!-- Readonly Info Fields -->
        <div class="panel-form-row">
          <label>Debit Note No</label>
          <input type="text" id="panel_debit_note_no" class="form-control" readonly>
          <input type="hidden" name="debit_note_id" id="panel_debit_note_id">
        </div>
        <div class="panel-form-row">
          <label>Policy Number</label>
          <input type="text" id="panel_policy_no" class="form-control" readonly>
        </div>
        <div class="panel-form-row">
          <label>Date Due</label>
          <input type="text" id="panel_date_due" class="form-control" readonly>
        </div>
        <div class="panel-form-row">
          <label>Amount Due</label>
          <input type="text" id="panel_amount_due" class="form-control" readonly>
        </div>

        <!-- Editable Fields -->
        <div class="panel-form-row">
          <label>Amount Paid</label>
          <input type="number" step="0.01" min="0" name="amount" id="panel_amount" class="form-control">
        </div>
        <div class="panel-form-row">
          <label>Date Paid</label>
          <input type="date" name="paid_on" id="panel_paid_on" class="form-control">
        </div>
        <div class="panel-form-row">
          <label>Payment Type</label>
          <input type="text" id="panel_payment_type" class="form-control" readonly>
        </div>
        <div class="panel-form-row">
          <label>Mode Of Payment</label>
          <select name="mode_of_payment_id" id="panel_mode_of_payment_id" class="form-control">
            <option value="">Select</option>
            @foreach($modesOfPayment as $mode)
              <option value="{{ $mode->id }}">{{ $mode->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="panel-form-row">
          <label>Cheque No</label>
          <input type="text" name="cheque_no" id="panel_cheque_no" class="form-control">
        </div>
        <div class="panel-form-row">
          <label>Variance</label>
          <input type="number" step="0.01" name="variance" id="panel_variance" class="form-control" readonly>
        </div>
        <div class="panel-form-row">
          <label>Variance Reason</label>
          <input type="text" name="variance_reason" id="panel_variance_reason" class="form-control">
        </div>
        <div class="panel-form-row" style="align-items:flex-start;">
          <label style="padding-top:8px;">Payment Notes</label>
          <textarea name="notes" id="panel_notes" class="form-control" rows="4"></textarea>
        </div>

        <!-- Hidden payment reference field -->
        <input type="hidden" name="payment_reference" id="panel_payment_reference">
      </div>
    </form>
  </div>
  <div class="side-panel-overlay" id="paymentPanelOverlay" onclick="closePaymentPanel()"></div>

  <!-- Legacy Modal (hidden, kept for form structure reference) -->
  <div class="modal" id="paymentModal" style="display:none !important;">
    <div class="modal-content">
      <form id="paymentForm" method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="paymentFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <select class="form-control" name="debit_note_id" id="debit_note_id">
            <option value="">Select Debit Note</option>
            @foreach($allDebitNotes as $dn)
              <option value="{{ $dn->id }}"
                data-debit-note-no="{{ $dn->debit_note_no }}"
                data-policy-no="{{ $dn->paymentPlan->schedule->policy->policy_no ?? 'N/A' }}"
                data-date-due="{{ $dn->paymentPlan->due_date ?? '' }}"
                data-amount-due="{{ $dn->amount }}"
                data-payment-type="{{ stripos($dn->paymentPlan->installment_label ?? '', 'full') !== false ? 'Full payment' : 'Instalment' }}">
                {{ $dn->debit_note_no }} - {{ $dn->paymentPlan->schedule->policy->policy_no ?? 'N/A' }}
              </option>
            @endforeach
          </select>
        </div>
      </form>
    </div>
  </div>

  <!-- Column Selection Modal -->
  <div class="modal" id="columnModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Column Select & Sort</h4>
        <button type="button" class="modal-close" onclick="closeColumnModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="column-actions">
          <button type="button" class="btn-select-all" onclick="selectAllColumns()">Select All</button>
          <button type="button" class="btn-deselect-all" onclick="deselectAllColumns()">Deselect All</button>
        </div>

        <form id="columnForm" action="{{ route('payments.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @php
              $all = $config['column_definitions'];
              // Maintain order based on selectedColumns
              $ordered = [];
              foreach($selectedColumns as $col) {
                if(isset($all[$col])) {
                  $ordered[$col] = $all[$col];
                  unset($all[$col]);
                }
              }
              $ordered = array_merge($ordered, $all);
            @endphp

            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryColumns);
                $isChecked = in_array($key, $selectedColumns) || $isMandatory;
              @endphp
              <div class="column-item" draggable="true" data-column="{{ $key }}" style="cursor:move;">
                <span style="cursor:move; margin-right:8px; font-size:16px; color:#666;">☰</span>
                <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
                <label for="col_{{ $key }}" style="cursor:pointer; flex:1; user-select:none;">{{ $label }}</label>
              </div>
            @endforeach
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn-cancel" onclick="closeColumnModal()">Cancel</button>
        <button class="btn-save" onclick="saveColumnSettings()">Save Settings</button>
      </div>
    </div>
  </div>

</div>



<script>
  // Initialize data from Blade - must be before partials-table-scripts
  let currentPaymentId = null;
  const selectedColumns = @json($selectedColumns);
  const paymentsStoreRoute = '{{ route("payments.store") }}';
  const paymentsUpdateRouteTemplate = '{{ route("payments.update", ":id") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/payments-index.js') }}"></script>
@endsection
