@method('PUT')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-12">
        <label for="lead_id" class="form-label fw-semibold">
        Lead
        </label>
        <select id="lead_id" name="lead_id" class="form-select">
        <option value="">Select Lead</option>
        @foreach ($leads as $lead)
            <option value="{{ $lead->uuid }}" {{ old('lead_id', $model->lead_id)==$lead->id ? 'selected' : '' }}>{{ ucfirst($lead->name) }} ({{ ucfirst($lead->email) }})</option>
        @endforeach
        </select>
        <span id="lead_id_error" class="text-danger error">{{ $errors->first('lead_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="start_date_time" class="form-label">Start Date & Time</label>
        <input type="datetime-local" class="form-control" value="{{ old('start_date_time', $model->start_date_time ?? '') }}" name="start_date_time">
        <span id="start_date_time_error" class="text-danger error">{{ $errors->first('start_date_time') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="end_date_time" class="form-label">End Date & Time</label>
        <input type="datetime-local" class="form-control" value="{{ old('end_date_time', $model->start_date_time ?? '') }}" name="start_date_time">
        <span id="end_date_time_error" class="text-danger error">{{ $errors->first('end_date_time') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="attendee_id" class="form-label">Meeting Attendee</label>
        <select class="form-select" name="attendee_id">
            <option value="">Select meeting attendee</option>
            @foreach ($agents as $attendee)
                <option value="{{ $attendee->uuid }}" {{ old('attendee_id', $model?->attendees()->first()?->id)==$attendee->id ? 'selected' : '' }}>{{ $attendee->name }} ({{ $attendee->email }})</option>
            @endforeach
        </select>
        <span id="attendee_id_error" class="text-danger error">{{ $errors->first('attendee_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="status_id" class="form-label fw-semibold">
            Status
        </label>
        <select name="status_id" id="status_id" class="select2 form-select status_id">
            <option value="">Select Status</option>
            @foreach($statuses as $status)
                <option value="{{$status?->uuid}}" {{ $model?->status_id==$status->id ? 'selected' : '' }}>{{ ucfirst($status->name ?? '-') }}</option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="description" class="form-label">Add Note</label>
        <textarea class="form-control" name="description" rows="5" placeholder="Enter note...">{{ old('description', $model->description) }}</textarea>
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