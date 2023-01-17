@php
$segment = Request::segment(2);
@endphp


<div class="navbar-header">
   <a href="{{ route('distributor.dashboard') }}" class="navbar-brand"><i class="fa fa-th"></i></a>
   <a href="javascript:void(0)" class="navbar-brand"><b>{{ __('Distributor') }}</b></a>
   <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
</div>
<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
   <ul class="nav navbar-nav">

      <li class=" @if( stripos(Request::url(),'distributor/dashboard')) active @endif">
         <a href="{{ route('distributor.dashboard') }}">{{ __('Distributor Dashboard') }} <span class="sr-only">{{ __('(current)') }}</span></a>
      </li>
      <li class=" @if( stripos(Request::url(),'distributor/voucher')) active @endif">
         <a href="{{ route("distributor.voucher.orders") }}">{{ __('Voucher Orders') }} <span class="sr-only">{{ __('(current)') }}</span></a>
      </li>

   </ul>
</div>

