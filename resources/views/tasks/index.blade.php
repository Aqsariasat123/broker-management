
@extends('layouts.app')

@section('content')

@include('partials.table-styles')

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
        <h3>Tasks</h3>
        <div class="records-found">Records Found - {{ $tasks->total() }}</div>
        <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
          <div class="filter-group">
            <button class="btn btn-overdue" id="overdueOnly" type="button">Overdue Only</button>
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
            
                  <td class="action-cell">
                    <svg class="action-expand" onclick="openTaskDetails({{ $task->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                      <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                      <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                      <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </td>
                      @foreach($selectedColumns as $col)
                      @if($col == 'task_id')
                        <td data-column="task_id">
                          <a href="javascript:void(0)" onclick="openTaskDetails({{ $task->id }})" style="color:#007bff; text-decoration:underline;">{{ $task->task_id }}</a>
                        </td>
                      @elseif($col == 'category')
                        <td data-column="category">{{ $task->category }}</td>
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

  <!-- Task Page View (Full Page) -->
  <div class="client-page-view" id="taskPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="taskPageTitle">Task</span> - <span class="client-name" id="taskPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editTaskFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeTaskPageBtn" onclick="closeTaskPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Task Details View -->
        <div id="taskDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="taskDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>
        
        <!-- Task Edit/Add Form -->
        <div id="taskFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="taskDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteTask()">Delete</button>
                <button type="submit" form="taskPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeTaskFormBtn" onclick="closeTaskPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="taskPageForm" method="POST" action="{{ route('tasks.store') }}">
              @csrf
              <div id="taskPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Task Modal (hidden, used for form structure) -->
  <div class="modal" id="taskModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modalTitle">Add Task</h4>
        <button type="button" class="modal-close" onclick="closeModal()">×</button>
      </div>
      <form id="taskForm" method="POST">
        @csrf
        <div id="formMethod" style="display: none;"></div>
        
        <div class="modal-body">
          <!-- ALWAYS render inputs for add/edit so JS can set values and server validation can run.
               Column selection only affects table display, not the add/edit form. -->
          <div class="form-row">
            <div class="form-group">
              <label for="category">Category</label>
              <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="form-group">
              <label for="task_id_hidden" style="visibility:hidden">placeholder</label>
              <input type="hidden" id="task_id_hidden" name="task_id_hidden">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="contact_no">Contact No</label>
              <input type="text" class="form-control" id="contact_no" name="contact_no">
            </div>
            <div class="form-group">
              <label for="assignee_small" style="visibility:hidden">placeholder</label>
              <input type="hidden" id="assignee_small" name="assignee_small">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="due_date">Due Date</label>
              <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>
            <div class="form-group">
              <label for="due_time">Due Time</label>
              <input type="time" class="form-control" id="due_time" name="due_time">
            </div>
            <div class="form-group">
              <label for="date_in">Date In</label>
              <input type="date" class="form-control" id="date_in" name="date_in">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="assignee">Assignee</label>
              <input type="text" class="form-control" id="assignee" name="assignee" required>
            </div>
            <div class="form-group">
              <label for="task_status">Task Status</label>
              <select class="form-control" id="task_status" name="task_status" required>
                <option value="Not Done">Not Done</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
              </select>
            </div>
            <div class="form-group">
              <label for="date_done">Date Done</label>
              <input type="date" class="form-control" id="date_done" name="date_done">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="task_notes">Task Notes</label>
              <input type="text" class="form-control" id="task_notes" name="task_notes">
            </div>
            <div class="form-group" style="align-items: center; display:flex; gap:8px;">
              <label style="display: flex; align-items: center; gap: 8px; margin:0;">
                <input type="checkbox" id="repeat" name="repeat" value="1">
                Repeat
              </label>
            </div>
            <div class="form-group">
              <label for="frequency">Frequency</label>
              <input type="text" class="form-control" id="frequency" name="frequency">
            </div>
          </div>

          <div style="border: 1px solid #ddd; padding: 12px; margin-bottom: 12px;">
            <h5 style="margin: 0 0 10px 0; font-size: 14px;">Repeat / Frequency</h5>
            
            <div class="form-row">
              <div class="form-group">
                <label for="rpt_date">Repeat Date</label>
                <input type="date" class="form-control" id="rpt_date" name="rpt_date">
              </div>
              <div class="form-group">
                <label for="rpt_stop_date">Repeat Stop Date</label>
                <input type="date" class="form-control" id="rpt_stop_date" name="rpt_stop_date">
              </div>
              <div class="form-group">
                <!-- empty placeholder to keep three-column layout -->
                <label style="visibility:hidden">placeholder</label>
                <input type="hidden">
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
          <button type="button" class="btn-delete" id="deleteBtn" style="display: none;" onclick="deleteTask()">Delete</button>
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
            <form id="columnForm" action="{{ route('tasks.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
            @php
              $all = [
                'task_id'=>'Task ID',
                'category'=>'Category',
                'description'=>'Description',
                'name'=>'Name',
                'contact_no'=>'Contact No',
                'due_date'=>'Due Date',
                'due_time'=>'Due Time',
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

    // Open task details (full page view) - MUST be defined before event listeners
    async function openTaskDetails(id) {
      try {
        const res = await fetch(`/tasks/${id}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const task = await res.json();
        currentTaskId = id;
        
        // Get all required elements
        const taskPageName = document.getElementById('taskPageName');
        const taskPageTitle = document.getElementById('taskPageTitle');
        const clientsTableView = document.getElementById('clientsTableView');
        const taskPageView = document.getElementById('taskPageView');
        const taskDetailsPageContent = document.getElementById('taskDetailsPageContent');
        const taskFormPageContent = document.getElementById('taskFormPageContent');
        const editTaskFromPageBtn = document.getElementById('editTaskFromPageBtn');
        const closeTaskPageBtn = document.getElementById('closeTaskPageBtn');
        
        if (!taskPageName || !taskPageTitle || !clientsTableView || !taskPageView || 
            !taskDetailsPageContent || !taskFormPageContent) {
          console.error('Required elements not found');
          alert('Error: Page elements not found');
          return;
        }
        
        // Set task name in header
        const taskName = task.task_id || task.name || 'Unknown';
        taskPageName.textContent = taskName;
        taskPageTitle.textContent = 'Task';
        
        populateTaskDetails(task);
        
        // Hide table view, show page view
        clientsTableView.classList.add('hidden');
        taskPageView.style.display = 'block';
        taskPageView.classList.add('show');
        taskDetailsPageContent.style.display = 'block';
        taskFormPageContent.style.display = 'none';
        if (editTaskFromPageBtn) editTaskFromPageBtn.style.display = 'inline-block';
        if (closeTaskPageBtn) closeTaskPageBtn.style.display = 'inline-block';
      } catch (e) {
        console.error(e);
        alert('Error loading task details: ' + e.message);
      }
    }

    // Populate task details view
    function populateTaskDetails(task) {
      const content = document.getElementById('taskDetailsContent');
      if (!content) return;

      const col1 = `
        <div class="detail-section">
          <div class="detail-section-header">TASK DETAILS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Task ID</span>
              <div class="detail-value">${task.task_id || '-'}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Category</span>
              <div class="detail-value">${task.category || '-'}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Description</span>
              <div class="detail-value">${task.description || '-'}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Name</span>
              <div class="detail-value">${task.name || '-'}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Contact No</span>
              <div class="detail-value">${task.contact_no || '-'}</div>
            </div>
          </div>
        </div>
      `;

      const col2 = `
        <div class="detail-section">
          <div class="detail-section-header">DATES & TIME</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Due Date</span>
              <div class="detail-value">${formatDate(task.due_date)}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Due Time</span>
              <div class="detail-value">${task.due_time || '-'}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Date In</span>
              <div class="detail-value">${formatDate(task.date_in)}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Date Done</span>
              <div class="detail-value">${formatDate(task.date_done)}</div>
            </div>
          </div>
        </div>
      `;

      const col3 = `
        <div class="detail-section">
          <div class="detail-section-header">ASSIGNMENT & STATUS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Assignee</span>
              <div class="detail-value">${task.assignee || '-'}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Task Status</span>
              <div class="detail-value">${task.task_status || '-'}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Repeat</span>
              <div class="detail-value">
                <input type="checkbox" ${task.repeat ? 'checked' : ''} disabled>
              </div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Frequency</span>
              <div class="detail-value">${task.frequency || '-'}</div>
            </div>
          </div>
        </div>
      `;

      const col4 = `
        <div class="detail-section">
          <div class="detail-section-header">REPEAT SETTINGS</div>
          <div class="detail-section-body">
            <div class="detail-row">
              <span class="detail-label">Repeat Date</span>
              <div class="detail-value">${formatDate(task.rpt_date)}</div>
            </div>
            <div class="detail-row">
              <span class="detail-label">Repeat Stop Date</span>
              <div class="detail-value">${formatDate(task.rpt_stop_date)}</div>
            </div>
            <div class="detail-row" style="align-items:flex-start;">
              <span class="detail-label">Task Notes</span>
              <textarea class="detail-value" style="min-height:40px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${task.task_notes || ''}</textarea>
            </div>
          </div>
        </div>
      `;

      content.innerHTML = col1 + col2 + col3 + col4;
    }

    // Initialize column checkboxes
    function initializeColumnCheckboxes() {
      const checkboxes = document.querySelectorAll('.column-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectedColumns.includes(checkbox.value);
      });
    }

    // Add Task Button
    document.getElementById('addTaskBtn').addEventListener('click', function() {
      openTaskPage('add');
    });

    // Column Button
    document.getElementById('columnBtn').addEventListener('click', function() {
      openColumnModal();
    });

    // Open task page (Add or Edit)
    async function openTaskPage(mode) {
      if (mode === 'add') {
        openTaskForm('add');
      } else {
        if (currentTaskId) {
          openEditTask(currentTaskId);
        }
      }
    }

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
        openTaskForm('edit', task);
      } catch (e) {
        console.error(e);
        alert('Error loading task data');
      }
    }

    function openTaskForm(mode, task = null) {
      // Clone form from modal
      const modalForm = document.getElementById('taskModal').querySelector('form');
      const pageForm = document.getElementById('taskPageForm');
      const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
      
      // Clone the modal form body
      const modalBody = modalForm.querySelector('.modal-body');
      if (modalBody && formContentDiv) {
        formContentDiv.innerHTML = modalBody.innerHTML;
      }

      const formMethod = document.getElementById('taskPageFormMethod');
      const deleteBtn = document.getElementById('taskDeleteBtn');
      const editBtn = document.getElementById('editTaskFromPageBtn');
      const closeBtn = document.getElementById('closeTaskPageBtn');
      const closeFormBtn = document.getElementById('closeTaskFormBtn');

      if (mode === 'add') {
        document.getElementById('taskPageTitle').textContent = 'Add Task';
        document.getElementById('taskPageName').textContent = '';
        pageForm.action = '{{ route("tasks.store") }}';
        formMethod.innerHTML = '';
        deleteBtn.style.display = 'none';
        if (editBtn) editBtn.style.display = 'none';
        if (closeBtn) closeBtn.style.display = 'inline-block';
        if (closeFormBtn) closeFormBtn.style.display = 'none';
        pageForm.reset();
      } else {
        const taskName = task.task_id || task.name || 'Unknown';
        document.getElementById('taskPageTitle').textContent = 'Edit Task';
        document.getElementById('taskPageName').textContent = taskName;
        pageForm.action = `/tasks/${currentTaskId}`;
        formMethod.innerHTML = `@method('PUT')`;
        deleteBtn.style.display = 'inline-block';
        if (editBtn) editBtn.style.display = 'none';
        if (closeBtn) closeBtn.style.display = 'none';
        if (closeFormBtn) closeFormBtn.style.display = 'inline-block';

        const fields = ['category','description','name','contact_no','due_date','due_time','date_in','assignee','task_status','date_done','task_notes','frequency','rpt_date','rpt_stop_date'];
        fields.forEach(id => {
          const el = formContentDiv ? formContentDiv.querySelector(`#${id}`) : null;
          if (!el) return;
          if (el.type === 'checkbox') {
            el.checked = !!task[id];
          } else if (el.type === 'date') {
            el.value = task[id] ? (typeof task[id] === 'string' ? task[id].substring(0,10) : task[id]) : '';
          } else {
            el.value = task[id] ?? '';
          }
        });
        
        // Handle repeat checkbox
        const repeatCheckbox = formContentDiv ? formContentDiv.querySelector('#repeat') : null;
        if (repeatCheckbox) {
          repeatCheckbox.checked = !!task.repeat;
        }
      }

      // Hide table view, show page view
      document.getElementById('clientsTableView').classList.add('hidden');
      const taskPageView = document.getElementById('taskPageView');
      taskPageView.style.display = 'block';
      taskPageView.classList.add('show');
      document.getElementById('taskDetailsPageContent').style.display = 'none';
      document.getElementById('taskFormPageContent').style.display = 'block';
    }

    function closeTaskPageView() {
      const taskPageView = document.getElementById('taskPageView');
      taskPageView.classList.remove('show');
      taskPageView.style.display = 'none';
      document.getElementById('clientsTableView').classList.remove('hidden');
      document.getElementById('taskDetailsPageContent').style.display = 'none';
      document.getElementById('taskFormPageContent').style.display = 'none';
      currentTaskId = null;
    }

    // Edit button from details page
    const editBtn = document.getElementById('editTaskFromPageBtn');
    if (editBtn) {
      editBtn.addEventListener('click', function() {
        if (currentTaskId) {
          openEditTask(currentTaskId);
        }
      });
    }

    // Legacy editTask function for backward compatibility
    async function editTask(taskId) {
      openTaskDetails(taskId);
    }

    // Open Task Modal
    function openModal(mode) {
      const modal = document.getElementById('taskModal');
      const title = document.getElementById('modalTitle');
      const form = document.getElementById('taskForm');
      const deleteBtn = document.getElementById('deleteBtn');
      const formMethod = document.getElementById('formMethod');
      
      if (mode === 'add') {
        title.textContent = 'Add Task';
        form.action = "{{ route('tasks.store') }}";
        form.method = 'POST';
        formMethod.innerHTML = '';
        deleteBtn.style.display = 'none';
        form.reset();
        currentTaskId = null;
      } else {
        title.textContent = 'Edit Task';
        form.action = `/tasks/${currentTaskId}`;
        form.method = 'POST';
        formMethod.innerHTML = '@method("PUT")';
        deleteBtn.style.display = 'block';
      }
      
      // prevent body scrollbar when modal open
      document.body.style.overflow = 'hidden';
      modal.classList.add('show');
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

      // overdue button handler
      const overdueBtn = document.getElementById('overdueOnly');
      if (overdueBtn) {
        // optional visual state
        if (overdueActive) overdueBtn.classList.add('active');

        overdueBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const u = new URL(window.location.href);
          const val = u.searchParams.get('overdue');
          if (val === 'true' || val === '1') {
            u.searchParams.delete('overdue');
          } else {
            u.searchParams.set('overdue', '1');
          }
          // navigate keeping other params intact
          window.location.href = u.toString();
        });
      }

      // keep compatibility for a (commented) filterToggle input if later enabled
      const filterToggle = document.getElementById('filterToggle');
      if (filterToggle) {
        filterToggle.checked = overdueActive;
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
  </script>

@endsection