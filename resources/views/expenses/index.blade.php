@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/expenses-index.css') }}">




@php
  $config = \App\Helpers\TableConfigHelper::getConfig('expenses');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('expenses');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:18px; font-weight:600;">
                  Expenses
              </h3>
              @include('partials.page-header-right')
      </div>
  </div>
  <!-- Main Expenses Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Expenses Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
           <div class="records-found">Records Found - {{ $expenses->total() }}</div>

      <div class="action-buttons">
        @if(auth()->check() && (auth()->user()->hasPermission('expenses.create') || auth()->user()->isAdmin()))
        <button class="btn btn-add" id="addExpenseBtn">Add</button>
        @endif
        <button class="btn btn-close" onclick="window.history.back()">Close</button>

      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger" id="errorAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #f5c6cb; background:#f8d7da; color:#721c24;">
        <ul style="margin:0; padding-left:20px;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="alert-close" onclick="document.getElementById('errorAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="expensesTable">
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
          @foreach($expenses as $expense)
            <tr class="{{ $expense->hasExpired ?? false ? 'has-expired' : ($expense->hasExpiring ?? false ? 'has-expiring' : '') }}">
              <td class="bell-cell {{ $expense->hasExpired ?? false ? 'expired' : ($expense->hasExpiring ?? false ? 'expiring' : '') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  @php
                    $isExpired = $expense->hasExpired ?? false;
                    $isExpiring = $expense->hasExpiring ?? false;
                  @endphp
                  <div class="status-indicator {{ $isExpired ? 'expired' : ($isExpiring ? 'expiring' : 'normal') }}" style="width:18px; height:18px; border-radius:50%; border:2px solid #000; background-color:{{ $isExpired ? '#dc3545' : ($isExpiring ? '#ffc107' : 'transparent') }};"></div>
                </div>
              </td>
              <td class="action-cell">
                @if(auth()->check() && (auth()->user()->hasPermission('expenses.view') || auth()->user()->hasPermission('expenses.edit') || auth()->user()->isAdmin()))
                <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openExpenseDetails({{ $expense->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
               
                @endif
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'expense_id')
                  <td data-column="expense_id">
                    {{ $expense->expense_id }}
                  </td>
                @elseif($col == 'payee')
                  <td data-column="payee">{{ $expense->payee ?? '-' }}</td>
                @elseif($col == 'date_paid')
                  <td data-column="date_paid">{{ $expense->date_paid ? $expense->date_paid->format('d-M-y') : '-' }}</td>
                @elseif($col == 'amount_paid')
                  <td data-column="amount_paid">{{ $expense->amount_paid ? number_format($expense->amount_paid, 2) : '-' }}</td>
                @elseif($col == 'description')
                  <td data-column="description">{{ $expense->description ?? '-' }}</td>
                @elseif($col == 'category_id')
                  <td data-column="category_id">{{ $expense->expenseCategory ? $expense->expenseCategory->name : '-' }}</td>
                @elseif($col == 'mode_of_payment')
                  <td data-column="mode_of_payment">{{ $expense->modeOfPayment ? $expense->modeOfPayment->name : '-' }}</td>
                @elseif($col == 'expense_notes')
                  <td data-column="expense_notes">{{ $expense->expense_notes ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

 
    </div>
    <div class="footer">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('expenses.export', array_merge(request()->query(), ['page' => $expenses->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $expenses->currentPage();
          $last = max(1, $expenses->lastPage());
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


  <!-- Expense Page View (Full Page) -->
  <div class="client-page-view" id="expensePageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="expensePageTitle">Expense</span> - <span class="client-name" id="expensePageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editExpenseFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeExpensePageBtn" onclick="closeExpensePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Expense Details View -->
        <div id="expenseDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="expenseDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Expense Edit/Add Form -->
        <div id="expenseFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="expenseDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteExpense()">Delete</button>
                <button type="submit" form="expensePageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeExpenseFormBtn" onclick="closeExpensePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="expensePageForm" method="POST" action="{{ route('expenses.store') }}">
              @csrf
              <div id="expensePageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Expense Modal -->
  <div class="modal" id="expenseModal">
    <div class="modal-content" style="max-width:800px; max-height:90vh; overflow-y:auto;">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd; background:#fff;">
        <h4 id="expenseModalTitle" style="margin:0; font-size:18px; font-weight:bold;">Add Expense</h4>
        <div style="display:flex; gap:10px;">
          <button type="submit" form="expenseForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeExpenseModal()" style="background:#ccc; color:#000; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Cancel</button>
      </div>
      </div>
      <form id="expenseForm" method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="expenseFormMethod" style="display:none;"></div>
        <input type="file" name="receipt" id="receiptFileInput" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
        <div class="modal-body" style="padding:20px;">
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="payee" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Payee</label>
              <input type="text" class="form-control" name="payee" id="payee" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="date_paid" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Date Paid</label>
              <input type="date" class="form-control" name="date_paid" id="date_paid" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="amount_paid" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Amount Paid</label>
              <input type="number" step="0.01" class="form-control" name="amount_paid" id="amount_paid" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="category_id" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Category</label>
              <select class="form-control" name="category_id" id="category_id" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
                <option value="">Select Category</option>
                @if(isset($expenseCategories))
                  @foreach($expenseCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="description" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Description</label>
              <input type="text" class="form-control" name="description" id="description" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="mode_of_payment_id" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Mode Of Payment</label>
              <select class="form-control" name="mode_of_payment_id" id="mode_of_payment_id" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
                <option value="">Select Mode Of Payment</option>
                @if(isset($modesOfPayment))
                  @foreach($modesOfPayment as $mode)
                    <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="receipt_no" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Receipt No.</label>
              <input type="text" class="form-control" name="receipt_no" id="receipt_no" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="expense_notes" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Expense Notes</label>
              <textarea class="form-control" name="expense_notes" id="expense_notes" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; resize:vertical;"></textarea>
            </div>
          </div>
          <div id="selectedReceiptPreview" style="margin-top:15px; padding:10px; background:#f5f5f5; border-radius:4px; display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div>
                <p style="margin:0; font-size:12px; color:#666; font-weight:500;">Selected Receipt:</p>
                <p id="selectedReceiptName" style="margin:5px 0 0 0; font-size:13px; color:#000;"></p>
        </div>
              <button type="button" onclick="removeSelectedReceipt()" style="background:#dc3545; color:#fff; border:none; padding:4px 10px; border-radius:2px; cursor:pointer; font-size:11px;">Remove</button>
            </div>
            <div id="selectedReceiptImagePreview" style="margin-top:10px; max-width:200px; max-height:200px;"></div>
          </div>
        </div>
        <div class="modal-footer" style="padding:15px 20px; border-top:1px solid #ddd; background:#fff; display:flex; justify-content:center;">
          <button type="button" class="btn-upload" onclick="openReceiptUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Receipt</button>
          <button type="button" class="btn-delete" id="expenseDeleteBtnModal" style="display: none; background:#dc3545; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px; margin-left:10px;" onclick="deleteExpense()">Delete</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Receipt Upload Modal -->
  <div class="modal" id="receiptUploadModal">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-header">
        <h4>Select Receipt</h4>
        <button type="button" class="modal-close" onclick="closeReceiptUploadModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="receiptFile">Select Receipt File</label>
          <input type="file" class="form-control" name="receipt" id="receiptFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" onchange="handleReceiptFileSelect(event)">
          <small style="color:#666; font-size:11px;">Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max 5MB)</small>
        </div>
        <div id="receiptPreview" style="margin-top:15px; display:none;">
          <p style="font-size:12px; color:#666; font-weight:500;">Preview:</p>
          <div id="receiptPreviewContent" style="margin-top:10px;"></div>
        </div>
        <div id="existingReceiptPreview" style="margin-top:15px; display:none;">
          <p style="font-size:12px; color:#666;">Current receipt:</p>
          <div id="existingReceiptPreviewContent"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeReceiptUploadModal()">Cancel</button>
        <button type="button" class="btn-save" onclick="confirmReceiptSelection()">Select</button>
      </div>
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
        <form id="columnForm" action="{{ route('expenses.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection-vertical" id="columnSelection">
            @php
              $all = $config['column_definitions'];
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
  let currentExpenseId = null;
  const lookupData = @json($lookupData ?? []);
  const selectedColumns = @json($selectedColumns);
  const canDeleteExpense = @json(auth()->check() && (auth()->user()->hasPermission('expenses.delete') || auth()->user()->isAdmin()));
  const canEditExpense = @json(auth()->check() && (auth()->user()->hasPermission('expenses.edit') || auth()->user()->isAdmin()));
  const expensesStoreRoute = '{{ route("expenses.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/expenses-index.js') }}"></script>
@endsection

</html>
</html>