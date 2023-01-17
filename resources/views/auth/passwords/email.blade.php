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
                    <div class="rest-box-wrapper">
                        <div class="login-box reset-box">
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-check"></i>{{ __('Sent Email!') }}</h4>
                                    {{ session('status') }}
                                </div>
                            @endif
                            <!-- /.login-logo -->
                            <div class="login-box-body">

                                <div class="login-logo">
                                    <a href="{{ route('login') }}">
                                        <img src="{{ asset('backend/dist/img/logo.png') }}"></a>
                                </div>
                                <p class="login-box-msg">{{ __('Reset Password') }}</p>
                                <form action="{{ route('password.email') }}" method="post">
                                    @csrf
                                    <div class="form-group has-feedback">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email"
                                            placeholder="{{ __('Email') }}" autofocus>
                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <button type="submit"
                                            class="bg-gareen btn skin-green-light-btn btn-block btn-flat">{{ __('Send Password Reset Link') }}</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.reset-box-body -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
