<table class="table table-flush-spacing">
    <tr>
        <td class="text-nowrap fw-semibold">Campaign Name</td>
        <td>{{ ucfirst($model?->campaign?->name ?? '-') }}</td>
    </tr>
    <tr>
        <td class="text-nowrap fw-semibold">Lead Capture Form Name</td>
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