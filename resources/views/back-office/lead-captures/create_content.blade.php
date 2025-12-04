
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
    <div class="col-12 col-md-12">
        <label for="status_id" class="form-label fw-semibold">
            Status
        </label>
        <select id="status_id" name="status_id" class="form-select">
            <option value="">Select status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->uuid }}">{{ ucfirst($status->name) }}</option>
            @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>