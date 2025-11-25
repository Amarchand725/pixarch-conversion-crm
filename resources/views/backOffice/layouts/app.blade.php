<!DOCTYPE html>

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="light-style layout-navbar-fixed layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('backOffice') }}/assets/"
  data-template="vertical-menu-template-no-customizer"
>
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Dashboard - CRM | {{ config('app.name', 'Laravel') }}</title>
    <title>@yield('title')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <x-favicon />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
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
    <link rel="stylesheet" href="{{ asset('backOffice') }}/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('backOffice/assets/vendor/libs/toastr/toastr.css') }}">
    <!-- Page CSS -->
    @stack('css')

    <!-- Helpers -->
    <script src="{{ asset('backOffice') }}/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <script src="{{ asset('backOffice') }}/assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <x-side-bar-menu />
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <x-nav-bar />
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            {{ $slot }}
            <!-- / Content -->

            <!-- Footer -->
            <x-footer />
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
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
    <script src="{{ asset('backOffice') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('backOffice') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('backOffice') }}/assets/js/dashboards-crm.js"></script>

    <!-- Toastr JS -->
    <script src="{{ asset('backOffice/assets/vendor/libs/toastr/toastr.js') }}"></script>

    @stack('js')
  </body>
</html>