@method('PUT')
<div class="row g-3 mb-4">
    <!-- Password Input -->
    <div class="col-12 col-md-12 position-relative">
        <label for="password" class="form-label fw-semibold">
            Password <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-control" 
                placeholder="Enter password" 
                value="{{ old('password') }}"
            />
            <button type="button" class="btn btn-outline-secondary" id="generatePassword">
                Generate
            </button>
            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                Show
            </button>
        </div>
        <span id="password_error" class="text-danger error">{{ $errors->first('password') }}</span>
    </div>
</div>

<script>
    // Generate random password
function generatePassword(length = 12) {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        return password;
    }

    // Auto-generate password
    $('#generatePassword').on('click', function() {
        const password = generatePassword();
        $('#password').val(password);
    });

    // Show/Hide password
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            $(this).text('Hide');
        } else {
            passwordInput.attr('type', 'password');
            $(this).text('Show');
        }
    });
</script>