@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/schedules-index.css') }}">



@if(session('success'))
  <div class="alert alert-success" id="successAlert" style="padding:8px 12px; margin-bottom:12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724;">
    {{ session('success') }}
    <button type="button" class="alert-close" onclick="document.getElementById('successAlert').style.display='none'" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">×</button>
  </div>
@endif

<div class="dashboard">
  <div style="background:#fff; border:1px solid #ddd; border-radius:4px; margin-bottom:15px; padding:15px 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="page-title-section">
              <h3 style="margin:0; font-size:18px; font-weight:600;">
                
                
              @if($policy)
                {{ $policy->policy_code }} - 
              @endif
              
              @if($policy)
                 <span class="client-name" style="color:#f3742a; font-size:20px; font-weight:500;"> Schedules</span>
              @else
                 <span class="client-name" > Schedules</span>
              @endif
              </h3>
           </div>
      </div>
  </div>
  <div class="container-table" style="background:#fff; border:1px solid #ddd; border-radius:4px; padding:15px 20px;">

    <div class="top-bar">
      <div class="left-group">
        <div class="records-found">Records Found - {{ $schedules->total() }}</div>
          <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer;">
              <span style="font-size:13px;">Filter</span>
              @php
                $hasFollowUp = request()->has('follow_up') && (request()->follow_up == 'true' || request()->follow_up == '1');
                $hasSubmitted = request()->has('submitted') && (request()->submitted == 'true' || request()->submitted == '1');
              @endphp
              <input type="checkbox" id="filterToggle" {{ $hasFollowUp || $hasSubmitted ? 'checked' : '' }}>
            </label>
    
      </div>
      <div class="action-buttons">
        <!-- <button class="btn btn-add" onclick="openScheduleModal('add')">Add</button> -->
        @if($policy && $policy->client_id)
          <button class="btn btn-back" onclick="window.location.href='{{ route('policies.index', ['client_id' => $policy->client_id, 'policy_id' => $policy->id]) }}'">Back</button>
        @else
          <button class="btn btn-back" onclick="window.history.back()">Back</button>
        @endif
      </div>
    </div>

    <div class="table-responsive" id="tableResponsive" style="overflow-x:auto;">
      <table style="min-width:1800px;">
        <thead>
          <tr>
            <th style="text-align:center; width:40px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle;">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 2 16 2 16H22C22 16 19 14.25 19 9C19 5.13 15.87 2 12 2Z" fill="#fff" stroke="#fff" stroke-width="1.5"/>
                <path d="M9 21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21H9Z" fill="#fff"/>
              </svg>
            </th>
            <th style="width:50px;">Action</th>
            <th>Year</th>
            <th>Status</th>
            <th>Policy Plan</th>
            <th>Sum Insured</th>
            <th>Inclusions</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Base Premium</th>
            <th>Full Premium</th>
            <th>FOP</th>
            <th>NOP</th>
            <th>Pay Plan</th>
            <th>Regn No</th>
            <th>Term</th>
            <th>Insured Item</th>
            <th>Excess</th>
            <th>Note</th>
            <th>Schedule ID</th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedules as $schedule)
            @php
              $p = $schedule->policy;
              $isExpired = $schedule->effective_to && $schedule->effective_to->isPast();
              $isInForce = !$isExpired && $schedule->effective_from && $schedule->effective_from->isPast();

              // Get year from effective_from
              $year = $schedule->effective_from ? $schedule->effective_from->format('Y') : '-';

              // Status display
              $statusText = $isExpired ? 'Expired' : ($isInForce ? 'In Force' : ucfirst($schedule->status ?? 'Draft'));

              // Inclusions from policy (WSC, LOU, PA)
              $inclusions = [];
              if ($p && $p->wsc) $inclusions[] = 'WSC ' . number_format($p->wsc/1000) . 'k';
              if ($p && $p->lou) $inclusions[] = 'LOU ' . number_format($p->lou/1000) . 'k';
              if ($p && $p->pa) $inclusions[] = 'PA' . number_format($p->pa/1000) . 'k';
              $inclusionsText = count($inclusions) > 0 ? implode(', ', $inclusions) : '-';

              // Get vehicle registration if exists
              $regnNo = '-';
              if ($p && $p->vehicles && $p->vehicles->count() > 0) {
                $regnNo = $p->vehicles->first()->registration_no ?? '-';
              }

              // Pay plan name
              $payPlanName = $p && $p->payPlan ? $p->payPlan->name : '-';

              // Policy plan name
              $policyPlanName = $p && $p->policyPlan ? $p->policyPlan->name : '-';

              // Frequency name (FOP)
              $fopName = $p && $p->frequency ? substr($p->frequency->name, 0, 1) : '-';

              // NOP (Number of Payments) - calculate from term and frequency
              $nop = '-';
              if ($p && $p->term && $p->frequency) {
                $freqName = strtolower($p->frequency->name ?? '');
                if (str_contains($freqName, 'annual') || str_contains($freqName, 'yearly')) {
                  $nop = $p->term;
                } elseif (str_contains($freqName, 'monthly')) {
                  $nop = $p->term * 12;
                } elseif (str_contains($freqName, 'quarter')) {
                  $nop = $p->term * 4;
                } else {
                  $nop = $p->term;
                }
              }
            @endphp

            <tr class="{{ $isExpired ? 'expired-row' : ($isInForce ? '' : 'dfr-row') }}">
              <td class="bell-cell {{ $isExpired ? 'expired' : ($isInForce ? '' : 'dfr') }}">
                <div style="display:flex; align-items:center; justify-content:center;">
                  <div class="status-indicator {{ $isExpired ? 'expired' : 'normal' }}" style="width:18px; height:18px; border-radius:50%; border:2px solid {{ $isExpired ? '#dc3545' : '#f3742a' }}; background-color:{{ $isExpired ? '#dc3545' : ($isInForce ? '#f3742a' : 'transparent') }};"></div>
                </div>
              </td>
              <td class="action-cell">
                @if($p)
                  <a href="{{ route('policies.index', ['client_id' => $p->client_id, 'policy_id' => $p->id]) }}">
                    <img src="{{ asset('asset/arrow-expand.svg') }}"
                        class="action-expand"
                        width="22" height="22"
                        style="cursor:pointer; vertical-align:middle;"
                        alt="View Policy">
                  </a>
                @endif
              </td>
              <td>{{ $year }}</td>
              <td>
                <span style="color:{{ $isInForce ? '#28a745' : ($isExpired ? '#dc3545' : '#666') }}; font-weight:500;">
                  {{ $statusText }}
                </span>
              </td>
              <td>{{ $policyPlanName }}</td>
              <td style="text-align:right;">{{ $p ? number_format($p->sum_insured ?? 0, 2) : '-' }}</td>
              <td>{{ $inclusionsText }}</td>
              <td>{{ $schedule->effective_from ? $schedule->effective_from->format('d-M-y') : '-' }}</td>
              <td>{{ $schedule->effective_to ? $schedule->effective_to->format('d-M-y') : '-' }}</td>
              <td style="text-align:right;">{{ $p ? number_format($p->base_premium ?? 0, 2) : '-' }}</td>
              <td style="text-align:right;">{{ $p ? number_format($p->premium ?? 0, 2) : '-' }}</td>
              <td style="text-align:center;">{{ $fopName }}</td>
              <td style="text-align:center;">{{ $nop }}</td>
              <td>{{ $payPlanName }}</td>
              <td>{{ $regnNo }}</td>
              <td style="text-align:center;">{{ $p->term ?? '-' }}</td>
              <td>{{ $p->insured_item ?? '-' }}</td>
              <td>{{ $p->excess ?? '-' }}</td>
              <td>{{ $schedule->notes ?? '-' }}</td>
              <td>{{ $schedule->schedule_no }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="20" style="text-align:center; padding:20px; color:#999;">No schedules found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="footer">
      {{ $schedules->links() }}
    </div>
  </div>
</div>

<!-- Add/Edit Schedule Modal -->
<div class="modal" id="scheduleModal">
  <div class="modal-content" style="max-width:800px; max-height:90vh; overflow-y:auto;">
    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd; background:#fff;">
      <h4 id="scheduleModalTitle" style="margin:0; font-size:18px; font-weight:bold;">Add Schedule</h4>
      <div style="display:flex; gap:10px;">
        <button type="submit" form="scheduleForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Save</button>
        <button type="button" class="btn-cancel" onclick="closeScheduleModal()" style="background:#ccc; color:#000; border:none; padding:8px 20px; border-radius:2px; cursor:pointer; font-size:13px;">Cancel</button>
      </div>
    </div>
    <form id="scheduleForm" method="POST" action="{{ route('schedules.store') }}">
      @csrf
      <div id="scheduleFormMethod" style="display:none;"></div>
      <div class="modal-body" style="padding:20px;">
        <div class="form-row full-width" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="policy_id" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Policy *</label>
            <select id="policy_id" name="policy_id" class="form-control" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">Select Policy</option>
              @foreach($policies as $policy)
                <option value="{{ $policy->id }}">
                  {{ $policy->policy_no }} - {{ $policy->client->client_name ?? 'N/A' }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="schedule_no" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Schedule Number *</label>
            <input type="text" id="schedule_no" name="schedule_no" class="form-control" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
          <div class="form-group" style="flex:1;">
            <label for="status" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Status *</label>
            <select id="status" name="status" class="form-control" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="draft">Draft</option>
              <option value="active">Active</option>
              <option value="expired">Expired</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="issued_on" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Issued On</label>
            <input type="date" id="issued_on" name="issued_on" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
          <div class="form-group" style="flex:1;">
            <label for="effective_from" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Effective From</label>
            <input type="date" id="effective_from" name="effective_from" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>
        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="effective_to" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Effective To</label>
            <input type="date" id="effective_to" name="effective_to" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>
        <div class="form-row full-width" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1 1 100%;">
            <label for="notes" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Notes</label>
            <textarea id="notes" name="notes" class="form-control" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px; resize:vertical;"></textarea>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal" id="filterModal">
  <div class="modal-content" style="max-width:800px; max-height:90vh; overflow-y:auto;">
    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid #ddd; background:#fff;">
      <h4 id="filterModalTitle" style="margin:0; font-size:18px; font-weight:bold;">Filters</h4>
      <div style="display:flex; gap:10px;">
        <button type="submit" form="filterForm" class="btn-save" style="background:#f3742a; color:#fff; border:none; padding:8px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Apply</button>
        <button type="button" class="btn-cancel" onclick="closeFilterModal()" style="background:#ccc; color:#000; border:none; padding:8px 16px; border-radius:2px; cursor:pointer; font-size:13px;">Close</button>
      </div>
    </div>

    <form id="filterForm" method="GET" action="{{ route('schedules.index') }}">

      <div class="modal-body" style="padding:20px;">
        <!-- New Filter Fields -->
        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="set_record_lines" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Set Record Lines</label>
            <input type="number" id="set_record_lines" name="set_record_lines" value="{{ request('set_record_lines', 15) }}" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
          <div class="form-group" style="flex:1;">
            <label for="search_term" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Search Term</label>
            <input type="text" id="search_term" name="search_term" value="{{ request('search_term') }}" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>

        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="client_id" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Client</label>
            <select id="client_id" name="client_id" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">All</option>
              @if(isset($clients))
                @foreach($clients as $c)
                  <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->client_name }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="form-group" style="flex:1;">
            <label for="filter_policy_id" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Policy Number</label>
            <select id="filter_policy_id" name="policy_id" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">All</option>
              @if(isset($policies))
                @foreach($policies as $p)
                  <option value="{{ $p->id }}" data-client-id="{{ $p->client_id }}" {{ request('policy_id') == $p->id ? 'selected' : '' }}>{{ $p->policy_no }} - {{ $p->client->client_name ?? '' }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>

        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="insurer" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Insurer <span style="color:#dc3545; font-weight:700;">•</span></label>
            <select id="insurer" name="insurer" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">All</option>
              @if(isset($insurers))
                @foreach($insurers as $ins)
                  <option value="{{ $ins->id }}" {{ request('insurer') == $ins->id ? 'selected' : '' }}>{{ $ins->name }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="form-group" style="flex:1;">
            <label for="insurance_class" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Insurance Class</label>
            <select id="insurance_class" name="insurance_class" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">All</option>
              @if(isset($policyClasses))
                @foreach($policyClasses as $pc)
                  <option value="{{ $pc->id }}" {{ request('insurance_class', 'Motor') == $pc->id ? 'selected' : '' }}>{{ $pc->name }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>

        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="agency" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Agency</label>
            <select id="agency" name="agency" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">All</option>
=                @foreach($agencies as $a)
                  <option value="{{ $a->id }}" {{ request('agency') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
          </div>
          <div class="form-group" style="flex:1;">
            <label for="agent" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Agent</label>
            <select id="agent" name="agent" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">All</option>
              @if(isset($agents))
                @foreach($agents as $ag)
                  <option value="{{ $ag }}" {{ request('agent') == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>

        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="status_filter" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Status</label>
            <select id="status_filter" name="status_filter" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
              <option value="">All</option>
              <option value="draft" {{ request('status_filter')=='draft' ? 'selected' : '' }}>Draft</option>
              <option value="active" {{ request('status_filter')=='active' ? 'selected' : '' }}>Active</option>
              <option value="expired" {{ request('status_filter')=='expired' ? 'selected' : '' }}>Expired</option>
              <option value="cancelled" {{ request('status_filter')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>
          <div class="form-group" style="flex:1;">
            <label for="from_start_date" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">From Start Date</label>
            <input type="date" id="from_start_date" name="from_start_date" value="{{ request('from_start_date') }}" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>

        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="from_end_date" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">From End Date</label>
            <input type="date" id="from_end_date" name="from_end_date" value="{{ request('from_end_date') }}" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
          <div class="form-group" style="flex:1;">
            <label for="premium_unpaid" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Premium Unpaid</label>
            <input type="number" id="premium_unpaid" name="premium_unpaid" value="{{ request('premium_unpaid') }}" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>

        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
          <div class="form-group" style="flex:1;">
            <label for="commission_unpaid" style="display:block; margin-bottom:5px; font-size:13px; font-weight:500;">Commission Unpaid</label>
            <input type="number" id="commission_unpaid" name="commission_unpaid" value="{{ request('commission_unpaid') }}" class="form-control" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:2px; font-size:13px;">
          </div>
        </div>

      </div>
    </form>
  </div>
</div>



  <script>


    // Initialize data from Blade
    const schedulesStoreRoute = '{{ route("schedules.store") }}';
    const schedulesUpdateRouteTemplate = '{{ route("schedules.update", ":id") }}';
  </script>
<script src="{{ asset('js/schedules-index.js') }}"></script>
@endsection

