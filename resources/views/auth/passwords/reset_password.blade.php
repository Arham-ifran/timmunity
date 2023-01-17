<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('TIMmunity') }} | {{ __('Set Password') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="author" content="{{ env('SITE_AUTHOR') }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 3.3.7 -->
    <link rel="shortcut icon" href="{{ asset('backend/dist/img/favicon.png') . env('ASSET_VERSION') }}">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('backend/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('backend/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend/dist/css/AdminLTE.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/iCheck/square/blue.css') }}">

    <link rel="stylesheet" href="{{ asset('backend/dist/css/custom-style.css') }}">
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <style>
        .loader{
            /* width: 100px; */
            /* height: 100px; */
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
        }

        /* LOADER 2 */

        #loader-2 span{
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 100%;
            background-color: #005a42;
            margin: 15px 5px;
        }

        #loader-2 span:nth-child(1){
            animation: bounce 1s ease-in-out infinite;
        }

        #loader-2 span:nth-child(2){
            animation: bounce 1s ease-in-out 0.33s infinite;
        }

        #loader-2 span:nth-child(3){
            animation: bounce 1s ease-in-out 0.66s infinite;
        }

        @keyframes bounce{
            0%, 75%, 100%{
                -webkit-transform: translateY(0);
                -ms-transform: translateY(0);
                -o-transform: translateY(0);
                transform: translateY(0);
            }

            25%{
                -webkit-transform: translateY(-20px);
                -ms-transform: translateY(-20px);
                -o-transform: translateY(-20px);
                transform: translateY(-20px);
            }
        }


    </style>
</head>

<body class="hold-transition login-page">
    <!-- Header Section -->
    <header class="main-header"
        style="border-top: 1px solid #009a71;border-bottom: 1px solid #efefef;box-shadow: 0px 0px 2px 2px #efefef;">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="index.html" class="navbar-brand logo"><img
                            src="{{ asset('backend/dist/img/logo.png') }}"></a>
                    <button type="button" class="mobile-nav navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse pull-right" id="navbar-collapse">
                    <ul class="nav navbar-nav custom-margin header-nav">
                        <li><a class="active" href="{{ route('frontside.home.index') }}">{{ __('Home') }}</a>
                        </li>
                        <li><a href="{{ route('frontside.shop.index') }}">{{ __('Shop') }}</a></li>
                        <li><a href="{{ route('frontside.about.index') }}">{{ __('About') }} Us</a></li>
                        <li><a href="{{ route('frontside.contact.index') }}">{{ __('Contact') }} Us</a></li>
                        <li class="transparent-button"><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
                    </ul>
                </div>

            </div>

        </nav>
    </header>
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="login-box-body">
            <div class="login-logo">
                <a href="{{ route('login') }}">
                    <img src="{{ asset('backend/dist/img/logo.png') }}"></a>
            </div>
            @if ($user != null)
                <form action="{{ route('password.store') }}" id="reset-form" method="POST">
                    <input type="hidden" name="id" value="{!! Hashids::encode(@$user->id) !!}">
                    @csrf
                    <div class="form-group has-feedback">
                        <input id="email" type="email" class="form-control" name="email" value="{!! old('email', $user->email ?? '') !!}"
                            autocomplete="email" disabled>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input id="name" type="text" class="form-control" name="name"
                            value="{{ isset($user) && $user != null ? ucfirst($user->name) : '' }}" disabled>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>

                    {{-- @if($user->contact->type == 3 && $user->password != null)
                    <div class="row">
                        <!-- /.col -->
                        <div class="row">
                            <p class="text-center">Redirecting</p>
                            <div class="col-md-12 text-center">
                                <div class="loader" id="loader-2">
                                  <span></span>
                                  <span></span>
                                  <span></span>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <small class="">If you are not auto redirected <button type="submit"
                                    class="btn btn-link">{{ __('Click Here') }}</button></small>
                            </div>
                        </div>
                    </div>
                    @else --}}
                    <div class="form-group has-feedback">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            placeholder="Password" required>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @error('password')
                            <div id="password-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group has-feedback">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            placeholder="{{ __('Confirm Password') }}" required>
                        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="row">
                            <button type="submit"
                            class="bg-gareen btn btn-primary btn-block btn-flat">{{ __('Confirm') }}</button>
                        </div>
                    </div>
                    {{-- @endif --}}
                </form>
            @else
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ __('Invalid signup token') }}.
                </div>
                <a href="{{ route('login') }}">Back to Login</a>
            @endif
        </div>
        <!-- /.reset-box-body -->
    </div>
    <!-- /.reset-box -->
    <!-- Footer Section -->
    <footer class="tim-footer footer" style="margin: auto; text-align: center; float: none;">
        <div class="copy-right"><span>{{ __('Copyright © TIMmunity GmbH -') }}</span></div>
    </footer>
    <!-- jQuery 3 -->
    <script src="{{ asset('backend/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('backend/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ asset('backend/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- JQuery Validate -->
    <script src="{{ asset('backend/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    {{-- @php dd($user->contact->type, $user->password); @endphp --}}
    @if(isset($user) && $user->contact->type == 3 && $user->password != null)
    <script>
        $('#reset-form').submit();
    </script>
    @endif
<script type="text/javascript">
    // Mix password validations
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "{{ __('*Password should contain at least one digit, *Should contain at least one upper & lower case letter,*Should contain at least 8 from the mentioned characters, *Should contain special character  & numbers.')}}");
    jQuery("#reset-form").validate({
        ignore: [],
        errorClass: "invalid-feedback animated fadeInDown",
        errorElement: "div",
        onkeyup: function(element) {$(element).valid()},
        errorPlacement: function (e, a) {
            jQuery(a).parents(".form-group").append(e);
        },
        rules: {
            "password":{
                passwords:true
            },
            "password_confirmation":{
                equalTo: "#password"
            }
        },
        highlight: function (e) {
            jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid");
            jQuery(e).closest(".form-group > .form-control").removeClass("is-invalid").addClass("is-invalid");
        },
        success: function (e) {
            jQuery(e).closest(".form-group").removeClass("is-invalid");
            jQuery(e).closest(".form-group").find('.form-control').removeClass("is-invalid");
            jQuery(e).remove();
        },
        messages: {
            "password_confirmation":{
                equalTo: "{{__('The password must match')}}"
            }
        }
    });
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/localization/messages_{{ session()->get('locale') }}.js" />
</body>

</html>
