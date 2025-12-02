<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Vehicles</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; color: #000; margin: 10px; background: #fff; }
    .container-table { max-width: 100%; margin: 0 auto; }
    h3 { background: #f1f1f1; padding: 8px; margin-bottom: 10px; font-weight: bold; border: 1px solid #ddd; }
    .top-bar { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
    .left-group { display:flex; align-items:center; gap:10px; flex:1 1 auto; min-width:220px; }
    .records-found { font-size:14px; color:#555; min-width:150px; }
    .action-buttons { margin-left:auto; display:flex; gap:10px; }
    .btn { border:none; cursor:pointer; padding:6px 12px; font-size:13px; border-radius:2px; white-space:nowrap; transition:background-color .2s; text-decoration:none; color:inherit; background:#fff; border:1px solid #ccc; }
    .btn-add { background:#df7900; color:#fff; border-color:#df7900; }
    .btn-export, .btn-column { background:#fff; color:#000; border:1px solid #ccc; }
    .btn-back { background:#ccc; color:#333; border-color:#ccc; }
    .table-responsive { width: 100%; overflow-x: auto; border: 1px solid #ddd; max-height: 520px; overflow-y: auto; background: #fff; }
    .footer { display:flex; justify-content:center; align-items:center; padding:5px 0; gap:10px; border-top:1px solid #ccc; flex-wrap:wrap; margin-top:15px; }
    .paginator {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: #555;
      white-space: nowrap;
      justify-content: center;
    }
    .btn-page{
      color: #2d2d2d;
      font-size: 25px;
      width: 22px;
      height: 50px;
      padding: 5px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    table { width:100%; border-collapse:collapse; font-size:13px; min-width:900px; }
    thead tr { background-color: black; color: white; height:35px; font-weight: normal; }
    thead th { padding:6px 5px; text-align:left; border-right:1px solid #444; white-space:nowrap; font-weight: normal; }
    thead th:last-child { border-right:none; }
    tbody tr { background-color:#fefefe; border-bottom:1px solid #ddd; min-height:28px; }
    tbody tr:nth-child(even) { background-color:#f8f8f8; }
    tbody tr.inactive-row { background:#fff3cd !important; }
    tbody td { padding:5px 5px; border-right:1px solid #ddd; white-space:nowrap; vertical-align:middle; font-size:12px; }
    tbody td:last-child { border-right:none; }
    .icon-expand { cursor:pointer; color:black; text-align:center; width:20px; }
    .btn-action { padding:2px 6px; font-size:11px; margin:1px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:2px; display:inline-block; }
    .badge-status { font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; }
    /* Modal styles */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; }
    .modal.show { display:flex; }
    .modal-content { background:#fff; border-radius:6px; width:92%; max-width:1100px; max-height:calc(100vh - 40px); overflow:auto; box-shadow:0 4px 6px rgba(0,0,0,.1); padding:0; }
    .modal-header { padding:12px 15px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; background:#f5f5f5; }
    .modal-body { padding:15px; }
    .modal-close { background:none; border:none; font-size:18px; cursor:pointer; color:#666; }
    .modal-footer { padding:12px 15px; border-top:1px solid #ddd; display:flex; justify-content:flex-end; gap:8px; background:#f9f9f9; }
    .form-row { display:flex; gap:10px; margin-bottom:12px; flex-wrap:wrap; align-items:flex-start; }
    .form-group { flex:0 0 calc((100% - 20px) / 3); }
    .form-group label { display:block; margin-bottom:4px; font-weight:600; font-size:13px; }
    .form-control, select, textarea { width:100%; padding:6px 8px; border:1px solid #ccc; border-radius:2px; font-size:13px; }
    .btn-save { background:#007bff; color:#fff; border:none; padding:6px 12px; border-radius:2px; cursor:pointer; }
    .btn-cancel { background:#6c757d; color:#fff; border:none; padding:6px 12px; border-radius:2px; cursor:pointer; }
    .btn-delete { background:#dc3545; color:#fff; border:none; padding:6px 12px; border-radius:2px; cursor:pointer; }
    .column-selection { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:8px; margin-bottom:15px; }
    .column-item { display:flex; align-items:center; gap:8px; padding:6px 8px; border:1px solid #ddd; border-radius:2px; cursor:move; }
    .column-item.dragging { opacity: 0.5; }
    .column-item.drag-over { border-color: #007bff; background-color: #f0f8ff; }
    .btn-print { background:#fff; color:#000; border:1px solid #ccc; }
    @media (max-width:768px) { .form-row .form-group { flex:0 0 calc((100% - 20px) / 2); } .table-responsive { max-height:500px; } }
  </style>
</head>
<body>
@extends('layouts.app')
@section('content')
@php
  $selectedColumns = session('vehicle_columns', [
    'regn_no','make','model','type','useage','year','value','policy_id','engine','engine_type','cc','engine_no','chassis_no','from','to','notes','vehicle_id'
  ]);
  $allColumns = [
    'regn_no' => 'Regn No',
    'make' => 'Make',
    'model' => 'Model',
    'type' => 'Type',
    'useage' => 'Useage',
    'year' => 'Year',
    'value' => 'Value',
    'policy_id' => 'Policy ID',
    'engine' => 'Engine',
    'engine_type' => 'Engine Type',
    'cc' => 'CC',
    'engine_no' => 'Engine No',
    'chassis_no' => 'Chassis No',
    'from' => 'From',
    'to' => 'To',
    'notes' => 'Notes',
    'vehicle_id' => 'VehicleID'
  ];
@endphp
<div class="dashboard">
  <div class="container-table">
    <h3>Vehicles</h3>
    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif
    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Records Found - {{ $vehicles->total() }}</div>
        <a class="btn btn-export" href="{{ route('vehicles.export', array_merge(request()->query(), ['page' => $vehicles->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button">Column</button>
        <button class="btn btn-print" id="printBtn" type="button">Print</button>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addVehicleBtn">Add</button>
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
      </div>
    </div>
    <div class="table-responsive">
      <table id="vehiclesTable">
        <thead>
          <tr>
            <th>Action</th>
            @foreach($allColumns as $key => $label)
              @if(in_array($key, $selectedColumns))
                <th data-column="{{ $key }}">{{ $label }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($vehicles as $vh)
          <tr>
            <td>
              <span class="icon-expand" style="cursor:pointer;" onclick="openEditVehicle({{ $vh->id }})">⤢</span>
              <form action="{{ route('vehicles.destroy', $vh->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn-action btn-delete" title="Delete" style="background:none;border:none;padding:0 6px;font-size:12px;color:#dc3545;vertical-align:middle;cursor:pointer;" onclick="return confirm('Delete this vehicle?')">
                  🗑️
                </button>
              </form>
            </td>
            @if(in_array('regn_no', $selectedColumns))<td>{{ $vh->regn_no }}</td>@endif
            @if(in_array('make', $selectedColumns))<td>{{ $vh->make }}</td>@endif
            @if(in_array('model', $selectedColumns))<td>{{ $vh->model }}</td>@endif
            @if(in_array('type', $selectedColumns))<td>{{ $vh->type }}</td>@endif
            @if(in_array('useage', $selectedColumns))<td>{{ $vh->useage }}</td>@endif
            @if(in_array('year', $selectedColumns))<td>{{ $vh->year }}</td>@endif
            @if(in_array('value', $selectedColumns))<td>{{ $vh->value }}</td>@endif
            @if(in_array('policy_id', $selectedColumns))<td>{{ $vh->policy_id }}</td>@endif
            @if(in_array('engine', $selectedColumns))<td>{{ $vh->engine }}</td>@endif
            @if(in_array('engine_type', $selectedColumns))<td>{{ $vh->engine_type }}</td>@endif
            @if(in_array('cc', $selectedColumns))<td>{{ $vh->cc }}</td>@endif
            @if(in_array('engine_no', $selectedColumns))<td>{{ $vh->engine_no }}</td>@endif
            @if(in_array('chassis_no', $selectedColumns))<td>{{ $vh->chassis_no }}</td>@endif
            @if(in_array('from', $selectedColumns))<td>{{ $vh->from }}</td>@endif
            @if(in_array('to', $selectedColumns))<td>{{ $vh->to }}</td>@endif
            @if(in_array('notes', $selectedColumns))<td>{{ $vh->notes }}</td>@endif
            @if(in_array('vehicle_id', $selectedColumns))<td>{{ $vh->vehicle_id }}</td>@endif
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="footer">
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $vehicles->currentPage();
          $last = max(1, $vehicles->lastPage());
          function page_url($base, $q, $p) { $params = array_merge($q, ['page' => $p]); return $base . '?' . http_build_query($params); }
        @endphp
        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, 1) : '#' }}" @if($current <= 1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, $current-1) : '#' }}" @if($current <= 1) disabled @endif>&lsaquo;</a>
        <span class="page-info">Page {{ $current }} of {{ $last }}</span>
        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $current+1) : '#' }}" @if($current >= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $last) : '#' }}" @if($current >= $last) disabled @endif>&raquo;</a>
      </div>
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
        <div style="display:flex;gap:8px;margin-bottom:12px;">
          <button class="btn" onclick="selectAllColumns()">Select All</button>
          <button class="btn" onclick="deselectAllColumns()">Deselect All</button>
        </div>
        <form id="columnForm" action="{{ route('vehicles.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @foreach($allColumns as $key => $label)
              <div class="column-item" draggable="true" data-column="{{ $key }}" style="cursor:move;">
                <span style="cursor:move; margin-right:8px; font-size:16px; color:#666;">☰</span>
                <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" name="columns[]" @if(in_array($key, $selectedColumns)) checked @endif>
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

  <!-- Add/Edit Vehicle Modal -->
  <div class="modal" id="vehicleModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="vehicleModalTitle">Add Vehicle</h4>
        <button type="button" class="modal-close" onclick="closeVehicleModal()">×</button>
      </div>
      <form id="vehicleForm" method="POST">
        @csrf
        <input type="hidden" name="_method" id="vehicleFormMethod" value="POST">
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="regn_no">Regn No <span style="color:red">*</span></label>
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
              <label for="useage">Useage</label>
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
              <textarea class="form-control" name="notes" id="notes"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeVehicleModal()">Cancel</button>
          <button type="submit" class="btn-save" id="vehicleSaveBtn">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let currentVehicleId = null;

document.getElementById('addVehicleBtn').addEventListener('click', () => openVehicleModal('add'));
document.getElementById('columnBtn').addEventListener('click', () => openColumnModal());

function openColumnModal(){
  document.getElementById('columnModal').classList.add('show');
  document.body.style.overflow = 'hidden';
  setTimeout(initDragAndDrop, 100);
}
function closeColumnModal(){
  document.getElementById('columnModal').classList.remove('show');
  document.body.style.overflow = '';
}
function selectAllColumns(){ document.querySelectorAll('.column-checkbox').forEach(cb=>cb.checked=true); }
function deselectAllColumns(){ document.querySelectorAll('.column-checkbox').forEach(cb=>cb.checked=false); }
function saveColumnSettings(){
  const items = Array.from(document.querySelectorAll('#columnSelection .column-item'));
  const order = items.map(item => item.dataset.column);
  const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n=>n.value);
  const orderedChecked = order.filter(col => checked.includes(col));
  const form = document.getElementById('columnForm');
  const existing = form.querySelectorAll('input[name="columns[]"]'); existing.forEach(e=>e.remove());
  orderedChecked.forEach(c => {
    const i = document.createElement('input'); i.type='hidden'; i.name='columns[]'; i.value=c; form.appendChild(i);
  });
  form.submit();
}

// Drag and drop functionality
let draggedElement = null;
let dragOverElement = null;

function initDragAndDrop() {
  const columnSelection = document.getElementById('columnSelection');
  if (!columnSelection) return;
  const columnItems = columnSelection.querySelectorAll('.column-item');
  columnItems.forEach(item => {
    item.addEventListener('dragstart', function(e) {
      draggedElement = this;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', '');
      const dragImage = this.cloneNode(true);
      dragImage.style.opacity = '0.5';
      document.body.appendChild(dragImage);
      e.dataTransfer.setDragImage(dragImage, 0, 0);
      setTimeout(() => document.body.removeChild(dragImage), 0);
    });
    item.addEventListener('dragend', function(e) {
      this.classList.remove('dragging');
      if (dragOverElement) {
        dragOverElement.classList.remove('drag-over');
        dragOverElement = null;
      }
      draggedElement = null;
    });
    item.addEventListener('dragover', function(e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      if (draggedElement && this !== draggedElement) {
        if (dragOverElement && dragOverElement !== this) {
          dragOverElement.classList.remove('drag-over');
        }
        this.classList.add('drag-over');
        dragOverElement = this;
        const rect = this.getBoundingClientRect();
        const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
        if (next) {
          if (this.nextSibling && this.nextSibling !== draggedElement) {
            this.parentNode.insertBefore(draggedElement, this.nextSibling);
          } else if (!this.nextSibling) {
            this.parentNode.appendChild(draggedElement);
          }
        } else {
          if (this.previousSibling !== draggedElement) {
            this.parentNode.insertBefore(draggedElement, this);
          }
        }
      }
    });
    item.addEventListener('dragleave', function(e) {
      if (!this.contains(e.relatedTarget)) {
        this.classList.remove('drag-over');
        if (dragOverElement === this) {
          dragOverElement = null;
        }
      }
    });
    item.addEventListener('drop', function(e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.remove('drag-over');
      dragOverElement = null;
      return false;
    });
  });
}

// Print table function
function printTable() {
  const table = document.getElementById('vehiclesTable');
  if (!table) return;
  const headers = [];
  const headerCells = table.querySelectorAll('thead th');
  headerCells.forEach(th => {
    let headerText = th.textContent.trim();
    if (headerText) headers.push(headerText);
  });
  const rows = [];
  const tableRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
  tableRows.forEach(row => {
    if (row.style.display === 'none') return;
    const cells = [];
    const rowCells = row.querySelectorAll('td');
    rowCells.forEach((cell) => {
      let cellContent = '';
      if (cell.querySelector('.icon-expand')) {
        cellContent = '⤢';
      } else {
        const link = cell.querySelector('a');
        cellContent = link ? link.textContent.trim() : cell.textContent.trim();
      }
      cells.push(cellContent || '-');
    });
    rows.push(cells);
  });
  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  const headersHTML = headers.map(h => '<th>' + escapeHtml(h) + '</th>').join('');
  const rowsHTML = rows.map(row => {
    const cellsHTML = row.map(cell => {
      return '<td>' + escapeHtml(String(cell || '-')) + '</td>';
    }).join('');
    return '<tr>' + cellsHTML + '</tr>';
  }).join('');
  const printWindow = window.open('', '_blank', 'width=800,height=600');
  const printHTML = '<!DOCTYPE html><html><head><title>Vehicles - Print</title><style>@page { margin: 1cm; size: A4 landscape; }html, body { margin: 0; padding: 0; background: #fff !important; }body { font-family: Arial, sans-serif; font-size: 10px; }table { width: 100%; border-collapse: collapse; page-break-inside: auto; }thead { display: table-header-group; }thead th { background-color: #000 !important; color: #fff !important; padding: 8px 5px; text-align: left; border: 1px solid #333; font-weight: normal; -webkit-print-color-adjust: exact; print-color-adjust: exact; }tbody tr { page-break-inside: avoid; border-bottom: 1px solid #ddd; }tbody tr:nth-child(even) { background-color: #f8f8f8; }tbody td { padding: 6px 5px; border: 1px solid #ddd; white-space: nowrap; }</style></head><body><table><thead><tr>' + headersHTML + '</tr></thead><tbody>' + rowsHTML + '</tbody></table><scr' + 'ipt>window.onload = function() { setTimeout(function() { window.print(); }, 100); };window.onafterprint = function() { window.close(); };</scr' + 'ipt></body></html>';
  if (printWindow) {
    printWindow.document.open();
    printWindow.document.write(printHTML);
    printWindow.document.close();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const printBtn = document.getElementById('printBtn');
  if (printBtn) {
    printBtn.addEventListener('click', function() {
      printTable();
    });
  }
});

function openVehicleModal(mode, vh = null) {
  const modal = document.getElementById('vehicleModal');
  const title = document.getElementById('vehicleModalTitle');
  const form = document.getElementById('vehicleForm');
  const formMethod = document.getElementById('vehicleFormMethod');
  form.reset();

  if (mode === 'add') {
    title.textContent = 'Add Vehicle';
    form.action = "{{ route('vehicles.store') }}";
    formMethod.value = 'POST';
    currentVehicleId = null;
    document.getElementById('regn_no').value = '';
    document.getElementById('make').value = '';
    document.getElementById('model').value = '';
    document.getElementById('type').value = '';
    document.getElementById('useage').value = '';
    document.getElementById('year').value = '';
    document.getElementById('value').value = '';
    document.getElementById('policy_id').value = '';
    document.getElementById('engine').value = '';
    document.getElementById('engine_type').value = '';
    document.getElementById('cc').value = '';
    document.getElementById('engine_no').value = '';
    document.getElementById('chassis_no').value = '';
    document.getElementById('from').value = '';
    document.getElementById('to').value = '';
    document.getElementById('notes').value = '';
  } else if (mode === 'edit' && vh) {
    title.textContent = 'Edit Vehicle';
    form.action = `/vehicles/${vh.id}`;
    formMethod.value = 'PUT';
    currentVehicleId = vh.id;
    document.getElementById('regn_no').value = vh.regn_no ?? '';
    document.getElementById('make').value = vh.make ?? '';
    document.getElementById('model').value = vh.model ?? '';
    document.getElementById('type').value = vh.type ?? '';
    document.getElementById('useage').value = vh.useage ?? '';
    document.getElementById('year').value = vh.year ?? '';
    document.getElementById('value').value = vh.value ?? '';
    document.getElementById('policy_id').value = vh.policy_id ?? '';
    document.getElementById('engine').value = vh.engine ?? '';
    document.getElementById('engine_type').value = vh.engine_type ?? '';
    document.getElementById('cc').value = vh.cc ?? '';
    document.getElementById('engine_no').value = vh.engine_no ?? '';
    document.getElementById('chassis_no').value = vh.chassis_no ?? '';
    document.getElementById('from').value = vh.from ?? '';
    document.getElementById('to').value = vh.to ?? '';
    document.getElementById('notes').value = vh.notes ?? '';
  }

  document.body.style.overflow = 'hidden';
  modal.classList.add('show');
}

function closeVehicleModal() {
  document.getElementById('vehicleModal').classList.remove('show');
  document.body.style.overflow = '';
}

function openEditVehicle(id) {
  fetch(`/vehicles/${id}/edit`)
    .then(res => res.json())
    .then(vh => openVehicleModal('edit', vh));
}

// Close modal on backdrop click or ESC
document.getElementById('vehicleModal').addEventListener('click', e => {
  if (e.target === document.getElementById('vehicleModal')) closeVehicleModal();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeVehicleModal();
});

// Simple validation
document.getElementById('vehicleForm').addEventListener('submit', function(e){
  if (!document.getElementById('regn_no').value.trim()) {
    e.preventDefault();
    alert('Regn No is required');
  }
});
</script>
@endsection
</body>
</html>