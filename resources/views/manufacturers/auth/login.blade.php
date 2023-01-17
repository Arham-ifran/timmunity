@extends('frontside.layouts.app')
@section('title', __('Manufacturer Login Form'))
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
                            <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>
                           
                            <form action="{{route('manufacturers.login.submit')}}" method="post">
                                {{ csrf_field()}}
                              
                                <div class="form-group has-feedback">
                                    <input type="text" class="form-control " id="email"
                                        name="email" placeholder="{{ __('Email') }}" value="" required
                                        autocomplete="email" autofocus>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                   
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="password" class="form-control"
                                        id="password" placeholder="{{ __('Password') }}" name="password" required="">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <!-- <label>
                                                    <input type="checkbox" class="custom-control-input" id="rememberme"
                                                        name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    {{ __('Remember Me') }}
                                                </label> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        @if (Route::has('manufacturers.password.request'))
                                            <a class="reg-text text-center" href="{{ route('manufacturers.password.request') }}">
                                                {{ __('Forgot Password?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <button type="submit"
                                        class="bg-gareen btn skin-green-light-btn btn-block btn-flat">{{ __('Login') }}</button>
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
    </section>
@endsection
