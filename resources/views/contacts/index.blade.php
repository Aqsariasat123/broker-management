@extends('layouts.app')
@section('content')

@include('partials.table-styles')
<link rel="stylesheet" href="{{ asset('css/contacts-index.css') }}">




@php
  $config = \App\Helpers\TableConfigHelper::getConfig('contacts');
  $selectedColumns = session('contact_columns', $config['default_columns'] ?? []);
  $columnDefinitions = $config['column_definitions'] ?? [];
  $mandatoryColumns = $config['mandatory_columns'] ?? [];
@endphp

<div class="dashboard">
  <!-- Main Contacts Table View -->
  <div class="clients-table-view" id="clientsTableView">
    
    <div class="container-table">
      <!-- Header -->
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:15px; margin-bottom:15px; padding:15px 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
           <h3 style="margin:0; font-size:18px; font-weight:600;">
                    @if($statusfilter == 'open')
                        Open Leads
                    @else
                  Contacts{{ request()->has('follow_up') && request()->follow_up ? ' - To Follow Up' : '' }}
                    @endif
            </h3>
            @include('partials.page-header-right')
        </div>
      </div>

      <!-- Contacts Card -->
      <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
          <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
              <div class="records-found">Records Found - {{ $contacts->total() }}</div>

              <div class="page-title-section">
                <div class="filter-group">
                  <label class="toggle-switch">
                    <input type="checkbox" id="filterToggle" {{ request()->has('follow_up') && request()->follow_up ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                  </label>
                  <label for="filterToggle" style="font-size:14px; color:#2d2d2d; margin:0; cursor:pointer; user-select:none;">Filter</label>
                </div>
                @if(request()->has('follow_up') && request()->follow_up)
                  <button class="btn btn-list-all" id="listAllBtn" type="button">List ALL</button>
                @else
                  <button class="btn btn-follow-up" id="followUpBtn" type="button">To Follow Up</button>
                @endif
              </div>
              <div class="action-buttons">
                <button class="btn btn-add" id="addContactBtn">Add</button>
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
            <table id="contactsTable">
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
                @foreach($contacts as $contact)
                <tr class="{{ optional($contact->statusRelation)->name === 'Archived' ? 'archived-row' : '' }}">
                    <td class="bell-cell {{ $contact->hasExpired ? 'expired' : ($contact->hasExpiring ? 'expiring' : '') }}">
                      <div style="display:flex; align-items:center; justify-content:center;">
                        @php
                          $isExpired = $contact->hasExpired;
                          $isExpiring = $contact->hasExpiring;
                          $hasFollowUp = $contact->next_follow_up && optional($contact->statusRelation)->name !== 'Archived';
                          
                          // Determine color based on status
                          if ($isExpired) {
                            $dotColor = '#dc3545'; // Red - expired
                            $dotFill = '#dc3545';
                          } elseif ($isExpiring) {
                            $dotColor = '#f3742a'; // Yellow/Orange - expiring soon
                            $dotFill = 'transparent';
                          } elseif ($hasFollowUp) {
                            $dotColor = '#007bff'; // Blue - has follow-up
                            $dotFill = 'transparent';
                          } else {
                            $dotColor = '#ccc'; // Gray - no follow-up
                            $dotFill = 'transparent';
                          }
                        @endphp
                        <div class="status-indicator" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $dotColor }}; background-color:{{ $dotFill }};"></div>
                      </div>
                    </td>
                    <td class="action-cell">
                    <img src="{{ asset('asset/arrow-expand.svg') }}" class="action-expand" onclick="openContactDetails({{ $contact->id }})" width="22" height="22" style="cursor:pointer; vertical-align:middle;" alt="Expand">

                    
                    </td>
                  @foreach($selectedColumns as $col)
                    @php
                        $value = '-'; // default fallback
                    @endphp

                    @switch($col)

                        {{-- Contact Name --}}
                        @case('contact_name')
                            @php $value = $contact->contact_name ?? '-' @endphp
                            @break

                        {{-- Contact ID --}}
                        @case('contact_id')
                            @php $value = $contact->contact_id ?? '##########' @endphp
                            @break

                        {{-- Contact Number --}}
                        @case('contact_no')
                            @php $value = $contact->contact_no ?? '##########' @endphp
                            @break

                        {{-- Type --}}
                        @case('type')
                            @php $value = $contact->type_value->name ?? $contact->type ?? '-' @endphp
                            @break

                        {{-- Occupation --}}
                        @case('occupation')
                            @php $value = $contact->occupation ?? '-' @endphp
                            @break

                        {{-- Employer --}}
                        @case('employer')
                            @php $value = $contact->employer ?? '-' @endphp
                            @break

                        {{-- Acquired --}}
                        @case('acquired')
                            @php $value = $contact->acquired ? $contact->acquired->format('d-M-y') : '##########' @endphp
                            @break

                        {{-- Source --}}
                        @case('source')
                            @php $value = $contact->source_value->name ?? $contact->source ?? '-' @endphp
                            @break

                        {{-- Status with color badge --}}
                        @case('status')
                            @php
                                $statusColor = match(optional($contact->statusRelation)->name) {
                                    'Archived' => '#343a40',
                                    'Proposal Made' => '#28a745',
                                    'In Discussion' => '#ffc107',
                                    default => '#6c757d'
                                };
                                $value = "<span class='badge-status' style='background:{$statusColor}'>{$contact->statusRelation->name}</span>";
                            @endphp
                            @break

                        {{-- Rank --}}
                        @case('rank')
                            @php $value = $contact->rank ?? '-' @endphp
                            @break

                        {{-- First Contact --}}
                        @case('first_contact')
                            @php $value = $contact->first_contact ? $contact->first_contact->format('d-M-y') : '##########' @endphp
                            @break

                        {{-- Next Follow Up --}}
                        @case('next_follow_up')
                            @php $value = $contact->next_follow_up ? $contact->next_follow_up->format('d-M-y') : '##########' @endphp
                            @break

                        {{-- COID --}}
                        @case('coid')
                            @php $value = $contact->coid ?? '##########' @endphp
                            @break

                        {{-- DOB --}}
                        @case('dob')
                            @php $value = $contact->dob ? $contact->dob->format('d-M-y') : '##########' @endphp
                            @break

                        {{-- Salutation --}}
                        @case('salutation')
                            @php $value = $contact->salutation ?? '-' @endphp
                            @break

                        {{-- Source Name --}}
                        @case('source_name')
                            @php $value = $contact->source_name ?? '-' @endphp
                            @break

                        {{-- Agency --}}
                        @case('agency')
                            @php $value = $contact->agency_user->name ?? $contact->agency ?? '-' @endphp
                            @break

                        {{-- Agent --}}
                        @case('agent')
                            @php $value = $contact->agent_user->name ?? $contact->agent ?? '-' @endphp
                            @break

                        {{-- Address --}}
                        @case('address')
                            @php $value = $contact->address ?? '-' @endphp
                            @break

                        {{-- Email Address --}}
                        @case('email_address')
                            @php $value = $contact->email_address ?? '-' @endphp
                            @break

                        {{-- Savings Budget --}}
                        @case('savings_budget')
                            @php $value = $contact->savings_budget ? number_format($contact->savings_budget, 2) : '##########' @endphp
                            @break

                        {{-- Married --}}
                        @case('married')
                            @php $value = $contact->married ? 'Yes' : 'No' @endphp
                            @break

                        {{-- Children --}}
                        @case('children')
                            @php $value = $contact->children ?? '0' @endphp
                            @break

                        {{-- Children Details --}}
                        @case('children_details')
                            @php $value = $contact->children_details ?? '-' @endphp
                            @break

                        {{-- Vehicle --}}
                        @case('vehicle')
                            @php $value = $contact->vehicle ?? '-' @endphp
                            @break

                        {{-- House --}}
                        @case('house')
                            @php $value = $contact->house ?? '-' @endphp
                            @break

                        {{-- Business --}}
                        @case('business')
                            @php $value = $contact->business ?? '-' @endphp
                            @break

                        {{-- Other --}}
                        @case('other')
                            @php $value = $contact->other ?? '-' @endphp
                            @break

                    @endswitch

                    <td data-column="{{ $col }}">{!! $value !!}</td>
                  @endforeach
 

                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>


          <div class="footer" style="background:#fff; border-top:1px solid #ddd; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
            <div class="footer-left">
              <a class="btn btn-export" href="{{ route('contacts.export', array_merge(request()->query(), ['page' => $contacts->currentPage()])) }}">Export</a>
              <button class="btn btn-column" id="columnBtn2" type="button">Column</button>
            </div>
            <div class="paginator">
              @php
                $base = url()->current();
                $q = request()->query();
                $current = $contacts->currentPage();
                $last = max(1,$contacts->lastPage());
                function page_url($base,$q,$p){ $params = array_merge($q,['page'=>$p]); return $base . '?' . http_build_query($params); }
              @endphp

              <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,1) : '#' }}" @if($current<=1) disabled @endif>&laquo;</a>
              <a class="btn-page" href="{{ $current>1 ? page_url($base,$q,$current-1) : '#' }}" @if($current<=1) disabled @endif>&lsaquo;</a>
              <span class="page-info">Page {{ $current }} of {{ $last }}</span>
              <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$current+1) : '#' }}" @if($current>= $last) disabled @endif>&rsaquo;</a>
              <a class="btn-page" href="{{ $current<$last ? page_url($base,$q,$last) : '#' }}" @if($current>=$last) disabled @endif>&raquo;</a>
            </div>
          </div>
      </div>

    </div>
  </div>
    <!-- Conact Page View (Full Page) -->
  <div class="client-page-view" id="contactPageView" style="display:none;">
    <!-- Header Card with Contact Name -->
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="contactPageTitle">Contact Name</span> - <span class="client-name" id="contactPageName">-</span>
      </div>
      </div>
    
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Navigation Tabs and Actions Card -->
      
        
        <!-- Contact Details Content Card - Separate -->
        <div id="contactDetailsContentWrapper" style="display:none; background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:12px; overflow:hidden;">
        <div id="contactDetailsPageContent" style="display:none;">
          <div style=" margin-bottom:15px; overflow:hidden;">
              <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 15px;">
                <div class="client-page-nav">
                       
                
                <button class="contact-tab active" data-tab="life-proposals-view" data-url="{{ route('life-proposals.index') }}">View Proposals</button>
                <button class="contact-tab" data-tab="life-proposals-add" data-url="{{ route('life-proposals.index') }}">Add Proposal</button>
                <button class="contact-tab" data-tab="life-proposals-follow-up" data-url="{{ route('life-proposals.index') }}">Follow Up</button>

                </div>
                <div class="client-page-actions" id="contactHeaderActions">
                  <button class="btn btn-edit" id="editContactFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px; display:none;" onclick="if(currentContactId) openEditContract(currentContactId)">Edit</button>
                  <button class="btn" id="contactContactPageBtn" onclick="closeContactPageView()" style="background:#e0e0e0; color:#000; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px; display:none;">Cancel</button>
                  <button class="btn" id="deleteContactFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px; display:none;" onclick="deleteContact()">Delete</button>
                  <button class="btn" id="saveContactFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px; display:none;" onclick="saveContactFromPage()">Save</button>
                  <button class="btn" id="closeContactFromPageBtn" style="background:#e0e0e0; color:#000; border:none; padding:4px 12px; border-radius:2px; cursor:pointer; font-size:12px; display:none;" onclick="closeContactPageView()">Cancel</button>
                </div>
              </div>
            </div>
          </div> 
        <div id="contactDetailsContent" style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; padding:0;">
              <!-- Content will be loaded via JavaScript -->
            </div>
        </div>

        <!-- Contact Schedule Card - Separate -->
        <div id="contactScheduleContentWrapper" style="display:none; background:#fff; border:1px solid #ddd; border-radius:4px; padding:12px;  margin-bottom:15px; overflow:hidden;justify-content: space-between; ">
             <div class="contact-bottom-tabs">
                @foreach($lookupData['contact_statuses'] as $status)
                    <button 
                        class="contact-bottom-tab" 
                        data-tab="{{ $status['id'] }}" 
                        data-url="{{ route('schedules.index') }}">
                        {{ $status['name'] }}
                    </button>
                @endforeach
            </div>
          <div id="contactScheduleContent" style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; padding:0;">
            <!-- Content will be loaded via JavaScript -->
          </div>
        </div>
        
        <!-- Follow Ups Card - Separate -->
        <div id="followupsContentWrapper" style="display:none; background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 15px; border-bottom:1px solid #ddd;">
            <h4 style="margin:0; font-size:14px; font-weight:600; color:#333;">Follow Ups</h4>
            <button class="btn" onclick="openAddFollowUpModal()" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:4px; cursor:pointer; font-size:12px;">Add Follow Up</button>
          </div>
          <div id="followupcontent" style="padding:10px;">
            <!-- Follow ups will be loaded via JavaScript -->
          </div>
        </div>
        
        <!-- Policy Add Form -->
        <div id="contactFormPageContent" style="display:none;">
          <!-- Header for Add/Edit Policy -->
          <!-- <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:12px 15px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <h4 id="policyFormTitle" style="margin:0; font-size:16px; font-weight:600; color:#333;">Policy - Add New</h4>
                <div class="client-page-actions" id="policyFormHeaderActions">
                <button type="submit" form="policyForm" class="btn-save" id="policySaveBtnHeader" style="display:inline-block; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px; margin-right:8px;">Save</button>
                <button type="button" class="btn" id="closePolicyFormBtnHeader" style="display:inline-block; background:#fff; color:#333; border:1px solid #ddd; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px;" onclick="closecontactPageView()">Cancel</button>
              </div>  -->
           
         
    
       
          
        
          <form id="policyForm" method="POST" action="{{ route('policies.store') }}" enctype="multipart/form-data">
              @csrf
         <div class="edit-parent" style="background-color:white; padding:10px ;">
          <div id="policyFormTabs" style="background:#fff; border:1px solid #ddd; border-radius:4px;  overflow:hidden; display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 15px; background:#fff;">
              <div class="client-page-nav">
     
                <button class="contact-tab active" data-tab="schedules" data-url="{{ route('schedules.index') }}">Schedules</button>
                <button class="contact-tab" data-tab="payments" data-url="{{ route('payments.index') }}">Payments</button>
                <button class="contact-tab" data-tab="vehicles" data-url="{{ route('vehicles.index') }}">Vehicles</button>
                <button class="contact-tab" data-tab="claims" data-url="{{ route('claims.index') }}">Claims</button>
                <button class="contact-tab" data-tab="documents" data-url="{{ route('documents.index') }}">Documents</button>
                <button class="contact-tab" data-tab="endorsements" data-url="{{ route('endorsements.index') }}">Endorsements</button>
                <button class="contact-tab" data-tab="commissions" data-url="{{ route('commissions.index') }}">Commission</button>
                <button class="contact-tab" data-tab="nominees" data-url="{{ route('nominees.index') }}">Nominees</button>

              </div>
               <div class="client-page-actions" id="policyFormHeaderActions">
                <button type="submit" form="policyForm" class="btn-save" id="policySaveBtnHeader" style="display:inline-block; background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px; margin-right:8px;">Save</button>
                <button type="button" class="btn" id="closePolicyFormBtnHeader" style="display:inline-block; background:#fff; color:#333; border:1px solid #ddd; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:13px;" onclick="closecontactPageView()">Cancel</button>
              </div>
            </div>
          </div>
              <div id="policyFormMethod" style="display:none;"></div>
            
            <!-- Policy Form Content Card -->
            <div id="policyFormContentWrapper" style="background:#fff;  #ddd; border-radius:4px; padding:0; overflow:hidden;gap:10px;">
              <!-- Content will be loaded via JavaScript -->
              <div id="policyFormContent" style="padding:0;">
                <!-- Content will be loaded via JavaScript -->
              </div>
          </div>
             </div>

       
            
            <!-- Policy Schedule Card -->
            <div id="policyFormScheduleWrapper">
            
            <div id="policyFormScheduleContent" style="padding:0;">
                <!-- Content will be loaded via JavaScript -->
              </div>
            </div>
            
            <!-- Documents Card -->
            <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-top:20px; overflow:hidden;">
              <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; border-bottom:1px solid #ddd;">
                <h4 style="margin:0; font-size:13px; font-weight:600; color:#333;">Documents</h4>
                <div>
                  <button type="button" class="btn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:3px; cursor:pointer; font-size:12px;" onclick="openPolicyDocumentUploadModal()">Upload Document</button>
                </div>
              </div>
              <div id="policyFormDocumentsContent" style="display:flex; gap:10px; flex-wrap:wrap; padding:12px; min-height:100px;">
                <!-- Documents will be loaded via JavaScript -->
              </div>
            </div>
          </form>
        </div>
 
      </div>
    </div>
  </div>
  <!-- Add/Edit Contact Modal -->
  <div class="modal" id="contactModal">
    <div class="modal-content" style="max-width: 500px; width: 90%;">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; border-bottom: 1px solid #ddd;">
        <h4 id="contactModalTitle" style="margin: 0; font-size: 16px; font-weight: bold;">Add Contact</h4>
        <div style="display: flex; gap: 8px;">
          <button type="submit" form="contactForm" class="btn-save" style="background: #f3742a; color: #fff; border: none; padding: 5px 12px; border-radius: 2px; cursor: pointer; font-size: 12px;">Save</button>
          <button type="button" class="btn-cancel" onclick="closeContactModal()" style="background: #ccc; color: #000; border: none; padding: 5px 12px; border-radius: 2px; cursor: pointer; font-size: 12px;">Cancel</button>
        </div>
      </div>

      <form id="contactForm" method="POST" action="{{ route('contacts.store') }}">
        @csrf
        <div id="contactFormMethod" style="display:none;"></div>

        <div class="modal-body" style="padding: 12px 15px;">
          <h5 style="color: #f3742a; margin: 0 0 10px 0; font-size: 13px; font-weight: bold;">Contact Details</h5>

          <div style="display: flex; flex-direction: column; gap: 6px;">
            <!-- Contact Type -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="type" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Contact Type</label>
              <select id="type" name="type" class="form-control" required style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select</option>
                @foreach($lookupData['contact_types'] as $t) <option value="{{ $t['id'] }}">{{ $t['name'] }}</option> @endforeach
              </select>
            </div>
            <!-- Contact Name -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="contact_name" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Contact Name</label>
              <input id="contact_name" name="contact_name" class="form-control" required style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
            </div>
            <!-- Occupation -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="occupation" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Occupation</label>
              <select id="occupation" name="occupation" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select or Type</option>
                @foreach($allOccupations as $occ) <option value="{{ $occ }}">{{ $occ }}</option> @endforeach
              </select>
            </div>
            <!-- Employer -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="employer" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Employer</label>
              <select id="employer" name="employer" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select or Type</option>
                @foreach($allEmployers as $emp) <option value="{{ $emp }}">{{ $emp }}</option> @endforeach
              </select>
            </div>
            <!-- Contact No -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="contact_no" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Contact No</label>
              <div style="flex: 1; display: flex; gap: 6px; align-items: center;">
                <input id="contact_no" name="contact_no" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
                <input id="wa" name="wa" placeholder="WA" class="form-control" style="width: 50px; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
                <input type="checkbox" id="wa_checkbox" class="checkbox-style" style="width: 18px; height: 18px;">
              </div>
            </div>
            <!-- Email Address -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="email_address" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Email Address</label>
              <input id="email_address" name="email_address" type="email" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
            </div>
            <!-- Address / Location -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="address" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Address / Location</label>
              <div style="flex: 1; display: flex; gap: 6px;">
                <input id="address" name="address" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
                <input id="location" name="location" placeholder="PR" class="form-control" style="width: 50px; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
              </div>
            </div>
            <!-- Date Of Birth / Age -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="dob" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Date Of Birth / Age</label>
              <div style="flex: 1; display: flex; gap: 6px;">
                <input id="dob" name="dob" type="date" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
                <input id="age_display" type="text" placeholder="Age" readonly class="form-control" style="width: 50px; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; background: #f5f5f5; font-size: 12px;">
              </div>
            </div>
            <!-- Date Acquired -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="acquired" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Date Acquired</label>
              <div style="flex: 1; display: flex; gap: 6px;">
                <input id="acquired" name="acquired" type="date" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
                <input type="text" value="-" readonly class="form-control" style="width: 50px; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; background: #f5f5f5; font-size: 12px;">
              </div>
            </div>
            <!-- Source -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="source" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Source</label>
              <select id="source" name="source" class="form-control" required style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select</option>
                @foreach($lookupData['sources'] as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
              </select>
            </div>
            <!-- Source Name -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="source_name" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Source Name</label>
              <input id="source_name" name="source_name" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
            </div>
            <!-- Agency -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="agency" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Agency</label>
              <select id="agency" name="agency" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select</option>
                @foreach($lookupData['agencies'] as $a) <option value="{{ $a['id'] }}">{{ $a['name'] }}</option> @endforeach
              </select>
            </div>
            <!-- Agent -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="agent" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Agent</label>
              <select id="agent" name="agent" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select</option>
                @foreach($lookupData['agents'] as $a) <option value="{{ $a['id'] }}">{{ $a['name'] }}</option> @endforeach
                @foreach($users as $user) <option value="{{ $user->name }}">{{ $user->name }}</option> @endforeach
              </select>
            </div>
            <!-- Status -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="status" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Status</label>
              <select id="status" name="status" class="form-control" required style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select</option>
                @foreach($lookupData['contact_statuses'] as $st) <option value="{{ $st['id'] }}">{{ $st['name']  }}</option> @endforeach
              </select>
            </div>
            <!-- Ranking -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="rank" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Ranking</label>
              <select id="rank" name="rank" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="">Select</option>
                @foreach($lookupData['ranks'] as $r) <option value="{{ $r['id'] }}">{{ $r['name'] }}</option> @endforeach
              </select>
            </div>
            <!-- Savings Budget -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="savings_budget" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Savings Budget</label>
              <input id="savings_budget" name="savings_budget" type="number" step="1" class="form-control" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
            </div>
            <!-- Children -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label for="children" style="font-size: 12px; font-weight: 500; min-width: 130px; text-align: left;">Children</label>
              <input id="children" name="children" type="number" min="0" class="form-control" value="0" style="flex: 1; padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
            </div>
          </div>

          <input type="hidden" id="first_contact" name="first_contact">
          <input type="hidden" id="next_follow_up" name="next_follow_up">
          <input type="hidden" id="coid" name="coid">
          <input type="hidden" id="married" name="married" value="0">
          <input type="hidden" id="children_details" name="children_details">
          <input type="hidden" id="vehicle" name="vehicle">
          <input type="hidden" id="house" name="house">
          <input type="hidden" id="business" name="business">
          <input type="hidden" id="other" name="other">
          <input type="hidden" id="salutation" name="salutation">
        </div>

        <div class="modal-footer" style="display: none;">
          <button type="button" class="btn-delete" id="contactDeleteBtn" style="display:none;" onclick="deleteContact()">Delete</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Follow Up Modal -->
  <div class="modal" id="addFollowUpModal">
    <div class="modal-content" style="max-width: 500px; width: 90%;">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; border-bottom: 1px solid #ddd;">
        <h4 style="margin: 0; font-size: 16px; font-weight: bold;">Add Follow Up</h4>
        <div style="display: flex; gap: 8px;">
          <button type="button" onclick="saveFollowUp()" class="btn-save" style="background: #f3742a; color: #fff; border: none; padding: 5px 12px; border-radius: 2px; cursor: pointer; font-size: 12px;">Save</button>
          <button type="button" onclick="closeAddFollowUpModal()" class="btn-cancel" style="background: #ccc; color: #000; border: none; padding: 5px 12px; border-radius: 2px; cursor: pointer; font-size: 12px;">Cancel</button>
        </div>
      </div>
      <div class="modal-body" style="padding: 15px;">
        <form id="followUpForm">
          <input type="hidden" id="fu_contact_id" name="contact_id">

          <div style="display: flex; flex-direction: column; gap: 10px;">
            <!-- Follow Up Date -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label style="font-size: 12px; font-weight: 500; min-width: 120px;">Follow Up Date</label>
              <input type="date" id="fu_follow_up_date" name="follow_up_date" required style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px;">
            </div>
            <!-- Channel -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label style="font-size: 12px; font-weight: 500; min-width: 120px;">Channel</label>
              <select id="fu_channel" name="channel" style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="Phone">Phone</option>
                <option value="Email">Email</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="Meeting">Meeting</option>
                <option value="SMS">SMS</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <!-- Status -->
            <div style="display: flex; align-items: center; gap: 10px;">
              <label style="font-size: 12px; font-weight: 500; min-width: 120px;">Status</label>
              <select id="fu_status" name="status" style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; background: #fff;">
                <option value="Open">Open</option>
                <option value="In Progress">In Progress</option>
                <option value="Done">Done</option>
              </select>
            </div>
            <!-- Summary -->
            <div style="display: flex; align-items: flex-start; gap: 10px;">
              <label style="font-size: 12px; font-weight: 500; min-width: 120px; padding-top: 6px;">Summary</label>
              <textarea id="fu_summary" name="summary" rows="3" style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; resize: vertical;"></textarea>
            </div>
            <!-- Next Action -->
            <div style="display: flex; align-items: flex-start; gap: 10px;">
              <label style="font-size: 12px; font-weight: 500; min-width: 120px; padding-top: 6px;">Next Action</label>
              <textarea id="fu_next_action" name="next_action" rows="2" style="flex: 1; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; resize: vertical;"></textarea>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Column Selection Modal -->
</div>

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
        <form id="columnForm" action="{{ route('contacts.save-column-settings') }}" method="POST">
          @csrf
          <div class="column-selection-vertical" id="columnSelection">
            @php
              $all = [
                'contact_name'=>'Contact Name','contact_no'=>'Contact No','type'=>'Type','occupation'=>'Occupation','employer'=>'Employer',
                'acquired'=>'Acquired','source'=>'Source','status'=>'Status','rank'=>'Rank','first_contact'=>'1st Contact',
                'next_follow_up'=>'Next FU','coid'=>'COID','dob'=>'DOB','salutation'=>'Salutation','source_name'=>'Source Name',
                'agency'=>'Agency','agent'=>'Agent','address'=>'Address','email_address'=>'Email Address','contact_id'=>'Contact ID',
                'savings_budget'=>'Savings Budget','married'=>'Married','children'=>'Children','children_details'=>'Children Details',
                'vehicle'=>'Vehicle','house'=>'House','business'=>'Business','other'=>'Other'
              ];
              $ordered = [];
              foreach($selectedColumns as $col) {
                if(isset($all[$col])) {
                  $ordered[$col] = $all[$col];
                  unset($all[$col]);
                }
              }
              $ordered = array_merge($ordered, $all);
              $mandatoryFields = $mandatoryColumns;
              $counter = 1;
            @endphp
            @foreach($ordered as $key => $label)
              @php
                $isMandatory = in_array($key, $mandatoryFields);
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

<script>
  // Initialize data from Blade
  let currentContactId = null;
  const lookupData = @json($lookupData ?? []);
  const allOccupations = @json($allOccupations ?? []);
  const allEmployers = @json($allEmployers ?? []);
  const users = @json($users ?? []);
  const selectedColumns = @json($selectedColumns ?? []);
  const mandatoryFields = @json($mandatoryColumns ?? []);
  const contactsStoreRoute = '{{ route("contacts.store") }}';
  const csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/contacts-index.js') }}"></script>
@endsection
