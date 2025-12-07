<table class="table table-flush-spacing">
    @if($assignee = $model->assignees->first())
    <tr>
        <td class="text-nowrap fw-semibold">Assignee</td>
        <td>
            @php
                $avatarPath = optional($assignee->avatar)->path
                        ? asset('storage/' . $assignee->avatar->path)
                        : asset('back-office/assets/img/avatars/default-avatar.png');
            @endphp
            <div class="d-flex align-items-center gap-2">
                <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
                <div class="d-flex flex-column">
                    <span class="fw-bold">{{ $assignee->name ?? '-' }}</span>
                    <small class="text-muted">{{ $assignee->email ?? '-' }}</small>
                </div>
            </div>
        </td>
    </tr>
    @endif
    <tr>
        <td class="text-nowrap fw-semibold">Name</td>
        <td>{{ $model->name??'-' }}</td>
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
        <td class="text-nowrap fw-semibold">Pipeline</td>
        <td>{{ $model->pipeline??'-' }}</td>
    </tr>    
    <tr>
        <td class="text-nowrap fw-semibold">Value</td>
        <td>
            <span class="text-success">
                {{ config('app.currency_symbol') }}{{ number_format($model->value, 2) }}
            </span>
        </td>
    </tr>   
    <tr>
        <td class="text-nowrap fw-semibold">Stage</td>
        <td>
            @php 
                $status = $model?->lastStatusLog?->status;
            @endphp 
            <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status->name)) ?? 'bg-light text-dark' }}">
                {{ strtoupper($status->name) }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Source</td>
        <td>
            
            {{ ucfirst($model?->source?->name) }}
            
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Status</td>
        <td>{{ ucfirst($model->status) }}</td>
    </tr>
    @if($author = $model->author)
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
        <td>{{ date('d, M Y | h:i A', strtotime($model->created_at)) }}</td>
    </tr>
</table>

<h3 class="mt-4">Lead History</h3>
<table class="table table-flush-spacing">
    <tr>
        <th>Assignee</th>
        <th>Status</th>
        <th>Description</th>
        <th>Author</th>
        <th>Created At</th>
    </tr>
    @foreach ($model->statusLogs as $statusLog)
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
    @endforeach
</table>

<h3 class="mt-4">Meeting History</h3>
<table class="table table-flush-spacing">
    <tr>
        <th>Attendee</th>
        <th>Status</th>
        <th>Start Date&Time</th>
        <th>End Date&Time</th>
        <th>Note</th>
        <th>Created At</th>
    </tr>
    @foreach ($model->meetings as $meeting)
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
            <td>{{ $meeting->description ?? '-' }}</td>
            <td>{{ date('d, M Y | h:i A', strtotime($statusLog->start_date_time)) }}</td>
            <td>{{ date('d, M Y | h:i A', strtotime($statusLog->end_date_time)) }}</td>
            <td>{{ date('d, M Y | h:i A', strtotime($statusLog->created_at)) }}</td>
        </tr>
    @endforeach
</table>