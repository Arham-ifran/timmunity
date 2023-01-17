<aside class="settingpage-sidbar main-sidebar position-fixed">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- sidebar menu: : style can be found in sidebar.less -->
		
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header">{{ __('MAIN NAVIGATION') }}</li>
			@can('View General Settings')
			<li @if(request()->segment(count(request()->segments())) == 'settings') class="active" @endif>
				<a href="">
					<i class="fa fa-cog"></i>
                         <span>{{ __('General Settings') }}</span>
                </a>
			</li>
			@endcan
			@can('View Sales Settings')
			<li @if(request()->segment(count(request()->segments())) == 'sales' && request()->segment(count(request()->segments())-1) == 'settings') class="active" @endif>
                <a href="">
					<i class="fa fa-balance-scale"></i>
					<span>{{ __('Sales') }}</span>
				</a>
			</li>
			@endcan
			{{-- <li>
				<a href="websites-settings.html">
					<i class="fa fa-desktop"></i>
					<span>Websites</span>
				</a>
			</li>
			<li>
				<a href="purchase-settings.html">
					<i class="fa fa-credit-card"></i>
					<span>Purchase</span>
				</a>
			</li>
			<li>
				<a href="documents-settings.html">
					<i class="fa fa-file"></i>
					<span>Documents</span>
				</a>
			</li> --}}
		</ul>
		
	</section>
	<!-- /.sidebar -->
</aside>