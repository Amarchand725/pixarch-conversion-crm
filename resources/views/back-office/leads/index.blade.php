<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', '100 KEYS UAE'))
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="{{ asset('back-office') }}/assets/css/lead-custom.css" />
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="card-header">
                        <h4 class="fw-bold mb-0">
                            <span class="text-muted fw-light">Home /</span> {{ $title }} 
                            <span class="badge rounded-pill px-3 py-2 bg-primary text-white">
                                {{ $total_leads }} {{ module_label('module_title', $pluralLabel) }}
                            </span>
                        </h4>
                    </div>
                </div>
                @if(request('view') == 'list') 
                    <div class="col-md-4">
                        <div class="dt-buttons btn-group flex-wrap float-end mt-4">
                            <button id="refresh-record" class="btn btn-success mx-2" title="{{ module_label('tooltip_refresh', $pluralLabel) }}"><i class="ti ti-refresh me-0 ti-xs"></i></button>
                            <x-action-button
                                type="button"
                                id="add-btn"
                                btn-class="btn btn-success add-btn mb-3 mb-md-0 mx-2"
                                title="Import Leads"
                                label="Import Leads"
                                icon="ti ti-upload me-0 me-sm-1 ti-xs"
                                data-bs-toggle="modal"
                                 data-bs-target="#create-pop-up-modal-for-file"
                                :data-attributes="[ 
                                    'data-url' => route($routeInitialize.'.import-data'), 
                                    'data-create-url' => route($routeInitialize.'.import-form')
                                ]"
                            />
                            @can($permissionPrefix.'-create')
                                <x-action-button
                                    type="button"
                                    id="add-btn"
                                    btn-class="btn btn-primary add-btn mb-3 mb-md-0 mx-2"
                                    title="{{ module_label('tooltip_add', $singularLabel) }}"
                                    label="{{ module_label('tooltip_add', module: $singularLabel) }}"
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
                @endif
            </div>  
        </div>

        <div class="d-flex justify-content-end mb-3">
            <a href="?view=cards" class="btn btn-sm {{ request('view')=='cards' || request('view')==null ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-grid-3x3-gap" style="margin-right: 5px;"></i> Cards View
            </a>
            <a href="?view=list" class="btn btn-sm ms-2 {{ request('view')=='list' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-list" style="margin-right: 5px;"></i> List View
            </a>
        </div>
        
        @if(request('view') != 'list') {{-- Cards Grid View --}}
            @include('back-office.leads.partials.card-grid-view', [
                'statusLeads' => $statusLeads
            ])
        @else {{-- Data Table Listing --}}
            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="table dataTable dtr-column data_table">
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
        @endif
    </div>

    <!-- Modals -->
    <x-modals/>
    <!--/ Modals -->
    @push('js')
        <!-- Page JS -->
        <script src="{{ asset('back-office') }}/assets/vendor/libs/sortablejs/sortable.js"></script>
        <script src="{{ asset('back-office') }}/assets/js/extended-ui-drag-and-drop.js"></script>
        
        <script>
            window.leadConfig = {
                updateStatusUrl: "{{ route('back-office.leads.update-status', ':lead') }}",
                csrfToken: "{{ csrf_token() }}"
            };
            const pageUrl = "{{ url()->current() }}";
            const columns = @json($dataTable->jsColumns());

            initializeDataTable(pageUrl, columns);

            $('#refresh-record').on('click', function(){
                $('.data_table').DataTable().ajax.reload();
            });
        </script>

        {{-- lead custom js --}}
        <script src="{{ asset('back-office') }}/assets/js/lead-custom.js"></script>
    @endpush
</x-app-layout>