@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/calender-index.css') }}?v={{ time() }}">



<div class="calendar-container">
  <!-- Header -->
  <div class="calendar-header">
    <div class="calendar-title">Calendar</div>
    
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

  <!-- Date Navigation and View Controls -->
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
      <!-- Loading placeholder -->
      <tr><td colspan="7" style="text-align:center; padding:50px; color:#999;">Loading calendar...</td></tr>
    </tbody>
  </table>
</div>


<script>
  // Initialize data from Blade
  const calendarEventsRoute = '{{ route("calendar.events") }}';
</script>
<script src="{{ asset('js/calender-index.js') }}?v={{ time() }}"></script>
@endsection
