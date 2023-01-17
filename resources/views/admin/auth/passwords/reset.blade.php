@extends('frontside.layouts.app')
@section('title', __('Admin Login Form'))
@section('content')
    <section class="content-section" id="account-page">
        <div class="container">
            <div class="row mt-6 bottom-space">
                <div class="container">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <!-- Banner Section -->
                        <div class="login-box-body">

                            <div class="login-logo">
                                <a href="{{ route('admin.login') }}">
                                    <img src="{{ asset('backend/dist/img/logo.png') }}"></a>
                            </div>
                            <p class="login-box-msg">{{ __('Reset Password') }}</p>

                            <div class="login-box-body">
                            <form action="{{ route('admin.password.update') }}" method="POST" id="reset-form">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="form-group has-feedback">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="Email" required autocomplete="email" readonly>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="new-password">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Retype password') }}" required autocomplete="new-password">
                                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                </div>
                                <div class="row">
                                    <!-- /.col -->
                                    <div class="row">
                                    <button type="submit" class="bg-gareen btn btn-primary btn-block btn-flat">{{ __('Reset Password') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </div>
@endsection
@section('script')
    <script>
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
@endsection

