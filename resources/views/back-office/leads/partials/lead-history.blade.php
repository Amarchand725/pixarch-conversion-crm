<table class="table table-flush-spacing">
    <tr>
        <th>Assignee</th>
        <th>Status</th>
        <th>Budget</th>
        <th>Description</th>
        <th>Author</th>
        <th>Created At</th>
    </tr>
    @forelse ($model->statusLogs as $statusLog)
        <tr>
            <td>
                @php
                    $avatarPath = optional($statusLog?->assignee->avatar)->path
                            ? asset('storage/' . $statusLog?->assignee->avatar->path)
                            : asset('back-office/assets/img/avatars/default-avatar.png');
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                    <div class="d-flex flex-column">
                        <span class="fw-bold">{{ $statusLog?->assignee->name ?? '-' }}</span>
                        <small class="text-muted">{{ $statusLog?->assignee->email ?? '-' }}</small>
                    </div>
                </div>
            </td>
            <td>
                @php 
                    $status = $statusLog?->status;
                @endphp 
                <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status->name)) ?? 'bg-light text-dark' }}">
                    {{ strtoupper($status->name) }}
                </span>
            </td>
            <td>
                <span class="badge rounded-pill px-3 py-2 bg-info text-white">
                    {{ $symbol }}{{ $statusLog->amount }}
                </span>
            </td>
            <td>{{ $statusLog->description ?? '-' }}</td>
            <td>
                @php
                    $avatarPath = optional($statusLog?->author->avatar)->path
                            ? asset('storage/' . $statusLog?->author->avatar->path)
                            : asset('back-office/assets/img/avatars/default-avatar.png');
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                    <div class="d-flex flex-column">
                        <span class="fw-bold">{{ $statusLog?->author->name ?? '-' }}</span>
                        <small class="text-muted">{{ $statusLog?->author->email ?? '-' }}</small>
                    </div>
                </div>
            </td>
            <td>{{ date('d, M Y | h:i A', strtotime($statusLog->created_at)) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">No history found.</td>
        </tr>
    @endforelse
</table>