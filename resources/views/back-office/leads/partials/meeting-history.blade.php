<table class="table table-flush-spacing">
    <tr>
        <th>Attendee</th>
        <th>Status</th>
        <th>Start Date&Time</th>
        <th>End Date&Time</th>
        <th>Note</th>
        <th>Created At</th>
    </tr>
    @forelse ($model->meetings as $meeting)
        <tr>
            <td>
                {{ ucfirst($meeting?->attendees()->first()?->name) ?? '-' }}
                @php $attendee = $meeting?->attendees()->first(); @endphp 
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
            <td>
                @php 
                    $status = $meeting?->status;
                @endphp 
                <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status->name)) ?? 'bg-light text-dark' }}">
                    {{ strtoupper($status->name) }}
                </span>
            </td>
            <td>{{ date('d, M Y | h:i A', strtotime($meeting->start_date_time)) }}</td>
            <td>{{ date('d, M Y | h:i A', strtotime($meeting->end_date_time)) }}</td>
            <td>{{ $meeting->description ?? '-' }}</td>
            <td>{{ date('d, M Y | h:i A', strtotime($meeting->created_at)) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">No meetings found.</td>
        </tr>
    @endforelse
</table>