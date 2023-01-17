@extends('frontside.layouts.app')
@section('title'){{ __('Verify Your Email') }} @endsection
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="row">
    <div class="col-md-4">

    </div>
    <div class="col-md-4">

        <div class="login-box">
          @if (session('resent'))
          <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <h4><i class="icon fa fa-check"></i>{{ __('Sent Email!') }}</h4>
              {{ __('A fresh verification link has been sent to your email address') }}.
          </div>
          @endif
          <!-- /.login-logo -->
          <div class="login-box-body">
            <div class="login-logo">
            <a href="{{route('admin.login')}}">
                  <img src="{{ asset('backend/dist/img/logo.png')}}"></a>
            </div>
            <p class="login-box-msg">{{ __('Verify Your Email Address') }}</p>
            <hr />
            <form action="{{ route('admin.verification.resend') }}" method="post">
              @csrf
                {{ __('Before proceeding, please check your email for a verification link. If you did not receive the email') }},
              <div class="row">
                  <button type="submit" class="bg-gareen btn skin-green-light-btn btn-block btn-flat">{{ __('click here to request another') }}</button>
              </div>
            </form>
          </div>
          <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->
    </div>
    <div class="col-md-4">

    </div>
</div>

@endsection
