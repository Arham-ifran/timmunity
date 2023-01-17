<style>
   .navbar-nav>.user-menu>.dropdown-menu>li.user-header{

      height:120px !important;
   }
</style>

<header class="main-header">
   <nav class="navbar navbar-static-top">
      <div class="container-fluid">
         @include('manufacturers.sections.navbar')
         <!-- /.navbar-collapse -->
         <!-- Navbar Right Menu -->
         <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
               <!-- Notifications Menu -->

               <li class="dropdown language-bar">
                   <!-- <div class="dropdown">
                       <button type="button" class="btn btn-primary dropdown-toggle"data-toggle="dropdown">
                       <img src="{{asset('backend/dist/flag/'.App::getLocale().'.svg')}}"
                       height="20px" width="20px"style="border-radius:50%;"> en <span class="caret">
                       </button>
                       <div class="dropdown-menu">
                     
                       <a href="" class="dropdown-item"   href=""><span class="lang-name"></span>
                       <img src=""height="30px">
                       </a>
                      
                       </div>
                   </div> -->
               </li>
               <!-- User Account Menu -->
               <li class="dropdown user user-menu">
                  <!-- Menu Toggle Button -->
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                     <!-- The user image in the navbar-->
                     <!-- <img  src="" class="user-image" alt="User Image"> -->
                     <!-- hidden-xs hides the username on small devices so only the image appears. -->
                     <span class="hidden-xs">{{ Auth::guard('manufacture')->user()->manufacturer_name}}<span class="caret"></span></span>
                  </a>
                  <ul class="dropdown-menu">
                     <!-- The user image in the menu -->
                     <li class="user-header">
                        <!-- <img src="{{asset('frontside/dist/img/avatar5.png') }}" class="img-circle" alt="User Image"> -->
                        <p>
                  
                           <strong>{{Auth::guard('manufacture')->user()->manufacturer_name}}</strong>
                        </p>
                        <p>
                           <?php 
                               $created_at = Auth::guard('manufacture')->user()->created_at;
                           ?>
                           <small>{{ __('Member since') }} <strong>{{date('y-m-d', strtotime($created_at))}}</strong></small>
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
                           <a href="{{route('manufacturers.profile')}}" class="btn btn-default btn-flat">{{ __('Profile') }}</a>
                        </div>
                        <div class="pull-right">
                           <a href="{{route('manufacturers.logout')}}" class="btn btn-default btn-flat">{{ __('Sign out') }}</a>
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