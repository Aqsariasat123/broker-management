<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Policy Details</title>
  
</head>
<body>
@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/policies-show.css') }}">


<div class="dashboard">
  <div class="container-table">
    <h3>Policy Details</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="action-buttons">
        <button class="btn btn-back" onclick="window.location.href='{{ route('policies.index') }}'">Back</button>
      </div>
    </div>

    <div class="info-section">
      <h4>Client & Policy Information</h4>
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Client</span>
          <span class="info-value">{{ $policy->client_name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Policy Number</span>
          <span class="info-value">{{ $policy->policy_no ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Policy Code</span>
          <span class="info-value">{{ $policy->policy_code ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Status</span>
          <span class="info-value">
            @php
              $statusName = $policy->policy_status_name ?? 'N/A';
              $statusColor = '#6c757d';
              if (stripos($statusName, 'In Force') !== false) $statusColor = '#28a745';
              elseif (stripos($statusName, 'DFR') !== false || stripos($statusName, 'Due') !== false) $statusColor = '#ffc107';
              elseif (stripos($statusName, 'Expired') !== false) $statusColor = '#6c757d';
              elseif (stripos($statusName, 'Cancelled') !== false) $statusColor = '#dc3545';
            @endphp
            <span class="badge-status" style="background:{{ $statusColor }}">{{ $statusName }}</span>
          </span>
        </div>
        <div class="info-item">
          <span class="info-label">Renewable</span>
          <span class="info-value">{{ $policy->renewable ? 'Yes' : 'No' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Date Registered</span>
          <span class="info-value">{{ $policy->date_registered ? $policy->date_registered->format('d-M-y') : 'N/A' }}</span>
        </div>
      </div>
    </div>

    <div class="info-section">
      <h4>Coverage Details</h4>
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Coverage Period</span>
          <span class="info-value">
            {{ optional($coverage['start_date'])->format('d-M-y') ?? 'N/A' }} – 
            {{ optional($coverage['end_date'])->format('d-M-y') ?? 'N/A' }}
            @if($coverage['coverage_duration'])
              <span style="color:#666; font-size:11px;">({{ $coverage['coverage_duration'] }} days)</span>
            @endif
          </span>
        </div>
        <div class="info-item">
          <span class="info-label">Days Remaining</span>
          <span class="info-value">
            @if($coverage['days_remaining'] !== null)
              @if($coverage['days_remaining'] < 0)
                <span style="color:#dc3545;">Expired {{ abs((int)$coverage['days_remaining']) }} days ago</span>
              @elseif($coverage['days_remaining'] <= 30)
                <span style="color:#ffc107; font-weight:bold;">{{ (int)$coverage['days_remaining'] }} days</span>
              @else
                <span style="color:#28a745;">{{ (int)$coverage['days_remaining'] }} days</span>
              @endif
            @else
              N/A
            @endif
          </span>
        </div>
        <div class="info-item">
          <span class="info-label">Sum Insured</span>
          <span class="info-value">{{ number_format($coverage['sum_insured'] ?? 0, 2) }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Base Premium</span>
          <span class="info-value">{{ number_format($coverage['base_premium'] ?? 0, 2) }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Total Premium</span>
          <span class="info-value" style="font-weight:bold;">{{ number_format($coverage['premium'] ?? 0, 2) }}</span>
        </div>
        @if($coverage['premium_difference'] > 0)
        <div class="info-item">
          <span class="info-label">Additional Charges</span>
          <span class="info-value" style="color:#dc3545;">{{ number_format($coverage['premium_difference'], 2) }}</span>
        </div>
        @endif
        @if($coverage['term'] && $coverage['term_unit'])
        <div class="info-item">
          <span class="info-label">Term</span>
          <span class="info-value">{{ $coverage['term'] }} {{ $coverage['term_unit'] }}</span>
        </div>
        @endif
        <div class="info-item">
          <span class="info-label">Insurer</span>
          <span class="info-value">{{ $policy->insurer_name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Policy Class</span>
          <span class="info-value">{{ $policy->policy_class_name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Policy Plan</span>
          <span class="info-value">{{ $policy->policy_plan_name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Frequency</span>
          <span class="info-value">{{ $policy->frequency_name ?? 'N/A' }}</span>
        </div>
      </div>
    </div>

    @if(isset($paymentSummary))
    <div class="info-section">
      <h4>Payment Summary</h4>
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Total Due</span>
          <span class="info-value" style="font-weight:bold;">{{ number_format($paymentSummary['total_due'] ?? 0, 2) }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Total Paid</span>
          <span class="info-value" style="color:#28a745; font-weight:bold;">{{ number_format($paymentSummary['total_paid'] ?? 0, 2) }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Outstanding</span>
          <span class="info-value" style="color:#dc3545; font-weight:bold;">{{ number_format($paymentSummary['total_outstanding'] ?? 0, 2) }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Total Installments</span>
          <span class="info-value">{{ $paymentSummary['total_installments'] ?? 0 }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Paid Installments</span>
          <span class="info-value" style="color:#28a745;">{{ $paymentSummary['paid_installments'] ?? 0 }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Pending Installments</span>
          <span class="info-value" style="color:#ffc107;">{{ $paymentSummary['pending_installments'] ?? 0 }}</span>
        </div>
        @if(($paymentSummary['overdue_installments'] ?? 0) > 0)
        <div class="info-item">
          <span class="info-label">Overdue Installments</span>
          <span class="info-value" style="color:#dc3545; font-weight:bold;">{{ $paymentSummary['overdue_installments'] }}</span>
        </div>
        @endif
      </div>
    </div>
    @endif

    <div class="info-section">
      <h4>Payment History</h4>
      <p style="font-size:12px; color:#555; margin-bottom:10px;">Instalments and recorded payments for this policy. Click on payment dates to view details.</p>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Schedule</th>
              <th>Installment</th>
              <th>Due Date</th>
              <th>Amount Due</th>
              <th>Status</th>
              <th>Payment Details</th>
            </tr>
          </thead>
          <tbody>
            @forelse($paymentHistory as $entry)
              @php
                $entry = is_array($entry) ? $entry : (array) $entry;
                $payments = isset($entry['payments']) && is_array($entry['payments']) ? $entry['payments'] : [];
              @endphp
              <tr>
                <td>{{ isset($entry['schedule_no']) ? (string) $entry['schedule_no'] : '—' }}</td>
                <td>{{ isset($entry['installment_label']) ? (string) $entry['installment_label'] : '—' }}</td>
                <td>
                  @php
                    $dueDate = isset($entry['due_date']) && $entry['due_date'] ? \Carbon\Carbon::parse($entry['due_date']) : null;
                  @endphp
                  @if($dueDate)
                    {{ $dueDate->format('d-M-y') }}
                    @if($dueDate->isPast() && (strtolower($entry['status'] ?? 'pending') !== 'paid'))
                      <span style="color:#dc3545; font-size:10px;">(Overdue)</span>
                    @endif
                  @else
                    —
                  @endif
                </td>
                <td>{{ number_format(isset($entry['amount']) ? (float) $entry['amount'] : 0, 2) }}</td>
                <td>
                  @php
                    $status = isset($entry['status']) ? strtolower((string) $entry['status']) : 'pending';
                    $statusColor = '#6c757d';
                    if ($status === 'paid') $statusColor = '#28a745';
                    elseif ($status === 'pending' || $status === 'active') $statusColor = '#ffc107';
                    elseif ($status === 'overdue') $statusColor = '#dc3545';
                  @endphp
                  <span class="badge-status" style="background:{{ $statusColor }}">{{ ucfirst($status) }}</span>
                </td>
                <td>
                  @php
                    $totalPaid = isset($entry['total_paid']) ? (float) $entry['total_paid'] : 0;
                    $outstanding = isset($entry['outstanding']) ? (float) $entry['outstanding'] : 0;
                  @endphp
                  <div style="font-size:11px;">
                    <div><strong>Paid:</strong> <span style="color:#28a745;">{{ number_format($totalPaid, 2) }}</span></div>
                    @if($outstanding > 0)
                    <div><strong>Outstanding:</strong> <span style="color:#dc3545;">{{ number_format($outstanding, 2) }}</span></div>
                    @endif
                    <ul class="payment-list" style="margin-top:4px;">
                      @forelse($payments as $payment)
                        @php
                          $payment = is_array($payment) ? $payment : (array) $payment;
                        @endphp
                        <li>
                          <a href="{{ route('payments.show', $payment['id'] ?? '#') }}" style="color:#007bff; text-decoration:underline;">
                            {{ isset($payment['paid_on']) && $payment['paid_on'] ? \Carbon\Carbon::parse($payment['paid_on'])->format('d-M-y') : 'N/A' }}
                          </a>
                          — {{ number_format(isset($payment['amount']) ? (float) $payment['amount'] : 0, 2) }}
                          @if(isset($payment['payment_reference']) && $payment['payment_reference'])
                            <span style="color:#666;">({{ (string) $payment['payment_reference'] }})</span>
                          @endif
                        </li>
                      @empty
                        <li style="color:#999;">No payments yet</li>
                      @endforelse
                    </ul>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="empty-state">No payment data available for this policy.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
</body>
</html>
