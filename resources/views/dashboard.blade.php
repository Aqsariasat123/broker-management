@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">



<div class="dashboard">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Dashboard</h2>
  </div>

  <!-- Statistics Cards -->
  <div class="cards">

    <!-- Tasks Today -->
    <a href="{{ route('tasks.index') }}" class="card-link">
      <div class="card">
        <span class="icon icon-green"><i class="fa-regular fa-clock"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['tasks_today'] ?? 0 }}</span>
          <span>Tasks Today</span>
        </div>
      </div>
    </a>

    <!-- Policies Expiring -->
    <a href="{{ route('policies.index', ['filter' => 'expiring']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-red"><i class="fa-solid fa-exclamation"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['policies_expiring'] ?? 0 }}</span>
          <span>Policies Expiring</span>
        </div>
      </div>
    </a>

    <!-- Instalments Overdue -->
    <a href="{{ route('payment-plans.index', ['filter' => 'overdue']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-salmon"><i class="fa-solid fa-dollar-sign"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['instalments_overdue'] ?? 0 }}</span>
          <span>Instalments Overdue</span>
        </div>
      </div>
    </a>

    <!-- IDs Expired -->
    <a href="{{ route('clients.index', ['filter' => 'ids_expired']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-black"><i class="fa-solid fa-user"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['ids_expired'] ?? 0 }}</span>
          <span>IDs Expired</span>
        </div>
      </div>
    </a>

    <!-- General Policies -->
    <a href="{{ route('policies.index', ['type' => 'general']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-black"><i class="fa-solid fa-expand"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['general_policies'] ?? 0 }}</span>
          <span>General Policies</span>
        </div>
      </div>
    </a>

    <!-- Gen-Com Outstanding -->
    <a href="{{ route('commissions.index', ['filter' => 'outstanding']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-black"><i class="fa-solid fa-expand"></i></span>
        <div class="card-content">
          <span class="value">{{ number_format($stats['gen_com_outstanding'] ?? 0, 2) }}</span>
          <span>Gen-Com Outstanding</span>
        </div>
      </div>
    </a>

    <!-- Open Leads -->
    <a href="{{ route('contacts.index', ['status' => 'open']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-black"><i class="fa-solid fa-user"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['open_leads'] ?? 0 }}</span>
          <span>Open Leads</span>
        </div>
      </div>
    </a>

    <!-- Follow Ups Today -->
    <a href="{{ route('contacts.index', ['follow_up' => '1']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-red"><i class="fa-solid fa-calendar-days"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['follow_ups_today'] ?? 0 }}</span>
          <span>Follow Ups Today</span>
        </div>
      </div>
    </a>

    <!-- Proposals Pending -->
    <a href="{{ route('life-proposals.index', ['status' => 'pending']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-black"><i class="fa-solid fa-expand"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['proposals_pending'] ?? 0 }}</span>
          <span>Proposals Pending</span>
        </div>
      </div>
    </a>

    <!-- Proposals Processing -->
    <a href="{{ route('life-proposals.index', ['status' => 'processing']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-black"><i class="fa-solid fa-expand"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['proposals_processing'] ?? 0 }}</span>
          <span>Proposals Processing</span>
        </div>
      </div>
    </a>

    <!-- Life Policies -->
    <a href="{{ route('policies.index', ['type' => 'life']) }}" class="card-link">
      <div class="card">
        <span class="icon icon-black"><i class="fa-solid fa-expand"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['life_policies'] ?? 0 }}</span>
          <span>Life Policies</span>
        </div>
      </div>
    </a>

    <!-- Birthdays This Month -->
    <a href="{{ route('clients.birthday-list') }}" class="card-link">
      <div class="card">
        <span class="icon icon-red"><i class="fa-solid fa-calendar-days"></i></span>
        <div class="card-content">
          <span class="value">{{ $stats['birthdays_today'] ?? 0 }}</span>
          <span>Birthdays This Month</span>
        </div>
      </div>
    </a>

  </div>
   @php
        // Determine selected chart year
        $chartYear = now()->year;
        if ($selectedDateRange) {
            if ($selectedDateRange == 'year') {
                $chartYear = now()->year;
            } elseif (str_starts_with($selectedDateRange, 'year-')) {
                $chartYear = (int) str_replace('year-', '', $selectedDateRange);
            }
        }

        $years = range(now()->year, now()->year - 5);
    @endphp


  <!-- Income vs Expense Charts -->
<div class="charts">
    {{-- Income vs Expense --}}
    <div class="chart-box">
        <div class="chart-controls">
            <h3 style="margin: 0;">Income v/s Expense - {{ $chartYear }}</h3>
            <div class="year-selector">
               <select name="incomeExpenseYear" id="incomeExpenseYear" onchange="updateChartYear('incomeExpense', this.value)">
                @php
                    $currentYear = now()->year;
                    $years = range($currentYear, $currentYear - 5); // last 5 years
                @endphp

                @foreach($years as $y)
                    <option value="{{ $y }}" {{ ($incomeExpenseYear ?? $chartYear) == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
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

    {{-- Income --}}
    <div class="chart-box">
        <div class="chart-controls">
            <h3 style="margin: 0;">Income - {{ $chartYear }}</h3>
            <div class="year-selector">
                <select name="incomeYear" id="incomeYear" onchange="updateChartYear('income', this.value)">
                   @foreach($years as $y)
                      <option value="{{ $y }}" {{ ($incomeYear ?? $chartYear) == $y ? 'selected' : '' }}>
                          {{ $y }}
                      </option>
                  @endforeach

                </select>
            </div>
        </div>
        <canvas id="incomeChart"></canvas>
        <div class="month-stats" id="incomeStats"></div>
    </div>

    {{-- Expenses --}}
    <div class="chart-box">
        <div class="chart-controls">
            <h3 style="margin: 0;">Expenses - {{ $chartYear }}</h3>
            <div class="year-selector">
                <select name="expenseYear" id="expenseYear" onchange="updateChartYear('expense', this.value)">
                       @foreach($years as $y)
                            <option value="{{ $y }}" {{ ($expenseYear ?? $chartYear) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
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
