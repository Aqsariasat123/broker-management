@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/permissions-create.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Create Permission</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="left-group">
        <a href="{{ route('permissions.index') }}" class="btn btn-back">Back</a>
      </div>
    </div>

    <div class="form-container">
      <form method="POST" action="{{ route('permissions.store') }}">
        @csrf

        <div class="form-row">
          <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
            @error('name')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="slug">Slug *</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="form-control" required placeholder="e.g., policies.create">
            @error('slug')<span class="error-message">{{ $message }}</span>@enderror
            <small style="color:#666; font-size:11px;">Format: module.action (e.g., policies.create)</small>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="module">Module</label>
            <input type="text" id="module" name="module" value="{{ old('module') }}" class="form-control" list="modules" placeholder="e.g., policies">
            <datalist id="modules">
              @foreach($modules as $module)
                <option value="{{ $module }}">
              @endforeach
            </datalist>
            @error('module')<span class="error-message">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="{{ old('description') }}" class="form-control">
            @error('description')<span class="error-message">{{ $message }}</span>@enderror
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:15px; border-top:1px solid #ddd;">
          <a href="{{ route('permissions.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-block;">Cancel</a>
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

