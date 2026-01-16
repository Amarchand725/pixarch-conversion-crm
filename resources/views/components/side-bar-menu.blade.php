<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('back-office.auth.dashboard') }}" class="app-brand-link">
            {{-- <span class="app-brand-logo demo">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </span>
            <span class="app-brand-text demo text-body fw-bold ms-1">{{ config('app.name', '100KEYS UAE') }}</span> --}}

            <span class="app-brand-logo demo">
                <x-application-logo class="logo-full" />
                <x-favicon class="logo-mini" />
            </span>

            <span class="app-brand-text demo menu-text fw-bold ms-2">
                {{ config('app.name') }}
            </span>

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

        @can('notification-list')
        <li class="menu-item {{ request()->is('back-office/notifications') || request()->is('back-office/notifications/*')?'active open':'' }}">
            <a href="{{ route('back-office.notifications.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-bell"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Notifications') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['notifications'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        @can('meeting-list')
        <li class="menu-item {{ request()->is('back-office/meetings') || request()->is('back-office/meetings/*')?'active open':'' }}">
            <a href="{{ route('back-office.meetings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-calendar"></i>   
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Meetings') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['meetings'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        @can('lead-list')
        <li class="menu-item {{ request()->is('back-office/leads') || request()->is('back-office/leads/*')?'active open':'' }}">
            <a href="{{ route('back-office.leads.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user-search"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Leads') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['leads'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        @can('lead_report-list')
        <li class="menu-item {{ request()->is('back-office/lead-reports') || request()->is('back-office/lead-reports/*')?'active open':'' }}">
            <a href="{{ route('back-office.lead-reports.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user-search"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Lead Reports') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['lead Reports'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        @can('user-list')
        <li class="menu-item {{ request()->is('back-office/users') || request()->is('back-office/users/*')?'active open':'' }}">
            <a href="{{ route('back-office.users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Agents') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['users'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('role-list')
        <li class="menu-item {{ request()->is('back-office/roles') || request()->is('back-office/roles/*')?'active open':'' }}">
            <a href="{{ route('back-office.roles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-shield-check"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Roles') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['roles'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        @can('lead_capture-list')
        <li class="menu-item {{ request()->is('back-office/lead-captures') || request()->is('back-office/lead-captures/*')?'active open':'' }}">
            <a href="{{ route('back-office.lead-captures.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-clipboard-list"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Lead Captures') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['lead_captures'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan

        @can('campaign-list')
        <li class="menu-item {{ request()->is('back-office/campaigns') || request()->is('back-office/campaigns/*')?'active open':'' }}">
            <a href="{{ route('back-office.campaigns.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-rocket"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Campaigns') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['campaigns'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('faq-list')
        <li class="menu-item {{ request()->is('back-office/faqs') || request()->is('back-office/faqs/*')?'active open':'' }}">
            <a href="{{ route('back-office.faqs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-info-circle"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Faqs') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['faqs'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
        @can('activity_log-list')
        <li class="menu-item {{ request()->is('back-office/activity-logs') || request()->is('back-office/activity-logs/*')?'active open':'' }}">
            <a href="{{ route('back-office.activity-logs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-activity"></i>
                <div class="d-flex justify-content-between w-100">
                    <span>{{ module_label('list', 'Activity Logs') }}</span>

                    <span class="badge bg-primary">
                        {{ $sidebarCounts['activity_logs'] ?? 0 }}
                    </span>
                </div>
            </a>
        </li>
        @endcan
    </ul>
</aside>