<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('back-office.auth.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </span>
            <span class="app-brand-text demo text-body fw-bold ms-1">{{ config('app.name', 'Laravel') }}</span>
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
                <i class="menu-icon tf-icons ti ti-tags"></i>
                <div data-i18n="Leads List">Leads List</div>
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
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Roles List">Roles List</div>
            </a>
        </li>
        @endcan
    </ul>
</aside>