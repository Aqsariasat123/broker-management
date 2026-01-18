@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('payment-plans');

  // When coming from Client page, use custom columns
  $fromClient = isset($client) && $client;
  if ($fromClient) {
    // Client view column definitions
    $columnDefinitions = [
      'debit_note' => 'Debit Note',
      'payment_type' => 'Payment Type',
      'date_due' => 'Date Due',
      'amount_due' => 'Amount Due',
      'status' => 'Status',
      'amount_paid' => 'Amount Paid',
      'date_paid' => 'Date Paid',
      'payment_mode' => 'Payment Mode',
      'cheque_no' => 'Cheque No',
      'policy_no' => 'Policy Number',
      'comments' => 'Comments',
    ];
    // Default columns for client view
    $defaultClientColumns = ['debit_note','payment_type','date_due','amount_due','status','amount_paid','date_paid','payment_mode','cheque_no','policy_no','comments'];
    // Read from session if saved, otherwise use defaults
    $selectedColumns = session('payment_plan_client_columns', $defaultClientColumns);
    // Filter to only include valid columns from definitions
    $selectedColumns = array_filter($selectedColumns, fn($col) => isset($columnDefinitions[$col]));
    if (empty($selectedColumns)) {
      $selectedColumns = $defaultClientColumns;
    }
    $mandatoryColumns = ['status']; // Minimal mandatory for client view
  } else {
    $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('payment-plans');
    $columnDefinitions = $config['column_definitions'] ?? [];
    $mandatoryColumns = $config['mandatory_columns'] ?? [];
  }
@endphp

<div class="dashboard">
  <!-- Main Payment Plans Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
            @if(isset($client) && $client)
              Payments - <span style="color:#f3742a;">{{ $client->client_name ?? $client->first_name . ' ' . $client->surname }}</span>
            @else
              Payment Plans
            @endif
          </h3>
       
      </div>
    </div>
  <div class="container-table">
    <!-- Payment Plans Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
                <div class="records-found">Records Found - {{ $paymentPlans->total() }}</div>

      <div class="page-title-section">
        @if(isset($client) && $client)
          {{-- Simple filter toggle for client view --}}
          <div style="display:flex; align-items:center; gap:15px;">
            <label class="toggle-switch" style="display:flex; align-items:center; gap:8px;">
              <input type="checkbox" id="filterToggle">
              <span class="toggle-slider"></span>
            </label>
            <span style="font-size:14px;">Filter</span>
          </div>
        @else
          <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
            <div class="filter-group">
              <form method="GET" action="{{ route('payment-plans.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
                <select name="status" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
                  <option value="">All Status</option>
                  <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                  <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                  <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                  <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <label style="display:flex; align-items:center; gap:4px; font-size:13px;"><input type="checkbox" name="due_soon" value="true" {{ request('due_soon') == 'true' ? 'checked' : '' }}> Due Soon</label>
                <button type="submit" class="btn btn-column" style="background:#fff; color:#000; border:1px solid #ccc;">Filter</button>
                @if(request()->hasAny(['search', 'status', 'due_soon']))
                  <a href="{{ route('payment-plans.index') }}" class="btn btn-back" style="background:#ccc; color:#333; border-color:#ccc;">Clear</a>
                @endif
              </form>
            </div>
          </div>
        @endif
      </div>
      <div class="action-buttons">
        @if(request()->has('client_id') && request()->client_id)
          <button class="btn btn-back" onclick="window.location.href='{{ route('clients.index', ['client_id' => request()->client_id]) }}'">Back</button>
        @else
          <button class="btn btn-add" id="addPaymentPlanBtn">Add</button>
          <button class="btn btn-close" onclick="window.history.back()">Close</button>
        @endif
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="paymentPlansTable">
        <thead>
          <tr>
            <th style="text-align:center;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle;">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 2 16 2 16H22C22 16 19 14.25 19 9C19 5.13 15.87 2 12 2Z" fill="#fff" stroke="#fff" stroke-width="1.5"/>
                <path d="M9 21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21H9Z" fill="#fff"/>
              </svg>
            </th>
            <th>Action</th>
            @foreach($selectedColumns as $col)
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($paymentPlans as $plan)
            @php
              // Determine payment status for bell indicator
              $isPaid = $plan->status == 'paid';
              $isOverdue = !$isPaid && ($plan->status == 'overdue' || ($plan->due_date && $plan->due_date->isPast()));
              $isDueSoon = !$isPaid && !$isOverdue && $plan->due_date && $plan->due_date->isBetween(now(), now()->addDays(30));

              // Bell indicator: red=overdue, yellow=due soon, green=paid, orange border=normal
              if ($isOverdue) {
                $bellColor = '#dc3545'; // red
                $bellBg = '#dc3545';
                $rowClass = 'overdue-row';
              } elseif ($isDueSoon) {
                $bellColor = '#ffc107'; // yellow
                $bellBg = '#ffc107';
                $rowClass = 'due-soon-row';
              } elseif ($isPaid) {
                $bellColor = '#28a745'; // green
                $bellBg = '#28a745';
                $rowClass = 'paid-row';
              } else {
                $bellColor = '#f3742a'; // orange border only
                $bellBg = 'transparent';
                $rowClass = '';
              }
            @endphp
            <tr class="{{ $rowClass }}">
              <td class="bell-cell">
                <div style="display:flex; align-items:center; justify-content:center;">
                  <div class="status-indicator" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $bellColor }}; background-color:{{ $bellBg }};"></div>
                </div>
              </td>
              <td class="action-cell">
                <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openPaymentPlanModal('edit',{{ $plan->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'installment_label')
                  <td data-column="installment_label">{{ $plan->installment_label ?? 'Installment #' . $plan->id }}</td>
                @elseif($col == 'debit_note')
                  <td data-column="debit_note">{{ $plan->debitNotes->first()->debit_note_no ?? '-' }}</td>
                @elseif($col == 'payment_type')
                  <td data-column="payment_type">{{ $plan->installment_label ?? 'Instalment' }}</td>
                @elseif($col == 'date_due')
                  <td data-column="date_due">{{ $plan->due_date ? $plan->due_date->format('d-M-y') : '-' }}</td>
                @elseif($col == 'amount_due')
                  <td data-column="amount_due">{{ $plan->amount ? number_format($plan->amount, 2) : '-' }}</td>
                @elseif($col == 'amount_paid')
                  @php $latestPayment = $plan->debitNotes->first()?->payments->first(); @endphp
                  <td data-column="amount_paid">{{ $latestPayment ? number_format($latestPayment->amount, 2) : '' }}</td>
                @elseif($col == 'date_paid')
                  @php $latestPayment = $latestPayment ?? $plan->debitNotes->first()?->payments->first(); @endphp
                  <td data-column="date_paid">{{ $latestPayment && $latestPayment->paid_on ? \Carbon\Carbon::parse($latestPayment->paid_on)->format('d-M-y') : '' }}</td>
                @elseif($col == 'payment_mode')
                  @php $latestPayment = $latestPayment ?? $plan->debitNotes->first()?->payments->first(); @endphp
                  <td data-column="payment_mode">{{ $latestPayment?->modeOfPayment?->name ?? '' }}</td>
                @elseif($col == 'cheque_no')
                  @php $latestPayment = $latestPayment ?? $plan->debitNotes->first()?->payments->first(); @endphp
                  <td data-column="cheque_no">{{ $latestPayment?->cheque_no ?? '' }}</td>
                @elseif($col == 'policy_no')
                  <td data-column="policy_no">{{ $plan->schedule->policy->policy_no ?? '-' }}</td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $plan->schedule->policy->client->client_name ?? '-' }}</td>
                @elseif($col == 'due_date')
                  <td data-column="due_date">{{ $plan->due_date ? $plan->due_date->format('d-M-y') : '-' }}</td>
                @elseif($col == 'amount')
                  <td data-column="amount">{{ $plan->amount ? number_format($plan->amount, 2) : '-' }}</td>
                @elseif($col == 'frequency')
                  <td data-column="frequency">{{ $plan->lookuFrequency->name ?? '-' }}</td>
                @elseif($col == 'comments')
                  <td data-column="comments">{{ $plan->comments ?? $plan->notes ?? '' }}</td>
                @elseif($col == 'status')
                  <td data-column="status">
                    <span class="badge-status badge-{{ $plan->status }}" style="font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; background:{{ $plan->status == 'pending' ? '#ffc107' : ($plan->status == 'active' ? '#17a2b8' : ($plan->status == 'paid' ? '#28a745' : ($plan->status == 'overdue' ? '#dc3545' : '#6c757d'))) }};">
                      {{ ucfirst($plan->status) }}
                    </span>
                  </td>
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
        <a class="btn btn-export" href="{{ route('payment-plans.export', array_merge(request()->query(), ['page' => $paymentPlans->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $paymentPlans->currentPage();
          $last = max(1, $paymentPlans->lastPage());
          function page_url($base, $q, $p) {
            $params = array_merge($q, ['page' => $p]);
            return $base . '?' . http_build_query($params);
          }
        @endphp

        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, 1) : '#' }}" @if($current <= 1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, $current - 1) : '#' }}" @if($current <= 1) disabled @endif>&lsaquo;</a>

        <span style="padding:0 8px;">Page {{ $current }} of {{ $last }}</span>

        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $current + 1) : '#' }}" @if($current >= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $last) : '#' }}" @if($current >= $last) disabled @endif>&raquo;</a>
      </div>
    </div>
    </div>
  </div>

  <!-- Payment Plan Page View (Full Page) -->
  <div class="client-page-view" id="paymentPlanPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="paymentPlanPageTitle">Payment Plan</span> - <span class="client-name" id="paymentPlanPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editPaymentPlanFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closePaymentPlanPageBtn" onclick="closePaymentPlanPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Payment Plan Details View -->
        <div id="paymentPlanDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="paymentPlanDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Payment Plan Edit/Add Form -->
        <div id="paymentPlanFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="paymentPlanDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deletePaymentPlan()">Delete</button>
                <button type="submit" form="paymentPlanPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closePaymentPlanFormBtn" onclick="closePaymentPlanPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="paymentPlanPageForm" method="POST" action="{{ route('payment-plans.store') }}">
              @csrf
              <div id="paymentPlanPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Payment Plan Modal (hidden, used for form structure) -->
  <div class="modal" id="paymentPlanModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="paymentPlanModalTitle">Add Payment Plan</h4>
        <button type="button" class="modal-close" onclick="closePaymentPlanModal()">×</button>
      </div>
      <form id="paymentPlanForm" method="POST" action="{{ route('payment-plans.store') }}">
        @csrf
        <div id="paymentPlanFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="schedule_id">Schedule *</label>
              <select class="form-control" name="schedule_id" id="schedule_id" required>
                <option value="">Select Schedule</option>
                @foreach($schedules as $schedule)
                  <option value="{{ $schedule->id }}">
                    {{ $schedule->policy->policy_no ?? 'N/A' }} - 
                    {{ $schedule->policy->client->client_name ?? 'N/A' }} - 
                    Schedule #{{ $schedule->schedule_no ?? $schedule->id }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="installment_label">Instalment Label</label>
              <input type="text" class="form-control" name="installment_label" id="installment_label" placeholder="e.g., Instalment 1 of 4">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="due_date">Due Date *</label>
              <input type="date" class="form-control" name="due_date" id="due_date" required>
            </div>
            <div class="form-group">
              <label for="amount">Amount *</label>
              <input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="frequency">Frequency</label>
              <select class="form-control" name="frequency" id="frequency">
                <option value="">Select Frequency</option>
                @if(isset($frequencies))
                  @foreach($frequencies as $freq)
                    <option value="{{ $freq->id }}">{{ $freq->name }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="form-group">
              <label for="status">Status *</label>
              <select class="form-control" name="status" id="status" required>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closePaymentPlanModal()">Cancel</button>
          <button type="button" class="btn-delete" id="paymentPlanDeleteBtn" style="display: none;" onclick="deletePaymentPlan()">Delete</button>
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Column Selection Modal -->
  <div class="modal" id="columnModal">
    <div class="modal-content column-modal-vertical">
      <div class="modal-header">
        <h4>Column Select & Sort</h4>
        <div class="modal-header-buttons">
          <button class="btn-save-orange" onclick="saveColumnSettings()">Save</button>
          <button class="btn-cancel-gray" onclick="closeColumnModal()">Cancel</button>
        </div>
      </div>
      <div class="modal-body">
        <form id="columnForm" action="{{ route('payment-plans.save-column-settings') }}" method="POST">
          @csrf
          @if(request()->has('client_id'))
            <input type="hidden" name="client_id" value="{{ request()->client_id }}">
          @endif
          <div class="column-selection-vertical" id="columnSelection">
            @php
              // Use the $columnDefinitions variable that's already set correctly (handles both fromClient and normal cases)
              $all = $columnDefinitions;
              // Maintain order based on selectedColumns
              $ordered = [];
              foreach($selectedColumns as $col) {
                if(isset($all[$col])) {
                  $ordered[$col] = $all[$col];
                  unset($all[$col]);
                }
              }
              $ordered = array_merge($ordered, $all);
              $counter = 1;
            @endphp

            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryColumns);
                $isChecked = in_array($key, $selectedColumns) || $isMandatory;
              @endphp
              <div class="column-item-vertical" draggable="true" data-column="{{ $key }}">
                <span class="column-number">{{ $counter }}</span>
                <label class="column-label-wrapper">
                  <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
                  <span class="column-label-text">{{ $label }}</span>
                </label>
              </div>
              @php $counter++; @endphp
            @endforeach
          </div>
          <div class="column-drag-hint">Drag and Select to position and display</div>
        </form>
      </div>
    </div>
  </div>

</div>



<script>
  // Initialize data from Blade - must be before partials-table-scripts
  // Note: mandatoryColumns is already declared in partials-table-scripts
  let currentPaymentPlanId = null;
  const selectedColumns = @json($selectedColumns);
  const paymentPlansStoreRoute = '{{ route("payment-plans.store") }}';
  const paymentPlansUpdateRouteTemplate = '{{ route("payment-plans.update", ":id") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/payment-plans-index.js') }}"></script>
@endsection
