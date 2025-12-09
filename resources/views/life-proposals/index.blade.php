@extends('layouts.app')
@section('content')

@include('partials.table-styles')

@php
  $config = \App\Helpers\TableConfigHelper::getConfig('life-proposals');
  $selectedColumns = \App\Helpers\TableConfigHelper::getSelectedColumns('life-proposals');
  $columnDefinitions = $config['column_definitions'];
  $mandatoryColumns = $config['mandatory_columns'];
@endphp

<div class="dashboard">
  <!-- Main Life Proposals Table View -->
  <div class="clients-table-view" id="clientsTableView">
  <div class="container-table">
    <!-- Life Proposals Card -->
    <div style="background:#fff; border:1px solid #ddd; border-radius:4px; overflow:hidden;">
      <div class="page-header" style="background:#fff; border-bottom:1px solid #ddd; margin-bottom:0;">
      <div class="page-title-section">
        <h3>Life Proposals</h3>
        <div class="records-found">Records Found - {{ $proposals->total() }}</div>
      </div>
      <div class="action-buttons">
        <button class="btn btn-add" id="addProposalBtn">Add</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin:15px 20px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
      </div>
    @endif

    <div class="table-responsive" id="tableResponsive">
      <table id="proposalsTable">
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
          @foreach($proposals as $proposal)
            <tr class="{{ $proposal->is_submitted ? 'submitted-row' : '' }}">
              <td class="action-cell">
                <svg class="action-expand" onclick="openProposalDetails({{ $proposal->id }})" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor:pointer; vertical-align:middle;">
                  <rect x="9" y="9" width="6" height="6" stroke="#2d2d2d" stroke-width="1.5" fill="none"/>
                  <path d="M12 9L12 5M12 15L12 19M9 12L5 12M15 12L19 12" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M12 5L10 7M12 5L14 7M12 19L10 17M12 19L14 17M5 12L7 10M5 12L7 14M19 12L17 10M19 12L17 14" stroke="#2d2d2d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              @foreach($selectedColumns as $col)
                @if($col == 'proposers_name')
                  <td data-column="proposers_name">
                    <a href="javascript:void(0)" onclick="openProposalDetails({{ $proposal->id }})" style="color:#007bff; text-decoration:underline;">{{ $proposal->proposers_name }}</a>
                  </td>
                @elseif($col == 'prid')
                  <td data-column="prid">
                    <a href="javascript:void(0)" onclick="openProposalDetails({{ $proposal->id }})" style="color:#007bff; text-decoration:underline;">{{ $proposal->prid }}</a>
                  </td>
                @elseif($col == 'insurer')
                  <td data-column="insurer">{{ $proposal->insurer }}</td>
                @elseif($col == 'policy_plan')
                  <td data-column="policy_plan">{{ $proposal->policy_plan }}</td>
                @elseif($col == 'sum_assured')
                  <td data-column="sum_assured">{{ $proposal->sum_assured ? number_format($proposal->sum_assured,2) : '##########' }}</td>
                @elseif($col == 'term')
                  <td data-column="term">{{ $proposal->term }}</td>
                @elseif($col == 'add_ons')
                  <td data-column="add_ons">{{ $proposal->add_ons ?? '-' }}</td>
                @elseif($col == 'offer_date')
                  <td data-column="offer_date">{{ $proposal->offer_date ? $proposal->offer_date->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'premium')
                  <td data-column="premium">{{ number_format($proposal->premium,2) }}</td>
                @elseif($col == 'frequency')
                  <td data-column="frequency">{{ $proposal->frequency }}</td>
                @elseif($col == 'stage')
                  <td data-column="stage">{{ $proposal->stage }}</td>
                @elseif($col == 'date')
                  <td data-column="date">{{ $proposal->date ? $proposal->date->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'age')
                  <td data-column="age">{{ $proposal->age }}</td>
                @elseif($col == 'status')
                  <td data-column="status"><span class="badge-status" style="background:{{ $proposal->status == 'Approved' ? '#28a745' : ($proposal->status=='Pending' ? '#ffc107' : ($proposal->status=='Declined' ? '#dc3545' : '#6c757d')) }}">{{ $proposal->status }}</span></td>
                @elseif($col == 'source_of_payment')
                  <td data-column="source_of_payment">{{ $proposal->source_of_payment }}</td>
                @elseif($col == 'mcr')
                  <td data-column="mcr">{{ $proposal->mcr ?? '-' }}</td>
                @elseif($col == 'doctor')
                  <td data-column="doctor">{{ $proposal->doctor ?? '-' }}</td>
                @elseif($col == 'date_sent')
                  <td data-column="date_sent">{{ $proposal->date_sent ? $proposal->date_sent->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'date_completed')
                  <td data-column="date_completed">{{ $proposal->date_completed ? $proposal->date_completed->format('d-M-y') : '##########' }}</td>
                @elseif($col == 'notes')
                  <td data-column="notes">{{ $proposal->notes ?? '-' }}</td>
                @elseif($col == 'agency')
                  <td data-column="agency">{{ $proposal->agency ?? '-' }}</td>
                @elseif($col == 'class')
                  <td data-column="class">{{ $proposal->class }}</td>
                @elseif($col == 'is_submitted')
                  <td data-column="is_submitted">{{ $proposal->is_submitted ? 'Yes' : 'No' }}</td>
                @endif
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    </div>

    <div class="footer">
      <div class="footer-left">
        <a class="btn btn-export" href="{{ route('life-proposals.export') }}">Export</a>
        <button class="btn btn-column" id="columnBtn" type="button">Column</button>
      </div>
      <div class="paginator">
        @php
          $base = url()->current();
          $q = request()->query();
          $current = $proposals->currentPage();
          $last = max(1,$proposals->lastPage());
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

  <!-- Proposal Page View (Full Page) -->
  <div class="client-page-view" id="proposalPageView" style="display:none;">
    <div class="client-page-header">
      <div class="client-page-title">
        <span id="proposalPageTitle">Life Proposal</span> - <span class="client-name" id="proposalPageName"></span>
      </div>
      <div class="client-page-actions">
        <button class="btn btn-edit" id="editProposalFromPageBtn" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Edit</button>
        <button class="btn" id="closeProposalPageBtn" onclick="closeProposalPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Close</button>
      </div>
    </div>
    <div class="client-page-body">
      <div class="client-page-content">
        <!-- Proposal Details View -->
        <div id="proposalDetailsPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div id="proposalDetailsContent" style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0; align-items:start; padding:12px;">
              <!-- Content will be loaded via JavaScript -->
            </div>
          </div>
        </div>
        
        <!-- Proposal Edit/Add Form -->
        <div id="proposalFormPageContent" style="display:none;">
          <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; overflow:hidden;">
            <div style="display:flex; justify-content:flex-end; align-items:center; padding:12px 15px; border-bottom:1px solid #ddd; background:#fff;">
              <div class="client-page-actions">
                <button type="button" class="btn-delete" id="proposalDeleteBtn" style="display:none; background:#dc3545; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;" onclick="deleteProposal()">Delete</button>
                <button type="submit" form="proposalPageForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:6px 16px; border-radius:2px; cursor:pointer;">Save</button>
                <button type="button" class="btn" id="closeProposalFormBtn" onclick="closeProposalPageView()" style="background:#e0e0e0; color:#000; border:none; padding:6px 16px; border-radius:2px; cursor:pointer; display:none;">Close</button>
              </div>
            </div>
            <form id="proposalPageForm" method="POST" action="{{ route('life-proposals.store') }}">
              @csrf
              <div id="proposalPageFormMethod" style="display:none;"></div>
              <div style="padding:12px;">
                <!-- Form content will be cloned from modal -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Proposal Modal (hidden, used for form structure) -->
  <div class="modal" id="proposalModal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="proposalModalTitle">Add Life Proposal</h4>
        <button type="button" class="modal-close" onclick="closeProposalModal()">×</button>
      </div>
      <form id="proposalForm" method="POST" action="{{ route('life-proposals.store') }}">
        @csrf
        <div id="proposalFormMethod" style="display:none;"></div>
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label for="proposers_name">Proposer's Name *</label>
              <input id="proposers_name" name="proposers_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="insurer">Insurer *</label>
              <select id="insurer" name="insurer" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['insurers'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="policy_plan">Policy Plan *</label>
              <select id="policy_plan" name="policy_plan" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['policy_plans'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="sum_assured">Sum Assured</label>
              <input id="sum_assured" name="sum_assured" type="number" step="0.01" class="form-control">
            </div>
            <div class="form-group">
              <label for="term">Term *</label>
              <input id="term" name="term" type="number" min="1" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="add_ons">Add Ons</label>
              <select id="add_ons" name="add_ons" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['add_ons'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="offer_date">Offer Date *</label>
              <input id="offer_date" name="offer_date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="premium">Premium *</label>
              <input id="premium" name="premium" type="number" step="0.01" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="frequency">Frequency *</label>
              <select id="frequency" name="frequency" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['frequencies'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="stage">Stage *</label>
              <select id="stage" name="stage" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['stages'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="date">Date *</label>
              <input id="date" name="date" type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="age">Age *</label>
              <input id="age" name="age" type="number" min="1" max="120" class="form-control" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="status">Status *</label>
              <select id="status" name="status" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['statuses'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="source_of_payment">Source Of Payment *</label>
              <select id="source_of_payment" name="source_of_payment" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['sources_of_payment'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="mcr">MCR</label>
              <input id="mcr" name="mcr" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="doctor">Doctor</label>
              <select id="doctor" name="doctor" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['doctors'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="date_sent">Date Sent</label>
              <input id="date_sent" name="date_sent" type="date" class="form-control">
            </div>
            <div class="form-group">
              <label for="date_completed">Date Completed</label>
              <input id="date_completed" name="date_completed" type="date" class="form-control">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
              <label for="agency">Agency</label>
              <select id="agency" name="agency" class="form-control">
                <option value="">Select</option>
                @foreach($lookupData['agencies'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="class">Class *</label>
              <select id="class" name="class" class="form-control" required>
                <option value="">Select</option>
                @foreach($lookupData['classes'] as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="prid">PRID</label>
              <input id="prid" name="prid" class="form-control" readonly>
            </div>
            <div class="form-group">
              <label for="is_submitted" style="display:block;">Submitted</label>
              <input id="is_submitted" name="is_submitted" type="checkbox" value="1">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel" onclick="closeProposalModal()">Cancel</button>
          <button type="button" class="btn-delete" id="proposalDeleteBtn" style="display:none;" onclick="deleteProposal()">Delete</button>
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>

@include('partials.column-selection-modal', [
  'selectedColumns' => $selectedColumns,
  'columnDefinitions' => $columnDefinitions,
  'mandatoryColumns' => $mandatoryColumns,
  'columnSettingsRoute' => route('life-proposals.save-column-settings'),
])

<script>
  let currentProposalId = null;
  const lookupData = @json($lookupData);
  const selectedColumns = @json($selectedColumns);

  // Format date helper function
  function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${date.getDate()}-${months[date.getMonth()]}-${String(date.getFullYear()).slice(-2)}`;
  }

  // Format number helper function
  function formatNumber(num) {
    if (!num && num !== 0) return '-';
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Open proposal details (full page view) - MUST be defined before HTML onclick handlers
  async function openProposalDetails(id) {
    try {
      const res = await fetch(`/life-proposals/${id}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const proposal = await res.json();
      currentProposalId = id;
      
      // Get all required elements
      const proposalPageName = document.getElementById('proposalPageName');
      const proposalPageTitle = document.getElementById('proposalPageTitle');
      const clientsTableView = document.getElementById('clientsTableView');
      const proposalPageView = document.getElementById('proposalPageView');
      const proposalDetailsPageContent = document.getElementById('proposalDetailsPageContent');
      const proposalFormPageContent = document.getElementById('proposalFormPageContent');
      const editProposalFromPageBtn = document.getElementById('editProposalFromPageBtn');
      const closeProposalPageBtn = document.getElementById('closeProposalPageBtn');
      
      if (!proposalPageName || !proposalPageTitle || !clientsTableView || !proposalPageView || 
          !proposalDetailsPageContent || !proposalFormPageContent) {
        console.error('Required elements not found');
        alert('Error: Page elements not found');
        return;
      }
      
      // Set proposal name in header
      const proposalName = proposal.proposers_name || proposal.prid || 'Unknown';
      proposalPageName.textContent = proposalName;
      proposalPageTitle.textContent = 'Life Proposal';
      
      populateProposalDetails(proposal);
      
      // Hide table view, show page view
      clientsTableView.classList.add('hidden');
      proposalPageView.style.display = 'block';
      proposalPageView.classList.add('show');
      proposalDetailsPageContent.style.display = 'block';
      proposalFormPageContent.style.display = 'none';
      if (editProposalFromPageBtn) editProposalFromPageBtn.style.display = 'inline-block';
      if (closeProposalPageBtn) closeProposalPageBtn.style.display = 'inline-block';
    } catch (e) {
      console.error(e);
      alert('Error loading proposal details: ' + e.message);
    }
  }

  // Populate proposal details view
  function populateProposalDetails(proposal) {
    const content = document.getElementById('proposalDetailsContent');
    if (!content) return;

    const col1 = `
      <div class="detail-section">
        <div class="detail-section-header">PROPOSAL DETAILS</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">PRID</span>
            <div class="detail-value">${proposal.prid || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Proposer's Name</span>
            <div class="detail-value">${proposal.proposers_name || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Insurer</span>
            <div class="detail-value">${proposal.insurer || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Policy Plan</span>
            <div class="detail-value">${proposal.policy_plan || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Class</span>
            <div class="detail-value">${proposal.class || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col2 = `
      <div class="detail-section">
        <div class="detail-section-header">COVERAGE</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Sum Assured</span>
            <div class="detail-value">${formatNumber(proposal.sum_assured)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Term</span>
            <div class="detail-value">${proposal.term || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Add Ons</span>
            <div class="detail-value">${proposal.add_ons || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Premium</span>
            <div class="detail-value">${formatNumber(proposal.premium)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Frequency</span>
            <div class="detail-value">${proposal.frequency || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col3 = `
      <div class="detail-section">
        <div class="detail-section-header">STATUS & DATES</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <div class="detail-value">${proposal.status || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Stage</span>
            <div class="detail-value">${proposal.stage || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Offer Date</span>
            <div class="detail-value">${formatDate(proposal.offer_date)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date</span>
            <div class="detail-value">${formatDate(proposal.date)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Age</span>
            <div class="detail-value">${proposal.age || '-'}</div>
          </div>
        </div>
      </div>
    `;

    const col4 = `
      <div class="detail-section">
        <div class="detail-section-header">MEDICAL & PAYMENT</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Source Of Payment</span>
            <div class="detail-value">${proposal.source_of_payment || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">MCR</span>
            <div class="detail-value">${proposal.mcr || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Doctor</span>
            <div class="detail-value">${proposal.doctor || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Sent</span>
            <div class="detail-value">${formatDate(proposal.date_sent)}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date Completed</span>
            <div class="detail-value">${formatDate(proposal.date_completed)}</div>
          </div>
        </div>
      </div>
      <div class="detail-section">
        <div class="detail-section-header">ADDITIONAL INFO</div>
        <div class="detail-section-body">
          <div class="detail-row">
            <span class="detail-label">Agency</span>
            <div class="detail-value">${proposal.agency || '-'}</div>
          </div>
          <div class="detail-row">
            <span class="detail-label">Submitted</span>
            <div class="detail-value">
              <input type="checkbox" ${proposal.is_submitted ? 'checked' : ''} disabled>
            </div>
          </div>
          <div class="detail-row" style="align-items:flex-start;">
            <span class="detail-label">Notes</span>
            <textarea class="detail-value" style="min-height:40px; resize:vertical; flex:1; font-size:11px; padding:4px 6px;" readonly>${proposal.notes || ''}</textarea>
          </div>
        </div>
      </div>
    `;

    content.innerHTML = col1 + col2 + col3 + col4;
  }

  // Open proposal page (Add or Edit)
  async function openProposalPage(mode) {
    if (mode === 'add') {
      openProposalForm('add');
    } else {
      if (currentProposalId) {
        openEditProposal(currentProposalId);
      }
    }
  }

  // Add Proposal Button
  document.getElementById('addProposalBtn').addEventListener('click', () => openProposalPage('add'));
  document.getElementById('columnBtn').addEventListener('click', () => openColumnModal());

  async function openEditProposal(id) {
    try {
      const res = await fetch(`/life-proposals/${id}/edit`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
      if (!res.ok) throw new Error('Network error');
      const proposal = await res.json();
      currentProposalId = id;
      openProposalForm('edit', proposal);
    } catch (e) {
      console.error(e);
      alert('Error loading proposal data');
    }
  }

  function openProposalForm(mode, proposal = null) {
    // Clone form from modal
    const modalForm = document.getElementById('proposalModal').querySelector('form');
    const pageForm = document.getElementById('proposalPageForm');
    const formContentDiv = pageForm.querySelector('div[style*="padding:12px"]');
    
    // Clone the modal form body
    const modalBody = modalForm.querySelector('.modal-body');
    if (modalBody && formContentDiv) {
      formContentDiv.innerHTML = modalBody.innerHTML;
    }

    const formMethod = document.getElementById('proposalPageFormMethod');
    const deleteBtn = document.getElementById('proposalDeleteBtn');
    const editBtn = document.getElementById('editProposalFromPageBtn');
    const closeBtn = document.getElementById('closeProposalPageBtn');
    const closeFormBtn = document.getElementById('closeProposalFormBtn');

    if (mode === 'add') {
      document.getElementById('proposalPageTitle').textContent = 'Add Life Proposal';
      document.getElementById('proposalPageName').textContent = '';
      pageForm.action = '{{ route("life-proposals.store") }}';
      formMethod.innerHTML = '';
      deleteBtn.style.display = 'none';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'inline-block';
      if (closeFormBtn) closeFormBtn.style.display = 'none';
      pageForm.reset();
    } else {
      const proposalName = proposal.proposers_name || proposal.prid || 'Unknown';
      document.getElementById('proposalPageTitle').textContent = 'Edit Life Proposal';
      document.getElementById('proposalPageName').textContent = proposalName;
      pageForm.action = `/life-proposals/${currentProposalId}`;
      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'PUT';
      formMethod.innerHTML = '';
      formMethod.appendChild(methodInput);
      deleteBtn.style.display = 'inline-block';
      if (editBtn) editBtn.style.display = 'none';
      if (closeBtn) closeBtn.style.display = 'none';
      if (closeFormBtn) closeFormBtn.style.display = 'inline-block';

      const fields = ['proposers_name','insurer','policy_plan','sum_assured','term','add_ons','offer_date','premium','frequency','stage','date','age','status','source_of_payment','mcr','doctor','date_sent','date_completed','notes','agency','class','prid'];
      fields.forEach(k => {
        const el = formContentDiv ? formContentDiv.querySelector(`#${k}`) : null;
        if (!el) return;
        if (el.type === 'checkbox') {
          el.checked = !!proposal[k];
        } else if (el.type === 'date') {
          el.value = proposal[k] ? (typeof proposal[k] === 'string' ? proposal[k].substring(0,10) : proposal[k]) : '';
        } else {
          el.value = proposal[k] ?? '';
        }
      });
      const isSubmittedCheckbox = formContentDiv ? formContentDiv.querySelector('#is_submitted') : null;
      if (isSubmittedCheckbox) {
        isSubmittedCheckbox.checked = !!proposal.is_submitted;
      }
    }

    // Hide table view, show page view
    document.getElementById('clientsTableView').classList.add('hidden');
    const proposalPageView = document.getElementById('proposalPageView');
    proposalPageView.style.display = 'block';
    proposalPageView.classList.add('show');
    document.getElementById('proposalDetailsPageContent').style.display = 'none';
    document.getElementById('proposalFormPageContent').style.display = 'block';
  }

  function closeProposalPageView() {
    const proposalPageView = document.getElementById('proposalPageView');
    proposalPageView.classList.remove('show');
    proposalPageView.style.display = 'none';
    document.getElementById('clientsTableView').classList.remove('hidden');
    document.getElementById('proposalDetailsPageContent').style.display = 'none';
    document.getElementById('proposalFormPageContent').style.display = 'none';
    currentProposalId = null;
  }

  // Edit button from details page
  const editBtn = document.getElementById('editProposalFromPageBtn');
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (currentProposalId) {
        openEditProposal(currentProposalId);
      }
    });
  }

  // Legacy function for backward compatibility
  function openProposalModal(mode, proposal = null) {
    if (mode === 'add') {
      openProposalPage('add');
    } else if (proposal && currentProposalId) {
      openEditProposal(currentProposalId);
    }
  }

  function closeProposalModal() {
    closeProposalPageView();
  }

  function deleteProposal() {
    if (!currentProposalId) return;
    if (!confirm('Delete this proposal?')) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/life-proposals/${currentProposalId}`;
    const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
    const method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE'; form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
  }
</script>

@include('partials.table-scripts', [
  'mandatoryColumns' => $mandatoryColumns,
])

@endsection
