
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
            value="{{ old('name', $model->name) }}"
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
            class="form-control form-control-lg" 
            placeholder="Enter phone" 
            value="{{ old('phone', $model->phone) }}"
        />
        <span id="phone_error" class="text-danger error">{{ $errors->first('phone') }}</span>
    </div>
    <!-- Name Input -->
    <div class="col-12 col-md-6">
        <label for="gender" class="form-label fw-semibold">
            Gender <span class="text-danger">*</span>
        </label>
        <select id="gender" name="gender" class="form-select form-select-lg">
            <option value="">Select gender</option>
            <option value="M" {{ $model->gender=='M' ? 'selected' :'' }}>Male</option>
            <option value="F" {{ $model->gender=='F' ? 'selected' :'' }}>Female</option>
        </select>
        <span id="gender_error" class="text-danger error">{{ $errors->first('gender') }}</span>
    </div>
    <!-- Name Input -->
    <div class="col-12 col-md-6">
        <label for="doj" class="form-label fw-semibold">
            Date of Joining
        </label>
        <input 
            type="date" 
            id="doj" 
            name="doj" 
            class="form-control form-control-lg" 
            value="{{ old('doj', $model->doj) }}"
        />
        <span id="doj_error" class="text-danger error">{{ $errors->first('doj') }}</span>
    </div>
    <div class="col-12 col-md-6">
        <label for="email" class="form-label fw-semibold">
            Email <span class="text-danger">*</span>
        </label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-control form-control-lg" 
            placeholder="Enter email" 
            value="{{ old('email', $model->email) }}"
        />
        <span id="email_error" class="text-danger error">{{ $errors->first('email') }}</span>
    </div>

    <!-- Avatar Upload -->
    <div class="col-12 col-md-6">
        <label for="avatar" class="form-label fw-semibold">
            Avatar
        </label>

        <!-- File input -->
        <input 
            type="file" 
            id="avatar" 
            name="avatar" 
            accept=".png, .jpg, .jpeg" 
            class="form-control form-control-lg"
            onchange="previewAvatar(event)"
        />
        <small class="text-muted">Allowed file types: png, jpg, jpeg.</small>

        <!-- Preview wrapper -->
        <div class="mb-3">
            <img id="avatar_preview" 
                alt="Avatar Preview" 
                class="img-thumbnail rounded-circle" 
                style="width: 80px; height: 80px; object-fit: cover; {{ $model->avatar ? '' : 'display: none;' }}"
                src="{{ $model->avatar ? asset('storage/'.$model->avatar) : '' }}"
            >
        </div>
    </div>
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });

    function previewAvatar(event) {
        const input = event.target;
        const preview = document.getElementById('avatar_preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // show preview
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            // Reset preview if no file selected
            preview.src = '';
            preview.style.display = 'none';
        }
    }
</script>