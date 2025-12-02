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
            class="form-control form-control-lg" 
            placeholder="Enter name" 
            value="{{ old('name') }}"
        />
        <span id="name_error" class="text-danger error">{{ $errors->first('name') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="status" class="form-label fw-semibold">
            Status <span class="text-danger">*</span>
        </label>
        <select id="status" name="status" class="form-select form-select-lg">
            <option value="">Select status</option>
            <option value="1" selected>Active</option>
            <option value="0">De-Active</option>
        </select>
        <span id="status_error" class="text-danger error">{{ $errors->first('status') }}</span>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>