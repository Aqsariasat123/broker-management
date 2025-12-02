@extends('layouts.app')

@section('content')
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
  }

  .calendar-container {
    margin-top: 50px;
    background: #fff;
    padding: 20px;
    
    border-radius: 4px;
  }

  /* Header Section */
  .calendar-header {
    margin-bottom: 20px;
  }

  .calendar-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #2d2d2d;
  }

  /* Category Filters */
  .category-filters {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }

  .category-btn {
    background: #000;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .category-btn:hover {
    background: #333;
  }

  .category-btn.selected {
    background: #000;
  }

  .category-btn.selected.tasks,
  .category-btn.selected.follow-ups,
  .category-btn.selected.renewals,
  .category-btn.selected.instalments,
  .category-btn.selected.birthdays {
    background: #32cd32;
    color: #fff;
  }

  /* Dropdown container for Tasks and Follow Ups */
  .category-dropdown-container {
    position: relative;
    display: inline-block;
  }

  .category-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 4px;
    background: #32cd32;
    color: #fff;
    padding: 4px 8px;
    border-radius: 0 0 4px 4px;
    font-size: 13px;
    font-weight: normal;
    white-space: nowrap;
    z-index: 10;
    min-width: 60px;
    text-align: center;
  }

  .category-dropdown.show {
    display: block;
  }

  /* Date Navigation and View Controls */
  .controls-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
  }

  .date-navigation {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .date-navigation .year,
  .date-navigation .month {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 600;
    color: #2d2d2d;
  }

  .date-navigation .arrow {
    cursor: pointer;
    font-size: 18px;
    color: #666;
    user-select: none;
  }

  .date-navigation .arrow:hover {
    color: #000;
  }

  /* View Controls */
  .view-controls {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .nav-arrow {
    background: #00b8f4;
    color: #fff;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .nav-arrow:hover {
    background: #0099cc;
  }

  .view-btn {
    background: #00b8f4;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: 0.5px;
  }

  .view-btn:hover {
    background: #0099cc;
  }

  .view-btn.active {
    background: #00b8f4;
  }

  /* Calendar Table */
  .calendar-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
  }

  .calendar-table thead th {
    padding: 12px 8px;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    color: #2d2d2d;
    border: 1px solid #e0e0e0;
    background: #fff;
  }

  .calendar-table tbody td {
    border: 1px solid #e0e0e0;
    padding: 8px;
    vertical-align: top;
    height: 100px;
    width: 14.28%;
    position: relative;
  }

  .calendar-table tbody td.outside-month {
    color: #bbb;
    background: #fafafa;
  }

  .day-number {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
    color: #2d2d2d;
  }

  .day-number.outside {
    color: #bbb;
  }

  /* Event Styles */
  .event {
    font-size: 12px;
    padding: 4px 8px;
    margin-bottom: 4px;
    border-radius: 4px;
    cursor: pointer;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
  }

  .event.renewal {
    background: #ffb74d;
    color: #000;
  }

  .event.birthday,
  .event.follow-up {
    background: #f8bbd0;
    color: #000;
  }

  .event.task,
  .event.instalment {
    background: #b3e5fc;
    color: #000;
  }
</style>

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
      <!-- Calendar cells will be generated by JavaScript -->
    </tbody>
  </table>
</div>

<script>
  // Calendar state
  const today = new Date();
  let currentYear = today.getFullYear();
  let currentMonth = today.getMonth(); // 0-indexed
  let currentFilter = 'all';
  let eventsData = {};
  
  const monthNames = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];

  // Fetch events from API
  async function fetchEvents() {
    try {
      const response = await fetch(`{{ route('calendar.events') }}?year=${currentYear}&month=${currentMonth + 1}&filter=${currentFilter}`);
      const data = await response.json();
      eventsData = data;
      generateCalendar();
    } catch (error) {
      console.error('Error fetching events:', error);
      eventsData = {};
      generateCalendar();
    }
  }

  // Update display
  function updateDisplay() {
    document.getElementById('current-year').textContent = currentYear;
    document.getElementById('current-month').textContent = monthNames[currentMonth];
    fetchEvents();
  }

  // Generate calendar
  function generateCalendar() {
    const calendarBody = document.getElementById('calendar-body');
    calendarBody.innerHTML = '';

    // Get first day of month (Monday = 0)
    const firstDay = new Date(currentYear, currentMonth, 1);
    let startDay = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.
    // Convert to Monday = 0
    startDay = startDay === 0 ? 6 : startDay - 1;

    // Days in current month
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    // Days in previous month
    const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
    const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
    const daysInPrevMonth = new Date(prevYear, prevMonth + 1, 0).getDate();

    let dayCounter = 1;
    let nextMonthDay = 1;
    let prevMonthDay = daysInPrevMonth - startDay + 1;

    // Generate 6 weeks
    for (let week = 0; week < 6; week++) {
      const row = document.createElement('tr');

      for (let day = 0; day < 7; day++) {
        const cell = document.createElement('td');
        const dayNumber = document.createElement('div');
        dayNumber.className = 'day-number';

        if (week === 0 && day < startDay) {
          // Previous month days
          cell.classList.add('outside-month');
          dayNumber.classList.add('outside');
          dayNumber.textContent = prevMonthDay++;
        } else if (dayCounter > daysInMonth) {
          // Next month days
          cell.classList.add('outside-month');
          dayNumber.classList.add('outside');
          dayNumber.textContent = nextMonthDay++;
        } else {
          // Current month days
          dayNumber.textContent = dayCounter;
          
          // Add events
          const dateKey = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(dayCounter).padStart(2, '0')}`;
          const events = eventsData[dateKey] || [];
          
          events.forEach(event => {
            const eventDiv = document.createElement('div');
            eventDiv.className = `event ${event.class || event.type}`;
            eventDiv.textContent = event.text;
            eventDiv.title = event.text; // Tooltip
            cell.appendChild(eventDiv);
          });

          dayCounter++;
        }

        cell.appendChild(dayNumber);
        row.appendChild(cell);
      }

      calendarBody.appendChild(row);
    }
  }

  // Event listeners
  document.getElementById('year-prev').addEventListener('click', () => {
    currentYear--;
    updateDisplay();
  });

  document.getElementById('year-next').addEventListener('click', () => {
    currentYear++;
    updateDisplay();
  });

  document.getElementById('month-prev').addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    updateDisplay();
  });

  document.getElementById('month-next').addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    updateDisplay();
  });

  document.getElementById('prev-btn').addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    updateDisplay();
  });

  document.getElementById('next-btn').addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    updateDisplay();
  });

  document.getElementById('today-btn').addEventListener('click', () => {
    const today = new Date();
    currentYear = today.getFullYear();
    currentMonth = today.getMonth();
    updateDisplay();
  });

  // View buttons
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Category filter buttons
  document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      // Remove selected class and special classes from all buttons
      document.querySelectorAll('.category-btn').forEach(b => {
        b.classList.remove('selected', 'tasks', 'follow-ups', 'renewals', 'instalments', 'birthdays');
      });
      
      // Hide all dropdowns
      document.querySelectorAll('.category-dropdown').forEach(d => {
        d.classList.remove('show');
      });
      
      // Add selected class to clicked button
      this.classList.add('selected');
      const filter = this.getAttribute('data-filter');
      currentFilter = filter;
      
      // Show dropdown and make button green for all filter types except 'all'
      if (filter === 'tasks') {
        this.classList.add('tasks');
        document.getElementById('dropdown-tasks').classList.add('show');
      } else if (filter === 'follow-ups') {
        this.classList.add('follow-ups');
        document.getElementById('dropdown-follow-ups').classList.add('show');
      } else if (filter === 'renewals') {
        this.classList.add('renewals');
        document.getElementById('dropdown-renewals').classList.add('show');
      } else if (filter === 'instalments') {
        this.classList.add('instalments');
        document.getElementById('dropdown-instalments').classList.add('show');
      } else if (filter === 'birthdays') {
        this.classList.add('birthdays');
        document.getElementById('dropdown-birthdays').classList.add('show');
      }
      
      fetchEvents();
    });
  });

  // Initialize
  updateDisplay();
</script>
@endsection
