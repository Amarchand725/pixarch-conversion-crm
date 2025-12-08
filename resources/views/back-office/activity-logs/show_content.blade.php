<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Log Name</td>
        <td>{{ ucfirst($model?->log_name ?? '-') }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Event</td>
        <td>
            @php
                $event = $model->getRawOriginal('event') ?? '-';
            @endphp
            <span class="badge rounded-pill px-3 py-2 {{ activityEventBadgeClass($event) }}">
                {{ ucfirst($event) }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Description</td>
        <td>{{ $model?->description ?? '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Subject</td>
        <td>
            @php
                $subject = $model?->subject;
            @endphp
            @if($subject)
                <span>{{ class_basename($subject) }} (#{{ $subject->id }})</span>
            @else
                -
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Causer</td>
        <td>
            @php
                $causer = $model?->causer;
                $avatarPath = $causer && optional($causer->avatar)->path
                    ? asset('storage/' . $causer->avatar->path)
                    : asset('back-office/assets/img/avatars/default-avatar.png');
            @endphp
            @if($causer)
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                    <div class="d-flex flex-column">
                        <span class="fw-bold">{{ $causer->name ?? '-' }}</span>
                        <small class="text-muted">{{ $causer->email ?? '-' }}</small>
                    </div>
                </div>
            @else
                <span class="text-muted">System</span>
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Created At</td>
        <td>{{ $model?->created_at ? $model->created_at->format('d, M Y | h:i A') : '-' }}</td>
    </tr>
</table>