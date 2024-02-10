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

    @if(isset($load_datatable) && $load_datatable)
        <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.25/css/dataTables.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/ColReorder-1.5.4/css/colReorder.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/Buttons-1.7.1/css/buttons.bootstrap5.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/cdn/css/daterangepicker.css')}}" />
    @endif

    <link rel="stylesheet" href="{{asset('assets/css/vertical-layout-light/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/datetimepicker/jquery.datetimepicker.css')}}">
    <link rel="stylesheet" href="{{asset('assets/cdn/css/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/cdn/css/sweetalert2.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/cdn/css/toastr.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/cdn/css/all.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/summernote/summernote-bs4.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/codemirror/codemirror.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/codemirror/ambiance.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/chocolat/css/chocolat.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/prism/prism.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/modal-right.css') }}">
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">

    <script src="{{ asset('assets/js/common/include_head.js') }}"></script>

    @stack('styles-header')
    @stack('scripts-header')
  </head>

  <?php $profile_pic  = !empty(Auth::user()->profile_pic) ? Auth::user()->profile_pic : asset('assets/images/avatar/avatar-6.png');?>

  <body class="with-welcome-text <?php if(in_array($route_name,$full_width_page_routes)) echo ' sidebar-icon-only ';?> <?php if(isset($load_box_layout) && $load_box_layout) echo 'boxed-layout';?> {{config('app.localeDirection')}}">
    <div class="container-scroller">

      <!--layouts.shared.auth.navbar -->
      @include('layouts.shared.auth.navbar')

      <div class="container-fluid page-body-wrapper">
        <!-- layouts.shared.auth.settings-panel -->
        <!-- layouts.shared.auth.sidebar -->
        @include('layouts.shared.auth.sidebar')

        <div class="main-panel">
          @yield('content')
          <!--layouts.shared.auth.footer -->
          @if($load_footer)
            @include('layouts.shared.auth.footer')
          @endif
        </div>

      </div>
    </div>

    @include('include.auth-variables')

    <script src="{{asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/vendors/chart.js/Chart.min.js')}}"></script>
    <script src="{{asset('assets/vendors/progressbar.js/progressbar.min.js')}}"></script>
    <script src="{{asset('assets/js/off-canvas.js')}}"></script>
    <script src="{{asset('assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('assets/js/template.js')}}"></script>
    <script src="{{asset('assets/js/settings.js')}}"></script>
    <script src="{{asset('assets/js/todolist.js')}}"></script>
    <script src="{{asset('assets/js/Chart.roundedBarCharts.js')}}"></script>

    <script src="{{ asset('assets/vendors/nicescroll/jquery.nicescroll.min.js')}}"></script>

    @if(isset($load_datatable) && $load_datatable)
        <script src="{{asset('assets/vendors/datatables/datatables.min.js')}}"></script>
        <script src="{{asset('assets/vendors/datatables/DataTables-1.10.25/js/dataTables.bootstrap5.min.js')}}"></script>
        <script src="{{asset('assets/vendors/datatables/ColReorder-1.5.4/js/colReorder.bootstrap5.min.js')}}"></script>
        <script src="{{asset('assets/vendors/datatables/Buttons-1.7.1/js/dataTables.buttons.min.js')}}"></script>
        <script src="{{asset('assets/vendors/datatables/Buttons-1.7.1/js/buttons.bootstrap5.min.js')}}"></script>
        <script src="{{asset('assets/vendors/datatables/Buttons-1.7.1/js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('assets/cdn/js/moment.js')}}"></script>
        <script src="{{asset('assets/cdn/js/daterangepicker.min.js')}}"></script>        
    @endif

    <script src="{{asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('assets/vendors/datetimepicker/build/jquery.datetimepicker.full.min.js')}}"></script>
    <script src="{{asset('assets/cdn/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/cdn/js/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/cdn/js/toastr.min.js')}}"></script>
    <script src="{{asset('assets/vendors/OwlCarousel/dist/owl.carousel.min.js')}}"></script>
    <script src="{{asset('assets/vendors/chocolat/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{asset('assets/vendors/prism/prism.js') }}"></script>
    <script src="{{asset('assets/vendors/summernote/summernote-bs4.js') }}"></script>
    <script src="{{asset('assets/vendors/ace-builds/src-min/ace.js')}}"></script>
    <script src="{{asset('assets/vendors/ace-builds/src-min/mode-javascript.js')}}"></script>
    <script src="{{asset('assets/vendors/ace-builds/src-min/theme-chaos.js')}}"></script>
    <script src="{{asset('assets/vendors/codemirror/codemirror.js')}}"></script>
    <script src="{{asset('assets/vendors/codemirror/javascript.js')}}"></script>
    <script src="{{asset('assets/vendors/codemirror/shell.js')}}"></script>

    <script src="{{asset('assets/js/common/common.js')}}"></script>
    <script src="{{asset('assets/js/common/include.js')}}"></script>

    @stack('scripts-footer')
    @stack('styles-footer')
  </body>

</html>

