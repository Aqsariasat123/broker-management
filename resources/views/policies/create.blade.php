@extends('layouts.app')
@section('content')

<link rel="stylesheet" href="{{ asset('css/policies-index.css') }}?v={{ time() }}">

<style>
.form-container { background:#fff; padding:20px; }
.form-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:15px; border-bottom:1px solid #ddd; }
.form-header h2 { margin:0; font-size:18px; font-weight:600; }
.form-header-buttons { display:flex; gap:10px; }
.btn-save { background:#f3742a; color:#fff; border:none; padding:8px 24px; border-radius:4px; cursor:pointer; font-size:14px; }
.btn-cancel { background:#ccc; color:#000; border:none; padding:8px 24px; border-radius:4px; cursor:pointer; font-size:14px; }
.form-row { display:grid; grid-template-columns:repeat(5, 1fr); gap:15px; margin-bottom:15px; }
.form-row-4 { display:grid; grid-template-columns:repeat(4, 1fr); gap:15px; margin-bottom:15px; }
.form-row-6 { display:grid; grid-template-columns:repeat(6, 1fr); gap:15px; margin-bottom:15px; }
.form-group { display:flex; flex-direction:column; }
.form-group label { font-size:12px; color:#333; margin-bottom:4px; font-weight:500; }
.form-group input, .form-group select, .form-group textarea { padding:8px 10px; border:1px solid #ccc; border-radius:3px; font-size:13px; background:#e8e8e8; }
.form-group input:focus, .form-group select:focus { outline:none; border-color:#f3742a; background:#fff; }
.section-title { font-weight:600; font-size:14px; margin:20px 0 10px 0; color:#333; }
.checkbox-row { display:flex; align-items:center; gap:10px; margin:15px 0; }
.checkbox-row input[type="checkbox"] { width:20px; height:20px; accent-color:#f3742a; }
.checkbox-row label { font-size:13px; }
.documents-section { margin-top:20px; padding-top:15px; border-top:1px solid #ddd; }
.documents-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
.documents-header h4 { margin:0; font-size:14px; font-weight:600; }
.btn-upload { background:#f3742a; color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-size:13px; }
</style>

<div class="dashboard">
  <div class="form-container">
    <form action="{{ route('policies.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <!-- Header -->
      <div class="form-header">
        <h2>Policy - Add New</h2>
        <div class="form-header-buttons">
          <button type="submit" class="btn-save">Save</button>
          <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
        </div>
      </div>

      @if($errors->any())
        <div style="background:#fee; border:1px solid #fcc; color:#c33; padding:10px; margin-bottom:15px; border-radius:4px;">
          <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Row 1: Policy Number, Clients Name, Insurance Class, Insurer, Insured Asset -->
      <div class="form-row">
        <div class="form-group">
          <label>Policy Number</label>
          <input type="text" name="policy_no" value="{{ old('policy_no') }}" required>
        </div>
        <div class="form-group">
          <label>Clients Name</label>
          <select name="client_id" required>
            <option value="">Select Client</option>
            @foreach(\App\Models\Client::orderBy('client_name')->get() as $client)
              <option value="{{ $client->id }}" {{ (old('client_id') == $client->id || (isset($selectedClientId) && $selectedClientId == $client->id)) ? 'selected' : '' }}>
                {{ $client->client_name ?? $client->first_name . ' ' . $client->surname }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Insurance Class</label>
          <select name="policy_class_id">
            <option value="">Select</option>
            @foreach($lookupData['policy_classes'] ?? [] as $class)
              <option value="{{ $class['id'] }}" {{ old('policy_class_id') == $class['id'] ? 'selected' : '' }}>{{ $class['name'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Insurer</label>
          <select name="insurer_id">
            <option value="">Select</option>
            @foreach($lookupData['insurers'] ?? [] as $insurer)
              <option value="{{ $insurer['id'] }}" {{ old('insurer_id') == $insurer['id'] ? 'selected' : '' }}>{{ $insurer['name'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Insured Asset / Destination</label>
          <input type="text" name="insured_item" value="{{ old('insured_item') }}">
        </div>
      </div>

      <!-- Row 2: Application Date, Business Type, Agency, Agent, Source -->
      <div class="form-row">
        <div class="form-group">
          <label>Application Date</label>
          <input type="date" name="date_registered" value="{{ old('date_registered', date('Y-m-d')) }}" required>
        </div>
        <div class="form-group">
          <label>Business Type</label>
          <select name="business_type_id">
            <option value="">Select</option>
            @foreach($lookupData['business_types'] ?? [] as $type)
              <option value="{{ $type['id'] }}" {{ old('business_type_id') == $type['id'] ? 'selected' : '' }}>{{ $type['name'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Agency</label>
          <select name="agency_id">
            <option value="">Select</option>
            @foreach($lookupData['agencies'] ?? [] as $agency)
              <option value="{{ $agency['id'] }}" {{ old('agency_id') == $agency['id'] ? 'selected' : '' }}>{{ $agency['name'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Agent</label>
          <select name="agent_id">
            <option value="">Select</option>
            @foreach($lookupData['agents'] ?? [] as $agent)
              <option value="{{ $agent['id'] }}" {{ old('agent_id') == $agent['id'] ? 'selected' : '' }}>{{ $agent['name'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Source</label>
          <select name="source_id">
            <option value="">Select</option>
            @foreach($lookupData['sources'] ?? [] as $source)
              <option value="{{ $source['id'] }}" {{ old('source_id') == $source['id'] ? 'selected' : '' }}>{{ $source['name'] }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <!-- Row 3: Source Name, Delivery Channel, Notes -->
      <div class="form-row" style="grid-template-columns: 1fr 1fr 2fr;">
        <div class="form-group">
          <label>Source Name</label>
          <input type="text" name="source_name" value="{{ old('source_name') }}">
        </div>
        <div class="form-group">
          <label>Delivery Channel</label>
          <select name="delivery_channel_id">
            <option value="">Select</option>
            @foreach($lookupData['delivery_channels'] ?? [] as $channel)
              <option value="{{ $channel['id'] }}" {{ old('delivery_channel_id') == $channel['id'] ? 'selected' : '' }}>{{ $channel['name'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Notes</label>
          <input type="text" name="notes" value="{{ old('notes') }}">
        </div>
      </div>

      <!-- Renewal Notices Required -->
      <div class="checkbox-row">
        <label>Renewal Notices Required?</label>
        <input type="checkbox" name="renewable" value="1" {{ old('renewable') ? 'checked' : 'checked' }}>
      </div>

      <!-- Schedule Details -->
      <div class="section-title">Schedule Details</div>
      <div class="form-row-6">
        <div class="form-group">
          <label>Year</label>
          <input type="text" name="year" value="{{ old('year', date('Y')) }}">
        </div>
        <div class="form-group">
          <label>Plan</label>
          <select name="policy_plan_id">
            <option value="">Select</option>
            @foreach($lookupData['policy_plans'] ?? [] as $plan)
              <option value="{{ $plan['id'] }}" {{ old('policy_plan_id') == $plan['id'] ? 'selected' : '' }}>{{ $plan['name'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Sum Insured</label>
          <input type="number" step="0.01" name="sum_insured" value="{{ old('sum_insured') }}">
        </div>
        <div class="form-group">
          <label>Term</label>
          <input type="text" name="term" value="{{ old('term') }}">
        </div>
        <div class="form-group">
          <label>Period</label>
          <input type="text" name="period" value="{{ old('period') }}">
        </div>
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date" value="{{ old('start_date') }}" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>WSC</label>
          <input type="number" step="0.01" name="wsc" value="{{ old('wsc') }}">
        </div>
        <div class="form-group">
          <label>LOU</label>
          <input type="number" step="0.01" name="lou" value="{{ old('lou') }}">
        </div>
        <div class="form-group">
          <label>PA</label>
          <input type="number" step="0.01" name="pa" value="{{ old('pa') }}">
        </div>
        <div class="form-group">
          <label>Base Premium</label>
          <input type="number" step="0.01" name="base_premium" value="{{ old('base_premium') }}">
        </div>
        <div class="form-group">
          <label>Total Premium</label>
          <input type="number" step="0.01" name="premium" value="{{ old('premium') }}">
        </div>
      </div>

      <!-- Hidden End Date (calculated or entered) -->
      <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date') }}">

      <!-- Payment Plan -->
      <div class="section-title">Payment Plan</div>
      <div class="form-row">
        <div class="form-group">
          <label>Option</label>
          <select name="payment_option">
            <option value="">Select</option>
            <option value="Full" {{ old('payment_option') == 'Full' ? 'selected' : '' }}>Full</option>
            <option value="Instalments" {{ old('payment_option') == 'Instalments' ? 'selected' : '' }}>Instalments</option>
          </select>
        </div>
        <div class="form-group">
          <label>No Of Instalments</label>
          <input type="number" name="no_of_instalments" value="{{ old('no_of_instalments') }}">
        </div>
        <div class="form-group">
          <label>Interval</label>
          <select name="instalment_interval">
            <option value="">Select</option>
            <option value="Monthly" {{ old('instalment_interval') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
            <option value="Quarterly" {{ old('instalment_interval') == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
            <option value="Yearly" {{ old('instalment_interval') == 'Yearly' ? 'selected' : '' }}>Yearly</option>
          </select>
        </div>
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="payment_start_date" value="{{ old('payment_start_date') }}">
        </div>
        <div class="form-group">
          <label>End Date</label>
          <input type="date" name="payment_end_date" value="{{ old('payment_end_date') }}">
        </div>
      </div>

      <!-- Documents Section -->
      <div class="documents-section">
        <div class="documents-header">
          <h4>Documents</h4>
          <button type="button" class="btn-upload" onclick="document.getElementById('documentInput').click()">Upload Document</button>
          <input type="file" id="documentInput" name="documents[]" multiple style="display:none;">
        </div>
        <div id="documentsList" style="min-height:50px; padding:10px; background:#f9f9f9; border-radius:4px;">
          <!-- Documents will be listed here -->
        </div>
      </div>

    </form>
  </div>
</div>

<script>
// Calculate end date based on start date and term
document.querySelector('input[name="start_date"]').addEventListener('change', function() {
  const startDate = new Date(this.value);
  if (startDate) {
    const endDate = new Date(startDate);
    endDate.setFullYear(endDate.getFullYear() + 1);
    endDate.setDate(endDate.getDate() - 1);
    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
  }
});

// Show selected files
document.getElementById('documentInput').addEventListener('change', function() {
  const list = document.getElementById('documentsList');
  list.innerHTML = '';
  for (let file of this.files) {
    const div = document.createElement('div');
    div.style.cssText = 'padding:5px; background:#fff; margin:5px 0; border-radius:3px; border:1px solid #ddd;';
    div.textContent = file.name;
    list.appendChild(div);
  }
});
</script>

@endsection
