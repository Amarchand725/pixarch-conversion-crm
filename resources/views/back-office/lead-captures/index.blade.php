<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'Laravel'))
    
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