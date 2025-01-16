<!--

=========================================================
* Volt Free - Bootstrap 5 Dashboard
=========================================================

* Product Page: https://themesberg.com/product/admin-dashboard/volt-bootstrap-5-dashboard
* Copyright 2021 Themesberg (https://www.themesberg.com)
* License (https://themesberg.com/licensing)

* Designed and coded by https://themesberg.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. Please contact us to request a removal.

-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <!-- Primary Meta Tags -->
  <title>{{ $title ?? 'Sehatea Admin' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="title" content="Volt - Free Bootstrap 5 Dashboard" />
  <meta name="author" content="Themesberg" />
  <meta name="description"
    content="Volt Pro is a Premium Bootstrap 5 Admin Dashboard featuring over 800 components, 10+ plugins and 20 example pages using Vanilla JS." />
  <meta name="keywords"
    content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, themesberg, themesberg dashboard, themesberg admin dashboard" />
  <link rel="canonical" href="https://themesberg.com/product/admin-dashboard/volt-premium-bootstrap-5-dashboard" />

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://demo.themesberg.com/volt-pro" />
  <meta property="og:title" content="Volt - Free Bootstrap 5 Dashboard" />
  <meta property="og:description"
    content="Volt Pro is a Premium Bootstrap 5 Admin Dashboard featuring over 800 components, 10+ plugins and 20 example pages using Vanilla JS." />
  <meta property="og:image"
    content="https://themesberg.s3.us-east-2.amazonaws.com/public/products/volt-pro-bootstrap-5-dashboard/volt-pro-preview.jpg" />

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image" />
  <meta property="twitter:url" content="https://demo.themesberg.com/volt-pro" />
  <meta property="twitter:title" content="Volt - Free Bootstrap 5 Dashboard" />
  <meta property="twitter:description"
    content="Volt Pro is a Premium Bootstrap 5 Admin Dashboard featuring over 800 components, 10+ plugins and 20 example pages using Vanilla JS." />
  <meta property="twitter:image"
    content="https://themesberg.s3.us-east-2.amazonaws.com/public/products/volt-pro-bootstrap-5-dashboard/volt-pro-preview.jpg" />

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('volt') }}/assets/img/favicon/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="32x32"
    href="{{ asset('volt') }}/assets/img/favicon/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="16x16"
    href="{{ asset('volt') }}/assets/img/favicon/favicon-16x16.png" />
  <link rel="manifest" href="{{ asset('volt') }}/assets/img/favicon/site.webmanifest" />
  <link rel="mask-icon" href="{{ asset('volt') }}/assets/img/favicon/safari-pinned-tab.svg" color="#ffffff" />
  <meta name="msapplication-TileColor" content="#ffffff" />
  <meta name="theme-color" content="#ffffff" />

  <!-- Sweet Alert -->
  <link type="text/css" href="{{ asset('volt') }}/vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet" />

  <!-- Notyf -->
  <link type="text/css" href="{{ asset('volt') }}/vendor/notyf/notyf.min.css" rel="stylesheet" />

  <!-- Volt CSS -->
  <link type="text/css" href="{{ asset('volt') }}/css/volt.css" rel="stylesheet" />
  <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

  {{-- select2 --}}
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  {{-- choice  --}}
  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script> --}}

  {{-- chooice local --}}
  <!-- Include base CSS (optional) -->
  {{-- <link rel="stylesheet" href="{{ asset('choice/css/base.min.css') }}" /> --}}
  <!-- Include Choices CSS -->
  {{-- <link rel="stylesheet" href="{{ asset('choice/css/choices.min.css') }}" /> --}}
  <!-- Include Choices JavaScript -->
  {{-- <script src="{{ asset('choice/js/choices.min.js') }}"></script> --}}

  {{-- FLATPICKR CDN --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


  {{-- TOM SELECT CDN --}}
  <link href="{{ asset('tom/css/tom-select.css') }}" rel="stylesheet">
  <script src="{{ asset('tom/js/tom-select.complete.min.js') }}"></script>


  <style>
    .choices__inner {
      border-radius: 8px !important;
      height: 35.18px !important;
    }

    .choices__inner select {
      height: 35.18px !important;
    }

    label.wajib::after {
      content: "*";
      color: red;
      margin-left: 4px;
    }
  </style>



  {{-- @vite(['resources/css/app.css', 'resources/css/app.js']) --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}



  @vite(['resources/css/app.css', 'resources/js/app.js'])


  @livewireStyles
  @stack('css')

</head>

<body>
  <!-- NOTICE: You can use the _analytics.html partial to include production code specific code & trackers -->

  <nav class="navbar navbar-dark navbar-theme-primary col-12 d-lg-none px-4">
    <a class="navbar-brand me-lg-5" href="{{ asset('volt') }}/index.html">
      <img class="navbar-brand-dark" src="{{ asset('volt') }}/assets/img/brand/light.svg" alt="Volt logo" />
      <img class="navbar-brand-light" src="{{ asset('volt') }}/assets/img/brand/dark.svg" alt="Volt logo" />
    </a>
    <div class="d-flex align-items-center">
      <button class="navbar-toggler d-lg-none collapsed" type="button" data-bs-toggle="collapse"
        data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>

  {{-- sidebar --}}
  {{-- <x-sidebar /> --}}
  @include('components.layouts.partials.sidebar');
  {{-- sidebar --}}

  <main class="content">

    <livewire:component.navbar-component />


    <div class="p-5" style="min-height: 80vh;">
      @php
        $currentMaster = ['cabang', 'kategori', 'merk', 'brand', 'supplier', 'bank', 'instansi', 'jasa'];
      @endphp
      @isset($currentMaster)
        @if (in_array(Route::currentRouteName(), $currentMaster))
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Perhatian!</strong> Menghapus data master dapat memengaruhi data lain yang terkait. Harap pastikan
            bahwa penghapusan ini benar-benar diperlukan dan dilakukan dengan hati-hati.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
      @endisset

      {{ $slot }}
    </div>

    <footer class="mb-4 mt-4 rounded bg-white p-4 shadow">
      <div class="row">
        <div class="col-12">
          <p class="text-lg-start mb-0 text-center">
            © <span class="current-year"></span>
            <a class="text-primary fw-normal" href="https://themesberg.com" target="_blank">Sanca Developer</a>
          </p>
        </div>

      </div>
    </footer>
  </main>

  <!-- Core -->
  <script src="{{ asset('volt') }}/vendor/@popperjs/core/dist/umd/popper.min.js"></script>
  <script src="{{ asset('volt') }}/vendor/bootstrap/dist/js/bootstrap.min.js"></script>

  <!-- Vendor JS -->
  <script src="{{ asset('volt') }}/vendor/onscreen/dist/on-screen.umd.min.js"></script>

  <!-- Slider -->
  <script src="{{ asset('volt') }}/vendor/nouislider/distribute/nouislider.min.js"></script>

  <!-- Smooth scroll -->
  <script src="{{ asset('volt') }}/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

  <!-- Charts -->
  <script src="{{ asset('volt') }}/vendor/chartist/dist/chartist.min.js"></script>
  <script src="{{ asset('volt') }}/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>

  <!-- Datepicker -->
  <script src="{{ asset('volt') }}/vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

  <!-- Sweet Alerts 2 -->
  <script src="{{ asset('volt') }}/vendor/sweetalert2/dist/sweetalert2.all.min.js"></script>

  <!-- Moment JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

  <!-- Vanilla JS Datepicker -->
  <script src="{{ asset('volt') }}/vendor/vanillajs-datepicker/dist/js/datepicker.min.js"></script>

  <!-- Notyf -->
  <script src="{{ asset('volt') }}/vendor/notyf/notyf.min.js"></script>

  <!-- Simplebar -->
  <script src="{{ asset('volt') }}/vendor/simplebar/dist/simplebar.min.js"></script>

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>

  <!-- Volt JS -->
  <script src="{{ asset('volt') }}/assets/js/volt.js"></script>

  {{-- <script>
									alert("test");
									document.addEventListener('livewire:load', function() {
												$('.select2').select2();
												Livewire.hook('message.processed', function() {
															$('.select2').select2();
												});
									});
						</script> --}}

  @livewireScripts
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('script')






  <x-livewire-alert::scripts />

</body>

</html>
