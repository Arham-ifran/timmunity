<!-- Left side column. contains the sidebar -->
<aside class="settingpage-sidbar main-sidebar position-fixed">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- sidebar menu: : style can be found in sidebar.less -->
		@canany(['View General Settings','View Sales Settings'])
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header">{{ __('MAIN NAVIGATION') }}</li>
			@can('View General Settings')
			<li @if(request()->segment(count(request()->segments())) == 'settings') class="active" @endif>
				<a href="{{ route('admin.settings') }}">
					<i class="fa fa-cog"></i>
                         <span>{{ __('General Settings') }}</span>
                </a>
			</li>
			@endcan
			@can('View Sales Settings')
			<li @if(request()->segment(count(request()->segments())) == 'sales' && request()->segment(count(request()->segments())-1) == 'settings') class="active" @endif>
                <a href="{{ route('admin.sales.settings') }}">
					<i class="fa fa-balance-scale"></i>
					<span>{{ __('Sales') }}</span>
				</a>
			</li>
			@endcan

		</ul>
		@endcanany
	</section>
	<!-- /.sidebar -->
</aside>
