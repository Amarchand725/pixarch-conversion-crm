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