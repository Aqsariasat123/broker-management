@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('debit-notes');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('debit-notes');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="page-title-section">
              <h3 style="margin:0; font-size:18px; font-weight:600;">
                  Debit Notes
              </h3>
           </div>
      </div>
  </div>
  <!-- Main Debit Notes Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Debit Notes Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <div class="records-found">Records Found - {{ $debitNotes->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            <form method="GET" action="{{ route('debit-notes.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
              <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
              <select name="status" style="padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px;">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
              <button type="submit" class="btn btn-column" style="background:#fff; color:#000; border:1px solid #ccc;">Filter</button>
              @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('debit-notes.index') }}" class="btn btn-back" style="background:#ccc; color:#333; border-color:#ccc;">Clear</a>
              @endif
            </form>
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addDebitNoteBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="debitNotesTable">
        <thead>
          <tr>
            <th>Action</th>
            @foreach($selectedColumns as $col)
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($debitNotes as $note)
            <tr>
              <td class="action-cell">
                <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openDebitNoteDetails({{ $note->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">  

               
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'debit_note_no')
                  <td data-column="debit_note_no">
                  {{ $note->debit_note_no }}
                  </td>
                @elseif($col == 'policy_no')
                  <td data-column="policy_no">{{ $note->paymentPlan->schedule->policy->policy_no ?? '-' }}</td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $note->paymentPlan->schedule->policy->client->client_name ?? '-' }}</td>
                @elseif($col == 'issued_on')
                  <td data-column="issued_on">{{ $note->issued_on ? $note->issued_on->format('d-M-y') : '-' }}</td>
                @elseif($col == 'amount')
                  <td data-column="amount">{{ $note->amount ? number_format($note->amount, 2) : '-' }}</td>
                @elseif($col == 'status')
                  <td data-column="status">
                    <span class="badge-status badge-{{ $note->status }}" style="font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; background:{{ $note->status == 'pending' ? '#ffc107' : ($note->status == 'issued' ? '#17a2b8' : ($note->status == 'paid' ? '#28a745' : ($note->status == 'overdue' ? '#dc3545' : '#6c757d'))) }};">
                      {{ ucfirst($note->status) }}
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
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $debitNotes->currentPage();
          $last = max(1, $debitNotes->lastPage());
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

  <!-- Debit Note Page View (Full Page) -->
  <div class="client-page-view" id="debitNotePageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="debitNotePageTitle">Debit Note</span> - <span class="client-name" id="debitNotePageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editDebitNoteFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeDebitNotePageBtn" onclick="closeDebitNotePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Debit Note Details View -->
        <div id="debitNoteDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="debitNoteDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Debit Note Edit/Add Form -->
        <div id="debitNoteFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="debitNoteDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteDebitNote()">Delete</button>
                <button type="submit" form="debitNotePageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeDebitNoteFormBtn" onclick="closeDebitNotePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="debitNotePageForm" method="POST" action="{{ route('debit-notes.store') }}" enctype="multipart/form-data">
              @csrf
              <div id="debitNotePageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Debit Note Modal (hidden, used for form structure) -->
  <div class="modal" id="debitNoteModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="debitNoteModalTitle">Add Debit Note</h4>
        <button type="button" class="modal-close" onclick="closeDebitNoteModal()">×</button>
      </div>
      <form id="debitNoteForm" method="POST" action="{{ route('debit-notes.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="debitNoteFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="payment_plan_id">Payment Plan *</label>
              <select class="form-control" name="payment_plan_id" id="payment_plan_id" required>
                <option value="">Select Payment Plan</option>
                @foreach($paymentPlans as $plan)
                  <option value="{{ $plan->id }}">
                    {{ $plan->schedule->policy->policy_no ?? 'N/A' }} - 
                    {{ $plan->schedule->policy->client->client_name ?? 'N/A' }} - 
                    {{ $plan->installment_label ?? 'Instalment #' . $plan->id }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="debit_note_no">Debit Note No *</label>
              <input type="text" class="form-control" name="debit_note_no" id="debit_note_no" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="issued_on">Issued On *</label>
              <input type="date" class="form-control" name="issued_on" id="issued_on" required>
            </div>
            <div class="form-group">
              <label for="amount">Amount *</label>
              <input type="number" step="0.01" min="0" class="form-control" name="amount" id="amount" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="status">Status *</label>
              <select class="form-control" name="status" id="status" required>
                <option value="pending">Pending</option>
                <option value="issued">Issued</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="form-group">
              <label for="document">Document</label>
              <input type="file" class="form-control" name="document" id="document" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
            </div>
          </div>
          <div id="existingDocumentPreview" style="margin-top:15px; padding:10px; background:#f5f5f5; border-radius:4px; display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div>
                <p style="margin:0; font-size:12px; color:#666; font-weight:500;">Current Document:</p>
                <div id="existingDocumentPreviewContent" style="margin-top:5px;"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeDebitNoteModal()">Cancel</button>
          <button type="button" class="btn-delete" id="debitNoteDeleteBtn" style="display: none;" onclick="deleteDebitNote()">Delete</button>
          <button type="submit" class="btn-save">Save</button>
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

        <form id="columnForm" action="{{ route('debit-notes.save-column-settings') }}" method="POST">
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
  // Note: mandatoryColumns is already declared in partials-table-scripts
  let currentDebitNoteId = null;
  const selectedColumns = @json($selectedColumns);
  const debitNotesStoreRoute = '{{ route("debit-notes.store") }}';
  const debitNotesUpdateRouteTemplate = '{{ route("debit-notes.update", ":id") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/debit-notes-index.js') }}"></script>
@endsection
