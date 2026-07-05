<!DOCTYPE html>

<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('back-office') }}/assets/"
>
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    {{-- <title>Login | {{ config('app.name', 'Laravel') }}</title> --}}
    <title>@yield('title')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <x-favicon />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gauth-session-status"/>

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/toastr/toastr.css') }}">
    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="{{ asset('back-office') }}/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <script src="{{ asset('back-office') }}/assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Login -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center mb-4 mt-2">
                <a href="{{ route('login') }}" class="app-brand-link">
                  <span class="app-brand-logo demo">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                  </span>
                  {{-- <span class="app-brand-text demo text-body fw-bold ms-1">{{ config('app.name', 'Laravel') }}</span> --}}
                </a>
              </div>
              <!-- /Logo -->
              
              {{ $slot }}
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('back-office') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/libs/node-waves/node-waves.js"></script>

    <script src="{{ asset('back-office') }}/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="{{ asset('back-office') }}/assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('back-office') }}/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('back-office') }}/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('back-office') }}/assets/js/main.js"></script>
    <!-- Toastr JS -->
    <script src="{{ asset('back-office/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{ asset('back-office') }}/assets/custom/ajax-gateway.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('back-office') }}/assets/js/pages-auth.js"></script>
  </body>
</html>
