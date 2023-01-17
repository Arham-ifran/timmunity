@extends('frontside.layouts.app')
@section('title', __('Admin Verify Form'))
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


                            @if($admin != null)
                            <form action="{{ route('admin.password.store') }}" method="POST" id="resetform">
                                <input type="hidden" name="id" value="{!! Hashids::encode(@$admin->id) !!}">
                                @csrf
                                <div class="form-group has-feedback">
                                    <input id="email" type="email" class="form-control" name="email" value="{!! old('email', $admin->email ?? '') !!}" autocomplete="email" disabled>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ (isset($admin) && $admin != null) ? ucfirst($admin->firstname.' '. $admin->lastname) : '' }}" disabled>
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required >
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required >
                                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                </div>
                                <div class="row">
                                    <!-- /.col -->
                                    <div class="row">
                                    <button type="submit" class="bg-gareen btn btn-primary btn-block btn-flat">{{ __('Confirm') }}</button>
                                    </div>
                                </div>
                            </form>
                            @else
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    {{ __('Invalid signup token') }}.
                                </div>
                                <a href="{{ route('admin.login') }}">Back to Login</a>
                            @endif
                        </div>
                        <!-- /.login-box-body -->
                    </div>
                    <!-- /.login-box -->

                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </section>
@endsection
@section('script')
<script>
     $.validator.addMethod("passwords", function (value, element) {
        return  /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "*Should contain at least 8 from the mentioned characters, *Password should contain at least one digit, *Should contain at least one upper & lower case letter, *Should contain special character  & numbers.");
    $("#resetform").validate({
        ignore: [],
        errorClass: "invalid-feedback animated fadeInDown",
        errorElement: "div",
        rules: {
            "password_confirmation":{
                required:true,
                equalTo:"#password",
            },
            "password":{
                required:true,
                passwords:true
            }
        },
        messages: {
            "password_confirmation":{
                equalTo: "{{__('The password must match')}}"
            }
        }
    });
</script>
@endsection
