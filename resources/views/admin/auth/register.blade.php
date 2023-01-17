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
                            <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>


                            <form action="{{ route('admin.register') }}" method="POST">
                                @csrf
                                <div class="form-group has-feedback">
                                    <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" placeholder="{{ __('First Name') }}" required autocomplete="firstname" autofocus>
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                    @error('firstname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname') }}" placeholder="{{ __('Last Name') }}" required autocomplete="lastname" autofocus>
                                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                    @error('lastname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required autocomplete="email">
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group has-feedback">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" required autocomplete="new-password">
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
                                    <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                        <input type="checkbox" name="is_term_condition" id="is_term_condition" value="0">
                                        {{ __('I agree to the terms') }}
                                        </label>
                                    </div>
                                    </div>
                                    <!-- /.col -->
                                    <div class="row">
                                    <button type="submit" class="bg-gareen btn btn-primary btn-block btn-flat">{{ __('Register') }}</button>
                                    </div>
                                    <!-- /.col -->
                                    <div class="row">
                                    <a href="{{route('admin.login')}}" class="text-center">{{ __('I already have a membership') }}</a>
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
    </section>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#is_term_condition').click(function()
        {
            if($(this).is(':checked'))
            {
                $(this).val('1');
            }
            else
            {
                $(this).val('0');
            }
        });

    });
</script>
@endsection
