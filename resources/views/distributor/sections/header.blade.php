<style>
.navbar-nav>.user-menu>.dropdown-menu>li.user-header{

    height:120px !important;
}
</style>

<header class="main-header">
<nav class="navbar navbar-static-top">
    <div class="container-fluid">
        @include('distributor.sections.navbar')
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            <li class="dropdown language-bar">
            </li>
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="hidden-xs">{{ Auth::guard('distributor')->user()->name}}<span class="caret"></span></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="user-header">
                        <p>

                        <strong>{{Auth::guard('distributor')->user()->name}}</strong>
                        </p>
                        <p>
                        <?php
                            $created_at = Auth::guard('distributor')->user()->created_at;
                        ?>
                        <small>{{ __('Member since') }} <strong>{{date('y-m-d', strtotime($created_at))}}</strong></small>
                        </p>
                    </li>
                    <li class="user-footer">
                        <div class="pull-left">
                        <a href="{{route('distributor.profile')}}" class="btn btn-default btn-flat">{{ __('Profile') }}</a>
                        </div>
                        <div class="pull-right">
                        <a href="{{route('distributor.logout')}}" class="btn btn-default btn-flat">{{ __('Sign out') }}</a>
                        </div>
                    </li>
                </ul>
            </li>
            </ul>
        </div>
    </div>
</nav>
@yield('header')
</header>
