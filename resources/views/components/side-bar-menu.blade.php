<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
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
        <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">Dashboard</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('dashboard')?'active':'' }}">
                    <a href="{{ route('dashboard') }}" class="menu-link">
                        <div data-i18n="Analytics">Analytics</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('leads')?'active':'' }}">
                    <a href="{{ route('leads.index') }}" class="menu-link">
                        <div data-i18n="All Leads">All Leads</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Apps &amp; Pages</span>
        </li>
    
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-users"></i>
            <div data-i18n="Users">Users</div>
            </a>
            <ul class="menu-sub">
            <li class="menu-item">
                <a href="app-user-list.html" class="menu-link">
                <div data-i18n="List">List</div>
                </a>
            </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-settings"></i>
            <div data-i18n="Roles & Permissions">Roles & Permissions</div>
            </a>
            <ul class="menu-sub">
            <li class="menu-item">
                <a href="app-access-roles.html" class="menu-link">
                <div data-i18n="Roles">Roles</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="app-access-permission.html" class="menu-link">
                <div data-i18n="Permission">Permission</div>
                </a>
            </li>
            </ul>
        </li>
    </ul>
</aside>