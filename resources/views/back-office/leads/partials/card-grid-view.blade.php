<div class="status-row-wrapper">
    <div class="status-row d-flex overflow-auto pb-2">
        @forelse($statusLeads as $status)
            <div class="status-card card"
                data-status-id="{{ $status['status_id'] }}"
                data-total="{{ number_format($status['total_budget']) }}"
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
                            <span class="lead-count">Total: {{ $status['count'] }}</span> Leads
                        </span>

                        <!-- Total Value -->
                        <span class="badge bg-success fs-6">
                            <span class="total-value">Value: ${{ number_format($status['total_budget']) }}</span>
                        </span>
                    </div>
                </div>

                <ul class="list-group list-group-flush task-column"
                    data-status="{{ $status['status_db_id'] }}"
                    data-loaded="{{ $status['loaded'] }}">
                    
                    @include('back-office.leads.partials.lead-cards', [
                        'leads' => $status['leads'],
                        'routeInitialize' => $routeInitialize,
                        'singularLabel' => $singularLabel
                    ])
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