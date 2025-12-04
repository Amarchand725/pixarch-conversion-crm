<div class="row g-3 mb-4">
    <!-- Name Input -->
    <div class="col-12 col-md-6">
      <label for="name" class="form-label fw-semibold">
        Form Name <span class="text-danger">*</span>
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
    <div class="col-12 col-md-12">
      <div class="col-12 col-md-6">
        <h5 class="fw-bold mb-3">Form Fields</h5>
      </div>
      <div class="col-12 col-md-6">
        <button type="button"
          id="add-field"
          class="btn btn-sm btn-primary mt-3">
          <i class="ti ti-plus"></i> Add Field
        </button>
      </div>
    </div>
    <div class="col-12 col-md-12">
      <div id="fields-wrapper"></div>
    </div>
</div>

<script>
    $('select').each(function () {
      $(this).select2({
        dropdownParent: $(this).parent(),
      });
    });

     let fieldIndex = 0;

    // Add field
    $('#add-field').on('click', function () {
        let html = `
        <div class="card p-3 mb-3 border field-item">
          <div class="row g-3">

            <div class="col-md-3">
              <label class="form-label fw-bold">Label</label>
              <input type="text" name="fields[${fieldIndex}][label]" class="form-control"
                     placeholder="Full Name" required>
            </div>

            <div class="col-md-3">
              <label class="form-label fw-bold">Type</label>
              <select name="fields[${fieldIndex}][type]" 
                      class="form-select field-type"
                      data-index="${fieldIndex}">
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="number">Number</option>
                <option value="textarea">Textarea</option>
                <option value="select">Select</option>
                <option value="file">File</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label fw-bold">Placeholder</label>
              <input type="text" name="fields[${fieldIndex}][placeholder]"
                     class="form-control" placeholder="Write here...">
            </div>

            <div class="col-md-2">
              <label class="form-label fw-bold">Required</label>
              <select name="fields[${fieldIndex}][required]" class="form-select">
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
              <button type="button" class="btn btn-icon btn-outline-danger btn-sm remove-field">
                <i class="ti ti-x"></i>
              </button>
            </div>

            <div class="col-12 mt-2 select-options d-none" id="options-${fieldIndex}">
              <label class="form-label fw-bold">Select Options (comma separated)</label>
              <input type="text" name="fields[${fieldIndex}][options]"
                     class="form-control"
                     placeholder="Male, Female, Other">
            </div>

          </div>
        </div>
        `;

        $('#fields-wrapper').append(html);
        fieldIndex++;
    });

    // Remove field
    $(document).on('click', '.remove-field', function () {
      $(this).closest('.field-item').remove();
    });

    // Show/hide select options
    $(document).on('change', '.field-type', function () {
      const index = $(this).data('index');
      const optionsBox = $('#options-' + index);

      $(this).val() === 'select'
          ? optionsBox.removeClass('d-none')
          : optionsBox.addClass('d-none');
    });
</script>