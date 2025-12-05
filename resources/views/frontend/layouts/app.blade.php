<!DOCTYPE html>

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="light-style layout-navbar-fixed layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('back-office') }}/assets/"
  data-template="vertical-menu-template-no-customizer"
>
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

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

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/css/pages/page-pricing.css" />
    <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/select2/select2.css') }}" />
    <style>
      /* Remove left space when sidebar is hidden */
      .layout-wrapper.layout-content-navbar .layout-page {
        padding-left: 0 !important;
      }

      /* Optional: remove extra margin on navbar */
      .layout-navbar {
        margin-left: 0 !important;
      }

      .layout-navbar-fixed .layout-wrapper:not(.layout-horizontal):not(.layout-without-menu) .layout-page {
        padding-top: 0px !important;
      }

      .layout-navbar-fixed .layout-wrapper:not(.layout-without-menu) .layout-page {
        padding-top: 0px !important;
      }
    </style>

    @stack('css')

    <!-- Helpers -->
    <script src="{{ asset('back-office') }}/assets/vendor/js/helpers.js"></script>

    <script src="{{ asset('back-office') }}/assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <!-- Layout container -->
        <div class="layout-page">

          <!-- Content wrapper -->
          <div class="content-wrapper">

            <!-- Content -->
            @yield('content')
            <!-- / Content -->

            <!-- Footer -->
            <x-footer />
            <!-- / Footer -->
            <!-- Content -->
            {{--  --}}
            <!-- / Content -->

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

    <!-- Main JS -->
    <script src="{{ asset('back-office') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('back-office') }}/assets//js/pages-pricing.js"></script>

    <script src="{{ asset('back-office/assets/js/select2.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('js')

    <script>
      $(document).on('keyup', '.phoneNumber', function() {
        var phone = $(this).val();
        var formattedPhone = formatPhoneNumber(phone);
        $(this).val(formattedPhone);
      });

      function formatPhoneNumber(phone) {
        phone = phone.replace(/\D/g, '');
        if (phone.length > 3) {
          var areaCode = phone.substring(0, 3);
          var telephoneNumber = phone.substring(3, 11);
          phone = "(" + areaCode + ") - " + telephoneNumber;
        }
        return phone;
      }
    </script>
  </body>
</html>
