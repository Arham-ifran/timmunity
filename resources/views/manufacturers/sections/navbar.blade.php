@php
$segment = Request::segment(2);
@endphp


<div class="navbar-header">
   <a href="{{ route('manufacturers.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Manufacturer') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
</div>
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
   <ul class="nav navbar-nav">
     
      <li class=" @if( stripos(Request::url(),'manufacturer/dashboard')) active @endif">
         <a href="{{ route('manufacturers.dashboard') }}">{{ __('Manufacturer Dashboard') }} <span class="sr-only">{{ __('(current)') }}</span></a>
      </li>
      
      <li class="dropdown @if(stripos(Request::url(),'analysis') || stripos(Request::url(),'voucher-orders'))
      active
      @endif">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Reporting') }}<span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            <li class="@if( stripos(Request::url(),'manufacturer/analysis')) active @endif"><a href="{{route('manufacturers.product.analysis')}}">{{ __('Sales') }}</a></li>
            <li class="@if( stripos(Request::url(),'manufacturer/voucher-orders')) active @endif"><a href="{{route('manufacturers.voucher.orders.manufacturer.product')}}">{{ __('Voucher Orders') }}</a></li>

         </ul>
         
      </li>

      <!-- <li class="dropdown
                @if( stripos(Request::url(),'products') || stripos(Request::url(),'product-variant') || stripos(Request::url(),'price-lists') )
                    active
                @endif
            ">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ __('Products') }} <span class="caret"></span></a>
         <ul class="dropdown-menu" role="menu">
            @can('Products Listing')
            <li class="@if( stripos(Request::url(),'products')  ) active @endif"><a href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
            @endcan
           
            <li class="@if( stripos(Request::url(),'manufacturers')  ) active @endif"><a href="{{route('admin.manufacturer.index')}}">{{ __('Manufacturer') }}</a></li>
          
            @can('Product Variant Listing')
            @if(@$sales_settings['product_catalog_variants'] == 1)
            <li class="@if( stripos(Request::url(),'product-variant') ) active @endif"><a href="{{ route('admin.product-variant.index') }}">{{ __('Products Variants') }}</a></li>
            @endif
            @endcan
            @can('Price Lists Listing')
            @if(@$sales_settings['pricing_pricelist'] == 1)
            <li class="@if( stripos(Request::url(),'price-lists') ) active @endif"><a href="{{ route('admin.price-lists.index') }}">{{ __('Price lists') }}</a></li>
            @endif
            @endcan
         </ul>
      </li>
      
      <li class=" @if( stripos(Request::url(),'channel-pilot-sales')) active @endif">
        <a href="{{ route('admin.channel-pilot-sales-analytics') }}">{{ __('Channel Pilot') }} <span class="sr-only">{{ __('(current)') }}</span></a>
      </li> -->
      
   </ul>
</div>

