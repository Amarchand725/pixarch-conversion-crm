<table class="table table-flush-spacing">
    @if($attendee = $model?->attendees()->first())
    <tr>
        <td class="text-nowrap fw-semibold">Attendee</td>
        <td>
            @php
                $avatarPath = optional($attendee->avatar)->path
                        ? asset('storage/' . $attendee->avatar->path)
                        : asset('back-office/assets/img/avatars/default-avatar.png');
            @endphp
            <div class="d-flex align-items-center gap-2">
                <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                <div class="d-flex flex-column">
                    <span class="fw-bold">{{ $attendee->name ?? '-' }}</span>
                    <small class="text-muted">{{ $attendee->email ?? '-' }}</small>
                </div>
            </div>
        </td>
    </tr>
    @endif
    <tr>
        <td class="text-nowrap fw-semibold">Lead Name</td>
        <td>
            {{ ucfirst($model?->name ?? '-') }} ({{ ucfirst($model?->phone ?? '-') }})
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>
            @php 
                $status = $model?->status;
            @endphp 
            <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status->name)) ?? 'bg-light text-dark' }}">
                {{ strtoupper($status->name) }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Timezone</td>
        <td>
            {{ $model->timezone ?? '-' }}
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Start Date Time</td>
        <td>
            {{ getDateTimeFormat($model->start_date_time) }}
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">End Date Time</td>
        <td>
            {{ getDateTimeFormat($model->end_date_time) }}
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Note</td>
        <td>
            {{ $model->description ?? '-' }}
        </td>
    </tr>
    @if($author = $model?->lastStatusLog->author)
    <tr>
        <td class="text-nowrap fw-semibold">Author</td>
        <td>
            @php
                $avatarPath = optional($author->avatar)->path
                        ? asset('storage/' . $author->avatar->path)
                        : asset('back-office/assets/img/avatars/default-avatar.png');
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
        <td>{{ $model->created_at ? $model->created_at->format('d, M Y | h:i A') : '-' }}</td>
    </tr>
</table>