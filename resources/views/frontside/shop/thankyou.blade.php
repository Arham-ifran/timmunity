@extends('frontside.layouts.app')
@section('title') Cart @endsection
@section('body_class') cart-page @endsection
@section('style')
<style>
    .order-heading {
        color: #009a71;
        text-transform: uppercase;
    }
    .order-text {
        font-size: 1.1em;
    }
</style>
@endsection
@section('content')
	<div class="container">
		<div class="row">
            <div class="col-md-2"></div>
			<div class="col-md-8" style="margin-top: 50px;">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title">
							<div class="row">
								<div class="col-md-12">
									<h4><b>{{ __('Order Placed') }}</b></h4>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body text-center">
                        <h2 class="order-heading mb-2">{{ __("Your order has been placed successfully.") }}</h2>
                        <p class="order-text">
                            {{ __('Your Order # is') }} <br><strong>S{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</strong>
                        </p>
                        <p class="order-text">
                        {{ __('Your Transaction reference is') }} <br><strong>{{ $quotation->transaction_id }}</strong><br>
                        </p>
                        <p class="order-text mb-2">{{ __('In case of any query kindly contact the sales team.') }}</p>
                        @if(Auth::user())
                            <a href="{{ route('user.dashboard') }}" class="mt-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block mt-2 mb-1">
                                    <span class="glyphicon glyphicon-share-alt"></span> {{ __('Dashboard') }}
                                </button>
                            </a>
                        @else
                            <a href="{{ route('frontside.home.index') }}" class="mt-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block mt-2 mb-1">
                                    <span class="glyphicon glyphicon-share-alt"></span> {{ __('Back to Home') }}
                                </button>
                            </a>
                        @endif
					</div>

				</div>
			</div>
            <div class="col-md-2"></div>

		</div>
	</div>

</section>
@php
$totalAmount = currency_format(@$quotation->total*@$quotation->exchange_rate,'','',1);
$currencyCode = $quotation->currency;
$orderReference = 'S'.str_pad($quotation->id, 5, '0', STR_PAD_LEFT);
@endphp
@endsection

@section('script')
<!--Master Tag add just before the closing </body> tag--> 
@if(Cookie::get('awc') != null)
<img border="0" height="0" src="https://www.awin1.com/sread.img?tt=ns&tv=2&merchant=27866&amount={{$totalAmount}}&ch=aw&cr={{$currencyCode}}&parts=DEFAULT:{{$totalAmount}}&ref={{$orderReference}}&testmode=1&vc={{$orderReference}}" style="display: none;" width="0">
<script type="text/javascript">
    var AWIN = {};
    AWIN.Tracking = {};
    AWIN.Tracking.Sale = {};
    AWIN.Tracking.Sale.amount = "{{$totalAmount}}";
    AWIN.Tracking.Sale.channel = "aw";
    AWIN.Tracking.Sale.currency = "{{$currencyCode}}";
    AWIN.Tracking.Sale.orderRef = "{{$orderReference}}";
    AWIN.Tracking.Sale.parts = "DEFAULT:{{$totalAmount}}";
    AWIN.Tracking.Sale.test = "0";
    AWIN.Tracking.Sale.voucher = "{{$orderReference}}";
</script>
@endif
@endsection
