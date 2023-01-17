@extends('frontside.layouts.app')
@section('title') Cart @endsection
@section('body_class') cart-page @endsection
@section('style')
<style>
    .quantity-div{
        display:inline-flex;
    }
    .quantity-div button {
        background: #009a71;
        border: 1px solid #009a71;
        color: white;
    }

    .quantity-div button:hover {
        color: #009a71;
        background: white;
    }
    .quantity-div input {
        text-align:center;
        background-color: white !important;
        border-radius: 0;
    }
    .remove-cart-item {
        color: red;
        font-size: 15px;
        border: 1px solid;
        padding: 5px;
        font-family: 'Glyphicons Halflings' !important;
    }
    .glyphicon {
        font-family: 'Glyphicons Halflings' !important;
    }
    .remove-cart-item:hover {
        color: white;
        background: red;
    }
    button.decrement {
        border-radius: 20px 0px 0px 20px;
    }
    button.increment {
        border-radius: 0px 20px 20px 0px;
    }
    .cart-h4{
        font-family: 'Avenir Next' !important;
        font-weight: bold !important;
    }
    small[title="Variation"] {
        font-size: 1.7rem;
        font-weight: inherit;
        /* color: black; */
    }
    .copoun-wrapper h6 {
        font-size: 20px;
    }
</style>
@endsection
@section('content')
    <form name="ignore_me">
        <input type="hidden" id="page_is_dirty" name="page_is_dirty" value="0" />
    </form>

	<div class="container">
		<div class="row">
			<div class="col-xs-12" style="margin-top: 50px;">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title">
							<div class="row flex-row">
								<div class="col-xs-6">
									<h4><span class="glyphicon glyphicon-shopping-cart"></span> <b>{{ __('Cart') }}</b></h4>
								</div>
								<div class="col-xs-6">
									<a href="{{ route('frontside.shop.index') }}">
                                        <button type="button" class="btn btn-primary btn-sm btn-block">
										    <span class="glyphicon glyphicon-share-alt"></span> {{ __('Continue') }}
									    </button>
                                    </a>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body">
                        @php
                            // dd($cart->cart_items);
                        @endphp
                        @if( isset($cart->cart_items)  && count($cart->cart_items))
                            @foreach( $cart->cart_items as $key => $cart_item )
                                <div class="row cart_row cart_row_{{ Hashids::encode($cart_item->id) }}">
                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                        <a target="_blank" title="View product" href="{{ route('frontside.shop.product-details', $cart_item->product->slug) }}">
                                            <img class="img-responsive" width="100"
                                            src="{!! checkImage(asset('storage/uploads/sales-management/products/' . $cart_item->product->image), 'placeholder-products.jpg') !!}">
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-10 col-sm-10">
                                        <h4 class="product-name">
                                            <strong title="Product name">{{ $cart_item->product->product_name }}</strong>
                                            @if($cart_item->variation_id != null)
                                            {{-- <br> --}}
                                                <small title="Variation">{{ $cart_item->variation->variation_name }}</small>
                                            @endif
                                        </h4>
                                        <small title="Product description" class="product_description">
                                            {!! translation( $cart_item->product->sales->id,11,app()->getLocale(),'description',$cart_item->product->sales->description) !!}
                                        </small>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="quantity-price-wrapper">
                                            <div class="text-right">
                                                <h6 title="Unit price">
                                                    <strong>
                                                        @php
                                                            $currency_symbol =  Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                                            $currency_code =  Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                                            $price = $cart_item->unit_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1);
                                                        @endphp
                                                        {{ currency_format($price, $currency_symbol, $currency_code ) }}
                                                        <span class="text-muted">x</span>
                                                    </strong>
                                                </h6>

                                            </div>
                                            <div class="quantity-div cart-quilty-incremnt">
                                                <button type="button" class="decrement" id="" data-input="qty-input{{ $key }}" title="Decrease quantity"><i class="fa fa-minus"
                                                        aria-hidden="true"></i></button>
                                                <input  title="Quantity" onkeypress="return isNumber(event)" data-id="{{ Hashids::encode($cart_item->id) }}" value="{{ $cart_item->qty }}" type="text"  maxlength="2" max="10" size="1"
                                                    class="qty-input qty-input{{ $key }} form-control" />
                                                <button type="button" class="increment" id="" data-input="qty-input{{ $key }}"  title="Increase quantity"><i class="fa fa-plus"
                                                        aria-hidden="true"></i></button>
                                            </div>
                                            <div class="dlt-btn-wrap">
                                                <button type="button" class="btn btn-link btn-xs">
                                                    <span class="glyphicon glyphicon-trash remove-cart-item" title="Remove item" style="cursor:pointer;" title="Remove item" data-id="{{ Hashids::encode($cart_item->id) }}" > </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        @else
                        <div class="row ">
                            <div class="col-xs-12 text-center">
                                {{ __('There are no items in your cart. Kindly visit the') }} <a href="{{ route('frontside.shop.index') }}"> <strong>{{ __('shop') }}</strong></a> {{ __('to add items in cart') }}.
                            </div>
                        </div>
                        <hr>
                        @endif

                        @if(isset($cart) && count($cart->cart_items))
						<div class="row" id="couponArea">
							<div class="copoun-wrapper">
								<div class="col-lg-9 col-md-5 col-xs-12">
									<h6>{{ __('Have coupon code?') }}</h6>
								</div>
								<div class="col-lg-3 col-md-7 col-xs-12">
                                    @if(auth()->user())
                                        <input type="text"class="form-control" value="{{ auth()->user()->cart ? auth()->user()->cart->coupon_code : ''}}" id="coupon-code-text" placeholder="{{ __('Enter coupon code') }}">
									@else
                                        <input type="text"class="form-control" value="{{ Session::get('coupon_code') ? Session::get('coupon_code') : ''}}" id="coupon-code-text" placeholder="{{ __('Enter coupon code') }}">
                                    @endif
                                    <button type="button" class="btn btn-default btn-sm btn-block @if(isset($cart))  coupon-btn @endif">
										{{ __('Apply Coupon') }}
									</button>
								</div>
							</div>
						</div>
                        @endif
					</div>
					<div class="panel-footer">
						<div class="row text-center">
							<div class="col-xs-12">

                                @php
                                    $cart_subtotal = $cart ? $cart->total['subtotal'] : 0;
                                    $cart_taxtotal = $cart ? $cart->total['taxtotal'] + ($cart->total['subtotal'] * $vat_percentage / 100) : 0;
                                    $cart_grandtotal = $cart ? $cart->total['grandtotal'] + ($cart->total['subtotal'] * $vat_percentage / 100) : 0;
                                @endphp

                                @if($discount_percentage > 0)
								<h4 class="text-right cart-h4">{{ __('Applied Discount ') }}
                                    <strong id="subtotal_price">
                                    {{ $discount_percentage }} %
                                    </strong>
                                </h4>
                                @endif
								<h4 class="text-right cart-h4">{{ __('Sub Total') }}
                                    <strong id="subtotal_price">
                                        @php
                                            $currency_symbol =  Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                            $currency_code =  Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                            $price = $cart ? currency_format($cart->total['subtotal'],'','',1 ) : 0;
                                        @endphp
                                        {{ currency_format($price, $currency_symbol, $currency_code ) }}
                                    </strong>
                                </h4>
								<h4 class="text-right cart-h4">{{ __($vat_label) }}
                                    <strong id="vat_total_price">
                                        @php
                                            $currency_symbol =  Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                            $currency_code =  Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                            $price = $cart ? currency_format(($cart->total['subtotal'] * $vat_percentage / 100) , '','',1 ) : 0;
                                        @endphp
                                        {{ currency_format($price, $currency_symbol, $currency_code ) }}
                                    </strong>
                                </h4>
                                @if(isset($cart->total['taxtotal']) )
                                {{-- @php
                                    dd(currency_format(($cart->total['taxtotal'] - ($cart->total['subtotal'] * $vat_percentage / 100)), '','',1 ));
                                @endphp --}}
                                @if(currency_format(($cart->total['taxtotal'] - ($cart->total['subtotal'] * $vat_percentage / 100)), '','',1 ) > 0)
								<h4 class="text-right cart-h4">{{ __('Tax') }}
                                    <strong id="tax_total_price">
                                        @php
                                            $currency_symbol =  Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                            $currency_code =  Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                            $price =  $cart ? currency_format(($cart->total['taxtotal'] - ($cart->total['subtotal'] * $vat_percentage / 100)), '','',1 ) : 0;
                                        @endphp
                                        {{ currency_format($price, $currency_symbol, $currency_code ) }}
                                    </strong>
                                </h4>
                                @endif
                                @endif
								<h4 class="text-right cart-h4">{{ __('Total') }}
                                    <strong id="total_price">
                                        @php
                                            $currency_symbol =  Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                            $currency_code =  Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                            $price =  $cart ? currency_format($cart->total['grandtotal'], '','',1 ) : 0;
                                        @endphp
                                        {{ currency_format($price, $currency_symbol, $currency_code ) }}
                                    </strong>
                                </h4>
							</div>
							<div class="col-xs-9">
                            </div>
                            @if(isset($cart) && count($cart->cart_items) > 0 )

							<div id="checkoutButton">
								<a href="@if(isset($cart)) {{route('frontside.shop.checkout')}} @else #. @endif">
								<button type="button" class="btn btn-success btn-block">
									{{ __('Checkout') }}
								</button>
								</a>
							</div>
                            @endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <form id="action_form">
        @csrf
    </form>
</section>

@endsection

@section('script')
<script>
    var dirty_bit = document.getElementById('page_is_dirty');

    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        // if ($('#page_is_dirty').val() == '1') {
            $('#page_is_dirty').val(0);
            window.location.reload()
        // };
    }

    exchange_rate = "{{Session::get('exchange_rate') ? Session::get('exchange_rate') : 1}}";
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) || charCode == 8) {
            return false;
        }
        return true;
    }
    $('body').on('click','.increment', function(){
        selector = '.'+$(this).data('input');
        number = parseInt($(selector).val());
        $(selector).val(number + 1);
        $(selector).trigger('input');
        $('#page_is_dirty').val(1);
    });
    $('body').on('click','.decrement', function(){
        selector = '.'+$(this).data('input');
        number = parseInt($(selector).val());
        $(selector).val(number - 1);
        $(selector).trigger('input');
        $('#page_is_dirty').val(1);
    });
    /* Cart Item Trash icon click event
    * Remove the cart item
    */
    $('body').on('click','.remove-cart-item', function(){
        url = "{{ route('frontside.shop.cart.remove',':id') }}";
        url = url.replace(":id", $(this).data('id'));
        $('#action_form').attr('action', url);
        $('#action_form').attr('method', 'POST');
        $('#action_form').submit();
        $('#page_is_dirty').val(1);
    });

    /*
    * Update Cart Item Quantity
    */
    $('body').on('input', '.qty-input', function(){
        $('#page_is_dirty').val(1);
        url = "{{ route('frontside.shop.cart.update.qty',[':id',':qty']) }}";
        url = url.replace(":id", $(this).data('id'));
        if($(this).val() == ''){
            $(this).val(0);
        }
        url = url.replace(":qty", $(this).val());
        input = $(this);
        $.ajax({
            url:url,
            type: "post",
            data:{
                "_token": $('input[name=_token]').val()
            },
            success: (data) => {
                if(data['success'] == 'true'){
                    if(data['item_removed'] == 'true'){
                        $('.cart_row_'+data['item_id']).remove();
                        if($('.cart_row').length == 0){
                            location.reload();

                            // $('#checkoutButton').remove();
                            // $('#couponArea').remove();
                            // $('#total_price').html(data['currency']+' 0 '+data['currency_code']);
                            // $('#tax_total_price').html(data['currency']+' 0 '+data['currency_code']);
                            // $('#vat_total_price').html(data['currency']+' 0 '+data['currency_code']);
                            // $('#subtotal_price').html(data['currency']+' 0 '+data['currency_code']);
                            // $('#cart_zero').html('0');
                            $('.panel-body').html("<div class='row'><div class='col-xs-12 text-center'>{{ __('There are no items in your cart. Kindly visit the') }} <a href='{{ route('frontside.shop.index') }}'> <strong>{{ __('shop') }}</strong></a> {{ __('to add items in cart') }}.</div></div><hr>");
                        }else{
                            $('#cart_zero').html($('.cart_row').length);
                        }
                    }
                    if($('.cart_row').length > 0){
                        @isset($cart)
                            url = "{{ route('frontside.shop.cart.get.total',Hashids::encode($cart->id)) }}";
                            $.ajax({
                                url:url,
                                type: "post",
                                data:{
                                    "_token": $('input[name=_token]').val()
                                },
                                success: (data) => {
                                    if(data['success'] == 'true'){
                                        $('#total_price').html(data['currency']+' '+data['cart_grandtotal']+' '+data['currency_code']);
                                        $('#tax_total_price').html(data['currency']+' '+data['cart_taxtotal']+' '+data['currency_code']);
                                        $('#vat_total_price').html(data['currency']+' '+data['cart_vattotal']+' '+data['currency_code']);
                                        $('#subtotal_price').html(data['currency']+' '+data['cart_subtotal']+' '+data['currency_code']);
                                    }
                                    coupon_code = $('#coupon-code-text').val();
                                    if( coupon_code != null  && coupon_code != '')
                                    {
                                        $('.coupon-btn').trigger('click');
                                    }
                                },
                            });
                        @endisset
                    }
                }
            },
        });
    });

    // Apply Coupon Button Click
    $('body').on('click','.coupon-btn', function(){
        coupon_code = $('#coupon-code-text').val();
        if( coupon_code != null  && coupon_code != '')
        {
            url = "{{ route('frontside.shop.cart.apply.coupon',[':coupon_code']) }}";
            url = url.replace(":coupon_code", $('#coupon-code-text').val());
            $('#action_form').attr('action', url);
            $('#action_form').attr('method', 'POST');
            $('#action_form').submit();
        }
    });
</script>
@endsection
