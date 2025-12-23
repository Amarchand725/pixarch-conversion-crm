<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Form Name</td>
        <td>{{ $model->name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Campaign Name</td>
        <td>{{ ucfirst($model?->campaign?->name ?? '-') }}</td>
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
                {{ strtoupper($status->name) ?? '-' }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Faq Section</td>
        <td>
            @php 
                $status = $model?->faq_status;
            @endphp 
            <span class="badge rounded-pill px-3 py-2 bg-info text-white ?? 'bg-warning text-white' }}">
                {{ $status ? 'Enable' : 'Disable' }}
            </span>
        </td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Description</td>
        <td>
            {{ $model->description ?? '-' }}
        </td>
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
        <td>{{ $model->created_at ? $model->created_at->format('d, M Y | h:i A') : '-' }}</td>
    </tr>

    @if($model->fields->isNotEmpty())
        <tr>
            <td colspan="2" class="fw-bold text-uppercase bg-light text-center">Form Fields</td>
        </tr>
        <tr>
            <td style="padding:0;" colspan="2">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($model->fields as $field)
                                <tr>
                                    <td style="word-break:break-word; padding-left: 8px">
                                        {{ Illuminate\Support\Str::upper(str_replace('_',' ',subject: $field->label)) }}
                                    </td>
                                    <td>{{ ucfirst($field->type) }}</td>
                                    <td>{{ $field->required ? 'Yes' : 'No' }}</td>
                                    <td>
                                        @if($field->type === 'select' && !empty($field->options))
                                            {{ is_array($field->options) ? implode(', ', $field->options) : $field->options }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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