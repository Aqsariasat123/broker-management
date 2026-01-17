@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/payment-plans-show.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Payment Plan Details</h3>
    
    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('payment-plans.index') }}" class="btn btn-back">Back to List</a>
        <a href="{{ route('payment-plans.edit', $paymentPlan->id) }}" class="btn">Edit</a>
        <a href="{{ route('debit-notes.create') }}?payment_plan_id={{ $paymentPlan->id }}" class="btn" style="background:#df7900; color:#fff; border-color:#df7900;">Add Debit Note</a>
      </div>
    </div>

    <div class="info-section">
      <h4>Payment Plan Information</h4>
      <div class="detail-row">
        <div class="detail-label">Instalment Label</div>
        <div class="detail-value">{{ $paymentPlan->installment_label ?? 'Instalment #' . $paymentPlan->id }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Policy</div>
        <div class="detail-value">{{ $paymentPlan->schedule->policy->policy_no ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Client</div>
        <div class="detail-value">{{ $paymentPlan->schedule->policy->client->client_name ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Schedule</div>
        <div class="detail-value">{{ $paymentPlan->schedule->schedule_no ?? 'Schedule #' . $paymentPlan->schedule->id }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Due Date</div>
        <div class="detail-value">{{ $paymentPlan->due_date ? $paymentPlan->due_date->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Amount</div>
        <div class="detail-value">{{ number_format($paymentPlan->amount, 2) }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Frequency</div>
        <div class="detail-value">{{ $paymentPlan->frequency ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">
          <span class="badge-status badge-{{ $paymentPlan->status }}">
            {{ ucfirst($paymentPlan->status) }}
          </span>
        </div>
      </div>
    </div>

    <div class="info-section">
      <h4>Debit Notes</h4>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Debit Note No</th>
              <th>Issued On</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($paymentPlan->debitNotes as $note)
              <tr>
                <td>{{ $note->debit_note_no }}</td>
                <td>{{ $note->issued_on ? $note->issued_on->format('d-M-y') : '-' }}</td>
                <td>{{ number_format($note->amount, 2) }}</td>
                <td>
                  <span class="badge-status badge-{{ $note->status }}">
                    {{ ucfirst($note->status) }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('debit-notes.show', $note->id) }}" class="btn-action">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#999;">No debit notes for this payment plan</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="info-section">
      <h4>Payments</h4>
      @php
        $allPayments = $paymentPlan->debitNotes->flatMap->payments;
        $totalPaid = $allPayments->sum('amount');
      @endphp
      <div style="margin-bottom:10px; padding:8px; background:#f9f9f9; border:1px solid #ddd; border-radius:4px;">
        <strong>Total Amount:</strong> {{ number_format($paymentPlan->amount, 2) }} | 
        <strong>Total Paid:</strong> {{ number_format($totalPaid, 2) }} | 
        <strong>Remaining:</strong> {{ number_format($paymentPlan->amount - $totalPaid, 2) }}
      </div>
      
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Payment Reference</th>
              <th>Debit Note</th>
              <th>Paid On</th>
              <th>Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($allPayments as $payment)
              <tr>
                <td>{{ $payment->payment_reference }}</td>
                <td>{{ $payment->debitNote->debit_note_no ?? '-' }}</td>
                <td>{{ $payment->paid_on ? $payment->paid_on->format('d-M-y') : '-' }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
                <td>
                  <a href="{{ route('payments.show', $payment->id) }}" class="btn-action">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:#999;">No payments recorded yet</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

