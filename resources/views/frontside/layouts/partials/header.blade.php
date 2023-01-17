@php
$segment = Request::segment(1);
@endphp
<style>
    ul.dropdown-menu.user-dropdown {
        border-top-right-radius: 0;
        border-top-left-radius: 0;
        padding: 1px 0 0 0;
        border-top-width: 0;
        width: 250px;
        position: absolute;
        right: 0;
        left: auto;
    }
    ul.dropdown-menu.user-dropdown li.user-header {
        background: #009a71;
        color: white;
        text-align: center;
        padding: 20px;
    }
    ul.dropdown-menu.user-dropdown li.user-header img {
        border-radius: 50%;
        max-width: 100px;
        max-height: 100px;
        min-height: 100px;
    }
    ul.dropdown-menu.user-dropdown li.user-body {
        padding: 10px 0px;
    }
    ul.dropdown-menu.user-dropdown li.user-body a {
        padding: 10px 5px;
    }
    ul.dropdown-menu.user-dropdown li.user-body a:hover {
        background: #009a71;
        color: white;
        text-decoration: none;
    }
    .user-account-btn{
        margin-left: 10px;
        border: 1px solid #009a71;
        border-radius: 25px;
        padding: 10px 30px;
        color: #009a71 !important;
        transition: 0.3s;
    }
    .user-account-btn:hover{
        color: white !important;
        background-color: #009a71 !important;
    }
    img#nav-image {
        width: 20px;
        margin-right: 10px;
        border-radius: 20px;
        min-height: 20px;
        min-width: 20px;
    }
    ul.dropdown-menu.currency-switch-dropdown {
        max-height: 240px;
        overflow: auto;
    }
    .currency-switch-dropdown {
        max-height: 350px;
        overflow-y: auto;
        left: -137px !important;
        width: 232px !important;
        padding: 0 !important;
        background: #f0f8ff;
    }
</style>
<header class="main-header"
    style="border-top: 1px solid #009a71;border-bottom: 1px solid #efefef;box-shadow: 0px 0px 2px 2px #efefef;">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="mbl-header-wrapper">
            <div class="navbar-header">
                <a href="{{ route('frontside.home.index') }}" class="navbar-brand logo"><img
                        src="{{ checkImage(asset('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" ></a>
                        {{-- src="{{ asset('storage/uploads/' . $site_settings[0]->site_logo) }}" ></a> --}}
                <button type="button" class="mobile-nav navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-right" id="navbar-collapse">
                <ul class="nav navbar-nav custom-margin header-nav">
                    <li><a @if ($segment == null) class="active" @endif href="{{ route('frontside.home.index') }}">{{ __('Home') }}</a></li>
                    <li><a @if ($segment == 'shop') class="active" @endif href="{{ route('frontside.shop.index') }}">{{ __('Shop') }}</a></li>
                    <li><a @if ($segment == 'about') class="active" @endif href="{{ route('frontside.about.index') }}">{{ __('About Us') }}</a></li>
                    <li><a @if ($segment == 'contact') class="active" @endif href="{{ route('frontside.contact.index') }}">{{ __('Contact Us') }}</a></li>
                    @if(@Auth::user()->contact->type != 3)
                    <li>
                        <a href="{{ route('frontside.shop.cart') }}">
                            <span class="price" style="color:black">
                                <i class="fa fa-shopping-cart"></i>
                                <b id="cart_zero">{{ isset(Auth::user()->cart) ? count(Auth::user()->cart->cart_items) : (Session::get('cart_items') ? count(Session::get('cart_items')) : 0) }}</b>
                            </span>
                        </a>
                    </li>
                    @endif
                    @if(!stripos(Request::url(),'email/verify'))
                        @if(\App\Models\Languages::where('is_active',1)->count() > 0)
                            <li class="dropdown language-bar">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-primary dropdown-toggle"data-toggle="dropdown">
                                        <img src="{{asset('frontside/dist/flag/'.App::getLocale().'.svg')}}"
                                        height="30px" width="30px"style="border-radius:50%;"> {{ App::getLocale() }} <span class="caret">
                                    </button>
                                    <div class="dropdown-menu">
                                    @foreach(\App\Models\Languages::where('is_active',1)->get() as $language)
                                    {{-- <a class="dropdown-item  @if($language->iso_code == App::getLocale()) class= active @endif" href="{{ url('lang/'.$language->iso_code) }}"> --}}
                                    <a class="dropdown-item  @if($language->iso_code == App::getLocale()) class= active @endif" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($language->iso_code, null, [], true) }}">
                                        <span class="lang-name">{{ucwords($language->name)}}</span>
                                        <img src="{{asset('frontside/dist/flag/'.$language->iso_code.'.svg')}}"height="30px">
                                    </a>
                                    @endforeach
                                    </div>
                                </div>
                            </li>
                        @endif
                        @php
                            // dd(Auth::user(), auth()->user());
                        @endphp
                        @if(Auth::user())
                            @if(@Auth::user()->contact->type == 3)
                                <li class="transparent-button dark-bg"><a href="{{ route('frontside.reseller.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            @endif
                            <li class="">
                                <a class="btn btn-default user-account-btn dropdown-toggle" data-toggle="dropdown" href="#.">
                                    <span><img id="nav-image" src="{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(Auth::user()->contact->id) . '/' . Auth::user()->contact->image), 'avatar5.png') !!}"
                                        ></span>{{ ucwords(Auth::user()->name) }}
                                </a>
                                <ul class="dropdown-menu user-dropdown">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
                                        <img id="imagePreview" src="{!! checkImage(asset('storage/uploads/contact/' . Hashids::encode(Auth::user()->contact->id) . '/' . Auth::user()->contact->image), 'avatar5.png') !!}"
                                        >
                                        <p>
                                            {{ Auth::user()->name }} <br>
                                            <small>
                                                {{ __('Member since') }}<strong> {{ \Carbon\Carbon::parse(Auth::user()->created_at)->diffForHumans() }}</strong>
                                            </small>
                                        </p>
                                        @if(@Auth::user()->contact->type == 3)
                                        {{-- <a class="btn btn-default" href="{{ route('frontside.reseller.dashboard') }}">{{ __('Reseller Dashboard') }}</a><br /> --}}
                                        @endif
                                        @if(Auth::user()->email_verified_at == null)
                                        <span class="badge unverified-badge">{{ __('Email Unverified') }}</span>
                                        @else
                                        <span class="badge verified-badge">{{ __('Email Verified') }}</span>
                                        @endif
                                    </li>
                                    <li class="user-body">
                                        <div class="row">
                                            <div class="col-xs-4 text-center">
                                                @if(@Auth::user()->contact->type == 3)
                                                    <a href="{{ route('frontside.reseller.dashboard') }}">{{ __('Account') }}</a>
                                                @else
                                                    <a href="{{ route('user.dashboard') }}">{{ __('Account') }}</a>
                                                @endif
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <a href="{{ route('user.dashboard.profile') }}">{{ __('Profile') }}</a>
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <a href="{{ route('logout') }}">{{ __('Logout') }}</a>
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </li>
                                </ul>
                            </li>
                            {{-- <li class="currency-switch">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#.">{{ Session::get('currency_code') ? Session::get('currency_symbol') .' - '. Session::get('currency_code'): "€ - EUR" }}</a>
                                <ul class="dropdown-menu currency-switch-dropdown ">
                                    @foreach($currencies as $currency)
                                    @if(Session::get('currency_code') != $currency->code)
                                        <li>
                                            <a href="{{ route('switch.currency', $currency->code) }}">{{ $currency->symbol .' - '. $currency->code }}</a>
                                        </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </li> --}}
                        @else
                            {{-- <li class="transparent-button"><a href="{{ route('admin.login') }}">{{ __('Login') }}</a></li> --}}
                            <li class="transparent-button dark-bg"><a href="{{ route('reseller.signup') }}">{{ __('Become Reseller') }}</a></li>
                            <li class="transparent-button"><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        @endif
                    @endif
                    <li class="currency-switch">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#.">{{ Session::get('currency_code') ? Session::get('currency_symbol') .' - '. Session::get('currency_code'): "€ - EUR" }}</a>
                        <ul class="dropdown-menu currency-switch-dropdown ">
                            @foreach($currencies as $currency)
                            @if(Session::get('currency_code') != $currency->code)
                                <li>
                                    <a href="{{ route('switch.currency', $currency->code) }}">{{ $currency->symbol .' - '. $currency->code }}</a>
                                </li>
                            @endif
                            @endforeach
                        </ul>
                    </li>

                </ul>
            </div>
            </div>
        </div>
    </nav>
    @if ($segment == 'product-details')
        <div class="row dark-green div-breadcrumbs" style="background: #009a71; color: white; padding: 10px;">
            <div class="container">
                <div>
                    <a style="color:white;font-weight:500;" href="{{ route('frontside.shop.index') }}">{{ __('Products') }}</a> /
                    {{ @$product->product_name }}
                </div>
            </div>
        </div>
    @endif
</header>
