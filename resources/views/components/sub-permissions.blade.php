<div class="table-responsive">
    <h5>Assign Permissions</h5>
    <table class="table table-flush-spacing">
        <tbody>
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" />
                        <label class="form-check-label" for="selectAll"> All </label>
                    </div>
                </td>
                <td class="text-nowrap fw-semibold">
                    Group
                </td>
                <td class="text-nowrap fw-semibold">
                    Permissions
                    <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Allows a full access to the system"></i>
                </td>
            </tr>
            @props(['permissions', 'role'])

            @foreach ($permissions as $permission)
                <tr class="sub-permissions">
                    <td class="text-nowrap fw-semibold">
                        <div class="row">
                            <div class="form-check">
                                <!-- Add data-group attribute to relate selectGroup with its sub-permissions -->
                                <input class="form-check-input selectGroup" data-group="{{ $permission->label }}" type="checkbox" id="selectGroup-{{ $permission->label }}" />
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap fw-semibold">
                        <label class="form-check-label" for="selectGroup-{{ $permission->label }}"> {{ ucfirst($permission->label) }}</label>
                    </td>
                    <td>
                        <div class="row">
                            @foreach (SubPermissions($permission->label) as $sub_permission)
                                @php $label = explode('-', $sub_permission->name) @endphp
                                <div class="col-sm-3 mt-2">
                                    <div class="form-check me-3 me-lg-5">
                                        @if(isset($role) && $role->hasPermissionTo($sub_permission->name))
                                            <input class="form-check-input permissionCheckbox" checked data-group="{{ $permission->label }}" name="permissions[]" value="{{ $sub_permission->name }}" type="checkbox" id="userManagementRead-{{ $sub_permission->id }}" />
                                        @else
                                            <input class="form-check-input permissionCheckbox" data-group="{{ $permission->label }}" name="permissions[]" value="{{ $sub_permission->name }}" type="checkbox" id="userManagementRead-{{ $sub_permission->id }}" />
                                        @endif
                                        <label class="form-check-label" for="userManagementRead-{{ $sub_permission->id }}"> {{ Str::ucfirst($label[1]) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>