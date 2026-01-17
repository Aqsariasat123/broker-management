@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/users-show.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>User Details</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:10px 14px; margin-bottom:15px; border:1px solid #c3e6cb; background:#d4edda; color:#155724; border-radius:4px; display:flex; justify-content:space-between; align-items:center;">
        <span>{{ session('success') }}</span>
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="background:none;border:none;font-size:18px;cursor:pointer;color:#155724;padding:0;margin-left:10px;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="action-buttons">
        @auth
        @if(auth()->user()->isAdmin())
          <a href="{{ route('users.edit', $user->id) }}" class="btn btn-add">Edit</a>
        @endif
        @endauth
        <button class="btn btn-back" onclick="window.location.href='{{ route('users.index') }}'">Back</button>
      </div>
    </div>

    <div class="info-section">
      <h4>User Information</h4>
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Name</span>
          <span class="info-value">{{ $user->name }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Email</span>
          <span class="info-value">{{ $user->email }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Role</span>
          <span class="info-value">
            @php
              $roleName = $user->roleModel ? $user->roleModel->name : ($user->role ?? 'N/A');
              $roleSlug = $user->roleModel ? $user->roleModel->slug : ($user->role ?? '');
              $roleColor = ($roleSlug == 'admin') ? '#dc3545' : '#007bff';
            @endphp
            <span class="badge-status" style="background: {{ $roleColor }};">
              {{ $roleName }}
            </span>
          </span>
        </div>
        <div class="info-item">
          <span class="info-label">Status</span>
          <span class="info-value">
            <span class="badge-status" style="background: {{ $user->is_active ? '#28a745' : '#6c757d' }};">
              {{ $user->is_active ? 'Active' : 'Inactive' }}
            </span>
          </span>
        </div>
        <div class="info-item">
          <span class="info-label">Last Login</span>
          <span class="info-value">
            @if($user->last_login_at)
              {{ $user->last_login_at->format('d-M-y H:i') }}
              <br><small style="color: #999; font-size: 11px;">IP: {{ $user->last_login_ip }}</small>
            @else
              <span style="color: #999;">Never</span>
            @endif
          </span>
        </div>
        <div class="info-item">
          <span class="info-label">Created</span>
          <span class="info-value">{{ $user->created_at->format('d-M-y H:i') }}</span>
        </div>
      </div>
    </div>

    <div class="info-section">
      <h4>Recent Activity</h4>
      <div class="activity-list">
        @forelse($recentLogs as $log)
          <div class="activity-item">
            <div class="activity-action">{{ ucfirst($log->action) }}</div>
            <div class="activity-description">{{ $log->description }}</div>
            <div class="activity-meta">
              {{ $log->created_at->format('d-M-y H:i') }}
              @if($log->ip_address)
                • {{ $log->ip_address }}
              @endif
            </div>
          </div>
        @empty
          <div class="empty-state">No activity recorded</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

