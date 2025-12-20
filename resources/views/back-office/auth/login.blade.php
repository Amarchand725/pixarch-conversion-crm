<x-guest-layout>
    @section('title', ('Login').' - '. config('app.name', 'Laravel'))
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h4 class="mb-1 pt-2">Welcome to  {{ config('app.name', 'Laravel') }}👋</h4>
    <p class="mb-4">Sign-in to your account</p>
    <form class="ajax-form" id="" data-type="login" action="{{ route('login') }}" data-method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
            type="text"
            class="form-control is-invalid"
            id="email"
            name="email"
            placeholder="Enter your email"
            autofocus
            />

            <span id="email_error" class="text-danger error">{{ $errors->first('email') }}</span>
        </div>
        <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Password</label>
                <a href="{{ route('password.request') }}">
                    <small>Forgot Password?</small>
                </a>
            </div>
            <div class="input-group input-group-merge">
                <input
                    type="password"
                    id="password"
                    class="form-control "
                    name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password"
                />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>

            <span id="password_error" class="text-danger error">{{ $errors->first('password') }}</span>
        </div>
        {{-- <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                <label class="form-check-label" for="remember-me"> Remember Me </label>
            </div>
        </div> --}}
        <div class="mb-3">
            <div class="demo-inline-spacing sub-btn">
                <button type="submit" class="btn btn-primary d-grid w-100">Sign in</button>
            </div>
            <div class="demo-inline-spacing loading-btn" style="display: none;">
                <button class="btn btn-primary waves-effect waves-light w-100" type="button" disabled="">
                <span class="spinner-border me-1" role="status" aria-hidden="true"></span>
                Loading...
                </button>
            </div>
        </div>
    </form>
</x-guest-layout>
