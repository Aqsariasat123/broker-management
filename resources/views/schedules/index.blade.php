@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/schedules-index.css') }}">



@if(session('success'))
  <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
    {{ session('success') }}
    <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
  </div>
@endif

<div class="dashboard">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="page-title-section">
              <h3 style="margin:0; font-size:18px; font-weight:600;">
                Schedules
              </h3>
           </div>
      </div>
  </div>
  <div class="container-table" style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:15px 20px;">

    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Records Found - {{ $schedules->total() }}</div>
        <form method="GET" action="{{ route('schedules.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
          <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
          <select name="status">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
          <button type="submit" class="btn">Filter</button>
          @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('schedules.index') }}" class="btn">Clear</a>
          @endif
        </form>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" onclick="openScheduleModal('add')">Add</button>
        <button class="btn btn-back" onclick="window.history.back()">Back</button>
      </div>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Schedule No</th>
            <th>Policy</th>
            <th>Client</th>
            <th>Issued On</th>
            <th>Effective From</th>
            <th>Effective To</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedules as $schedule)
            <tr>
              <td>{{ $schedule->schedule_no }}</td>
              <td>{{ $schedule->policy->policy_no ?? '-' }}</td>
              <td>{{ $schedule->policy->client->client_name ?? '-' }}</td>
              <td>{{ $schedule->issued_on ? $schedule->issued_on->format('d-M-y') : '-' }}</td>
              <td>{{ $schedule->effective_from ? $schedule->effective_from->format('d-M-y') : '-' }}</td>
              <td>{{ $schedule->effective_to ? $schedule->effective_to->format('d-M-y') : '-' }}</td>
              <td>
                <span class="badge-status badge-{{ $schedule->status }}">
                  {{ ucfirst($schedule->status) }}
                </span>
              </td>
              <td>
                <a href="{{ route('schedules.show', $schedule->id) }}" class="btn-action">View</a>
                <button type="button" class="btn-action" onclick="openScheduleModal('edit', {{ $schedule->id }})">Edit</button>
                <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-action">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:20px; color:#999;">No schedules found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="footer">
      {{ $schedules->links() }}
    </div>
  </div>
</div>

<!-- Add/Edit Schedule Modal -->
<div class="modal" id="scheduleModal">
  <div class="modal-content" style="max-width:800px; max-height:90vh; overflow-y:auto;">
    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd; background:#fff;">
      <h4 id="scheduleModalTitle" style="margin:0; font-size:18px; font-weight:bold;">Add Schedule</h4>
      <div style="display:flex; gap:10px;">
        <button type="submit" form="scheduleForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Save</button>
        <button type="button" class="btn-cancel" onclick="closeScheduleModal()" style="background:#000; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Cancel</button>
      </div>
    </div>
    <form id="scheduleForm" method="POST" action="{{ route('schedules.store') }}">
      @csrf
      <div id="scheduleFormMethod" style="display:none;"></div>
      <div class="modal-body" style="padding:20px;">
        <div class="form-row full-width" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="policy_id" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Policy *</label>
            <select id="policy_id" name="policy_id" class="form-control" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">Select Policy</option>
              @foreach($policies as $policy)
                <option value="{{ $policy->id }}">
                  {{ $policy->policy_no }} - {{ $policy->client->client_name ?? 'N/A' }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="schedule_no" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Schedule Number *</label>
            <input type="text" id="schedule_no" name="schedule_no" class="form-control" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
          <div class="form-group" style="flex:1;">
            <label for="status" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Status *</label>
            <select id="status" name="status" class="form-control" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="draft">Draft</option>
              <option value="active">Active</option>
              <option value="expired">Expired</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="issued_on" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Issued On</label>
            <input type="date" id="issued_on" name="issued_on" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
          <div class="form-group" style="flex:1;">
            <label for="effective_from" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Effective From</label>
            <input type="date" id="effective_from" name="effective_from" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>
        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="effective_to" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Effective To</label>
            <input type="date" id="effective_to" name="effective_to" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>
        <div class="form-row full-width" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1 1 100%;">
            <label for="notes" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Notes</label>
            <textarea id="notes" name="notes" class="form-control" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; resize:vertical;"></textarea>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
  // Initialize data from Blade
  const schedulesStoreRoute = '{{ route("schedules.store") }}';
  const schedulesUpdateRouteTemplate = '{{ route("schedules.update", ":id") }}';
</script>
<script src="{{ asset('js/schedules-index.js') }}"></script>
@endsection

