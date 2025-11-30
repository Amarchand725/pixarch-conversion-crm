<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', '100 KEYS UAE'))
    
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
                        
                            @can($permissionPrefix.'-create')
                                <button
                                    id="add-btn"
                                    data-toggle="tooltip" 
                                    data-placement="top" 
                                    title="Add {{ $singularLabel }}"
                                    data-title="Add {{ $singularLabel }}"
                                    data-url="{{ route($routeInitialize.'.store') }}"
                                    data-create-url="{{ route($routeInitialize.'.create') }}"
                                    class="btn btn-primary add-btn mb-3 mb-md-0 mx-2"
                                    tabindex="0" aria-controls="DataTables_Table_0"
                                    type="button" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#create-pop-up-modal-for-file">
                                    <span>
                                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                        <span class="d-none d-sm-inline-block"> Add {{ $singularLabel }} </span>
                                    </span>
                                </button>
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
    <!-- Modals -->
    <x-modals />
    <!--/ Modals -->

    @push('js')
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