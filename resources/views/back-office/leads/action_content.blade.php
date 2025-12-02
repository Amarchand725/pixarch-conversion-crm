<input type="hidden" name="lead_id" value="{{ $lead->uuid }}">

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
                    <label class="form-label">Assign to Agent</label>
                    <select class="form-select" name="assigned_to">
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- NOTE -->
            <div class="tab-pane fade {{ $action=='note'?'show active':'' }}" id="noteTab">
                <div class="mb-3">
                    <label class="form-label">Add Note</label>
                    <textarea class="form-control" name="note" rows="5"></textarea>
                </div>
            </div>

            <!-- MEETING -->
            <div class="tab-pane fade {{ $action=='meeting'?'show active':'' }}" id="meetingTab">
                <div class="mb-3">
                    <label class="form-label">Meeting Date & Time</label>
                    <input type="datetime-local" class="form-control" name="meeting_time">
                </div>
            </div>

            <!-- STATUS -->
            <div class="tab-pane fade {{ $action=='status'?'show active':'' }}" id="statusTab">
                <div class="mb-3">
                    <label class="form-label">Lead Status</label>
                    <select class="form-select" name="status">
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="in_progress">In Progress</option>
                        <option value="qualified">Qualified</option>
                        <option value="closed">Closed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

</div>
