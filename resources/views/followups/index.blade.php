@extends('layouts.app')

@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/followups-index.css') }}?v={{ time() }}">

<div class="dashboard">
  <div class="clients-table-view" id="clientsTableView">
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0; font-size:18px; font-weight:600;">Follow Ups</h3>
      </div>
    </div>

    <div class="container-table">
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
        <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
          <div class="records-found">Records 1 to {{ min(15, count($followups)) }} of {{ count($followups) }}</div>
          <div class="page-title-section">
            <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
              <div class="filter-group" style="display:flex; align-items:center; gap:10px;">
                <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
                  <input type="checkbox" id="showAllToggle" {{ request()->has('show_all') ? 'checked' : '' }}>
                  <span style="font-size:13px;">Show Completed</span>
                </label>
              </div>
            </div>
          </div>
          <div class="action-buttons">
            <button class="btn btn-back" onclick="handleBack()">Back</button>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">&times;</button>
          </div>
        @endif

        <div class="table-responsive" id="tableResponsive">
          <table id="followupsTable">
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
              @forelse($followups as $followup)
                <tr class="{{ $followup->is_overdue ? 'overdue' : '' }}">
                  <td class="bell-cell {{ $followup->is_overdue ? 'expired' : '' }}">
                    <div style="display:flex; align-items:center; justify-content:center;">
                      <div class="status-indicator {{ $followup->is_overdue ? 'expired' : 'normal' }}"
                           style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $followup->is_overdue ? '#dc3545' : '#ccc' }};
                                  background-color:{{ $followup->is_overdue ? '#dc3545' : 'transparent' }};">
                      </div>
                    </div>
                  </td>
                  <td class="action-cell">
                    <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand"
                         onclick="openEditFollowup('{{ $followup->id }}', '{{ $followup->source }}')" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
                  </td>
                  @foreach($selectedColumns as $col)
                    @php
                      $value = '-';
                    @endphp
                    @switch($col)
                      @case('fuid')
                        @php $value = $followup->fuid ?? '-'; @endphp
                        @break
                      @case('due_date')
                        @php $value = $followup->due_date ? \Carbon\Carbon::parse($followup->due_date)->format('d-M-y') : '-'; @endphp
                        @break
                      @case('due_in')
                        @php $value = $followup->due_in !== '' ? $followup->due_in : '-'; @endphp
                        @break
                      @case('category')
                        @php $value = $followup->category ?? '-'; @endphp
                        @break
                      @case('name')
                        @php $value = $followup->name ?? '-'; @endphp
                        @break
                      @case('follow_up_note')
                        @php $value = $followup->follow_up_note ?? '-'; @endphp
                        @break
                      @case('contact_no')
                        @php $value = $followup->contact_no ?? '-'; @endphp
                        @break
                      @case('policy_no')
                        @php $value = $followup->policy_no ?? '-'; @endphp
                        @break
                      @case('fu_status')
                        @php $value = $followup->fu_status ?? '-'; @endphp
                        @break
                      @case('date_done')
                        @php $value = $followup->date_done ? \Carbon\Carbon::parse($followup->date_done)->format('d/m/Y') : '-'; @endphp
                        @break
                      @case('comment')
                        @php $value = $followup->comment ?? '-'; @endphp
                        @break
                    @endswitch
                    <td data-column="{{ $col }}">{{ $value }}</td>
                  @endforeach
                </tr>
              @empty
                <tr>
                  <td colspan="{{ count($selectedColumns) + 2 }}" style="text-align:center; padding:20px;">No follow ups found</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
          <div class="footer-left">
            <a class="btn btn-export" href="{{ route('followups.export', request()->query()) }}">Export</a>
            <button class="btn btn-column" id="columnBtn" type="button">Column</button>
          </div>
          <div class="paginator">
            @php
              $total = count($followups);
              $perPage = 15;
              $currentPage = request()->get('page', 1);
              $lastPage = max(1, ceil($total / $perPage));
            @endphp
            <a class="btn-page" href="{{ $currentPage > 1 ? url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => 1])) : '#' }}" @if($currentPage <= 1) disabled @endif>&laquo;</a>
            <a class="btn-page" href="{{ $currentPage > 1 ? url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => $currentPage - 1])) : '#' }}" @if($currentPage <= 1) disabled @endif>&lsaquo;</a>
            <span style="padding:0 8px;">Page {{ $currentPage }} of {{ $lastPage }}</span>
            <a class="btn-page" href="{{ $currentPage < $lastPage ? url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => $currentPage + 1])) : '#' }}" @if($currentPage >= $lastPage) disabled @endif>&rsaquo;</a>
            <a class="btn-page" href="{{ $currentPage < $lastPage ? url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => $lastPage])) : '#' }}" @if($currentPage >= $lastPage) disabled @endif>&raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Column Selection Modal - Same UI as Tasks -->
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
        <form id="columnForm" action="{{ route('followups.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection-vertical" id="columnSelection">
            @php
              $all = [
                'fuid' => 'FUID',
                'due_date' => 'Due Date',
                'due_in' => 'Due in',
                'category' => 'Category',
                'name' => 'Name',
                'follow_up_note' => 'Follow Up Note',
                'contact_no' => 'Contact No',
                'policy_no' => 'Policy No',
                'fu_status' => 'FU Status',
                'date_done' => 'Date Done',
                'comment' => 'Comment',
              ];
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

            @php
              $mandatoryFields = $mandatoryColumns;
              $counter = 1;
            @endphp
            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryFields);
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
  let selectedColumns = @json($selectedColumns ?? []);
  const mandatoryColumns = @json($mandatoryColumns ?? []);
  const csrfToken = '{{ csrf_token() }}';
  const fromCalendar = '{{ request()->get("from_calendar", "") }}';
  const startDate = '{{ request()->get("start_date", "") }}';
  const endDate = '{{ request()->get("end_date", "") }}';
</script>
<script src="{{ asset('js/followups-index.js') }}?v={{ time() }}"></script>
@endsection
