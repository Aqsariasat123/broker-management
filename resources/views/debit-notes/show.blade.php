@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/debit-notes-show.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Debit Note Details</h3>
    
    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('debit-notes.index') }}" class="btn btn-back">Back to List</a>
        <a href="{{ route('debit-notes.edit', $debitNote->id) }}" class="btn">Edit</a>
        <a href="{{ route('payments.create') }}?debit_note_id={{ $debitNote->id }}" class="btn btn-add">Add Payment</a>
      </div>
    </div>

    <div class="info-section">
      <h4>Debit Note Information</h4>
      <div class="detail-row">
        <div class="detail-label">Debit Note Number</div>
        <div class="detail-value">{{ $debitNote->debit_note_no }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Policy</div>
        <div class="detail-value">{{ $debitNote->paymentPlan->schedule->policy->policy_no ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Client</div>
        <div class="detail-value">{{ $debitNote->paymentPlan->schedule->policy->client->client_name ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Payment Plan</div>
        <div class="detail-value">{{ $debitNote->paymentPlan->installment_label ?? 'Instalment #' . $debitNote->paymentPlan->id }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Issued On</div>
        <div class="detail-value">{{ $debitNote->issued_on ? $debitNote->issued_on->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Amount</div>
        <div class="detail-value">{{ number_format($debitNote->amount, 2) }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">
          <span class="badge-status badge-{{ $debitNote->status }}">
            {{ ucfirst($debitNote->status) }}
          </span>
        </div>
      </div>
      @if($debitNote->document_path)
      <div class="detail-row">
        <div class="detail-label">Document</div>
        <div class="detail-value">
          <a href="{{ ($debitNote->is_encrypted ?? false) ? route('secure.file', ['type' => 'debit-note', 'id' => $debitNote->id]) : route('storage.serve', $debitNote->document_path) }}" target="_blank" class="btn-action" style="text-decoration:none;">View Document</a>
        </div>
      </div>
      @endif
    </div>

    <div class="info-section">
      <h4>Payments</h4>
      @php
        $totalPaid = $debitNote->payments->sum('amount');
        $remaining = $debitNote->amount - $totalPaid;
      @endphp
      <div style="margin-bottom:10px; padding:8px; background:#f9f9f9; border:1px solid #ddd; border-radius:4px;">
        <strong>Total Amount:</strong> {{ number_format($debitNote->amount, 2) }} | 
        <strong>Total Paid:</strong> {{ number_format($totalPaid, 2) }} | 
        <strong>Remaining:</strong> {{ number_format($remaining, 2) }}
      </div>
      
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Payment Reference</th>
              <th>Paid On</th>
              <th>Amount</th>
              <th>Mode of Payment</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($debitNote->payments as $payment)
              <tr>
                <td>{{ $payment->payment_reference }}</td>
                <td>{{ $payment->paid_on ? $payment->paid_on->format('d-M-y') : '-' }}</td>
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
                  <a href="{{ route('payments.show', $payment->id) }}" class="btn-action">View</a>
                  <a href="{{ route('payments.edit', $payment->id) }}" class="btn-action">Edit</a>
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

