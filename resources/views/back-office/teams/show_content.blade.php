<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Name</td>
        <td><span class="text-primary fw-semibold">{{ $model->name??'-' }}</span></td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>
            @if($model->status==1)
                <span class="badge rounded-pill px-3 py-2 bg-success text-white">
                Active
                </span>
            @else
                <span class="badge rounded-pill px-3 py-2 bg-danger text-white">
                De-Active
                </span>
            @endif
        </td>
    </tr>
    
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ date('d, M Y | h:i A', strtotime($model->created_at)) }}</td>
    </tr>
</table>