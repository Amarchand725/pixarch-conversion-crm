<div class="row g-3 mb-4">
    <!-- Name Input -->
    <div class="col-12 col-md-12">
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
    <div class="col-12 col-md-6">
        <label for="type" class="form-label fw-semibold">
            Type
        </label>
        <select id="type" name="type" class="form-select">
            <option value="">Select type</option>
            @foreach (campaignTypes() as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
            @endforeach
        </select>
        <span id="type_error" class="text-danger error">{{ $errors->first('type') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="user_id" class="form-label fw-semibold">
            Agents
        </label>
        <select id="user_id" name="user_ids[]" class="form-select" multiple>
            @foreach ($agents as $agent)
                <option value="{{ $agent->uuid }}" 
                    {{ (collect(old('user_ids'))->contains($agent->uuid)) ? 'selected' : '' }}>
                    {{ $agent->name }} ({{ $agent->email }})
                </option>
            @endforeach
        </select>
        <span id="user_id_error" class="text-danger error">{{ $errors->first('user_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="budget" class="form-label fw-semibold">
            Budget
        </label>
        <input 
            type="text" 
            id="budget" 
            name="budget" 
            class="form-control" 
            placeholder="Enter budget" 
            value="{{ old('budget') }}"
            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
        />
        <span id="budget_error" class="text-danger error">{{ $errors->first('budget') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="start_date" class="form-label fw-semibold">
            Start Date
        </label>
        <input 
            type="date" 
            id="start_date" 
            name="start_date" 
            class="form-control" 
            placeholder="Enter start_date" 
            value="{{ old('start_date') }}"
        />
        <span id="start_date_error" class="text-danger error">{{ $errors->first('start_date') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="end_date" class="form-label fw-semibold">
            End Date
        </label>
        <input 
            type="date" 
            id="end_date" 
            name="end_date" 
            class="form-control" 
            placeholder="Enter end_date" 
            value="{{ old('end_date') }}"
        />
        <span id="end_date_error" class="text-danger error">{{ $errors->first('end_date') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="description" class="form-label fw-semibold">
            Description
        </label>
        <textarea class="form-control" placeholder="Enter description" name="description" rows="5">{{ old('description') }}</textarea>
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