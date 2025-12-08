<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('back-office.auth.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </span>
            <span class="app-brand-text demo text-body fw-bold ms-1">{{ config('app.name', '100KEYS UAE') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->is('back-office/auth/dashboard') ? 'active' : '' }}">
            <a href="{{ route('back-office.auth.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-home-2"></i>
                <div data-i18n="Dashboards">Dashboard</div>
            </a>
        </li>

        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Apps &amp; Pages</span>
        </li>

        @can('lead-list')
        <li class="menu-item {{ request()->is('back-office/leads') || request()->is('back-office/leads/*')?'active open':'' }}">
            <a href="{{ route('back-office.leads.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user-search"></i>
                <div data-i18n="Leads List">Leads List</div>
            </a>
        </li>
        @endcan

        @can('faq-list')
        <li class="menu-item {{ request()->is('back-office/faqs') || request()->is('back-office/faqs/*')?'active open':'' }}">
            <a href="{{ route('back-office.faqs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-info-circle"></i>
                <div data-i18n="Faqs List">Faqs List</div>
            </a>
        </li>
        @endcan

        @can('lead_capture-list')
        <li class="menu-item {{ request()->is('back-office/lead-captures') || request()->is('back-office/lead-captures/*')?'active open':'' }}">
            <a href="{{ route('back-office.lead-captures.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-clipboard-list"></i>
                <div data-i18n="Lead Captures List">Lead Captures List</div>
            </a>
        </li>
        @endcan

        @can('campaign-list')
        <li class="menu-item {{ request()->is('back-office/campaigns') || request()->is('back-office/campaigns/*')?'active open':'' }}">
            <a href="{{ route('back-office.campaigns.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-rocket"></i>
                <div data-i18n="Campaigns List">Campaigns List</div>
            </a>
        </li>
        @endcan

        @can('user-list')
        <li class="menu-item {{ request()->is('back-office/users') || request()->is('back-office/users/*')?'active open':'' }}">
            <a href="{{ route('back-office.users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Agents List">Agents List</div>
            </a>
        </li>
        @endcan
        @can('role-list')
        <li class="menu-item {{ request()->is('back-office/roles') || request()->is('back-office/roles/*')?'active open':'' }}">
            <a href="{{ route('back-office.roles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-shield-check"></i>
                <div data-i18n="Roles List">Roles List</div>
            </a>
        </li>
        @endcan
        @can('activity_log-list')
        <li class="menu-item {{ request()->is('back-office/activity-logs') || request()->is('back-office/activity-logs/*')?'active open':'' }}">
            <a href="{{ route('back-office.activity-logs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-activity"></i>
                <div data-i18n="Activity Logs List">Activity Logs List</div>
            </a>
        </li>
        @endcan

        <li class="menu-item {{ request()->is('back-office/meetings') || request()->is('back-office/roles/*')?'active open':'' }}">
            <a href="{{ route('back-office.meetings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-calendar"></i>   
                <div data-i18n="Meetings List">Meetings List</div>
            </a>
        </li>
    </ul>
</aside>