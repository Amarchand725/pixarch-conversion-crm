<!DOCTYPE html>

<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('backOffice') }}/assets/"
>
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Login | {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <x-favicon />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gauth-session-status
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="{{ asset('backOffice') }}/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <script src="{{ asset('backOffice') }}/assets/js/config.js"></script>
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
                <a href="#" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                  </span>
                  <span class="app-brand-text demo text-body fw-bold ms-1">{{ config('app.name', 'Laravel') }}</span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-1 pt-2">Welcome to  {{ config('app.name', 'Laravel') }}👋</h4>
              <p class="mb-4">Sign-in to your account</p>
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
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/node-waves/node-waves.js"></script>

    <script src="{{ asset('backOffice') }}/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="{{ asset('backOffice') }}/assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('backOffice') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('backOffice') }}/assets/js/pages-auth.js"></script>
  </body>
</html>
