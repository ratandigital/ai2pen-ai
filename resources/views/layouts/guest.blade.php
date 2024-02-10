<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{config('app.localeDirection')}}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}} - @yield('title')</title>
    <link rel="shortcut icon" href="{{config('app.favicon')}}" />

    <link rel="stylesheet" href="{{asset('assets/vendors/feather/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/ti-icons/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/typicons/typicons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/simple-line-icons/css/simple-line-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/vertical-layout-light/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/cdn/css/sweetalert2.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/cdn/css/toastr.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">

    @stack('styles-header')
    @stack('scripts-header')
  </head>

  <body class="{{config('app.localeDirection')}}">
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
          <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo">
                  <a href="{{route('home')}}"><img src="{{config('app.logo')}}" alt="logo"></a>
                </div>
                 @yield('content')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    @include('include.guest-variables')
    <script src="{{asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('assets/js/template.js')}}"></script>
    <script src="{{asset('assets/cdn/js/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/cdn/js/toastr.min.js')}}"></script>
    <script src="{{ asset('assets/js/common/common.js') }}"></script>

    @stack('scripts-footer')
    @stack('styles-footer')
  </body>

</html>
