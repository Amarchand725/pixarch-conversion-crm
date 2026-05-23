@forelse($leads as $lead)
    <li class="list-group-item drag-item d-flex flex-column p-3 mb-2 shadow-sm rounded bg-white"
        data-lead-id="{{ $lead->uuid }}"
        data-value="{{ $lead->budget }}"
        style="cursor: grab;">

        <!-- Top row: Avatar + Name + Value -->
        <div class="d-flex justify-content-between align-items-center w-100 mb-2">
            <div class="d-flex align-items-center gap-2">
                <img class="rounded-circle"
                    src="{{ optional($lead->assignees->first()?->avatar)->path
                            ? asset('storage/' . $lead->assignees->first()->avatar->path)
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
                {{ config('app.currency_symbol') }}{{ number_format($lead->budget) }}
            </span>
        </div>

        <!-- Action Icons + Toggle -->
        <div class="d-flex gap-1">
            @if(auth()->user()->can('lead-assign'))
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
            @endif

            @if(auth()->user()->can('lead-note'))
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
            @endif

            @if(auth()->user()->can('meeting-create'))
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
            @endif

            @if(auth()->user()->can('lead-status'))
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
            @endif

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