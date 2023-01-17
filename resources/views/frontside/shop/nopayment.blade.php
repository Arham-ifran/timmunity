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
									<h4><b>{{ __('Payment Link Expired') }}</b></h4>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body text-center">
                        <h2 class="order-heading mb-2">{{ __("Payment Link Expired") }}</h2>
                        <p class="order-text">
                            {{ __('Payment Link has either expired or something went wrong. Contact admin.') }}
                        </p>
					</div>
				</div>
			</div>
            <div class="col-md-2"></div>
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
