<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Name</td>
        <td><span class="text-primary fw-semibold">{{ $model->name??'-' }}</span></td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Email</td>
        <td><span class="text-primary fw-semibold">{{ $model->email??'-' }}</span></td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Phone</td>
        <td><span class="text-primary fw-semibold">{{ $model->phone??'-' }}</span></td>
    </tr>    
    <tr>
        <td class="text-nowrap fw-semibold">Value</td>
        <td><span class="text-primary fw-semibold">${{ number_format($model->value, 2) }}</span></td>
    </tr>   
     <tr>
        <td class="text-nowrap fw-semibold">Address</td>
        <td><span class="text-primary fw-semibold">{{ $model->address }}</span></td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ date('d, M Y | h:i A', strtotime($model->created_at)) }}</td>
    </tr>
</table>