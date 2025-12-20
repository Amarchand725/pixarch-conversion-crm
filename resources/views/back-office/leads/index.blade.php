<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', '100 KEYS UAE'))
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <style>
            .status-row-wrapper {
                width: 100%;
            }

            .status-row {
                display: flex;
                white-space: nowrap;
                overflow-x: auto; /* horizontal scroll */
                padding-bottom: 10px;
            }

            .status-card {
                flex-shrink: 0;
                min-width: 33.333%; /* col-md-4 width for 3 visible cards */
                max-width: 33.333%;
                margin-right: 1rem;
            }

            .task-column {
                max-height: 300px; /* vertical scroll if many items */
                overflow-y: auto;
            }

            .task-card {
                cursor: move;
            }

            .sortable-ghost {
                opacity: 0.4;
            }

            .drag-item {
                user-select: none;
            }
            
        </style>
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
                <div class="col-md-4">
                    <div class="dt-buttons btn-group flex-wrap float-end mt-4">
                        <button id="refresh-record" class="btn btn-success mx-2" title="{{ module_label('tooltip_refresh', $pluralLabel) }}"><i class="ti ti-refresh me-0 ti-xs"></i></button>
                    
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
        
        @if(request('view') != 'list') {{-- Cards --}}
            <div class="status-row-wrapper">
                <div class="status-row d-flex overflow-auto pb-2">
                    @forelse($statusLeads as $status)
                        <div class="status-card card"
                            data-status-id="{{ $status['status_id'] }}"
                            data-total="{{ $status['leads']->sum('budget') }}"
                        >
                            <div class="card-header text-center bg-light border-bottom-0 p-3">
                                <h5 class="mb-1">
                                    <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status['status_name'])) ?? 'bg-light text-dark' }}">
                                        {{ strtoupper($status['status_name']) }}
                                    </span>
                                </h5>
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <!-- Total Leads -->
                                    <span class="badge bg-info text-dark fs-6">
                                        <span class="lead-count">Total: {{ $status['leads']->count() }}</span> Leads
                                    </span>

                                    <!-- Total Value -->
                                    <span class="badge bg-success fs-6">
                                        <span class="total-value">Value: ${{ number_format($status['leads']->sum('budget')) }}</span>
                                    </span>
                                </div>
                            </div>

                            <ul class="list-group list-group-flush task-column">
                                @forelse($status['leads'] as $lead)
                                    <li class="list-group-item drag-item d-flex flex-column p-3 mb-2 shadow-sm rounded bg-white"
                                        data-lead-id="{{ $lead->uuid }}"
                                        data-value="{{ $lead->budget }}"
                                        style="cursor: grab;">

                                        <!-- Top row: Avatar + Name + Value -->
                                        <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <img class="rounded-circle"
                                                    src="{{ optional($lead->assignees->first()?->avatar)->path
                                                            ? asset('back-office/assets/' . $lead->assignees->first()->avatar->path)
                                                            : asset('back-office/assets/img/avatars/default-avatar.png') }}"
                                                    width="36" height="36" alt="Avatar" title="{{ $lead?->assignees->first()->name ?? '' }}">
                                                <span class="fw-semibold">
                                                    <a href="#" class="show fw-semibold cursor-pointer"
                                                        data-show-url="{{ route($routeInitialize.'.show', $lead->uuid) }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#details-modal"
                                                        title="{{ module_label('show', $singularLabel) }}"
                                                        label="{{ module_label('show', $singularLabel) }}"
                                                        >
                                                        {{ $lead->name ?? '' }}
                                                    </a>
                                                </span>
                                            </div>

                                            <!-- Value Badge stays on same row -->
                                            <span class="badge bg-success text-white">
                                                ${{ number_format($lead->budget) }}
                                            </span>
                                        </div>

                                        <!-- Action Icons + Toggle -->
                                        <div class="d-flex gap-1">
                                            <x-action-button
                                                type="button"
                                                id="assign-btn"
                                                btn-class="btn btn-outline-primary btn-sm p-1 edit-btn"
                                                title="{{ module_label('assign', 'Assignee') }}"
                                                label="{{ module_label('assign', 'Assignee') }}"
                                                icon="bi bi-person-fill fs-6"
                                                data-bs-toggle="modal"
                                                data-bs-target="#create-pop-up-modal-for-file"
                                                :data-attributes="[
                                                    'data-url' => route($routeInitialize.'.update-status', $lead->uuid),
                                                    'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'assign', 'lead' => $lead->uuid])
                                                ]"
                                            />

                                            <x-action-button
                                                type="button"
                                                id="note-btn"
                                                btn-class="btn btn-outline-warning btn-sm p-1 edit-btn"
                                                title="{{ module_label('add', 'Note') }}"
                                                label="{{ module_label('add', 'Note') }}"
                                                icon="bi bi-sticky fs-6"
                                                data-bs-toggle="modal"
                                                data-bs-target="#create-pop-up-modal-for-file"
                                                :data-attributes="[
                                                    'data-url' => route($routeInitialize.'.update-status', $lead->uuid),
                                                    'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'note', 'lead' => $lead->uuid])
                                                ]"
                                            />

                                            <x-action-button
                                                type="button"
                                                id="meeting-btn"
                                                btn-class="btn btn-outline-info btn-sm lead-action-btn p-1 edit-btn"
                                                title="{{ module_label('schedule', 'Meeting') }}"
                                                label="{{ module_label('schedule', 'Meeting') }}"
                                                icon="bi bi-calendar-event fs-6"
                                                data-bs-toggle="modal"
                                                data-bs-target="#create-pop-up-modal-for-file"
                                                :data-attributes="[
                                                    'data-url' => route($routeInitialize.'.update-status', $lead->uuid),
                                                    'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'meeting', 'lead' => $lead->uuid])
                                                ]"
                                            />

                                            <x-action-button
                                                type="button"
                                                id="status-btn"
                                                btn-class="btn btn-outline-success btn-sm lead-action-btn p-1 edit-btn"
                                                title="{{ module_label('status', $singularLabel) }}"
                                                label="{{ module_label('status', $singularLabel) }}"
                                                icon="bi bi-list-check fs-6"
                                                data-bs-toggle="modal"
                                                data-bs-target="#create-pop-up-modal-for-file"
                                                :data-attributes="[
                                                    'data-url' => route($routeInitialize.'.update-status', $lead->uuid),
                                                    'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'status', 'lead' => $lead->uuid])
                                                ]"
                                            />

                                            <!-- Toggle More Info -->
                                            <button class="btn btn-outline-secondary btn-sm ms-auto toggle-more-info" type="button">
                                                <i class="bi bi-chevron-down"></i> More
                                            </button>
                                        </div>

                                        <!-- More Info Section -->
                                        <div class="more-info mt-2 p-2 border rounded bg-light text-wrap text-break" style="display:none;">
                                            <p><strong>Email:</strong> {{ $lead->email ?? 'N/A' }}</p>
                                            <p><strong>Phone:</strong> {{ $lead->phone ?? 'N/A' }}</p>
                                            <p><strong>Pipeline:</strong> {{ ucfirst($lead->pipeline) ?? 'N/A' }}</p>
                                            <p>
                                                <strong>Status:</strong> 
                                                {{ strtoupper($lead->status) }}
                                            </p>
                                            <p>
                                                <strong>Source:</strong> 
                                                <span class="text-primary fw-semibold">
                                                {{ ucfirst($lead?->source?->name) }}
                                                </span>
                                            </p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="empty-slot text-center py-4 text-muted"
                                        style="opacity:.6;border:2px dashed #ccc;cursor:default;">
                                        Drop lead here
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    @empty
                        <li class="empty-slot text-center py-4 text-muted"
                            style="opacity:.6;border:2px dashed #ccc;cursor:default;">
                            Drop lead here
                        </li>
                    @endforelse
                </div>
            </div>
        @else {{-- Listing --}}
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
            function ensurePlaceholder(column) {
                const realItems = column.querySelectorAll('li[data-lead-id]');
                const emptySlot = column.querySelector('.empty-slot');

                if (realItems.length === 0 && !emptySlot) {
                    column.insertAdjacentHTML('beforeend',
                        '<li class="empty-slot text-center py-4 text-muted" style="opacity:.6;border:2px dashed #ccc;cursor:default;">Drop lead here</li>'
                    );
                }

                if (realItems.length > 0 && emptySlot) {
                    emptySlot.remove();
                }
            }

            function updateCardTotals(card) {
                const leadEls = card.querySelectorAll('.task-column li[data-lead-id]');
                let total = 0;

                leadEls.forEach(li => {
                    const val = Number(li.dataset.value);
                    if (!isNaN(val)) total += val;
                });

                const totalEl = card.querySelector('.total-value');
                if (totalEl) totalEl.textContent = `Value: $${total.toLocaleString()}`;

                card.dataset.total = total;

                const countEl = card.querySelector('.lead-count');
                if (countEl) countEl.textContent = `Total : ${leadEls.length}`;
            }

            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".task-column").forEach(column => {
                    new Sortable(column, {
                        group: { name: "leadGroup", pull: true, put: true },
                        animation: 150,
                        ghostClass: "sortable-ghost",
                        fallbackOnBody: true,
                        forceFallback: true,

                        onEnd: function (evt) {
                            const oldColumn = evt.from;
                            const newColumn = evt.to;
                            ensurePlaceholder(oldColumn);
                            ensurePlaceholder(newColumn);

                            const leadEl = evt.item;
                            const oldCard = evt.from.closest(".status-card");
                            const newCard = evt.to.closest(".status-card");
                            
                            const leadId = leadEl.dataset.leadId;
                            const statusId = newCard.dataset.statusId;

                            // Use a placeholder in Blade
                            let urlTemplate = "{{ route('back-office.leads.update-status', ':lead') }}";

                            // Replace placeholder with actual lead ID
                            let url = urlTemplate.replace(':lead', leadId);
                            
                            // Send AJAX to update status first
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ status_id: statusId })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    if (oldCard) updateCardTotals(oldCard);
                                    if (newCard) updateCardTotals(newCard);

                                    // Show Vuexy Toastr success
                                    toastr.success('You moved lead successfully!', 'Success', {
                                        closeButton: true,
                                        progressBar: true,
                                        positionClass: 'toast-top-right',
                                        timeOut: 2500
                                    });
                                } else {
                                    // Optional: revert drag if AJAX fails
                                    evt.from.insertBefore(leadEl, evt.from.children[evt.oldIndex]);
                                    if (oldCard) updateCardTotals(oldCard);
                                    if (newCard) updateCardTotals(newCard);

                                    toastr.error('Failed to update lead status', 'Error', {timeOut: 2500});
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                // Move lead back if needed
                                evt.from.insertBefore(leadEl, evt.from.children[evt.oldIndex]);
                                if (oldCard) updateCardTotals(oldCard);
                                if (newCard) updateCardTotals(newCard);

                                toastr.error('An error occurred', 'Error', {timeOut: 2500});
                            });
                        }
                    });
                });

                // Toggle More Info Section
                document.querySelectorAll('.toggle-more-info').forEach(button => {
                    button.addEventListener('click', function() {
                        const moreInfo = this.closest('li').querySelector('.more-info');
                        if (moreInfo.style.display === 'none' || moreInfo.style.display === '') {
                            moreInfo.style.display = 'block';
                            this.innerHTML = '<i class="bi bi-chevron-up"></i> Less';
                        } else {
                            moreInfo.style.display = 'none';
                            this.innerHTML = '<i class="bi bi-chevron-down"></i> More';
                        }
                    });
                });
            });

            const pageUrl = "{{ url()->current() }}";
            const columns = @json($dataTable->jsColumns());

            initializeDataTable(pageUrl, columns);

            $('#refresh-record').on('click', function(){
                $('.data_table').DataTable().ajax.reload();
            });
        </script>
    @endpush
</x-app-layout>