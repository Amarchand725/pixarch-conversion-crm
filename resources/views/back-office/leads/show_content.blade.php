<table class="table table-flush-spacing">
    @if($assignee = $model->assignees->first())
    <tr>
        <td class="text-nowrap fw-semibold">Assignee</td>
        <td>
            @php
                $avatarPath = optional($assignee->avatar)->path
                        ? asset('storage/' . $assignee->avatar->path)
                        : asset('back-office/assets/img/avatars/' . rand(1,10) . '.png');
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
        <td class="text-nowrap fw-semibold">Value</td>
        <td>${{ number_format($model->value, 2) }}</td>
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