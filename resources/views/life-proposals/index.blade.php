<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Life Proposals</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; color: #000; margin: 10px; background: #fff; }
    .container-table { max-width: 100%; margin: 0 auto; }
    h3 { background: #f1f1f1; padding: 8px; margin-bottom: 10px; font-weight: bold; border: 1px solid #ddd; }
    .top-bar { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
    .left-group { display:flex; align-items:center; gap:10px; flex:1 1 auto; min-width:220px; }
    .left-buttons { display:flex; gap:10px; align-items:center; }
    .records-found { font-size:14px; color:#555; min-width:150px; }
    .action-buttons { margin-left:auto; display:flex; gap:10px; }
    .btn { border:none; cursor:pointer; padding:6px 12px; font-size:13px; border-radius:2px; white-space:nowrap; transition:background-color .2s; text-decoration:none; color:inherit; background:#fff; border:1px solid #ccc; }
    .btn-add { background:#df7900; color:#fff; border-color:#df7900; }
    .btn-export, .btn-column { background:#fff; color:#000; border:1px solid #ccc; }
    .btn-back { background:#ccc; color:#333; border-color:#ccc; }
    .btn-submitted { background:#000; color:#fff; border-color:#000; }
    .table-responsive { width: 100%; overflow-x: auto; border: 1px solid #ddd; max-height: 420px; overflow-y: auto; background: #fff; }
    .footer { display:flex; justify-content:center; align-items:center; padding:5px 0; gap:10px; border-top:1px solid #ccc; flex-wrap:wrap; margin-top:15px; position:relative; }
    .paginator { display:flex; align-items:center; gap:5px; font-size:12px; color:#555; white-space:nowrap; justify-content:center; }
    .page-info { padding:0 8px; display:inline-flex; align-items:center; justify-content:center; min-width:120px; }
    .btn-page { color:#2d2d2d; font-size:25px; width:22px; height:50px; padding:5px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-page[disabled] { cursor:not-allowed; opacity:.5; }
    table { width:100%; border-collapse:collapse; font-size:13px; min-width:1600px; }
    thead tr { background-color: black; color: white; height:35px; font-weight: normal; }
    thead th { padding:6px 5px; text-align:left; border-right:1px solid #444; white-space:nowrap; font-weight: normal; }
    thead th:last-child { border-right:none; }
    tbody tr { background-color:#fefefe; border-bottom:1px solid #ddd; min-height:28px; }
    tbody tr:nth-child(even) { background-color:#f8f8f8; }
    tbody tr.submitted-row { background:#d4edda !important; }
    tbody td { padding:5px 5px; border-right:1px solid #ddd; white-space:nowrap; vertical-align:middle; font-size:12px; }
    tbody td:last-child { border-right:none; }
    .icon-expand { cursor:pointer; color:black; text-align:center; width:20px; }
    .btn-action { padding:2px 6px; font-size:11px; margin:1px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:2px; display:inline-block; }
    .badge-status { font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; }
    /* Modal styles (like contacts) */
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
    @media (max-width:1200px) { table { min-width:1200px; } }
    @media (max-width:768px) { .form-row .form-group { flex:0 0 calc((100% - 20px) / 2); } .table-responsive { max-height:500px; } }
  </style>
</head>
<body>
@extends('layouts.app')
@section('content')

@php
  $selectedColumns = session('life_proposal_columns', [
    'proposers_name','insurer','policy_plan','sum_assured','term','add_ons','offer_date','premium','frequency','stage','date','age','status','source_of_payment','mcr','doctor','date_sent','date_completed','notes','agency','prid','class','is_submitted'
  ]);
@endphp

<div class="dashboard">
  <div class="container-table">
    <h3>Life Proposals</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Records Found - {{ $proposals->total() }}</div>
        <div class="left-buttons" aria-label="left action buttons">
          <a class="btn btn-export" href="{{ route('life-proposals.export', array_merge(request()->query(), ['page' => $proposals->currentPage()])) }}">Export</a>
          <button class="btn btn-column" id="columnBtn" type="button">Column</button>
          <button class="btn btn-print" id="printBtn" type="button">Print</button>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addProposalBtn">Add</button>
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
      </div>
    </div>

    <div class="table-responsive" id="tableResponsive">
      <table id="proposalsTable">
        <thead>
          <tr>
            <th>Action</th>
            @if(in_array('proposers_name',$selectedColumns))<th data-column="proposers_name">Proposer's Name</th>@endif
            @if(in_array('insurer',$selectedColumns))<th data-column="insurer">Insurer</th>@endif
            @if(in_array('policy_plan',$selectedColumns))<th data-column="policy_plan">Policy Plan</th>@endif
            @if(in_array('sum_assured',$selectedColumns))<th data-column="sum_assured">Sum Assured</th>@endif
            @if(in_array('term',$selectedColumns))<th data-column="term">Term</th>@endif
            @if(in_array('add_ons',$selectedColumns))<th data-column="add_ons">Add Ons</th>@endif
            @if(in_array('offer_date',$selectedColumns))<th data-column="offer_date">Offer Date</th>@endif
            @if(in_array('premium',$selectedColumns))<th data-column="premium">Premium</th>@endif
            @if(in_array('frequency',$selectedColumns))<th data-column="frequency">Freq</th>@endif
            @if(in_array('stage',$selectedColumns))<th data-column="stage">Stage</th>@endif
            @if(in_array('date',$selectedColumns))<th data-column="date">Date</th>@endif
            @if(in_array('age',$selectedColumns))<th data-column="age">Age</th>@endif
            @if(in_array('status',$selectedColumns))<th data-column="status">Status</th>@endif
            @if(in_array('source_of_payment',$selectedColumns))<th data-column="source_of_payment">Source Of Payment</th>@endif
            @if(in_array('mcr',$selectedColumns))<th data-column="mcr">MCR</th>@endif
            @if(in_array('doctor',$selectedColumns))<th data-column="doctor">Doctor</th>@endif
            @if(in_array('date_sent',$selectedColumns))<th data-column="date_sent">Date Sent</th>@endif
            @if(in_array('date_completed',$selectedColumns))<th data-column="date_completed">Date Completed</th>@endif
            @if(in_array('notes',$selectedColumns))<th data-column="notes">Notes</th>@endif
            @if(in_array('agency',$selectedColumns))<th data-column="agency">Agency</th>@endif
            @if(in_array('prid',$selectedColumns))<th data-column="prid">PRID</th>@endif
            @if(in_array('class',$selectedColumns))<th data-column="class">Class</th>@endif
            @if(in_array('is_submitted',$selectedColumns))<th data-column="is_submitted">Submitted</th>@endif
          </tr>
        </thead>
        <tbody>
          @foreach($proposals as $proposal)
            <tr class="{{ $proposal->is_submitted ? 'submitted-row' : '' }}">
              <td class="icon-expand" onclick="openEditProposal({{ $proposal->id }})">⤢</td>
              @if(in_array('proposers_name',$selectedColumns))<td data-column="proposers_name">{{ $proposal->proposers_name }}</td>@endif
              @if(in_array('insurer',$selectedColumns))<td data-column="insurer">{{ $proposal->insurer }}</td>@endif
              @if(in_array('policy_plan',$selectedColumns))<td data-column="policy_plan">{{ $proposal->policy_plan }}</td>@endif
              @if(in_array('sum_assured',$selectedColumns))<td data-column="sum_assured">{{ $proposal->sum_assured ? number_format($proposal->sum_assured,2) : '##########' }}</td>@endif
              @if(in_array('term',$selectedColumns))<td data-column="term">{{ $proposal->term }}</td>@endif
              @if(in_array('add_ons',$selectedColumns))<td data-column="add_ons">{{ $proposal->add_ons ?? '-' }}</td>@endif
              @if(in_array('offer_date',$selectedColumns))<td data-column="offer_date">{{ $proposal->offer_date ? $proposal->offer_date->format('d-M-y') : '##########' }}</td>@endif
              @if(in_array('premium',$selectedColumns))<td data-column="premium">{{ number_format($proposal->premium,2) }}</td>@endif
              @if(in_array('frequency',$selectedColumns))<td data-column="frequency">{{ $proposal->frequency }}</td>@endif
              @if(in_array('stage',$selectedColumns))<td data-column="stage">{{ $proposal->stage }}</td>@endif
              @if(in_array('date',$selectedColumns))<td data-column="date">{{ $proposal->date ? $proposal->date->format('d-M-y') : '##########' }}</td>@endif
              @if(in_array('age',$selectedColumns))<td data-column="age">{{ $proposal->age }}</td>@endif
              @if(in_array('status',$selectedColumns))<td data-column="status"><span class="badge-status" style="background:{{ $proposal->status == 'Approved' ? '#28a745' : ($proposal->status=='Pending' ? '#ffc107' : ($proposal->status=='Declined' ? '#dc3545' : '#6c757d')) }}">{{ $proposal->status }}</span></td>@endif
              @if(in_array('source_of_payment',$selectedColumns))<td data-column="source_of_payment">{{ $proposal->source_of_payment }}</td>@endif
              @if(in_array('mcr',$selectedColumns))<td data-column="mcr">{{ $proposal->mcr ?? '-' }}</td>@endif
              @if(in_array('doctor',$selectedColumns))<td data-column="doctor">{{ $proposal->doctor ?? '-' }}</td>@endif
              @if(in_array('date_sent',$selectedColumns))<td data-column="date_sent">{{ $proposal->date_sent ? $proposal->date_sent->format('d-M-y') : '##########' }}</td>@endif
              @if(in_array('date_completed',$selectedColumns))<td data-column="date_completed">{{ $proposal->date_completed ? $proposal->date_completed->format('d-M-y') : '##########' }}</td>@endif
              @if(in_array('notes',$selectedColumns))<td data-column="notes">{{ $proposal->notes ?? '-' }}</td>@endif
              @if(in_array('agency',$selectedColumns))<td data-column="agency">{{ $proposal->agency ?? '-' }}</td>@endif
              @if(in_array('prid',$selectedColumns))<td data-column="prid">{{ $proposal->prid }}</td>@endif
              @if(in_array('class',$selectedColumns))<td data-column="class">{{ $proposal->class }}</td>@endif
              @if(in_array('is_submitted',$selectedColumns))<td data-column="is_submitted">{{ $proposal->is_submitted ? 'Yes' : 'No' }}</td>@endif
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
          $current = $proposals->currentPage();
          $last = max(1,$proposals->lastPage());
          function page_url($base,$q,$p){ $params = array_merge($q,['page'=>$p]); return $base . '?' . http_build_query($params); }
        @endphp

        <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,1) : '#' }}" @if($current<=1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,$current-1) : '#' }}" @if($current<=1) disabled @endif>&lsaquo;</a>
        <span class="page-info">Page {{ $current }} of {{ $last }}</span>
        <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$current+1) : '#' }}" @if($current>= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$last) : '#' }}" @if($current>=$last) disabled @endif>&raquo;</a>
      </div>
    </div>
  </div>

  <!-- Add/Edit Proposal Modal (single) -->
  <div class="modal" id="proposalModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="proposalModalTitle">Add Life Proposal</h4>
        <button type="button" class="modal-close" onclick="closeProposalModal()">×</button>
      </div>
      <form id="proposalForm" method="POST" action="{{ route('life-proposals.store') }}">
        @csrf
        <div id="proposalFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="proposers_name">Proposer's Name *</label>
              <input id="proposers_name" name="proposers_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="insurer">Insurer *</label>
              <select id="insurer" name="insurer" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['insurers'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="policy_plan">Policy Plan *</label>
              <select id="policy_plan" name="policy_plan" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['policy_plans'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="sum_assured">Sum Assured</label>
              <input id="sum_assured" name="sum_assured" type="number" step="0.01" class="form-control">
            </div>
            <div class="form-group">
              <label for="term">Term (Years) *</label>
              <input id="term" name="term" type="number" min="1" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="add_ons">Add Ons</label>
              <select id="add_ons" name="add_ons" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['add_ons'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="offer_date">Offer Date *</label>
              <input id="offer_date" name="offer_date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="premium">Premium *</label>
              <input id="premium" name="premium" type="number" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="frequency">Frequency *</label>
              <select id="frequency" name="frequency" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['frequencies'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="stage">Stage *</label>
              <select id="stage" name="stage" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['stages'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="date">Date *</label>
              <input id="date" name="date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="age">Age *</label>
              <input id="age" name="age" type="number" min="1" max="120" class="form-control" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="status">Status *</label>
              <select id="status" name="status" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['statuses'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="source_of_payment">Source Of Payment *</label>
              <select id="source_of_payment" name="source_of_payment" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['sources_of_payment'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="mcr">MCR</label>
              <input id="mcr" name="mcr" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="doctor">Doctor</label>
              <select id="doctor" name="doctor" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['doctors'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="date_sent">Date Sent</label>
              <input id="date_sent" name="date_sent" type="date" class="form-control">
            </div>
            <div class="form-group">
              <label for="date_completed">Date Completed</label>
              <input id="date_completed" name="date_completed" type="date" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
              <label for="agency">Agency</label>
              <select id="agency" name="agency" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['agencies'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="class">Class *</label>
              <select id="class" name="class" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['classes'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="prid">PRID</label>
              <input id="prid" name="prid" class="form-control" readonly>
            </div>
            <div class="form-group">
              <label for="is_submitted" style="display:block;">Submitted</label>
              <input id="is_submitted" name="is_submitted" type="checkbox" value="1">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeProposalModal()">Cancel</button>
          <button type="button" class="btn-delete" id="proposalDeleteBtn" style="display:none;" onclick="deleteProposal()">Delete</button>
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
        <div style="display:flex;gap:8px;margin-bottom:12px;">
          <button class="btn" onclick="selectAllColumns()">Select All</button>
          <button class="btn" onclick="deselectAllColumns()">Deselect All</button>
        </div>
        <form id="columnForm" action="{{ route('life-proposals.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @php
              $all = [
                'proposers_name'=>"Proposer's Name",'insurer'=>'Insurer','policy_plan'=>'Policy Plan','sum_assured'=>'Sum Assured','term'=>'Term','add_ons'=>'Add Ons','offer_date'=>'Offer Date','premium'=>'Premium','frequency'=>'Freq','stage'=>'Stage','date'=>'Date','age'=>'Age','status'=>'Status','source_of_payment'=>'Source Of Payment','mcr'=>'MCR','doctor'=>'Doctor','date_sent'=>'Date Sent','date_completed'=>'Date Completed','notes'=>'Notes','agency'=>'Agency','prid'=>'PRID','class'=>'Class','is_submitted'=>'Submitted'
              ];
            @endphp
            @foreach($all as $key => $label)
              <div class="column-item" draggable="true" data-column="{{ $key }}" style="cursor:move;">
                <span style="cursor:move; margin-right:8px; font-size:16px; color:#666;">☰</span>
                <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if(in_array($key,$selectedColumns)) checked @endif>
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
  let currentProposalId = null;
  const lookupData = @json($lookupData);
  const selectedColumns = @json($selectedColumns);

  document.getElementById('addProposalBtn').addEventListener('click', () => openProposalModal('add'));
  document.getElementById('columnBtn').addEventListener('click', () => openColumnModal());

  async function openEditProposal(id){
    try {
      const res = await fetch(`/life-proposals/${id}/edit`);
      if (!res.ok) throw new Error('Network error');
      const proposal = await res.json();
      currentProposalId = id;
      openProposalModal('edit', proposal);
    } catch (e) {
      console.error(e);
      alert('Error loading proposal data');
    }
  }

  function openProposalModal(mode, proposal = null){
    const modal = document.getElementById('proposalModal');
    const title = document.getElementById('proposalModalTitle');
    const form = document.getElementById('proposalForm');
    const formMethod = document.getElementById('proposalFormMethod');
    const deleteBtn = document.getElementById('proposalDeleteBtn');

    if (mode === 'add') {
      title.textContent = 'Add Life Proposal';
      form.action = '{{ route("life-proposals.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      form.reset();
      document.getElementById('is_submitted').checked = false;
      document.getElementById('prid').value = '';
    } else {
      title.textContent = 'Edit Life Proposal';
      form.action = `/life-proposals/${currentProposalId}`;
      formMethod.innerHTML = `@method('PUT')`;
      deleteBtn.style.display = 'inline-block';

      const fields = ['proposers_name','insurer','policy_plan','sum_assured','term','add_ons','offer_date','premium','frequency','stage','date','age','status','source_of_payment','mcr','doctor','date_sent','date_completed','notes','agency','class','prid'];
      fields.forEach(k => {
        const el = document.getElementById(k);
        if (!el) return;
        if (el.type === 'checkbox') {
          el.checked = !!proposal[k];
        } else if (el.type === 'date') {
          el.value = proposal[k] ? proposal[k].substring(0,10) : '';
        } else {
          el.value = proposal[k] ?? '';
        }
      });
      document.getElementById('is_submitted').checked = !!proposal.is_submitted;
    }

    document.body.style.overflow = 'hidden';
    modal.classList.add('show');
  }

  function closeProposalModal(){
    document.getElementById('proposalModal').classList.remove('show');
    currentProposalId = null;
    document.body.style.overflow = '';
  }

  function deleteProposal(){
    if (!currentProposalId) return;
    if (!confirm('Delete this proposal?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/life-proposals/${currentProposalId}`;
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }

  // Column modal functions
  function openColumnModal(){
    document.getElementById('tableResponsive').classList.add('no-scroll');
    document.querySelectorAll('.column-checkbox').forEach(cb => cb.checked = selectedColumns.includes(cb.value));
    document.body.style.overflow = 'hidden';
    document.getElementById('columnModal').classList.add('show');
    setTimeout(initDragAndDrop, 100);
  }
  function closeColumnModal(){
    document.getElementById('tableResponsive').classList.remove('no-scroll');
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
    toggleTableScroll();
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
    const table = document.getElementById('proposalsTable');
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
    const printHTML = '<!DOCTYPE html><html><head><title>Life Proposals - Print</title><style>@page { margin: 1cm; size: A4 landscape; }html, body { margin: 0; padding: 0; background: #fff !important; }body { font-family: Arial, sans-serif; font-size: 10px; }table { width: 100%; border-collapse: collapse; page-break-inside: auto; }thead { display: table-header-group; }thead th { background-color: #000 !important; color: #fff !important; padding: 8px 5px; text-align: left; border: 1px solid #333; font-weight: normal; -webkit-print-color-adjust: exact; print-color-adjust: exact; }tbody tr { page-break-inside: avoid; border-bottom: 1px solid #ddd; }tbody tr:nth-child(even) { background-color: #f8f8f8; }tbody td { padding: 6px 5px; border: 1px solid #ddd; white-space: nowrap; }</style></head><body><table><thead><tr>' + headersHTML + '</tr></thead><tbody>' + rowsHTML + '</tbody></table><scr' + 'ipt>window.onload = function() { setTimeout(function() { window.print(); }, 100); };window.onafterprint = function() { window.close(); };</scr' + 'ipt></body></html>';
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

  // Close modals on ESC or backdrop
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeProposalModal(); closeColumnModal(); } });
  document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) { m.classList.remove('show'); document.body.style.overflow = ''; } });
  });

  // Simple validation
  document.getElementById('proposalForm').addEventListener('submit', function(e){
    const req = this.querySelectorAll('[required]');
    let ok = true;
    req.forEach(f => { if (!String(f.value||'').trim()) { ok = false; f.style.borderColor='red'; } else { f.style.borderColor=''; } });
    if (!ok) { e.preventDefault(); alert('Please fill required fields'); }
  });

  // Toggle scrollbar helper for responsive table
  function toggleTableScroll() {
    const table = document.getElementById('proposalsTable');
    const wrapper = document.getElementById('tableResponsive');
    if (!table || !wrapper) return;
    const hasHorizontalOverflow = table.offsetWidth > wrapper.offsetWidth;
    const hasVerticalOverflow = table.offsetHeight > wrapper.offsetHeight;
    wrapper.classList.toggle('no-scroll', !hasHorizontalOverflow && !hasVerticalOverflow);
  }
  window.addEventListener('load', toggleTableScroll);
  window.addEventListener('resize', toggleTableScroll);

</script>
@endsection
</body>
</html>