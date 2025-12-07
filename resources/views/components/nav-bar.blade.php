<nav
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
    >
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
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
                <span class="badge bg-danger rounded-pill badge-notifications">{{ count($unreadNotifications) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">Notification</h5>
                            <a
                                href="javascript:void(0)"
                                class="dropdown-notifications-all text-body"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Mark all as read">
                                <i class="ti ti-mail-opened fs-4"></i>
                            </a>
                        </div>
                    </li>
                    @if(count($unreadNotifications) == 0)
                        <li class="dropdown-notifications-list scrollable-container">
                            <div class="text-center p-3">
                                <p class="mb-0">No new notifications</p>
                            </div>
                        </li>
                    @else
                        @foreach ($unreadNotifications as $notification)
                            <li class="dropdown-notifications-list scrollable-container">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar">
                                                    @if(Auth::check())
                                                        @php
                                                        $user = auth()->user();
                                                        $avatarPath = optional($user->avatar)->path
                                                                ? asset('storage/' . $user->avatar->path)
                                                                : asset('back-office/assets/img/avatars/default-avatar.png');
                                                        @endphp
                                                        <img src="{{ asset('back-office') }}/assets/img/avatars/{{ $avatarPath }}" alt class="h-auto rounded-circle" />
                                                    @else
                                                        <img src="{{ asset('back-office') }}/assets/img/avatars/default-avatar.png" alt class="h-auto rounded-circle" />
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="javascript:void(0);"
                                                        class="notification-title show"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#show-xl-modal"
                                                        data-show-url="{{ $notification->data['url'] }}"
                                                        data-id="{{ $notification->id }}">
                                                        {{ $notification->data['title'] }} 🎉
                                                    </a>
                                                </h6>
                                                <p class="mb-0">{{ $notification->data['message'] }}</p>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
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
                    @endif
                    <li class="dropdown-menu-footer border-top">
                        <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                            View all notifications
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
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('back-office.auth.logout') }}">
                            <i class="ti ti-logout me-2 ti-sm"></i>
                            <span class="align-middle">Log Out</span>
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
                            <span class="align-middle">Stop Impersonate</span>
                        </a>
                    </li>
                    @endImpersonating
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>