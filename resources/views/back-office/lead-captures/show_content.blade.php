<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Campaign Name</td>
        <td>{{ ucfirst($model?->campaign?->name ?? '-') }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Form Name</td>
        <td>{{ $model->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Shareable Link</td>
        <td class="d-flex align-items-center">
            <input 
                type="hidden" 
                id="shareableLink" 
                value="{{ $model->shareable_link ?? '' }}" 
                class="form-control form-control-sm me-2" 
                readonly
            />
            <button type="button" class="btn btn-sm btn-primary" onclick="copyLink(this)">
                Copy to share form link
            </button>
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
        <td>{{ $model->created_at ? $model->created_at->format('d, M Y | h:i A') : '-' }}</td>
    </tr>

    @if($model->fields->isNotEmpty())
        <tr>
            <td class="text-nowrap fw-semibold">Form Fields</td>
            <td>
                <ul class="list-unstyled mb-0">
                    @foreach($model->fields as $field)
                        <li>
                            <strong>{{ $field->label }}</strong> 
                            (Type: {{ ucfirst($field->type) }} 
                            @if($field->required) | Required @endif
                            @if($field->type === 'select' && $field->options)
                                | Options: {{ $field->options }}
                            @endif)
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
    @endif
</table>

<script>
    function copyLink(button) {
        const copyText = document.getElementById('shareableLink');

        navigator.clipboard.writeText(copyText.value).then(() => {
            // Change button text
            const originalText = button.innerHTML;
            button.innerHTML = "Copied!";

            // Restore back after 2 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 2000);
        });
    }
</script>