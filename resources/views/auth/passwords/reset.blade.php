@extends('frontside.layouts.app')
@section('title') About @endsection
@section('style')
    <style>

    </style>
@endsection
@section('content')
    <section class="content-section" id="account-page">
        <div class="container">
            <div class="row mt-4 bottom-space">
                <div class="container">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="login-box">
                            <!-- /.login-logo -->
                            <div class="login-box-body">
                                <div class="login-logo">
                                    <a href="{{ route('admin.login') }}">
                                        <img src="{{ asset('backend/dist/img/logo.png') }}"></a>
                                </div>
                                <form method="POST" action="{{ route('password.update') }}" id="reset_form">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="form-group has-feedback">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input id="password" type="password" placeholder="New Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input id="password-confirm" placeholder="Confirm Password" type="password" placeholder="" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                    <div class="form-group row mb-0">
                                            <button type="submit" class="bg-gareen btn btn-primary btn-block btn-flat">
                                                {{ __('Reset Password') }}
                                            </button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.reset-box-body -->
                        </div>
                        <!-- /.reset-box -->
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
<script>
    $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
      // Mix Password Method
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "*Should contain at least 8 from the mentioned characters, *Password should contain at least one digit, *Should contain at least one upper & lower case letter, *Should contain special character  & numbers.");

    jQuery("#reset_form").validate({
        ignore: [],
        errorClass: "invalid-feedback animated fadeInDown",
        errorElement: "div",
        onkeyup: function (element) { $(element).valid() },
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
@endsection
