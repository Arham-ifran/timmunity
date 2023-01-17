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
                                <form action="{{ route('admin.password.email') }}" method="post">
                                    @csrf
                                    <div class="form-group has-feedback">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('Email') }}" autofocus>
                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="bg-gareen btn skin-green-light-btn btn-block btn-flat">{{ __('Send Password Reset Link') }}</button>
                                        </div>
                                        <div class="col-sm-12">
                                            <a class="reg-text text-center" href="{{ route('admin.login') }}">
                                                {{ __('Back to Login') }}
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
