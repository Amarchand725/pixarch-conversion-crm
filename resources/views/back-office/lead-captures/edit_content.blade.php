@method('PUT')
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
        value="{{ old('name', $model->name) }}"
      />
      <span id="name_error" class="text-danger error">{{ $errors->first('name') }}</span>
    </div>
    <div class="col-12 col-md-6">
      <label for="campaign_id" class="form-label fw-semibold">
        Campaign
      </label>
      <select id="campaign_id" name="campaign_id" class="form-select">
        <option value="">Select Campaign</option>
        @foreach ($campaigns as $campaign)
          <option value="{{ $campaign->uuid }}" {{ $model->campaign_id==$campaign->id ? 'Selected' : '' }}>{{ ucfirst($campaign->name) }}</option>
        @endforeach
      </select>
      <span id="campaign_id_error" class="text-danger error">{{ $errors->first('campaign_id') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="status_id" class="form-label fw-semibold">
          Status
        </label>
        <select id="status_id" name="status_id" class="form-select">
          <option value="">Select status</option>
          @foreach ($statuses as $status)
            <option value="{{ $status->uuid }}" {{ $model->status_id == $status->id ? 'selected' : '' }}>{{ ucfirst($status->name) }}</option>
          @endforeach
        </select>
        <span id="status_id_error" class="text-danger error">{{ $errors->first('status_id') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="description" class="form-label fw-semibold">
          Description
        </label>
        <textarea name="description" rows="5" placeholder="Enter short description" id="description" class="form-control">{{ $model->description }}</textarea>
        <span id="description_error" class="text-danger error">{{ $errors->first('description') }}</span>
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

<script src="{{ asset('back-office/assets/custom/form-fields.js') }}"></script>
<script>
    $('select').each(function () {
      $(this).select2({
        dropdownParent: $(this).parent(),
      });
    });

    let existingFields = @json($model->fields ?? []);
</script>