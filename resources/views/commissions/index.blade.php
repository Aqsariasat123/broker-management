@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('commissions');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('commissions');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="page-title-section">
              <h3 style="margin:0; font-size:18px; font-weight:600;">
              @if($policy)
                {{ $policy->policy_code }} - 
              @endif
          Commissions
              </h3>
           </div>
      </div>
  </div>
  <!-- Main Commissions Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Commissions Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <div class="records-found">Records Found - {{ $commissions->total() }}</div>
        <!-- <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            @foreach(['SACOS','Alliance','Hsavy','MUA'] as $insurerBtn)
              <button class="btn btn-column" onclick="filterByInsurer('{{ $insurerBtn }}')" style="margin-left:5px;{{ isset($insurerFilter) && $insurerFilter==$insurerBtn ? 'background:#007bff;color:#fff;' : '' }}">{{ $insurerBtn }}</button>
            @endforeach
            <button class="btn btn-back" onclick="window.location.href='{{ route('commissions.index') }}'">All</button>
          </div>
        </div> -->
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
  <div class="filter-group" style="display:flex; align-items:center; gap:12px;">

    <!-- Custom Toggle Switch -->
    <label class="switch">
      <input type="checkbox" id="insurerFilterToggle" {{ request()->has('insurer') ? 'checked' : '' }}>
      <span class="slider round"></span>
    </label>

    <span style="font-size:13px; font-weight:normal;">Filter</span>

    <!-- Show All Button (green when no filter) -->
    @php $hasInsurer = request()->has('insurer'); @endphp
    <button class="btn filter-btn {{ !$hasInsurer ? 'active-all' : '' }}" 
            type="button" 
            onclick="filterByInsurer()">
      Show All
    </button>

    <!-- Insurer Buttons -->
    @foreach(['SACOS', 'Alliance', 'Hsavy', 'MUA'] as $insurerBtn)
      @php $isActive = request()->get('insurer') === $insurerBtn; @endphp
      <button class="btn filter-btn {{ $isActive ? 'active-insurer' : '' }}" 
              type="button"
              onclick="filterByInsurer('{{ $insurerBtn }}')">
        {{ $insurerBtn }}
      </button>
    @endforeach


  </div>
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
  background: #007bff !important;
}

/* HOVER STATES - This is the fix! */

/* Hover on inactive buttons (black → dark gray) */
.filter-btn:hover:not(.active-all):not(.active-insurer) {
  background: #333;
}

/* Hover on "Show All" when active (green → darker green) */
.filter-btn.active-all:hover {
  background: #218838 !important; /* Darker green */
}

/* Hover on selected insurer when active (blue → darker blue) */
.filter-btn.active-insurer:hover {
  background: #0056b3 !important; /* Darker blue */
}
</style>
</div>
      </div>
      
      @if($policy)
          <div class="action-buttons">
            <button class="btn btn-add" id="addCommissionBtn">Add</button>
          </div>
      @endif
   
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="commissionsTable">
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
          @foreach($commissions as $com)
            <tr>
              <td class="action-cell">
                <svg class="action-expand" onclick="openCommissionDetails({{ $com->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <!-- Maximize icon: four arrows pointing outward from center -->
                  <!-- Top arrow -->
                  <path d="M12 2L12 8M12 2L10 4M12 2L14 4" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <!-- Right arrow -->
                  <path d="M22 12L16 12M22 12L20 10M22 12L20 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <!-- Bottom arrow -->
                  <path d="M12 22L12 16M12 22L10 20M12 22L14 20" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <!-- Left arrow -->
                  <path d="M2 12L8 12M2 12L4 10M2 12L4 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'policy_number')
                  <td data-column="policy_number">
                    <a href="javascript:void(0)" onclick="openCommissionDetails({{ $com->id }})" style="color:#007bff; text-decoration:underline;">{{ $com->policy_number }}</a>
                  </td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $com->client_name }}</td>
                @elseif($col == 'insurer')
                  <td data-column="insurer">{{ $com->insurer ? $com->insurer->name : '-' }}</td>
                @elseif($col == 'grouping')
                  <td data-column="grouping">{{ $com->grouping ?? '-' }}</td>
                @elseif($col == 'basic_premium')
                  <td data-column="basic_premium">{{ $com->basic_premium ? number_format($com->basic_premium, 2) : '-' }}</td>
                @elseif($col == 'rate')
                  <td data-column="rate">{{ $com->rate ? number_format($com->rate, 2) : '-' }}</td>
                @elseif($col == 'amount_due')
                  <td data-column="amount_due">{{ $com->amount_due ? number_format($com->amount_due, 2) : '-' }}</td>
                @elseif($col == 'payment_status')
                  <td data-column="payment_status">{{ $com->paymentStatus ? $com->paymentStatus->name : '-' }}</td>
                @elseif($col == 'amount_rcvd')
                  <td data-column="amount_rcvd">{{ $com->amount_rcvd ? number_format($com->amount_rcvd, 2) : '-' }}</td>
                @elseif($col == 'date_rcvd')
                  <td data-column="date_rcvd">{{ $com->date_rcvd ? $com->date_rcvd->format('d-M-y') : '-' }}</td>
                @elseif($col == 'state_no')
                  <td data-column="state_no">{{ $com->state_no ?? '-' }}</td>
                @elseif($col == 'mode_of_payment')
                  <td data-column="mode_of_payment">{{ $com->modeOfPayment ? $com->modeOfPayment->name : '-' }}</td>
                @elseif($col == 'variance')
                  <td data-column="variance">{{ $com->variance ? number_format($com->variance, 2) : '-' }}</td>
                @elseif($col == 'reason')
                  <td data-column="reason">{{ $com->reason ?? '-' }}</td>
                @elseif($col == 'date_due')
                  <td data-column="date_due">{{ $com->date_due ? $com->date_due->format('d-M-y') : '-' }}</td>
                @elseif($col == 'cnid')
                  <td data-column="cnid">
                    <a href="javascript:void(0)" onclick="openCommissionDetails({{ $com->id }})" style="color:#007bff; text-decoration:underline;">{{ $com->cnid }}</a>
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
        <a class="btn btn-export" href="{{ route('commissions.export', array_merge(request()->query(), ['page' => $commissions->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $commissions->currentPage();
          $last = max(1, $commissions->lastPage());
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

  <!-- Commission Page View (Full Page) -->
  <div class="client-page-view" id="commissionPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="commissionPageTitle">Commission</span> - <span class="client-name" id="commissionPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editCommissionFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeCommissionPageBtn" onclick="closeCommissionPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Commission Details View -->
        <div id="commissionDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="commissionDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Commission Edit/Add Form -->
        <div id="commissionFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="commissionDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteCommission()">Delete</button>
                <button type="submit" form="commissionPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeCommissionFormBtn" onclick="closeCommissionPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="commissionPageForm" method="POST" action="{{ route('commissions.store') }}">
              @csrf
              <div id="commissionPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Commission Modal (hidden, used for form structure) -->
  <div class="modal" id="commissionModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="commissionModalTitle">Add Commission</h4>
        <button type="button" class="modal-close" onclick="closeCommissionModal()">×</button>
      </div>
      <form id="commissionForm" method="POST" action="{{ route('commissions.store') }}">
        @csrf
        <div id="commissionFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
          <div class="form-group">
              <label for="client_name">Com Note no</label>
              <input type="text" class="form-control" name="com_note_no" id="com_note_no" readonly>
            </div>
            <div class="form-group">

              <label for="policy_id">Policy Number</label>
              <select class="form-control" name="policy_id" id="policy_id">
                <option value="">Select</option>
                @foreach($policies as $policy)
                  <option value="{{ $policy->id }}">{{ $policy->policy_code }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="basic_premium">Basic Premium</label>
              <input type="number" step="0.01" class="form-control" name="basic_premium" id="basic_premium">
            </div>
       
          </div>
          
          <div class="form-row">
          <div class="form-group">
              <label for="rate">Rate</label>
              <input type="number" step="0.01" class="form-control" name="rate" id="rate">
            </div>
            <div class="form-group">
              <label for="amount_due">Amount Due</label>
              <input type="number" step="0.01" class="form-control" name="amount_due" id="amount_due">
            </div>
            <div class="form-group">
              <label for="date_due">Date Due</label>
              <input type="date" class="form-control" name="date_due" id="date_due">
            </div>
        
          </div>
          <div class="form-row">
          <div class="form-group">
              <label for="payment_status_id"> Status</label>
              <select class="form-control" name="payment_status_id" id="payment_status_id">
                <option value="">Select</option>
                @foreach($paymentStatuses as $ps)
                  <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                @endforeach
              </select>
            </div>
          
          <div class="form-group">
              <label for="amount_rcvd">Amount Rcvd</label>
              <input type="number" step="0.01" class="form-control" name="amount_rcvd" id="amount_rcvd">
            </div>
            <div class="form-group">
              <label for="date_rcvd">Date Rcvd</label>
              <input type="date" class="form-control" name="date_rcvd" id="date_rcvd">
            </div>
            <div class="form-group">
              <label for="mode_of_payment_id">Mode</label>
              <select class="form-control" name="mode_of_payment_id" id="mode_of_payment_id">
                <option value="">Select</option>
                @foreach($modesOfPayment as $mode)
                  <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="state_no">Statement No</label>
              <input type="text" class="form-control" name="state_no" id="state_no">
            </div>
         
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="comission_notes" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Comission  Notes</label>
              <textarea class="form-control" name="comission_notes" id="comission_notes" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; resize:vertical;"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeCommissionModal()">Cancel</button>
          <button type="button" class="btn-delete" id="commissionDeleteBtn" style="display: none;" onclick="deleteCommission()">Delete</button>
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

        <form id="columnForm" action="{{ route('commissions.save-column-settings') }}" method="POST">
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
  let currentCommissionId = null;
  const lookupData = {
    insurers: @json($insurers ?? []),
    paymentStatuses: @json($paymentStatuses ?? []),
    modesOfPayment: @json($modesOfPayment ?? [])
  };
  const selectedColumns = @json($selectedColumns ?? []);
  const commissionsStoreRoute = '{{ route("commissions.store") }}';
  const commissionsIndexRoute = '{{ route("commissions.index") }}';
  const commissionsUpdateRouteTemplate = '{{ route("commissions.update", ":id") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/commissions-index.js') }}"></script>
@endsection
