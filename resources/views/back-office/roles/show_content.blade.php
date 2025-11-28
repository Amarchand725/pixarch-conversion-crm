<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Role</td>
        <td><span class="text-primary fw-semibold">{{ $model->name??'-' }}</span></td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ date('d, M Y | h:i A', strtotime($model->created_at)) }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">
            Group
        </td>
        <td class="text-nowrap fw-semibold">
            Permissions
        </td>
    </tr>

    @foreach ($groupedPermissions as $group=>$permissions)
        <tr>
            <td class="text-nowrap fw-semibold"><i class="ti ti-info-circle"></i> {{ ucfirst($group) }}</td>
            <td>
                <div class="row">
                    @foreach ($permissions as $sub_permission)
                        @php $label = explode('-', $sub_permission) @endphp
                        <div class="col-sm-3 mt-2">
                            <label class="form-check-label"><i class="fa fa-check"></i> {{ Str::ucfirst($label[1]) }}</label>
                        </div>
                    @endforeach
                </div>
            </td>
        </tr>
    @endforeach
</table>
