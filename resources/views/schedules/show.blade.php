@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/schedules-show.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Schedule Details</h3>
    
    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('schedules.index') }}" class="btn btn-back">Back to List</a>
        <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn">Edit</a>
        <a href="{{ route('payment-plans.create') }}?schedule_id={{ $schedule->id }}" class="btn" style="background:#df7900; color:#fff; border-color:#df7900;">Add Payment Plan</a>
      </div>
    </div>

    <div class="info-section">
      <h4>Schedule Information</h4>
      <div class="detail-row">
        <div class="detail-label">Schedule Number</div>
        <div class="detail-value">{{ $schedule->schedule_no }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Policy</div>
        <div class="detail-value">{{ $schedule->policy->policy_no ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Client</div>
        <div class="detail-value">{{ $schedule->policy->client->client_name ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Issued On</div>
        <div class="detail-value">{{ $schedule->issued_on ? $schedule->issued_on->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Effective From</div>
        <div class="detail-value">{{ $schedule->effective_from ? $schedule->effective_from->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Effective To</div>
        <div class="detail-value">{{ $schedule->effective_to ? $schedule->effective_to->format('d-M-Y') : '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">
          <span class="badge-status badge-{{ $schedule->status }}">
            {{ ucfirst($schedule->status) }}
          </span>
        </div>
      </div>
      @if($schedule->notes)
      <div class="detail-row">
        <div class="detail-label">Notes</div>
        <div class="detail-value">{{ $schedule->notes }}</div>
      </div>
      @endif
    </div>

    <div class="info-section">
      <h4>Payment Plans</h4>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Instalment Label</th>
              <th>Due Date</th>
              <th>Amount</th>
              <th>Frequency</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($schedule->paymentPlans as $plan)
              <tr>
                <td>{{ $plan->installment_label ?? 'Instalment #' . $plan->id }}</td>
                <td>{{ $plan->due_date ? $plan->due_date->format('d-M-y') : '-' }}</td>
                <td>{{ number_format($plan->amount, 2) }}</td>
                <td>{{ $plan->frequency ?? '-' }}</td>
                <td>
                  <span class="badge-status badge-{{ $plan->status }}">
                    {{ ucfirst($plan->status) }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('payment-plans.show', $plan->id) }}" class="btn-action">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#999;">No payment plans for this schedule</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

