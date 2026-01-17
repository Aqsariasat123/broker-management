@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/payments-report.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Financial Report</h3>

    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('payments.index') }}" class="btn btn-back">Back to Payments</a>
      </div>
    </div>

    <div class="filter-form">
      <form method="GET" action="{{ route('payments.report') }}">
        <div class="form-row">
          <div class="form-group">
            <label for="date_from">From Date</label>
            <input type="date" id="date_from" name="date_from" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
          </div>
          <div class="form-group">
            <label for="date_to">To Date</label>
            <input type="date" id="date_to" name="date_to" value="{{ request('date_to', now()->endOfMonth()->format('Y-m-d')) }}">
          </div>
          <div class="form-group">
            <button type="submit">Filter</button>
            <a href="{{ route('payments.report') }}" class="btn" style="margin-left:5px;">Reset</a>
          </div>
        </div>
      </form>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <h4>Total Payments</h4>
        <div class="value">{{ number_format($totalAmount, 2) }}</div>
        <div class="label">{{ $totalCount }} payment(s)</div>
      </div>
      <div class="stat-card">
        <h4>Average Payment</h4>
        <div class="value">{{ $totalCount > 0 ? number_format($totalAmount / $totalCount, 2) : '0.00' }}</div>
        <div class="label">Per transaction</div>
      </div>
      <div class="stat-card">
        <h4>Date Range</h4>
        <div class="value" style="font-size:14px;">{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}</div>
        <div class="label">to {{ request('date_to', now()->endOfMonth()->format('Y-m-d')) }}</div>
      </div>
      <div class="stat-card">
        <h4>Payment Methods</h4>
        <div class="value">{{ $byModeOfPayment->count() }}</div>
        <div class="label">Different methods</div>
      </div>
    </div>

    @if(isset($statusSummary))
    <div class="chart-container">
      <h4>Debit Note Status Summary</h4>
      <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
        <div class="stat-card">
          <h4>Paid</h4>
          <div class="value" style="color:#28a745;">{{ $statusSummary['paid'] }}</div>
        </div>
        <div class="stat-card">
          <h4>Partial</h4>
          <div class="value" style="color:#ffc107;">{{ $statusSummary['partial'] }}</div>
        </div>
        <div class="stat-card">
          <h4>Overdue</h4>
          <div class="value" style="color:#dc3545;">{{ $statusSummary['overdue'] }}</div>
        </div>
        <div class="stat-card">
          <h4>Pending</h4>
          <div class="value" style="color:#6c757d;">{{ $statusSummary['pending'] }}</div>
        </div>
        <div class="stat-card">
          <h4>Issued</h4>
          <div class="value" style="color:#17a2b8;">{{ $statusSummary['issued'] }}</div>
        </div>
      </div>
    </div>
    @endif

    @if(isset($byClient) && $byClient->count() > 0)
    <div class="chart-container">
      <h4>Top Clients by Payment Amount</h4>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Client</th>
              <th>Payment Count</th>
              <th>Total Amount</th>
              <th>Percentage</th>
            </tr>
          </thead>
          <tbody>
            @php
              $totalForPercentage = $totalAmount > 0 ? $totalAmount : 1;
            @endphp
            @foreach($byClient as $data)
              <tr>
                <td>{{ $data['client_name'] }}</td>
                <td>{{ $data['count'] }}</td>
                <td>{{ number_format($data['amount'], 2) }}</td>
                <td>{{ number_format(($data['amount'] / $totalForPercentage) * 100, 1) }}%</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

    @if($byDate->count() > 0)
    <div class="chart-container">
      <h4>Payments by Date</h4>
      <div class="bar-chart">
        @php
          $maxAmount = $byDate->max('amount');
        @endphp
        @foreach($byDate->take(30) as $date => $data)
          <div class="bar-item">
            <div class="bar" style="height: {{ $maxAmount > 0 ? ($data['amount'] / $maxAmount * 100) : 0 }}%;">
              <span class="bar-value">{{ number_format($data['amount'], 0) }}</span>
            </div>
            <div class="bar-label">{{ \Carbon\Carbon::parse($date)->format('d M') }}</div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($byModeOfPayment->count() > 0)
    <div class="chart-container">
      <h4>Payments by Mode of Payment</h4>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Mode of Payment</th>
              <th>Count</th>
              <th>Total Amount</th>
              <th>Percentage</th>
            </tr>
          </thead>
          <tbody>
            @php
              $totalForPercentage = $totalAmount > 0 ? $totalAmount : 1;
            @endphp
            @foreach($byModeOfPayment as $modeId => $data)
              @php
                $mode = $modeId ? \App\Models\LookupValue::find($modeId) : null;
              @endphp
              <tr>
                <td>{{ $mode ? $mode->name : 'Not Specified' }}</td>
                <td>{{ $data['count'] }}</td>
                <td>{{ number_format($data['amount'], 2) }}</td>
                <td>{{ number_format(($data['amount'] / $totalForPercentage) * 100, 1) }}%</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

    <div class="chart-container">
      <h4>Payment Details</h4>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Payment Reference</th>
              <th>Date</th>
              <th>Debit Note</th>
              <th>Client</th>
              <th>Policy</th>
              <th>Amount</th>
              <th>Mode of Payment</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $payment)
              <tr>
                <td>{{ $payment->payment_reference }}</td>
                <td>{{ $payment->paid_on ? $payment->paid_on->format('d-M-y') : '-' }}</td>
                <td>{{ $payment->debitNote->debit_note_no ?? '-' }}</td>
                <td>{{ $payment->debitNote->paymentPlan->schedule->policy->client->client_name ?? '-' }}</td>
                <td>{{ $payment->debitNote->paymentPlan->schedule->policy->policy_no ?? '-' }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
                <td>
                  @if($payment->mode_of_payment_id)
                    @php
                      $mode = \App\Models\LookupValue::find($payment->mode_of_payment_id);
                    @endphp
                    {{ $mode->name ?? '-' }}
                  @else
                    -
                  @endif
                </td>
                <td>
                  <a href="{{ route('payments.show', $payment->id) }}" class="btn-action" style="text-decoration:none; padding:2px 6px; font-size:11px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:2px; display:inline-block; color:#333;">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align:center; padding:20px; color:#999;">No payments found for the selected date range</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

