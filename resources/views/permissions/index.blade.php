@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/permissions-index.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Permissions</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Records Found - {{ $permissions->total() }}</div>
        <div class="left-buttons">
          <form method="GET" action="{{ route('permissions.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Search permissions..." value="{{ request('search') }}" style="min-width: 150px;">
            <select name="module">
              <option value="">All Modules</option>
              @foreach($modules as $module)
                <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
              @endforeach
            </select>
            <button type="submit" class="btn">Filter</button>
            @if(request()->hasAny(['search', 'module']))
              <a href="{{ route('permissions.index') }}" class="btn">Clear</a>
            @endif
          </form>
        </div>
      </div>
      <div class="action-buttons">
        <a href="{{ route('permissions.create') }}" class="btn btn-add">Add</a>
        <a href="{{ route('roles.index') }}" class="btn" style="background:#007bff; color:#fff; border-color:#007bff;">Manage Roles</a>
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
      </div>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Module</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($permissions as $permission)
            <tr>
              <td>{{ $permission->name }}</td>
              <td><code style="font-size:11px; background:#f5f5f5; padding:2px 6px; border-radius:2px;">{{ $permission->slug }}</code></td>
              <td>
                @if($permission->module)
                  <span class="badge-module">{{ ucfirst($permission->module) }}</span>
                @else
                  <span style="color: #999;">—</span>
                @endif
              </td>
              <td>{{ $permission->description ?? '—' }}</td>
              <td class="action-buttons-cell">
                <span class="icon-expand" onclick="window.location.href='{{ route('permissions.edit', $permission->id) }}'" title="Edit Permission">⤢</span>
                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this permission?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action" style="background: #dc3545; color: white; border-color: #dc3545;" title="Delete Permission">🗑</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="padding: 20px; text-align: center; color: #999;">No permissions found</td>
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
          $current = $permissions->currentPage();
          $last = max(1, $permissions->lastPage());
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

