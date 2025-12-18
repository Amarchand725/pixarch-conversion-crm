@can($permissionPrefix.'-change_password')
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('password', $singularLabel) }}"
        label="{{ module_label('password', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.action.update-password', $model->uuid),
            'data-edit-url' => route($routeInitialize.'.action.edit', $model->uuid)
        ]"
    />
@endcan