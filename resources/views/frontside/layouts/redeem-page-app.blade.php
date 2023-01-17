@php

$last_segment = request()->segment(count(request()->segments()));
$second_last_segment = request()->segment(count(request()->segments())-1);

@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title> TIMmunity GmbH | Voucher Redeemed Page</title>
    <link rel="shortcut icon" href="{{ asset('frontside/dist/img/favicon.png')}}">
    <link rel="stylesheet" href="{{asset('frontside/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontside/bower_components/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/dist/css/loader.css')}}">
    <link rel="stylesheet" href="{{asset('frontside/dist/css/custom-style.css')}}">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        .bg-red, .bg-yellow, .bg-aqua, .bg-blue, .bg-light-blue, .bg-green, .bg-navy, .bg-teal, .bg-olive, .bg-lime, .bg-orange, .bg-fuchsia, .bg-purple, .bg-maroon, .bg-black, .bg-red-active, .bg-yellow-active, .bg-aqua-active, .bg-blue-active, .bg-light-blue-active, .bg-green-active, .bg-navy-active, .bg-teal-active, .bg-olive-active, .bg-lime-active, .bg-orange-active, .bg-fuchsia-active, .bg-purple-active, .bg-maroon-active, .bg-black-active, .callout.callout-danger, .callout.callout-warning, .callout.callout-info, .callout.callout-success, .alert-success, .alert-danger, .alert-error, .alert-warning, .alert-info, .label-danger, .label-info, .label-warning, .label-primary, .label-success, .modal-primary .modal-body, .modal-primary .modal-header, .modal-primary .modal-footer, .modal-warning .modal-body, .modal-warning .modal-header, .modal-warning .modal-footer, .modal-info .modal-body, .modal-info .modal-header, .modal-info .modal-footer, .modal-success .modal-body, .modal-success .modal-header, .modal-success .modal-footer, .modal-danger .modal-body, .modal-danger .modal-header, .modal-danger .modal-footer {
            color: #fff !important
        }

        .bg-gray {
            color: #000;
            background-color: #d2d6de !important
        }
        .bg-gray-light {
            background-color: #f7f7f7
        }
        .bg-black {
            background-color: #111 !important
        }
        .bg-red, .callout.callout-danger, .alert-danger, .alert-error, .label-danger, .modal-danger .modal-body {
            background-color: #dd4b39 !important
        }
        .bg-yellow, .callout.callout-warning, .alert-warning, .label-warning, .modal-warning .modal-body {
            background-color: #f39c12 !important
        }
        .bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body {
            background-color: #00c0ef !important
        }
        .bg-blue {
            background-color: #0073b7 !important
        }
        .bg-light-blue, .label-primary, .modal-primary .modal-body {
            background-color: #3c8dbc !important
        }
        .bg-green, .callout.callout-success, .alert-success, .label-success, .modal-success .modal-body {
            background-color: #009a71 !important
        }
        .bg-navy {
            background-color: #001f3f !important
        }
        .bg-teal {
            background-color: #39cccc !important
        }
        .bg-olive {
            background-color: #3d9970 !important
        }
        .bg-lime {
            background-color: #01ff70 !important
        }
        .bg-orange {
            background-color: #ff851b !important
        }
        .bg-fuchsia {
            background-color: #f012be !important
        }
        .bg-purple {
            background-color: #605ca8 !important
        }
        .bg-maroon {
            background-color: #d81b60 !important
        }
        .bg-gray-active {
            color: #000;
            background-color: #b5bbc8 !important
        }
        .bg-black-active {
            background-color: #000 !important
        }
        .bg-red-active, .modal-danger .modal-header, .modal-danger .modal-footer {
            background-color: #d33724 !important
        }
        .bg-yellow-active, .modal-warning .modal-header, .modal-warning .modal-footer {
            background-color: #db8b0b !important
        }
        .bg-aqua-active, .modal-info .modal-header, .modal-info .modal-footer {
            background-color: #00a7d0 !important
        }
        .bg-blue-active {
            background-color: #005384 !important
        }
        .bg-light-blue-active, .modal-primary .modal-header, .modal-primary .modal-footer {
            background-color: #357ca5 !important
        }
        .bg-green-active, .modal-success .modal-header, .modal-success .modal-footer {
            background-color: #009A71 !important
        }
        .bg-navy-active {
            background-color: #001a35 !important
        }
        .bg-teal-active {
            background-color: #30bbbb !important
        }
        .bg-olive-active {
            background-color: #368763 !important
        }
        .bg-lime-active {
            background-color: #00e765 !important
        }
        .bg-orange-active {
            background-color: #ff7701 !important
        }
        .bg-fuchsia-active {
            background-color: #db0ead !important
        }
        .bg-purple-active {
            background-color: #555299 !important
        }
        .bg-maroon-active {
            background-color: #ca195a !important
        }
    .login-box-body, .register-box-body {
        background: #efe;
        padding: 20px;
        border-top: 0;
        color: #666;
    }
    .login-logo, .register-logo {
        font-size: 35px;
        text-align: center;
        margin-bottom: 25px;
        font-weight: 300;
    }
    .login-box-msg, .register-box-msg {
        margin: 0;
        text-align: center;
        padding: 0 20px 20px 20px;
    }
    .pagination>li>a, .pagination>li>span {
        color: #009a71;
    }
    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover{
        background-color: #009a71;
        border-color: #009a71;

    }
    .copy-right {
            padding: 0px;
    }
    .copy-right .col-md-6.text-left {
            /* margin-top: 0px; */
    }
    .copyright{
        background: #fff;
        padding: 12px 15px 12px 15px;
        margin-top: 80px;
    }
    .copyright p{
        font-size: 14px;
        color: #000;
        margin: 0;
        font-weight: 400;
    }
    .footer .right-img{
        position: absolute;
        right: 0;
        bottom: 0;
    }
    .footer-logo-text{
        font-size: 14px;
        color: #fff;
        margin: 0;
        font-weight: 400;
    }
    @media(max-width:991px){
    .footer .right-img {
            background: #fff;
            text-align: center;
            position: relative;
        }
        .footer .right-img a{
            display: inline-block;
            position: absolute;
            right: 0;
            top: 0;
        }
        .copyright p{
            text-align: center;
        }
    }
    @media (max-width: 480px){
    .copyright {
            padding: 12px 0px 5px 0px;
        }
        .copyright p {
            font-size: 12px;
            text-align: center;
        }
        .footer-logo-text{
            font-size: 11px;
        }
    }
    .footer-heading {
        color: #009a71;
        text-align: left;
        text-transform: important;
    }
    .footer-ul {
        list-style: none;
        text-align: left;
        padding: 0px;
    }
    .footer-ul li {
        padding: 4px 0px;
        font-size: 16px;
    }
    .glyphicon {
        font-family: 'Glyphicons Halflings' !important;
    }
    .fa{
        font: normal normal normal 14px/1 FontAwesome !important;
    }
    a{
        color: {{$reseller->color}};
    }
    .navbar-toggle{
        color: {{$reseller->color}};
    }
    .language-bar .dropdown .btn-primary:hover {
        background: {{$reseller->color}} !important;
    }
    .language-bar .dropdown .btn-primary{
        color: {{$reseller->color}} !important;
    }
    ul.nav.navbar-nav.custom-margin.header-nav .active,.language-bar .dropdown-menu > a:hover, .dropdown-menu > a.active{
        background: {{$reseller->color}} !important;

    }
    .footer-links ul li a,footer .contact-info ul li a{
            color: {{$reseller->color}} !important;
        }
    </style>
    @yield('style')
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-204840638-1"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-204840638-1');
    </script>

</head>
<body class="@yield('body_class') {{ App::getLocale()}} {{ App::getLocale() == 'en' ? 'english_body' : 'other_body'}}" @if($second_last_segment == "admin" || $last_segment == "admin" || $second_last_segment == "manufacturer" || $last_segment == "manufacturer"  || request()->url() == route('manufacturers.password.request') || request()->url() == route('admin.password.request') || request()->url() == route('admin.password.email') || strpos(request()->url(),'admin/password/reset') || strpos(request()->url(),'manufacturers/password/reset'))    style="background-color: #009a71;" @endif>

    <div class="loader-parent" id="ajax_loader">
        <div class="loader">
          <div class="square"></div>
             <div class="path">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
             </div>
         </div>
     </div>
    <!-- Header Section -->


    @include('frontside.reseller.partials.header')


    <!-- Content Area -->
    <div id="main-content-wrapper-div">
        @yield('content')
    </div>
    <!-- Footer Section -->

    @include('frontside.reseller.partials.footer')
    @include('frontside.layouts.modals.alert-message')


    <!-- javacrcipt files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- JQuery Validate -->
    <script src="{{ asset('backend/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script>
        
        @if ( $message = Session::get('alert-success') 
            || $message = Session::get('alert-error')
            || $message = Session::get('alert-info')
            || $message = Session::get('alert-warning')
        )
            $('#alert_message_modal').modal('show');
        @endif
        $(document).ajaxStart(function () {
            showLoader();
        });
        $(document).ajaxStop(function () {
            hideLoader();
        });


        function showLoader() {
            $("#ajax_loader").show();
        }

        function hideLoader() {
            $("#ajax_loader").fadeOut();
        }
        setTimeout(function(){
            $('.alert').slideUp("slow");
        },10000)
        $.validator.addMethod("email", function (value, element) {
            return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
        }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
    </script>
    @yield('script')
</body>
</html>
<script>
  AOS.init();
</script>
