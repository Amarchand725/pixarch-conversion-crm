<div class="dropdown">
    <a class="dropdown-toggle text-body" data-bs-toggle="dropdown" style="--bs-dropdown-item-padding-y:0;">
        <i class="ti ti-dots-vertical ti-sm"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-end">
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
        @can($permissionPrefix.'-view')
            <x-action-button
                type="link"
                href="#"
                btn-class="dropdown-item show"
                title="{{ $singularLabel }} Details"
                label="{{ $singularLabel }} Details"
                data-bs-toggle="modal"
                data-bs-target="#details-modal"
                :data-attributes="[
                    'data-show-url' => route($routeInitialize.'.show', $model->uuid)
                ]"
            />
        @endcan
        @can($permissionPrefix.'-edit')
            <x-action-button
                type="button"
                id="edit-btn"
                btn-class="dropdown-item edit-btn"
                title="Edit {{ $singularLabel }}"
                label="Edit {{ $singularLabel }}"
                data-bs-toggle="modal"
                data-bs-target="#create-pop-up-modal-for-file"
                :data-attributes="[
                    'data-url' => route($routeInitialize. '.update', $model->uuid),
                    'data-edit-url' => route($routeInitialize. '.edit', $model->uuid)
                ]"
            />
        @endcan
        @can($permissionPrefix.'-delete')
            <x-action-button
                type="link"
                href="javascript:;"
                btn-class="dropdown-item delete"
                title="Delete"
                label="Delete"
                :data-attributes="[
                    'data-del-url' => route($routeInitialize.'.destroy', $model->uuid)
                ]"
            />
        @endcan
    </div>
</div>