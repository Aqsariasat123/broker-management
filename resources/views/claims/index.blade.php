@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/claims-index.css') }}">




@php
  $config = \App\Helpers\TableConfigHelper::getConfig('claims');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('claims');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Claims Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
         
            
            
              @if($policy)
                {{ $policy->policy_code }} - 
              @endif
              
              @if($policy)
                 <span class="client-name" style="color:#f3742a; font-size:20px; font-weight:500;"> Claims</span>
              @else
                 <span class="client-name" > Claims</span>
              @endif
            @if(isset($client) && $client)
              <span class="client-name" style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $client->client_name }}</span>
            @endif
            <span id="followUpLabel" style="display:{{ request()->get('pending') == 1 ? 'inline' : 'none' }}; color:#f3742a; font-size:16px; font-weight:500;"> - Pending </span>

          </h3>
       
      </div>
    </div>
  <div class="container-table">
    <!-- Claims Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
                <div class="records-found">Records Found - {{ $claims->total() }}</div>

      <div class="page-title-section">
        <div style="display:flex; align-items:center; gap:15px;">
          <div class="filter-group" style="display:flex; align-items:center; gap:10px;">
            <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
              <span style="font-size:13px;">Filter</span>
              @php
              
                $hasPending = request()->has('pending') && (request()->pending == 'true' || request()->pending == '1');
              @endphp
              <input type="checkbox" id="filterToggle" {{ $hasPending ? 'checked' : '' }}>
            </label>
            @if($hasPending)
              <button class="btn" id="listAllBtn" type="button" style="background:#28a745; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">List ALL</button>
            @else
              <button class="btn" id="showPendingBtn" type="button" style="background:#000; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Show Pending</button>
            @endif
          </div>
        </div>
      </div>
           <!-- @if(isset($client) && $client)
      <div class="action-buttons">
        <button class="btn btn-add" id="addDocumentBtn">Add</button>
      </div>
      @endif -->
      @if(isset($policy) && $policy)
        <div class="action-buttons">
          <button class="btn btn-add" id="addClaimBtn">Add</button>
        </div>
      @endif
        <div class="action-buttons">
          <button class="btn btn-close" onclick="window.history.back()">Close</button>
        </div>

    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="claimsTable">
        <thead>
          <tr>
            <th style="text-align:center;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle;">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 2 16 2 16H22C22 16 19 14.25 19 9C19 5.13 15.87 2 12 2Z" fill="#fff" stroke="#fff" stroke-width="1.5"/>
                <path d="M9 21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21H9Z" fill="#fff"/>
              </svg>
            </th>
            <th>Action</th>
            @foreach($selectedColumns as $col)
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($claims as $index => $clm)
            <tr class="{{ $clm->hasExpired ?? false ? 'expired' : ($clm->hasExpiring ?? false ? 'expiring' : '') }}">
             
              <td class="bell-cell {{ $client->hasExpired ?? false ? 'expired' : ($client->hasExpiring ?? false ? 'expiring' : '') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  @php
                    $isExpired = $clm->hasExpired ?? false;
                    $isExpiring = $clm->hasExpiring ?? false;
                  @endphp
                  <div class="status-indicator {{ $isExpired ? 'expired' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid #000; background-color:{{ $isExpired ? '#dc3545' : 'transparent' }};"></div>
                </div>
              </td>
              <td class="action-cell">
              <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openClaimModal('edit', {{ $clm->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">

              
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'claim_id')
                  <td data-column="claim_id">
                   {{ $clm->claim_id }}
                  </td>
                @elseif($col == 'policy_no')
                  <td data-column="policy_no">{{ $clm->policy ? $clm->policy->policy_no : ($clm->policy_no ?? '-') }}</td>
                @elseif($col == 'client_name')
                  <td data-column="client_name">{{ $clm->client ? $clm->client->client_name : '-' }}</td>
                @elseif($col == 'loss_date')
                  <td data-column="loss_date">{{ $clm->loss_date ? \Carbon\Carbon::parse($clm->loss_date)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'claim_date')
                  <td data-column="claim_date">{{ $clm->claim_date ? \Carbon\Carbon::parse($clm->claim_date)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'claim_amount')
                  <td data-column="claim_amount">{{ $clm->claim_amount ? number_format($clm->claim_amount, 2) : '-' }}</td>
                @elseif($col == 'claim_summary')
                  <td data-column="claim_summary">{{ $clm->claim_summary ?? '-' }}</td>
                @elseif($col == 'status')
                  <td data-column="status">{{ $clm->status ?? '-' }}</td>
                @elseif($col == 'close_date')
                  <td data-column="close_date">{{ $clm->close_date ? \Carbon\Carbon::parse($clm->close_date)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'paid_amount')
                  <td data-column="paid_amount">{{ $clm->paid_amount ? number_format($clm->paid_amount, 2) : '-' }}</td>
                @elseif($col == 'settlment_notes')
                  <td data-column="settlment_notes">{{ $clm->settlment_notes ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('claims.export', array_merge(request()->query(), ['page' => $claims->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $claims->currentPage();
          $last = max(1, $claims->lastPage());
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

  <!-- Add/Edit Claim Modal -->
  <div class="modal" id="claimModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="claimModalTitle">View/Edit Claim</h4>
        <div style="display:flex; gap:10px;">
          <button type="submit" form="claimForm" class="btn-save">Save</button>
          <button type="button" class="btn-cancel" onclick="closeClaimModal()">Cancel</button>
        </div>
      </div>
      <form id="claimForm" method="POST" action="{{ route('claims.store') }}">
        @csrf
        <div id="claimFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="policy_id">Policy Number</label>
              <select class="form-control" name="policy_id" id="policy_id" required>
                <option value="">Select Policy</option>
                @if(isset($policies))
                  @foreach($policies as $policy)
                    <option value="{{ $policy->id }}">{{ $policy->policy_no }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="form-group">
              <label for="loss_date">Loss Date</label>
              <input type="date" class="form-control" name="loss_date" id="loss_date">
            </div>
          </div>
     
          <div class="form-row">
            <div class="form-group">
              <label for="claim_date">Claim Date</label>
              <input type="date" class="form-control" name="claim_date" id="claim_date">
            </div>
            <div class="form-group">
              <label for="claim_amount">Claim Amount</label>
              <input type="number" step="0.01" class="form-control" name="claim_amount" id="claim_amount">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="claim_stage">Claim Stage</label>
              <select class="form-control" name="claim_stage" id="claim_stage">
                <option value="">Select</option>
                @if(isset($lookupData['claim_stages']))
                  @foreach($lookupData['claim_stages'] as $stage)
                    <option value="{{ $stage }}">{{ $stage }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="form-group">
              <label for="status">Claim Status</label>
              <select class="form-control" name="status" id="status">
                <option value="">Select</option>
                @if(isset($lookupData['claim_statuses']))
                  @foreach($lookupData['claim_statuses'] as $claimStatus)
                    <option value="{{ $claimStatus }}">{{ $claimStatus }}</option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="close_date">Date Closed</label>
              <input type="date" class="form-control" name="close_date" id="close_date">
            </div>
            <div class="form-group">
              <label for="paid_amount">Paid Amount</label>
              <input type="number" step="0.01" class="form-control" name="paid_amount" id="paid_amount">
            </div>
          </div>
          <div class="form-row full-width">
            <div class="form-group">
              <label for="claim_summary">Claim Summary</label>
              <textarea class="form-control" name="claim_summary" id="claim_summary" rows="4"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-upload" id="claimUploadBtnModal" style="display: none;" onclick="openDocumentUploadModal()">Upload Document</button>
          <button type="button" class="btn-delete" id="claimDeleteBtnModal" style="display: none;" onclick="deleteClaim()">Delete</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Document Upload Modal -->
  <div class="modal" id="claimDocumentUploadModal">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-header">
        <h4>Upload Document</h4>
        <button type="button" class="modal-close" onclick="closeDocumentUploadModal()">×</button>
      </div>
      <form id="claimDocumentUploadForm" onsubmit="uploadDocument(event)">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label for="documentType">Document Type</label>
            <select class="form-control" name="document_type" id="documentType" required>
              <option value="">Select Document Type</option>
              <option value="claim_form">Claim Form</option>
              <option value="supporting_document">Supporting Document</option>
              <option value="medical_report">Medical Report</option>
              <option value="police_report">Police Report</option>
              <option value="estimate">Estimate</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="documentFile">Select File</label>
            <input type="file" class="form-control" name="document" id="documentFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
            <small style="color:#666; font-size:11px;">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max 5MB)</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeDocumentUploadModal()">Cancel</button>
          <button type="submit" class="btn-save">Upload</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Column Selection Modal -->
  <div class="modal" id="columnModal">
    <div class="modal-content column-modal-vertical">
      <div class="modal-header">
        <h4>Column Select & Sort</h4>
        <div class="modal-header-buttons">
          <button class="btn-save-orange" onclick="saveColumnSettings()">Save</button>
          <button class="btn-cancel-gray" onclick="closeColumnModal()">Cancel</button>
        </div>
      </div>
      <div class="modal-body">
        <form id="columnForm" action="{{ route('claims.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection-vertical" id="columnSelection">
            @php
              $all = $config['column_definitions'];
              $ordered = [];
              foreach($selectedColumns as $col) {
                if(isset($all[$col])) {
                  $ordered[$col] = $all[$col];
                  unset($all[$col]);
                }
              }
              $ordered = array_merge($ordered, $all);
              $counter = 1;
            @endphp

            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryColumns);
                $isChecked = in_array($key, $selectedColumns) || $isMandatory;
              @endphp
              <div class="column-item-vertical" draggable="true" data-column="{{ $key }}">
                <span class="column-number">{{ $counter }}</span>
                <label class="column-label-wrapper">
                  <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
                  <span class="column-label-text">{{ $label }}</span>
                </label>
              </div>
              @php $counter++; @endphp
            @endforeach
          </div>
          <div class="column-drag-hint">Drag and Select to position and display</div>
        </form>
      </div>
    </div>
  </div>

</div>



<script>
  // Initialize data from Blade - must be before partials-table-scripts
  // Note: mandatoryColumns is already declared in partials-table-scripts
  let currentClaimId = null;
  const selectedColumns = @json($selectedColumns);
  const claimsStoreRoute = '{{ route("claims.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/claims-index.js') }}"></script>
@endsection
