<div class="row g-3 mb-4">
    <div class="col-12 col-md-12">
        <label for="lead_id" class="form-label fw-semibold">
        Lead <span class="text-danger">*</span>
        </label>
        <select id="lead_id" name="lead_id" class="form-select">
        <option value="">Select Lead</option>
        @foreach ($leads as $lead)
            <option value="{{ $lead->uuid }}" {{ old('lead_id')==$lead->uuid ? 'selected' : '' }}>{{ ucfirst($lead->name) }} ({{ ucfirst($lead->email) }})</option>
        @endforeach
        </select>
        <span id="lead_id_error" class="text-danger error">{{ $errors->first('lead_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="start_date_time" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
        <input type="datetime-local" class="form-control" value="{{ old('start_date_time') }}" name="start_date_time">
        <span id="start_date_time_error" class="text-danger error">{{ $errors->first('start_date_time') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="end_date_time" class="form-label">End Date & Time</label>
        <input type="datetime-local" class="form-control" value="{{ old('end_date_time') }}" name="end_date_time">
        <span id="end_date_time_error" class="text-danger error">{{ $errors->first('end_date_time') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="attendee_id" class="form-label">Meeting Attendee <span class="text-danger">*</span></label>
        <select class="form-select" name="attendee_id">
            <option value="">Select meeting attendee</option>
            @foreach ($agents as $attendee)
                <option value="{{ $attendee->uuid }}" {{ old('attendee_id')==$attendee->uuid ? 'selected' : '' }}>{{ $attendee->name }} ({{ $attendee->email }})</option>
            @endforeach
        </select>
        <span id="attendee_id_error" class="text-danger error">{{ $errors->first('attendee_id') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="description" class="form-label">Add Note</label>
        <textarea class="form-control" name="description" rows="5" placeholder="Enter note...">{{ old('description') }}</textarea>
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