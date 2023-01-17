{{--
@if ($message = Session::get('alert-success'))
<div class="alert alert-success alert-block text-center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('alert-error'))
<div class="alert alert-danger alert-block text-center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('alert-warning'))
    @php
        $show_warning = 1;
        if ($message == __('Your email is unverified! Kindly verify your email.')){
            if(isset($_COOKIE['close-verification-notice']) && $_COOKIE['close-verification-notice'] == 1 )
            {
                $show_warning = 0;
            }
        }
    @endphp
    @if ($show_warning == 1)
        <div class="alert alert-warning alert-block text-center">
            @if($message == __('Your email is unverified! Kindly verify your email.'))
                <button type="button" class="close close-verification-notice" data-dismiss="alert">×</button>
            @else
                <button type="button" class="close" data-dismiss="alert">×</button>
            @endif
            <strong>{{ $message }}</strong>
        </div>
    @endif
@endif

@if ($message = Session::get('alert-info'))
<div class="alert alert-info alert-block text-center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
@if(isset($errors))
@if ($errors->any())
<div class="alert alert-danger text-center">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{__('Please check the form below for errors')}}
</div>
@endif
@endif --}}

