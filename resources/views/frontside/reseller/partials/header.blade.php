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
    .img{

        float: left;
        height: 50px;
        font-size: 18px;
        line-height: 20px;
    }
    .footer-logo .image {
        max-width: 250px;
        height: auto;
    }
</style>
<header class="main-header"
    style="border-top: 1px solid #009a71;border-bottom: 1px solid #efefef;box-shadow: 0px 0px 2px 2px #efefef;">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="mbl-header-wrapper">
            <div class="navbar-header">
                <a href="" class="navbar-brand">
                    @if($reseller->logo)
                        <img class="img" src="{{ asset('storage/uploads/redeem-page/' . $reseller->logo) }}" />
                    @else
                        <img src="{{ asset('frontside/dist/img/site_logo.png') }}" />
                    @endif
                </a>

                <button type="button" class="mobile-nav navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-right" id="navbar-collapse">
                <ul class="nav navbar-nav custom-margin header-nav">
                    @foreach($reseller->reseller_redeemed_page_navigations as $ind => $title)
                    <li id="list"><a href="{{ $title->url }}" target="_blank">{{ $title->title }}</a></li>
                    @endforeach
                    @if(\App\Models\Languages::where('is_active',1)->count() > 0)
                                <li class="dropdown language-bar">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-primary dropdown-toggle"data-toggle="dropdown">
                                        <img src="{{asset('frontside/dist/flag/'.App::getLocale().'.svg')}}"
                                        height="30px" width="30px"style="border-radius:50%;"> {{ App::getLocale() }} <span class="caret">
                                        </button>
                                        <div class="dropdown-menu">
                                        @foreach(\App\Models\Languages::where('is_active',1)->get() as $language)
                                        <a class="dropdown-item  @if($language->iso_code == App::getLocale()) class= active @endif" href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($language->iso_code, null, [], true) }}">
                                            <span class="lang-name">{{ucwords($language->name)}}</span>
                                            <img src="{{asset('frontside/dist/flag/'.$language->iso_code.'.svg')}}"height="30px">
                                        </a>
                                        @endforeach
                                        </div>
                                    </div>
                                </li>
                            @endif
                </ul>
            </div>

            </div>
        </div>
    </nav>

</header>
