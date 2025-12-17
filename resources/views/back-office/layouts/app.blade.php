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
    <link rel="stylesheet" href="{{ asset('back-office') }}/assets/vendor/libs/apex-charts/apex-charts.css" />

    <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/select2/select2.css') }}" />

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('back-office/assets/vendor/libs/toastr/toastr.css') }}">
    <!-- Page CSS -->
    <style>
      a.dropdown-toggle::after {
        display: none !important;
      }
      .rt-popup {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #1e88e5;
        color: white;
        padding: 12px 18px;
        border-radius: 6px;
        z-index: 9999;
      }
    </style>
    @stack('css')

    <!-- Helpers -->
    <script src="{{ asset('back-office') }}/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <script src="{{ asset('back-office') }}/assets/js/config.js"></script>
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

            <!-- Include modals at the bottom of the body -->
            <x-modals />

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
    <script src="{{ asset('back-office') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('back-office') }}/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{ asset('back-office') }}/assets/js/dashboards-crm.js"></script>

    <!-- Toastr JS -->
    <script src="{{ asset('back-office/assets/vendor/libs/toastr/toastr.js') }}"></script>

    <script src="{{ asset('back-office/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    <!-- Custom Ajax Gateway JS -->
    <script src="{{ asset('back-office/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('back-office') }}/assets/custom/ajax-gateway.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        var btn = $('#scrollTop');

        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        })

        $(window).scroll(function() {
          if ($(window).scrollTop() > 300) {
            btn.addClass('show');
          } else {
            btn.removeClass('show');
          }
        });

        btn.on('click', function(e) {
          e.preventDefault();
          $('html, body').animate({scrollTop:0}, '300');
        });

        function hideLoader() {
            $('#loading-gif').hide();
        }

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

        $(window).ready(hideLoader);
        @if(Session::has('message'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if(Session::has('error'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if(Session::has('info'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if(Session::has('warning'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>
    @stack('js')
    @vite(['resources/js/app.js'])

    <script>
      function showPopup(message) {
          const div = document.createElement('div')
          div.className = 'rt-popup'
          div.innerText = message

          document.body.appendChild(div)

          setTimeout(() => div.remove(), 4000)
      }

      document.addEventListener('DOMContentLoaded', function () {
        let countEl = document.getElementById('notif-count')

        if (!countEl || typeof Echo === 'undefined') return

        Echo.private('App.Models.User.{{ auth()->id() }}')
            .notification((notification) => {

                // increase counter
                let current = parseInt(countEl.innerText || 0)
                countEl.innerText = current + 1

                toastr.success(notification.message)
            })
      })
  </script>

  </body>
</html>