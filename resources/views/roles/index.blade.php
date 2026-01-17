@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/roles-index.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Roles Management</h3>

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

    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Total Roles: {{ $roles->count() }}</div>
      </div>
      <div class="action-buttons">
        <a href="{{ route('roles.create') }}" class="btn btn-add">Add Role</a>
        <a href="{{ route('permissions.index') }}" class="btn" style="background:#df7900; color:#fff; border-color:#df7900;">Manage Permissions</a>
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
      </div>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Description</th>
            <th>Type</th>
            <th>Users</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($roles as $role)
            <tr>
              <td>{{ $role->name }}</td>
              <td><code style="font-size:11px; background:#f5f5f5; padding:2px 6px; border-radius:2px;">{{ $role->slug }}</code></td>
              <td>{{ $role->description ?? '—' }}</td>
              <td>
                @if($role->is_system)
                  <span class="badge-role badge-system">System</span>
                @else
                  <span class="badge-role" style="background:#28a745;">Custom</span>
                @endif
              </td>
              <td>{{ $role->users()->count() }}</td>
              <td class="action-buttons-cell">
                <span class="icon-expand" onclick="switchTab({{ $role->id }})" title="Manage Permissions">⚙</span>
                <span class="icon-expand" onclick="window.location.href='{{ route('roles.edit', $role->id) }}'" title="Edit Role">⤢</span>
                @if(!$role->is_system && $role->users()->count() == 0)
                  <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this role?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action" style="background: #dc3545; color: white; border-color: #dc3545;" title="Delete Role">🗑</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="padding: 20px; text-align: center; color: #999;">No roles found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($roles->count() > 0)
      <div class="tabs-container">
        <div class="tabs-header">
          @foreach($roles as $index => $role)
            <button class="tab-button {{ $index === 0 ? 'active' : '' }}" 
                    onclick="switchTab({{ $role->id }})" 
                    id="tab-btn-{{ $role->id }}">
              {{ $role->name }}
              <span class="badge-role {{ $role->is_system ? 'badge-system' : '' }}" 
                    style="margin-left:8px; background:{{ $role->is_system ? '#6c757d' : '#28a745' }}; font-size:10px; padding:2px 6px;">
                {{ $role->is_system ? 'System' : 'Custom' }}
              </span>
              <span style="margin-left:8px; color:#999; font-size:11px;">({{ $role->users()->count() }})</span>
            </button>
          @endforeach
        </div>

        @foreach($roles as $index => $role)
          <div class="tab-content {{ $index === 0 ? 'active' : '' }}" id="tab-content-{{ $role->id }}">
            <div class="role-container">
              <form method="POST" action="{{ route('roles.permissions.update', $role->id) }}">
                @csrf
                @method('PUT')
                
                <div class="role-header">
                  <div>
                    <span class="role-title">{{ $role->name }}</span>
                    <span class="badge-role {{ $role->is_system ? 'badge-system' : '' }}" 
                          style="margin-left:10px; background:{{ $role->is_system ? '#6c757d' : '#28a745' }};">
                      {{ $role->is_system ? 'System' : 'Custom' }}
                    </span>
                    <span style="margin-left:10px; color:#666; font-size:13px;">
                      <code style="font-size:11px; background:#f5f5f5; padding:2px 6px; border-radius:2px;">{{ $role->slug }}</code>
                    </span>
                    @if($role->description)
                      <span style="margin-left:10px; color:#999; font-size:12px;">{{ $role->description }}</span>
                    @endif
                  </div>
                  <div style="display:flex; gap:10px; align-items:center;">
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn" style="background:#6c757d; color:#fff; border-color:#6c757d;">Edit Role</a>
                    @if(!$role->is_system && $role->users()->count() == 0)
                      <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this role?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn" style="background: #dc3545; color: white; border-color: #dc3545;">Delete</button>
                      </form>
                    @endif
                    <button type="submit" class="btn-save">Save Permissions</button>
                  </div>
                </div>

                <div class="permissions-grid">
                  @foreach($permissions as $module => $modulePermissions)
                    <div class="module-section">
                      <div>
                        <span class="module-title">{{ ucfirst($module ?: 'Other') }}</span>
                        <span class="select-all-module" onclick="toggleModule('{{ $role->id }}_{{ $module }}')">Select All</span>
                      </div>
                      @foreach($modulePermissions as $permission)
                        <div class="permission-item">
                          <input type="checkbox" 
                                 id="perm_{{ $role->id }}_{{ $permission->id }}" 
                                 name="permissions[]" 
                                 value="{{ $permission->id }}"
                                 data-module="{{ $role->id }}_{{ $module }}"
                                 {{ in_array($permission->id, $rolePermissions[$role->id] ?? []) ? 'checked' : '' }}>
                          <label for="perm_{{ $role->id }}_{{ $permission->id }}">
                            {{ $permission->name }}
                            <span class="permission-slug">({{ $permission->slug }})</span>
                          </label>
                        </div>
                      @endforeach
                    </div>
                  @endforeach
                </div>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div style="padding:40px; text-align:center; background:#fff; border:1px solid #ddd; margin-top:20px;">
        <p style="color:#999; font-size:14px;">No roles found. <a href="{{ route('roles.create') }}" style="color:#df7900;">Create your first role</a></p>
      </div>
    @endif
  </div>
</div>


<script src="{{ asset('js/roles-index.js') }}"></script>
@endsection

