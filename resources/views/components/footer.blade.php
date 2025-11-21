<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
    <div
        class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column"
    >
        <div>
        ©
        <script>
            document.write(new Date().getFullYear());
        </script>, made by <a href="{{ url('/') }}" target="_blank" class="fw-semibold">{{ config('app.name', 'Laravel') }}</a>
        </div>
        <div>
            <a href="{{ url('/') }}" class="footer-link me-4" target="_blank">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>
    </div>
    </div>
</footer>
