@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/audit-logs-index.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Audit Logs</h3>
    
    <div class="filters">
      <form method="GET" action="{{ route('audit-logs.index') }}">
        <div class="filter-row">
          <div class="filter-group">
            <label>Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description...">
          </div>
          <div class="filter-group">
            <label>User</label>
            <select name="user_id">
              <option value="">All Users</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Action</label>
            <select name="action">
              <option value="">All Actions</option>
              @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Model Type</label>
            <select name="model_type">
              <option value="">All Models</option>
              @foreach($modelTypes as $type)
                <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}">
          </div>
          <div class="filter-group">
            <label>Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}">
          </div>
        </div>
        <div class="filter-actions">
          <button type="submit" class="btn" style="background:#007bff; color:#fff; border-color:#007bff;">Filter</button>
          <a href="{{ route('audit-logs.index') }}" class="btn" style="background:#6c757d; color:#fff; border-color:#6c757d;">Clear</a>
        </div>
      </form>
    </div>

    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Records Found - {{ $logs->total() }}</div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
      </div>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Description</th>
            <th>Model</th>
            <th>IP Address</th>
            <th>Method</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr>
              <td>{{ $log->created_at->format('d-M-y H:i:s') }}</td>
              <td>{{ $log->user ? $log->user->name : 'System' }}</td>
              <td>
                <span class="badge-action badge-{{ $log->action }}">
                  {{ ucfirst($log->action) }}
                </span>
              </td>
              <td>{{ $log->description }}</td>
              <td>
                @if($log->model_type)
                  {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                @else
                  -
                @endif
              </td>
              <td>{{ $log->ip_address ?? '-' }}</td>
              <td>{{ $log->method ?? '-' }}</td>
              <td>
                <a href="{{ route('audit-logs.show', $log->id) }}" class="btn-action" title="View Details">View</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:20px; color:#999;">No audit logs found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="footer">
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $logs->currentPage();
          $last = max(1, $logs->lastPage());
          function page_url($base, $q, $p) {
            $params = array_merge($q, ['page' => $p]);
            return $base . '?' . http_build_query($params);
          }
        @endphp

        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, 1) : '#' }}" @if($current <= 1) disabled @endif>&laquo;</a>
        <a class="btn-page" href="{{ $current > 1 ? page_url($base, $q, $current - 1) : '#' }}" @if($current <= 1) disabled @endif>&lsaquo;</a>

        <span style="padding:0 8px;">Page {{ $current }} of {{ $last }}</span>

        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $current + 1) : '#' }}" @if($current >= $last) disabled @endif>&rsaquo;</a>
        <a class="btn-page" href="{{ $current < $last ? page_url($base, $q, $last) : '#' }}" @if($current >= $last) disabled @endif>&raquo;</a>
      </div>
    </div>
  </div>
</div>
@endsection

