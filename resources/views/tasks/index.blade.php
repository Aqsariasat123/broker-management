
@extends('layouts.app')

@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/tasks-index.css') }}">




@php
  $config = \App\Helpers\TableConfigHelper::getConfig('tasks');
  $selectedColumns = session('task_columns', $config['default_columns'] ?? []);
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Tasks Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
            {{ request()->has('filter') && request()->filter== 'overdue' ? 'Tasks - Overdue' : 'Tasks' }}
          </h3>
       
      </div>
    </div>
  <div class="container-table">
    <!-- Tasks Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="records-found">Records Found - {{ $tasks->total() }}</div>
      <div class="page-title-section">
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group" style="display:flex; align-items:center; gap:10px;">
            <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
              <span style="font-size:13px;">Filter</span>
              <input type="checkbox" id="filterToggle" {{ request()->has('filter') && request()->filter =='overdue' ? 'checked' : '' }}>
            </label>
            @if(request()->has('filter') && request()->filter== 'overdue')
              <button class="btn" id="listAllBtn" type="button" style="background:#28a745; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">List ALL</button>
            @else
              <button class="btn btn-overdue" id="overdueOnly" type="button" style="background:{{ request()->has('overdue') && request()->overdue ? '#000' : '#000' }}; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Overdue Only</button>
            @endif
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addTaskBtn">Add</button>
        <button class="btn btn-back" onclick="handleBack()">Back</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
            <table id="tasksTable">
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
                        {{ $columnDefinitions[$col] }}
                      </th>
                    @endif
                  @endforeach
                
                </tr>
              </thead>
              <tbody>
               @foreach($tasks as $task)
<tr class="{{ $task->isOverdue() ? 'overdue' : '' }}">
    <td class="bell-cell {{ $task->isOverdue() ? 'expired' :'' }}">
        <div style="display:flex; align-items:center; justify-content:center;">
            @php
                $isExpired = $task->isOverdue();
            @endphp
            <div class="status-indicator {{ $isExpired ? 'expired' : 'normal' }}"
                 style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $isExpired ? '#dc3545' : '#ccc' }}; 
                        background-color:{{ $isExpired ? '#dc3545' : 'transparent' }};">
            </div>
        </div>
    </td>

    <td class="action-cell">
        <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" 
             onclick="openEditTask({{ $task->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
    </td>

        @foreach($selectedColumns as $col)
            @php
                $value = '-';
            @endphp

            @switch($col)
                @case('task_id')
                    @php $value = $task->task_id; @endphp
                    @break
                @case('category')
                    @php $value = $task->category ?? '-'; @endphp
                    @break
                @case('item')
                    @php $value = $task->item ?? '-'; @endphp
                    @break
                @case('description')
                    @php $value = $task->description ?? '-'; @endphp
                    @break
                @case('name')
                    @php
                        $value = $task->name ?? '-';
                    @endphp
                    @break
                @case('contact_no')
                    @php
                        $value = $task->contact_no ?? '-';
                    @endphp
                    @break
                @case('due_date')
                    @php $value = $task->due_date ? $task->due_date->format('d-M-y') : ''; @endphp
                    @break
                @case('due_time')
                    @php $value = $task->due_time ?? ''; @endphp
                    @break
                @case('due_in')
                    @php $value = $task->getDueInDays() ?? '-'; @endphp
                    @break
                @case('date_in')
                    @php $value = $task->date_in ? $task->date_in->format('d-M-y') : ''; @endphp
                    @break
                @case('assignee')
                    @php $value = $task->assignee ?? '-'; @endphp
                    @break
                @case('task_status')
                    @php $value = $task->task_status ?? '-'; @endphp
                    @break
                @case('date_done')
                    @php $value = $task->date_done ? $task->date_done->format('d-M-y') : ''; @endphp
                    @break
                @case('repeat')
                    @php $value = $task->repeat ? 'Y' : 'N'; @endphp
                    @break
                @case('frequency')
                    @php $value = $task->frequency ?? '-'; @endphp
                    @break
                @case('rpt_date')
                    @php $value = $task->rpt_date ? $task->rpt_date->format('d-M-y') : ''; @endphp
                    @break
                @case('rpt_stop_date')
                    @php $value = $task->rpt_stop_date ? $task->rpt_stop_date->format('d-M-y') : ''; @endphp
                    @break
            @endswitch

            <td data-column="{{ $col }}">{{ $value }}</td>
          @endforeach
        </tr>
        @endforeach

              </tbody>
            </table>
          </div>

          <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
             <div class="footer-left">
              <a class="btn btn-export" href="{{ route('tasks.export', array_merge(request()->query(), ['page' => $tasks->currentPage()])) }}">Export</a>
              <button class="btn btn-column" id="columnBtn" type="button">Column</button>
               <button class="btn btn-export" id="printBtn" type="button" style="margin-left:10px;">Print</button>

            </div>
            <div class="paginator">
              @php
                $base = url()->current();
                $q = request()->query();
                $current = $tasks->currentPage();
                $last = max(1, $tasks->lastPage());
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
  </div>

  <!-- Add/Edit Task Modal (hidden, used for form structure) -->
  <div class="modal" id="taskModal">
    <div class="modal-content">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #ddd;">
        <h4 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: bold;">Add Task</h4>
        <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn-delete" id="deleteBtn" style="display: none;" onclick="deleteTask()">Delete</button>
          <button type="submit" form="taskForm" class="btn-save" style="background: #f3742a; color: #fff; border: none; padding: 6px 16px; border-radius: 2px; cursor: pointer;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeModal()" style="background: #000; color: #fff; border: none; padding: 6px 16px; border-radius: 2px; cursor: pointer;">Close</button>
        </div>
      </div>
      <form id="taskForm" method="POST">
        @csrf
        <div id="formMethod" style="display: none;"></div>
        
        <div class="modal-body" style="padding: 20px;">
          <!-- ALWAYS render inputs for add/edit so JS can set values and server validation can run.
               Column selection only affects table display, not the add/edit form. -->
          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
              <label for="category" style="display: block; margin-bottom: 5px; font-weight: 500;">Category</label>
              <select class="form-control" id="category" name="category" required style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="item" style="display: block; margin-bottom: 5px; font-weight: 500;">Item</label>
              <input type="text" class="form-control" id="item" name="item" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
            </div>
            <div class="form-group" style="display:none;">
              <label for="description" >Description</label>
              <input type="hidden" id="description" name="description" value="">
            </div>
            <div class="form-group">
              <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500;">Name</label>
              <select class="form-control" id="name" name="name" required style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
                <option value="">Select Name</option>
                @foreach($contacts as $contact)
                  <option value="{{ $contact->name }}">{{ $contact->name }}</option>
                @endforeach
                @foreach($clients as $client)
                  <option value="{{ $client->name }}">{{ $client->name }}</option>
                @endforeach
              </select>
            </div>
           
          </div>

        

          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
               <div class="form-group">
              <label for="contact_no" style="display: block; margin-bottom: 5px; font-weight: 500;">Contact No.</label>
              <input type="number" class="form-control" id="contact_no" name="contact_no" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
            </div>
            <div class="form-group"  style="display:none;">
              <label for="assignee_small">placeholder</label>
              <input type="hidden" id="assignee_small" name="assignee_small">
            </div>
            <div class="form-group">
              <label for="due_date" style="display: block; margin-bottom: 5px; font-weight: 500;">Due Date</label>
              <input type="date" class="form-control" id="due_date" name="due_date" required style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
            </div>
            <div class="form-group">
              <label for="due_time" style="display: block; margin-bottom: 5px; font-weight: 500;">Due Time</label>
              <input type="time" class="form-control" id="due_time" name="due_time" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
            </div>
            <div class="form-group">
              <label for="date_in" style="visibility:hidden">placeholder</label>
              <input type="hidden" id="date_in" name="date_in">
            </div>
          </div>

          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
              <label for="assignee" style="display: block; margin-bottom: 5px; font-weight: 500;">Assignee</label>
              <select class="form-control" id="assignee" name="assignee" required style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
                <option value="">Select Assignee</option>
                @foreach($users as $user)
                  <option value="{{ $user->name }}">{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="task_status" style="display: block; margin-bottom: 5px; font-weight: 500;">Task Status</label>
              <select class="form-control" id="task_status" name="task_status" required style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
                <option value="Not Done">Not Done</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
              </select>
            </div>
            <div class="form-group">
              <label for="date_done" style="display: block; margin-bottom: 5px; font-weight: 500;">Date Done</label>
              <input type="date" class="form-control" id="date_done" name="date_done" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
            </div>
          </div>

          <div class="form-row" style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
              <label for="task_notes" style="display: block; margin-bottom: 5px; font-weight: 500;">Task Notes</label>
              <textarea class="form-control" id="task_notes" name="task_notes" rows="3" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px; resize: vertical;"></textarea>
            </div>
          </div>

          <div style="border: 1px solid #ddd; padding: 12px; margin-bottom: 12px;">

            <h5 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 500;">Repeat / Frequency</h5>
            
       
            
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
              <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" id="repeat" name="repeat" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="repeat" style="margin: 0; cursor: pointer;">Repeat</label>
                <select class="form-control" id="frequency" name="frequency" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
                  <option value="">Select Frequency</option>
                  @if($frequencyCategories && $frequencyCategories->values)
                    @foreach($frequencyCategories->values as $freq)
                      <option value="{{ $freq->name }}">{{ $freq->name }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            
              <div class="form-group">
                <label for="rpt_date" style="display: block; margin-bottom: 5px; font-weight: 500;">Repeat Date</label>
                <input type="date" class="form-control" id="rpt_date" name="rpt_date" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
              </div>
              <div class="form-group">
                <label for="rpt_stop_date" style="display: block; margin-bottom: 5px; font-weight: 500;">Repeat Stop Date</label>
                <input type="date" class="form-control" id="rpt_stop_date" name="rpt_stop_date" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
              </div>
            </div>
          </div>
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
        <form id="columnForm" action="{{ route('tasks.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection-vertical" id="columnSelection">
            @php
              $all = [
                'task_id'=>'Task ID',
                'category'=>'Category',
                'item'=>'Item',
                'description'=>'Description',
                'name'=>'Name',
                'contact_no'=>'Contact No',
                'due_date'=>'Due Date',
                'due_time'=>'Due Time',
                'due_in'=>'Due in',
                'date_in'=>'Date In',
                'assignee'=>'Assignee',
                'task_status'=>'Task Status',
                'date_done'=>'Date Done',
                'repeat'=>'Repeat',
                'frequency'=>'Frequency',
                'rpt_date'=>'Rpt Date',
                'rpt_stop_date'=>'Rpt Stop Date',
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
              // Use mandatory columns from config
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
  // Initialize data from Blade
  let selectedColumns = @json($selectedColumns ?? []);
  const mandatoryColumns = @json($mandatoryColumns ?? []);
  const tasksStoreRoute = '{{ route("tasks.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/tasks-index.js') }}"></script>
@endsection