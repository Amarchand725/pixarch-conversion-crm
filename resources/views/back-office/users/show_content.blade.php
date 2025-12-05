<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Avatar</td>
        <td>
            @php
                $avatarPath = optional($model->avatar)->path
                        ? asset('storage/' . $model->avatar->path)
                        : asset('back-office/assets/img/avatars/' . rand(1,10) . '.png');
            @endphp
            <img src="{{ $avatarPath }}" width="50" height="50" class="rounded-circle" alt="Avatar">
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Name</td>
        <td>{{ $model->name??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Role</td>
        <td>{{ $model?->roles()?->first()?->name??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Email</td>
        <td>{{ $model->email??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Phone</td>
        <td>{{ $model->phone??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Gender</td>
        <td>
            @if($model->gender=='M')
                Male
            @elseif ($model->gender=='F')
                Female
            @else
                Other
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Date of Joining</td>
        <td>{{ $model->doj??'-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>
            <span class="badge rounded-pill px-3 py-2 {{ badgeClass($model?->status?->name) }}">
            {{ ucfirst($model?->status?->name) }}
            </span>
        </td>
    </tr>
    @if($author = $model->author)
    <tr>
        <td class="text-nowrap fw-semibold">Author</td>
        <td>
            @php
                $avatarPath = optional($author->avatar)->path
                        ? asset('storage/' . $author->avatar->path)
                        : asset('back-office/assets/img/avatars/' . rand(1,10) . '.png');
            @endphp
            <div class="d-flex align-items-center gap-2">
                <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                <div class="d-flex flex-column">
                    <span class="fw-bold">{{ $author->name ?? '-' }}</span>
                    <small class="text-muted">{{ $author->email ?? '-' }}</small>
                </div>
            </div>
        </td>
    </tr>
@endif
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ date('d, M Y | h:i A', strtotime($model->created_at)) }}</td>
    </tr>
</table>