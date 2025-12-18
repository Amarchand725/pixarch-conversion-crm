@method('PUT')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-12">
        <label for="password" class="form-label fw-semibold">
            Password <span class="text-danger">*</span>
        </label>

        <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-control" 
            placeholder="Enter password" 
            value="{{ old('password') }}"
        />

        <!-- Action buttons under input -->
        <div class="d-flex gap-3 mt-2 small">
            <a href="javascript:void(0)" id="generatePassword" class="text-primary">
                Generate
            </a>

            <a href="javascript:void(0)" id="togglePassword" class="text-secondary">
                Show
            </a>

            <a href="javascript:void(0)" id="copyPassword" class="text-success">
                Copy
            </a>
        </div>

        <span id="password_error" class="text-danger error d-block mt-1">
            {{ $errors->first('password') }}
        </span>
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

    document.getElementById('copyPassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');

        if (!passwordInput.value) {
            return;
        }

        // Temporarily show password to copy
        const originalType = passwordInput.type;
        passwordInput.type = 'text';
        passwordInput.select();
        passwordInput.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(passwordInput.value).then(() => {
            // Optional: small UX feedback
            this.innerText = 'Copied!';
            setTimeout(() => this.innerText = 'Copy', 1500);
        });

        // Restore original type
        passwordInput.type = originalType;
    });
</script>