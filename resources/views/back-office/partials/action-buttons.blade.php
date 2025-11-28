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
                data-show-url="{{ route($routeInitialize. '.show', $model->id) }}"
                >
                {{ $singularLabel }} Details
            </a>
        @endcan
        @can($permissionPrefix.'-edit')
            <button
                data-toggle="tooltip" data-placement="top" title="Edit {{ $singularLabel }}"
                data-edit-url="{{ route($routeInitialize. '.edit', $model->id) }}"
                data-url="{{ route($routeInitialize. '.update', $model->id) }}"
                class="dropdown-item edit-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#create-pop-up-modal-for-file">
                Edit {{ $singularLabel }}
            </button>
        @endcan
         @can($permissionPrefix.'-delete')
            <a href="javascript:;" class="dropdown-item delete" data-del-url="{{ route($routeInitialize . '.destroy', $model->id) }}">Delete</a>
        @endcan
    </div>
</div>