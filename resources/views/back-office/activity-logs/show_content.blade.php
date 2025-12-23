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
    <tr>
        <td class="fw-bold text-uppercase bg-light text-center" colspan="2">Properties</td>
    </tr>
    <tr>
        <td style="padding:0;" colspan="2">
            @php
                $properties = $model?->properties ?? [];
            @endphp

            @if(!empty($properties))
                <div style="overflow-x:auto; max-width:100%;">
                    <table class="table table-sm table-bordered mb-0" style="min-width:600px; table-layout:fixed;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:25%;">Field</th>
                                <th style="width:35%;">Old</th>
                                <th style="width:40%;">New</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($properties['old']) && is_array($properties['old']))
                                @foreach($properties['old'] as $key => $oldValue)
                                    @if($key == 'password')
                                        @continue
                                    @endif
                                    @php
                                        $newValue = $properties['attributes'][$key] ?? '-';
                                        $oldValue = $oldValue ?? '-';

                                        // Format based on key
                                        switch ($key) {
                                            case 'created_at':
                                            case 'updated_at':
                                                $oldValue = $oldValue !== '-' ? \Carbon\Carbon::parse($oldValue)->format('d M Y | h:i A') : '-';
                                                $newValue = $newValue !== '-' ? \Carbon\Carbon::parse($newValue)->format('d M Y | h:i A') : '-';
                                                break;
                                            case 'uuid':
                                                $oldValue = is_string($oldValue) ? substr($oldValue, 0, 8) . '...' : $oldValue;
                                                $newValue = is_string($newValue) ? substr($newValue, 0, 8) . '...' : $newValue;
                                                break;
                                            case 'id':
                                            case 'author_id':
                                            case 'assignee_id':
                                            case 'model_id':
                                            case 'status_id':
                                                $oldValue = $oldValue ? '#' . $oldValue : '-';
                                                $newValue = $newValue ? '#' . $newValue : '-';
                                                break;
                                            default:
                                                break;
                                        }

                                        // Truncate very long strings for display
                                        $displayOld = strlen($oldValue) > 100 ? substr($oldValue, 0, 100) . '...' : $oldValue;
                                        $displayNew = strlen($newValue) > 100 ? substr($newValue, 0, 100) . '...' : $newValue;
                                    @endphp

                                    <tr>
                                        <td class="fw-semibold" style="word-break:break-word; padding-left: 8px;">{{ Illuminate\Support\Str::upper(str_replace('_',' ',$key)) }}</td>
                                        <td style="word-break:break-word;" title="{{ $oldValue }}">{{ $displayOld }}</td>
                                        <td style="word-break:break-word;" title="{{ $newValue }}">{{ $displayNew }}</td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($properties['attributes'] ?? [] as $key => $value)
                                    @if($key == 'password')
                                        @continue
                                    @endif
                                    @php
                                        $oldValue = '-';
                                        $newValue = $value;

                                        switch ($key) {
                                            case 'created_at':
                                            case 'updated_at':
                                                $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('d M Y | h:i A') : '-';
                                                break;
                                            case 'uuid':
                                                $newValue = is_string($newValue) ? substr($newValue, 0, 8) . '...' : $newValue;
                                                break;
                                            case 'id':
                                            case 'author_id':
                                            case 'assignee_id':
                                            case 'model_id':
                                            case 'status_id':
                                                $newValue = $newValue ? '#' . $newValue : '-';
                                                break;
                                            default:
                                                break;
                                        }

                                        $displayNew = strlen($newValue) > 100 ? substr($newValue, 0, 100) . '...' : $newValue;
                                    @endphp

                                    <tr>
                                        <td class="fw-semibold" style="word-break:break-word; padding-left: 8px;">{{ Illuminate\Support\Str::upper(str_replace('_',' ',$key)) }}</td>
                                        <td style="word-break:break-word;" title="{{ $oldValue }}">{{ $oldValue }}</td>
                                        <td style="word-break:break-word;" title="{{ $newValue }}">{{ $displayNew ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            @else
                <span class="text-muted">No changes recorded</span>
            @endif
        </td>
    </tr>
</table>