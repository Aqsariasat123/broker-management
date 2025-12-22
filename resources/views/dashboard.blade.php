@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">



<div class="dashboard">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Admin Dashboard</h2>
    <div style="display: flex; gap: 10px;">
      <form method="GET" action="{{ route('dashboard') }}" style="display: flex; gap: 10px; align-items: center;">
        <select name="date_range" class="form-control" style="width: auto; padding: 5px 10px;" onchange="this.form.submit()">
          <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
          <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
          <option value="month" {{ request('date_range') == 'month' || !request('date_range') ? 'selected' : '' }}>This Month</option>
          <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>This Quarter</option>
          <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
        </select>
        <button type="button" class="btn" onclick="exportDashboard()" style="padding: 5px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Export Report</button>
      </form>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="cards">

    <!-- Tasks Today -->
    <a href="{{ route('tasks.index') }}?filter=today" class="card-link">
      <div class="card icon-green">
        <span class="icon">⏰</span>
        <span class="value">{{ $stats['tasks_today'] ?? 0 }}</span>
        <span>Tasks Today</span>
      </div>
    </a>

    <!-- Policies Expiring -->
    <a href="{{ route('policies.index') }}?filter=expiring" class="card-link">
      <div class="card icon-red">
        <span class="icon">⚠️</span>
        <span class="value">{{ $stats['policies_expiring'] ?? 0 }}</span>
        <span>Policies Expiring</span>
      </div>
    </a>

    <!-- Instalments Overdue -->
    <a href="{{ route('payment-plans.index') }}?filter=overdue" class="card-link">
      <div class="card icon-pink">
        <span class="icon">💰</span>
        <span class="value">{{ $stats['instalments_overdue'] ?? 0 }}</span>
        <span>Instalments Overdue</span>
      </div>
    </a>

    <!-- IDs Expired -->
    <a href="{{ route('clients.index') }}?filter=ids_expired" class="card-link">
      <div class="card icon-black">
        <span class="icon">🆔</span>
        <span class="value">{{ $stats['ids_expired'] ?? 0 }}</span>
        <span>IDs Expired</span>
      </div>
    </a>

    <!-- General Policies -->
    <a href="{{ route('policies.index') }}" class="card-link">
      <div class="card icon-black">
        <span class="icon">📄</span>
        <span class="value">{{ $stats['general_policies'] ?? 0 }}</span>
        <span>General Policies</span>
      </div>
    </a>

    <!-- Gen-Com Outstanding -->
    <a href="{{ route('payment-plans.index') }}?filter=outstanding" class="card-link">
      <div class="card icon-black">
        <span class="icon">💵</span>
        <span class="value">{{ number_format($stats['gen_com_outstanding'] ?? 0,2) }}</span>
        <span>Gen-Com Outstanding</span>
      </div>
    </a>

    <!-- Open Leads -->
    <a href="{{ route('contacts.index') }}?status=open" class="card-link">
      <div class="card icon-black">
        <span class="icon">👥</span>
        <span class="value">{{ $stats['open_leads'] ?? 0 }}</span>
        <span>Open Leads</span>
      </div>
    </a>

    <!-- Follow Ups Today -->
    <a href="{{ route('tasks.index') }}?filter=today" class="card-link">
      <div class="card icon-red">
        <span class="icon">📅</span>
        <span class="value">{{ $stats['follow_ups_today'] ?? 0 }}</span>
        <span>Follow Ups Today</span>
      </div>
    </a>

    <!-- Proposals Pending -->
    <a href="{{ route('life-proposals.index') }}?status=pending" class="card-link">
      <div class="card icon-black">
        <span class="icon">📋</span>
        <span class="value">{{ $stats['proposals_pending'] ?? 0 }}</span>
        <span>Proposals Pending</span>
      </div>
    </a>

    <!-- Proposals Processing -->
    <a href="{{ route('life-proposals.index') }}?status=processing" class="card-link">
      <div class="card icon-black">
        <span class="icon">⚙️</span>
        <span class="value">{{ $stats['proposals_processing'] ?? 0 }}</span>
        <span>Proposals Processing</span>
      </div>
    </a>

    <!-- Life Policies -->
    <a href="{{ route('policies.index') }}?type=life" class="card-link">
      <div class="card icon-black">
        <span class="icon">❤️</span>
        <span class="value">{{ $stats['life_policies'] ?? 0 }}</span>
        <span>Life Policies</span>
      </div>
    </a>

    <!-- Birthdays Today -->
    <a href="{{ route('clients.index') }}?filter=birthday_today" class="card-link">
      <div class="card icon-red">
        <span class="icon">🎂</span>
        <span class="value">{{ $stats['birthdays_today'] ?? 0 }}</span>
        <span>Birthdays Today</span>
      </div>
    </a>

  </div>

  <!-- Income vs Expense Charts -->
  <div class="charts">
    <div class="chart-box">
      <div class="chart-controls">
        <h3 style="margin: 0;">Income v/s Expense</h3>
        <div class="year-selector">
          <select name="incomeExpenseYear" id="incomeExpenseYear" onchange="updateChartYear('incomeExpense', this.value)">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
              <option value="{{ $y }}" {{ ($incomeExpenseYear ?? $selectedYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </div>
      </div>
      <div class="date-range">
        <div class="date-range-item">
          <div>From : <input type="text" value="{{ $yearStart->format('j-M-y') }}" readonly class="date-input"></div>
          <input type="text" value="{{ number_format($totalIncome ?? 0, 2) }}" readonly class="amount-input">
        </div>
        <div class="date-range-item">
          <div>To : <input type="text" value="{{ $yearEnd->format('j-M-y') }}" readonly class="date-input"></div>
          <input type="text" value="{{ number_format($totalExpense ?? 0, 2) }}" readonly class="amount-input">
        </div>
      </div>
      <canvas id="incomeExpenseChart"></canvas>
    </div>

    <div class="chart-box">
      <div class="chart-controls">
        <h3 style="margin: 0;">Income</h3>
        <div class="year-selector">
          <select name="incomeYear" id="incomeYear" onchange="updateChartYear('income', this.value)">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
              <option value="{{ $y }}" {{ ($incomeYear ?? $selectedYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </div>
      </div>
      <canvas id="incomeChart"></canvas>
      <div class="month-stats" id="incomeStats"></div>
    </div>

    <div class="chart-box">
      <div class="chart-controls">
        <h3 style="margin: 0;">Expenses</h3>
        <div class="year-selector">
          <select name="expenseYear" id="expenseYear" onchange="updateChartYear('expense', this.value)">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
              <option value="{{ $y }}" {{ ($expenseYear ?? $selectedYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </div>
      </div>
      <canvas id="expenseChart"></canvas>
      <div class="month-stats" id="expenseStats"></div>
    </div>
  </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Initialize data from Blade
  const incomeExpenseMonthlyData = @json($incomeExpenseMonthlyData ?? $monthlyData ?? []);
  const incomeExpenseTotalIncome = {{ $incomeExpenseTotalIncome ?? $totalIncome ?? 0 }};
  const incomeExpenseTotalExpense = {{ $incomeExpenseTotalExpense ?? $totalExpense ?? 0 }};
  
  const incomeMonthlyData = @json($incomeMonthlyData ?? $monthlyData ?? []);
  const expenseMonthlyData = @json($expenseMonthlyData ?? $monthlyData ?? []);
  
  const monthlyData = @json($monthlyData ?? []);
  const totalIncome = {{ $totalIncome ?? 0 }};
  const totalExpense = {{ $totalExpense ?? 0 }};
  
  const dashboardRoute = '{{ route('dashboard') }}';
  const dashboardExportRoute = '{{ route('dashboard.export') }}';
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
