@extends('frontside.layouts.app')
@section('title') {{ __('Register New Customer') }} @endsection
@section('content')
    <section class="content-section" id="account-page">
        <div class="container">
            <div class="row mt-4 bottom-space">
                <div class="container">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <!-- Banner Section -->
                        <div class="login-box">
                            @foreach (['danger', 'warning', 'success', 'info'] as $message)
                                @if (Session::has('alert-' . $message))
                                    <div class="alert alert-{{ $message }} alert-dismissible">
                                        {{ Session::get('alert-' . $message) }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                            <!-- /.login-logo -->
                            <div class="login-box-body">
                                <div class="login-logo">
                                    <a href="{{ route('login') }}">
                                        <img src="{{ asset('backend/dist/img/logo.png') }}"></a>
                                </div>
                                <p class="login-box-msg">{{ __('Register a new reseller') }}</p>

                                <form action="{{ route('reseller.register.post') }}" method="post" id="reseller_form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="redeem_page_url" name="redeem_page_url" value="">
                                    <div class="form-group has-feedback">
                                        <input id="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" name="name"
                                            value="{{ old('name') }}" placeholder="{{ __('Name') }}" required
                                            autocomplete="name" autofocus>
                                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                        @error('name')
                                            <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" placeholder="{{ __('Email') }}" required
                                            autocomplete="email">
                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                        @error('email')
                                            <div id="email-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group has-feedback">
                                        <select name="country_id" id="country_id" class="form-control @error('country_id') is-invalid @enderror">
                                            <option value="">{{ __('Select Country') }}</option>
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $country->id }}" {{old ('country_id') == $country->id ? 'selected' : ''}}>{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div id="country_id-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input id="company_name" type="text"
                                            class="form-control @error('email') is-invalid @enderror" name="company_name"
                                            value="{{ old('company_name') }}" placeholder="{{ __('Company Name') }}">
                                        <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
                                        @error('company')
                                            <div id="company_name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            placeholder="{{ __('Password') }}" required autocomplete="new-password">
                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                        @error('password')
                                            <div id="password-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" placeholder="{{ __('Retype password') }}"
                                            required autocomplete="new-password">
                                        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                    </div>
                                    @if(config('services.recaptcha.key'))
                                    <div class="form-group has-feedback">
                                        <div class="g-recaptcha"
                                            data-sitekey="{{config('services.recaptcha.key')}}"
                                            data-callback="correctCaptcha">
                                        </div>
                                        @error('g-recaptcha-response')
                                            <span class="invalid-feedback" role="alert" id="capticha_message">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    @endif
                                    <input type="hidden" selected="selected" value="3" required name="user_type" id="">
                                    <div class="row">
                                        <!-- /.col -->
                                        <div class="row">
                                            <button type="submit"
                                                class="bg-gareen btn btn-primary btn-block btn-flat">{{ __('Register') }}</button>
                                        </div>
                                        <!-- /.col -->
                                        <div class="row">
                                            <a href="{{ route('login') }}"
                                                class="text-center">{{ __('I already have a reseller account') }}</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.login-box-body -->
                        </div>
                        <!-- /.login-box -->

                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
<script type="text/javascript">
    // Email Format validation Method
    $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
      }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
    // Mix Password Method
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "{{ __('*Password should contain at least one digit, *Should contain at least one upper & lower case letter,*Should contain at least 8 from the mentioned characters, *Should contain special character  & numbers.')}}");
    jQuery("#reseller_form").validate({
        ignore: [],
        errorClass: "invalid-feedback animated fadeInDown",
        errorElement: "div",
        onkeyup: function (element) { $(element).valid() },
        errorPlacement: function (e, a) {
            jQuery(a).parents(".form-group").append(e);
        },
        rules: {
            "password":{
                required:true,
                passwords:true
            },
            "password_confirmation":{
                required:true,
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
<script type="text/javascript">
    var correctCaptcha = function(response) {
        $('#capticha_message').hide();
    };
</script>
<script type="text/javascript">

$(document).ready(function() {
        // $('#description').summernote({
        //     height: 200,
        //     placeholder: 'Enter Description'
        // });

        $("#name").on('keyup blur change', function() {
            var title = $("#name").val();
            $("#redeem_page_url").val(convertToSlug(title));
        });
    })



    function convertToSlug(Text) {
        var text = Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        var protocol = window.location.protocol;
        var hostname = window.location.hostname;
        var url = window.location.href;
        var id = url.substring(url.lastIndexOf('/') + 1);
        // var url_slug =  protocol+"//"+hostname+'/'+text;
        var url_slug =  "https://www.{{env('reseller_domain')}}"+'/'+text;
        return url_slug.replace('--', '-');
    }
</script>
@endsection
