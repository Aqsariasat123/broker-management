@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/clients-show.css') }}">



<div class="dashboard">
  <div class="container-table">
    <h3>Client Details</h3>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="top-bar">
      <div class="action-buttons">
        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-edit">Edit</a>
        <a href="{{ route('policies.create') }}?client_id={{ $client->id }}" class="btn btn-add">Add Policy</a>
        <button class="btn btn-back" onclick="window.location.href='{{ route('clients.index') }}'">Back</button>
      </div>
    </div>

    <div class="info-section">
      <h4>Client Information</h4>
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Client ID</span>
          <span class="info-value">{{ $client->clid ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Client Name</span>
          <span class="info-value">{{ $client->client_name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Client Type</span>
          <span class="info-value">{{ $client->client_type ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Status</span>
          <span class="info-value">
            @php
              $status = $client->status ?? 'Active';
              $statusColor = $status === 'Active' ? '#28a745' : ($status === 'Inactive' ? '#6c757d' : '#ffc107');
            @endphp
            <span class="badge-status" style="background:{{ $statusColor }}">{{ $status }}</span>
          </span>
        </div>
        <div class="info-item">
          <span class="info-label">Mobile No</span>
          <span class="info-value">{{ $client->mobile_no ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Email Address</span>
          <span class="info-value">{{ $client->email_address ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">NIN/BCRN</span>
          <span class="info-value">{{ $client->nin_bcrn ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">DOB/DOR</span>
          <span class="info-value">{{ $client->dob_dor ? $client->dob_dor->format('d-M-y') : 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Signed Up</span>
          <span class="info-value">{{ $client->signed_up ? $client->signed_up->format('d-M-y') : 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Source</span>
          <span class="info-value">{{ $client->source ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">District</span>
          <span class="info-value">{{ $client->district ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Island</span>
          <span class="info-value">{{ $client->island ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Country</span>
          <span class="info-value">{{ $client->country ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Occupation</span>
          <span class="info-value">{{ $client->occupation ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Employer</span>
          <span class="info-value">{{ $client->employer ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Location</span>
          <span class="info-value">{{ $client->location ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">PEP</span>
          <span class="info-value">{{ $client->pep ? 'Yes' : 'No' }}</span>
        </div>
      </div>
    </div>

    <div class="info-section">
      <h4>Associated Policies</h4>
      <p style="font-size:12px; color:#555; margin-bottom:10px;">All policies associated with this client.</p>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Policy No</th>
              <th>Policy Code</th>
              <th>Insurer</th>
              <th>Policy Class</th>
              <th>Policy Plan</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Status</th>
              <th>Premium</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($client->policies as $policy)
              <tr>
                <td>{{ $policy->policy_no ?? 'N/A' }}</td>
                <td>{{ $policy->policy_code ?? 'N/A' }}</td>
                <td>{{ $policy->insurer->name ?? 'N/A' }}</td>
                <td>{{ $policy->policyClass->name ?? 'N/A' }}</td>
                <td>{{ $policy->policyPlan->name ?? 'N/A' }}</td>
                <td>{{ $policy->start_date ? $policy->start_date->format('d-M-y') : 'N/A' }}</td>
                <td>{{ $policy->end_date ? $policy->end_date->format('d-M-y') : 'N/A' }}</td>
                <td>
                  @php
                    $statusName = $policy->policyStatus->name ?? 'N/A';
                    $statusColor = '#6c757d';
                    if (stripos($statusName, 'In Force') !== false) $statusColor = '#28a745';
                    elseif (stripos($statusName, 'DFR') !== false || stripos($statusName, 'Due') !== false) $statusColor = '#ffc107';
                    elseif (stripos($statusName, 'Expired') !== false) $statusColor = '#6c757d';
                    elseif (stripos($statusName, 'Cancelled') !== false) $statusColor = '#dc3545';
                  @endphp
                  <span class="badge-status" style="background:{{ $statusColor }}">{{ $statusName }}</span>
                </td>
                <td>{{ number_format($policy->premium ?? 0, 2) }}</td>
                <td>
                  <a href="{{ route('policies.show', $policy->id) }}" class="btn-action">View</a>
                  <a href="{{ route('policies.edit', $policy->id) }}" class="btn-action">Edit</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="empty-state">No policies associated with this client.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

