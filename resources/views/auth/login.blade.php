@extends('frontside.layouts.app')
@section('title') Login Form @endsection
@section('style')
<style>

</style>
@endsection
@section('content')
<section class="content-section" id="account-page">
    <div class="container">
        <div class="row mt-4 bottom-space">
            <div class="container">
                <div class="rest-box-wrapper">
                    <!-- Banner Section -->
                    <div class="login-box">
                        {{-- @foreach (['danger', 'warning', 'success', 'info'] as $message)
                            @if (Session::has('alert-' . $message))
                                <div class="alert alert-{{ $message }} alert-dismissible">{{ Session::get('alert-' . $message) }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        @endforeach --}}
                        <!-- /.login-logo -->

                        <div class="login-box-body">
                            <div class="login-logo">
                                <a href="{{ route('login') }}">
                                    <img src="{{ asset('backend/dist/img/logo.png') }}"></a>
                            </div>
                            <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>

                            <form action="{{ route('login') }}" method="post">
                                @csrf
                                <div class="form-group has-feedback">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        placeholder="{{ __('Email') }}" value="{{ old('email') }}" required autocomplete="email"
                                        autofocus>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                        placeholder="{{ __('Password') }}" name="password" required="">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="custom-control-input" id="rememberme" name="remember"
                                                        {{ old('remember') ? 'checked' : '' }}>
                                                    {{ __('Remember Me') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 form-group">
                                        @if (Route::has('password.request'))
                                            <a class="reg-text text-center" href="{{ route('password.request') }}">
                                                {{ __('Forgot Password?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @if(config('services.recaptcha.key'))
                                <div class="captcha-des">
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
                                </div>
                                @endif
                                <div class="row">
                                    <button type="submit"
                                        class="bg-gareen btn skin-green-light-btn btn-block btn-flat">{{ __('Login') }}</button>
                                </div>
                                <div class="row">
                                    @if (Route::has('customer.signup'))
                                        <a href="{{ route('customer.signup') }}"
                                            class="reg-text text-center">{{ __('Register a new membership') }} </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <!-- /.login-box-body -->
                    </div>
                    <!-- /.login-box -->

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script type="text/javascript">
    var correctCaptcha = function(response) {
        $('#capticha_message').hide();
    };
</script>
@endsection
