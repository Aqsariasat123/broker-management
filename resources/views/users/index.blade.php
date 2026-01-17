@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/users-index.css') }}">



@if(session('success'))
  <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
    {{ session('success') }}
    <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger" id="errorAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #f5c6cb; background:#f8d7da; color:#721c24;">
    {{ session('error') }}
    <button type="button" class="alert-close" onclick="document.getElementById('errorAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
  </div>
@endif

<div class="dashboard">
  <div class="container-table">
    <h3>Users</h3>

    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Records Found - {{ $users->total() }}</div>
        <div class="left-buttons">
          <form method="GET" action="{{ route('users.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}" style="min-width: 150px;">
            <select name="role">
              <option value="">All Roles</option>
              @php
                $roles = \App\Models\Role::orderBy('name')->get();
              @endphp
              @foreach($roles as $r)
                <option value="{{ $r->slug }}" {{ request('role') == $r->slug ? 'selected' : '' }}>{{ $r->name }}</option>
              @endforeach
              @if(\App\Models\Role::where('slug', 'admin')->doesntExist())
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
              @endif
              @if(\App\Models\Role::where('slug', 'support')->doesntExist())
                <option value="support" {{ request('role') == 'support' ? 'selected' : '' }}>Support</option>
              @endif
            </select>
            <select name="status">
              <option value="">All Status</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn">Filter</button>
            @if(request()->hasAny(['search', 'role', 'status']))
              <a href="{{ route('users.index') }}" class="btn">Clear</a>
            @endif
          </form>
        </div>
      </div>
      <div class="action-buttons">
        @auth
        @if(auth()->user()->isAdmin())
          <a href="{{ route('users.create') }}" class="btn btn-add">Add</a>
        @endif
        @endauth
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
      </div>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last Login</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr class="{{ !$user->is_active ? 'inactive-row' : '' }}">
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                @php
                  $roleName = $user->roleModel ? $user->roleModel->name : ($user->role ?? 'N/A');
                  $roleSlug = $user->roleModel ? $user->roleModel->slug : ($user->role ?? '');
                  $roleColor = ($roleSlug == 'admin') ? '#dc3545' : '#007bff';
                @endphp
                <span class="badge-status" style="background: {{ $roleColor }};">
                  {{ $roleName }}
                </span>
              </td>
              <td>
                <span class="badge-status" style="background: {{ $user->is_active ? '#28a745' : '#6c757d' }};">
                  {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                @if($user->last_login_at)
                  {{ $user->last_login_at->format('d-M-y H:i') }}
                  <br><small style="color: #999; font-size: 11px;">{{ $user->last_login_ip }}</small>
                @else
                  <span style="color: #999;">Never</span>
                @endif
              </td>
              <td>{{ $user->created_at->format('d-M-y') }}</td>
              <td class="action-buttons-cell">
                <a href="{{ route('users.show', $user->id) }}" class="btn-action" title="View User">👁</a>
                @auth
                @if(auth()->user()->isAdmin())
                  <span class="icon-expand" onclick="window.location.href='{{ route('users.edit', $user->id) }}'" title="Edit User">⤢</span>
                  @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-action" style="background: #dc3545; color: white; border-color: #dc3545;" title="Delete User">🗑</button>
                    </form>
                  @endif
                @endif
                @endauth
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="padding: 20px; text-align: center; color: #999;">No users found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="footer">
      <div class="paginator">
        {{ $users->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

