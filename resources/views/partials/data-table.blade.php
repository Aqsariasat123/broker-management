@php
  // Get configuration for this module
  $config = \App\Helpers\TableConfigHelper::getConfig($module);
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns($module);
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
  $columnSettingsRoute = route($config['route_prefix'] . '.save-column-settings');
  $exportRoute = route($config['route_prefix'] . '.export');
@endphp

<div class="dashboard">
  <!-- Main Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Data Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>{{ ucfirst($module) }}</h3>
        <div class="records-found">Records Found - {{ $data->total() ?? count($data) }}</div>
        @if(isset($filterButtons))
          <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
            @foreach($filterButtons as $filter)
              @if(isset($filter['type']) && $filter['type'] === 'archived')
                <div class="filter-group">
                  <button class="btn btn-archived {{ request()->get('archived') == '1' ? 'active' : '' }}" 
                          id="{{ $filter['id'] ?? 'archivedOnlyBtn' }}" 
                          type="button"
                          onclick="window.location.href='{{ request()->fullUrlWithQuery(['archived' => request()->get('archived') == '1' ? null : '1']) }}'">
                    {{ $filter['label'] ?? 'Archived Only' }}
                  </button>
                </div>
              @endif
            @endforeach
          </div>
        @endif
      </div>
      <div class="action-buttons">
        @if(isset($addButton))
          <button class="btn btn-add" id="{{ $addButton['id'] ?? 'addBtn' }}">{{ $addButton['label'] ?? 'Add' }}</button>
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
      <table id="{{ $module }}Table">
        <thead>
          <tr>
            <th>Action</th>
            @foreach($selectedColumns as $col)
              @if(isset($columnDefinitions[$col]))
                <th data-column="{{ $col }}">{{ $columnDefinitions[$col] }}</th>
              @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
          @if(isset($tableRows))
            {!! $tableRows !!}
          @else
            @foreach($data as $row)
              <tr class="{{ isset($rowClass) ? $rowClass($row) : '' }}">
                <td class="action-cell">
                  <svg class="action-expand" onclick="{{ isset($editFunction) ? $editFunction : 'openEdit' }}({{ $row->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
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
                  <td data-column="{{ $col }}">
                    {!! isset($renderCell) ? $renderCell($row, $col) : ($row->$col ?? '-') !!}
                  </td>
                @endforeach
              </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>

    </div>

    <div class="footer">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ $exportRoute }}">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button" onclick="openColumnModal()">Column</button>
      </div>
      @if(isset($data) && method_exists($data, 'links'))
        <div class="paginator">
          @php
            $base = url()->current();
            $q = request()->query();
            $current = $data->currentPage();
            $last = max(1, $data->lastPage());
            function page_url($base, $q, $p) { 
              $params = array_merge($q, ['page' => $p]); 
              return $base . '?' . http_build_query($params); 
            }
          @endphp

          <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,1) : '#' }}" @if($current<=1) disabled @endif>&laquo;</a>
          <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,$current-1) : '#' }}" @if($current<=1) disabled @endif>&lsaquo;</a>
          <span class="page-info">Page {{ $current }} of {{ $last }}</span>
          <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$current+1) : '#' }}" @if($current>=$last) disabled @endif>&rsaquo;</a>
          <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$last) : '#' }}" @if($current>=$last) disabled @endif>&raquo;</a>
        </div>
      @endif
    </div>
    </div>
  </div>

@include('partials.column-selection-modal', [
  'selectedColumns' => $selectedColumns,
  'columnDefinitions' => $columnDefinitions,
  'mandatoryColumns' => $mandatoryColumns,
  'columnSettingsRoute' => $columnSettingsRoute,
])


<script>
  // Initialize data from Blade
  const selectedColumns = @json($selectedColumns ?? []);
</script>
<script src="{{ asset('js/partials-data-table.js') }}"></script>
@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

