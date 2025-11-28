<x-guest-layout>
    @section('title', ('Forgot Password').' - '. config('app.name', 'Laravel'))

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h4 class="mb-1 pt-2">Forgot Password? 🔒</h4>
    <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>
    <form id="formAuthentication" class="mb-3" action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="Enter your email"
                :value="old('email')" required autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Email Password Reset Link') }}</button>
    </form>
    <div class="text-center">
        <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
            <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
            Back to login
        </a>
    </div>
</x-guest-layout>
