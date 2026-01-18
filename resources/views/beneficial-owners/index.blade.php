@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/beneficial-owners-index.css') }}">

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('beneficial_owners');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('beneficial_owners');
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Beneficial Owners Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:5px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:18px; font-weight:600;">
            Beneficial Owners
            @if(isset($client) && $client)
              <span class="client-name" style="color:#f3742a; font-size:16px; font-weight:500;"> - {{ $client->client_name }}</span>
            @endif
          </h3>
       
      </div>
    </div>
  <div class="container-table">
    <!-- Beneficial Owners Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <div class="records-found">Records Found - {{ $beneficialOwners->total() }}</div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addBeneficialOwnerBtn">Add</button>
        @if(request()->has('client_id'))
          <button class="btn" id="backBtn" onclick="window.history.back()" style="background:#999; color:#fff;">Back</button>
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
      <table id="beneficialOwnersTable">
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
          @foreach($beneficialOwners as $bo)
            @php
              $isExpired = $bo->is_expired ?? false;
            @endphp
            <tr class="{{ $isExpired ? 'expired-row' : '' }}" data-owner-code="{{ $bo->owner_code }}">
              <td class="bell-cell {{ $isExpired ? 'expired' : '' }}">
                <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                  @if($isExpired)
                    <div class="status-indicator expired" style="width:12px; height:12px; border-radius:50%; background-color:#dc3545; border:2px solid #dc3545;"></div>
                  @else
                    <label style="cursor:pointer; margin:0; padding:0; display:flex; align-items:center; justify-content:center;" onclick="document.querySelector('input[name=\'selected_bo\'][value=\'{{ $bo->id }}\']').click();">
                      <input type="radio" name="selected_bo" value="{{ $bo->id }}" data-owner-code="{{ $bo->owner_code }}" style="display:none;" onchange="loadDocumentsForSelectedBO(this)">
                      <div class="status-indicator normal" style="width:12px; height:12px; border-radius:50%; background-color:transparent; border:2px solid #000;"></div>
                    </label>
                  @endif
                </div>
              </td>
              <td class="action-cell">
                <svg class="action-expand" onclick="openBeneficialOwnerDetails({{ $bo->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <path d="M12 2L12 8M12 2L10 4M12 2L14 4" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M22 12L16 12M22 12L20 10M22 12L20 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 22L12 16M12 22L10 20M12 22L14 20" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M2 12L8 12M2 12L4 10M2 12L4 14" stroke="#2d2d2d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'full_name')
                  <td data-column="full_name">{{ $bo->full_name }}</td>
                @elseif($col == 'dob')
                  <td data-column="dob">{{ $bo->dob ? $bo->dob->format('d-M-y') : '-' }}</td>
                @elseif($col == 'age')
                  <td data-column="age">{{ $bo->age ?? '-' }}</td>
                @elseif($col == 'nin_passport_no')
                  <td data-column="nin_passport_no">{{ $bo->nin_passport_no ?? '-' }}</td>
                @elseif($col == 'country')
                  <td data-column="country">{{ $bo->country ?? '-' }}</td>
                @elseif($col == 'expiry_date')
                  <td data-column="expiry_date">{{ $bo->expiry_date ? $bo->expiry_date->format('d-M-y') : '-' }}</td>
                @elseif($col == 'status')
                  @php
                    $statusColor = '#6c757d';
                    if ($isExpired || ($bo->status && stripos($bo->status, 'Expired') !== false)) {
                      $statusColor = '#dc3545';
                    }
                  @endphp
                  <td data-column="status">
                    @if($isExpired || ($bo->status && stripos($bo->status, 'Expired') !== false))
                      <span class="badge-status" style="background:{{ $statusColor }}; color:#fff; padding:4px 8px; border-radius:4px; font-size:11px;">Expired</span>
                    @elseif($bo->status)
                      <span class="badge-status" style="background:{{ $statusColor }}; color:#fff; padding:4px 8px; border-radius:4px; font-size:11px;">{{ $bo->status }}</span>
                    @else
                      -
                    @endif
                  </td>
                @elseif($col == 'position')
                  <td data-column="position">{{ $bo->position ?? '-' }}</td>
                @elseif($col == 'shares')
                  <td data-column="shares">{{ $bo->shares ? number_format($bo->shares, 0) . '%' : '-' }}</td>
                @elseif($col == 'pep')
                  <td data-column="pep">{{ $bo->pep ? 'Y' : 'N' }}</td>
                @elseif($col == 'pep_details')
                  <td data-column="pep_details">{{ $bo->pep_details ?? '-' }}</td>
                @elseif($col == 'date_added')
                  <td data-column="date_added">{{ $bo->date_added ? $bo->date_added->format('d-M-y') : '-' }}</td>
                @elseif($col == 'removed')
                  <td data-column="removed">
                    <input type="checkbox" {{ $bo->removed ? 'checked' : '' }} disabled>
                  </td>
                @endif
              @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

    <!-- Documents Section -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h4 style="margin:0; font-size:16px; font-weight:600;">Documents</h4>
        <button class="btn btn-add" id="addDocumentBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Add Document</button>
      </div>
      <div id="documentsContainer" style="display:flex; gap:15px; flex-wrap:wrap;">
        <!-- Documents will be loaded here -->
      </div>
    </div>

    <div class="footer" style="background:#fff; border-top:1px solid #ddd; margin-top:15px; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('beneficial-owners.export', array_merge(request()->query(), ['page' => $beneficialOwners->currentPage()])) }}">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $beneficialOwners->currentPage();
          $last = max(1, $beneficialOwners->lastPage());
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

  <!-- Beneficial Owner Page View (Full Page) -->
  <div class="client-page-view" id="beneficialOwnerPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="beneficialOwnerPageTitle">Beneficial Owner</span> - <span class="client-name" id="beneficialOwnerPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editBeneficialOwnerFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeBeneficialOwnerPageBtn" onclick="closeBeneficialOwnerPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Beneficial Owner Details View -->
        <div id="beneficialOwnerDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="beneficialOwnerDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
          
          <!-- Documents Section in Page View -->
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; padding:15px 20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
              <h4 style="margin:0; font-size:16px; font-weight:600;">Documents</h4>
              <button class="btn btn-add" id="addDocumentBtnPageView" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Add Document</button>
            </div>
            <div id="documentsContainerPageView" style="display:flex; gap:15px; flex-wrap:wrap;">
              <!-- Documents will be loaded here -->
            </div>
          </div>
        </div>

        <!-- Beneficial Owner Edit/Add Form -->
        <div id="beneficialOwnerFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="beneficialOwnerDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteBeneficialOwner()">Delete</button>
                <button type="submit" form="beneficialOwnerPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeBeneficialOwnerFormBtn" onclick="closeBeneficialOwnerPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="beneficialOwnerPageForm" method="POST" action="{{ route('beneficial-owners.store') }}" enctype="multipart/form-data">
              @csrf
              <div id="beneficialOwnerPageFormMethod" style="display:none;"></div>
              <input type="file" name="document" id="documentFileInputPage" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
          </div>
        </form>
      </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Beneficial Owner Modal -->
  <div class="modal" id="beneficialOwnerModal">
    <div class="modal-content" style="max-width:900px; max-height:90vh; overflow-y:auto;">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd; background:#fff;">
        <h4 id="beneficialOwnerModalTitle" style="margin:0; font-size:18px; font-weight:bold;">Add Beneficial Owner</h4>
        <div style="display:flex; gap:10px;">
          <button type="submit" form="beneficialOwnerForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeBeneficialOwnerModal()" style="background:#ccc; color:#000; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Cancel</button>
        </div>
      </div>
      <form id="beneficialOwnerForm" method="POST" action="{{ route('beneficial-owners.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="beneficialOwnerFormMethod" style="display:none;"></div>
        <input type="hidden" name="client_id" id="bo_client_id" value="{{ request()->get('client_id') }}">
        <div class="modal-body" style="padding:20px;">
          <div class="form-row" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:15px;">
            <div class="form-group">
              <label for="full_name" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Full Name *</label>
              <input type="text" class="form-control" name="full_name" id="full_name" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group">
              <label for="dob" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">DOB</label>
              <input type="date" class="form-control" name="dob" id="dob" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group">
              <label for="nin_passport_no" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">NIN/Passport No</label>
              <input type="text" class="form-control" name="nin_passport_no" id="nin_passport_no" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
          </div>
          <div class="form-row" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:15px;">
            <div class="form-group">
              <label for="country" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Country *</label>
              <select class="form-control" name="country" id="country" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
                <option value="">Select Country</option>
                @foreach($lookupData['countries'] ?? [] as $country)
                  <option value="{{ $country['name'] }}">{{ $country['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="expiry_date" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Expiry Date</label>
              <input type="date" class="form-control" name="expiry_date" id="expiry_date" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group">
              <label for="status" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Status *</label>
              <select class="form-control" name="status" id="status" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
                <option value="">Select Status</option>
                @foreach($lookupData['statuses'] ?? [] as $status)
                  <option value="{{ $status['name'] }}">{{ $status['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:15px;">
            <div class="form-group">
              <label for="position" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Position *</label>
              <select class="form-control" name="position" id="position" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
                <option value="">Select Position</option>
                @foreach($lookupData['positions'] ?? [] as $position)
                  <option value="{{ $position['name'] }}">{{ $position['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="shares" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Shares (%)</label>
              <input type="number" step="0.01" min="0" max="100" class="form-control" name="shares" id="shares" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            </div>
            <div class="form-group">
              <label for="pep" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">PEP</label>
              <select class="form-control" name="pep" id="pep" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
                <option value="0">N</option>
                <option value="1">Y</option>
              </select>
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1;">
              <label for="pep_details" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">PEP Details</label>
              <textarea class="form-control" name="pep_details" id="pep_details" rows="3" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; resize:vertical;"></textarea>
            </div>
          </div>
          <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group">
              <label for="date_added" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Date Added</label>
              <input type="date" class="form-control" name="date_added" id="date_added" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
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
          <input type="file" name="document" id="documentFileInput" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
          <button type="button" class="btn-upload" onclick="openDocumentUploadModal()" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Document</button>
          <button type="button" class="btn-delete" id="beneficialOwnerDeleteBtnModal" style="display: none; background:#dc3545; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px; margin-left:10px;" onclick="deleteBeneficialOwner()">Delete</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Document Upload Modal -->
  <div class="modal" id="documentUploadModal" style="z-index:10001;">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-header">
        <h4>Select Document</h4>
        <button type="button" class="modal-close" onclick="closeDocumentUploadModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group" style="margin-bottom:15px;">
          <label for="documentType" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Document Type</label>
          <select id="documentType" name="document_type" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
            <option value="">Select Document Type</option>
            <option value="id_card">ID Card</option>
            <option value="passport">Passport</option>
            <option value="proof_of_address">Proof Of Address</option>
            <option value="other">Other Document</option>
          </select>
        </div>
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

  <!-- Document Upload Modal for Detail Modal -->
  <div class="modal" id="documentUploadModalForDetail" style="z-index:10001;">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-header">
        <h4>Upload Document</h4>
        <button type="button" class="modal-close" onclick="closeDocumentUploadModalForDetail()">×</button>
      </div>
      <div class="modal-body">
        <form id="documentUploadFormForDetail">
          <div class="form-group" style="margin-bottom:15px;">
            <label for="documentTypeForDetail" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Document Type</label>
            <select id="documentTypeForDetail" name="document_type" class="form-control" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">Select Document Type</option>
              <option value="id_card">ID Card</option>
              <option value="passport">Passport</option>
              <option value="proof_of_address">Proof Of Address</option>
              <option value="other">Other Document</option>
            </select>
          </div>
          <div class="form-group">
            <label for="documentFileForDetail">Select Document File</label>
            <input type="file" class="form-control" name="document" id="documentFileForDetail" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required onchange="handleDocumentFileSelectForDetail(event)">
            <small style="color:#666; font-size:11px;">Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max 5MB)</small>
          </div>
          <div id="documentPreviewForDetail" style="margin-top:15px; display:none;">
            <p style="font-size:12px; color:#666; font-weight:500;">Preview:</p>
            <div id="documentPreviewContentForDetail" style="margin-top:10px;"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeDocumentUploadModalForDetail()">Cancel</button>
        <button type="button" class="btn-save" onclick="uploadDocumentForDetail()">Upload</button>
      </div>
    </div>
  </div>

  <!-- Beneficial Owner Detail Modal -->
  <div class="modal" id="beneficialOwnerDetailModal" onclick="if(event.target === this) closeBeneficialOwnerDetailModal();">
    <div class="modal-content" style="max-width:900px; max-height:90vh; overflow-y:auto;" onclick="event.stopPropagation();">
      <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd; background:#fff;">
        <h4 id="beneficialOwnerDetailModalTitle" style="margin:0; font-size:18px; font-weight:bold;">Beneficial Owner Details</h4>
        <div style="display:flex; gap:10px;">
          <button type="button" class="btn btn-edit" id="editBeneficialOwnerFromDetailModalBtn" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px; display:none;">Edit</button>
          <button type="button" class="btn-cancel" onclick="closeBeneficialOwnerDetailModal()" style="background:#ccc; color:#000; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Close</button>
        </div>
      </div>
      <div class="modal-body" style="padding:20px;">
        <div id="beneficialOwnerDetailModalContent">
          <!-- Content will be loaded via JavaScript -->
        </div>
        
        <!-- Documents Preview Section (matching add dialog) -->
        <div id="selectedDocumentPreviewDetailModal" style="margin-top:15px; padding:10px; background:#f5f5f5; border-radius:4px; display:none;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <p style="margin:0; font-size:12px; color:#666; font-weight:500;">Selected Document:</p>
              <p id="selectedDocumentNameDetailModal" style="margin:5px 0 0 0; font-size:13px; color:#000;"></p>
            </div>
            <button type="button" onclick="removeSelectedDocumentDetailModal()" style="background:#dc3545; color:#fff; border:none; padding:4px 10px; border-radius:2px; cursor:pointer; font-size:11px;">Remove</button>
          </div>
          <div id="selectedDocumentImagePreviewDetailModal" style="margin-top:10px; max-width:200px; max-height:200px;"></div>
        </div>
      </div>
      <div class="modal-footer" style="padding:15px 20px; border-top:1px solid #ddd; background:#fff; display:flex; justify-content:center;">
        <button type="button" class="btn-upload" onclick="openDocumentUploadModalForDetail()" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Upload Document</button>
      </div>
    </div>
  </div>

  <!-- Column Selection Modal -->
  @include('partials.column-selection-modal', [
    'selectedColumns' => $selectedColumns,
    'columnDefinitions' => $columnDefinitions,
    'mandatoryColumns' => $mandatoryColumns,
    'columnSettingsRoute' => route('beneficial-owners.save-column-settings'),
  ])

</div>

<script>
  // Initialize data from Blade
  let currentBeneficialOwnerId = null;
  const selectedColumns = @json($selectedColumns);
  const beneficialOwnersStoreRoute = '{{ route("beneficial-owners.store") }}';
  const csrfToken = '{{ csrf_token() }}';
  const clientId = {{ request()->get('client_id') ?? 'null' }};
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])
<script src="{{ asset('js/beneficial-owners-index.js') }}"></script>
@endsection
