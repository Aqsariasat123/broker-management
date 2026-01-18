@extends('layouts.app')

@section('content')
@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/calender-index.css') }}?v={{ time() }}">

<div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; margin-bottom:15px; padding:15px 20px;">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    <h3 style="margin:0; font-size:18px; font-weight:600;">Calendar</h3>
    @include('partials.page-header-right')
  </div>
</div>

<div class="calendar-container">
  <!-- Header -->
  <div class="calendar-header">
    <div class="calendar-title"></div>
    
    <!-- Category Filters -->
    <div class="category-filters">
      <button class="category-btn selected" data-filter="all">ALL</button>
      <div class="category-dropdown-container">
        <button class="category-btn" data-filter="tasks" id="btn-tasks">Tasks</button>
        <div class="category-dropdown" id="dropdown-tasks">List</div>
      </div>
      <div class="category-dropdown-container">
        <button class="category-btn" data-filter="follow-ups" id="btn-follow-ups">Follow Ups</button>
        <div class="category-dropdown" id="dropdown-follow-ups">List</div>
      </div>
      <div class="category-dropdown-container">
        <button class="category-btn" data-filter="renewals" id="btn-renewals">Renewals</button>
        <div class="category-dropdown" id="dropdown-renewals">List</div>
      </div>
      <div class="category-dropdown-container">
        <button class="category-btn" data-filter="instalments" id="btn-instalments">Instalments</button>
        <div class="category-dropdown" id="dropdown-instalments">List</div>
      </div>
      <div class="category-dropdown-container">
        <button class="category-btn" data-filter="birthdays" id="btn-birthdays">Birthdays</button>
        <div class="category-dropdown" id="dropdown-birthdays">List</div>
      </div>
    </div>
  </div>

  <!-- Date Navigation -->
  <div class="controls-row">
    <div class="date-navigation">
      <div class="year">
        <span class="arrow" id="year-prev">▲</span>
        <span id="current-year">2025</span>
        <span class="arrow" id="year-next">▲</span>
      </div>
      <div class="month">
        <span class="arrow" id="month-prev">▲</span>
        <span id="current-month">JUNE</span>
        <span class="arrow" id="month-next">▲</span>
      </div>
    </div>

    <div class="view-controls">
      <button class="nav-arrow" id="prev-btn">‹</button>
      <button class="nav-arrow" id="next-btn">›</button>
      <button class="view-btn" id="today-btn">TODAY</button>
      <button class="view-btn active" id="month-view">MONTH</button>
      <button class="view-btn" id="week-view">WEEK</button>
      <button class="view-btn" id="day-view">DAY</button>
      <button class="view-btn" id="schedule-view">SCHEDULE</button>
    </div>
  </div>

  <!-- Calendar Table -->
  <table class="calendar-table">
    <thead>
      <tr>
        <th>MON</th>
        <th>TUE</th>
        <th>WED</th>
        <th>THU</th>
        <th>FRI</th>
        <th>SAT</th>
        <th>SUN</th>
      </tr>
    </thead>
    <tbody id="calendar-body">
      <tr><td colspan="7" style="text-align:center; padding:50px; color:#999;">Loading calendar...</td></tr>
    </tbody>
  </table>
</div>

<!-- Task Modal (Same as tasks-index) -->
<div class="modal" id="taskModal">
    <div class="modal-content" style="max-width: 520px;">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #ddd;">
        <h4 id="modalTitle" style="margin: 0; font-size: 16px; font-weight: 600;">View/Edit Task</h4>
        <div style="display: flex; gap: 8px;">
          <button type="button" class="btn-delete" id="deleteBtn" style="display: none; background: #f3742a; color: #fff; border: none; padding: 5px 14px; border-radius: 3px; cursor: pointer; font-size: 13px;" onclick="deleteTask()">Delete</button>
          <button type="submit" form="taskForm" class="btn-save" style="background: #f3742a; color: #fff; border: none; padding: 5px 14px; border-radius: 3px; cursor: pointer; font-size: 13px;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeModal()" style="background: #ccc; color: #000; border: none; padding: 5px 14px; border-radius: 3px; cursor: pointer; font-size: 13px;">Cancel</button>
        </div>
      </div>
      <form id="taskForm" method="POST">
        @csrf
        <div id="formMethod" style="display: none;"></div>
        <input type="hidden" id="description" name="description" value="">
        <input type="hidden" id="date_in" name="date_in">

        <div class="modal-body" style="padding: 20px;">
          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="category" style="width: 120px; font-weight: 500; font-size: 13px;">Category</label>
            <select class="form-control" id="category" name="category" required style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
              <option value="">Select Category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="item" style="width: 120px; font-weight: 500; font-size: 13px;">Item</label>
            <input type="text" class="form-control" id="item" name="item" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="name" style="width: 120px; font-weight: 500; font-size: 13px;">Name</label>
            <select class="form-control" id="name" name="name" required style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
              <option value="">Select Name</option>
              @foreach($contacts as $contact)
                <option value="{{ $contact->name }}">{{ $contact->name }}</option>
              @endforeach
              @foreach($clients as $client)
                <option value="{{ $client->name }}">{{ $client->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="contact_no" style="width: 120px; font-weight: 500; font-size: 13px;">Contact No.</label>
            <input type="text" class="form-control" id="contact_no" name="contact_no" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="due_date" style="width: 120px; font-weight: 500; font-size: 13px;">Due Date</label>
            <input type="date" class="form-control" id="due_date" name="due_date" required style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="due_time" style="width: 120px; font-weight: 500; font-size: 13px;">Due Time</label>
            <input type="time" class="form-control" id="due_time" name="due_time" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="assignee" style="width: 120px; font-weight: 500; font-size: 13px;">Assignee</label>
            <select class="form-control" id="assignee" name="assignee" required style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
              <option value="">Select Assignee</option>
              @foreach($users as $user)
                <option value="{{ $user->name }}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="task_status" style="width: 120px; font-weight: 500; font-size: 13px;">Task Status</label>
            <select class="form-control" id="task_status" name="task_status" required style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
              <option value="Not Done">Not Done</option>
              <option value="In Progress">In Progress</option>
              <option value="Completed">Completed</option>
            </select>
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
            <label for="date_done" style="width: 120px; font-weight: 500; font-size: 13px;">Date Done</label>
            <input type="date" class="form-control" id="date_done" name="date_done" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
          </div>

          <div class="form-row-vertical" style="display: flex; align-items: flex-start; margin-bottom: 10px;">
            <label for="task_notes" style="width: 120px; font-weight: 500; font-size: 13px; padding-top: 6px;">Task Notes</label>
            <textarea class="form-control" id="task_notes" name="task_notes" rows="3" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; resize: vertical; font-size: 13px;"></textarea>
          </div>

          <div style="border: 1px solid #ddd; padding: 12px; margin-top: 15px;">
            <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
              <label style="width: 120px; font-weight: 500; font-size: 13px;">Repeat / Frequency</label>
              <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                <input type="checkbox" id="repeat" name="repeat" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                <select class="form-control" id="frequency" name="frequency" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
                  <option value="">Select Frequency</option>
                  @if($frequencyCategories && $frequencyCategories->values)
                    @foreach($frequencyCategories->values as $freq)
                      <option value="{{ $freq->name }}">{{ $freq->name }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>

            <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 10px;">
              <label for="rpt_date" style="width: 120px; font-weight: 500; font-size: 13px;">Repeat Date</label>
              <input type="date" class="form-control" id="rpt_date" name="rpt_date" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
            </div>

            <div class="form-row-vertical" style="display: flex; align-items: center; margin-bottom: 0;">
              <label for="rpt_stop_date" style="width: 120px; font-weight: 500; font-size: 13px;">Repeat Stop Date</label>
              <input type="date" class="form-control" id="rpt_stop_date" name="rpt_stop_date" style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 2px; font-size: 13px;">
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

<script>
  const calendarEventsRoute = '{{ route("calendar.events") }}';
  const tasksStoreRoute = '{{ route("tasks.store") }}';
  const csrfToken = '{{ csrf_token() }}';
  let currentTaskId = null;
</script>
<script src="{{ asset('js/calender-index.js') }}?v={{ time() }}"></script>
@endsection