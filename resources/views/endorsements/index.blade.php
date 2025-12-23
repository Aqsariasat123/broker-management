@extends('layouts.app')
@section('content')
@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('endorsements');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('endorsements');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="page-title-section">
              <h3 style="margin:0; font-size:18px; font-weight:600;">
                  Endorsements
              </h3>
           </div>
      </div>
  </div>
  <!-- Main Endorsements Table View -->
  <div class="clients-table-view" id="endorsementsTableView">
    <div class="container-table">
      <!-- Endorsements Card -->
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
        <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
          <div class="page-title-section">
            <div class="records-found">Records Found - {{ $endorsements->total() }}</div>
          </div>
          <div class="action-buttons">
            @if(auth()->check() && (auth()->user()->hasPermission('endorsements.create') || auth()->user()->isAdmin()))
            <button class="btn btn-add" id="addEndorsementBtn">Add</button>
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
          <table id="endorsementsTable">
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
              @foreach($endorsements as $e)
              <tr>
                <td>
                  <a href="{{ route('endorsements.show', $e->id) }}" class="btn btn-info">View</a>
                  @if(auth()->check() && (auth()->user()->hasPermission('endorsements.edit') || auth()->user()->isAdmin()))
                  <a href="{{ route('endorsements.edit', $e->id) }}" class="btn btn-edit">Edit</a>
                  @endif
                  @if(auth()->check() && (auth()->user()->hasPermission('endorsements.delete') || auth()->user()->isAdmin()))
                  <form action="{{ route('endorsements.destroy', $e->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure?');">Delete</button>
                  </form>
                  @endif
                </td>
                <td>{{ $e->endorsement_no }}</td>
                <td>{{ $e->policy ? $e->policy->policy_no : '' }}</td>
                <td>{{ $e->type }}</td>
                <td>{{ $e->effective_date ? $e->effective_date->format('d-M-Y') : '' }}</td>
                <td>{{ $e->status }}</td>
                <td>{{ $e->description }}</td>
                <td>
                  @if($e->document_path)
                    <a href="{{ asset('storage/' . $e->document_path) }}" target="_blank" class="btn btn-info">View Document</a>
                  @else
                    -
                  @endif
                </td>
              </tr>
              @endforeach

              @foreach($endorsements as $e)
                <tr class="{{ $e->hasExpired ?? false ? 'has-expired' : ($e->hasExpiring ?? false ? 'has-expiring' : '') }}">
                    <td class="bell-cell {{ $inc->hasExpired ?? false ? 'expired' : ($inc->hasExpiring ?? false ? 'expiring' : '') }}">
                      <div style="display:flex; align-items:center; justify-content:center;">
                        @php
                          $isExpired = $e->hasExpired ?? false;
                          $isExpiring = $e->hasExpiring ?? false;
                        @endphp
                        <div class="status-indicator {{ $isExpired ? 'expired' : ($isExpiring ? 'expiring' : 'normal') }}" style="width:18px; height:18px; border-radius:50%; border:2px solid #000; background-color:{{ $isExpired ? '#dc3545' : ($isExpiring ? '#ffc107' : 'transparent') }};"></div>
                      </div>
                    </td>
                    <td class="action-cell">
                      <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openIncomeDetails({{ $inc->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">
                    
                    </td>
                    @foreach($selectedColumns as $col)
                      @if($col == 'endorsement_id')
                        <td data-column="endorsement_id">{{ $e->endorsement_id }}</td>
                      @elseif($col == 'endorsement_no')
                        <td data-column="endorsement_no">{{ $e->endorsement_no }}</td>
                      @elseif($col == 'policy_no')
                        <td data-column="policy_no">{{ $e->policy_no  }}</td>
                      @elseif($col == 'date')
                        <td data-column="date">{{ $e->date  }}</td>
                      @elseif($col == 'date')
                        <td data-column="date">{{ $e->date  }}</td>
                      @elseif($col == 'type')
                        <td data-column="date">{{ $e->type  }}</td>
                      @elseif($col == 'description')
                        <td data-column="description">{{ $e->description  }}</td>
                      @elseif($col == 'notes')
                        <td data-column="description">{{ $e->notes  }}</td>
                      @endif
                    @endforeach
                </tr>

               @endforeach
            </tbody>
          </table>
        </div>
        <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
          <div class="footer-left">
            <a class="btn btn-export" href="{{ route('endorsements.export', array_merge(request()->query(), ['page' => $endorsements->currentPage()])) }}">Export</a>
            <button class="btn btn-column" id="columnBtn2" type="button">Column</button>

          </div>
          <div class="paginator">
            @php
              $base = url()->current();
              $q = request()->query();
              $current = $endorsements->currentPage();
              $last = max(1, $endorsements->lastPage());
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
  </div>
    <!-- Income Page View (Full Page) -->
  <div class="client-page-view" id="incomePageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="incomePageTitle">Income</span> - <span class="client-name" id="incomePageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editIncomeFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeIncomePageBtn" onclick="closeIncomePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Income Details View -->
        <div id="incomeDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="incomeDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>

        <!-- Income Edit/Add Form -->
        <div id="incomeFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="incomeDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteIncome()">Delete</button>
                <button type="submit" form="incomePageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeIncomeFormBtn" onclick="closeIncomePageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="incomePageForm" method="POST" action="{{ route('incomes.store') }}">
              @csrf
              <div id="incomePageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
          </div>
        </form>
      </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Income Modal -->
  <div class="modal" id="incomeModal">
    <div class="modal-content" style="max-width:800px; max-height:90vh; overflow-y:auto;">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd; background:#fff;">
        <h4 id="incomeModalTitle" style="margin:0; font-size:18px; font-weight:bold;">Add Income</h4>
        <div style="display:flex; gap:10px;">
          <button type="submit" form="incomeForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeIncomeModal()" style="background:#000; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Cancel</button>
        </div>
      </div>
      <form id="incomeForm" method="POST" action="{{ route('incomes.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="incomeFormMethod" style="display:none;"></div>
        <input type="file" name="document" id="documentFileInput" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
        <div class="modal-body" style="padding:20px;">
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="income_source_id" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Income Source</label>
              <select class="form-control" name="income_source_id" id="income_source_id" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
                <option value="">Select</option>
                @foreach($types as $src)
                  <option value="{{ $src->id }}">{{ $src->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group" style="flex:1;">
              <label for="date_rcvd" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Date Received</label>
              <input type="date" class="form-control" name="date_rcvd" id="date_rcvd" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group" style="flex:1;">
              <label for="amount_received" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Amount Received</label>
              <input type="number" step="0.01" class="form-control" name="amount_received" id="amount_received" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
        
            <div class="form-group" style="flex:1;">
              <label for="description" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Description</label>
              <input type="text" class="form-control" name="description" id="description" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="statement_no" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Statement No.</label>
              <input type="text" class="form-control" name="statement_no" id="statement_no" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1 1 100%;">
              <label for="income_notes" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Income Notes</label>
              <textarea class="form-control" name="income_notes" id="income_notes" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; resize:vertical;"></textarea>
            </div>
          </div>
          <div id="selectedDocumentPreview" style="margin-top:15px; padding:10px; background:#f5f5f5; border-radius:4px; display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div>
                <p style="margin:0; font-size:12px; color:#666; font-weight:500;">Selected Document:</p>
                <p id="selectedDocumentName" style="margin:5px 0 0 0; font-size:13px; color:#000;"></p>
              </div>
              <button type="button" onclick="removeSelectedDocument()" style="background:#dc3545; color:#fff; border:none; padding:4px 10px; border-radius:2px; cursor:pointer; font-size:11px;">Remove</button>
            </div>
            <div id="selectedDocumentImagePreview" style="margin-top:10px; max-width:200px; max-height:200px;"></div>
          </div>
        </div>
        <div class="modal-footer" style="padding:15px 20px; border-top:1px solid #ddd; background:#fff; display:flex; justify-content:center;">
          <button type="button" class="btn-upload" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Document</button>
          <button type="button" class="btn-delete" id="incomeDeleteBtnModal" style="display: none; background:#dc3545; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px; margin-left:10px;" onclick="deleteIncome()">Delete</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Document Upload Modal -->
  <div class="modal" id="documentUploadModal">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-header">
        <h4>Select Document</h4>
        <button type="button" class="modal-close" onclick="closeDocumentUploadModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="documentFile">Select Document File</label>
          <input type="file" class="form-control" name="document" id="documentFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" onchange="handleDocumentFileSelect(event)">
          <small style="color:#666; font-size:11px;">Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max 5MB)</small>
        </div>
        <div id="documentPreview" style="margin-top:15px; display:none;">
          <p style="font-size:12px; color:#666; font-weight:500;">Preview:</p>
          <div id="documentPreviewContent" style="margin-top:10px;"></div>
        </div>
        <div id="existingDocumentPreview" style="margin-top:15px; display:none;">
          <p style="font-size:12px; color:#666;">Current document:</p>
          <div id="existingDocumentPreviewContent"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeDocumentUploadModal()">Cancel</button>
        <button type="button" class="btn-save" onclick="confirmDocumentSelection()">Select</button>
      </div>
    </div>
  </div>

  <!-- Column Selection Modal -->
  <div class="modal" id="columnModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4>Column Select & Sort</h4>
        <button type="button" class="modal-close" onclick="closeColumnModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="column-actions">
          <button type="button" class="btn-select-all" onclick="selectAllColumns()">Select All</button>
          <button type="button" class="btn-deselect-all" onclick="deselectAllColumns()">Deselect All</button>
        </div>

        <form id="columnForm" action="{{ route('incomes.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection" id="columnSelection">
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
            @endphp

            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryColumns);
                $isChecked = in_array($key, $selectedColumns) || $isMandatory;
              @endphp
              <div class="column-item" draggable="true" data-column="{{ $key }}" style="cursor:move;">
                <span style="cursor:move; margin-right:8px; font-size:16px; color:#666;">☰</span>
                <input type="checkbox" class="column-checkbox" id="col_{{ $key }}" value="{{ $key }}" @if($isChecked) checked @endif @if($isMandatory) disabled @endif>
                <label for="col_{{ $key }}" style="cursor:pointer; flex:1; user-select:none;">{{ $label }}</label>
              </div>
            @endforeach
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn-cancel" onclick="closeColumnModal()">Cancel</button>
        <button class="btn-save" onclick="saveColumnSettings()">Save Settings</button>
      </div>
    </div>
  </div>
</div>

@endsection

