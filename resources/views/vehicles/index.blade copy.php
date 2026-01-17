@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/vehicles-index.css') }}">




@php
  $config = \App\Helpers\TableConfigHelper::getConfig('vehicles');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('vehicles');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">

  <!-- Main Vehicles Table View -->
  <div class="clients-table-view" id="clientsTableView">
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
          @if($policy)
            {{ $policy->policy_no }} - 
          @endif
            Vehicles
            @if(isset($client) && $client)
              <span class="client-name" style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $client->client_name }}</span>
            @endif
          </h3>
       
      </div>
    </div>
  <div class="container-table">
    <!-- Vehicles Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
    
        <div class="records-found">Records Found - {{ $vehicles->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            <div class="filter-toggle">
              <label class="toggle-switch">
                <input type="checkbox" id="filterToggle" onchange="toggleFilter()">
                <span class="toggle-slider"></span>
              </label>
              <span style="font-size:13px; color:#555;">Filter</span>
            </div>
          </div>
        </div>
      </div>
      <div class="action-buttons">
          @if(isset($policy) && $policy)
          <button class="btn btn-add" id="addVehicleBtn">Add</button>
          @endif
        <a href="{{ $policy ? route('policies.show', $policy->id) : route('policies.index') }}" class="btn" style="background:#6c757d; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; text-decoration:none; font-size:13px;">Back</a>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="vehiclesTable">
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
          @foreach($vehicles as $vh)
            @php
              $hasNoPolicy = empty($vh->policy_id);
            @endphp
            <tr>
            <td class="bell-cell {{ $hasNoPolicy ? 'no-policy' : '' }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  <div class="status-indicator {{ $hasNoPolicy ? 'no-policy' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $hasNoPolicy ? '#555' : '#f3742a' }}; background-color:{{ $hasNoPolicy ? '#555' : 'transparent' }};"></div>
                </div>
              </td>
              <td class="action-cell"><!-- onclick="openVehicleDetails({{ $vh->id }})" -->
                <svg class="action-expand" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
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
                @if($col == 'regn_no')
                  <td data-column="regn_no">
                    <a href="javascript:void(0)" onclick="openVehicleDetails({{ $vh->id }})" style="color:#007bff; text-decoration:underline;">{{ $vh->regn_no }}</a>
                  </td>
                @elseif($col == 'make')
                  <td data-column="make">{{ $vh->make ?? '-' }}</td>
                @elseif($col == 'model')
                  <td data-column="model">{{ $vh->model ?? '-' }}</td>
                @elseif($col == 'type')
                  <td data-column="type">{{ $vh->type ?? '-' }}</td>
                @elseif($col == 'useage')
                  <td data-column="useage">{{ $vh->useage ?? '-' }}</td>
                @elseif($col == 'year')
                  <td data-column="year">{{ $vh->year ?? '-' }}</td>
                @elseif($col == 'value')
                  <td data-column="value">{{ $vh->value ? number_format($vh->value, 2) : '-' }}</td>
                @elseif($col == 'policy_id')
                  <td data-column="policy_id">{{ $vh->policy_id ?? '-' }}</td>
                @elseif($col == 'engine')
                  <td data-column="engine">{{ $vh->engine ?? '-' }}</td>
                @elseif($col == 'engine_type')
                  <td data-column="engine_type">{{ $vh->engine_type ?? '-' }}</td>
                @elseif($col == 'cc')
                  <td data-column="cc">{{ $vh->cc ?? '-' }}</td>
                @elseif($col == 'engine_no')
                  <td data-column="engine_no">{{ $vh->engine_no ?? '-' }}</td>
                @elseif($col == 'chassis_no')
                  <td data-column="chassis_no">{{ $vh->chassis_no ?? '-' }}</td>
                @elseif($col == 'from')
                  <td data-column="from">{{ $vh->from ? \Carbon\Carbon::parse($vh->from)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'to')
                  <td data-column="to">{{ $vh->to ? \Carbon\Carbon::parse($vh->to)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'notes')
                  <td data-column="notes">{{ $vh->notes ?? '-' }}</td>
                @elseif($col == 'vehicle_id')
                  <td data-column="vehicle_id">{{ $vh->vehicle_id ?? '-' }}</td>
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
        <a class="btn btn-export" href="{{ route('vehicles.export') }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
        <button class="btn btn-export" id="printBtn" type="button" style="margin-left:10px;">Print</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $vehicles->currentPage();
          $last = max(1, $vehicles->lastPage());
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

  <!-- Vehicle Page View (Full Page) -->
  <div class="client-page-view" id="vehiclePageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="vehiclePageTitle">Vehicle</span> - <span class="client-name" id="vehiclePageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editVehicleFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeVehiclePageBtn" onclick="closeVehiclePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Vehicle Details View -->
        <div id="vehicleDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="vehicleDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Vehicle Edit/Add Form -->
        <div id="vehicleFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="vehicleDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteVehicle()">Delete</button>
                <button type="submit" form="vehiclePageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeVehicleFormBtn" onclick="closeVehiclePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="vehiclePageForm" method="POST" action="{{ route('vehicles.store') }}">
              @csrf
              <div id="vehiclePageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Vehicle Modal (hidden, used for form structure) -->

  <!-- Add/Edit Vehicle Modal (hidden, used for form structure) -->
<div class="modal" id="vehicleModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="vehicleModalTitle">Add Vehicle</h4>
        <button type="button" class="modal-close" onclick="closeVehicleModal()">×</button>
      </div>
      <form id="vehicleForm" method="POST" action="{{ route('vehicles.store') }}">
        @csrf
        <div id="vehicleFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="regn_no">Regn No *</label>
              <input type="text" class="form-control" name="regn_no" id="regn_no" required>
            </div>
            <div class="form-group">
              <label for="make">Make</label>
              <input type="text" class="form-control" name="make" id="make">
            </div>
            <div class="form-group">
              <label for="model">Model</label>
              <input type="text" class="form-control" name="model" id="model">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="type">Type</label>
              <input type="text" class="form-control" name="type" id="type">
            </div>
            <div class="form-group">
              <label for="useage">Usage</label>
              <input type="text" class="form-control" name="useage" id="useage">
            </div>
            <div class="form-group">
              <label for="year">Year</label>
              <input type="text" class="form-control" name="year" id="year">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="value">Value</label>
              <input type="number" step="0.01" class="form-control" name="value" id="value">
            </div>
            <div class="form-group">
              <label for="policy_id">Policy ID</label>
              <input type="text" class="form-control" name="policy_id" id="policy_id">
            </div>
            <div class="form-group">
              <label for="engine">Engine</label>
              <input type="text" class="form-control" name="engine" id="engine">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="engine_type">Engine Type</label>
              <input type="text" class="form-control" name="engine_type" id="engine_type">
            </div>
            <div class="form-group">
              <label for="cc">CC</label>
              <input type="text" class="form-control" name="cc" id="cc">
            </div>
            <div class="form-group">
              <label for="engine_no">Engine No</label>
              <input type="text" class="form-control" name="engine_no" id="engine_no">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="chassis_no">Chassis No</label>
              <input type="text" class="form-control" name="chassis_no" id="chassis_no">
            </div>
            <div class="form-group">
              <label for="from">From</label>
              <input type="date" class="form-control" name="from" id="from">
            </div>
            <div class="form-group">
              <label for="to">To</label>
              <input type="date" class="form-control" name="to" id="to">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="notes">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeVehicleModal()">Cancel</button>
          <button type="button" class="btn-delete" id="vehicleDeleteBtn" style="display: none;" onclick="deleteVehicle()">Delete</button>
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

        <form id="columnForm" action="{{ route('vehicles.save-column-settings') }}" method="POST">
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
  let currentVehicleId = null;
  const selectedColumns = @json($selectedColumns);
  const vehiclesStoreRoute = '{{ route("vehicles.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/vehicles-index.js') }}"></script>
@endsection
