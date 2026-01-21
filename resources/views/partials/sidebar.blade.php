
<div class="ks-sidebar-logo">
    <img src="{{ asset('asset/logo.png') }}" alt="Keystone Brokers" class="ks-sidebar-logo-img">
    <span class="ks-sidebar-logo-text">KEYSTONE BROKERS (PTY) LTD</span>
</div>

@php
    $currentRoute = request()->path();
    $query = request()->query();

    $isClientContext     = request()->has('client_id');
    $isPolicyContext     = request()->is('policies*') && !request()->has('client_id');
    $isPolicySchedule    = request()->is('schedules*') && request()->has('policy_id');
    $isPolicyPayment     = request()->is('payments*') && request()->has('policy_id');
    $isPolicyVehicle     = request()->is('vehicles*') && request()->has('policy_id');
    $isPolicyClaim       = request()->is('claims*') && request()->has('policy_id');
    $isPolicyDoc         = request()->is('documents*') && request()->has('policy_id');
    $isPolicyNominee     = request()->is('nominees*') && request()->has('policy_id');
    $isPolicyEndorment   = request()->is('endorsements*') && request()->has('policy_id');
    $isPolicyComision    = request()->is('commissions*') && request()->has('policy_id');
    $isProposalContext   = request()->is('life-proposals*') && !request()->has('client_id');
    $isProposalNominee   = request()->is('nominees*') && request()->has('life-proposal-id');
    $isClaimContext      = request()->is('claims*') && !request()->has('client_id');
    $isVehicleContext    = request()->is('vehicles*') && !request()->has('client_id');
    $isDocumentContext   = request()->is('documents*') && !request()->has('client_id');
@endphp

<ul class="ks-sidebar-menu">
    {{-- Dashboard --}}
    @if(auth()->check() && (auth()->user()->hasPermission('dashboard.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}" data-tooltip="Dashboard">
        <a href="/dashboard">
            <span class="ks-sidebar-label">Dashboard</span>
        </a>
    </li>
    @endif

    {{-- Calendar --}}
    @if(auth()->check() && (auth()->user()->hasPermission('calendar.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('calendar*') ? 'active' : '' }}" data-tooltip="Calendar">
        <a href="/calendar">
            <span class="ks-sidebar-label">Calendar</span>
        </a>
    </li>
    @endif

    {{-- Tasks --}}
    @if(auth()->check() && (auth()->user()->hasPermission('tasks.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('tasks*') ? 'active' : '' }}" data-tooltip="Tasks">
        <a href="/tasks">
            <span class="ks-sidebar-label">Tasks</span>
        </a>
    </li>
    @endif

    {{-- Contacts --}}
    @if(auth()->check() && (auth()->user()->hasPermission('contacts.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('contacts*') ? 'active' : '' }}" data-tooltip="Contacts">
        <a href="/contacts">
            <span class="ks-sidebar-label">Contacts</span>
        </a>
    </li>
    @endif

    {{-- Clients --}}
    @if(auth()->check() && (auth()->user()->hasPermission('clients.view') || auth()->user()->isAdmin()))
    <li class="{{ $isClientContext || request()->is('clients*') ? 'active' : '' }}" data-tooltip="Clients">
        <a href="/clients">
            <span class="ks-sidebar-label">Clients</span>
        </a>
    </li>
    @endif

    {{-- Life Proposals --}}
    @if(auth()->check() && (auth()->user()->hasPermission('life-proposals.view') || auth()->user()->isAdmin()))
    <li class="{{ (request()->is('life-proposals*') || $isProposalNominee) && !request()->has('client_id') ? 'active' : '' }}" data-tooltip="Life Proposals">
        <a href="/life-proposals">
            <span class="ks-sidebar-label">Life Proposals</span>
        </a>
    </li>
    @endif

    {{-- Policies --}}
    @if(auth()->check() && (auth()->user()->hasPermission('policies.view') || auth()->user()->isAdmin()))
    <li class="{{ (request()->is('policies*') || $isPolicyNominee || $isPolicyComision || $isPolicyEndorment || $isPolicySchedule || $isPolicyVehicle || $isPolicyPayment || $isPolicyClaim || $isPolicyDoc) && !request()->has('client_id') ? 'active' : '' }}" data-tooltip="Policies">
        <a href="/policies">
            <span class="ks-sidebar-label">Policies</span>
        </a>
    </li>
    @endif

    {{-- Commission --}}
    @if(auth()->check() && (auth()->user()->hasPermission('commissions.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('commissions*') && !request()->has('policy_id') ? 'active' : '' }}" data-tooltip="Commission">
        <a href="/commissions">
            <span class="ks-sidebar-label">Commission</span>
        </a>
    </li>
    @endif

    {{-- Statements --}}
    @if(auth()->check() && (auth()->user()->hasPermission('statements.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('statements*') ? 'active' : '' }}" data-tooltip="Statements">
        <a href="/statements">
            <span class="ks-sidebar-label">Statements</span>
        </a>
    </li>
    @endif

    {{-- Income --}}
    @if(auth()->check() && (auth()->user()->hasPermission('incomes.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('incomes*') ? 'active' : '' }}" data-tooltip="Income">
        <a href="/incomes">
            <span class="ks-sidebar-label">Income</span>
        </a>
    </li>
    @endif

    {{-- Expenses --}}
    @if(auth()->check() && (auth()->user()->hasPermission('expenses.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('expenses*') ? 'active' : '' }}" data-tooltip="Expenses">
        <a href="/expenses">
            <span class="ks-sidebar-label">Expenses</span>
        </a>
    </li>
    @endif

    {{-- Claims --}}
    @if(auth()->check() && (auth()->user()->hasPermission('claims.view') || auth()->user()->isAdmin()))
    <li class="{{ (!request()->has('client_id') && !request()->has('policy_id') && request()->is('claims*')) ? 'active' : '' }}" data-tooltip="Claims">
        <a href="/claims?pending=1">
            <span class="ks-sidebar-label">Claims</span>
        </a>
    </li>
    @endif

    {{-- Vehicles --}}
    @if(auth()->check() && (auth()->user()->hasPermission('vehicles.view') || auth()->user()->isAdmin()))
    <li class="{{ (request()->is('vehicles*') && !$isPolicyVehicle) && (!request()->has('client_id')) ? 'active' : '' }}" data-tooltip="Vehicles">
        <a href="/vehicles">
            <span class="ks-sidebar-label">Vehicles</span>
        </a>
    </li>
    @endif

    {{-- Documents --}}
    @if(auth()->check() && (auth()->user()->hasPermission('documents.view') || auth()->user()->isAdmin()))
    <li class="{{ (!request()->has('client_id') && !request()->has('policy_id') && request()->is('documents*')) ? 'active' : '' }}" data-tooltip="Documents">
        <a href="/documents">
            <span class="ks-sidebar-label">Documents</span>
        </a>
    </li>
    @endif

    {{-- Reports --}}
    @if(auth()->check() && (auth()->user()->hasPermission('reports.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('reports*') ? 'active' : '' }}" data-tooltip="Reports">
        <a href="/reports">
            <span class="ks-sidebar-label">Reports</span>
        </a>
    </li>
    @endif

    {{-- Settings --}}
    @if(auth()->check() && (auth()->user()->hasPermission('settings.view') || auth()->user()->isAdmin()))
    <li class="{{ request()->is('settings*') ? 'active' : '' }}" data-tooltip="Settings">
        <a href="/settings">
            <span class="ks-sidebar-label">Settings</span>
        </a>
    </li>
    @endif
</ul>
