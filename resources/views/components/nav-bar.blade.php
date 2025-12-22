<nav
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
    >
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>

    <!-- Welcome Message (Desktop only, 2 lines) -->
    <div class="d-none d-md-flex flex-column me-auto" style="max-width: 500px;">
        <!-- First line: Name + Role -->
        <div class="fw-semibold text-primary text-truncate" 
            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            Welcome, 
        </div>

        <!-- Second line: Last login -->
        <div class="text-muted text-truncate" 
            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.95rem;">
            {{ Auth::user()?->name }} ({{ Auth::user()?->roles()?->first()?->name ?? 'No Role' }})
        </div>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            @php 
                $unreadNotifications = auth()->user()->unreadNotifications;
            @endphp 
            <!-- Notification -->

            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                >
                    <i class="ti ti-bell ti-md"></i>
                    <span class="badge bg-danger rounded-pill badge-notifications" id="notif-count">{{ count($unreadNotifications) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">{{ module_label('notification', '') }}</h5>
                            <a href="javascript:void(0);"
                                title="Mark all as read"
                                class="show text-body text-decoration-none"
                                data-bs-toggle="modal"
                                data-bs-target="#"
                                data-show-url="{{ route('back-office.notifications.mark-all-read') }}"
                            >
                                <i class="ti ti-mail-opened fs-4"></i>
                            </a>
                        </div>
                    </li>
                    <div class="notification-scroll">
                        @if(count($unreadNotifications) == 0)
                            <li class="dropdown-notifications-list no-notifications">
                                <div class="text-center p-3">
                                    <p class="mb-0">{{ module_label('no_new_notification', '') }}</p>
                                </div>
                            </li>
                        @else
                            <div class="">
                                @foreach ($unreadNotifications as $notification)
                                    <li class="dropdown-notifications-list scrollable-container">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar">
                                                            <img 
                                                                src="{{ $notification->data['assigner_avatar'] }}" 
                                                                alt class="h-auto rounded-circle" 
                                                                style="width:50px; height: 50px !important;"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <a href="javascript:void(0);"
                                                                title="Show Details"
                                                                class="notification-title show text-body text-decoration-none"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#details-modal"
                                                                data-show-url="{{ $notification->data['url'] }}"
                                                                data-id="{{ $notification->id }}">
                                                                {{ $notification->data['title'] }} 🎉
                                                            </a>
                                                        </h6>
                                                        <a href="javascript:void(0);"
                                                            title="Read Notification"
                                                            class="show text-body text-decoration-none"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#"
                                                            data-show-url="{{ route('back-office.notifications.show', $notification->id) }}"
                                                        >
                                                            <p class="mb-0">{{ $notification->data['message'] }}</p>
                                                        </a>
                                                        @php
                                                        $created = $notification->created_at;

                                                        if ($created->isToday()) {
                                                            $humanTime = $created->diffForHumans();
                                                        } elseif ($created->isYesterday()) {
                                                            $humanTime = 'Yesterday at ' . $created->format('h:i A');
                                                        } else {
                                                            $humanTime = $created->format('M d \a\t h:i A');
                                                        }
                                                        @endphp
                                                        <small class="text-muted">{{ $humanTime }}</small>
                                                    </div>
                                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                                            <span class="badge badge-dot"></span>
                                                        </a>
                                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                                            <span class="ti ti-x"></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <li class="dropdown-menu-footer border-top">
                        <a href="{{ route('back-office.notifications.index') }}" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                            {{ module_label('view_all_notifications', '') }}
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ Notification -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img class="rounded-circle"
                            src="{{ optional(Auth::user()?->avatar)->path
                                    ? asset('storage/' . Auth::user()?->avatar->path)
                                    : asset('back-office/assets/img/avatars/default-avatar.png') }}"
                            width="36" height="36" alt="Avatar">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('back-office.auth.profile') }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img class="rounded-circle"
                                            src="{{ optional(Auth::user()?->avatar)->path
                                                    ? asset('storage/' . Auth::user()?->avatar->path)
                                                    : asset('back-office/assets/img/avatars/default-avatar.png') }}"
                                            width="36" height="36" alt="Avatar">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ Auth::user()?->name }}</span>
                                    <small class="text-muted">{{ Auth::user()?->roles()?->first()?->name }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('back-office.auth.profile') }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">{{ module_label('my_profile', '') }}</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('back-office.auth.logout') }}">
                            <i class="ti ti-logout me-2 ti-sm"></i>
                            <span class="align-middle">{{ module_label('log_out', '') }}</span>
                        </a>
                    </li>
                    <!-- Stop impersonation -->
                    @impersonating($guard = null)
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('impersonate.leave') }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">{{ module_label('stop_impersonate', '') }}</span>
                        </a>
                    </li>
                    @endImpersonating
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>