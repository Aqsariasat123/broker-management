@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('statements');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('statements');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp
<style>
  .statement-container {
    font-family: Arial, Helvetica, sans-serif;
    overflow: hidden;
  }

  .statement-header {
    color: #000;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 18px;
    font-weight: bold;
  }

  .header-buttons button {
    padding: 8px 18px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    margin-left: 8px;
  }

  .btn-edit { background: #ff6200; color: white; }
  .btn-close { background: #e0e0e0; color: #333; }

  .summary-title {
    background: #000;
    color: white;
    padding: 10px 20px;
    font-weight: bold;
    font-size: 16px;
  }

  .summary-bar {
    color: #000;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: nowrap;
    overflow-x: auto;
    font-size: 14px;
    border-bottom: 1px solid #ddd;
  }

  .summary-item {
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
  }

  .summary-label {
    font-weight: normal;
  }

  .summary-input {
    background: white;
    color: #333;
    border: 1px solid #ccc;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 14px;
    min-width: 120px;
  }

  .summary-value {
      color: #333;
    padding: 6px 0;
    font-size: 14px;
    min-width: 100px;
  }

  .details-title {
    background: white;
    color: #000;
    padding: 15px 20px 10px;
    font-weight: bold;
    font-size: 18px;
    border-bottom: 1px solid #ddd;
  }

  .details-table {
    width: 100%;
    border-collapse: collapse;
  }


  .details-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #eee;
  }

  .details-table tr:nth-child(even) {
    background: #f9f9f9;
  }

  .variance-reason-cell {
    background: #e8f5e9;
    color: #2e7d32;
    font-weight: bold;
    border: 2px solid #4caf50;
  }
  /* HOVER STATES - This is the fix! */

</style>
<style>
/* Custom Toggle Switch - Green when ON */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
  margin-right: 8px;
}

.switch input { opacity: 0; width: 0; height: 0; }

.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

input:checked + .slider {
  background-color: #28a745;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

/* Filter Buttons Base Style */
.filter-btn {
  background: #000;
  color: #fff;
  border: none;
  padding: 6px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 13px;
  font-weight: normal;
  transition: background-color 0.2s ease;
}

/* Show All Active - Green */
.filter-btn.active-all {
  background: #28a745 !important;
}

/* Selected Insurer - Blue */
.filter-btn.active-insurer {
  background: #2e7d32 !important;
}

/* HOVER STATES - This is the fix! */

/* Hover on inactive buttons (black → dark gray) */
.filter-btn:hover:not(.active-all):not(.active-insurer) {
  background: #333;
}


/* Hover on selected insurer when active (blue → darker blue) */
.filter-btn.active-insurer:hover {
  background: #2e7d32 !important; /* Darker blue */
}
</style>
<div class="dashboard">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
               <h3 style="margin:0; font-size:18px; font-weight:600;">
                  Statements
              </h3>
      </div>
  </div>
  <!-- Main Statements Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Statements Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
                <div class="records-found">Records Found - {{ $statements->total() }}</div>

      <div class="page-title-section">
        
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
             <label class="switch">
          <input type="checkbox"
                id="insurerFilterToggle"
                {{ ( request()->filled('insurer') ) ? 'checked' : '' }}>
          <span class="slider round"></span>
              </label>

        <span style="font-size:13px; font-weight:normal;">Filter</span>

            @foreach($insurers as $soruceBtn)
                  @php $isActive = request()->get('insurer') ===  $soruceBtn->name; @endphp

              <button class="btn filter-btn {{ $isActive ? 'active-insurer' : '' }}" 
               onclick="filterByInsurer('{{ $soruceBtn->name }}')"  >
              {{ $soruceBtn->name }}  
            </button>
            @endforeach
      
          </div>
        </div>
      </div>
        @if(isset($client) && $client)
      <div class="action-buttons">
        <button class="btn btn-add" id="addStatementBtn">Add</button>
      </div>
      @endif
  @if(isset($pageType) && $pageType =='commission')
      <div class="action-buttons">
        <button class="btn btn-add" id="addStatementBtn">Add</button>
      </div>
      @endif
      
        <div class="action-buttons">
              <button class="btn btn-close" onclick="window.history.back()">Close</button>
            </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="statementsTable">
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
          @foreach($statements as $st)
            <tr>
              <td class="action-cell">
             <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openStatementDetails({{ $st->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">

              
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'statement_no')
                  <td data-column="statement_no">
                      {{ $st->com_stat_id  }}
                  </td>
                @elseif($col == 'year')
                @php $commission = $st->commissions->first(); @endphp

                  <td data-column="year">{{ $commission?->date_received ? \Carbon\Carbon::parse($commission->date_received)->format('Y') : '-' }}</td>
                @elseif($col == 'insurer')
                @php
    $commission = $st->commissions->first();
    $insurerName = $commission?->commissionNote?->schedule?->policy?->insurer?->name ?? '-';
@endphp
                   <td data-column="insurer">
    {{ $insurerName }}
</td>
                @elseif($col == 'business_category')
                @php
                  $commission = $st->commissions->first();
    $businessCategory = $commission?->commissionNote?->schedule?->policy?->policyClass?->name
                @endphp
                  <td data-column="business_category">{{ $businessCategory ?? '-' }}</td>
                @elseif($col == 'date_received')
                  <td data-column="date_received">
                    {{ optional($st->commissions->first())->date_received ? \Carbon\Carbon::parse($st->commissions->first()->date_received)->format('d-M-y') : '-' }}
                </td>
                @elseif($col == 'amount_received')
                    <td data-column="amount_received">
                           {{ number_format(optional($st->commissions->first())->amount_received ?? 0, 2) ?: '-' }}

                    </td>

                @elseif($col == 'mode_of_payment')
                  <td data-column="mode_of_payment">
                      {{ $st->commissions->first()->modeOfPayment->name ?? '-' }}
                  </td>
                @elseif($col == 'remarks')
                  <td data-column="remarks">{{ $st->remarks ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
      <div class="footer-left" style="display:flex; gap:8px;">
        <a class="btn btn-export" href="{{ route('statements.export', array_merge(request()->query(), ['page' => $statements->currentPage()])) }}" style="background:#fff; border:1px solid #ddd; padding:6px 16px; border-radius:2px; cursor:pointer; text-decoration:none; color:#333;">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button" style="background:#fff; border:1px solid #ddd; padding:6px 16px; border-radius:2px; cursor:pointer;">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $statements->currentPage();
          $last = max(1, $statements->lastPage());
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

  <!-- Statement Page View (Full Page) -->
  <div class="client-page-view" id="statementPageView" style="display:none;">
     <div style="background:white;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="statementPageTitle">Statement</span> - <span class="client-name" id="statementPageName"></span>
      </div>
      
     
      <div class="client-page-actions">
        <!-- <button class="btn btn-edit" id="editStatementFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button> -->
        <button class="btn" id="closeStatementPageBtn" onclick="closeStatementPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body" style="padding-top:0px !important;">
      <div class="client-page-content">
        <!-- Statement Details View -->
        <div id="statementDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="statementDetailsContent" style="display:grid; grid-template-columns:repeat(1, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Statement Edit/Add Form -->
        <div id="statementFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="statementDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteStatement()">Delete</button>
                <button type="submit" form="statementPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeStatementFormBtn" onclick="closeStatementPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="statementPageForm" method="POST" action="{{ route('statements.store') }}">
              @csrf
              <div id="statementPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
      </div>
  </div>

  <!-- Add/Edit Statement Modal (hidden, used for form structure) -->
  <div class="modal" id="statementModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="statementModalTitle">Add Statement</h4>
        <button type="button" class="modal-close" onclick="closeStatementModal()">×</button>
      </div>
      <form id="statementForm" method="POST" action="{{ route('statements.store') }}">
        @csrf
        <div id="statementFormMethod" style="display:none;"></div>
        <div class="modal-body">
      
          <div class="form-row">
            <div class="form-group">
              <label for="period_start">Period Start</label>
              <input type="date" class="form-control" name="period_start" id="period_start">
            </div>
            <div class="form-group">
              <label for="period_end">Period End</label>
              <input type="date" class="form-control" name="period_end" id="period_end">
            </div>
              <div class="form-group">
              <label for="net_comission">Net  Comission</label>
              <input type="number" class="form-control" name="net_comission" id="net_comission">
            </div>
              <div class="form-group">
              <label for="tax_withheld">Tax withheld</label>
              <input type="number" class="form-control" name="tax_withheld" id="tax_withheld">
            </div>
         
          </div>
          <div class="form-row">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="remarks">Remarks</label>
              <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeStatementModal()">Cancel</button>
          <button type="button" class="btn-delete" id="statementDeleteBtn" style="display: none;" onclick="deleteStatement()">Delete</button>
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
        <form id="columnForm" action="{{ route('statements.save-column-settings') }}" method="POST">
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
  const lookupData = {
    insurers: @json($insurers ?? []),
    modesOfPayment: @json($modesOfPayment ?? [])
  };
  const selectedColumns = @json($selectedColumns ?? []);
  const statementsStoreRoute = '{{ route("statements.store") }}';
  const statementsIndexRoute = '{{ route("statements.index") }}';
  const statementsUpdateRouteTemplate = '{{ route("statements.update", ":id") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/statements-index.js') }}?v={{ time() }}"></script>
@endsection
