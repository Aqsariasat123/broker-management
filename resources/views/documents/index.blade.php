@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('documents');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('documents');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Documents Table View -->
  <div class="clients-table-view" id="clientsTableView">
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
         
            
            @if(isset($client) && $client)
              <span class="client-name" style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $client->client_name }}</span>
            @endif

              @if($policy)
                {{ $policy->policy_code }} - 
              @endif
              
              @if($policy)
                 <span class="client-name" style="color:#f3742a; font-size:20px; font-weight:500;"> Documents</span>
              @else
                 <span class="client-name" > Documents</span>
              @endif
          </h3>
       
      </div>
  </div> 
  <div class="container-table">
    <!-- Documents Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
                <div class="records-found">Records Found - {{ $documents->total() }}</div>

      <div class="page-title-section">
      </div>
      
       @if(isset($client) && $client)
      <div class="action-buttons">
        <button class="btn btn-add" id="addDocumentBtn">Add</button>
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
      <table id="documentsTable">
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
          @foreach($documents as $doc)
            <tr>
              <td class="bell-cell">
                <div style="display:flex; align-items:center; justify-content:center;">
                  <div class="status-indicator normal" style="width:18px; height:18px; border-radius:50%; border:2px solid #000; background-color:transparent;"></div>
                </div>
              </td>
              <td class="action-cell">
              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" style="color:#007bff; text-decoration:underline;"> 
                <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand"></a>

              <!-- <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand"
              onclick="openDocumentDetails({{ $doc->id }})"  width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand"> -->

                <svg class="action-delete" onclick="if(confirm('Delete this document?')) { deleteDocumentFromTable({{ $doc->id }}); }" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <!-- Trash icon -->
                  <path d="M3 6H5H21" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M10 11V17" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M14 11V17" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'doc_id')
                  <td data-column="doc_id">
                  {{ $doc->doc_id }}
                  </td>
                @elseif($col == 'tied_to')
                  <td data-column="tied_to">{{ $doc->tied_to ?? '-' }}</td>
                @elseif($col == 'name')
                  <td data-column="name">{{ $doc->name ?? '-' }}</td>
                @elseif($col == 'group')
                  <td data-column="group">{{ $doc->group ?? '-' }}</td>
                @elseif($col == 'type')
                  <td data-column="type">{{ $doc->type ?? '-' }}</td>
                @elseif($col == 'format')
                  <td data-column="format">{{ $doc->format ?? '-' }}</td>
                @elseif($col == 'date_added')
                  <td data-column="date_added">{{ $doc->date_added ? \Carbon\Carbon::parse($doc->date_added)->format('d-M-y') : '-' }}</td>
                @elseif($col == 'year')
                  <td data-column="year">{{ $doc->year ?? '-' }}</td>
                @elseif($col == 'file_path')
                  <td data-column="file_path">
                    @if($doc->file_path)
                      <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" style="color:#007bff; text-decoration:underline;">View</a>
                    @else
                      -
                    @endif
                  </td>
                @elseif($col == 'notes')
                  <td data-column="notes">{{ $doc->notes ?? '-' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
      <div class="footer-left" style="display:flex; gap:8px;">
        <a class="btn btn-export" href="{{ route('documents.export') }}" style="background:#fff; border:1px solid #ddd; padding:6px 16px; border-radius:2px; cursor:pointer; text-decoration:none; color:#333;">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button" style="background:#fff; border:1px solid #ddd; padding:6px 16px; border-radius:2px; cursor:pointer;">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $documents->currentPage();
          $last = max(1, $documents->lastPage());
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

  <!-- Document Page View (Full Page) -->
  <div class="client-page-view" id="documentPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="documentPageTitle">Document</span> - <span class="client-name" id="documentPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editDocumentFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeDocumentPageBtn" onclick="closeDocumentPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Document Details View -->
        <div id="documentDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="documentDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Document Edit/Add Form -->
        <div id="documentFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="documentDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteDocument()">Delete</button>
                <button type="submit" form="documentPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeDocumentFormBtn" onclick="closeDocumentPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="documentPageForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
              @csrf
              <div id="documentPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Document Details Modal -->
  <div class="modal" id="documentDetailsModal">
    <div class="modal-content" style="max-width:800px;">
      <div class="modal-header">
        <h4 id="documentDetailsModalTitle">Document Details</h4>
        <button type="button" class="modal-close" onclick="closeDocumentDetailsModal()">×</button>
      </div>
      <div class="modal-body" style="padding:20px;">
        <div id="documentDetailsContent" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:15px;">
          <!-- Content will be loaded via JavaScript -->
        </div>
      </div>
      <div class="modal-footer" style="padding:15px 20px; border-top:1px solid #ddd; background:#fff; display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn-edit" id="editDocumentFromDetailsBtn" onclick="openDocumentModal('edit', currentDocumentId)" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px; display:none;">Edit</button>
        <button type="button" class="btn-cancel" onclick="closeDocumentDetailsModal()" style="background:#000; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Close</button>
      </div>
    </div>
  </div>

  <!-- Add/Edit Document Modal -->
  <div class="modal" id="documentModal">
    <div class="modal-content" style="max-width:800px;">
      <div class="modal-header">
        <h4 id="documentModalTitle">Add Document</h4>
        <button type="button" class="modal-close" onclick="closeDocumentModal()">×</button>
      </div>
      <form id="documentForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="documentFormMethod" style="display:none;"></div>
        <div class="modal-body" style="padding:20px;">
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="tied_to" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Tied To</label>
              <input type="text" class="form-control" name="tied_to" id="tied_to" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="name" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Name *</label>
              <input type="text" class="form-control" name="name" id="name" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="group" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Group</label>
              <input type="text" class="form-control" name="group" id="group" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="type" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Type</label>
              <input type="text" class="form-control" name="type" id="type" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="date_added" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Date Added</label>
              <input type="date" class="form-control" name="date_added" id="date_added" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="year" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Year</label>
              <input type="text" class="form-control" name="year" id="year" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="file" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">File</label>
              <input type="file" class="form-control" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="notes" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; resize:vertical;"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding:15px 20px; border-top:1px solid #ddd; background:#fff; display:flex; justify-content:center; gap:10px;">
          <button type="button" class="btn-cancel" onclick="closeDocumentModal()" style="background:#000; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Cancel</button>
          <button type="button" class="btn-delete" id="documentDeleteBtnModal" style="display: none; background:#dc3545; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;" onclick="deleteDocument()">Delete</button>
          <button type="submit" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Save</button>
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
        <form id="columnForm" action="{{ route('documents.save-column-settings') }}" method="POST">
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



@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

<script>
  // Initialize data from Blade
  // Note: mandatoryColumns is already declared in partials-table-scripts
  let currentDocumentId = null;
  const selectedColumns = @json($selectedColumns);
    const client = @json($client);
  const documentsStoreRoute = '{{ route("documents.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/documents-index.js') }}"></script>
@endsection
