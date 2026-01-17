@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/users-edit.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Edit User</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('users.index') }}" class="btn btn-back">Back</a>
      </div>
    </div>

    <div class="form-container">
      <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
          <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
            @error('name')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
            @error('email')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" class="form-control">
            @error('password')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="role_id">Role *</label>
            <select id="role_id" name="role_id" class="form-control" required>
              <option value="">Select Role</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
              @endforeach
            </select>
            @error('role_id')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label>&nbsp;</label>
            <div class="checkbox-group">
              <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
              <label for="is_active" style="font-weight:normal; margin:0; cursor:pointer;">Active</label>
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:15px; border-top:1px solid #ddd;">
          <a href="{{ route('users.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-block;">Cancel</a>
          @if($user->id !== auth()->id())
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-delete">Delete</button>
            </form>
          @endif
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

