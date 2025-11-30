<div class="dropdown">
    <a class="dropdown-toggle text-body" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-end">
        @can($permissionPrefix.'-view')
            <a href="#"
                class="dropdown-item show"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#details-modal"
                data-toggle="tooltip"
                data-placement="top"
                title="{{ $singularLabel }} Details"
                data-show-url="{{ route($routeInitialize. '.show', $model->uuid) }}"
                >
                {{ $singularLabel }} Details
            </a>
        @endcan
        @can($permissionPrefix.'-edit')
            @if($permissionPrefix=='role')
                <a href="{{ route($routeInitialize.'.edit', $model->uuid) }}"
                    class="dropdown-item"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="Edit {{ $singularLabel }}">
                    Edit {{ $singularLabel }}
                </a>
            @else
                <button
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="Edit {{ $singularLabel }}"
                    data-edit-url="{{ route($routeInitialize. '.edit', $model->uuid) }}"
                    data-url="{{ route($routeInitialize. '.update', $model->uuid) }}"
                    class="dropdown-item edit-btn"
                    tabindex="0" aria-controls="DataTables_Table_0"
                    type="button" data-bs-toggle="modal"
                    data-bs-target="#create-pop-up-modal-for-file">
                    Edit {{ $singularLabel }}
                </button>
            @endif
        @endcan
        @can($permissionPrefix.'-delete')
            <a href="javascript:;" class="dropdown-item delete" data-del-url="{{ route($routeInitialize . '.destroy', $model->uuid) }}">Delete</a>
        @endcan
    </div>
</div>