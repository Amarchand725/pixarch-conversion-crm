@method('PUT')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-12">
        <label for="question" class="form-label fw-semibold">
            Question <span class="text-danger">*</span>
        </label>
        <textarea name="question" rows="5" placeholder="Enter short question" id="question" class="form-control" >{{ old('question', $model->question) }}</textarea>
        <span id="question_error" class="text-danger error">{{ $errors->first('question') }}</span>
    </div>
    <div class="col-12 col-md-12">
        <label for="answer" class="form-label fw-semibold">
            Answer <span class="text-danger">*</span>
        </label>
        <textarea name="answer" rows="5" placeholder="Enter short answer" id="answer" class="form-control" >{{ old('answer', $model->answer) }}</textarea>
        <span id="answer_error" class="text-danger error">{{ $errors->first('answer') }}</span>
    </div>
    <div class="col-12 col-md-12">
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
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>