@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/schedules-edit.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Edit Schedule</h3>

    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('schedules.index') }}" class="btn btn-back">Back</a>
      </div>
    </div>

    <div class="form-container">
      <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-row">
          <div class="form-group full-width">
            <label for="policy_id">Policy *</label>
            <select id="policy_id" name="policy_id" class="form-control" required>
              <option value="">Select Policy</option>
              @foreach($policies as $policy)
                <option value="{{ $policy->id }}" {{ old('policy_id', $schedule->policy_id) == $policy->id ? 'selected' : '' }}>
                  {{ $policy->policy_no }} - {{ $policy->client->client_name ?? 'N/A' }}
                </option>
              @endforeach
            </select>
            @error('policy_id')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="schedule_no">Schedule Number *</label>
            <input type="text" id="schedule_no" name="schedule_no" class="form-control" required value="{{ old('schedule_no', $schedule->schedule_no) }}">
            @error('schedule_no')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" class="form-control" required>
              <option value="draft" {{ old('status', $schedule->status) == 'draft' ? 'selected' : '' }}>Draft</option>
              <option value="active" {{ old('status', $schedule->status) == 'active' ? 'selected' : '' }}>Active</option>
              <option value="expired" {{ old('status', $schedule->status) == 'expired' ? 'selected' : '' }}>Expired</option>
              <option value="cancelled" {{ old('status', $schedule->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="issued_on">Issued On</label>
            <input type="date" id="issued_on" name="issued_on" class="form-control" value="{{ old('issued_on', $schedule->issued_on ? $schedule->issued_on->format('Y-m-d') : '') }}">
            @error('issued_on')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="effective_from">Effective From</label>
            <input type="date" id="effective_from" name="effective_from" class="form-control" value="{{ old('effective_from', $schedule->effective_from ? $schedule->effective_from->format('Y-m-d') : '') }}">
            @error('effective_from')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="effective_to">Effective To</label>
            <input type="date" id="effective_to" name="effective_to" class="form-control" value="{{ old('effective_to', $schedule->effective_to ? $schedule->effective_to->format('Y-m-d') : '') }}">
            @error('effective_to')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group full-width">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" class="form-control" style="min-height:80px;">{{ old('notes', $schedule->notes) }}</textarea>
            @error('notes')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:15px; border-top:1px solid #ddd;">
          <a href="{{ route('schedules.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-block;">Cancel</a>
          <button type="submit" class="btn-save">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

