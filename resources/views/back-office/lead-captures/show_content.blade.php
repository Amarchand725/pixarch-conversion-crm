<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Name</td>
        <td>{{ $model->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>{{ ucfirst($model?->status?->name ?? '-') }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ $model->created_at ? $model->created_at->format('d, M Y | h:i A') : '-' }}</td>
    </tr>

    @if($model->fields->isNotEmpty())
        <tr>
            <td class="text-nowrap fw-semibold">Form Fields</td>
            <td>
                <ul class="list-unstyled mb-0">
                    @foreach($model->fields as $field)
                        <li>
                            <strong>{{ $field->label }}</strong> 
                            (Type: {{ ucfirst($field->type) }} 
                            @if($field->required) | Required @endif
                            @if($field->type === 'select' && $field->options)
                                | Options: {{ $field->options }}
                            @endif)
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
    @endif
</table>
