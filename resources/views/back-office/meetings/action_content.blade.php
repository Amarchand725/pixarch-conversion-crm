<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="action" value="{{ $action }}">
        <input type="hidden" name="lead_id" value="{{ $meeting?->lead?->uuid }}">
        @if($action == 'status')
            <!-- STATUS -->
            <div class="mb-3">
                <label for="status_id" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status_id" id="status_id" class="select2 form-select status_id">
                    <option value="">Select Status <span class="text-danger">*</span></option>
                    @foreach($statuses as $status)
                        <option value="{{ $status?->uuid }}" {{ $meeting?->status_id == $status->id ? 'selected' : '' }}>
                            {{ ucfirst($status->name ?? '-') }}
                        </option>
                    @endforeach
                </select>
                <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
            </div>
            <!-- NOTE -->
            <div class="mb-3">
                <label for="description" class="form-label">Add Note <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="5" placeholder="Enter note...">{{ old('description') }}</textarea>
                <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
            </div>
        @endif

        @if($action == 'reschedule')
            <!-- MEETING -->
            <div class="mb-3">
                <label for="start_date_time" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" value="{{ old('start_date_time') }}" name="start_date_time">
                <span id="start_date_time_error" class="text-danger error">{{ $errors->first('start_date_time') }}</span>
            </div>

            <div class="mb-3">
                <label for="end_date_time" class="form-label">End Date & Time</label>
                <input type="datetime-local" class="form-control" value="{{ old('end_date_time') }}" name="end_date_time">
                <span id="end_date_time_error" class="text-danger error">{{ $errors->first('end_date_time') }}</span>
            </div>

            @if(auth()->user()->hasAnyRole(['admin', 'lead']))
            <div class="mb-3">
                <label for="attendee_id" class="form-label">Meeting Attendee <span class="text-danger">*</span></label>
                <select class="form-select select2" name="attendee_id">
                    <option value="">Select meeting attendee</option>
                    @foreach ($agents as $attendee)
                        <option value="{{ $attendee->uuid }}" {{ $meeting?->attendees->first()?->id == $attendee->id ? 'selected' : '' }}>
                            {{ $attendee->name }} ({{ $attendee->email }})
                        </option>
                    @endforeach
                </select>
                <span id="attendee_id_error" class="text-danger error">{{ $errors->first('attendee_id') }}</span>
            </div>
            @endif
            <!-- NOTE -->
            <div class="mb-3">
                <label for="description" class="form-label">Add Note </label>
                <textarea class="form-control" name="description" rows="5" placeholder="Enter note...">{{ old('description') }}</textarea>
                <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
            </div>
        @endif
        
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>