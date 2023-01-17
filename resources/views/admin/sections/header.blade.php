<header class="main-header">
   <nav class="navbar navbar-static-top">
      <div class="container-fluid">
         @include('admin.sections.navbar')
         <!-- /.navbar-collapse -->
         <!-- Navbar Right Menu -->
         <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
               <!-- Notifications Menu -->

               <li class="dropdown language-bar">
                   <div class="dropdown">
                       <button type="button" class="btn btn-primary dropdown-toggle"data-toggle="dropdown">
                       <img src="{{asset('backend/dist/flag/'.App::getLocale().'.svg')}}"
                       height="20px" width="20px"style="border-radius:50%;">&nbsp;{{ App::getLocale() }} <span class="caret">
                       </button>
                       <div class="dropdown-menu">
                       @foreach(\App\Models\Languages::where('is_active',1)->get() as $language)
                       {{-- <a href="{{ url('admin/lang/'.Hashids::encode($language->id)) }}" class="dropdown-item  @if($language->iso_code == App::getLocale()) active @endif" href="{{ url('lang/'.$language->iso_code) }}"><span class="lang-name">{{ucwords($language->name)}}</span> --}}
                       <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($language->iso_code, null, [], true) }}" class="dropdown-item  @if($language->iso_code == App::getLocale()) active @endif" href="{{ url('lang/'.$language->iso_code) }}"><span class="lang-name">{{ucwords($language->name)}}</span>
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
                     <img  src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(Auth::user()->id) . '/' . Auth::user()->image), 'avatar5.png') !!}" class="user-image" alt="User Image">
                     <!-- hidden-xs hides the username on small devices so only the image appears. -->
                     <span class="hidden-xs">{{ Auth::user()->firstname .' '. Auth::user()->lastname ?? '' }} <span class="caret"></span></span>
                  </a>
                  <ul class="dropdown-menu">
                     <!-- The user image in the menu -->
                     <li class="user-header">
                        <img src="{!! checkImage(asset('storage/uploads/admin/' . Hashids::encode(Auth::user()->id) . '/' . Auth::user()->image), 'avatar5.png') !!}" class="img-circle" alt="User Image">
                        <p>
                           {{ Auth::user()->firstname .' '. Auth::user()->lastname ?? '' }}
                           <small>{{ __('Member since') }} <strong>{{ date('M Y', strtotime(Auth::user()->created_at)) }}</strong></small>
                        </p>
                     </li>
                     <!-- Menu Body -->
                    {{--  <li class="user-body">
                        <div class="row">
                           <div class="col-xs-4 text-center">
                              <a href="#">Followers</a>
                           </div>
                           <div class="col-xs-4 text-center">
                              <a href="#">Sales</a>
                           </div>
                           <div class="col-xs-4 text-center">
                              <a href="#">Friends</a>
                           </div>
                        </div>
                        <!-- /.row -->
                     </li> --}}
                     <!-- Menu Footer-->
                     <li class="user-footer">
                        <div class="pull-left">
                           <a href="{{ route('admin.admin-user.edit',['admin_user'=> Hashids::encode(Auth::user()->id)]) }}" class="btn btn-default btn-flat">{{ __('Profile') }}</a>
                        </div>
                        <div class="pull-right">
                           <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat">{{ __('Sign out') }}</a>
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
   @yield('header')
</header>
