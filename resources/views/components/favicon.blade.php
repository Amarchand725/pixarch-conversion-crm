@php
    $brandPath = env('APP_FAVICON_PATH', 'back-office/assets/img/branding/apple-touch-icon.png');
@endphp

<link 
    rel="icon"
    type="image/x-icon"
    href="{{ asset($brandPath ) }}"
>