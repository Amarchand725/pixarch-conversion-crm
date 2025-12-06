<div class="row g-3 mb-4">
    <!-- Name Input -->
    <div class="col-12 col-md-6">
        <label for="name" class="form-label fw-semibold">
            Name <span class="text-danger">*</span>
        </label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            class="form-control" 
            placeholder="Enter name" 
            value="{{ old('name') }}"
        />
        <span id="name_error" class="text-danger error">{{ $errors->first('name') }}</span>
    </div>
    <!-- Name Input -->
    <div class="col-12 col-md-6">
        <label for="phone" class="form-label fw-semibold">
            Phone
        </label>
        <input 
            type="text" 
            id="phone" 
            name="phone" 
            class="form-control phoneNumber" 
            placeholder="(999) - 12345678"
            value="{{ old('phone') }}"
        />
        <span id="phone_error" class="text-danger error">{{ $errors->first('phone') }}</span>
    </div>
   
    <div class="col-12 col-md-6">
        <label for="email" class="form-label fw-semibold">
            Email
        </label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-control" 
            placeholder="Enter email" 
            value="{{ old('email') }}"
        />
        <span id="email_error" class="text-danger error">{{ $errors->first('email') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="value" class="form-label fw-semibold">
            Value 
        </label>
        <input 
            type="number" 
            id="value" 
            name="value" 
            class="form-control" 
            placeholder="Enter value" 
            value="{{ old('value') }}"
        />
        <span id="value_error" class="text-danger error">{{ $errors->first('value') }}</span>
    </div>

    <div class="col-12 col-md-6">
        <label for="source_id" class="form-label fw-semibold">
            Source
        </label>
        <select name="source_id" id="source_id" class="select2 form-select source_id">
            <option value="">Select Source</option>
            @foreach($sources as $source)
                <option value="{{$source?->uuid}}">{{$source->name ?? '-'}}</option>
            @endforeach
        </select>
        <span id="source_id_error" class="text-danger error">{{ $errors->first('source_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="status_id" class="form-label fw-semibold">
            Stages
        </label>
        <select name="status_id" id="status_id" class="select2 form-select status_id">
            <option value="">Select Stage</option>
            @foreach($stages as $stage)
                <option value="{{$stage?->uuid}}">{{$stage->name ?? '-'}}</option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="pipeline" class="form-label fw-semibold">
            Pipeline
        </label>
        <select name="pipeline" id="pipeline" class="select2 form-select">
            <option value="">Select Status</option>
            @foreach(pipelines() as $pipeline)
                <option value="{{$pipeline}}">{{ ucfirst($pipeline)  }}</option>
            @endforeach
        </select>
        <span id="pipeline_error" class="text-danger error">{{ $errors->first('pipeline') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="status" class="form-label fw-semibold">
            Statuses
        </label>
        <select name="status" id="status" class="select2 form-select">
            <option value="">Select Status</option>
            @foreach(statuses() as $status)
                <option value="{{$status}}">{{ ucfirst($status)  }}</option>
            @endforeach
        </select>
        <span id="status_error" class="text-danger error">{{ $errors->first('status') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="assignee_id" class="form-label fw-semibold">
            Followers
        </label>
        <select name="assignee_id" id="assignee_id" class="select2 form-select">
            <option value="">Select Status</option>
            @foreach($agents as $agent)
                <option value="{{ $agent?->uuid }}">{{ $agent->name  }}</option>
            @endforeach
        </select>
        <span id="assignee_id_error" class="text-danger error">{{ $errors->first('assignee_id') }}</span>
    </div>
    
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>