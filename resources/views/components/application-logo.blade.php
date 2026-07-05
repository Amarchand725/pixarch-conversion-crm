@php
    $brandPath = env('APP_LOGO_PATH', 'back-office/assets/img/branding/pixarch-black-logo.png');
@endphp

<img 
    src="{{ asset($brandPath) }}"
    alt="Logo"
    width="100px"
/>