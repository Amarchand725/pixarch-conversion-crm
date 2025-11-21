<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'Laravel'))
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
        </style>
    @endpush
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">{{ ($title ?? ''). ' /' }} </span> Drag &amp; Drop</h4>

        <div class="status-row-wrapper">
            <div class="status-row d-flex overflow-auto pb-2">
                @foreach($statusLeads as $status)
                    <div class="status-card card"
                        data-status-id="{{ $status['status_id'] }}"
                        data-total="{{ $status['leads']->sum('value') }}"
                    >
                        <div class="card-header text-center bg-light border-bottom-0 p-3">
                            <h5 class="mb-1 text-primary">{{ ucwords($status['status_name']) }}</h5>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <!-- Total Leads -->
                                <span class="badge bg-info text-dark fs-6">
                                    <span class="lead-count">Total: {{ $status['leads']->count() }}</span> Leads
                                </span>

                                <!-- Total Value -->
                                <span class="badge bg-success fs-6">
                                    <span class="total-value">Value: ${{ number_format($status['leads']->sum('value')) }}</span>
                                </span>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush task-column">
                            @foreach($status['leads'] as $lead)
                                <li class="list-group-item drag-item d-flex justify-content-between align-items-center p-3 mb-2 shadow-sm rounded bg-white"
                                    data-lead-id="{{ $lead->uuid }}"
                                    data-value="{{ $lead->value }}"
                                    style="cursor: grab;">

                                    <div class="d-flex flex-column w-100">
                                        <!-- Top: Avatar + Name -->
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <img class="rounded-circle"
                                                src="{{ optional($lead->currentAssignee?->avatar)->path
                                                        ? asset('backend/assets/' . $lead->currentAssignee->avatar->path)
                                                        : asset('backend/assets/img/avatars/' . rand(1, 10) . '.png') }}"
                                                width="36" height="36" alt="Avatar">

                                            <span class="fw-semibold">{{ $lead->name }}</span>
                                        </div>

                                        <!-- Action Icons centered under name, smaller size -->
                                        <div class="d-flex gap-1 mt-1 ml-5">
                                            <button class="btn btn-outline-primary btn-sm lead-action-btn p-1" data-action="assign" title="Assign">
                                                <i class="bi bi-person-fill fs-6"></i>
                                            </button>
                                            <button class="btn btn-outline-success btn-sm lead-action-btn p-1" data-action="task" title="Add Task">
                                                <i class="bi bi-list-task fs-6"></i>
                                            </button>
                                            <button class="btn btn-outline-warning btn-sm lead-action-btn p-1" data-action="note" title="Add Note">
                                                <i class="bi bi-sticky fs-6"></i>
                                            </button>
                                            <button class="btn btn-outline-info btn-sm lead-action-btn p-1" data-action="meeting" title="Schedule Meeting">
                                                <i class="bi bi-calendar-event fs-6"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Value Badge -->
                                    <span class="badge bg-success text-white ms-2">
                                        ${{ number_format($lead->value) }}
                                    </span>
                                </li>

                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- /Multiple Lists Draggable ends -->
    </div>
    @push('js')
        <!-- Page JS -->
        <script src="{{ asset('backend') }}/assets/vendor/libs/sortablejs/sortable.js"></script>
        <script src="{{ asset('backend') }}/assets/js/extended-ui-drag-and-drop.js"></script>
        <script>
            function updateCardTotals(card) {
                const leadEls = card.querySelectorAll('.task-column li');
                let total = 0;
                leadEls.forEach(li => {
                    const val = Number(li.dataset.value);
                    if (!isNaN(val)) total += val;
                });

                // Update the DOM
                const totalEl = card.querySelector('.total-value');
                if (totalEl) totalEl.textContent = `Value: $${total.toLocaleString()}`;

                // Update dataset for reference
                card.dataset.total = total;

                // Update total leads count
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

                        onEnd: function (evt) {

                            const leadEl = evt.item;
                            const oldCard = evt.from.closest(".status-card");
                            const newCard = evt.to.closest(".status-card");

                            const leadId = leadEl.dataset.leadId;
                            const statusId = newCard.dataset.statusId;

                            // Send AJAX to update status first
                            fetch("{{ route('leads.update-status') }}", {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ lead_id: leadId, status_id: statusId })
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
            
                // Initialize Bootstrap tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
                tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl)
                })
            });
        </script>
    @endpush
</x-app-layout>