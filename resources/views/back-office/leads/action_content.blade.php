<div class="row">

    <!-- LEFT SIDE TABS -->
    <div class="col-md-3 border-end">
        <div class="nav flex-column nav-pills">

            <a class="nav-link {{ $action=='assign'?'active':'' }}" data-bs-toggle="pill" href="#assignTab">
                <i class="bi bi-person-fill me-1"></i> Assign
            </a>

            <a class="nav-link {{ $action=='note'?'active':'' }}" data-bs-toggle="pill" href="#noteTab">
                <i class="bi bi-sticky me-1"></i> Note
            </a>

            <a class="nav-link {{ $action=='meeting'?'active':'' }}" data-bs-toggle="pill" href="#meetingTab">
                <i class="bi bi-calendar-event me-1"></i> Meeting
            </a>

            <a class="nav-link {{ $action=='status'?'active':'' }}" data-bs-toggle="pill" href="#statusTab">
                <i class="bi bi-list-check me-1"></i> Status
            </a>

        </div>
    </div>

    <!-- RIGHT SIDE CONTENT -->
    <div class="col-md-9">
        <div class="tab-content">
            <!-- ASSIGN -->
            <div class="tab-pane fade {{ $action=='assign'?'show active':'' }}" id="assignTab">
                <div class="mb-3">
                    <label for="assignee_id" class="form-label">Lead Assignee</label>
                    <select class="form-select" name="assignee_id">
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->uuid }}" {{ $lead->assignees->first()->id==$agent->id ? "selected" : '' }}>{{ $agent->name }} ({{ $agent->email }})</option>
                        @endforeach
                    </select>
                    <span id="assignee_id_error" class="text-danger error">{{ $errors->first('assignee_id') }}</span>
                </div>
            </div>

            <!-- NOTE -->
            <div class="tab-pane fade {{ $action=='note'?'show active':'' }}" id="noteTab">
                <div class="mb-3">
                    <label for="description" class="form-label">Add Note</label>
                    <textarea class="form-control" name="description" rows="5" placeholder="Enter note..."></textarea>
                    <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
                </div>
            </div>

            <!-- MEETING -->
            <div class="tab-pane fade {{ $action=='meeting'?'show active':'' }}" id="meetingTab">
                <div class="mb-3">
                    <label for="start_date_time" class="form-label">Start Date & Time</label>
                    <input type="datetime-local" class="form-control" value="{{ old('start_date_time') }}" name="start_date_time">
                    <span id="start_date_time_error" class="text-danger error">{{ $errors->first('start_date_time') }}</span>
                </div>
                <div class="mb-3">
                    <label for="end_date_time" class="form-label">End Date & Time</label>
                    <input type="datetime-local" class="form-control" value="{{ old('end_date_time') }}" name="end_date_time">
                    <span id="end_date_time_error" class="text-danger error">{{ $errors->first('end_date_time') }}</span>
                </div>
                <div class="mb-3">
                    <label for="attendee_id" class="form-label">Meeting Attendee</label>
                    <select class="form-select" name="attendee_id">
                        <option value="">Select meeting attendee</option>
                        @foreach ($agents as $attendee)
                            <option value="{{ $attendee->uuid }}">{{ $attendee->name }} ({{ $attendee->email }})</option>
                        @endforeach
                    </select>
                    <span id="attendee_id_error" class="text-danger error">{{ $errors->first('attendee_id') }}</span>
                </div>
            </div>

            <!-- STATUS -->
            <div class="tab-pane fade {{ $action=='status'?'show active':'' }}" id="statusTab">
                <div class="mb-3">
                    <label for="status_id" class="form-label fw-semibold">
                        Stages
                    </label>
                    <select name="status_id" id="status_id" class="select2 form-select status_id">
                        <option value="">Select Stage</option>
                        @foreach($stages as $stage)
                            <option value="{{$stage?->uuid}}" {{ $lead?->lastStatusLog?->status_id==$stage->id ? 'selected' : '' }}>{{ ucfirst($stage->name ?? '-') }}</option>
                        @endforeach
                    </select>
                    <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
