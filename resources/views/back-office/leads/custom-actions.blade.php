@can($permissionPrefix.'-status')
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ $singularLabel }} Status"
        label="{{ $singularLabel }} Status"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.update-status', $model->uuid),
            'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => 'assign', 'lead' => $model->uuid])
        ]"
    />
@endcan