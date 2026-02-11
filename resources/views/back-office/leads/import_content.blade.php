<div class="row g-3 mb-4">
    <!-- Name Input -->
    <div class="col-12 col-md-12">
        <label for="lead-file" class="form-label">Select CSV/XLSX file</label>
        <input type="file" name="file" id="lead-file" class="form-control" required>
        <span id="file_error" class="text-danger error">{{ $errors->first('file') }}</span>
    </div>
    
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>