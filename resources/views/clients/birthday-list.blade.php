@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/clients-index.css') }}">
@include('partials.table-styles')

@php
  $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  $currentMonth = request()->get('month', now()->month);
  $monthName = $monthNames[$currentMonth] ?? 'This Month';
@endphp

<div class="dashboard">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h3 style="margin:0; font-size:18px; font-weight:600;">
        Birthday List - <span style="color:#f3742a;">{{ $monthName }}</span>
      </h3>
    </div>
  </div>

  <div class="clients-table-view" id="clientsTableView">
    <div class="container-table">
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
        <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
          <div class="records-found">Records Found - {{ $clients->total() }}</div>
          <div class="page-title-section">
            <div style="display:flex; align-items:center; gap:15px;">
              <div class="filter-group">
                <label class="toggle-switch">
                  <input type="checkbox" id="filterToggle" {{ request()->get('show_done') ? '' : 'checked' }}>
                  <span class="toggle-slider"></span>
                </label>
                <label for="filterToggle" style="font-size:14px; color:#2d2d2d; margin:0; cursor:pointer; user-select:none;">Filter</label>
              </div>
            </div>
          </div>
          <div class="action-buttons">
            <button class="btn btn-back" onclick="window.location.href='/calendar?filter=birthdays'">Back</button>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">&times;</button>
          </div>
        @endif

        <div class="table-responsive" id="tableResponsive">
          <table id="birthdayTable">
            <thead>
              <tr>
                <th style="text-align:center;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle;">
                    <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 2 16 2 16H22C22 16 19 14.25 19 9C19 5.13 15.87 2 12 2Z" fill="#fff" stroke="#fff" stroke-width="1.5"/>
                    <path d="M9 21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21H9Z" fill="#fff"/>
                  </svg>
                </th>
                <th>Action</th>
                <th>Name</th>
                <th>Date Of Birth</th>
                <th>Age</th>
                <th>Client Status</th>
                <th>Mobile No</th>
                <th style="text-align:center;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle;">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" fill="#fff"/>
                  </svg>
                </th>
                <th>Contact No</th>
                <th>Home No</th>
                <th>Email Address</th>
                <th>Medium</th>
                <th>Wish Status</th>
                <th>Date Done</th>
              </tr>
            </thead>
            <tbody>
              @foreach($clients as $client)
                @php
                  $age = $client->dob_dor ? now()->diffInYears($client->dob_dor) : '-';
                  $wishDone = $client->bday_wish_status && in_array($client->bday_wish_status, ['Done', 'Sent']);
                @endphp
                <tr data-client-id="{{ $client->id }}">
                  <td class="bell-cell {{ $wishDone ? '' : 'pending' }}">
                    <div style="display:flex; align-items:center; justify-content:center;">
                      <div class="status-indicator {{ $wishDone ? 'done' : 'pending' }}"
                           style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $wishDone ? '#28a745' : '#ccc' }};
                                  background-color:{{ $wishDone ? '#28a745' : 'transparent' }};"></div>
                    </div>
                  </td>
                  <td class="action-cell">
                    <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand"
                         onclick="openBirthdayEditPanel({{ $client->id }})" width="22" height="22"
                         style="cursor:pointer; vertical-align:middle;" alt="Expand">
                  </td>
                  <td>
                    <a href="javascript:void(0)" onclick="openBirthdayEditPanel({{ $client->id }})" style="color:#007bff; text-decoration:underline;">
                      {{ $client->client_name }}
                    </a>
                  </td>
                  <td>{{ $client->dob_dor ? $client->dob_dor->format('d-M-y') : '-' }}</td>
                  <td>{{ $age }}</td>
                  <td>{{ $client->status == 'Inactive' ? 'Dormant' : ($client->status == 'Active' ? 'Active' : $client->status) }}</td>
                  <td>{{ $client->mobile_no ?? '-' }}</td>
                  <td style="text-align:center;">{{ $client->wa ? 'Y' : '' }}</td>
                  <td>{{ $client->alternate_no ?? '-' }}</td>
                  <td>{{ $client->home_no ?? '-' }}</td>
                  <td>
                    @if($client->email_address)
                      <a href="mailto:{{ $client->email_address }}" style="color:#007bff;">{{ $client->email_address }}</a>
                    @else
                      -
                    @endif
                  </td>
                  <td>{{ $client->bday_medium ?? '-' }}</td>
                  <td>{{ $client->bday_wish_status ?? 'Not Done' }}</td>
                  <td>{{ $client->bday_date_done ? $client->bday_date_done->format('d-M-y') : '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="footer" style="background:#fff; border-top:1px solid #ddd; margin-top:0;">
          <div class="footer-left">
            <a class="btn btn-export" href="{{ route('clients.birthday-export', request()->query()) }}">Export</a>
            <button class="btn btn-column" id="columnBtn" type="button">Column</button>
          </div>
          <div class="paginator">
            @php
              $base = url()->current();
              $q = request()->query();
              $current = $clients->currentPage();
              $last = max(1, $clients->lastPage());
            @endphp
            <a class="btn-page" href="{{ $current > 1 ? $base . '?' . http_build_query(array_merge($q, ['page' => 1])) : '#' }}" @if($current <= 1) disabled @endif>&laquo;</a>
            <a class="btn-page" href="{{ $current > 1 ? $base . '?' . http_build_query(array_merge($q, ['page' => $current - 1])) : '#' }}" @if($current <= 1) disabled @endif>&lsaquo;</a>
            <span class="page-info">Page {{ $current }} of {{ $last }}</span>
            <a class="btn-page" href="{{ $current < $last ? $base . '?' . http_build_query(array_merge($q, ['page' => $current + 1])) : '#' }}" @if($current >= $last) disabled @endif>&rsaquo;</a>
            <a class="btn-page" href="{{ $current < $last ? $base . '?' . http_build_query(array_merge($q, ['page' => $last])) : '#' }}" @if($current >= $last) disabled @endif>&raquo;</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Side Panel Overlay -->
  <div class="panel-overlay" id="birthdayPanelOverlay" onclick="closeBirthdayPanel()"></div>

  <!-- Birthday Edit Side Panel -->
  <div class="side-panel" id="birthdaySidePanel">
    <div class="side-panel-header">
      <h4 id="birthdayPanelTitle">Edit Birthday Wish</h4>
      <div class="side-panel-actions">
        <button type="submit" form="birthdayForm" class="btn-save">Save</button>
        <button type="button" class="btn-cancel" onclick="closeBirthdayPanel()">Cancel</button>
      </div>
    </div>
    <form id="birthdayForm" method="POST" class="side-panel-body">
      @csrf
      <input type="hidden" name="_method" value="PUT">
      <input type="hidden" id="birthday_client_id" name="client_id">

      <div class="panel-form-row">
        <label>Name</label>
        <input type="text" id="birthday_name" class="form-control" readonly style="background:#f5f5f5;">
      </div>

      <div class="panel-form-row">
        <label>Date Of Birth</label>
        <input type="text" id="birthday_dob" class="form-control" readonly style="background:#f5f5f5;">
      </div>

      <div class="panel-form-row">
        <label>Age</label>
        <input type="text" id="birthday_age" class="form-control" readonly style="background:#f5f5f5;">
      </div>

      <div class="panel-form-row">
        <label>Client Status</label>
        <input type="text" id="birthday_status" class="form-control" readonly style="background:#f5f5f5;">
      </div>

      <div class="panel-form-row">
        <label>Mobile No</label>
        <input type="text" id="birthday_mobile" class="form-control" readonly style="background:#f5f5f5;">
      </div>

      <div class="panel-form-row">
        <label>Contact No</label>
        <input type="text" id="birthday_contact_no" class="form-control" readonly style="background:#f5f5f5;">
      </div>

      <div class="panel-form-row">
        <label>Home No</label>
        <input type="text" id="birthday_home_no" name="home_no" class="form-control">
      </div>

      <div class="panel-form-row">
        <label>Email Address</label>
        <input type="text" id="birthday_email" class="form-control" readonly style="background:#f5f5f5;">
      </div>

      <hr style="margin: 15px 0; border: none; border-top: 1px solid #ddd;">

      <div class="panel-form-row">
        <label for="bday_medium">Medium</label>
        <select id="bday_medium" name="bday_medium" class="form-control">
          <option value="">Select</option>
          <option value="Wattsapp">Wattsapp</option>
          <option value="SMS">SMS</option>
          <option value="Call">Call</option>
          <option value="Email">Email</option>
        </select>
      </div>

      <div class="panel-form-row">
        <label for="bday_wish_status">Wish Status</label>
        <select id="bday_wish_status" name="bday_wish_status" class="form-control">
          <option value="">Select</option>
          <option value="Not Done">Not Done</option>
          <option value="Done">Done</option>
          <option value="Sent">Sent</option>
          <option value="No Reply">No Reply</option>
        </select>
      </div>

      <div class="panel-form-row">
        <label for="bday_date_done">Date Done</label>
        <input type="date" id="bday_date_done" name="bday_date_done" class="form-control">
      </div>
    </form>
  </div>
</div>

<script>
  const csrfToken = '{{ csrf_token() }}';
  let currentBirthdayClientId = null;

  // Open birthday edit panel
  async function openBirthdayEditPanel(clientId) {
    try {
      const res = await fetch(`/clients/${clientId}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const client = await res.json();
      currentBirthdayClientId = clientId;
      populateBirthdayPanel(client);
    } catch (e) {
      console.error(e);
      alert('Error loading client data');
    }
  }

  // Populate panel with client data
  function populateBirthdayPanel(client) {
    const panel = document.getElementById('birthdaySidePanel');
    const overlay = document.getElementById('birthdayPanelOverlay');
    const form = document.getElementById('birthdayForm');

    // Set form action
    form.action = `/clients/${currentBirthdayClientId}/birthday-update`;

    // Populate readonly fields
    document.getElementById('birthday_client_id').value = client.id;
    document.getElementById('birthday_name').value = client.client_name || '';

    // Format date
    let dobStr = '-';
    let age = '-';
    if (client.dob_dor) {
      const dob = new Date(client.dob_dor);
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      dobStr = `${dob.getDate()}-${months[dob.getMonth()]}-${String(dob.getFullYear()).slice(-2)}`;
      age = Math.floor((new Date() - dob) / (365.25 * 24 * 60 * 60 * 1000));
    }
    document.getElementById('birthday_dob').value = dobStr;
    document.getElementById('birthday_age').value = age;

    document.getElementById('birthday_status').value = client.status === 'Inactive' ? 'Dormant' : (client.status || '');
    document.getElementById('birthday_mobile').value = client.mobile_no || '';
    document.getElementById('birthday_contact_no').value = client.alternate_no || '';
    document.getElementById('birthday_home_no').value = client.home_no || '';
    document.getElementById('birthday_email').value = client.email_address || '';

    // Populate editable fields
    document.getElementById('bday_medium').value = client.bday_medium || '';
    document.getElementById('bday_wish_status').value = client.bday_wish_status || '';

    // Format date_done
    if (client.bday_date_done) {
      const dd = new Date(client.bday_date_done);
      document.getElementById('bday_date_done').value = dd.toISOString().split('T')[0];
    } else {
      document.getElementById('bday_date_done').value = '';
    }

    // Show panel
    panel.classList.add('show');
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  // Close birthday panel
  function closeBirthdayPanel() {
    const panel = document.getElementById('birthdaySidePanel');
    const overlay = document.getElementById('birthdayPanelOverlay');

    panel.classList.remove('show');
    overlay.classList.remove('show');
    document.body.style.overflow = '';
    currentBirthdayClientId = null;
  }

  // Handle form submission
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('birthdayForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            closeBirthdayPanel();
            window.location.reload();
          } else {
            alert(data.message || 'Error saving');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          this.submit();
        });
      });
    }

    // Filter toggle
    const filterToggle = document.getElementById('filterToggle');
    if (filterToggle) {
      filterToggle.addEventListener('change', function() {
        const u = new URL(window.location.href);
        if (this.checked) {
          u.searchParams.delete('show_done');
        } else {
          u.searchParams.set('show_done', '1');
        }
        window.location.href = u.toString();
      });
    }

    // Close on escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeBirthdayPanel();
      }
    });
  });
</script>
@endsection
