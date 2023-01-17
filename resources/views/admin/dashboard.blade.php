<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('TIMmunity') }} | {{ __('Dashboard') }}</title>
    <meta name="author" content="{{ env('SITE_AUTHOR') }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 3.3.7 -->
    <link rel="shortcut icon" href="{{ asset('backend/dist/img/favicon.png')}}">
    <link rel="stylesheet" href="{{ asset('backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('backend/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('backend/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('backend/dist/css/dashboard-custom-style.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/dist/css/skins/_all-skins.min.css') }}">
    <!-- Custom file custom-style.css -->
    <link rel="stylesheet" href="{{ asset('backend/dist/css/custom-style.css') }}">
    <!-- Morris chart -->
    <link rel="stylesheet" href="{{ asset('backend/bower_components/morris.js/morris.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ asset('backend/bower_components/jvectormap/jquery-jvectormap.css') }}">
    <!-- Date Picker -->
    <link rel="stylesheet"
        href="{{ asset('backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet"
        href="{{ asset('backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <style>
            .copy-right{
                padding: 0px !important;
            }
            .main-footer {
                padding: 0px;
            }
            .main-footer .col-md-6.text-left{
                padding-top: 10px;
            }
        </style>
    @yield('styles')
</head>

<body class="skin-black dashboard-timmunity icon-main-page" style="background: #ecf0f5;">
    <div class="wrapper">
        <!-- Top Navigation bar header -->
        <header class="main-header">
            <nav class="navbar navbar-static-top">
                <div class="container-fluid" style="padding-right: 0px;">
                    <div class="navbar-header">
                        {{-- <a href="home.html" class=""><img src="{{ asset('backend\dist\img\product-immunity.png')}}"></a> --}}
                        <a href="{{ route('admin.dashboard') }}" class="navbar-brand" style="width: 60%;">
                            <img style="width: 100%;" src="{{ asset('backend\dist\img\logo.png') }}">
                            <!-- <i class="fa fa-th"></i> -->
                        </a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                             <li class="dropdown language-bar">
                               <div class="dropdown">
                                   <button type="button" class="btn btn-primary dropdown-toggle dashboard" data-toggle="dropdown">
                                   <img src="{{ asset('backend/dist/flag/'.App::getLocale().'.svg')}}"
                                   height="20px" width="20px"style="border-radius:50%;">&nbsp;{{ App::getLocale() }} <span class="caret">
                                   </button>
                                   <div class="dropdown-menu">
                                   @foreach(\App\Models\Languages::where('is_active',1)->get() as $language)
                                   <a href="{{ url('admin/lang/'.Hashids::encode($language->id)) }}" class="dropdown-item  @if($language->iso_code == App::getLocale()) active @endif" href="{{ url('lang/'.$language->iso_code) }}">
                                   <span class="lang-name">{{ucwords($language->name)}}</span>
                                   <img src="{{asset('backend/dist/flag/'.$language->iso_code.'.svg')}}"height="30px">
                                   </a>
                                   @endforeach
                                   </div>
                               </div>
                           </li>
                            <!-- User Account Menu -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <!-- The user image in the navbar-->
                                    <img src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(Auth::user()->id) . '/' . Auth::user()->image), 'avatar5.png') !!}" class="user-image" alt="User Image">
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                    <span
                                        class="hidden-xs">{{ Auth::user()->firstname . ' ' . Auth::user()->lastname ?? '' }}
                                        <span class="caret"></span></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header bg-green">
                                        <img src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(Auth::user()->id) . '/' . Auth::user()->image), 'avatar5.png') !!}" class="img-circle" alt="User Image">
                                        <p>
                                            {{ Auth::user()->firstname . ' ' . Auth::user()->lastname ?? '' }}
                                            <small>{{ __('Member since') }}
                                                <strong>{{ date('M Y', strtotime(Auth::user()->created_at)) }}</strong></small>
                                        </p>
                                    </li>

                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="{{ route('admin.admin-user.edit', ['admin_user' => Hashids::encode(Auth::user()->id)]) }}"
                                                class="btn btn-default btn-flat">{{ __('Profile') }}</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="{{ route('admin.logout') }}"
                                                class="btn btn-default btn-flat">{{ __('Sign out') }}</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!-- /.navbar-custom-menu -->
                </div>
                <!-- /.container-fluid -->
            </nav>
        </header>
        <div class="content-wrapper dashboard-content-wrapper">
            <!-- Icons box section -->
            <section class="sales-home-menu">
                <div class="row custom-margin-set">
                    <div class="sales-icons-list clearfix">
                         @php
                        $url = route('admin.products.index');
                        @endphp
                        <a id="result-06" class="icon-item" href="{{ $url }}">
                            <!-- Start settings svg -->
                            <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 22">
                                <defs>
                                    <style>.cls-1{fill-rule:evenodd;}</style>
                                </defs>
                                <path class="cls-1" d="M3,7.36,0,4.74,8.71,0l3.34,2.35L15.26,0,24,4.64,21,7.32h0l3,4.11-3,1.64V17.2L12,22,3,17.2V13.08l1,.55v3l7.5,4V12.71L9,16.35l-9-4.9L3,7.36Zm9.52,5.36V20.6l7.5-4v-3l-5,2.73-2.5-3.63Zm-11-1.6L8.69,15l1.84-2.69L3.3,8.61,1.48,11.12Zm12,1.25L15.31,15l7.21-3.93L20.7,8.61l-7.23,3.76ZM4,7.84l8-4.43,8,4.44L12,12,4,7.84ZM1.73,4.93,3.84,6.79l7.16-4L8.64,1.18,1.73,4.93ZM13,2.88l7.07,3.9,2.18-1.93L15.34,1.18,13,2.88Z"/>
                           </svg>
                            <!-- Eng setting svg -->
                            <div class="sale-caption"><span>{{ __('Products') }}</span></div>
                        </a>
                        @if(auth()->user()->can('Sales Dashboard'))
                            @php
                            $url = route('admin.sales-dashboard');
                            @endphp
                        @elseif(auth()->user()->can('Quotations Listing'))
                            @php
                            $url = route('admin.quotations.index');
                            @endphp
                        @elseif(auth()->user()->can('Orders Listing'))
                            @php
                            $url = route('admin.quotation.sales.orders');
                            @endphp
                        @elseif(auth()->user()->can('View Sales Analytics'))
                            @php
                            $url = route('admin.sales-team.analytics');
                            @endphp
                        @elseif(auth()->user()->can('Customers Listing'))
                            @php
                            $url = route('admin.customers.index');
                            @endphp
                        @elseif(auth()->user()->can('Products Listing'))
                            @php
                            $url = route('admin.products.index');
                            @endphp
                        @elseif(auth()->user()->can('View Sale Analysis'))
                            @php
                            $url = route('admin.sales-team.analysis');
                            @endphp
                        @elseif(auth()->user()->can('Sales Team Listing'))
                            @php
                            $url = route('admin.sales-team.index');
                            @endphp
                        @elseif(auth()->user()->can('Taxes Listing'))
                            @php
                            $url = route('admin.taxes.index');
                            @endphp
                        @elseif(auth()->user()->can('Ecommerce Categories Listing'))
                            @php
                            $url = route('admin.eccomerce-categories.index');
                            @endphp
                        @elseif(auth()->user()->can('Email Templates Listing'))
                            @php
                            $url = route('admin.email-templates.index');
                            @endphp
                        @endif
                        @canany(['Quotations Listing','Orders Listing','View Sales Analytics','Customers Listing','Products Listing','View Sale Analysis','Sales Team Listing','Taxes Listing','Ecommerce Categories Listing','Email Templates Listing'])
                        <a class="icon-item" href="{{ $url }}">
                            <!-- Start SvgSale -->
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 494.3 494.3"
                                style="enable-background:new 0 0 494.3 494.3;" xml:space="preserve">
                                <g>
                                    <g>
                                        <path d="M416.163,313.3V136.2c0-5.5-4.5-9.3-10-9.3h-38.7c-5.5,0-10.3,3.8-10.3,9.3V296c-6.4,0.2-12.8,1-19,2.5V188.4
                                    c0-5.5-4.7-9.5-10.2-9.5h-38.7c-5.5,0-10.1,3.9-10.1,9.5v149.3c-11.9,16.7-18.3,36.8-18.2,57.4c0,54.8,44.4,99.2,99.1,99.2
                                    c54.8,0,99.2-44.4,99.2-99.1C459.263,362.4,443.163,331.8,416.163,313.3z M377.163,146.9h19v155.8c-6.1-2.4-12.5-4.2-19-5.2V146.9
                                    z M299.163,198.9h19v106.5c-6.7,3.1-13.1,6.9-19,11.4V198.9z M428.263,435.4c-14.3,24.1-40.2,38.8-68.1,38.8
                                    c-43.7,0-79.1-35.5-79.1-79.1c0-32,19.3-60.9,48.9-73.1c1.2-0.2,2.3-0.7,3.3-1.3c8.6-3.2,17.8-4.8,27-4.8c2.1,0,4.1,0.1,6.2,0.3
                                    h0.1c12,0.9,23.7,4.6,34,10.8C438.162,349.3,450.563,397.8,428.263,435.4z" />
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <path d="M360.263,387c-7,0-12.6-5.6-12.6-12.6s5.5-12.6,12.5-12.7c7,0,12.6,5.7,12.6,12.6c0,5.5,4.5,10,10,10
                                    c5.6,0,10.1-4.4,10.2-10c0-13.4-8.2-25.5-20.7-30.4v-8.3c0-5.5-4.5-10-10-10s-10,4.5-10,10v7.2c-17.4,4.5-27.9,22.3-23.4,39.7
                                    c3.6,14.4,16.5,24.4,31.3,24.4c6.9,0,12.5,5.7,12.5,12.6c0,6.9-5.7,12.5-12.6,12.5c-6.9,0-12.5-5.7-12.5-12.6c0-5.5-4.5-10-10-10
                                    s-9.9,4.4-9.8,9.9v0.1c0.1,14.8,10.1,27.8,24.4,31.5v4.5c0,5.5,4.5,10,10,10s10-4.5,10-10v-5.5c16.8-6.6,25-25.5,18.5-42.2
                                    C385.763,395.2,373.663,387,360.263,387z" />
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <path d="M171.163,258.9h-39c-5.5,0-10,4.5-10,10v157c0,5.5,4.5,10,10,10h39c5.5,0,10-4.5,10-10v-157
                                    C181.163,263.4,176.663,258.9,171.163,258.9z M161.163,416.9h-19v-138h19V416.9z" />
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <path d="M250.163,235.9h-39c-5.5,0-10,4.5-10,10v181c0,5.5,4.5,10,10,10h39c5.5,0,10-4.5,10-10v-181
                                    C260.163,240.4,255.663,235.9,250.163,235.9z M240.163,416.9h-19v-161h19V416.9z" />
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <path d="M92.163,344.9h-39c-5.5,0-10,4.5-10,10v71c0,5.5,4.5,10,10,10h39c5.5,0,10-4.5,10-10v-71
                                    C102.163,349.4,97.663,344.9,92.163,344.9z M82.163,416.9h-19v-52h19V416.9z" />
                                    </g>
                                </g>
                                <g>
                                    <g>
                                        <path d="M414.663,0l-68.4,0.1c-5.5-0.1-10,4.4-10.1,9.9s4.4,10,9.9,10.1c0.1,0,0.1,0,0.2,0l44.3-0.1l-108.5,108.1l-37.9-37.6
                                    c-1.9-1.8-4.5-2.7-7.1-2.6c-2.6-0.1-5.2,0.8-7.1,2.6l-192,192c-3.9,3.9-3.9,10.2,0,14c1.9,1.9,4.4,2.9,7.1,2.9s5.2-1,7.1-2.9
                                    l185-185l37.9,38.2c1.8,2,4.4,3.1,7.1,3.2c2.7-0.1,5.2-1.3,7.1-3.2l115.4-115.5l-0.1,44.5c0,5.6,4.4,10.1,10,10.3
                                    c5.6-0.1,10-4.7,10-10.3l0.1-68.6C424.663,4.6,420.163,0,414.663,0z" />
                                    </g>
                                </g>
                            </svg>
                            <!-- End Svg sale -->
                            <div class="sale-caption"><span>{{ __('Sales') }}</span></div>
                        </a>
                        @endcanany
@if(auth()->user()->can('Voucher Dashboard'))
                        @php
                        $url = route('admin.voucher.dashboard');
                        @endphp
                        @elseif(auth()->user()->can('Voucher Order Listing'))
                            @php
                            $url = route('admin.voucher.orders');
                            @endphp
                        @endif
                        @canany(['Voucher Dashboard','Voucher Order Listing'])
                        <a class="icon-item" href="{{ $url }}">
                            <!-- Start SVG Kaspersky -->
                            <svg id="Capa_1" enable-background="new 0 0 511.986 511.986" viewBox="0 0 511.986 511.986"
                                xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path
                                        d="m185.08 114.947c1.405 7.504 8.849 13.422 17.63 11.78 8.526-1.78 13.375-9.955 11.78-17.64-1.873-8.936-10.699-13.914-19.061-11.42-7.469 2.229-11.887 9.942-10.349 17.28z" />
                                    <path
                                        d="m508.225 324.453c-81.619-237.586-76.004-221.308-76.353-222.117-8.496-19.663-24.988-34.949-45.249-41.94-2.901-1.001-167.187-39.431-171.935-41.075-14.561-5.043-34.791-2.216-48.993 10.846l-140.789 129.549c-15.828 14.558-24.906 35.282-24.906 56.86v229.41c0 27.035 21.877 49.03 48.768 49.03h301.205c26.891 0 48.768-21.995 48.768-49.03v-22.174l83.955-35.93c24.409-10.563 35.764-38.912 25.529-63.429zm-158.252 140.563h-301.205c-10.349 0-18.768-8.537-18.768-19.03v-229.41c0-13.209 5.546-25.887 15.217-34.782 120.62-110.988 46.064-41.76 141.269-129.559 4.965-4.576 11.82-6.414 18.619-4.364 6.077 1.815-4.646-6.919 148.421 133.925 9.669 8.894 15.215 21.571 15.215 34.78v229.41c-.001 10.493-8.42 19.03-18.768 19.03zm120.864-104.691-72.097 30.855v-174.604c0-21.578-9.078-42.302-24.904-56.858l-104.848-96.477 108.503 25.746c11.844 4.287 21.498 13.261 26.626 24.757 81.464 237.133 75.969 221.217 76.326 222.038 4.096 9.468-.238 20.489-9.606 24.543z" />
                                    <path
                                        d="m241.959 202.297c-7.637-3.206-16.43.39-19.636 8.028l-73.023 174.04c-4.166 9.929 3.193 20.808 13.825 20.808 5.861 0 11.429-3.457 13.839-9.2l73.023-174.04c3.205-7.64-.389-16.431-8.028-19.636z" />
                                    <path
                                        d="m171.659 245.52c0-28.95-21.203-52.503-47.266-52.503s-47.265 23.553-47.265 52.503 21.203 52.503 47.266 52.503 47.265-23.553 47.265-52.503zm-47.265 22.503c-9.359 0-17.266-10.305-17.266-22.503s7.906-22.503 17.266-22.503 17.266 10.305 17.266 22.503c-.001 12.198-7.907 22.503-17.266 22.503z" />
                                    <path
                                        d="m275.751 312.011c-26.063 0-47.266 23.553-47.266 52.503s21.203 52.503 47.266 52.503 47.266-23.553 47.266-52.503-21.204-52.503-47.266-52.503zm0 75.006c-9.359 0-17.266-10.305-17.266-22.503s7.906-22.503 17.266-22.503 17.266 10.305 17.266 22.503-7.907 22.503-17.266 22.503z" />
                                </g>
                            </svg>
                            <!-- / End Svg Kaspersky -->
                            <div class="sale-caption"><span>{{ __('Vouchers') }}</span></div>
                        </a>
                        @endcanany
                        @if(auth()->user()->can('License Dashboard'))
                        @php
                        $url = route('admin.license.dashboard');
                        @endphp
                        @elseif(auth()->user()->can('Licenses Listing'))
                            @php
                            $url = route('admin.license.index');
                            @endphp
                        @endif
                        @canany(['License Dashboard','Licenses Listing'])
                        <a id="result-07" class="icon-item" href="{{ $url }}">
                            <!-- Start settings svg -->
                            {{-- <img src="{{ asset('backend/dist/img/avast-icon.png') }}" width="50"> --}}
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                    viewBox="0 0 305.406 305.406" style="enable-background:new 0 0 305.406 305.406;" xml:space="preserve">
                                <g>
                                    <g>
                                        <g>
                                            <path d="M77.202,127.888c0,4.971,4.029,9,9,9h130.575c4.971,0,9-4.029,9-9s-4.029-9-9-9H86.202
                                                C81.231,118.888,77.202,122.917,77.202,127.888z"/>
                                            <path d="M86.202,168.538h130.575c4.971,0,9-4.029,9-9c0-4.971-4.029-9-9-9H86.202c-4.971,0-9,4.029-9,9
                                                C77.202,164.509,81.231,168.538,86.202,168.538z"/>
                                            <path d="M86.202,60.538h44.341c4.971,0,9-4.029,9-9c0-4.971-4.029-9-9-9H86.202c-4.971,0-9,4.029-9,9
                                                C77.202,56.509,81.231,60.538,86.202,60.538z"/>
                                            <path d="M149.503,242.374H86.202c-4.971,0-9,4.029-9,9c0,4.971,4.029,9,9,9h63.301c4.971,0,9-4.029,9-9
                                                S154.474,242.374,149.503,242.374z"/>
                                            <path d="M149.503,214.512H86.202c-4.971,0-9,4.029-9,9c0,4.971,4.029,9,9,9h63.301c4.971,0,9-4.029,9-9
                                                C158.503,218.541,154.474,214.512,149.503,214.512z"/>
                                            <path d="M267.067,70.613L199.091,2.636C197.402,0.948,195.113,0,192.727,0H44.703c-4.971,0-9,4.029-9,9v287.406
                                                c0,4.971,4.029,9,9,9h216c4.971,0,9-4.029,9-9V76.977C269.703,74.59,268.755,72.301,267.067,70.613z M199.727,28.728
                                                l41.249,41.249h-41.249V28.728z M251.703,287.406h-198V18h128.023v60.977c0,4.971,4.029,9,9,9h60.977V287.406z"/>
                                            <path d="M199.976,208.512c-16.078,0-29.158,13.081-29.158,29.159s13.08,29.158,29.158,29.158s29.158-13.08,29.158-29.158
                                                S216.054,208.512,199.976,208.512z M199.976,248.829c-6.152,0-11.158-5.005-11.158-11.158c0-6.153,5.006-11.159,11.158-11.159
                                                c6.152,0,11.158,5.006,11.158,11.159C211.134,243.824,206.128,248.829,199.976,248.829z"/>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            <!-- Eng setting svg -->
                            <div class="sale-caption"><span>{{ __('Licenses') }}</span></div>
                        </a>
                        @endcanany
                        <a class="icon-item" href="{{ route('admin.f-secure.index') }}">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                viewBox="0 0 49 59" style="enable-background:new 0 0 49 59;" xml:space="preserve">
                                {{-- <style type="text/css">
                                    .st0{fill:#009A71;}
                                </style> --}}
                                <g>
                                    <path class="st0" d="M46.39,13.45C32.54,8.62,18,8.4,5.14,12.33c-2.74,0.84-3.48,3.15-3,5.11c0.74,2.98,1.81,6.38,3.11,9.49
                                        c0.02,0.06,0.06,0.03,0.05-0.05c-0.16-1.45,0.93-3.28,3.12-3.95c12.17-3.69,22.79-2.79,32.27,0.43c1.6,0.54,3.38-0.31,3.89-1.96
                                        c1.29-4.18,1.82-7.02,1.95-7.72C46.55,13.54,46.44,13.47,46.39,13.45L46.39,13.45z"/>
                                </g>
                                <path class="st0" d="M14.34,22.62c-1.6,0.34-3.81,0.86-5.45,1.42c-3.3,1.13-3.16,5.15-1.39,6.43c0.12-0.75,0.92-1.76,1.81-2.1
                                    c3.32-1.28,6.84-2.07,10.43-2.38C17.93,25.25,16.13,24.19,14.34,22.62L14.34,22.62z"/>
                                <g>
                                    <path class="st0" d="M40.65,30.69c-9.56-4.58-21.05-4.91-30.53-1.22c-1.56,0.61-2.55,2.52-1.56,4.33c1.58,2.89,3.57,5.97,5.83,8.91
                                        c-0.31-1.08,0.24-3.09,2.36-3.74c5.83-1.8,11.97-1.04,16.06,0.61c1.16,0.47,2.77,0.19,3.69-1.19c2.02-3.01,3.84-6.64,4.24-7.45
                                        C40.81,30.83,40.75,30.74,40.65,30.69L40.65,30.69z"/>
                                    <path class="st0" d="M31.16,45.09c-1.04-0.3-3.63-2.33-4.63-3.33c-0.64-0.63-1.56-1.54-2.55-2.62c-2.23,0-4.4,0.23-6.72,0.96
                                        c-2.13,0.67-2.34,3.18-1.41,4.46c1.61,1.98,2.71,3.07,4.49,4.9c1.86,1.92,5.18,1.97,7.09,0.04c1.4-1.4,2.25-2.33,3.82-4.11
                                        C31.35,45.3,31.32,45.14,31.16,45.09L31.16,45.09z"/>
                                </g>
                            </svg>


                            <!-- / End Svg Kaspersky -->
                            <div class="sale-caption"><span>{{ __('F-Secure') }}</span></div>
                        </a>
                        <a class="icon-item" href="{{ route('admin.kss.licenses') }}">
                            <!-- Start SVG Kaspersky -->
                            {{-- <svg id="Capa_1" enable-background="new 0 0 512 512" viewBox="0 0 512 512">
                                <g>
                                    <path
                                        d="m365.813 512c-17.731 0-34.454-7.767-45.88-21.309l-113.729-134.789v96.096c0 33.084-26.916 60-60 60s-60-26.916-60-60v-391.998c0-33.084 26.916-60 60-60s60 26.916 60 60v96.097l113.728-134.789c21.335-25.284 59.264-28.5 84.55-7.166 12.25 10.336 19.74 24.822 21.094 40.792 1.353 15.969-3.594 31.51-13.929 43.759l-132.728 157.306 132.729 157.308c21.334 25.286 18.12 63.214-7.165 84.549-10.81 9.121-24.543 14.144-38.67 14.144zm-165.398-201.22c4.419 0 8.614 1.949 11.464 5.327l130.982 155.237c5.714 6.772 14.079 10.656 22.952 10.656 7.056 0 13.919-2.512 19.324-7.072 12.642-10.667 14.25-29.632 3.583-42.275l-140.891-166.982c-4.714-5.587-4.714-13.759 0-19.346l140.89-166.979c5.168-6.125 7.641-13.895 6.964-21.879-.676-7.985-4.422-15.228-10.546-20.396-12.643-10.666-31.608-9.059-42.276 3.583l-140.192 166.154c-4.062 4.814-10.7 6.578-16.614 4.416-5.916-2.162-9.851-7.79-9.851-14.088v-137.136c0-16.542-13.458-30-30-30s-30 13.458-30 30v391.999c0 16.542 13.458 30 30 30s30-13.458 30-30v-115.303c0-3.541 1.252-6.967 3.536-9.673l9.21-10.917c2.851-3.377 7.045-5.326 11.465-5.326z" />
                                </g>
                            </svg> --}}
                            <svg id="Capa_1" enable-background="new 0 0 512 512" viewBox="0 0 512 512">
                                <g>
                                    <path
                                        d="m365.813 512c-17.731 0-34.454-7.767-45.88-21.309l-113.729-134.789v96.096c0 33.084-26.916 60-60 60s-60-26.916-60-60v-391.998c0-33.084 26.916-60 60-60s60 26.916 60 60v96.097l113.728-134.789c21.335-25.284 59.264-28.5 84.55-7.166 12.25 10.336 19.74 24.822 21.094 40.792 1.353 15.969-3.594 31.51-13.929 43.759l-132.728 157.306 132.729 157.308c21.334 25.286 18.12 63.214-7.165 84.549-10.81 9.121-24.543 14.144-38.67 14.144zm-165.398-201.22c4.419 0 8.614 1.949 11.464 5.327l130.982 155.237c5.714 6.772 14.079 10.656 22.952 10.656 7.056 0 13.919-2.512 19.324-7.072 12.642-10.667 14.25-29.632 3.583-42.275l-140.891-166.982c-4.714-5.587-4.714-13.759 0-19.346l140.89-166.979c5.168-6.125 7.641-13.895 6.964-21.879-.676-7.985-4.422-15.228-10.546-20.396-12.643-10.666-31.608-9.059-42.276 3.583l-140.192 166.154c-4.062 4.814-10.7 6.578-16.614 4.416-5.916-2.162-9.851-7.79-9.851-14.088v-137.136c0-16.542-13.458-30-30-30s-30 13.458-30 30v391.999c0 16.542 13.458 30 30 30s30-13.458 30-30v-115.303c0-3.541 1.252-6.967 3.536-9.673l9.21-10.917c2.851-3.377 7.045-5.326 11.465-5.326z" />
                                </g>
                            </svg>


                            <!-- / End Svg Kaspersky -->
                            <div class="sale-caption"><span>{{ __('Kaspersky') }}</span></div>
                        </a>


                        <a class="icon-item" href="{{ route('admin.channel-pilot-sales-analytics') }}">
                            <svg id="Channel-Pilot-Logo" data-name="Channel-Pilot-Logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300" >
                                <path d="M140.08,170a43.81,43.81,0,0,1-8.39,22.72A47.53,47.53,0,0,1,112.48,208a59.52,59.52,0,0,1-25.77,5.09q-19-.42-30-12.28T44.78,168.64a104.77,104.77,0,0,1,3-29.49,77.94,77.94,0,0,1,12.11-27.77A55.63,55.63,0,0,1,79.65,93.67,51.42,51.42,0,0,1,104.59,88Q124.47,88.37,136,100t12.57,32l-28.48-.08q.16-11.11-4-15.87t-13.19-5.1Q80.36,110.17,75.44,146q-2.26,16.22-2.26,23.14-.42,20.55,15.2,21.13,10.11.33,16.12-5t7.69-15Z"/>
                                <path d="M190.68,170.06,183.5,211.4H154.93L176,89.79l43.69.08q20.29,0,31.94,11.27t10.24,29.2A39.67,39.67,0,0,1,254,151.61a43.25,43.25,0,0,1-18.08,13.77,64,64,0,0,1-25.18,4.76Zm3.93-22.55,17.2.16q10.86-.24,16.63-7.07T233,123.81a12.85,12.85,0,0,0-3.88-8.07,13.26,13.26,0,0,0-8.48-3.31l-20-.09Z"/>
                            </svg>


                            <!-- / End Svg Kaspersky -->
                            <div class="sale-caption"><span>{{ __('Channel Pilot') }} </span></div>
                        </a>
                        @php
                        $url = route('admin.reports.sales-report-dashboard');
                        @endphp
                        <a id="result-06" class="icon-item" href="{{$url}}">
                            <!-- Start settings svg -->
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                                <g>
                                    <g>
                                        <path d="M56.5,49L56.5,49V1c0-0.6-0.4-1-1-1h-45c-0.6,0-1,0.4-1,1v14h2V2h43v46h-9c-0.6,0-1,0.4-1,1v9h-33V43h-2v16
                                            c0,0.6,0.4,1,1,1h35c0.3,0,0.5-0.1,0.7-0.3l10-10c0.1-0.1,0.1-0.2,0.2-0.3v-0.1C56.5,49.2,56.5,49.1,56.5,49z M46.5,50h6.6
                                            l-3.3,3.3l-3.3,3.3L46.5,50L46.5,50z"/>
                                        <path d="M16.5,38h6h4v-2h-3V17c0-0.6-0.4-1-1-1h-6c-0.6,0-1,0.4-1,1v6h-5c-0.6,0-1,0.4-1,1v4h-5c-0.6,0-1,0.4-1,1v8
                                            c0,0.6,0.4,1,1,1h6H16.5z M17.5,18h4v18h-4V24V18z M11.5,25h4v11h-4v-7V25z M5.5,30h4v6h-4V30z"/>
                                        <path d="M50.5,24V7c0-0.6-0.4-1-1-1h-21c-0.6,0-1,0.4-1,1v17c0,0.6,0.4,1,1,1h21C50.1,25,50.5,24.6,50.5,24z M48.5,12h-12V8h12V12
                                            z M34.5,8v4h-5c0-1.6,0-4,0-4H34.5z M29.5,14h5v9h-5C29.5,23,29.5,18.3,29.5,14z M36.5,23v-9h12v9H36.5z"/>
                                        <rect x="28.5" y="28" width="21" height="2"/>
                                        <rect x="28.5" y="33" width="21" height="2"/>
                                        <rect x="28.5" y="38" width="21" height="2"/>
                                        <rect x="14.5" y="6" width="6" height="2"/>
                                        <rect x="14.5" y="11" width="9" height="2"/>
                                        <rect x="14.5" y="43" width="7" height="2"/>
                                        <rect x="24.5" y="43" width="7" height="2"/>
                                        <rect x="34.5" y="43" width="7" height="2"/>
                                        <rect x="14.5" y="48" width="7" height="2"/>
                                        <rect x="24.5" y="48" width="7" height="2"/>
                                        <rect x="34.5" y="48" width="7" height="2"/>
                                        <rect x="14.5" y="53" width="7" height="2"/>
                                        <rect x="24.5" y="53" width="7" height="2"/>
                                        <rect x="34.5" y="53" width="7" height="2"/>
                                    </g>
                                </g>

                            </svg>
                            <!-- Eng setting svg -->
                            <div class="sale-caption"><span>{{ __('Reports') }}</span></div>
                        </a>

                        
                        <!-- / Start Svg App -->
                        
                        <!-- Start SVG Contacts -->
                        @if(auth()->user()->can('Contact Listing'))
                          @php
                          $url = route('admin.contacts.index');
                          @endphp
                        @elseif(auth()->user()->can('Contact Tags Listing'))
                            @php
                            $url = route('admin.contacts-tags.index');
                            @endphp
                        @elseif(auth()->user()->can('Contact Titles Listing'))
                            @php
                            $url = route('admin.contacts-titles.index');
                            @endphp
                        @elseif(auth()->user()->can('Contact Sector of Activities Listing'))
                          @php
                            $url = route('admin.contacts-sectors-activities.index');
                          @endphp
                        @elseif(auth()->user()->can('Contact Currencies Listing'))
                          @php
                            $url = route('admin.currencies.index');
                          @endphp
                        @elseif(auth()->user()->can('Contact Countries Listing'))
                          @php
                            $url = route('admin.contacts-countries.index');
                          @endphp
                        @elseif(auth()->user()->can('Contact Fed. States Listing'))
                          @php
                            $url = route('admin.contacts-fed-states.index');
                          @endphp
                        @elseif(auth()->user()->can('Contact Country Groups Listing'))
                          @php
                            $url = route('admin.contacts-countries-groups.index');
                          @endphp
                        @elseif(auth()->user()->can('Contact Banks Listing'))
                          @php
                            $url = route('admin.contacts-banks.index');
                          @endphp
                        @elseif(auth()->user()->can('Contact Bank Accounts Listing'))
                          @php
                            $url = route('admin.contacts-bank-accounts.index');
                          @endphp
                        @endif
                        @canany(['Contact Tags Listing','Contact Titles Listing','Contact Sector of Activities Listing','Contact Currencies Listing','Contact Countries Listing','Contact Fed. States Listing','Contact Country Groups Listing','Contact Banks Listing','Contact Bank Accounts Listing'])
                        <a class="icon-item" href="{{ $url }} ">
                            <svg x="0px" y="0px" viewBox="0 0 505.4 505.4"
                                style="enable-background:new 0 0 505.4 505.4;" xml:space="preserve">
                                <g>
                                    <g>
                                        <path d="M437.1,233.45c14.8-10.4,24.6-27.7,24.6-47.2c0-31.9-25.8-57.7-57.7-57.7c-31.9,0-57.7,25.8-57.7,57.7
                                    c0,19.5,9.7,36.8,24.6,47.2c-12.7,4.4-24.3,11.2-34.1,20c-13.5-11.5-29.4-20.3-46.8-25.5c21.1-12.8,35.3-36.1,35.3-62.6
                                    c0-40.4-32.7-73.1-73.1-73.1c-40.4,0-73.1,32.8-73.1,73.1c0,26.5,14.1,49.8,35.3,62.6c-17.2,5.2-32.9,13.9-46.3,25.2
                                    c-9.8-8.6-21.2-15.3-33.7-19.6c14.8-10.4,24.6-27.7,24.6-47.2c0-31.9-25.8-57.7-57.7-57.7s-57.7,25.8-57.7,57.7
                                    c0,19.5,9.7,36.8,24.6,47.2C28.5,247.25,0,284.95,0,329.25v6.6c0,0.2,0.2,0.4,0.4,0.4h122.3c-0.7,5.5-1.1,11.2-1.1,16.9v6.8
                                    c0,29.4,23.8,53.2,53.2,53.2h155c29.4,0,53.2-23.8,53.2-53.2v-6.8c0-5.7-0.4-11.4-1.1-16.9H505c0.2,0,0.4-0.2,0.4-0.4v-6.6
                                    C505.2,284.85,476.8,247.15,437.1,233.45z M362.3,186.15c0-23,18.7-41.7,41.7-41.7s41.7,18.7,41.7,41.7
                                    c0,22.7-18.3,41.2-40.9,41.7c-0.3,0-0.5,0-0.8,0s-0.5,0-0.8,0C380.5,227.45,362.3,208.95,362.3,186.15z M194.9,165.35
                                    c0-31.5,25.6-57.1,57.1-57.1s57.1,25.6,57.1,57.1c0,30.4-23.9,55.3-53.8,57c-1.1,0-2.2,0-3.3,0c-1.1,0-2.2,0-3.3,0
                                    C218.8,220.65,194.9,195.75,194.9,165.35z M59.3,186.15c0-23,18.7-41.7,41.7-41.7s41.7,18.7,41.7,41.7c0,22.7-18.3,41.2-40.9,41.7
                                    c-0.3,0-0.5,0-0.8,0s-0.5,0-0.8,0C77.6,227.45,59.3,208.95,59.3,186.15z M125.5,320.15H16.2c4.5-42.6,40.5-76,84.2-76.3
                                    c0.2,0,0.4,0,0.6,0s0.4,0,0.6,0c20.8,0.1,39.8,7.8,54.5,20.3C141.7,279.75,131,298.95,125.5,320.15z M366.8,359.95
                                    c0,20.5-16.7,37.2-37.2,37.2h-155c-20.5,0-37.2-16.7-37.2-37.2v-6.8c0-62.1,49.6-112.9,111.3-114.7c1.1,0.1,2.3,0.1,3.4,0.1
                                    s2.3,0,3.4-0.1c61.7,1.8,111.3,52.6,111.3,114.7V359.95z M378.7,320.15c-5.5-21.1-16-40-30.3-55.6c14.8-12.8,34-20.5,55-20.7
                                    c0.2,0,0.4,0,0.6,0s0.4,0,0.6,0c43.7,0.3,79.7,33.7,84.2,76.3H378.7z" />
                                    </g>
                                </g>
                            </svg>
                            <!-- / End Svg Contacts -->
                            <div class="sale-caption"><span>{{ __('Contacts') }}</span></div>
                        </a>
                        @endcanany
                        <a id="result-06" class="icon-item" href="{{ route('admin.manufacturer.index') }}">
                            <!-- Start settings svg -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"  style="enable-background:new 0 0 122.87 122.88"><path d="M 20 5 L 20 10.46875 L 17 12.25 L 17 9.21875 L 15.5 10.15625 L 12 12.25 L 12 9.21875 L 10.5 10.15625 L 5.5 13.15625 L 5 13.4375 L 5 27 L 27 27 L 27 5 Z M 22 7 L 25 7 L 25 25 L 7 25 L 7 14.53125 L 10 12.75 L 10 15.78125 L 11.5 14.84375 L 15 12.75 L 15 15.78125 L 16.5 14.84375 L 21.5 11.84375 L 22 11.5625 Z M 9 17 L 9 19 L 11 19 L 11 17 Z M 13 17 L 13 19 L 15 19 L 15 17 Z M 17 17 L 17 19 L 19 19 L 19 17 Z M 21 17 L 21 19 L 23 19 L 23 17 Z M 9 21 L 9 23 L 11 23 L 11 21 Z M 13 21 L 13 23 L 15 23 L 15 21 Z M 17 21 L 17 23 L 19 23 L 19 21 Z M 21 21 L 21 23 L 23 23 L 23 21 Z"/></svg>
                            <!-- Eng setting svg -->
                            <div class="sale-caption"><span>{{ __('Manufacturer') }}</span></div>
                        </a>
                        <a id="result-06" class="icon-item" href="{{ route('admin.distributor.index') }}">
                            <!-- Start settings svg -->
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.87 122.88"
                                style="enable-background:new 0 0 122.87 122.88" xml:space="preserve">
                                <g>
                                <path d="M33.24,40.86l27.67-9.21c0.33-0.11,0.68-0.1,0.98,0v0l28.03,9.6c0.69,0.23,1.11,0.9,1.04,1.6 c0.01,0.03,0.01,0.07,0.01,0.11v32.6h-0.01c0,0.56-0.31,1.11-0.85,1.38L62.28,91.08c-0.23,0.14-0.51,0.22-0.8,0.22 c-0.31,0-0.6-0.09-0.84-0.25l-27.9-14.55c-0.53-0.28-0.83-0.81-0.83-1.37h0V42.4C31.9,41.61,32.48,40.97,33.24,40.86L33.24,40.86 L33.24,40.86z M24.28,21.66l8.46,8.46c0.74,0.74,0.74,1.93,0,2.67c-0.73,0.73-1.93,0.73-2.66,0l-8.4-8.4l0.23,5.56 c0,0.05,0,0.11-0.02,0.16c-0.13,0.42-0.4,0.78-0.74,1.03c-0.34,0.25-0.75,0.4-1.2,0.4c-0.56,0.01-1.08-0.22-1.45-0.59 c-0.38-0.37-0.61-0.88-0.62-1.45c-0.16-3.2-0.49-6.78-0.49-9.93c0-0.64,0.22-1.18,0.61-1.56c0.38-0.37,0.9-0.59,1.52-0.6 c2.68-0.1,7.21,0.26,10,0.46c0.56,0.01,1.07,0.23,1.43,0.6c0.36,0.36,0.59,0.86,0.61,1.41v0.05c0,0.56-0.23,1.08-0.6,1.45 c-0.36,0.36-0.86,0.59-1.41,0.6l-0.04,0l0,0c-1.7,0-3.01-0.12-4.31-0.24L24.28,21.66L24.28,21.66z M7.04,59.58H19 c1.04,0,1.88,0.84,1.88,1.88s-0.84,1.88-1.88,1.88H7.12l4.1,3.77c0.04,0.04,0.07,0.08,0.1,0.13c0.2,0.39,0.27,0.83,0.2,1.25 c-0.06,0.41-0.25,0.81-0.57,1.13c-0.39,0.4-0.92,0.61-1.44,0.61c-0.53,0-1.06-0.19-1.46-0.59c-2.37-2.15-5.14-4.45-7.37-6.68 C0.22,62.52,0,61.99,0,61.45c0-0.53,0.22-1.05,0.65-1.49c1.82-1.97,5.29-4.91,7.4-6.74c0.4-0.39,0.92-0.59,1.44-0.59 c0.51,0,1.02,0.19,1.42,0.56l0.04,0.04c0.4,0.4,0.6,0.93,0.6,1.45c0,0.51-0.19,1.02-0.57,1.42l-0.02,0.03l0,0 c-1.2,1.21-2.21,2.04-3.22,2.87L7.04,59.58L7.04,59.58z M21.66,98.6l8.46-8.46c0.73-0.73,1.93-0.73,2.66,0 c0.74,0.74,0.74,1.93,0,2.67l-8.4,8.4l5.56-0.23c0.05,0,0.11,0.01,0.16,0.02c0.42,0.14,0.78,0.4,1.03,0.74 c0.25,0.34,0.4,0.75,0.4,1.2c0,0.56-0.22,1.08-0.59,1.45c-0.37,0.38-0.88,0.61-1.45,0.62c-3.2,0.16-6.78,0.49-9.94,0.49 c-0.64,0-1.18-0.22-1.56-0.6c-0.37-0.38-0.59-0.9-0.6-1.52c-0.11-2.68,0.26-7.21,0.46-10c0.01-0.56,0.23-1.07,0.6-1.43 c0.36-0.36,0.86-0.59,1.4-0.61h0.05c0.56,0,1.08,0.23,1.45,0.6c0.36,0.36,0.59,0.86,0.61,1.41l0,0.03l0,0 c0.01,1.71-0.12,3.01-0.24,4.31L21.66,98.6L21.66,98.6z M59.58,115.83v-11.96c0-1.04,0.84-1.88,1.88-1.88 c1.04,0,1.88,0.84,1.88,1.88v11.88l3.77-4.1c0.04-0.04,0.08-0.07,0.13-0.1c0.39-0.2,0.83-0.27,1.25-0.2 c0.41,0.06,0.81,0.25,1.13,0.57c0.4,0.39,0.61,0.92,0.61,1.45c0,0.53-0.19,1.06-0.59,1.46c-2.15,2.37-4.45,5.14-6.68,7.37 c-0.46,0.45-0.99,0.68-1.53,0.68c-0.53,0-1.05-0.22-1.49-0.65c-1.97-1.82-4.91-5.28-6.74-7.4c-0.39-0.4-0.59-0.92-0.59-1.44 c0-0.51,0.19-1.03,0.56-1.42l0.04-0.04c0.4-0.4,0.93-0.6,1.45-0.6c0.51,0,1.02,0.19,1.42,0.57l0.02,0.02l0,0 c1.21,1.2,2.04,2.21,2.87,3.22L59.58,115.83L59.58,115.83z M98.6,101.22l-8.46-8.46c-0.74-0.74-0.74-1.93,0-2.67 c0.73-0.73,1.93-0.73,2.66,0l8.4,8.4l-0.23-5.56c0-0.05,0-0.11,0.02-0.16c0.13-0.42,0.4-0.78,0.74-1.03c0.34-0.25,0.75-0.4,1.2-0.4 c0.56-0.01,1.08,0.22,1.45,0.59c0.38,0.37,0.61,0.88,0.62,1.45c0.16,3.2,0.49,6.78,0.49,9.94c0,0.64-0.22,1.18-0.61,1.56 c-0.38,0.37-0.9,0.59-1.52,0.6c-2.68,0.1-7.21-0.26-10-0.46c-0.56-0.01-1.07-0.23-1.43-0.6c-0.36-0.36-0.59-0.86-0.61-1.41v-0.05 c0-0.56,0.23-1.08,0.6-1.45c0.36-0.36,0.86-0.59,1.41-0.61l0.04,0l0,0c1.71-0.01,3.01,0.12,4.3,0.24L98.6,101.22L98.6,101.22z M115.84,63.29h-11.96c-1.04,0-1.89-0.84-1.89-1.88c0-1.04,0.85-1.88,1.89-1.88h11.88l-4.1-3.77c-0.04-0.04-0.07-0.08-0.1-0.13 c-0.2-0.39-0.27-0.83-0.2-1.25c0.06-0.41,0.25-0.81,0.57-1.13c0.4-0.4,0.92-0.61,1.45-0.61c0.53,0,1.06,0.19,1.46,0.59 c2.37,2.15,5.14,4.45,7.37,6.68c0.45,0.46,0.68,0.99,0.67,1.53c0,0.53-0.22,1.05-0.65,1.49c-1.82,1.97-5.29,4.91-7.4,6.74 c-0.4,0.39-0.92,0.59-1.44,0.59c-0.51,0-1.03-0.19-1.42-0.56l-0.04-0.04c-0.4-0.4-0.6-0.93-0.6-1.45c0-0.51,0.19-1.03,0.57-1.42 l0.02-0.03l0,0c1.2-1.21,2.21-2.04,3.22-2.87L115.84,63.29L115.84,63.29z M101.21,24.28l-8.46,8.46c-0.73,0.73-1.93,0.73-2.66,0 c-0.74-0.74-0.74-1.93,0-2.66l8.4-8.4l-5.56,0.23c-0.05,0-0.11-0.01-0.16-0.02c-0.42-0.14-0.78-0.4-1.03-0.74 c-0.25-0.34-0.4-0.75-0.4-1.2c0-0.56,0.22-1.08,0.59-1.45c0.37-0.38,0.88-0.61,1.45-0.62c3.2-0.16,6.78-0.49,9.94-0.49 c0.64,0,1.18,0.22,1.56,0.6c0.37,0.38,0.59,0.9,0.6,1.52c0.11,2.68-0.26,7.21-0.46,10c-0.01,0.56-0.23,1.07-0.6,1.44 c-0.36,0.36-0.86,0.59-1.41,0.61h-0.05c-0.56,0-1.08-0.23-1.45-0.6c-0.36-0.36-0.59-0.86-0.61-1.41l0-0.03l0,0 c0-1.71,0.12-3.01,0.24-4.31L101.21,24.28L101.21,24.28z M63.29,7.04V19c0,1.04-0.84,1.88-1.88,1.88c-1.04,0-1.89-0.84-1.89-1.88 V7.13l-3.76,4.09c-0.04,0.04-0.08,0.07-0.13,0.1c-0.39,0.2-0.83,0.27-1.25,0.2c-0.41-0.06-0.81-0.25-1.13-0.57 c-0.4-0.39-0.61-0.92-0.61-1.44c0-0.53,0.19-1.06,0.59-1.46c2.15-2.37,4.45-5.14,6.68-7.37C60.35,0.22,60.89,0,61.43,0 c0.53,0,1.05,0.22,1.49,0.65c1.97,1.82,4.91,5.28,6.74,7.4c0.39,0.4,0.59,0.92,0.59,1.44c0,0.51-0.19,1.02-0.56,1.42l-0.04,0.04 c-0.4,0.4-0.93,0.6-1.45,0.6c-0.51,0-1.02-0.19-1.42-0.57l-0.03-0.02l0,0c-1.21-1.2-2.04-2.21-2.87-3.22L63.29,7.04L63.29,7.04z M39.36,64.75c0-0.59,0.48-1.08,1.08-1.08c0.59,0,1.08,0.48,1.08,1.08v4.39c0,0.03,0,0.07-0.01,0.11c0,0.15,0.02,0.27,0.05,0.37 c0.02,0.03,0.03,0.06,0.06,0.08l2.69,1.25c0.54,0.25,0.77,0.89,0.53,1.43c-0.25,0.54-0.88,0.77-1.42,0.53l-2.75-1.28 c-0.05-0.02-0.1-0.04-0.14-0.08c-0.44-0.28-0.75-0.65-0.94-1.11c-0.15-0.37-0.22-0.78-0.21-1.22v-0.07L39.36,64.75L39.36,64.75 L39.36,64.75z M59.93,87.21V56.02L35,44.72v29.48L59.93,87.21L59.93,87.21L59.93,87.21z M87.86,45.09L63.03,56.04v31.2l24.83-12.62 V45.09L87.86,45.09L87.86,45.09z M61.38,34.74l-23.57,7.85l23.68,10.74L85.17,42.9L61.38,34.74L61.38,34.74L61.38,34.74z"/>
                                </g>
                            </svg>
                            <!-- Eng setting svg -->
                            <div class="sale-caption"><span>{{ __('Distributor') }}</span></div>
                        </a>
                        <!-- Start SVG Website -->
                        @if(auth()->user()->can('Website Dashboard'))
                        @php
                        $url = route('admin.website.dashboard');
                        @endphp
                        @elseif(auth()->user()->can('Website Abandoned Cart Listing'))
                            @php
                            $url = route('admin.website.abandoned.carts');
                            @endphp
                        @elseif(auth()->user()->can('Products Listing'))
                            @php
                            $url = route('admin.products.index');
                            @endphp
                        @elseif(auth()->user()->can('Projects Listing'))
                            @php
                            $url = route('admin.website.projects');
                            @endphp
                        @elseif(auth()->user()->can('Visitors Listing'))
                            @php
                            $url = route('admin.website.visitors');
                            @endphp
                        @elseif(auth()->user()->can('Views Listing'))
                            @php
                            $url = route('admin.website.views');
                            @endphp
                        @elseif(auth()->user()->can('Reseller Listing'))
                            @php
                            $url = route('admin.website.resellers');
                            @endphp
                        @elseif(auth()->user()->can('Lawful Interception Listing'))
                            @php
                            $url = route('admin.website.lawfulinterception');
                            @endphp
                        @elseif(auth()->user()->can('FAQs Listing'))
                            @php
                            $url = route('admin.faqs.index');
                            @endphp
                        @elseif(auth()->user()->can('Contact Us Queries Listing'))
                            @php
                            $url = route('admin.contact-us-queries.index');
                            @endphp
                        @elseif(auth()->user()->can('Payment Gateway Settings'))
                            @php
                            $url = route('admin.website.payment.gateways');
                            @endphp
                        @endif

                        @canany(['Website Abandoned Cart Listing','Projects Listing','Products Listing','Visitors Listing','Views Listing','FAQs Listing','Lawful Interception Listing','Contact Us Queries Listing','Payment Gateway Settings'])
                        <a id="result-05" class="icon-item" href="{{ $url }}">
                            <svg version="1.1" id="Capa_1" viewBox="0 0 480.1 480.1" xml:space="preserve">
                                <g>
                                    <g>
                                        <path
                                            d="M240.135,0.05C144.085,0.036,57.277,57.289,19.472,145.586l-2.992,0.992l1.16,3.48
                                    c-49.776,122.766,9.393,262.639,132.159,312.415c28.673,11.626,59.324,17.594,90.265,17.577
                                    c132.548,0.02,240.016-107.416,240.036-239.964S372.684,0.069,240.135,0.05z M428.388,361.054l-12.324-12.316V320.05
                                    c0.014-1.238-0.26-2.462-0.8-3.576l-31.2-62.312V224.05c0-2.674-1.335-5.172-3.56-6.656l-24-16
                                    c-1.881-1.256-4.206-1.657-6.4-1.104l-29.392,7.344l-49.368-21.184l-6.792-47.584l18.824-18.816h40.408l13.6,20.44
                                    c1.228,1.838,3.163,3.087,5.344,3.448l48,8c1.286,0.216,2.604,0.111,3.84-0.304l44.208-14.736
                                    C475.855,208.053,471.889,293.634,428.388,361.054z M395.392,78.882l-13.008,8.672l-36.264-7.256l-23.528-7.832
                                    c-1.44-0.489-2.99-0.551-4.464-0.176l-29.744,7.432l-13.04-4.344l9.664-19.328h27.056c1.241,0.001,2.465-0.286,3.576-0.84
                                    l27.68-13.84C362.382,51.32,379.918,63.952,395.392,78.882z M152.44,33.914l19.2,12.8c0.944,0.628,2.01,1.048,3.128,1.232
                                    l38.768,6.464l-3.784,11.32l-20.2,6.744c-1.809,0.602-3.344,1.83-4.328,3.464l-22.976,38.288l-36.904,22.144l-54.4,7.768
                                    c-3.943,0.557-6.875,3.93-6.88,7.912v24c0,2.122,0.844,4.156,2.344,5.656l13.656,13.656v13.744l-33.28-22.192l-12.072-36.216
                                    C57.68,98.218,99.777,56.458,152.44,33.914z M129.664,296.21l-36.16-7.24l-13.44-26.808v-18.8l29.808-29.808l11.032,22.072
                                    c1.355,2.712,4.128,4.425,7.16,4.424h51.472l21.672,36.12c1.446,2.407,4.048,3.879,6.856,3.88h22.24l-5.6,28.056l-30.288,30.288
                                    c-1.503,1.499-2.349,3.533-2.352,5.656v20l-28.8,21.6c-2.014,1.511-3.2,3.882-3.2,6.4v28.896l-9.952-3.296l-14.048-35.136V304.05
                                    C136.065,300.248,133.389,296.97,129.664,296.21z M105.616,419.191C30.187,362.602-1.712,264.826,25.832,174.642l6.648,19.936
                                    c0.56,1.687,1.666,3.14,3.144,4.128l39.88,26.584l-9.096,9.104c-1.5,1.5-2.344,3.534-2.344,5.656v24
                                    c-0.001,1.241,0.286,2.465,0.84,3.576l16,32c1.108,2.21,3.175,3.784,5.6,4.264l33.6,6.712v73.448
                                    c-0.001,1.016,0.192,2.024,0.568,2.968l16,40c0.876,2.185,2.67,3.874,4.904,4.616l24,8c0.802,0.272,1.642,0.412,2.488,0.416
                                    c4.418,0,8-3.582,8-8v-36l28.8-21.6c2.014-1.511,3.2-3.882,3.2-6.4v-20.688l29.656-29.656c1.115-1.117,1.875-2.54,2.184-4.088
                                    l8-40c0.866-4.333-1.944-8.547-6.277-9.413c-0.515-0.103-1.038-0.155-1.563-0.155h-27.472l-21.672-36.12
                                    c-1.446-2.407-4.048-3.879-6.856-3.88h-51.056l-13.744-27.576c-1.151-2.302-3.339-3.91-5.88-4.32
                                    c-2.54-0.439-5.133,0.399-6.936,2.24l-10.384,10.344V192.05c0-2.122-0.844-4.156-2.344-5.656l-13.656-13.656v-13.752l49.136-7.016
                                    c1.055-0.153,2.07-0.515,2.984-1.064l40-24c1.122-0.674,2.062-1.614,2.736-2.736l22.48-37.464l21.192-7.072
                                    c2.393-0.785,4.271-2.662,5.056-5.056l8-24c1.386-4.195-0.891-8.72-5.086-10.106c-0.387-0.128-0.784-0.226-1.186-0.294
                                    l-46.304-7.72l-8.136-5.424c50.343-16.386,104.869-14.358,153.856,5.72l-14.616,7.296h-30.112c-3.047-0.017-5.838,1.699-7.2,4.424
                                    l-16,32c-1.971,3.954-0.364,8.758,3.59,10.729c0.337,0.168,0.685,0.312,1.042,0.431l24,8c1.44,0.489,2.99,0.551,4.464,0.176
                                    l29.744-7.432l21.792,7.256c0.312,0.112,0.633,0.198,0.96,0.256l40,8c2.08,0.424,4.244-0.002,6.008-1.184l18.208-12.144
                                    c8.961,9.981,17.014,20.741,24.064,32.152l-39.36,13.12l-42.616-7.104l-14.08-21.12c-1.476-2.213-3.956-3.547-6.616-3.56h-48
                                    c-2.122,0-4.156,0.844-5.656,2.344l-24,24c-1.782,1.781-2.621,4.298-2.264,6.792l8,56c0.403,2.769,2.223,5.126,4.8,6.216l56,24
                                    c1.604,0.695,3.394,0.838,5.088,0.408l28.568-7.144l17.464,11.664v27.72c-0.014,1.238,0.26,2.462,0.8,3.576l31.2,62.312v30.112
                                    c0,2.122,0.844,4.156,2.344,5.656l16.736,16.744C344.921,473.383,204.549,493.415,105.616,419.191z" />
                                    </g>
                                </g>
                            </svg>
                            <div class="sale-caption"><span>{{ __('Website') }}</span></div>
                        </a>
                        @endcanany

                       
                        
                        @if(auth()->user()->can('View General Settings'))
                        @php
                        $url = route('admin.settings');
                        @endphp
                        @elseif(auth()->user()->can('View Sales Settings'))
                            @php
                            $url = route('admin.website.abandoned.carts');
                            @endphp
                        @elseif(auth()->user()->can('User Listing'))
                            @php
                            $url = route('admin.admin-user.index');
                            @endphp
                        @elseif(auth()->user()->can('Company Listing'))
                            @php
                            $url = route('admin.companies.index');
                            @endphp
                        @elseif(auth()->user()->can('CMS Page Listing'))
                            @php
                            $url = route('admin.cms.index');
                            @endphp
                        @elseif(auth()->user()->can('Email Templates Listing'))
                            @php
                            $url = route('admin.email-templates.index');
                            @endphp
                        @elseif(auth()->user()->can('Email Template Labels Listing'))
                            @php
                            $url = route('admin.email-template-labels.index');
                            @endphp
                        @elseif(auth()->user()->can('View Site Settings'))
                            @php
                            $url = route('admin.site.settings');
                            @endphp
                        @elseif(auth()->user()->can('Languages Listing'))
                            @php
                            $url = route('admin.languages.index');
                            @endphp
                        @elseif(auth()->user()->can('Language Translations Listing'))
                            @php
                            $url = route('admin.language-translations.index');
                            @endphp
                        @elseif(auth()->user()->can('Language Modules Listing'))
                            @php
                            $url = route('admin.language-modules.index');
                            @endphp
                        @elseif(auth()->user()->can('Create Label Translations'))
                            @php
                            $url = route('admin.label-translations.index');
                            @endphp
                        @elseif(auth()->user()->can('Create Text Translations'))
                            @php
                            $url = route('admin.text-translations.index');
                            @endphp
                        @endif
                        @canany(['View General Settings','View Sales Settings','User Listing','Company Listing','CMS Page Listing','Email Templates Listing','Email Template Labels Listing','View Site Settings','Languages Listing','Language Translations Listing','Language Modules Listing','Create Label Translations','Create Text Translations'])
                        <a id="result-06" class="icon-item" href="{{ $url }}">
                            <!-- Start settings svg -->
                            <svg id="Layer_1" enable-background="new 0 0 512 512" viewBox="0 0 512 512">
                                <path
                                    d="m272.066 512h-32.133c-25.989 0-47.134-21.144-47.134-47.133v-10.871c-11.049-3.53-21.784-7.986-32.097-13.323l-7.704 7.704c-18.659 18.682-48.548 18.134-66.665-.007l-22.711-22.71c-18.149-18.129-18.671-48.008.006-66.665l7.698-7.698c-5.337-10.313-9.792-21.046-13.323-32.097h-10.87c-25.988 0-47.133-21.144-47.133-47.133v-32.134c0-25.989 21.145-47.133 47.134-47.133h10.87c3.531-11.05 7.986-21.784 13.323-32.097l-7.704-7.703c-18.666-18.646-18.151-48.528.006-66.665l22.713-22.712c18.159-18.184 48.041-18.638 66.664.006l7.697 7.697c10.313-5.336 21.048-9.792 32.097-13.323v-10.87c0-25.989 21.144-47.133 47.134-47.133h32.133c25.989 0 47.133 21.144 47.133 47.133v10.871c11.049 3.53 21.784 7.986 32.097 13.323l7.704-7.704c18.659-18.682 48.548-18.134 66.665.007l22.711 22.71c18.149 18.129 18.671 48.008-.006 66.665l-7.698 7.698c5.337 10.313 9.792 21.046 13.323 32.097h10.87c25.989 0 47.134 21.144 47.134 47.133v32.134c0 25.989-21.145 47.133-47.134 47.133h-10.87c-3.531 11.05-7.986 21.784-13.323 32.097l7.704 7.704c18.666 18.646 18.151 48.528-.006 66.665l-22.713 22.712c-18.159 18.184-48.041 18.638-66.664-.006l-7.697-7.697c-10.313 5.336-21.048 9.792-32.097 13.323v10.871c0 25.987-21.144 47.131-47.134 47.131zm-106.349-102.83c14.327 8.473 29.747 14.874 45.831 19.025 6.624 1.709 11.252 7.683 11.252 14.524v22.148c0 9.447 7.687 17.133 17.134 17.133h32.133c9.447 0 17.134-7.686 17.134-17.133v-22.148c0-6.841 4.628-12.815 11.252-14.524 16.084-4.151 31.504-10.552 45.831-19.025 5.895-3.486 13.4-2.538 18.243 2.305l15.688 15.689c6.764 6.772 17.626 6.615 24.224.007l22.727-22.726c6.582-6.574 6.802-17.438.006-24.225l-15.695-15.695c-4.842-4.842-5.79-12.348-2.305-18.242 8.473-14.326 14.873-29.746 19.024-45.831 1.71-6.624 7.684-11.251 14.524-11.251h22.147c9.447 0 17.134-7.686 17.134-17.133v-32.134c0-9.447-7.687-17.133-17.134-17.133h-22.147c-6.841 0-12.814-4.628-14.524-11.251-4.151-16.085-10.552-31.505-19.024-45.831-3.485-5.894-2.537-13.4 2.305-18.242l15.689-15.689c6.782-6.774 6.605-17.634.006-24.225l-22.725-22.725c-6.587-6.596-17.451-6.789-24.225-.006l-15.694 15.695c-4.842 4.843-12.35 5.791-18.243 2.305-14.327-8.473-29.747-14.874-45.831-19.025-6.624-1.709-11.252-7.683-11.252-14.524v-22.15c0-9.447-7.687-17.133-17.134-17.133h-32.133c-9.447 0-17.134 7.686-17.134 17.133v22.148c0 6.841-4.628 12.815-11.252 14.524-16.084 4.151-31.504 10.552-45.831 19.025-5.896 3.485-13.401 2.537-18.243-2.305l-15.688-15.689c-6.764-6.772-17.627-6.615-24.224-.007l-22.727 22.726c-6.582 6.574-6.802 17.437-.006 24.225l15.695 15.695c4.842 4.842 5.79 12.348 2.305 18.242-8.473 14.326-14.873 29.746-19.024 45.831-1.71 6.624-7.684 11.251-14.524 11.251h-22.148c-9.447.001-17.134 7.687-17.134 17.134v32.134c0 9.447 7.687 17.133 17.134 17.133h22.147c6.841 0 12.814 4.628 14.524 11.251 4.151 16.085 10.552 31.505 19.024 45.831 3.485 5.894 2.537 13.4-2.305 18.242l-15.689 15.689c-6.782 6.774-6.605 17.634-.006 24.225l22.725 22.725c6.587 6.596 17.451 6.789 24.225.006l15.694-15.695c3.568-3.567 10.991-6.594 18.244-2.304z" />
                                <path
                                    d="m256 367.4c-61.427 0-111.4-49.974-111.4-111.4s49.973-111.4 111.4-111.4 111.4 49.974 111.4 111.4-49.973 111.4-111.4 111.4zm0-192.8c-44.885 0-81.4 36.516-81.4 81.4s36.516 81.4 81.4 81.4 81.4-36.516 81.4-81.4-36.515-81.4-81.4-81.4z" />
                            </svg>
                            <!-- Eng setting svg -->
                            <div class="sale-caption"><span>{{ __('Settings') }}</span></div>
                        </a>
                        @endcanany
                       
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->
    </div>
    @include('admin.sections.footer')
    <!-- ./wrapper -->
    <!-- jQuery 3 -->
    <script src="{{ asset('backend/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('backend/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('backend/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('backend/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('backend/dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('backend/dist/js/demo.js') }}"></script>
    <!-- page script -->
    <script src="{{ asset('backend/dist/js/pages/dashboard.js') }}"></script>
    <!-- dashboard 2 -->
    <script src="{{ asset('backend/bower_components/chart.js/Chart.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('backend/dist/js/padiges/dashboard2.js') }}"></script>
    <!-- CK Editor -->
    <script src="{{ asset('backend/bower_components/ckeditor/ckeditor.js') }}"></script>
    @yield('scripts')
</body>

</html>
