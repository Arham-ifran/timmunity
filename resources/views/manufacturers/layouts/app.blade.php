
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>{{ __('TIMmunity') }} | @yield('title')</title>
   <meta name="author" content="{{ env('SITE_AUTHOR') }}">
   <!-- Tell the browser to be responsive to screen width -->
   <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <!-- Bootstrap 3.3.7 -->
   <link rel="shortcut icon" href="{{ asset('backend/dist/img/favicon.png') }}">
   <link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
   <!-- Font Awesome -->
   <link rel="stylesheet" href="{{ asset('backend/bower_components/font-awesome/css/font-awesome.min.css') }}">
   <!-- SweetAlert2 -->
   <link rel="stylesheet" href="{{ asset('backend/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
   <!-- Ionicons -->
   <link rel="stylesheet" href="{{ asset('backend/bower_components/Ionicons/css/ionicons.min.css') }}">
   <!-- jvectormap -->
   <link rel="stylesheet" href="{{ asset('backend/bower_components/jvectormap/jquery-jvectormap.css') }}">
   <!-- Theme style -->
   <link rel="stylesheet" href="{{ asset('backend/dist/css/AdminLTE.min.css') }}">
   <!-- AdminLTE Skins. Choose a skin from the css/skins
          folder instead of downloading all of them to reduce the load. -->
   <link rel="stylesheet" href="{{ asset('backend/dist/css/skins/_all-skins.min.css') }}">
   <!-- Custom file custom-style.css -->
   <link rel="stylesheet" href="{{ asset('backend/dist/css/custom-style.css') }}">

   <link rel="stylesheet" href="{{ asset('backend/dist/css/dashboard-custom-style.css') }}">
   <!-- SweetAlert2 -->
   <link rel="stylesheet" href="{{ _asset('backend/bower_components/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
   <!-- Summernote CSS-->
   <link rel="stylesheet" href="{{ asset('backend/bower_components/summernote/summernote.min.css') }}">
   <!-- Progress bar -->
   <link href="{{ asset('backend/plugins/progress/jqprogress.min.css') }}" rel="stylesheet" type="text/css">
   <!-- DatePicker -->
   <link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

   <!-- Google Font -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
   <link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
   <link rel="stylesheet" href="{{ asset('backend/dist/css/tokenfield-typeahead.css') }}" />
   <link rel="stylesheet" href="{{ asset('backend/dist/css/bootstrap-tokenfield.css') }}" />

   <script>
      var SITE_URL = "{{ url('/')}}";
      var ADMIN_URL = "{{ url('/admin')}}";
      var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
   </script>
    <style>
    .copy-right{
        padding: 0px !important;
    }
    .main-footer {
        padding: 0px;
    }
    .main-footer .col-md-6.text-left{
        padding-top: 10px;
    }
    </style>

   @yield('styles')
</head>

<body class="hold-transition skin-green-light sidebar-mini" id="setting-page">
<span class="progress"></span>
   <div class="wrapper">

      @include('manufacturers.sections.header')

      @yield('content')
      @include('sweetalert::alert')
      <!-- /.content-wrapper -->
      @include('manufacturers.sections.footer')
   </div>
{{--    </div> --}}


   <!-- ./wrapper -->
   <!-- jQuery 3 -->
   <script src="{{ asset('backend/bower_components/jquery/dist/jquery.min.js') }}"></script>
   <!-- SweetAlert2 -->
   <script src="{{ _asset('backend/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
   <!-- JQuery Validate -->
   <script src="{{ asset('backend/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
   <script src="{{ asset('backend/plugins/jquery-validation/additional-methods.min.js') }}"></script>
   <!-- Bootstrap 3.3.7 -->
   <script src="{{ asset('backend/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
   <!-- FastClick -->
   <script src="{{ asset('backend/bower_components/fastclick/lib/fastclick.js') }}"></script>
   <!-- AdminLTE App -->
   <script src="{{ asset('backend/dist/js/adminlte.min.js') }}"></script>
   <!-- Sparkline -->
   <script src="{{ asset('backend/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
   <!-- SlimScroll -->
   <script src="{{ asset('backend/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
   <!-- Progress bar -->
   <script src="{{ asset('backend/plugins/progress/jqprogress.min.js') }}"></script>
   <!-- ChartJS -->
   <script src="{{ asset('backend/bower_components/chart.js/Chart.js') }}"></script>
   <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
   <script src="{{ asset('backend/dist/js/pages/dashboard2.js') }}"></script>

   <!-- AdminLTE for demo purposes -->
   <script src="{{ asset('backend/dist/js/demo.js') }}"></script>
   <!-- Custom JS -->
   <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
   <!-- Summernote JS -->
   <script src="{{ asset('backend/bower_components/summernote/summernote.min.js') }}"></script>
   <!-- bootstrap datepicker -->
   <script src="{{ asset('backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
   <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
   <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
   <script src="{{ asset('backend/dist/js/bootstrap-tokenfield.js') }}"></script>

   <!-- Core JS For Summernote -->
   <script type="text/javascript">
      $(document).ready(function() {
         $('.summernote').summernote({
            height: 180, //set editable area's height
            minHeight: null,
         });

      });
   </script>
   @yield('scripts')
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/localization/messages_{{ session()->get('locale') }}.js" />

</body>

</html>
