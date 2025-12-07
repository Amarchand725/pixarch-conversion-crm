<div class="dropdown">
    <a class="dropdown-toggle text-body" data-bs-toggle="dropdown" style="--bs-dropdown-item-padding-y:0;">
        <i class="ti ti-dots-vertical ti-sm"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-end">
        @can($permissionPrefix.'-view')
            @if($permissionPrefix=='notification')
                <x-action-button
                    type="link"
                    href="#"
                    btn-class="dropdown-item show"
                    title="{{ $singularLabel }} Details"
                    label="{{ $singularLabel }} Details"
                    data-bs-toggle="modal"
                    data-bs-target="#details-modal"
                    :data-attributes="[
                        'data-show-url' => $model->data['url']
                    ]"
                />
            @else
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
            @endif
        @endcan
        @can($permissionPrefix.'-edit')
            @if($permissionPrefix=='role')
                <x-action-button
                    type="link"
                    href="{{ route($routeInitialize.'.edit', $model->uuid) }}"
                    btn-class="dropdown-item"
                    title="Edit {{ $singularLabel }}"
                    label="Edit {{ $singularLabel }}"
                />
            @else
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
            @endif
        @endcan
        @if($permissionPrefix=='user')
            @can('impersonate')
                <x-action-button
                    type="link"
                    href="{{ route('impersonate', ['id' => $model->id]) }}"
                    btn-class="dropdown-item"
                    title="Impersonate"
                    label="Impersonate"
                />
            @endcan
        @endif
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