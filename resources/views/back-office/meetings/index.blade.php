<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'Laravel'))
    @push('css')
        <!-- Vendors -->
        <link rel="stylesheet" type="text/css" href="{{ asset('back-office/assets/vendor/css/pages/app-calendar.css') }}">
        <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/flatpickr/flatpickr.css') }}">
        <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/quill/typography.css') }}">
        <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/quill/editor.css') }}">
        <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/fullcalendar/fullcalendar.css') }}">
    @endpush

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card app-calendar-wrapper">
            <!-- Tabs Navigation -->
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="meetingTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="calendar-tab" data-bs-toggle="tab" href="#calendar-view" role="tab">
                            Calendar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="list-tab" data-bs-toggle="tab" href="#list-view" role="tab">
                            Meeting List
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tabs Content -->
            <div class="card-body tab-content">
                <!-- Calendar Tab -->
                <div class="tab-pane fade show active" id="calendar-view" role="tabpanel">
                    <div class="row g-0">
                        <!-- Calendar Sidebar -->
                        <div class="col app-calendar-sidebar" id="app-calendar-sidebar">
                            <div class="border-bottom p-4 my-sm-0 mb-3">
                                <div class="d-grid">
                                    <!-- Removed Add Event Button -->
                                    <div class="d-flex align-items-center" style="padding: 0.5rem 1rem;">
                                        <h5 class="mb-0" style="font-weight: 500;">
                                            <i class="ti ti-calendar me-1"></i> Full Calendar
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <!-- inline calendar (flatpicker) -->
                                <div class="inline-calendar"></div>

                                <hr class="container-m-nx mb-4 mt-3" />

                                <!-- Filter -->
                                <div class="mb-3 ms-3">
                                    <small class="text-small text-muted text-uppercase align-middle">Filter</small>
                                </div>

                                <div class="form-check mb-2 ms-3">
                                    <input
                                        class="form-check-input select-all"
                                        type="checkbox"
                                        id="selectAll"
                                        data-value="all"
                                        checked
                                    />
                                    <label class="form-check-label" for="selectAll">View All</label>
                                </div>

                                <div class="app-calendar-events-filter ms-3">
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input input-filter"
                                            type="checkbox"
                                            id="select-business"
                                            data-value="business"
                                            checked
                                        />
                                        <label class="form-check-label" for="select-business">Meetings</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Calendar Sidebar -->

                        <!-- Calendar & Modal -->
                        <div class="col app-calendar-content">
                            <div class="card shadow-none border-0">
                                <div class="card-body pb-0">
                                    <!-- FullCalendar -->
                                    <div id="calendar"></div>
                                </div>
                            </div>
                            <div class="app-overlay"></div>
                            <!-- FullCalendar Offcanvas -->
                            <div
                                class="offcanvas offcanvas-end event-sidebar"
                                tabindex="-1"
                                id="addEventSidebar"
                                aria-labelledby="addEventSidebarLabel"
                            >
                                <div class="offcanvas-header my-1">
                                    <h5 class="offcanvas-title" id="addEventSidebarLabel">Meeting Details</h5>
                                    <button
                                        type="button"
                                        class="btn-close text-reset"
                                        data-bs-dismiss="offcanvas"
                                        aria-label="Close"
                                    ></button>
                                </div>
                                <div class="offcanvas-body pt-0">
                                    <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                        <div class="mb-3">
                                            <label class="form-label" for="eventTitle">Lead </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="eventTitle"
                                                name="eventTitle"
                                                placeholder="Event Title"
                                                readonly
                                            />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="eventStartDate">Start Date</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="eventStartDate"
                                                name="eventStartDate"
                                                placeholder="Start Date"
                                                readonly
                                            />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="eventEndDate">End Date</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="eventEndDate"
                                                name="eventEndDate"
                                                placeholder="End Date"
                                                readonly
                                            />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="eventDescription">Description</label>
                                            <textarea class="form-control" name="eventDescription" id="eventDescription" readonly></textarea>
                                        </div>
                                        <div class="mb-3 d-flex justify-content-start my-4">
                                            <button
                                                type="button"
                                                class="btn btn-label-secondary btn-cancel me-1"
                                                data-bs-dismiss="offcanvas"
                                            >
                                                Close
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /Calendar & Modal -->
                    </div>
                </div>
                <!-- Meeting List Tab -->
                <div class="tab-pane fade" id="list-view" role="tabpanel">
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="card mb-4">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card-header">
                                            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> {{ module_label('list', $title) }} 
                                                <span class="badge rounded-pill px-3 py-2 bg-primary text-white">
                                                    {{ $total_count }} {{ Str::plural($singularLabel, $total_count) }}
                                                </span> 
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="dt-buttons btn-group flex-wrap float-end mt-4">
                                            <button id="refresh-record" class="btn btn-success mx-2" title="Refresh Records"><i class="ti ti-refresh me-0 ti-xs"></i></button>
                                        
                                            @can($permissionPrefix.'-create')
                                            <x-action-button
                                                type="button"
                                                id="add-btn"
                                                btn-class="btn btn-primary add-btn mb-3 mb-md-0 mx-2"
                                                title="Add {{ $singularLabel }}"
                                                label="Add {{ $singularLabel }}"
                                                icon="ti ti-plus me-0 me-sm-1 ti-xs"
                                                data-bs-toggle="modal"
                                                data-bs-target="#create-pop-up-modal-for-file"
                                                :data-attributes="[
                                                    'data-url' => route($routeInitialize.'.store'),
                                                    'data-create-url' => route($routeInitialize.'.create')
                                                ]"
                                            />
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Users List Table -->
                            <div class="card">
                                <div class="card-datatable table-responsive">
                                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                        <div class="container">
                                            <table class="dt-row-grouping table dataTable dtr-column data_table">
                                                <thead>
                                                    <tr>
                                                        @foreach($dataTable->headers() as $header)
                                                            <th>{{ $header }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody id="body"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <x-modals size="modal-lg" />
    <!--/ Modals -->

    @push('js')
        <!-- Vendors -->
        <script src="{{ asset('back-office/assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('back-office/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
        <script src="{{ asset('back-office/assets/vendor/libs/quill/quill.js') }}"></script>
        <script src="{{ asset('back-office/assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>

        <!-- FormValidation -->
        <script src="{{ asset('back-office/assets/vendor/libs/formvalidation/dist/js/FormValidation.full.min.js') }}"></script>
        <script src="{{ asset('back-office/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
        <script src="{{ asset('back-office/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

        <!-- Vuexy Calendar Scripts -->
        <script src="{{ asset('back-office/assets/js/app-calendar-events.js') }}"></script>
        <script src="{{ asset('back-office/assets/js/app-calendar.js') }}"></script>

        <script type="text/javascript">
            const pageUrl = "{{ url()->current() }}";
            const columns = @json($dataTable->jsColumns());

            initializeDataTable(pageUrl, columns);

            $('#refresh-record').on('click', function(){
                $('.table').DataTable().ajax.reload();
            });
        </script>
    @endpush
</x-app-layout>