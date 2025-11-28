<div class="dropdown">
    <a class="dropdown-toggle text-body" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-end">
        @can(Str::singular($module).'-view')
            <a href="#"
                class="dropdown-item show"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#details-modal"
                data-toggle="tooltip"
                data-placement="top"
                title="{{ ucfirst(Str::singular($module)) }} Details"
                data-show-url="{{ route('back-office.' . $module . '.show', $model->id) }}"
                >
                View Details
            </a>
        @endcan
        @can(Str::singular($module).'-edit')
            <button
                data-toggle="tooltip" data-placement="top" title="Edit {{ ucfirst(Str::singular($module)) }}"
                data-edit-url="{{ route('back-office.' . $module . '.edit', $model->id) }}"
                data-url="{{ route('back-office.' . $module . '.update', $model->id) }}"
                class="dropdown-item edit-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#create-pop-up-modal-for-file">
                Edit {{ Str::singular(ucfirst($module)) }}
            </button>
        @endcan
         @can(Str::singular($module).'-delete')
            <a href="javascript:;" class="dropdown-item delete" data-del-url="{{ route('back-office.' . $module . '.destroy', $model->id) }}">Delete</a>
        @endcan
    </div>
</div>