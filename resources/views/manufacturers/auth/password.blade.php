@extends('frontside.layouts.app')
@section('title', __('Manufacturer Password Form'))
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
                                <a href="">
                                    <img src="{{ asset('backend/dist/img/logo.png') }}"></a>
                            </div>
                            <p class="login-box-msg">{{ __('Please type your Password') }}</p>

                            <div class="login-box-body">
                            <form action="{{route('manufacturers.password')}}" method="POST" id="password_form">
                                @csrf

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
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm password') }}" required autocomplete="new-password">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                </div>
                                <div class="row">
                                    <!-- /.col -->
                                    <div class="row">
                                    <button type="submit" class="bg-gareen btn btn-primary btn-block btn-flat">{{ __('Submit') }}</button>
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
        jQuery("#password_form").validate({
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