<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Name</td>
        <td>{{ $model->name??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Type</td>
        <td>{{ $model->type??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Budget</td>
        <td>{{ $model->budget??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Start Date</td>
        <td>{{ $model->start_date??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">End Date</td>
        <td>{{ $model->end_date??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Description</td>
        <td>{{ $model->description??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>{{ ucfirst($model?->status?->name) }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ date('d, M Y | h:i A', strtotime($model->created_at)) }}</td>
    </tr>
</table>