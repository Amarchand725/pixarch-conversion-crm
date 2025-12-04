let fieldIndex = 0;

/**
 * Renders a field card
 * @param {Object} field - optional existing field data
 */
function renderField(field = {}) {
    const index = fieldIndex;
    const type = field.type || 'text';
    const required = field.required ?? 0;
    const label = field.label || '';
    const placeholder = field.placeholder || '';
    const options = field.options || '';

    const html = `
    <div class="card p-3 mb-3 border field-item">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-bold">Label</label>
          <input type="text" name="fields[${index}][label]" class="form-control" placeholder="Full Name" value="${label}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Type</label>
          <select name="fields[${index}][type]" class="form-select field-type" data-index="${index}">
            <option value="text" ${type=='text' ? 'selected' : ''}>Text</option>
            <option value="email" ${type=='email' ? 'selected' : ''}>Email</option>
            <option value="number" ${type=='number' ? 'selected' : ''}>Number</option>
            <option value="textarea" ${type=='textarea' ? 'selected' : ''}>Textarea</option>
            <option value="select" ${type=='select' ? 'selected' : ''}>Select</option>
            <option value="file" ${type=='file' ? 'selected' : ''}>File</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Placeholder</label>
          <input type="text" name="fields[${index}][placeholder]" class="form-control" placeholder="Write here..." value="${placeholder}">
        </div>
        <div class="col-md-2">
          <label class="form-label fw-bold">Required</label>
          <select name="fields[${index}][required]" class="form-select">
            <option value="0" ${required==0 ? 'selected' : ''}>No</option>
            <option value="1" ${required==1 ? 'selected' : ''}>Yes</option>
          </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="button" class="btn btn-icon btn-outline-danger btn-sm remove-field">
            <i class="ti ti-x"></i>
          </button>
        </div>
        <div class="col-12 mt-2 select-options ${type !== 'select' ? 'd-none' : ''}" id="options-${index}">
          <label class="form-label fw-bold">Select Options (comma separated)</label>
          <input type="text" name="fields[${index}][options]" class="form-control" placeholder="Male, Female, Other" value="${options}">
        </div>
      </div>
    </div>
    `;
    $('#fields-wrapper').append(html);
    fieldIndex++;
}

// Remove field
$(document).on('click', '.remove-field', function () {
    $(this).closest('.field-item').remove();
});

// Show/hide select options
$(document).on('change', '.field-type', function () {
    const index = $(this).data('index');
    const optionsBox = $('#options-' + index);
    $(this).val() === 'select' ? optionsBox.removeClass('d-none') : optionsBox.addClass('d-none');
});

// Add new field button
$(document).on('click', '#add-field', function () {
    renderField();
});

// Initialize existing fields if editing
$(document).ready(function() {
    if (typeof existingFields !== 'undefined' && Array.isArray(existingFields)) {
        existingFields.forEach(field => renderField(field));
    }
});