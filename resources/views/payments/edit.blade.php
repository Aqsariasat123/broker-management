@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/payments-edit.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Edit Payment</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('payments.index') }}" class="btn btn-back">Back</a>
      </div>
    </div>

    <div class="form-container">
      <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-row">
          <div class="form-group full-width">
            <label for="debit_note_id">Debit Note *</label>
            <select id="debit_note_id" name="debit_note_id" class="form-control" required>
              <option value="">Select Debit Note</option>
              @foreach($debitNotes as $note)
                <option value="{{ $note->id }}" {{ old('debit_note_id', $payment->debit_note_id) == $note->id ? 'selected' : '' }}>
                  {{ $note->debit_note_no }} - 
                  {{ $note->paymentPlan->schedule->policy->policy_no ?? 'N/A' }} - 
                  {{ $note->paymentPlan->schedule->policy->client->client_name ?? 'N/A' }} - 
                  Amount: {{ number_format($note->amount, 2) }} - 
                  Status: {{ ucfirst($note->status) }}
                </option>
              @endforeach
            </select>
            @error('debit_note_id')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="payment_reference">Payment Reference *</label>
            <input type="text" id="payment_reference" name="payment_reference" class="form-control" required value="{{ old('payment_reference', $payment->payment_reference) }}" placeholder="e.g., PAY-2025-001">
            @error('payment_reference')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="paid_on">Paid On *</label>
            <input type="date" id="paid_on" name="paid_on" class="form-control" required value="{{ old('paid_on', $payment->paid_on ? $payment->paid_on->format('Y-m-d') : '') }}">
            @error('paid_on')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="amount">Amount *</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0" class="form-control" required value="{{ old('amount', $payment->amount) }}">
            @error('amount')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="mode_of_payment_id">Mode of Payment</label>
            <select id="mode_of_payment_id" name="mode_of_payment_id" class="form-control">
              <option value="">Select Mode of Payment</option>
              @foreach($modesOfPayment as $mode)
                <option value="{{ $mode->id }}" {{ old('mode_of_payment_id', $payment->mode_of_payment_id) == $mode->id ? 'selected' : '' }}>{{ $mode->name }}</option>
              @endforeach
            </select>
            @error('mode_of_payment_id')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group full-width">
            <label for="receipt">Receipt Document (PDF, Image, Word, Excel)</label>
            <input type="file" id="receipt" name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
            <small style="color:#666; font-size:11px;">Max file size: 10MB. Allowed formats: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX</small>
            @if($payment->receipt_path)
              <div class="current-file">
                <strong>Current receipt:</strong> 
                <a href="{{ ($payment->is_encrypted ?? false) ? route('secure.file', ['type' => 'payment', 'id' => $payment->id]) : route('storage.serve', $payment->receipt_path) }}" target="_blank">View Receipt</a>
                <span style="color:#666; font-size:11px;"> (Leave blank to keep current receipt)</span>
              </div>
            @endif
            @error('receipt')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group full-width">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" class="form-control" style="min-height:80px;">{{ old('notes', $payment->notes) }}</textarea>
            @error('notes')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:15px; border-top:1px solid #ddd;">
          <a href="{{ route('payments.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-block;">Cancel</a>
          <button type="submit" class="btn-save">Update Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

