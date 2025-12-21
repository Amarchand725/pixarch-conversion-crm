@php $access = null; @endphp
@if(auth()->user()->can('lead-status'))
    @php $access = 'status'; @endphp
@endif

@if(auth()->user()->can('lead-note'))
    @php $access = 'note'; @endphp
@endif
@if(auth()->user()->can('meeting-create'))
    @php $access = 'meeting'; @endphp
@endif
@if(auth()->user()->can('lead-assign'))
    @php $access = 'assign'; @endphp
@endif

@if($access)
    <x-action-button
        type="button"
        id="assign-btn"
        btn-class="dropdown-item edit-btn"
        title="{{ module_label('status', $singularLabel) }}"
        label="{{ module_label('status', $singularLabel) }}"
        data-bs-toggle="modal"
        data-bs-target="#create-pop-up-modal-for-file"
        :data-attributes="[
            'data-url' => route($routeInitialize.'.update-status', $model->uuid),
            'data-edit-url' => route($routeInitialize.'.action.edit', ['action' => $access, 'lead' => $model->uuid])
        ]"
    />
@endif