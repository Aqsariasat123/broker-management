@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/debit-notes-edit.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Edit Debit Note</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('debit-notes.index') }}" class="btn btn-back">Back</a>
      </div>
    </div>

    <div class="form-container">
      <form action="{{ route('debit-notes.update', $debitNote->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-row">
          <div class="form-group full-width">
            <label for="payment_plan_id">Payment Plan *</label>
            <select id="payment_plan_id" name="payment_plan_id" class="form-control" required>
              <option value="">Select Payment Plan</option>
              @foreach($paymentPlans as $plan)
                <option value="{{ $plan->id }}" {{ old('payment_plan_id', $debitNote->payment_plan_id) == $plan->id ? 'selected' : '' }}>
                  {{ $plan->schedule->policy->policy_no ?? 'N/A' }} - 
                  {{ $plan->schedule->policy->client->client_name ?? 'N/A' }} - 
                  {{ $plan->installment_label ?? 'Instalment #' . $plan->id }}
                </option>
              @endforeach
            </select>
            @error('payment_plan_id')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="debit_note_no">Debit Note Number *</label>
            <input type="text" id="debit_note_no" name="debit_note_no" class="form-control" required value="{{ old('debit_note_no', $debitNote->debit_note_no) }}">
            @error('debit_note_no')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="issued_on">Issued On *</label>
            <input type="date" id="issued_on" name="issued_on" class="form-control" required value="{{ old('issued_on', $debitNote->issued_on ? $debitNote->issued_on->format('Y-m-d') : '') }}">
            @error('issued_on')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="amount">Amount *</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0" class="form-control" required value="{{ old('amount', $debitNote->amount) }}">
            @error('amount')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" class="form-control" required>
              <option value="pending" {{ old('status', $debitNote->status) == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="issued" {{ old('status', $debitNote->status) == 'issued' ? 'selected' : '' }}>Issued</option>
              <option value="paid" {{ old('status', $debitNote->status) == 'paid' ? 'selected' : '' }}>Paid</option>
              <option value="overdue" {{ old('status', $debitNote->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
              <option value="cancelled" {{ old('status', $debitNote->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group full-width">
            <label for="document">Document (PDF, Image, Word, Excel)</label>
            <input type="file" id="document" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
            <small style="color:#666; font-size:11px;">Max file size: 10MB. Allowed formats: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX</small>
            @if($debitNote->document_path)
              <div class="current-file">
                <strong>Current document:</strong> 
                <a href="{{ ($debitNote->is_encrypted ?? false) ? route('secure.file', ['type' => 'debit-note', 'id' => $debitNote->id]) : route('storage.serve', $debitNote->document_path) }}" target="_blank">View Document</a>
                <span style="color:#666; font-size:11px;"> (Leave blank to keep current document)</span>
              </div>
            @endif
            @error('document')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:15px; border-top:1px solid #ddd;">
          <a href="{{ route('debit-notes.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-block;">Cancel</a>
          <button type="submit" class="btn-save">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

