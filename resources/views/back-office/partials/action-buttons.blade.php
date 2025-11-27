<div class="dropdown">
    <a class="dropdown-toggle text-body" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-end">
        <a href="#"
            class="dropdown-item show"
            tabindex="0" aria-controls="DataTables_Table_0"
            type="button" data-bs-toggle="modal"
            data-bs-target="#details-modal"
            data-toggle="tooltip"
            data-placement="top"
            title="Role Details"
            data-show-url="{{ route('back-office.roles.show', $model->id) }}"
            >
            View Details
        </a>
        <a href="{{ route('back-office.roles.edit', $model->id) }}"
            class="dropdown-item"
            data-toggle="tooltip"
            data-placement="top"
            title="Edit Role">
            Edit
        </a>
        <a href="javascript:;" class="dropdown-item delete">Delete</a>
    </div>
</div>