<div class="row">

    <!-- LEFT SIDE TABS -->
    <div class="col-md-3 border-end">
        <div class="nav flex-column nav-pills">
            @if(auth()->user()->can('lead-assign'))
            <a class="nav-link {{ $action=='assign'?'active':'' }}" data-bs-toggle="pill" href="#assignTab">
                <i class="bi bi-person-fill me-1"></i> Assign
            </a>
            @endif

            @if(auth()->user()->can('lead-note'))
            <a class="nav-link {{ $action=='note'?'active':'' }}" data-bs-toggle="pill" href="#noteTab">
                <i class="bi bi-sticky me-1"></i> Note
            </a>
            @endif

            @if(auth()->user()->can('meeting-create'))
            <a class="nav-link {{ $action=='meeting'?'active':'' }}" data-bs-toggle="pill" href="#meetingTab">
                <i class="bi bi-calendar-event me-1"></i> Meeting
            </a>
            @endif

            @if(auth()->user()->can('lead-status'))
            <a class="nav-link {{ $action=='status'?'active':'' }}" data-bs-toggle="pill" href="#statusTab">
                <i class="bi bi-list-check me-1"></i> Status
            </a>
            @endif
        </div>
    </div>

    <!-- RIGHT SIDE CONTENT -->
    <div class="col-md-9">
        <div class="tab-content">
            <!-- ASSIGN -->
            @if(auth()->user()->can('lead-assign'))
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
            @endif

            <!-- NOTE -->
            @if(auth()->user()->can('lead-note'))
                {{-- Existing Notes --}}
                @if(isset($notes) && $notes->count())
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Previous Notes</label>

                        <div id="lead-notes-wrapper"
                            class="border rounded p-2 bg-light"
                            style="max-height:220px; overflow-y:auto;">

                            @foreach($notes as $note)
                                <div class="mb-2 p-2 bg-white rounded shadow-sm">
                                    <div class="small text-muted mb-1">
                                        <strong>{{ $note->author->name ?? 'System' }}</strong>
                                        • {{ $note->created_at->diffForHumans() }}
                                    </div>
                                    <div class="text-break">
                                        {{ $note->description }}
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                @endif
                <div class="tab-pane fade {{ $action=='note'?'show active':'' }}" id="noteTab">
                    <div class="mb-3">
                        <label for="description" class="form-label">Add Note</label>
                        <textarea class="form-control" name="description" rows="5" placeholder="Enter note..."></textarea>
                        <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
                    </div>
                </div>
            @endif

            <!-- MEETING -->
            @if(auth()->user()->can('meeting-create'))
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
                @if(auth()->user()->hasAnyRole(['Admin', 'Lead']))
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
                @endif
            </div>
            @endif

            <!-- STATUS -->
            @if(auth()->user()->can('lead-status'))
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
                <div class="mb-3">
                    <label for="amount" class="form-label">Budget Amount</label>
                    <input type="number" class="form-control" placeholder="Enter budget if have changed" value="{{ old('amount') }}" name="amount">
                    <span id="amount_error" class="text-danger error">{{ $errors->first('amount') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        if (e.target.getAttribute('href') === '#noteTab') {
            const notes = document.getElementById('lead-notes-wrapper');
            if (notes) {
                setTimeout(() => {
                    notes.scrollTop = notes.scrollHeight;
                }, 100);
            }
        }
    });
</script>
