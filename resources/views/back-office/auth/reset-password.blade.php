<x-guest-layout>
    @section('title', ('Reset Password').' - '. config('app.name', 'Laravel'))

    <h4 class="mb-1 pt-2">Reset Password 🔒</h4>
    <p class="mb-4">for <span class="fw-bold">{{ $request->email ?? '' }}</span></p>
    <form id="formAuthentication" action="{{ route('password.store') }}" method="POST">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ $request->email ?? '' }}">

        <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">New Password</label>
            <div class="input-group input-group-merge">
                <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    required autocomplete="new-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password"
                />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <div class="input-group input-group-merge">
                <input
                    type="password"
                    id="password_confirmation"
                    class="form-control"
                    name="password_confirmation" required autocomplete="new-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password"
                />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <button class="btn btn-primary d-grid w-100 mb-3" type="submit">{{ __('Reset Password') }}</button>
        <div class="text-center">
            <a href="{{ route('login') }}">
                <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
                Back to login
            </a>
        </div>
    </form>
</x-guest-layout>
