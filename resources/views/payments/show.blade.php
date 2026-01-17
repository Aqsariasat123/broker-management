@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/payments-show.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Payment Details</h3>
    
    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('payments.index') }}" class="btn btn-back">Back to List</a>
        <a href="{{ route('payments.edit', $payment->id) }}" class="btn">Edit</a>
      </div>
    </div>

    <div class="info-section">
      <h4>Payment Information</h4>
      <div class="detail-row">
        <div class="detail-label">Payment Reference</div>
        <div class="detail-value">{{ $payment->payment_reference }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Debit Note</div>
        <div class="detail-value">{{ $payment->debitNote->debit_note_no ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Policy</div>
        <div class="detail-value">{{ $payment->debitNote->paymentPlan->schedule->policy->policy_no ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Client</div>
        <div class="detail-value">{{ $payment->debitNote->paymentPlan->schedule->policy->client->client_name ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Paid On</div>
        <div class="detail-value">{{ $payment->paid_on ? $payment->paid_on->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Amount</div>
        <div class="detail-value">{{ number_format($payment->amount, 2) }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Mode of Payment</div>
        <div class="detail-value">
          @if($payment->mode_of_payment_id)
            @php
              $mode = \App\Models\LookupValue::find($payment->mode_of_payment_id);
            @endphp
            {{ $mode->name ?? '-' }}
          @else
            -
          @endif
        </div>
      </div>
      @if($payment->receipt_path)
      <div class="detail-row">
        <div class="detail-label">Receipt</div>
        <div class="detail-value">
          <a href="{{ ($payment->is_encrypted ?? false) ? route('secure.file', ['type' => 'payment', 'id' => $payment->id]) : route('storage.serve', $payment->receipt_path) }}" target="_blank" class="btn-action" style="text-decoration:none;">View Receipt</a>
        </div>
      </div>
      @endif
      @if($payment->notes)
      <div class="detail-row">
        <div class="detail-label">Notes</div>
        <div class="detail-value">{{ $payment->notes }}</div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

