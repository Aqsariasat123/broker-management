
<div class="ks-sidebar-logo">
  <label class="ks-sidebar-logo-text custom-logo" id="login-title"><span class="ks-sidebar-orange">Key</span>stone</label>
</div>
             @php
                      $currentRoute = request()->path();
                    $query = request()->query();

                    $isClientContext     = request()->has('client_id');
                    $isPolicyContext     = request()->is('policies*') && !request()->has('client_id');
                    $isPolicySchedule    = request()->is('schedules*') && request()->has('policy_id');
                    $isPolicyPayment   = request()->is('payments*') && request()->has('policy_id');
                    $isPolicyVehicle   = request()->is('vehicles*') && request()->has('policy_id');
                    $isPolicyClaim = request()->is('claims*') && request()->has('policy_id');
                    $isPolicyDoc = request()->is('documents*') && request()->has('policy_id');
                    $isPolicyNominee   = request()->is('nominees*') && request()->has('policy_id');

                    $isPolicyEndorment = request()->is('endorsements*') && request()->has('policy_id');
                    $isPolicyComision = request()->is('commissions*') && request()->has('policy_id');
                    $isProposalContext   = request()->is('life-proposals*') && !request()->has('client_id');
                    $isProposalNominee   = request()->is('nominees*') && request()->has('life-proposal-id');
                    $isClaimContext      = request()->is('claims*') && !request()->has('client_id');
                    $isVehicleContext    = request()->is('vehicles*') && !request()->has('client_id');
                    $isDocumentContext   = request()->is('documents*') && !request()->has('client_id');
                    

                @endphp

        <ul class="ks-sidebar-menu">
            @if(auth()->check() && (auth()->user()->hasPermission('dashboard.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}" data-tooltip="Dashboard">
                <a href="/dashboard">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V11C3 10.7348 3.10536 10.4804 3.29289 10.2929L11.2929 2.29289C11.6834 1.90237 12.3166 1.90237 12.7071 2.29289L20.7071 10.2929C20.8946 10.4804 21 10.7348 21 11V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H15M9 21V17C9 16.4696 9.21071 15.9609 9.58579 15.5858C9.96086 15.2107 10.4696 15 11 15H13C13.5304 15 14.0391 15.2107 14.4142 15.5858C14.7893 15.9609 15 16.4696 15 17V21M9 21H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Dashboard</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('calendar.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('calendar*') ? 'active' : '' }}" data-tooltip="Calendar">
                <a href="/calendar">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M16 2V6M8 2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Calendar</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('tasks.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('tasks*') ? 'active' : '' }}" data-tooltip="Tasks">
                <a href="/tasks">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Tasks</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('contacts.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('contacts*') ? 'active' : '' }}" data-tooltip="Contacts">
                <a href="/contacts">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Contacts</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('clients.view') || auth()->user()->isAdmin()))
        
                <li class="{{ $isClientContext ||  request()->is('clients*') ? 'active' : '' }}" data-tooltip="Clients">
                <a href="/clients">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 7H4C2.89543 7 2 7.89543 2 9V19C2 20.1046 2.89543 21 4 21H20C21.1046 21 22 20.1046 22 19V9C22 7.89543 21.1046 7 20 7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 21V17C16 15.8954 15.1046 15 14 15H10C8.89543 15 8 15.8954 8 17V21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 11C13.6569 11 15 9.65685 15 8C15 6.34315 13.6569 5 12 5C10.3431 5 9 6.34315 9 8C9 9.65685 10.3431 11 12 11Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Clients</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('life-proposals.view') || auth()->user()->isAdmin()))
            <li class="{{ (request()->is('life-proposals*') || $isProposalNominee) && !request()->has('client_id') ? 'active' : '' }}" data-tooltip="Proposals">
                <a href="/life-proposals">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 8V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Proposals</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('policies.view') || auth()->user()->isAdmin()))
            <li class="{{ (request()->is('policies*') || $isPolicyNominee || $isPolicyComision || $isPolicyEndorment || $isPolicySchedule || $isPolicyVehicle || $isPolicyPayment || $isPolicyClaim || $isPolicyDoc )  && !request()->has('client_id') ? 'active' : '' }}" data-tooltip="Policies">
                <a href="/policies">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L4 5V11C4 16.55 7.16 21.74 12 23C16.84 21.74 20 16.55 20 11V5L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 8V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 16H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Policies</span>
                </a>
            </li>
            @endif
<!--             
            @if(auth()->check() && (auth()->user()->hasPermission('schedules.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('schedules.*') ? 'active' : '' }}" data-tooltip="Schedules">
                <a href="{{ route('schedules.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Schedules</span>
                </a>
            </li>
            @endif
             -->
            @if(auth()->check() && (auth()->user()->hasPermission('commissions.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('commissions*') && !request()->has('policy_id') ? 'active' : '' }}" data-tooltip="Commission">
                <a href="/commissions">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 12H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Commission</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('statements.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('statements*') ? 'active' : '' }}" data-tooltip="Statements">
                <a href="/statements">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M3 9H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M9 21V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Statements</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('incomes.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('incomes*') ? 'active' : '' }}" data-tooltip="Income">
                <a href="/incomes">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 7V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Income</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('expenses.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('expenses*') ? 'active' : '' }}" data-tooltip="Expenses">
                <a href="/expenses">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 3V21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M17 6H7C5.89543 6 5 6.89543 5 8V16C5 17.1046 5.89543 18 7 18H17C18.1046 18 19 17.1046 19 16V8C19 6.89543 18.1046 6 17 6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M15 14H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Expenses</span>
                </a>
            </li>
            @endif
            
            <!-- @if(auth()->check() && (auth()->user()->hasPermission('payment-plans.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('payment-plans.*') ? 'active' : '' }}" data-tooltip="Payment Plans">
                <a href="{{ route('payment-plans.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M6 3V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M18 3V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="3" y="8" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M8 13H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8 17H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Payment Plans</span>
                </a>
            </li>
            @endif -->
            
            <!-- @if(auth()->check() && (auth()->user()->hasPermission('debit-notes.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('debit-notes.*') ? 'active' : '' }}" data-tooltip="Debit Notes">
                <a href="{{ route('debit-notes.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M9 16H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M6 20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 4V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M16 4V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Debit Notes</span>
                </a>
            </li>
            @endif
             -->
            <!-- @if(auth()->check() && (auth()->user()->hasPermission('payments.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('payments.*') ? 'active' : '' }}" data-tooltip="Payments">
                <a href="{{ route('payments.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="1" y="4" width="22" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M1 10H23" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M7 14H7.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M12 14H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M17 14H17.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Payments</span>
                </a>
            </li>
            @endif -->
            
            @if(auth()->check() && (auth()->user()->hasPermission('claims.view') || auth()->user()->isAdmin()))
            <li class="{{  (!request()->has('client_id') && !request()->has('policy_id')  &&  request()->is('claims*')) ? 'active' : '' }}" data-tooltip="Claims">
                <a href="/claims?pending=1">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 8V12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M12 16H12.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12Z" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Claims</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('vehicles.view') || auth()->user()->isAdmin()))
            <li class="{{  (request()->is('vehicles*') &&  !$isPolicyVehicle)  &&   (!request()->has('client_id') ) ? 'active' : '' }}" data-tooltip="Vehicles">
                <a href="/vehicles">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 17H3C2.44772 17 2 16.5523 2 16V10C2 9.44772 2.44772 9 3 9H5L7 5H17L19 9H21C21.5523 9 22 9.44772 22 10V16C22 16.5523 21.5523 17 21 17H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="7" cy="17" r="2" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="17" cy="17" r="2" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Vehicles</span>
                </a>
            </li>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('documents.view') || auth()->user()->isAdmin()))
            <li class="{{  (!request()->has('client_id') && !request()->has('policy_id') && request()->is('documents*')) ? 'active' : '' }}" data-tooltip="Documents">
                <a href="/documents">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 7C3 5.89543 3.89543 5 5 5H9L12 8H19C20.1046 8 21 8.89543 21 10V17C21 18.1046 20.1046 19 19 19H5C3.89543 19 3 18.1046 3 17V7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 5V9H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Documents</span>
                </a>
            </li>
            @endif
            
         
           
            @if(auth()->check() && (auth()->user()->hasPermission('lookups.manage') || auth()->user()->isAdmin()))
            <!-- Web Settings Nested Menu -->
            <li class="ks-sidebar-section-header" style="padding: 10px 15px; margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.1);">
                <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.6); font-weight: 600;">Web Settings</span>
            </li>
            
            <!-- <li class="{{ request()->routeIs('lookup-categories.*') ? 'active' : '' }}" data-tooltip="Lookup Categories">
                <a href="{{ route('lookup-categories.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M4 12H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M4 18H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8 3V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M8 15V21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Lookup Categories</span>
                </a>
            </li> -->
            
            <li class="{{ request()->routeIs('lookup-values.*') ? 'active' : '' }}" data-tooltip="Lookup Values">
                <a href="{{ route('lookup-values.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V5C15 6.10457 14.1046 7 13 7H11C9.89543 7 9 6.10457 9 5V5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 12H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M9 16H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Lookup Values</span>
                </a>
            </li>
            @endif
               @if(auth()->check() && (auth()->user()->hasPermission('lookups.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->is('lookups*') ? 'active' : '' }}" data-tooltip="lookups">
                <a href="/lookups">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Lookups Category</span>
                </a>
            </li>
            @endif
            
            @auth
            @if(auth()->check() && (auth()->user()->hasPermission('users.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}" data-tooltip="Users">
                <a href="{{ route('users.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Users</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasPermission('permissions.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}" data-tooltip="Permissions">
                <a href="{{ route('permissions.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 1L15.09 8.26L23 9.27L17 14.14L18.18 22.02L12 18.77L5.82 22.02L7 14.14L1 9.27L8.91 8.26L12 1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Permissions</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasPermission('roles.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}" data-tooltip="Roles">
                <a href="{{ route('roles.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Roles</span>
                </a>
            </li>
            @endif
            @if(auth()->check() && (auth()->user()->hasPermission('audit-logs.view') || auth()->user()->isAdmin()))
            <li class="{{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" data-tooltip="Audit Logs">
                <a href="{{ route('audit-logs.index') }}">
                    <span class="ks-sidebar-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M12 6V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="ks-sidebar-label">Audit Logs</span>
                </a>
            </li>
            @endif
            @endauth
        </ul>
        