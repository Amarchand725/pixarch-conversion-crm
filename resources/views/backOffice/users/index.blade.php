<x-app-layout>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-header">
                            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> {{ $title }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dt-buttons btn-group flex-wrap float-end mt-4">
                            <button id="refresh-record" class="btn btn-success mx-2" title="Refresh Records"><i class="ti ti-refresh me-0 ti-xs"></i></button>
                            <a data-toggle="tooltip" data-placement="top" title="All Trashed Records" href="" class="btn btn-label-danger mx-2">
                                <span>
                                    <i class="ti ti-trash me-0 me-sm-1 ti-xs"></i>
                                    <span class="d-none d-sm-inline-block">All Trashed Records </span>
                                </span>
                            </a>
                        
                            <button
                                id="add-btn"
                                data-toggle="tooltip" data-placement="top" title="Add "
                                data-url=""
                                data-create-url=""
                                class="btn btn-primary add-btn mb-3 mb-md-0 mx-2"
                                tabindex="0" aria-controls="DataTables_Table_0"
                                type="button" data-bs-toggle="modal"
                                data-bs-target="#create-pop-up-modal-for-file">
                                <span>
                                    <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                    <span class="d-none d-sm-inline-block"> Add  </span>
                                </span>
                            </button>
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
                                        <th>Avatar</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Created_at</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="body">
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                <img class="rounded-circle"
                                                    src="{{ optional($user?->avatar)->path
                                                            ? asset('backOffice/assets/' . $user?->avatar->path)
                                                            : asset('backOffice/assets/img/avatars/' . rand(1, 10) . '.png') }}"
                                                    width="36" height="36" alt="Avatar">
                                                </div>
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->status }}</td>
                                            <td>{{ $user->created_at }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="ti ti-dots-vertical ti-sm mx-1"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end m-0">
                                                        <a href="#"
                                                            class="dropdown-item show"
                                                            tabindex="0" aria-controls="DataTables_Table_0"
                                                            type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#details-modal"
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="Announcement Details"
                                                            data-show-url=""
                                                            >
                                                            View Details
                                                        </a>
                                                        
                                                        <a href="#"
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="Edit Announcement"
                                                            data-edit-url=""
                                                            data-url=""
                                                            class="dropdown-item edit-btn"
                                                            type="button"
                                                            tabindex="0"
                                                            aria-controls="DataTables_Table_0"
                                                            type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#offcanvasAddAnnouncement"
                                                            >
                                                            Edit
                                                        </a>

                                                        <a href="javascript:;" class="dropdown-item delete" >Delete</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modals -->
    {{-- <x-modals /> --}}
    <!--/ Modals -->

    @push('js')
        <script type="text/javascript">
            var table = $('.data_table').DataTable();
            if ($.fn.DataTable.isDataTable('.data_table')) {
                table.destroy();
            }
            $(document).ready(function(){
                var page_url = $('#page_url').val();
                var table = $('.data_table').DataTable({
                    processing:true,
                    serverSide:true,
                    ajax: page_url+"?loaddata=yes",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        {data: 'avatar', name:'avatar'},
                        {data: 'name', name:'name'},
                        {data: 'email', name:'email'},
                        {data: 'phone', name:'phone'},
                        {data: 'status', name:'status'},
                        {data: 'created_at', name:'created_at'},
                        {data: 'action', name:'action', orderable:false, searchable:false}
                    ]
                });
            });
        </script>
    @endpush
</x-app-layout>