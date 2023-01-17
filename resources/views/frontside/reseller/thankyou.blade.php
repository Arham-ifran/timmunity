@extends('frontside.layouts.app')
@section('title') {{ __('Thank You') }}  @endsection
@section('body_class') cart-page @endsection
@section('style')

@endsection
@section('content')
	<div class="container">
		<div class="row">
			<div class="col-xs-12" style="margin-top: 50px;">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title">
							<div class="row">
								<div class="col-xs-6">
									<h4><b>{{ __('Voucher Payment Recieved')}}</b></h4>
								</div>
								<div class="col-xs-6">
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body text-center">
                        <p>
                            {{ __('The payment against the redeemed voucher has been completed')}}.{{__('Visit')}} <a href="{{ route('frontside.reseller.dashboard') }}">{{__('Dashboard')}}</a> {{__('to see the details')}}.
                        </p>
						<p class="order-text">
							{{ __('Your Transaction reference is') }} <br><strong>{{ $voucher_payment->transaction_id }}</strong><br>
						</p>
                        <p>{{__('In case of any query kindly contact the sales team')}}</p>
					</div>

				</div>
			</div>
		</div>
	</div>

</section>

@endsection

@section('script')
<script type="text/javascript">
    // window.onbeforeunload = function() {
    //     return "Dude, are you sure you want to leave? Think of the kittens!";
    // }
</script>
@endsection
