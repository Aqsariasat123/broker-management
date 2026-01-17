@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/audit-logs-show.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Audit Log Details</h3>
    
    <div class="action-buttons">
      <a href="{{ route('audit-logs.index') }}" class="btn btn-back">Back to List</a>
    </div>

    <div class="detail-section">
      <h4>Basic Information</h4>
      <div class="detail-row">
        <div class="detail-label">Date & Time</div>
        <div class="detail-value">{{ $auditLog->created_at->format('d-M-Y H:i:s') }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">User</div>
        <div class="detail-value">{{ $auditLog->user ? $auditLog->user->name : 'System' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Action</div>
        <div class="detail-value">
          <span class="badge-action badge-{{ $auditLog->action }}">
            {{ ucfirst($auditLog->action) }}
          </span>
        </div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Description</div>
        <div class="detail-value">{{ $auditLog->description }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Model</div>
        <div class="detail-value">
          @if($auditLog->model_type)
            {{ class_basename($auditLog->model_type) }} #{{ $auditLog->model_id }}
          @else
            -
          @endif
        </div>
      </div>
    </div>

    <div class="detail-section">
      <h4>Request Information</h4>
      <div class="detail-row">
        <div class="detail-label">IP Address</div>
        <div class="detail-value">{{ $auditLog->ip_address ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">User Agent</div>
        <div class="detail-value">{{ $auditLog->user_agent ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">HTTP Method</div>
        <div class="detail-value">{{ $auditLog->method ?? '-' }}</div>
      </div>
      <div class="detail-row">
        <div class="detail-label">URL</div>
        <div class="detail-value">{{ $auditLog->url ?? '-' }}</div>
      </div>
    </div>

    @if($auditLog->old_values || $auditLog->new_values)
    <div class="detail-section">
      <h4>Data Changes</h4>
      @if($auditLog->old_values)
      <div class="detail-row">
        <div class="detail-label">Old Values</div>
        <div class="detail-value">
          <div class="json-view">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</div>
        </div>
      </div>
      @endif
      @if($auditLog->new_values)
      <div class="detail-row">
        <div class="detail-label">New Values</div>
        <div class="detail-value">
          <div class="json-view">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</div>
        </div>
      </div>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection

