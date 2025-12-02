<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Policies</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; color: #000; margin: 0; background: #f5f5f5; }
    .container-table { max-width: 100%; margin: 0 auto; background: #fff; padding: 0; }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0; flex-wrap: wrap; gap: 15px; background: #f5f5f5; padding: 15px 20px; border-bottom: 1px solid #ddd; }
    .page-title-section { display: flex; align-items: center; gap: 15px; flex: 1; }
    h3 { background: transparent; padding: 0; margin: 0; font-weight: bold; color: #2d2d2d; font-size: 24px; }
    .records-found { font-size: 14px; color: #2d2d2d; font-weight: normal; }
    .top-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:15px; margin-bottom:15px; }
    .left-group { display:flex; align-items:center; gap:15px; flex:1 1 auto; min-width:220px; }
    .left-buttons { display:flex; gap:10px; align-items:center; }
    .filter-group { display:flex; align-items:center; gap:8px; }
    .filter-toggle { display:flex; align-items:center; gap:8px; }
    .toggle-switch { position:relative; width:44px; height:24px; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; transition:.4s; border-radius:24px; }
    .toggle-slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background-color:white; transition:.4s; border-radius:50%; }
    .toggle-switch input:checked + .toggle-slider { background-color:#4CAF50; }
    .toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
    .action-buttons { margin-left:auto; display:flex; gap:10px; align-items:center; }
    .btn { border:none; cursor:pointer; padding:6px 16px; font-size:13px; border-radius:2px; white-space:nowrap; transition:background-color .2s; text-decoration:none; color:inherit; background:#fff; border:1px solid #ccc; font-weight:normal; }
    .btn-add { background:#f3742a; color:#fff; border-color:#f3742a; }
    .btn-dfr { background:#2d2d2d; color:#fff; border-color:#2d2d2d; }
    .btn-export, .btn-column { background:#fff; color:#000; border:1px solid #ccc; }
    .btn-close { background:#e0e0e0; color:#000; border-color:#ccc; }
    .btn-back { background:#ccc; color:#333; border-color:#ccc; }
    .table-responsive { width: 100%; border: none; background: #fff; margin-bottom:0; overflow-x: auto; padding: 0 20px; }
    .table-responsive.no-scroll { overflow: visible; }
    .footer { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; gap:10px; border-top:1px solid #ddd; flex-wrap:wrap; margin-top:0; background:#f5f5f5; }
    .footer-left { display:flex; gap:10px; }
    .footer-right { margin-left:auto; }
    .paginator {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: #555;
      white-space: nowrap;
    }
    .btn-page{
      color: #2d2d2d;
      font-size: 14px;
      width: 32px;
      height: 32px;
      padding: 0;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
      border: 1px solid #ddd;
      border-radius: 2px;
      font-weight: normal;
    }
    .btn-page:hover:not([disabled]) { background: #e9e9e9; }
    .btn-page[disabled] { opacity: 0.5; cursor: not-allowed; background: #f5f5f5; }
    .page-info { padding: 0 12px; color: #555; font-size: 12px; }
    table { width:100%; border-collapse:collapse; font-size:13px; min-width:1200px; }
    thead tr { background-color: #000; color: #fff; height:35px; font-weight: normal; }
    thead th { padding:8px 5px; text-align:left; border-right:1px solid #444; white-space:nowrap; font-weight: normal; color: #fff !important; }
    thead th:first-child { text-align:center; }
    thead th:last-child { border-right:none; }
    tbody tr { background-color:#fff; border-bottom:1px solid #ddd; min-height:32px; }
    tbody tr:nth-child(even) { background-color:#f8f8f8; }
    tbody tr.dfr-row { background:#fff176 !important; }
    tbody tr.dfr-row td { background:#fff176 !important; color:#000 !important; }

    tbody td { padding:8px 5px; border-right:1px solid #ddd; white-space:nowrap; vertical-align:middle; font-size:12px; }
    tbody td:last-child { border-right:none; }
    tbody tr.dfr-row td a { color:#000 !important; }
    tbody tr.expired-row td a { color:#000 !important; }
    .bell-cell { text-align:center; padding:8px 5px; vertical-align:middle; min-width:50px; }
    .bell-cell.dfr { background-color:#fff176 !important; }
    .bell-cell:not(.expired):not(.dfr) { background-color:#fff !important; }
    .bell-radio { width:16px; height:16px; cursor:not-allowed; pointer-events:none; opacity:0.6; }
    .bell-radio.expired { accent-color:#dc3545; }
    .bell-radio.dfr { accent-color:#ffc107; }
    .bell-radio.normal { accent-color:#ccc; }
    .action-cell { display:flex; align-items:center; gap:10px; padding:8px; }
    .action-radio { width:18px; height:18px; cursor:default; pointer-events:none; accent-color:#f3742a; }
    .action-expand { width:18px; height:18px; cursor:pointer; display:inline-block; }
    .action-clock { width:18px; height:18px; cursor:pointer; display:inline-block; }
    .action-ellipsis { width:18px; height:18px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; line-height:1; }
    .icon-expand { cursor:pointer; color:black; text-align:center; width:20px; }
    .btn-action { padding:2px 6px; font-size:11px; margin:1px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:2px; display:inline-block; }
    .btn-action:hover { background:#f0f0f0; }
    .badge-status { font-size:11px; padding:4px 8px; display:inline-block; border-radius:4px; color:#fff; }
    tbody td a { color:#007bff; text-decoration:underline; }
    tbody td a:hover { color:#0056b3; text-decoration:none; }
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
    @media (max-width:1200px) { table { min-width:900px; } }
    @media (max-width:768px) { .form-row .form-group { flex:0 0 calc((100% - 20px) / 2); } .table-responsive { max-height:500px; } }
  </style>
</head>
<body>
@extends('layouts.app')
@section('content')

@php
  $selectedColumns = session('policy_columns', [
    'policy_no','client_name','insurer','policy_class','policy_plan','sum_insured','start_date','end_date','insured','policy_status','date_registered','policy_id','insured_item','renewable','biz_type','term','term_unit','base_premium','premium','frequency','pay_plan','agency','agent','notes'
  ]);
  
  // Define all available columns with their labels and filter settings
  $columnDefinitions = [
    'policy_no' => ['label' => 'Policy No', 'filter' => true],
    'client_name' => ['label' => 'Client Name', 'filter' => true],
    'insurer' => ['label' => 'Insurer', 'filter' => true],
    'policy_class' => ['label' => 'Policy Class', 'filter' => true],
    'policy_plan' => ['label' => 'Policy Plan', 'filter' => false],
    'sum_insured' => ['label' => 'Sum Insured', 'filter' => false],
    'start_date' => ['label' => 'Start Date', 'filter' => false],
    'end_date' => ['label' => 'End Date', 'filter' => false],
    'insured' => ['label' => 'Insured', 'filter' => false],
    'policy_status' => ['label' => 'Policy Status', 'filter' => true],
    'date_registered' => ['label' => 'Date Registered', 'filter' => false],
    'policy_id' => ['label' => 'Policy ID', 'filter' => false],
    'insured_item' => ['label' => 'Insured Item', 'filter' => false],
    'renewable' => ['label' => 'Renewable', 'filter' => false],
    'biz_type' => ['label' => 'Biz Type', 'filter' => false],
    'term' => ['label' => 'Term', 'filter' => false],
    'term_unit' => ['label' => 'Term Unit', 'filter' => false],
    'base_premium' => ['label' => 'Base Premium', 'filter' => false],
    'premium' => ['label' => 'Premium', 'filter' => false],
    'frequency' => ['label' => 'Frequency', 'filter' => false],
    'pay_plan' => ['label' => 'Pay Plan', 'filter' => false],
    'agency' => ['label' => 'Agency', 'filter' => false],
    'agent' => ['label' => 'Agent', 'filter' => false],
    'notes' => ['label' => 'Notes', 'filter' => false],
  ];
  
  // Get all column keys for the modal
  $allColumns = array_keys($columnDefinitions);
  
  // Order columns: selected columns first (in saved order), then unselected columns
  $orderedColumns = array_merge(
    $selectedColumns,
    array_diff($allColumns, $selectedColumns)
  );
@endphp

<div class="dashboard">
  <div class="container-table">
    <div class="page-header">
      <div class="page-title-section" style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
        <h3>Policies</h3>
        <div class="records-found">Records Found - {{ $policies->total() }}</div>
        <div class="filter-group">
          <label class="toggle-switch">
            <input type="checkbox" id="filterToggle">
            <span class="toggle-slider"></span>
          </label>
          <label for="filterToggle" style="font-size:14px; color:#2d2d2d; margin:0; cursor:pointer; user-select:none;">Filter</label>
        </div>
        <button class="btn btn-dfr" id="dfrOnlyBtn" type="button">Due For Renewal</button>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addPolicyBtn">Add</button>
        <button class="btn btn-close" id="closeBtn">Close</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="policiesTable">
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
                <th data-column="{{ $col }}">
                  {{ $columnDefinitions[$col]['label'] }}
                  @if($columnDefinitions[$col]['filter'])
                    <input type="text" class="column-filter" data-column="{{ $col }}" placeholder="Filter..." style="width:100%; margin-top:4px; padding:4px 6px; font-size:11px; border:1px solid #666; background:#000; color:#fff; border-radius:2px; display:none;">
                  @endif
                </th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($policies as $policy)
            @php
              $policyStatusName = $policy->policy_status_name ?? 'N/A';
              $isDFR = stripos($policyStatusName, 'DFR') !== false || (optional($policy->end_date) && $policy->end_date->isBetween(now(), now()->addDays(30)));
              $isExpired = stripos($policyStatusName, 'Expired') !== false || (optional($policy->end_date) && $policy->end_date->isPast());
            @endphp
            <tr class="{{ $isExpired ? 'expired-row' : ($isDFR ? 'dfr-row' : '') }}">
              <td class="bell-cell {{ $isExpired ? 'expired' : ($isDFR ? 'dfr' : '') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  <input type="radio" name="policy_select" class="bell-radio {{ $isExpired ? 'expired' : ($isDFR ? 'dfr' : 'normal') }}" value="{{ $policy->id }}" disabled {{ ($isExpired || $isDFR) ? 'checked' : '' }}>
                </div>
              </td>
              <td class="action-cell">
                <svg class="action-expand" onclick="openEditPolicy({{ $policy->id }})" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <path d="M12 2L22 12L12 22L2 12L12 2Z" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 6V18M6 12H18" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <svg class="action-clock" onclick="window.location.href='{{ route('policies.index') }}?dfr=true'" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <circle cx="12" cy="12" r="9" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 7V12L15 15" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span class="action-ellipsis" style="cursor:pointer;">⋯</span>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'policy_no')
                  <td data-column="policy_no"><a href="{{ route('policies.show', $policy->id) }}" style="color:#007bff; text-decoration:underline;">{{ $policy->policy_no }}</a></td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">
                    @php
                      $clientName = $policy->client_name;
                    @endphp
                    @if($clientName)
                      {{ $clientName }}
                    @else
                      <span style="color:#999;" title="Client ID: {{ $policy->client_id ?? 'NULL' }}">—</span>
                    @endif
                  </td>
                @elseif($col == 'insurer')
                  <td data-column="insurer">
                    @php
                      $insurerName = $policy->insurer_name;
                    @endphp
                    @if($insurerName)
                      {{ $insurerName }}
                    @else
                      <span style="color:#999;" title="Insurer ID: {{ $policy->insurer_id ?? 'NULL' }}">—</span>
                    @endif
                  </td>
                @elseif($col == 'policy_class')
                  <td data-column="policy_class">
                    @php
                      $className = $policy->policy_class_name;
                    @endphp
                    @if($className)
                      {{ $className }}
                    @else
                      <span style="color:#999;" title="Class ID: {{ $policy->policy_class_id ?? 'NULL' }}">—</span>
                    @endif
                  </td>
                @elseif($col == 'policy_plan')
                  <td data-column="policy_plan">
                    @php
                      $planName = $policy->policy_plan_name;
                    @endphp
                    @if($planName)
                      {{ $planName }}
                    @else
                      <span style="color:#999;" title="Plan ID: {{ $policy->policy_plan_id ?? 'NULL' }}">—</span>
                    @endif
                  </td>
                @elseif($col == 'sum_insured')
                  <td data-column="sum_insured">{{ $policy->sum_insured ? number_format($policy->sum_insured,2) : '###########' }}</td>
                @elseif($col == 'start_date')
                  <td data-column="start_date">{{ $policy->start_date ? $policy->start_date->format('d-M-y') : '###########' }}</td>
                @elseif($col == 'end_date')
                  <td data-column="end_date">{{ $policy->end_date ? $policy->end_date->format('d-M-y') : '###########' }}</td>
                @elseif($col == 'insured')
                  <td data-column="insured">{{ $policy->insured ?? '###########' }}</td>
                @elseif($col == 'policy_status')
                  @php
                    $statusColor = '#6c757d';
                    // Check for expired first (before DFR check)
                    if ($isExpired || stripos($policyStatusName, 'Expired') !== false) {
                      $statusColor = '#dc3545'; // Red for expired
                    } elseif ($isDFR || stripos($policyStatusName, 'DFR') !== false || stripos($policyStatusName, 'Due') !== false) {
                      $statusColor = '#ffc107'; // Yellow for DFR
                    } elseif (stripos($policyStatusName, 'In Force') !== false) {
                      $statusColor = '#28a745'; // Green for in force
                    } elseif (stripos($policyStatusName, 'Cancelled') !== false) {
                      $statusColor = '#dc3545'; // Red for cancelled
                    }
                  @endphp
                  <td data-column="policy_status"><span class="badge-status" style="background:{{ $statusColor }}">{{ $policyStatusName }}</span></td>
                @elseif($col == 'date_registered')
                  <td data-column="date_registered">{{ $policy->date_registered ? $policy->date_registered->format('d-M-y') : '###########' }}</td>
                @elseif($col == 'policy_id')
                  <td data-column="policy_id">{{ $policy->policy_code ?? $policy->id }}</td>
                @elseif($col == 'insured_item')
                  <td data-column="insured_item">{{ $policy->insured_item ?? '-' }}</td>
                @elseif($col == 'renewable')
                  <td data-column="renewable">{{ $policy->renewable ? 'Yes' : 'No' }}</td>
                @elseif($col == 'biz_type')
                  <td data-column="biz_type">{{ $policy->business_type_name ?? 'N/A' }}</td>
                @elseif($col == 'term')
                  <td data-column="term">{{ $policy->term ?? '-' }}</td>
                @elseif($col == 'term_unit')
                  <td data-column="term_unit">{{ $policy->term_unit ?? '-' }}</td>
                @elseif($col == 'base_premium')
                  <td data-column="base_premium">{{ $policy->base_premium ? number_format($policy->base_premium,2) : '###########' }}</td>
                @elseif($col == 'premium')
                  <td data-column="premium">{{ $policy->premium ? number_format($policy->premium,2) : '###########' }}</td>
                @elseif($col == 'frequency')
                  <td data-column="frequency">{{ $policy->frequency_name ?? 'N/A' }}</td>
                @elseif($col == 'pay_plan')
                  <td data-column="pay_plan">{{ $policy->pay_plan_name ?? 'N/A' }}</td>
                @elseif($col == 'agency')
                  <td data-column="agency">{{ $policy->agency_name ?? ($policy->agent ?? '-') }}</td>
                @elseif($col == 'agent')
                  <td data-column="agent">{{ $policy->agent ?? '-' }}</td>
                @elseif($col == 'notes')
                  <td data-column="notes">{{ $policy->notes ?? '-' }}</td>
                @endif
              @endforeach
              <!-- <td>
                <button class="btn-action btn-delete" onclick="deletePolicy({{ $policy->id }})">Delete</button>
              </td> -->
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="footer">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('policies.export', array_merge(request()->query(), ['page' => $policies->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtnFooter" type="button">Column</button>
      </div>
      <div class="footer-right">
        <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $policies->currentPage();
          $last = max(1,$policies->lastPage());
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
  </div>

  <!-- Add/Edit Policy Modal (single) -->
  <div class="modal" id="policyModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="policyModalTitle">Add Policy</h4>
 
        <button type="button" class="modal-close" onclick="closePolicyModal()">×</button>

      </div>

      <div class="modal-footer">
          <!-- <button type="button" class="btn-cancel" onclick="window.location.href='/schedule'">Schedule</button>
          <button type="button" class="btn-delete" onclick="window.location.href='/payments'">Payments</button> -->
          <button type="submit" class="btn-save" onclick="window.location.href='/vehicles'">Vehicles</button>
          <button type="button" class="btn-cancel" onclick="window.location.href='/claims'">Claims</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='/documents'">Documents</button>
          <!-- <button type="button" class="btn-delete" onclick="window.location.href='/endorsement'">Endorsement</button> -->
          <button type="submit" class="btn-save" onclick="window.location.href='/commissions'">Commissions</button>
          <!-- <button type="button" class="btn-cancel" onclick="window.location.href='/nominees'">Nominees</button> -->

        </div>
      <form id="policyForm" method="POST" action="{{ route('policies.store') }}">
        @csrf
        <div id="policyFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="policy_no">Policy No *</label>
              <input id="policy_no" name="policy_no" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="client_id">Client *</label>
              <select id="client_id" name="client_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['clients'] as $client)
                  <option value="{{ $client['id'] }}">{{ $client['client_name'] }} ({{ $client['clid'] ?? '' }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="insurer_id">Insurer *</label>
              <select id="insurer_id" name="insurer_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['insurers'] as $insurer)
                  <option value="{{ $insurer['id'] }}">{{ $insurer['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="policy_class_id">Policy Class *</label>
              <select id="policy_class_id" name="policy_class_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['policy_classes'] as $class)
                  <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="policy_plan_id">Policy Plan *</label>
              <select id="policy_plan_id" name="policy_plan_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['policy_plans'] as $plan)
                  <option value="{{ $plan['id'] }}">{{ $plan['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="sum_insured">Sum Insured</label>
              <input id="sum_insured" name="sum_insured" type="number" step="0.01" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="start_date">Start Date *</label>
              <input id="start_date" name="start_date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="end_date">End Date *</label>
              <input id="end_date" name="end_date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="insured">Insured</label>
              <input id="insured" name="insured" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="policy_status_id">Policy Status *</label>
              <select id="policy_status_id" name="policy_status_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['policy_statuses'] as $status)
                  <option value="{{ $status['id'] ?? '' }}">{{ $status['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="date_registered">Date Registered *</label>
              <input id="date_registered" name="date_registered" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="policy_code">Policy Code</label>
              <input id="policy_code" name="policy_code" class="form-control" placeholder="Auto-generated if empty">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="insured_item">Insured Item</label>
              <input id="insured_item" name="insured_item" class="form-control">
            </div>
            <div class="form-group">
              <label for="renewable">Renewable</label>
              <select id="renewable" name="renewable" class="form-control">
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>
            <div class="form-group">
              <label for="business_type_id">Business Type *</label>
              <select id="business_type_id" name="business_type_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['business_types'] as $bizType)
                  <option value="{{ $bizType['id'] ?? '' }}">{{ $bizType['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="term">Term *</label>
              <input id="term" name="term" type="number" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="term_unit">Term Unit *</label>
              <select id="term_unit" name="term_unit" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['term_units'] as $unit)
                  <option value="{{ $unit['name'] }}">{{ $unit['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="base_premium">Base Premium *</label>
              <input id="base_premium" name="base_premium" type="number" step="0.01" class="form-control" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="premium">Premium *</label>
              <input id="premium" name="premium" type="number" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="frequency_id">Frequency *</label>
              <select id="frequency_id" name="frequency_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['frequencies'] as $freq)
                  <option value="{{ $freq['id'] ?? '' }}">{{ $freq['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="pay_plan_lookup_id">Pay Plan *</label>
              <select id="pay_plan_lookup_id" name="pay_plan_lookup_id" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['pay_plans'] as $payPlan)
                  <option value="{{ $payPlan['id'] ?? '' }}">{{ $payPlan['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="agency_id">Agency</label>
              <select id="agency_id" name="agency_id" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['agencies'] ?? [] as $agency)
                  <option value="{{ $agency['id'] }}">{{ $agency['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="agent">Agent</label>
              <input id="agent" name="agent" class="form-control">
            </div>
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closePolicyModal()">Cancel</button>
          <button type="button" class="btn-delete" id="policyDeleteBtn" style="display:none;" onclick="deletePolicy()">Delete</button>
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
        <form id="columnForm" action="{{ route('policies.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @foreach($orderedColumns as $key)
              @if(isset($columnDefinitions[$key]))
                <div class="column-item" draggable="true" data-column="{{ $key }}" style="cursor:move;">
                  <span style="cursor:move; margin-right:8px; font-size:16px; color:#666;">☰</span>
                  <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if(in_array($key,$selectedColumns)) checked @endif disabled>
                  <label for="col_{{ $key }}" style="cursor:pointer; flex:1; user-select:none;">{{ $columnDefinitions[$key]['label'] }}</label>
                </div>
              @endif
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
  let currentPolicyId = null;
  const lookupData = @json($lookupData);
  const selectedColumns = @json($selectedColumns);

  document.getElementById('addPolicyBtn').addEventListener('click', () => openPolicyModal('add'));
  const columnBtnFooter = document.getElementById('columnBtnFooter');
  if (columnBtnFooter) columnBtnFooter.addEventListener('click', () => openColumnModal());
  const closeBtn = document.getElementById('closeBtn');
  if (closeBtn) closeBtn.addEventListener('click', () => window.history.back());

  // DFR Only Filter
  (function(){
    const btn = document.getElementById('dfrOnlyBtn');
    btn.addEventListener('click', () => {
      const rows = document.querySelectorAll('tbody tr');
      let showDfrOnly = btn.dataset.active === '1';
      showDfrOnly = !showDfrOnly;
      btn.dataset.active = showDfrOnly ? '1' : '0';
      rows.forEach(row => {
        if (showDfrOnly) {
          if (row.classList.contains('dfr-row')) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
          btn.style.backgroundColor = '#dc3545';
          btn.textContent = 'Show All';
        } else {
          row.style.display = '';
          btn.style.backgroundColor = '#000';
          btn.textContent = 'Due For Renewal';
        }
      });
    });
  })();

  async function openEditPolicy(id){
    try {
      const res = await fetch(`/policies/${id}/edit`, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error('Network error');
      const policy = await res.json();
      currentPolicyId = id;
      openPolicyModal('edit', policy);
    } catch (e) {
      console.error(e);
      alert('Error loading policy data');
    }
  }

  function openPolicyModal(mode, policy = null){
    const modal = document.getElementById('policyModal');
    const title = document.getElementById('policyModalTitle');
    const form = document.getElementById('policyForm');
    const formMethod = document.getElementById('policyFormMethod');
    const deleteBtn = document.getElementById('policyDeleteBtn');

    if (mode === 'add') {
      title.textContent = 'Add Policy';
      form.action = '{{ route("policies.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      form.reset();
    } else {
      title.textContent = 'Edit Policy';
      form.action = `/policies/${currentPolicyId}`;
      formMethod.innerHTML = `@method('PUT')`;
      deleteBtn.style.display = 'inline-block';

      // Populate fields - map old field names to new ones
      const fieldMap = {
        'policy_no': 'policy_no',
        'client_name': 'client_id',
        'insurer': 'insurer_id',
        'policy_class': 'policy_class_id',
        'policy_plan': 'policy_plan_id',
        'sum_insured': 'sum_insured',
        'start_date': 'start_date',
        'end_date': 'end_date',
        'insured': 'insured',
        'policy_status': 'policy_status_id',
        'date_registered': 'date_registered',
        'policy_id': 'policy_code',
        'insured_item': 'insured_item',
        'renewable': 'renewable',
        'biz_type': 'business_type_id',
        'term': 'term',
        'term_unit': 'term_unit',
        'base_premium': 'base_premium',
        'premium': 'premium',
        'frequency': 'frequency_id',
        'pay_plan': 'pay_plan_lookup_id',
        'agency': 'agency_id',
        'agent': 'agent',
        'notes': 'notes'
      };

      Object.keys(fieldMap).forEach(oldKey => {
        const newKey = fieldMap[oldKey];
        const el = document.getElementById(newKey);
        if (!el) return;
        
        let value = null;
        if (policy[oldKey] !== undefined) {
          value = policy[oldKey];
        } else if (policy[newKey] !== undefined) {
          value = policy[newKey];
        }
        
        if (el.type === 'date') {
          el.value = value ? (typeof value === 'string' ? value.substring(0,10) : value) : '';
        } else if (el.tagName === 'SELECT') {
          el.value = value ?? '';
        } else if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
          el.value = value ?? '';
        }
      });
    }

    document.body.style.overflow = 'hidden';
    modal.classList.add('show');
  }

  function closePolicyModal(){
    document.getElementById('policyModal').classList.remove('show');
    currentPolicyId = null;
    document.body.style.overflow = '';
  }

  function deletePolicy(id=null){
    if (!id && !currentPolicyId) return;
    if (!confirm('Delete this policy?')) return;
    const policyId = id || currentPolicyId;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/policies/${policyId}`;
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
    // Ensure all items are draggable
    const columnItems = document.querySelectorAll('#columnSelection .column-item');
    columnItems.forEach(item => {
      item.setAttribute('draggable', 'true');
      item.style.cursor = 'move';
    });
    // Initialize drag and drop after modal is shown
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
    // Get order from DOM - this preserves the drag and drop order
    const items = Array.from(document.querySelectorAll('#columnSelection .column-item'));
    const order = items.map(item => item.dataset.column);
    const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n=>n.value);
    
    // Maintain order of checked items based on DOM order (drag and drop order)
    const orderedChecked = order.filter(col => checked.includes(col));
    
    const form = document.getElementById('columnForm');
    const existing = form.querySelectorAll('input[name="columns[]"]'); 
    existing.forEach(e=>e.remove());
    
    // Add columns in the order they appear in the DOM (after drag and drop)
    orderedChecked.forEach(c => {
      const i = document.createElement('input'); 
      i.type='hidden'; 
      i.name='columns[]'; 
      i.value=c; 
      form.appendChild(i);
    });
    
    form.submit();
    toggleTableScroll();
  }

  // Drag and drop functionality
  let draggedElement = null;
  let dragOverElement = null;
  
  // Initialize drag and drop when column modal opens
  let dragInitialized = false;
  function initDragAndDrop() {
    const columnSelection = document.getElementById('columnSelection');
    if (!columnSelection) return;
    
    // Only initialize once to avoid duplicate event listeners
    if (dragInitialized) {
      // Re-enable draggable on all items
      const columnItems = columnSelection.querySelectorAll('.column-item');
      columnItems.forEach(item => {
        item.setAttribute('draggable', 'true');
      });
      return;
    }
    
    // Make all column items draggable
    const columnItems = columnSelection.querySelectorAll('.column-item');
    
    columnItems.forEach(item => {
      // Ensure draggable attribute is set
      item.setAttribute('draggable', 'true');
      item.style.cursor = 'move';
      
      // Drag start
      item.addEventListener('dragstart', function(e) {
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', ''); // Required for Firefox
        // Create a ghost image
        const dragImage = this.cloneNode(true);
        dragImage.style.opacity = '0.5';
        document.body.appendChild(dragImage);
        e.dataTransfer.setDragImage(dragImage, 0, 0);
        setTimeout(() => {
          if (document.body.contains(dragImage)) {
            document.body.removeChild(dragImage);
          }
        }, 0);
      });
      
      // Drag end
      item.addEventListener('dragend', function(e) {
        this.classList.remove('dragging');
        if (dragOverElement) {
          dragOverElement.classList.remove('drag-over');
          dragOverElement = null;
        }
        draggedElement = null;
      });
      
      // Drag over
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
      
      // Drag leave
      item.addEventListener('dragleave', function(e) {
        if (!this.contains(e.relatedTarget)) {
          this.classList.remove('drag-over');
          if (dragOverElement === this) {
            dragOverElement = null;
          }
        }
      });
      
      // Drop
      item.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('drag-over');
        dragOverElement = null;
        return false;
      });
    });
    
    dragInitialized = true;
  }

  // Print table function - creates a new print-friendly table
  function printTable() {
    const table = document.getElementById('policiesTable');
    if (!table) return;
    
    // Get table headers - preserve order
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    headerCells.forEach(th => {
      let headerText = '';
      const clone = th.cloneNode(true);
      const filterInput = clone.querySelector('.column-filter');
      if (filterInput) filterInput.remove();
      // Handle bell icon column
      if (clone.querySelector('svg')) {
        headerText = '🔔'; // Bell icon
      } else {
        headerText = clone.textContent.trim();
      }
      if (headerText) {
        headers.push(headerText);
      }
    });
    
    // Get table rows data
    const rows = [];
    const tableRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    tableRows.forEach(row => {
      if (row.style.display === 'none') return;
      
      const cells = [];
      const rowCells = row.querySelectorAll('td');
      rowCells.forEach((cell) => {
        let cellContent = '';
        
        // Handle bell column (notification)
        if (cell.classList.contains('bell-cell')) {
          const radio = cell.querySelector('input[type="radio"]');
          if (radio && radio.checked) {
            cellContent = '●'; // Filled circle for checked
          } else {
            cellContent = '○'; // Empty circle for unchecked
          }
        }
        // Handle action column
        else if (cell.classList.contains('action-cell')) {
          const expandIcon = cell.querySelector('.action-expand');
          const clockIcon = cell.querySelector('.action-clock');
          const ellipsis = cell.querySelector('.action-ellipsis');
          const radio = cell.querySelector('.action-radio');
          const icons = [];
          if (radio && radio.checked) icons.push('●');
          if (expandIcon) icons.push('⤢');
          if (clockIcon) icons.push('🕐');
          if (ellipsis) icons.push('⋯');
          cellContent = icons.join(' ');
        } 
        // Handle regular cells
        else {
          const link = cell.querySelector('a');
          if (link) {
            cellContent = link.textContent.trim();
          } else {
            cellContent = cell.textContent.trim();
          }
        }
        
        cells.push(cellContent || '-');
      });
      rows.push(cells);
    });
    
    // Escape HTML to prevent XSS and syntax issues
    function escapeHtml(text) {
      if (!text) return '';
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    
    // Build headers HTML
    const headersHTML = headers.map(h => '<th>' + escapeHtml(h) + '</th>').join('');
    
    // Build rows HTML
    const rowsHTML = rows.map(row => {
      const cellsHTML = row.map(cell => {
        const cellText = escapeHtml(String(cell || '-'));
        return '<td>' + cellText + '</td>';
      }).join('');
      return '<tr>' + cellsHTML + '</tr>';
    }).join('');
    
    // Create print window
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    const printHTML = '<!DOCTYPE html>' +
      '<html>' +
      '<head>' +
      '<title>Policies - Print</title>' +
      '<style>' +
      '@page { margin: 1cm; size: A4 landscape; }' +
      'html, body { margin: 0; padding: 0; background: #fff !important; }' +
      'body { font-family: Arial, sans-serif; font-size: 10px; }' +
      'table { width: 100%; border-collapse: collapse; page-break-inside: auto; }' +
      'thead { display: table-header-group; }' +
      'thead th { background-color: #000 !important; color: #fff !important; padding: 8px 5px; text-align: left; border: 1px solid #333; font-weight: normal; -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
      'tbody tr { page-break-inside: avoid; border-bottom: 1px solid #ddd; }' +
      'tbody tr:nth-child(even) { background-color: #f8f8f8; }' +
      'tbody td { padding: 6px 5px; border: 1px solid #ddd; white-space: nowrap; }' +
      '</style>' +
      '</head>' +
      '<body>' +
      '<table>' +
      '<thead><tr>' + headersHTML + '</tr></thead>' +
      '<tbody>' + rowsHTML + '</tbody>' +
      '</table>' +
      '<scr' + 'ipt>' +
      'window.onload = function() {' +
      '  setTimeout(function() {' +
      '    window.print();' +
      '  }, 100);' +
      '};' +
      'window.onafterprint = function() {' +
      '  window.close();' +
      '};' +
      '</scr' + 'ipt>' +
      '</body>' +
      '</html>';
    
    if (printWindow) {
      printWindow.document.open();
      printWindow.document.write(printHTML);
      printWindow.document.close();
    }
  }


  // Close modals on ESC or backdrop
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closePolicyModal(); closeColumnModal(); } });
  document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) { m.classList.remove('show'); document.body.style.overflow = ''; } });
  });

  // Simple validation
  document.getElementById('policyForm').addEventListener('submit', function(e){
    const req = this.querySelectorAll('[required]');
    let ok = true;
    req.forEach(f => { if (!String(f.value||'').trim()) { ok = false; f.style.borderColor='red'; } else { f.style.borderColor=''; } });
    if (!ok) { e.preventDefault(); alert('Please fill required fields'); }
  });

  // Toggle scrollbar helper for responsive table
  function toggleTableScroll() {
    const table = document.getElementById('policiesTable');
    const wrapper = document.getElementById('tableResponsive');
    if (!table || !wrapper) return;
    const hasHorizontalOverflow = table.offsetWidth > wrapper.offsetWidth;
    const hasVerticalOverflow = table.offsetHeight > wrapper.offsetHeight;
    wrapper.classList.toggle('no-scroll', !hasHorizontalOverflow && !hasVerticalOverflow);
  }
  window.addEventListener('load', toggleTableScroll);
  window.addEventListener('resize', toggleTableScroll);

  // Filter toggle functionality
  document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
      filterToggle.addEventListener('change', function() {
        const filters = document.querySelectorAll('.column-filter');
        filters.forEach(filter => {
          if (this.checked) {
            filter.style.display = 'block';
            filter.classList.add('visible');
          } else {
            filter.style.display = 'none';
            filter.classList.remove('visible');
            filter.value = '';
          }
        });
        applyFilters();
      });
    }

    // Add event listeners to all column filters
    document.querySelectorAll('.column-filter').forEach(filter => {
      filter.addEventListener('input', applyFilters);
    });
  });

  // Column filter functionality - apply all filters together
  function applyFilters() {
    const rows = document.querySelectorAll('tbody tr');
    const activeFilters = {};
    
    // Collect all active filter values
    document.querySelectorAll('.column-filter.visible').forEach(filter => {
      const column = filter.dataset.column;
      const value = filter.value.trim().toLowerCase();
      if (value) {
        activeFilters[column] = value;
      }
    });
    
    // Apply filters to rows
    rows.forEach(row => {
      let shouldShow = true;
      
      // Check if row matches all active filters
      for (const [column, filterValue] of Object.entries(activeFilters)) {
        const cell = row.querySelector(`td[data-column="${column}"]`);
        if (cell) {
          const cellText = cell.textContent.toLowerCase();
          if (!cellText.includes(filterValue)) {
            shouldShow = false;
            break;
          }
        } else {
          shouldShow = false;
          break;
        }
      }
      
      row.style.display = shouldShow ? '' : 'none';
    });
    
    // Update records count
    const visibleRows = Array.from(document.querySelectorAll('tbody tr')).filter(row => {
      return row.style.display !== 'none' && !row.style.display.includes('none');
    }).length;
    const recordsFound = document.querySelector('.records-found');
    if (recordsFound && Object.keys(activeFilters).length > 0) {
      const total = {{ $policies->total() }};
      recordsFound.textContent = `Records Found - ${visibleRows} of ${total} (filtered)`;
    } else if (recordsFound) {
      recordsFound.textContent = `Records Found - {{ $policies->total() }}`;
    }
  }

</script>
@endsection
</body>
</html>