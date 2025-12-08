@can($permissionPrefix.'-status')
    <x-action-button
        type="button"
        id="status-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ $singularLabel }} Status"
        label="{{ $singularLabel }} Status"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.update-status', $model->uuid),
            'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'status', 'meeting' => $model->uuid])
        ]"
    />
@endcan

@can($permissionPrefix.'-reschedule')
    <x-action-button
        type="button"
        id="reschedule-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ $singularLabel }} Reschedule"
        label="{{ $singularLabel }} Reschedule"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.update-status', $model->uuid),
            'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'reschedule', 'meeting' => $model->uuid])
        ]"
    />
@endcan