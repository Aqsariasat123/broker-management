// ---------------------------------
// 1. INITIAL STATE & URL PARAMS
// ---------------------------------
const urlParams = new URLSearchParams(window.location.search);
const today = new Date();

// Filter & Date Range
let currentFilter = urlParams.get('filter') || 'all';
const dateRange = urlParams.get('date_range') || 'month';

// Calendar state
let currentYear = today.getFullYear();
let currentMonth = today.getMonth(); // 0-indexed

// Lock navigation only for 'today' and 'week'
const isFixedRange = ['today', 'week'].includes(dateRange);

// ---------------------------------
// 2. DATE RANGE HANDLING
// ---------------------------------
switch (dateRange) {
    case 'today':
    case 'week':
    case 'month':
        currentYear = today.getFullYear();
        currentMonth = today.getMonth();
        break;
    case 'quarter':
        const quarter = Math.floor(today.getMonth() / 3);
        currentYear = today.getFullYear();
        currentMonth = quarter * 3;
        break;
    case 'year':
        currentYear = today.getFullYear();
        currentMonth = 0;
        break;
    default:
        if (dateRange.startsWith('year-')) {
            currentYear = parseInt(dateRange.replace('year-', ''), 10);
            currentMonth = 0;
        }
        break;
}

// ---------------------------------
// 3. CONSTANTS
// ---------------------------------
let eventsData = {};
const monthNames = [
    'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE',
    'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
];

// ---------------------------------
// 4. FETCH EVENTS
// ---------------------------------
async function fetchEvents() {
    try {
        const response = await fetch(
            `${calendarEventsRoute}?year=${currentYear}&month=${currentMonth + 1}&filter=${currentFilter}&date_range=${dateRange}`
        );
        eventsData = await response.json();
    } catch (error) {
        console.error('Error fetching events:', error);
        eventsData = {};
    }
    generateCalendar();
}

// ---------------------------------
// 5. UPDATE HEADER + LOAD EVENTS
// ---------------------------------
function updateDisplay() {
    document.getElementById('current-year').textContent = currentYear;
    document.getElementById('current-month').textContent = monthNames[currentMonth];
    fetchEvents();
}

// ---------------------------------
// 6. GENERATE CALENDAR GRID
// ---------------------------------
function generateCalendar() {
    const calendarBody = document.getElementById('calendar-body');
    calendarBody.innerHTML = '';

    const firstDay = new Date(currentYear, currentMonth, 1);
    let startDay = firstDay.getDay(); // Sunday=0
    startDay = startDay === 0 ? 6 : startDay - 1; // Monday=0

    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
    const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
    const daysInPrevMonth = new Date(prevYear, prevMonth + 1, 0).getDate();

    let dayCounter = 1;
    let nextMonthDay = 1;
    let prevMonthDay = daysInPrevMonth - startDay + 1;

    for (let week = 0; week < 6; week++) {
        const row = document.createElement('tr');

        for (let day = 0; day < 7; day++) {
            const cell = document.createElement('td');
            const dayNumber = document.createElement('div');
            dayNumber.className = 'day-number';

            let dateKey;

            if (week === 0 && day < startDay) {
                // Previous month
                cell.classList.add('outside-month');
                dayNumber.classList.add('outside');
                dayNumber.textContent = prevMonthDay++;
                dateKey = `${prevYear}-${String(prevMonth + 1).padStart(2, '0')}-${String(dayNumber.textContent).padStart(2, '0')}`;
            } else if (dayCounter > daysInMonth) {
                // Next month
                cell.classList.add('outside-month');
                dayNumber.classList.add('outside');
                dayNumber.textContent = nextMonthDay++;
                const nextMonth = (currentMonth + 1) % 12;
                const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
                dateKey = `${nextYear}-${String(nextMonth + 1).padStart(2, '0')}-${String(dayNumber.textContent).padStart(2, '0')}`;
            } else {
                // Current month
                dayNumber.textContent = dayCounter;
                dateKey = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(dayCounter).padStart(2, '0')}`;

                dayCounter++;
            }

            // Append day number first
            cell.appendChild(dayNumber);

            // Add events for this day
            const events = eventsData[dateKey] || [];
            events.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = `event ${event.class || event.type}`;
                eventDiv.textContent = event.text;
                eventDiv.title = event.text;
                cell.appendChild(eventDiv);
            });

            row.appendChild(cell);
        }

        calendarBody.appendChild(row);
    }
}

// ---------------------------------
// 7. NAVIGATION CONTROLS
// ---------------------------------
document.getElementById('year-prev').onclick = () => {
    if (isFixedRange) return;
    currentYear--;
    updateDisplay();
};
document.getElementById('year-next').onclick = () => {
    if (isFixedRange) return;
    currentYear++;
    updateDisplay();
};
document.getElementById('month-prev').onclick = () => {
    if (isFixedRange) return;
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateDisplay();
};
document.getElementById('month-next').onclick = () => {
    if (isFixedRange) return;
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    updateDisplay();
};
document.getElementById('today-btn').onclick = () => {
    currentYear = today.getFullYear();
    currentMonth = today.getMonth();
    updateDisplay();
};
document.getElementById('prev-btn').onclick = () => {
    if (isFixedRange) return;
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateDisplay();
};
document.getElementById('next-btn').onclick = () => {
    if (isFixedRange) return;
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    updateDisplay();
};

// ---------------------------------
// 8. FILTER BUTTONS
// ---------------------------------
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.category-btn').forEach(b =>
            b.classList.remove('selected', 'tasks', 'follow-ups', 'renewals', 'instalments', 'birthdays')
        );
        document.querySelectorAll('.category-dropdown').forEach(d =>
            d.classList.remove('show')
        );

        currentFilter = this.dataset.filter;
        this.classList.add('selected');

        if (currentFilter !== 'all') {
            this.classList.add(currentFilter);
            const dropdown = document.getElementById(`dropdown-${currentFilter}`);
            if (dropdown) dropdown.classList.add('show');
        }

        fetchEvents();
    });
});

// ---------------------------------
// 9. PRESELECT FILTER FROM URL
// ---------------------------------
document.querySelectorAll('.category-btn').forEach(btn => {
    if (btn.dataset.filter === currentFilter) {
        btn.classList.add('selected');
        if (currentFilter !== 'all') {
            btn.classList.add(currentFilter);
            const dropdown = document.getElementById(`dropdown-${currentFilter}`);
            if (dropdown) dropdown.classList.add('show');
        }
    }
});

// ---------------------------------
// 10. LIST DROPDOWN CLICK HANDLERS
// ---------------------------------
document.querySelectorAll('.category-dropdown').forEach(dropdown => {
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        const filter = this.id.replace('dropdown-', '');

        // Calculate date range for current month view
        const startDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-01`;
        const lastDay = new Date(currentYear, currentMonth + 1, 0).getDate();
        const endDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;

        // Navigate to respective list page based on filter
        let url = '';
        switch(filter) {
            case 'tasks':
                url = `/tasks?from_calendar=1&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'follow-ups':
                url = `/contacts?from_calendar=1&follow_up=1&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'renewals':
                url = `/policies?from_calendar=1&filter=expiring&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'instalments':
                url = `/debit-notes?from_calendar=1&filter=overdue&start_date=${startDate}&end_date=${endDate}`;
                break;
            case 'birthdays':
                url = `/clients?from_calendar=1&filter=birthday_today&start_date=${startDate}&end_date=${endDate}`;
                break;
        }

        if (url) {
            window.location.href = url;
        }
    });
});

// ---------------------------------
// 11. INIT
// ---------------------------------
updateDisplay();
