<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'Laravel'))
    
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User Profile /</span> Profile</h4>

        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                            <img
                                src="{{ optional(Auth::user()?->avatar)->path
                                    ? asset('storage/' . Auth::user()?->avatar->path)
                                    : asset('back-office/assets/img/avatars/1' . '.png') }}"
                                alt="user image"
                                class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img"
                            />
                        </div>
                        <div class="flex-grow-1 mt-3 mt-sm-5">
                            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4" >
                                <div class="user-profile-info">
                                    <h4>{{ Auth::user()?->name }}</h4>
                                    <ul
                                        class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2"
                                    >
                                        <li class="list-inline-item"><i class="ti ti-color-swatch"></i> {{ Auth::user()?->roles()?->first()?->name }}</li>
                                        <li class="list-inline-item"><i class="ti ti-calendar"></i> {{ getDateFormat(Auth::user()?->doj) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Header -->

        <!-- Navbar pills -->
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" 
                        data-bs-target="#navs-profile" aria-controls="navs-profile" 
                        aria-selected="true"><i class="ti-xs ti ti-user-check me-1"></i> Profile</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" 
                        data-bs-target="#navs-teams" aria-controls="navs-teams" 
                        aria-selected="true"><i class="ti-xs ti ti-users me-1"></i> Teams</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" 
                        data-bs-target="#navs-password" aria-controls="navs-password" 
                        aria-selected="true"><i class="ti ti-lock me-1 ti-xs"></i>Password</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="navs-profile" role="tabpanel">
                        <div class="card-body">
                            <!-- User Profile Content -->
                            <div class="row">
                                <div class="col-xl-4 col-lg-5 col-md-5">
                                    <!-- About User -->
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <small class="card-text text-uppercase">About</small>
                                            <ul class="list-unstyled mb-4 mt-3">
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-user"></i><span class="fw-bold mx-2">Name:</span> <span>{{ auth()->user()->name }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-check"></i><span class="fw-bold mx-2">Status:</span> <span>{{ ucfirst(auth()->user()->status->name) }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-crown"></i><span class="fw-bold mx-2">Role:</span> <span>{{ auth()->user()?->roles()?->first()?->name }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-file-description"></i><span class="fw-bold mx-2">Languages:</span>
                                                    <span>English</span>
                                                </li>
                                            </ul>
                                            <small class="card-text text-uppercase">Contacts</small>
                                            <ul class="list-unstyled mb-4 mt-3">
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-phone-call"></i><span class="fw-bold mx-2">Contact:</span>
                                                    <span>{{ auth()->user()->phone ?? 'N/A' }}</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-mail"></i><span class="fw-bold mx-2">Email:</span>
                                                    <span>{{ auth()->user()->email }}</span>
                                                </li>
                                            </ul>
                                            <small class="card-text text-uppercase">Teams</small>
                                            <ul class="list-unstyled mb-0 mt-3">
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-brand-angular text-danger me-2"></i>
                                                    <div class="d-flex flex-wrap">
                                                    <span class="fw-bold me-2">Backend Developer</span><span>(126 Members)</span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!--/ About User -->
                                </div>
                                <div class="col-xl-8 col-lg-7 col-md-7">
                                    <!-- Activity Timeline -->
                                    <div class="card card-action mb-4">
                                    <div class="card-header align-items-center">
                                        <h5 class="card-action-title mb-0">Activity Timeline</h5>
                                        <div class="card-action-element">
                                        <div class="dropdown">
                                            <button
                                            type="button"
                                            class="btn dropdown-toggle hide-arrow p-0"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            >
                                            <i class="ti ti-dots-vertical text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="javascript:void(0);">Share timeline</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                                            <li>
                                                <hr class="dropdown-divider" />
                                            </li>
                                            <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                                            </ul>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="card-body pb-0">
                                        <ul class="timeline ms-1 mb-0">
                                        <li class="timeline-item timeline-item-transparent">
                                            <span class="timeline-point timeline-point-primary"></span>
                                            <div class="timeline-event">
                                            <div class="timeline-header">
                                                <h6 class="mb-0">Client Meeting</h6>
                                                <small class="text-muted">Today</small>
                                            </div>
                                            <p class="mb-2">Project meeting with john @10:15am</p>
                                            <div class="d-flex flex-wrap">
                                                <div class="avatar me-2">
                                                <img src="{{ asset('back-office') }}/assets/img/avatars/3.png" alt="Avatar" class="rounded-circle" />
                                                </div>
                                                <div class="ms-1">
                                                <h6 class="mb-0">Lester McCarthy (Client)</h6>
                                                <span>CEO of Infibeam</span>
                                                </div>
                                            </div>
                                            </div>
                                        </li>
                                        <li class="timeline-item timeline-item-transparent">
                                            <span class="timeline-point timeline-point-success"></span>
                                            <div class="timeline-event">
                                            <div class="timeline-header">
                                                <h6 class="mb-0">Create a new project for client</h6>
                                                <small class="text-muted">2 Day Ago</small>
                                            </div>
                                            <p class="mb-0">Add files to new design folder</p>
                                            </div>
                                        </li>
                                        <li class="timeline-item timeline-item-transparent">
                                            <span class="timeline-point timeline-point-danger"></span>
                                            <div class="timeline-event">
                                            <div class="timeline-header">
                                                <h6 class="mb-0">Shared 2 New Project Files</h6>
                                                <small class="text-muted">6 Day Ago</small>
                                            </div>
                                            <p class="mb-2">
                                                Sent by Mollie Dixon
                                                <img
                                                src="{{ asset('back-office') }}/assets/img/avatars/4.png"
                                                class="rounded-circle me-3"
                                                alt="avatar"
                                                height="24"
                                                width="24"
                                                />
                                            </p>
                                            <div class="d-flex flex-wrap gap-2 pt-1">
                                                <a href="javascript:void(0)" class="me-3">
                                                <img
                                                    src="{{ asset('back-office') }}/assets/img/icons/misc/doc.png"
                                                    alt="Document image"
                                                    width="15"
                                                    class="me-2"
                                                />
                                                <span class="fw-semibold text-heading">App Guidelines</span>
                                                </a>
                                                <a href="javascript:void(0)">
                                                <img
                                                    src="{{ asset('back-office') }}/assets/img/icons/misc/xls.png"
                                                    alt="Excel image"
                                                    width="15"
                                                    class="me-2"
                                                />
                                                <span class="fw-semibold text-heading">Testing Results</span>
                                                </a>
                                            </div>
                                            </div>
                                        </li>
                                        <li class="timeline-item timeline-item-transparent border-0">
                                            <span class="timeline-point timeline-point-info"></span>
                                            <div class="timeline-event">
                                            <div class="timeline-header">
                                                <h6 class="mb-0">Project status updated</h6>
                                                <small class="text-muted">10 Day Ago</small>
                                            </div>
                                            <p class="mb-0">Woocommerce iOS App Completed</p>
                                            </div>
                                        </li>
                                        </ul>
                                    </div>
                                    </div>
                                    <!--/ Activity Timeline -->
                                </div>
                            </div>
                            <!--/ User Profile Content -->
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-password" role="tabpanel">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10 pl-md-2 pt-md-0 pt-sm-4 pt-4">
                                    <div class="tab-content px-primary">
                                        <div id="Change Password-1" class="tab-pane fade active show">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="d-flex align-items-center text-capitalize mb-0 title tab-content-header">
                                                    Change Password
                                                </h5>
                                                <div class="d-flex align-items-center mb-0"></div>
                                            </div>
                                            <hr />
                                            <div class="content py-primary" id="change-password">
                                                <div class="content" id="Change Password-1">
                                                    <form class="ajax-form" id="create-form" data-modal-id="change-password" action="{{ route('back-office.auth.change-password') }}" data-method="POST">
                                                        @csrf

                                                        <div class="form-group" placeholder="Enter old password" show-password="true">
                                                            <div class="row align-items-center">
                                                                <div class="col-lg-3 col-xl-3 col-md-3 col-sm-12">
                                                                    <label class="text-left d-block mb-lg-0">
                                                                        Old password <span class="text-danger">*</span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-8 col-xl-8 col-md-8 col-sm-12">
                                                                    <div class="form-password-toggle">
                                                                        <div class="input-group">
                                                                            <input type="password" class="form-control" id="old_password" name="old_password" placeholder="············" aria-describedby="basic-default-password2" />
                                                                            <span id="old_password" class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                            </div>
                                                                            <span id="old_password_error" class="text-danger error"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group mt-2">
                                                                <div class="row">
                                                                    <div class="col-lg-3 col-xl-3">
                                                                        <label for="input-text-new-password" class="text-left d-block mb-2 mb-lg-0">
                                                                            New password <span class="text-danger">*</span>
                                                                        </label>
                                                                    </div>
                                                                    <div class="col-lg-8 col-xl-8">
                                                                        <div class="form-password-toggle">
                                                                            <div class="input-group">
                                                                                <input type="password" class="form-control" id="password" name="password" placeholder="············" aria-describedby="basic-default-password2" />
                                                                                <span id="password" class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                                </div>
                                                                                <span id="password_error" class="text-danger error"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-2">
                                                            <div class="row align-items-center">
                                                                <div class="col-lg-3 col-xl-3 col-md-3 col-sm-12">
                                                                    <label class="text-left d-block mb-lg-0">
                                                                        Confirm password <span class="text-danger">*</span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-8 col-xl-8 col-md-8 col-sm-12">
                                                                    <div class="form-password-toggle">
                                                                        <div class="input-group">
                                                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="············" aria-describedby="basic-default-password2" />
                                                                            <span id="password_confirmation" class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                                                            <div class="fv-plugins-message-container invalid-feedback">
                                                                            </div>
                                                                            <span id="password_confirmation_error" class="text-danger error"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mt-5 mb-0">
                                                            <button type="submit" class="btn text-center btn-primary">
                                                                <span class="w-100">
                                                                    Save
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-teams" role="tabpanel">
                        <!-- Teams Cards -->
                        <div class="row g-4">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <a href="javascript:;" class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <img
                                            src="{{ asset('back-office') }}/assets/img/icons/brands/react-label.png"
                                            alt="Avatar"
                                            class="rounded-circle"
                                            />
                                        </div>
                                        <div class="me-2 text-body h5 mb-0">React Developers</div>
                                        </a>
                                        <div class="ms-auto">
                                        <ul class="list-inline mb-0 d-flex align-items-center">
                                            <li class="list-inline-item me-0">
                                            <a href="javascript:void(0);" class="text-body"
                                                ><i class="ti ti-star text-muted me-1"></i
                                            ></a>
                                            </li>
                                            <li class="list-inline-item">
                                            <div class="dropdown">
                                                <button
                                                type="button"
                                                class="btn dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                >
                                                <i class="ti ti-dots-vertical text-muted"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename Team</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View Details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);">Delete Team</a>
                                                </li>
                                                </ul>
                                            </div>
                                            </li>
                                        </ul>
                                        </div>
                                    </div>
                                    <p class="mb-3">
                                        We don’t make assumptions about the rest of your technology stack, so you can develop new
                                        features in React.
                                    </p>
                                    <div class="d-flex align-items-center pt-1">
                                        <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Vinnie Mostowy"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/5.png" alt="Avatar" />
                                            </li>
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Allen Rieske"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/12.png" alt="Avatar" />
                                            </li>
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Julee Rossignol"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/6.png" alt="Avatar" />
                                            </li>
                                            <li class="avatar avatar-sm">
                                            <span
                                                class="avatar-initial rounded-circle pull-up"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="8 more"
                                                >+8</span
                                            >
                                            </li>
                                        </ul>
                                        </div>
                                        <div class="ms-auto">
                                        <a href="javascript:;" class="me-2"><span class="badge bg-label-primary">React</span></a>
                                        <a href="javascript:;"><span class="badge bg-label-warning">Vue.JS</span></a>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <a href="javascript:;" class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <img
                                            src="{{ asset('back-office') }}/assets/img/icons/brands/vue-label.png"
                                            alt="Avatar"
                                            class="rounded-circle"
                                            />
                                        </div>
                                        <div class="me-2 text-body h5 mb-0">Vue.js Dev Team</div>
                                        </a>
                                        <div class="ms-auto">
                                        <ul class="list-inline mb-0 d-flex align-items-center">
                                            <li class="list-inline-item me-0">
                                            <a href="javascript:void(0);" class="text-body"
                                                ><i class="ti ti-star text-muted me-1"></i
                                            ></a>
                                            </li>
                                            <li class="list-inline-item">
                                            <div class="dropdown">
                                                <button
                                                type="button"
                                                class="btn dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                >
                                                <i class="ti ti-dots-vertical text-muted"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename Team</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View Details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);">Delete Team</a>
                                                </li>
                                                </ul>
                                            </div>
                                            </li>
                                        </ul>
                                        </div>
                                    </div>
                                    <p class="mb-3">
                                        The development of Vue and its ecosystem is guided by an international team, some of whom have
                                        chosen to be featured below.
                                    </p>
                                    <div class="d-flex align-items-center pt-1">
                                        <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Kaith D'souza"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/5.png" alt="Avatar" />
                                            </li>
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="John Doe"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/1.png" alt="Avatar" />
                                            </li>
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Alan Walker"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/6.png" alt="Avatar" />
                                            </li>
                                            <li class="avatar avatar-sm">
                                            <span
                                                class="avatar-initial rounded-circle pull-up"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="14 more"
                                                >+14</span
                                            >
                                            </li>
                                        </ul>
                                        </div>
                                        <div class="ms-auto">
                                        <a href="javascript:;"><span class="badge bg-label-danger">Developer</span></a>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <a href="javascript:;" class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <img src="{{ asset('back-office') }}/assets/img/icons/brands/xd-label.png" alt="Avatar" class="rounded-circle" />
                                        </div>
                                        <div class="me-2 text-body h5 mb-0">Creative Designers</div>
                                        </a>
                                        <div class="ms-auto">
                                        <ul class="list-inline mb-0 d-flex align-items-center">
                                            <li class="list-inline-item me-0">
                                            <a href="javascript:void(0);" class="text-body"
                                                ><i class="ti ti-star text-muted me-1"></i
                                            ></a>
                                            </li>
                                            <li class="list-inline-item">
                                            <div class="dropdown">
                                                <button
                                                type="button"
                                                class="btn dropdown-toggle hide-arrow p-0"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                >
                                                <i class="ti ti-dots-vertical text-muted"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Rename Team</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">View Details</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">Add to favorites</a></li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);">Delete Team</a>
                                                </li>
                                                </ul>
                                            </div>
                                            </li>
                                        </ul>
                                        </div>
                                    </div>
                                    <p class="mb-3">
                                        A design or product team is more than just the people on it. A team includes the people, the
                                        roles they play.
                                    </p>
                                    <div class="d-flex align-items-center pt-1">
                                        <div class="d-flex align-items-center">
                                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Jimmy Ressula"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/4.png" alt="Avatar" />
                                            </li>
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Kristi Lawker"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/2.png" alt="Avatar" />
                                            </li>
                                            <li
                                            data-bs-toggle="tooltip"
                                            data-popup="tooltip-custom"
                                            data-bs-placement="top"
                                            title="Danny Paul"
                                            class="avatar avatar-sm pull-up"
                                            >
                                            <img class="rounded-circle" src="{{ asset('back-office') }}/assets/img/avatars/7.png" alt="Avatar" />
                                            </li>
                                            <li class="avatar avatar-sm">
                                            <span
                                                class="avatar-initial rounded-circle pull-up"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="19 more"
                                                >+19</span
                                            >
                                            </li>
                                        </ul>
                                        </div>
                                        <div class="ms-auto">
                                        <a href="javascript:;" class="me-2"><span class="badge bg-label-warning">Sketch</span></a>
                                        <a href="javascript:;"><span class="badge bg-label-danger">XD</span></a>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ Teams Cards -->
                    </div>
                </div>
            </div>
        </div>
        <!--/ Navbar pills -->
    </div>
</x-app-layout>