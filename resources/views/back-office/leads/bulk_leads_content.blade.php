<div class="row g-3 mb-4">
    <div class="col-12">
        <label for="assignee_id" class="form-label fw-semibold">
            Lead Assignee
        </label>
        <select name="assignee_id" id="assignee_id" class="select2 form-select">
            <option value="">Select Assignee</option>
            @foreach($agents as $agent)
                <option value="{{ $agent?->uuid }}">{{ $agent->name  }} ({{ $agent->email }})</option>
            @endforeach
        </select>
        <span id="assignee_id_error" class="text-danger error">{{ $errors->first('assignee_id') }}</span>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-12">
        <label for="status_id" class="form-label fw-semibold">
            Stages
        </label>
        <select name="status_id" id="status_id" class="select2 form-select status_id">
            <option value="">Select Stage</option>
            @foreach($stages as $stage)
                <option value="{{$stage?->uuid}}">{{ ucfirst($stage->name ?? '-') }}</option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-12">
        <label for="description" class="form-label">Add Note</label>
        <textarea class="form-control" name="description" rows="5" placeholder="Enter note..."></textarea>
        <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>