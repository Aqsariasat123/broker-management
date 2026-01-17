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
  <!-- Main Documents Header with Client Name -->
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; margin-bottom:15px; padding:15px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h3 style="margin:0; font-size:18px; font-weight:600;">
        Documents
        @if(isset($client) && $client)
          <span style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $client->client_name }}</span>
        @endif
        @if($policy)
          <span style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $policy->policy_code }}</span>
        @endif
      </h3>
      @include('partials.page-header-right')
    </div>
  </div> 
  
  <div class="clients-table-view" id="clientsTableView">
    <div class="container-table">
      <!-- Documents Card -->
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
        <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
          <div class="records-found">Records Found - {{ $documents->total() }}</div>

          <div class="page-title-section">
          </div>
        
          @if(isset($client) && $client)
            <div class="action-buttons">
              <button class="btn btn-add" id="addDocumentBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Add</button>
            </div>
          @endif 
          
          <div class="action-buttons">
            @if(isset($client) && $client)
              {{-- Smart back button - goes to client page and opens client details --}}
              <button class="btn btn-close" onclick="goBackToClient({{ $client->id }}, '{{ addslashes($client->client_name) }}')" style="background:#000; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Back</button>
            @elseif(isset($policy) && $policy)
              {{-- Back to policy page --}}
              <button class="btn btn-close" onclick="window.location.href='{{ route('policies.show', $policy->id) }}'" style="background:#000; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Back</button>
            @else
              {{-- Regular back button for non-client/non-policy contexts --}}
              <button class="btn btn-close" onclick="window.history.back()" style="background:#000; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Back</button>
            @endif
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
                  <td class="bell-cell {{ $doc->status === 'Expired' ? 'expired' : '' }}">
                    <div style="display:flex; align-items:center; justify-content:center;">
                      @php
                        $isExpired = isset($doc->status) && $doc->status === 'Expired';
                        $isExpiring = false; // Add your expiring logic here if needed
                        
                        // Check if ID card is expired via expiry_date
                        if (isset($doc->expiry_date) && $doc->expiry_date) {
                          $expiryDate = \Carbon\Carbon::parse($doc->expiry_date);
                          $isExpired = $expiryDate->isPast();
                          $isExpiring = $expiryDate->isFuture() && $expiryDate->diffInDays(now()) <= 30;
                        }
                      @endphp
                      <div class="status-indicator {{ $isExpired ? 'expired' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid #000; background-color:{{ $isExpired ? '#dc3545' : 'transparent' }};"></div>
                    </div>
                  </td>
                  <td class="action-cell">
                    {{-- View document button - goes to document viewer --}}
                    @php
                      $viewerUrl = route('documents.viewer', $doc->id);
                      // If we have client context, add it to URL
                      if(isset($client) && $client) {
                        $viewerUrl .= '?client_id=' . $client->id;
                      }
                    @endphp
                    <a href="{{ $viewerUrl }}" style="color:#007bff; text-decoration:none;"> 
                      <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="View" title="View Document">
                    </a>

                    {{-- Delete document button --}}
                    <svg class="action-delete" onclick="if(confirm('Delete this document?')) { deleteDocumentFromTable({{ $doc->id }}); }" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle; margin-left:8px;" title="Delete Document">
                      <path d="M3 6H5H21" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M10 11V17" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 11V17" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </td>
                  @foreach($selectedColumns as $col)
                    @if($col == 'doc_id')
                      <td data-column="doc_id">{{ $doc->doc_id }}</td>
                    @elseif($col == 'tied_to')
                      <td data-column="tied_to">{{ $doc->tied_to ?? '-' }}</td>
                    @elseif($col == 'name')
                      <td data-column="name">{{ $doc->name ?? '-' }}</td>
                    @elseif($col == 'group')
                      <td data-column="group">{{ $doc->group ?? '-' }}</td>
                    @elseif($col == 'type')
                      <td data-column="type">{{ $doc->type ?? '-' }}</td>
                    @elseif($col == 'format')
                      <td data-column="format">{{ strtoupper($doc->format ?? '-') }}</td>
                    @elseif($col == 'date_added')
                      <td data-column="date_added">{{ $doc->date_added ? \Carbon\Carbon::parse($doc->date_added)->format('d-M-y') : '-' }}</td>
                    @elseif($col == 'expiry_date')
                      <td data-column="expiry_date">{{ $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('d-M-y') : '-' }}</td>
                    @elseif($col == 'status')
                      <td data-column="status">
                        @if(isset($doc->status))
                          <span style="padding:2px 8px; border-radius:2px; font-size:11px; font-weight:600; 
                            {{ $doc->status === 'Expired' ? 'background:#ffc107; color:#000;' : 'background:#28a745; color:#fff;' }}">
                            {{ $doc->status }}
                          </span>
                        @else
                          -
                        @endif
                      </td>
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

  <!-- Modals remain the same... -->
  
</div>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

<script>
  let currentDocumentId = null;
  const selectedColumns = @json($selectedColumns);
  const client = @json($client);
  const documentsStoreRoute = '{{ route("documents.store") }}';
  const csrfToken = '{{ csrf_token() }}';
  
  /**
   * Navigate back to client page and automatically open client details
   */
  function goBackToClient(clientId, clientName) {
      // Store client info in sessionStorage for quick access
      sessionStorage.setItem('openClientId', clientId);
      sessionStorage.setItem('openClientName', clientName);
      
      // Redirect to clients index with client_id parameter
      window.location.href = '{{ route("clients.index") }}?client_id=' + clientId;
  }

  /**
   * Delete document from table
   */
  function deleteDocumentFromTable(documentId) {
      if (!confirm('Are you sure you want to delete this document?')) {
          return;
      }
      
      fetch(`/documents/${documentId}`, {
          method: 'DELETE',
          headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              // Show success message
              alert('Document deleted successfully');
              // Reload page to show updated list
              window.location.reload();
          } else {
              alert('Error deleting document: ' + (data.message || 'Unknown error'));
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('Error deleting document');
      });
  }
</script>
<script src="{{ asset('js/documents-index.js') }}?v={{ time() }}"></script>
@endsection