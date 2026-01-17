@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/nominees-index.css') }}">


@php
  $config = \App\Helpers\TableConfigHelper::getConfig('nominees');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('nominees');
  // Ensure $selectedColumns is always an array
  if (!is_array($selectedColumns)) {
    $selectedColumns = $config['default_columns'] ?? [];
  }
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp



<div class="dashboard">
  <div class="container-table">
     <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3>
            @if($policy)
              {{ $policy->policy_no }} - 
            @endif
            
        <span class="client-name" style="color:#f3742a; font-size:20px; font-weight:500;"> Nominees</span>

          </h3>
       
      </div>
    </div>
    <!-- Nominees Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden; margin-bottom:15px;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
                  <div class="records-found">Records Found - {{ $nominees->total() }}</div>

        <div class="action-buttons">
          <button class="btn" onclick="removeSelectedNominees()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Remove</button>
          <button class="btn btn-add" onclick="openNomineeDialog()">Add</button>
          <a href="{{ $policy ? route('policies.index', ['policy_id' => $policy->id]) : route('policies.index') }}" class="btn" style="background:#6c757d; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; text-decoration:none; font-size:13px;">Back</a>
        </div>
      </div>

      @if(session('success') || request()->get('success'))
        <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
          {{ session('success') ?? request()->get('success') }}
          <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
        </div>
      @endif

      <div class="table-responsive" id="tableResponsive">
        <table id="nomineesTable">
          <thead>
            <tr>
            <th style="text-align:center;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle;">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 2 16 2 16H22C22 16 19 14.25 19 9C19 5.13 15.87 2 12 2Z" fill="#fff" stroke="#fff" stroke-width="1.5"/>
                <path d="M9 21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21H9Z" fill="#fff"/>
              </svg>
            </th>
              <th style="text-align:center; width:50px;">
                <input type="checkbox" class="nominee-checkbox" id="selectAllNominees" onchange="toggleAllNominees(this)">
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
            @forelse($nominees as $nominee)
              @php
                $age = $nominee->date_of_birth ? \Carbon\Carbon::parse($nominee->date_of_birth)->age : null;
              @endphp
              <tr>
                <td class="bell-cell {{ $nominee->date_removed ? 'expired' : '' }}">
                  <div style="display:flex; align-items:center; justify-content:center;">
                    <div class="status-indicator {{ $nominee->date_removed ? 'expired' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $nominee->date_removed ? '#000' : '#f3742a' }}; background-color:{{ $nominee->date_removed ? '#000' : 'transparent' }};"></div>
                  </div>
                </td>
                <td style="text-align:center;">
                  <input type="checkbox" name="selected_nominees[]" value="{{ $nominee->id }}" class="nominee-checkbox">
                </td>
                <td class="action-cell">
                  <svg class="action-expand" onclick="editNominee({{ $nominee->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                    <!-- Maximize icon: four arrows pointing outward from center -->
                    <!-- Top arrow -->
                    <path d="M12 2L12 8M12 2L10 4M12 2L14 4" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Right arrow -->
                    <path d="M22 12L16 12M22 12L20 10M22 12L20 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Bottom arrow -->
                    <path d="M12 22L12 16M12 22L10 20M12 22L14 20" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Left arrow -->
                    <path d="M2 12L8 12M2 12L4 10M2 12L4 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </td>
                @foreach($selectedColumns as $col)
                  @if($col == 'full_name')
                    <td data-column="full_name">{{ $nominee->full_name }}</td>
                  @elseif($col == 'date_of_birth')
                    <td data-column="date_of_birth">{{ $nominee->date_of_birth ? \Carbon\Carbon::parse($nominee->date_of_birth)->format('d-M-y') : '—' }}</td>
                  @elseif($col == 'age')
                    <td data-column="age">{{ $age ?? '—' }}</td>
                  @elseif($col == 'nin_passport_no')
                    <td data-column="nin_passport_no">{{ $nominee->nin_passport_no ?? '—' }}</td>
                  @elseif($col == 'relationship')
                    <td data-column="relationship">{{ $nominee->relationship ?? '—' }}</td>
                  @elseif($col == 'share_percentage')
                    <td data-column="share_percentage">{{ $nominee->share_percentage ? number_format($nominee->share_percentage, 2) . '%' : '—' }}</td>
                  @elseif($col == 'date_added')
                    <td data-column="date_added">{{ $nominee->created_at ? \Carbon\Carbon::parse($nominee->created_at)->format('d-M-y') : '—' }}</td>
                  @elseif($col == 'date_removed')
                    <td data-column="date_removed">{{ $nominee->date_removed ? \Carbon\Carbon::parse($nominee->date_removed)->format('d-M-y') : '—' }}</td>
                  @elseif($col == 'notes')
                    <td data-column="notes">{{ $nominee->notes ?? '—' }}</td>
                  @elseif($col == 'nominee_code')
                    <td data-column="nominee_code">{{ $nominee->nominee_code ?? '—' }}</td>
                  @endif
                @endforeach
              </tr>
            @empty
              <tr>
                <td colspan="{{ count($selectedColumns) + 2 }}" style="text-align:center; padding:20px; color:#999;">No nominees found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="footer">
        <div class="footer-left">
          <a class="btn btn-export" href="{{ route('nominees.export', $policyId ? ['policy_id' => $policyId] : []) }}">Export</a>
          <button class="btn btn-column" id="columnBtn" type="button">Column</button>
          <button class="btn btn-export" id="printBtn" type="button" style="margin-left:10px;">Print</button>
        </div>
        <div class="paginator">
          @php
            $base = url()->current();
            $q = request()->query();
            $current = $nominees->currentPage();
            $last = max(1, $nominees->lastPage());
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

    <!-- Documents Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden; margin-bottom:15px;">
      <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd;">
        <h4 style="margin:0; font-size:16px; font-weight:600;">Documents</h4>
        <button class="btn btn-add" onclick="openDocumentUpload()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Add Document</button>
      </div>
      <div id="documentsContent" style="display:flex; gap:10px; flex-wrap:wrap; padding:15px; min-height:100px;">
        <!-- Documents will be loaded here -->
        <div style="color:#999; font-size:12px;">No documents uploaded</div>
      </div>
    </div>
  </div>
</div>

<!-- Nominee Dialog Modal -->
<div class="modal" id="nomineeModal" style="display:none;" onclick="if(event.target === this) closeNomineeDialog();">
  <div class="modal-content" style="max-width:500px;" onclick="event.stopPropagation();">
    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center;">
      <h4 style="margin:0;" id="nomineeModalTitle">Add Nominee</h4>
      <div style="display:flex; gap:8px; align-items:center;">
        <button type="button" onclick="saveNominee()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Save</button>
        <button type="button" onclick="closeNomineeDialog()" style="background:#000; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px; font-weight:500;">Cancel</button>
      </div>
    </div>
      <form id="nomineeForm">
      <input type="hidden" name="nominee_id" id="nominee_id">
      <input type="hidden" name="policy_id" id="nominee_policy_id" value="{{ $policy->id ?? '' }}">
      <div class="modal-body" style="padding:20px;">
        <div class="form-group" style="margin-bottom:15px;">
          <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Full Name</label>
          <input type="text" name="full_name" id="nominee_full_name" class="form-control" required style="padding:6px; font-size:12px;">
        </div>
        <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
          <div class="form-group">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Date Of Birth</label>
            <input type="date" name="date_of_birth" id="nominee_date_of_birth" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div class="form-group">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">NIN/Passport No</label>
            <input type="text" name="nin_passport_no" id="nominee_nin_passport_no" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div class="form-group">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Relationship</label>
            <input type="text" name="relationship" id="nominee_relationship" class="form-control" style="padding:6px; font-size:12px;">
          </div>
          <div class="form-group">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Share</label>
            <input type="number" step="0.01" name="share_percentage" id="nominee_share_percentage" class="form-control" style="padding:6px; font-size:12px;" placeholder="%">
          </div>
          <div class="form-group">
            <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Date Removed</label>
            <input type="date" name="date_removed" id="nominee_date_removed" class="form-control" style="padding:6px; font-size:12px;">
          </div>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Notes</label>
          <textarea name="notes" id="nominee_notes" class="form-control" rows="3" style="padding:6px; font-size:12px;"></textarea>
        </div>
      </div>
      <div class="modal-footer" style="display:flex; gap:8px; justify-content:flex-end; padding:15px 20px; border-top:1px solid #ddd;">
        <button type="button" class="btn-save" onclick="saveNomineeAndAddAnother()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Upload ID</button>
        <button type="button" class="btn-save" onclick="saveNomineeAndAddAnother()" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Add Another</button>
      </div>
    </form>
  </div>
</div>



<!-- Column Selection Modal -->
<div class="modal" id="columnModal" style="display:none;" onclick="if(event.target === this) closeColumnModal();">
  <div class="modal-content column-modal-vertical" onclick="event.stopPropagation();">
    <div class="modal-header">
      <h4>Column Select & Sort</h4>
      <div class="modal-header-buttons">
        <button class="btn-save-orange" onclick="saveColumnSettings()">Save</button>
        <button class="btn-cancel-gray" onclick="closeColumnModal()">Cancel</button>
      </div>
    </div>
    <div class="modal-body">
      <form id="columnForm" action="{{ route('nominees.save-column-settings') }}" method="POST">
        @csrf
        <div class="column-selection-vertical" id="columnSelection">
          @php
            $all = $config['column_definitions'];
            // Maintain order based on selectedColumns
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

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

<!-- Document Upload Modal -->
<div class="modal" id="documentUploadModal" style="display:none;" onclick="if(event.target === this) closeDocumentUploadModal();">
  <div class="modal-content" style="max-width:500px;" onclick="event.stopPropagation();">
    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center;">
      <h4 style="margin:0;">Upload Document</h4>
      <button type="button" onclick="closeDocumentUploadModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#666;">×</button>
    </div>
    <form id="documentUploadForm" onsubmit="event.preventDefault(); uploadDocument();">
      <div class="modal-body" style="padding:20px;">
        <div class="form-group" style="margin-bottom:15px;">
          <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">Document Type</label>
          <select name="document_type" id="document_type" required style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:2px;">
            <option value="">Select Type</option>
            <option value="nominee_document">Nominee Document</option>
            <option value="id_document">ID Document</option>
            <option value="other">Other Document</option>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
          <label style="display:block; margin-bottom:5px; font-weight:600; font-size:12px;">File</label>
          <input type="file" name="document" id="document" required accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="width:100%; padding:6px; font-size:12px; border:1px solid #ddd; border-radius:2px;">
          <small style="color:#666; font-size:11px;">Max size: 5MB. Allowed: JPG, PNG, PDF, DOC, DOCX</small>
        </div>
      </div>
      <div class="modal-footer" style="display:flex; gap:8px; justify-content:flex-end; padding:15px 20px; border-top:1px solid #ddd;">
        <button type="button" onclick="closeDocumentUploadModal()" style="background:#6c757d; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Cancel</button>
        <button type="submit" style="background:#f3742a; color:#fff; border:none; padding:6px 20px; border-radius:2px; cursor:pointer; font-size:12px;">Upload</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Initialize data from Blade
  let currentNomineeId = null;

  const policyId = @json($policyId ?? null);
</script>
<script src="{{ asset('js/nominees-index.js') }}?v={{ time() }}"></script>
@endsection

