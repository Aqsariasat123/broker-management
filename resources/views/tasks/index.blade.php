
@extends('layouts.app')

@section('content')

@include('partials.table-styles')

<style>
  /* Toggle Switch Styling */
  #filterToggle {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 50px;
    height: 24px;
    background-color: #ccc;
    border-radius: 12px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s;
    outline: none;
  }
  
  #filterToggle:checked {
    background-color: #28a745;
  }
  
  #filterToggle::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: white;
    top: 2px;
    left: 2px;
    transition: left 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }
  
  #filterToggle:checked::before {
    left: 28px;
  }
  
  #filterToggleLabel {
    min-width: 30px;
    display: inline-block;
  }
</style>

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('tasks');
  $selectedColumns = session('task_columns', $config['default_columns'] ?? []);
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Tasks Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Tasks Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>{{ request()->has('overdue') && request()->overdue ? 'Tasks - Overdue' : 'Tasks' }}</h3>
        <div class="records-found">Records Found - {{ $tasks->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group" style="display:flex; align-items:center; gap:10px;">
            <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
              <span style="font-size:13px;">Filter</span>
              <input type="checkbox" id="filterToggle" {{ request()->has('overdue') && request()->overdue ? 'checked' : '' }}>
            </label>
            @if(request()->has('overdue') && request()->overdue)
              <button class="btn" id="listAllBtn" type="button" style="background:#28a745; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">List ALL</button>
            @else
              <button class="btn btn-overdue" id="overdueOnly" type="button" style="background:{{ request()->has('overdue') && request()->overdue ? '#000' : '#6c757d' }}; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Overdue Only</button>
            @endif
          </div>
        </div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addTaskBtn">Add</button>
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
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
                <td class="bell-cell {{ $task->isOverdue() ? 'expired' : ($task->isExpiringSoon() ? 'expiring' : '') }}">
                  <div style="display:flex; align-items:center; justify-content:center;">
                    @php
                      $isExpired = $task->isOverdue();
                      $isExpiring = $task->isExpiringSoon();
                    @endphp
                    <div class="status-indicator {{ $isExpired ? 'expired' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $isExpired ? '#dc3545' : ($isExpiring ? '#f3742a' : 'transparent') }}; background-color:{{ $isExpired ? '#dc3545' : 'transparent' }};"></div>
                  </div>
                </td>
                  <td class="action-cell">
                    <svg class="action-expand" onclick="openEditTask({{ $task->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                      <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                      <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                      <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </td>
                      @foreach($selectedColumns as $col)
                      @if($col == 'task_id')
                        <td data-column="task_id">
                          <a href="javascript:void(0)" onclick="openEditTask({{ $task->id }})" style="color:#007bff; text-decoration:underline;">{{ $task->task_id }}</a>
                        </td>
                      @elseif($col == 'category')
                        <td data-column="category">{{ $task->category }}</td>
                      @elseif($col == 'item')
                        <td data-column="item">{{ $task->item ?? '-' }}</td>
                      @elseif($col == 'description')
                        <td data-column="description">{{ $task->description }}</td>
                      @elseif($col == 'name')
                        <td data-column="name">{{ $task->name }}</td>
                      @elseif($col == 'contact_no')
                        <td data-column="contact_no">{{ $task->contact_no }}</td>
                      @elseif($col == 'due_date')
                        <td data-column="due_date">{{ $task->due_date? \Carbon\Carbon::parse($task->due_date)->format('d-M-y') : '' }}</td>
                      @elseif($col == 'due_time')
                        <td data-column="due_time">{{ $task->due_time ? $task->due_time : '' }}</td>
                      @elseif($col == 'due_in')
                        <td data-column="due_in">
                          @php
                            $dueIn = $task->getDueInDays();
                          @endphp
                          @if($dueIn !== null)
                            {{ $dueIn }}
                          @else
                            -
                          @endif
                        </td>
                      @elseif($col == 'date_in')
                        <td data-column="date_in">{{ $task->date_in ? \Carbon\Carbon::parse($task->date_in)->format('d-M-y') : '' }}</td>
                      @elseif($col == 'assignee')
                        <td data-column="assignee">{{ $task->assignee }}</td> 
                      @elseif($col == 'task_status')
                        <td data-column="task_status">{{ $task->task_status }}</td>
                      @elseif($col == 'date_done')
                        <td data-column="date_done">{{ $task->date_done ? \Carbon\Carbon::parse($task->date_done)->format('d-M-y') : '' }}</td>
                      @elseif($col == 'repeat')
                        <td data-column="repeat">{{ $task->repeat ? 'Y' : 'N' }}</td>
                      @elseif($col == 'frequency')
                        <td data-column="frequency">{{ $task->frequency }}</td>
                      @elseif($col == 'rpt_date')
                        <td data-column="rpt_date">{{ $task->rpt_date ? \Carbon\Carbon::parse($task->rpt_date)->format('d-M-y') : '' }}</td>
                      @elseif($col == 'rpt_stop_date')
                        <td data-column="rpt_stop_date">{{ $task->rpt_stop_date ? \Carbon\Carbon::parse($task->rpt_stop_date)->format('d-M-y') : '' }}</td>
                      @endif
                    @endforeach
                
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="footer">
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
          <button type="submit" form="taskForm" class="btn-save" style="background: #f3742a; color: #fff; border: none; padding: 6px 16px; border-radius: 2px; cursor: pointer;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeModal()" style="background: #000; color: #fff; border: none; padding: 6px 16px; border-radius: 2px; cursor: pointer;">Cancel</button>
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
            <div class="form-group">
              <label for="description" style="visibility:hidden">Description</label>
              <input type="hidden" id="description" name="description" value="">
            </div>
          </div>

          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
              <label for="name" style="display: block; margin-bottom: 5px; font-weight: 500;">Name</label>
              <select class="form-control" id="name" name="name" required style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
                <option value="">Select Name</option>
                <optgroup label="Contacts">
                  @foreach($contacts as $contact)
                    <option value="{{ $contact->name }}" data-contact-no="{{ $contact->contact_no }}">{{ $contact->name }}</option>
                  @endforeach
                </optgroup>
                <optgroup label="Clients">
                  @foreach($clients as $client)
                    <option value="{{ $client->name }}" data-contact-no="{{ $client->contact_no }}">{{ $client->name }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
            <div class="form-group">
              <label for="contact_no" style="display: block; margin-bottom: 5px; font-weight: 500;">Contact No.</label>
              <input type="text" class="form-control" id="contact_no" name="contact_no" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
            </div>
            <div class="form-group">
              <label for="assignee_small" style="visibility:hidden">placeholder</label>
              <input type="hidden" id="assignee_small" name="assignee_small">
            </div>
          </div>

          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
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
            
            <div class="form-row" style="display: grid; grid-template-columns: auto 1fr; gap: 15px; margin-bottom: 15px; align-items: center;">
              <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" id="repeat" name="repeat" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="repeat" style="margin: 0; cursor: pointer;">Repeat</label>
              </div>
              <div class="form-group">
                <input type="text" class="form-control" id="frequency" name="frequency" placeholder="Frequency" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 2px;">
              </div>
            </div>
            
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
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
        
        <div class="modal-footer" style="display: none;">
          <button type="button" class="btn-delete" id="deleteBtn" style="display: none;" onclick="deleteTask()">Delete</button>
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
            <form id="columnForm" action="{{ route('tasks.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
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
            @endphp
            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryFields);
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
        <!-- <form id="columnForm" action="{{ route('tasks.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection">
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="task_id" id="col_task_id">
              <label for="col_task_id">Task ID</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="category" id="col_category">
              <label for="col_category">Category</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="description" id="col_description">
              <label for="col_description">Description</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="name" id="col_name">
              <label for="col_name">Name</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="contact_no" id="col_contact_no">
              <label for="col_contact_no">Contact No</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="due_date" id="col_due_date">
              <label for="col_due_date">Due Date</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="due_time" id="col_due_time">
              <label for="col_due_time">Due Time</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="date_in" id="col_date_in">
              <label for="col_date_in">Date In</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="assignee" id="col_assignee">
              <label for="col_assignee">Assignee</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="task_status" id="col_task_status">
              <label for="col_task_status">Task Status</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="date_done" id="col_date_done">
              <label for="col_date_done">Date Done</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="repeat" id="col_repeat">
              <label for="col_repeat">Repeat</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="frequency" id="col_frequency">
              <label for="col_frequency">Frequency</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="rpt_date" id="col_rpt_date">
              <label for="col_rpt_date">Rpt Date</label>
            </div>
            <div class="column-item">
              <input type="checkbox" class="column-checkbox" value="rpt_stop_date" id="col_rpt_stop_date">
              <label for="col_rpt_stop_date">Rpt Stop Date</label>
            </div>
          </div>
        </form> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeColumnModal()">Cancel</button>
        <button type="button" class="btn-save" onclick="saveColumnSettings()">Save Settings</button>
      </div>
    </div>
  </div>
</div>

  <script>
    let currentTaskId = null;
    let selectedColumns = @json($selectedColumns);

    // Format date helper function
    function formatDate(dateStr) {
      if (!dateStr) return '-';
      const date = new Date(dateStr);
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
    }


    // Initialize column checkboxes
    function initializeColumnCheckboxes() {
      const checkboxes = document.querySelectorAll('.column-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectedColumns.includes(checkbox.value);
      });
    }

    // Add Task Button - moved to DOMContentLoaded to ensure button exists
    // Column Button - moved to DOMContentLoaded to ensure button exists


    async function openEditTask(id) {
      try {
        const res = await fetch(`/tasks/${id}/edit`, { 
          headers: { 
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          } 
        });
        if (!res.ok) throw new Error('Network error');
        const task = await res.json();
        currentTaskId = id;
        openModalWithTask('edit', task);
      } catch (e) {
        console.error(e);
        alert('Error loading task data');
      }
    }
    
    // Open modal with task data for editing
    function openModalWithTask(mode, task) {
      const modal = document.getElementById('taskModal');
      if (!modal) {
        console.error('Modal not found');
        return;
      }
      
      const title = document.getElementById('modalTitle');
      const form = document.getElementById('taskForm');
      const deleteBtn = document.getElementById('deleteBtn');
      const formMethod = document.getElementById('formMethod');
      
      if (mode === 'edit' && task) {
        if (title) title.textContent = 'Edit Task';
        if (form) {
          form.action = `/tasks/${currentTaskId}`;
          form.method = 'POST';
        }
        if (formMethod) formMethod.innerHTML = '@method("PUT")';
        if (deleteBtn) deleteBtn.style.display = 'block';
        
        // Populate form fields
        const fields = ['category','item','description','name','contact_no','due_date','due_time','date_in','assignee','task_status','date_done','task_notes','frequency','rpt_date','rpt_stop_date'];
        fields.forEach(id => {
          const el = form.querySelector(`#${id}`);
          if (!el) return;
          if (el.type === 'checkbox') {
            el.checked = !!task[id];
          } else if (el.type === 'date') {
            // Handle date fields - format to YYYY-MM-DD
            if (task[id]) {
              let dateValue = task[id];
              if (typeof dateValue === 'string') {
                // If it's already in YYYY-MM-DD format, use it directly
                if (dateValue.match(/^\d{4}-\d{2}-\d{2}/)) {
                  el.value = dateValue.substring(0, 10);
                } else {
                  // Try to parse and format the date
                  try {
                    const date = new Date(dateValue);
                    if (!isNaN(date.getTime())) {
                      el.value = date.toISOString().substring(0, 10);
                    }
                  } catch (e) {
                    el.value = '';
                  }
                }
              } else {
                el.value = '';
              }
            } else {
              el.value = '';
            }
          } else if (el.type === 'time') {
            // Handle time fields
            if (task[id]) {
              let timeValue = task[id];
              if (typeof timeValue === 'string') {
                // If it's already in HH:MM format, use it directly
                if (timeValue.match(/^\d{2}:\d{2}/)) {
                  el.value = timeValue.substring(0, 5);
                } else {
                  el.value = timeValue;
                }
              } else {
                el.value = '';
              }
            } else {
              el.value = '';
            }
          } else if (el.tagName === 'SELECT') {
            el.value = task[id] ?? '';
          } else {
            el.value = task[id] ?? '';
          }
        });
        
        // Handle repeat checkbox
        const repeatCheckbox = form.querySelector('#repeat');
        if (repeatCheckbox) {
          repeatCheckbox.checked = !!task.repeat;
        }
        
        // Sync item to description if needed
        const itemField = form.querySelector('#item');
        const descField = form.querySelector('#description');
        if (itemField && descField && !task.description && task.item) {
          descField.value = task.item;
        } else if (itemField && descField && !descField.value && itemField.value) {
          descField.value = itemField.value;
        }
      }
      
      // prevent body scrollbar when modal open
      document.body.style.overflow = 'hidden';
      modal.classList.add('show');
      
      // Setup event listeners for modal form
      setTimeout(() => {
        setupFormEventListeners(modal);
      }, 100);
    }

    // Setup event listeners for form dropdowns
    function setupFormEventListeners(container) {
      if (!container) return;
      
      // Handle name dropdown change to auto-fill contact_no
      const nameSelect = container.querySelector('#name');
      const contactNoInput = container.querySelector('#contact_no');
      if (nameSelect && contactNoInput) {
        nameSelect.addEventListener('change', function() {
          const selectedOption = this.options[this.selectedIndex];
          if (selectedOption && selectedOption.dataset.contactNo) {
            contactNoInput.value = selectedOption.dataset.contactNo;
          }
        });
      }
      
      // Sync item to description when item changes (since description is required)
      const itemInput = container.querySelector('#item');
      const descInput = container.querySelector('#description');
      if (itemInput && descInput) {
        itemInput.addEventListener('input', function() {
          if (!descInput.value || descInput.value === itemInput.value) {
            descInput.value = this.value;
          }
        });
      }
    }


    // Edit button from details page - moved to DOMContentLoaded

    // Legacy editTask function for backward compatibility
    async function editTask(taskId) {
      openEditTask(taskId);
    }

    // Open Task Modal
    function openModal(mode) {
      const modal = document.getElementById('taskModal');
      if (!modal) {
        console.error('Modal not found');
        return;
      }
      
      const title = document.getElementById('modalTitle');
      const form = document.getElementById('taskForm');
      const deleteBtn = document.getElementById('deleteBtn');
      const formMethod = document.getElementById('formMethod');
      
      if (mode === 'add') {
        if (title) title.textContent = 'Add Task';
        if (form) {
          form.action = "{{ route('tasks.store') }}";
          form.method = 'POST';
          form.reset();
        }
        if (formMethod) formMethod.innerHTML = '';
        if (deleteBtn) deleteBtn.style.display = 'none';
        currentTaskId = null;
      } else {
        if (title) title.textContent = 'Edit Task';
        if (form) {
          form.action = `/tasks/${currentTaskId}`;
          form.method = 'POST';
        }
        if (formMethod) formMethod.innerHTML = '@method("PUT")';
        if (deleteBtn) deleteBtn.style.display = 'block';
      }
      
      // prevent body scrollbar when modal open
      document.body.style.overflow = 'hidden';
      modal.classList.add('show');
      
      // Setup event listeners for modal form
      setTimeout(() => {
        setupFormEventListeners(modal);
      }, 100);
    }

    // Close Task Modal
    function closeModal() {
      document.getElementById('taskModal').classList.remove('show');
      currentTaskId = null;
      // restore body scrollbar
      document.body.style.overflow = '';
    }

    // Open Column Modal
    function openColumnModal() {
      initializeColumnCheckboxes();
      // prevent body scrollbar when modal open
      document.body.style.overflow = 'hidden';
      document.getElementById('columnModal').classList.add('show');
         setTimeout(initDragAndDrop, 100);
    }

    // Close Column Modal
    function closeColumnModal() {
      document.getElementById('columnModal').classList.remove('show');
      // restore body scrollbar
      document.body.style.overflow = '';
    }

    // Select All Columns
    function selectAllColumns() {
      const checkboxes = document.querySelectorAll('.column-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = true;
      });
    }

    // Deselect All Columns
    function deselectAllColumns() {
      const mandatoryFields = @json($mandatoryColumns);

        document.querySelectorAll('.column-checkbox').forEach(cb => {
          // Don't uncheck mandatory fields
          if (!mandatoryFields.includes(cb.value)) {
            cb.checked = false;
          }
        });
    }

    // Save Column Settings
    function saveColumnSettings() {
      const mandatoryFields = @json($mandatoryColumns);

      const items = Array.from(document.querySelectorAll('#columnSelection .column-item'));
    const order = items.map(item => item.dataset.column);
    const checked = Array.from(document.querySelectorAll('.column-checkbox:checked')).map(n=>n.value);
    
    // Ensure mandatory fields are always included
    mandatoryFields.forEach(field => {
      if (!checked.includes(field)) {
        checked.push(field);
      }
    });
    
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
    
    }

    // Drag and drop functionality
    let draggedElement = null;
    let dragOverElement = null;

    function initDragAndDrop() {
      const columnSelection = document.getElementById('columnSelection');
      if (!columnSelection) return;
      const columnItems = columnSelection.querySelectorAll('.column-item');
      columnItems.forEach(item => {
        // Skip if already initialized
        if (item.dataset.dragInitialized === 'true') {
          return;
        }
        item.dataset.dragInitialized = 'true';
        item.setAttribute('draggable', 'true');
        
        // Prevent checkbox from interfering with drag
        const checkbox = item.querySelector('.column-checkbox');
        if (checkbox) {
          checkbox.addEventListener('mousedown', function(e) {
            e.stopPropagation();
          });
          checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
          });
        }
        
        // Prevent label from interfering with drag
        const label = item.querySelector('label');
        if (label) {
          label.addEventListener('mousedown', function(e) {
            // Only prevent if clicking on the label text, not the checkbox
            if (e.target === label) {
              e.preventDefault();
            }
          });
        }
        
        item.addEventListener('dragstart', function(e) {
          draggedElement = this;
          this.classList.add('dragging');
          e.dataTransfer.effectAllowed = 'move';
          e.dataTransfer.setData('text/html', this.outerHTML);
          e.dataTransfer.setData('text/plain', this.querySelector('.column-checkbox').value);
        });
        
        item.addEventListener('dragend', function(e) {
          this.classList.remove('dragging');
          // Remove drag-over from all items
          columnItems.forEach(i => i.classList.remove('drag-over'));
          if (dragOverElement) {
            dragOverElement.classList.remove('drag-over');
            dragOverElement = null;
          }
          draggedElement = null;
        });
        
        item.addEventListener('dragover', function(e) {
          e.preventDefault();
          e.stopPropagation();
          e.dataTransfer.dropEffect = 'move';
          
          if (draggedElement && this !== draggedElement) {
            // Remove drag-over class from previous element
            if (dragOverElement && dragOverElement !== this) {
              dragOverElement.classList.remove('drag-over');
            }
            
            // Add drag-over class to current element
            this.classList.add('drag-over');
            dragOverElement = this;
            
            const rect = this.getBoundingClientRect();
            const midpoint = rect.top + (rect.height / 2);
            const next = e.clientY > midpoint;
            
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
        
        item.addEventListener('dragenter', function(e) {
          e.preventDefault();
          if (draggedElement && this !== draggedElement) {
            this.classList.add('drag-over');
          }
        });
        
        item.addEventListener('dragleave', function(e) {
          // Only remove if we're actually leaving the element
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

    // Delete Task
    function deleteTask() {
      if (!currentTaskId) return;
      if (!confirm('Are you sure you want to delete this task?')) return;
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/tasks/${currentTaskId}`;
      const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
      const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
      document.body.appendChild(form);
      form.submit();
    }

    // Set overdue filter if parameter exists + attach overdue button handler safely after DOM ready
    document.addEventListener('DOMContentLoaded', function() {
      // initialize column checkboxes and other startup code
      initializeColumnCheckboxes();
      
      // Add Task Button
      const addTaskBtn = document.getElementById('addTaskBtn');
      if (addTaskBtn) {
        addTaskBtn.addEventListener('click', function() {
          openModal('add');
        });
      }
      
      // Column Button
      const columnBtn = document.getElementById('columnBtn');
      if (columnBtn) {
        columnBtn.addEventListener('click', function() {
          openColumnModal();
        });
      }
      
      // Setup event listeners for modal form on page load
      setupFormEventListeners(document.getElementById('taskModal'));
      
      // Handle form submission to ensure description is set
      const taskForm = document.getElementById('taskForm');
      if (taskForm) {
        taskForm.addEventListener('submit', function(e) {
          const itemField = this.querySelector('#item');
          const descField = this.querySelector('#description');
          if (descField && (!descField.value || descField.value.trim() === '') && itemField && itemField.value) {
            descField.value = itemField.value;
          }
          // Ensure description is not empty (required field)
          if (descField && (!descField.value || descField.value.trim() === '')) {
            descField.value = 'Task';
          }
        });
      }

      // Print button handler
      const printBtn = document.getElementById('printBtn');
      if (printBtn) {
        printBtn.addEventListener('click', function() {
          printTable();
        });
      }

      // determine whether overdue param is active (supports '1' or 'true')
      const urlParams = new URLSearchParams(window.location.search);
      const overdueActive = urlParams.get('overdue') === 'true' || urlParams.get('overdue') === '1';

      // Filter toggle handler
      const filterToggle = document.getElementById('filterToggle');
      const filterToggleLabel = document.getElementById('filterToggleLabel');
      if (filterToggle) {
        filterToggle.checked = overdueActive;
        if (filterToggleLabel) {
          filterToggleLabel.textContent = overdueActive ? 'ON' : 'OFF';
        }

        filterToggle.addEventListener('change', function(e) {
          const u = new URL(window.location.href);
          if (this.checked) {
            u.searchParams.set('overdue', '1');
            if (filterToggleLabel) filterToggleLabel.textContent = 'ON';
          } else {
            u.searchParams.delete('overdue');
            if (filterToggleLabel) filterToggleLabel.textContent = 'OFF';
          }
          window.location.href = u.toString();
        });
      }

      // Overdue Only button handler
      const overdueBtn = document.getElementById('overdueOnly');
      if (overdueBtn) {
        if (overdueActive) {
          overdueBtn.style.background = '#000';
          overdueBtn.style.color = '#fff';
        }

        overdueBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const u = new URL(window.location.href);
          u.searchParams.set('overdue', '1');
          window.location.href = u.toString();
        });
      }

      // List ALL button handler
      const listAllBtn = document.getElementById('listAllBtn');
      if (listAllBtn) {
        listAllBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const u = new URL(window.location.href);
          u.searchParams.delete('overdue');
          window.location.href = u.toString();
        });
      }
    });

  function printTable() {
    const table = document.getElementById('tasksTable');
    if (!table) return;
    
    // Get table headers - preserve order
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    headerCells.forEach(th => {
      let headerText = '';
      // Get text, excluding filter input
      const clone = th.cloneNode(true);
      const filterInput = clone.querySelector('.column-filter');
      if (filterInput) filterInput.remove();
      headerText = clone.textContent.trim();
      // Handle bell icon column
      if (clone.querySelector('svg')) {
        headerText = '🔔'; // Bell icon
      }
      if (headerText) {
        headers.push(headerText);
      }
    });
    
    // Get table rows data
    const rows = [];
    const tableRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    tableRows.forEach(row => {
      if (row.style.display === 'none') return; // Skip hidden rows
      
      const cells = [];
      const rowCells = row.querySelectorAll('td');
      rowCells.forEach((cell) => {
        let cellContent = '';
        
        // Handle notification column (bell-cell)
        if (cell.classList.contains('bell-cell')) {
          const statusIndicator = cell.querySelector('.status-indicator');
          if (statusIndicator) {
            if (statusIndicator.classList.contains('expired')) {
              cellContent = '●'; // Red filled circle for overdue
            } else if (cell.classList.contains('expiring')) {
              cellContent = '○'; // Yellow/orange border for expiring
            } else {
              cellContent = ''; // No indicator
            }
          } else {
            cellContent = '';
          }
        } 
        // Handle action column
        else if (cell.classList.contains('action-cell')) {
          const expandIcon = cell.querySelector('.action-expand');
          const clockIcon = cell.querySelector('.action-clock');
          const ellipsis = cell.querySelector('.action-ellipsis');
          const icons = [];
          if (expandIcon) icons.push('⤢');
          if (clockIcon) icons.push('🕐');
          if (ellipsis) icons.push('⋯');
          cellContent = icons.join(' ');
        } 
        // Handle checkbox cells
        else if (cell.classList.contains('checkbox-cell')) {
          const checkbox = cell.querySelector('input[type="checkbox"]');
          cellContent = checkbox && checkbox.checked ? '✓' : '';
        } 
        // Handle regular cells
        else {
          // Get text content, handling links
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
    
    // Create print window with minimal delay
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    const printHTML = '<!DOCTYPE html>' +
      '<html>' +
      '<head>' +
      '<title>Clients - Print</title>' +
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
  </script>

@endsection