@extends('frontside.layouts.app')
@section('title') Cart @endsection
@section('body_class') cart-page @endsection
@section('style')
<style>
    .checkout-form select{
        width: 100%;
        margin-bottom: 20px;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }
    .add-new-address-btn, .use-existing-address-btn{
        width:auto !important;
    }
    .use-existing-address-btn{
        display:none;
    }
    select.error, input.error{
        border: 1px solid red;
    }
    label.error{
        color: red;
    }
    .checkout h4{
        font-family: 'Avenir Next' !important;
        font-weight: bold !important;
    }
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }
    .checkout-form input[type=number]{
        width: 100%;
        margin-bottom: 20px;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }
</style>
@endsection
@section('content')
    <!-- check out page -->
    <div class="checkout-form mt-4">
        <div class="row">
            <div class="container">
                <div class="col-md-9">
                    <div class="checkout-container">
                        <form action="{{ route('frontside.shop.checkout.post') }}" method="POST" id="checkoutForm">
                            <div class="row">
                                <div class="col-50">
                                    @if(Auth::user())
                                        <div id="addressSelections">
                                            <div class="" id="billing_address_area" >
                                                <h3> {{ __('Billing Address') }}</h3>
                                                <div class="select-existing">
                                                    <label for="address"> {{ __('Select Billing Address') }} </label>
                                                    <select class="" name="address_id" required='required'>
                                                        <option value="">{{ __('Select Address') }}</option>
                                                        @if (Auth::user()->contact->contact_addresses->count() > 0)
                                                            @foreach(Auth::user()->contact->contact_addresses as $cust_add)
                                                                    <option value="{{ $cust_add->id }}" >{{ $cust_add->contact_name.'  , '.$cust_add->street_1.'  , '.$cust_add->city.'  , '.$cust_add->contact_countries->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="new-addr" style="display:none">
                                                    <label for="fname"><i class="fa fa-user"></i> {{ __('Full Name') }}<small style="color:red">*</small></label>
                                                    <input required='required' type="text" id="fname" name="billing[firstname]" value="{{ Auth::user()->name  }}"placeholder="John M. Doe" >
                                                    <label for="email"><i class="fa fa-envelope"></i> {{ __('Email') }}<small style="color:red">*</small></label>
                                                    <input required='required' type="text" id="email" name="billing[email]" value="{{ Auth::user()->email  }}" placeholder="john@example.com">
                                                    <label for="phone"><i class="fa fa-phone"></i> {{ __('Phone No') }}</label>
                                                    <input type="number" id="phone" name="billing[phone_no]" value="{{ Auth::user()->contact->phone  }}" placeholder="4965874851">
                                                    <label for="adr"><i class="fa fa-address-card-o"></i> {{ __('Address') }}<small style="color:red">*</small></label>
                                                    <input required='required' type="text" id="adr" name="billing[address]" placeholder="542 W. 15th Street">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label for=""><i class="fa fa-flag"></i> {{ __('Country') }}<small style="color:red">*</small></label>
                                                            <select required='required' class="" name="billing[country_id]">
                                                                <option value="">{{ __('Select Country') }}</option>
                                                                @if ($countries->count() > 0)
                                                                    @foreach ($countries as $country)
                                                                        <option value="{{ $country->id }}" >
                                                                            {{ $country->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    <input type="hidden" class="form-control" name="billing[state]" />

                                                        {{-- <div class="col-sm-6">
                                                            <label for="state"><i class="fa fa-institution"></i> {{ __('State') }}</label>
                                                            <select name="billing[state]">
                                                                <option value="">{{ __('Select State') }}</option>
                                                                @if ($contact_fed_states->count() > 0)
                                                                    @foreach ($contact_fed_states as $state)
                                                                        <option value="{{ $state->id }}" >
                                                                            {{ $state->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div> --}}
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label for="city"><i class="fa fa-institution"></i> {{ __('City') }}<small style="color:red">*</small></label>
                                                            <input required='required' type="text" id="city" name="billing[city]" placeholder="New York" >
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="zip"><i class="fa fa-map-pin"></i> {{ __('Zip Code') }}</label>
                                                            <input  type="text" id="zip" name="billing[zip]" placeholder="10001" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="" id="shipping_address_area" style="display:none">
                                                <h3> {{ __('Shipping Address') }}</h3>
                                                <div class="select-existing">
                                                    <label for="address"> {{ __('Select Shipping Address') }} </label>
                                                    <select required='required' class="" name="shipping_address_id">
                                                        <option value="">{{ __('Select Address') }}</option>
                                                        @if (Auth::user()->contact->contact_addresses->count() > 0)
                                                            @foreach(Auth::user()->contact->contact_addresses as $cust_add)
                                                                @if($cust_add->type == 2)
                                                                    <option value="{{ $cust_add->id }}" >{{ $cust_add->contact_name.'  , '.$cust_add->street_1.'  , '.$cust_add->city.'  , '.$cust_add->contact_countries->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="new-addr">
                                                    <label for="fname"><i class="fa fa-user"></i> {{ __('Full Name') }}<small style="color:red">*</small></label>
                                                    <input required='required' type="text" id="fname" name="shipping[firstname]" value="{{ Auth::user()->name  }}" placeholder="John M. Doe" >
                                                    <label for="email"><i class="fa fa-envelope"></i> {{ __('Email') }}<small style="color:red">*</small></label>
                                                    <input required='required' type="text" id="email" name="shipping[email]" value="{{ Auth::user()->email  }}" placeholder="john@example.com" >
                                                    <label for="phone"><i class="fa fa-phone"></i> {{ __('Phone No') }}</label>
                                                    <input type="number" id="phone" name="shipping[phone_no]" value="{{ Auth::user()->contact->phone  }}" placeholder="4965874851">
                                                    <label for="adr"><i class="fa fa-address-card-o"></i> {{ __('Address') }}<small style="color:red">*</small></label>
                                                    <input required='required' type="text" id="adr" name="shipping[address]" placeholder="542 W. 15th Street" >
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label for=""><i class="fa fa-flag"></i> {{ __('Country') }}<small style="color:red">*</small></label>
                                                            <select required='required' class="" name="shipping[country_id]">
                                                                <option value="">{{ __('Select Country') }}</option>
                                                                @if ($countries->count() > 0)
                                                                    @foreach ($countries as $country)
                                                                        <option value="{{ $country->id }}">
                                                                            {{ $country->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <input type="hidden" class="form-control" name="shipping[state]" />
                                                        {{-- <div class="col-sm-6">
                                                            <label for="state"><i class="fa fa-institution"></i> {{ __('State') }}</label>
                                                            <select name="shipping[state]">
                                                                <option value="">{{ __('Select State') }}</option>
                                                                @if ($contact_fed_states->count() > 0)
                                                                    @foreach ($contact_fed_states as $state)
                                                                        <option value="{{ $state->id }}" >
                                                                            {{ $state->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div> --}}
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label for="city"><i class="fa fa-institution"></i> {{ __('City') }}<small style="color:red">*</small></label>
                                                            <input required='required' type="text" id="city" name="shipping[city]" placeholder="New York" >
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="zip"><i class="fa fa-map-pin"></i> {{ __('Zip Code') }}</label>
                                                            <input type="text" id="zip" name="shipping[zip]" placeholder="10001">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-default add-new-address-btn"> {{ __('Add New Address') }}</button>
                                        <button class="btn btn-default use-existing-address-btn"> {{ __('Select Existing Address') }}</button>
                                    @else
                                        <div class="new-addr">
                                            <label for="fname"><i class="fa fa-user"></i> {{ __('Full Name') }}<small style="color:red">*</small></label>
                                            <input required='required' type="text" id="fname" name="billing[firstname]" value=""placeholder="John M. Doe" >
                                            <label for="email"><i class="fa fa-envelope"></i> {{ __('Email') }}<small style="color:red">*</small></label>
                                            <input required='required' type="text" id="email" name="billing[email]" value="" placeholder="john@example.com">
                                            <label for="phone"><i class="fa fa-phone"></i> {{ __('Phone No') }}</label>
                                            <input type="number" id="phone" name="billing[phone_no]" value="" placeholder="4965874851">
                                            <label for="adr"><i class="fa fa-address-card-o"></i> {{ __('Address') }}<small style="color:red">*</small></label>
                                            <input required='required' type="text" id="adr" name="billing[address]" placeholder="542 W. 15th Street">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label for=""><i class="fa fa-flag"></i> {{ __('Country') }}<small style="color:red">*</small></label>
                                                    <select required='required' class="" name="billing[country_id]">
                                                        <option value="">{{ __('Select Country') }}</option>
                                                        @if ($countries->count() > 0)
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}" >
                                                                    {{ $country->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <input type="hidden" class="form-control" name="billing[state]" />

                                                {{-- <div class="col-sm-6">
                                                    <label for="state"><i class="fa fa-institution"></i>  {{ __('State') }}</label>
                                                    <select  name="billing[state]">
                                                        <option value="">{{ __('Select State') }}</option>
                                                        @if ($contact_fed_states->count() > 0)
                                                            @foreach ($contact_fed_states as $state)
                                                                <option value="{{ $state->id }}" >
                                                                    {{ $state->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div> --}}
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label for="city"><i class="fa fa-institution"></i> {{ __('City') }}<small style="color:red">*</small></label>
                                                    <input required='required' type="text" id="city" name="billing[city]" placeholder="New York" >
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="zip"><i class="fa fa-map-pin"></i> {{ __('Zip Code') }}</label>
                                                    <input type="text" id="zip" name="billing[zip]" placeholder="10001" >
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if(Auth::user())
                                <label>
                                    <input type="checkbox" checked="checked" name="sameadr"> {{ __('Shipping address same as billing') }}
                                </label>
                            @else
                                <label>
                                    <input type="checkbox" name="new_account"> {{ __('Create an account') }}
                                </label>
                                <div class="row password_row" id="password_row" style="display:none">
                                    <div class="col-sm-6">
                                        <label for="password"><i class="fa fa-key"></i> {{ __('Password') }}<small style="color:red">*</small></label>
                                        <input required='required' class="form-control" type="password" id="password" name="password" placeholder="xxxxxxxxx" >
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="confirm_password"><i class="fa fa-key"></i> {{ __('Confirm Password') }}<small style="color:red">*</small></label>
                                        <input required='required' class="form-control" type="password" id="confirm_password" name="confirm_password" placeholder="xxxxxxxxx" >
                                    </div>
                                </div>

                            @endif
                            @csrf
                            <input type="submit" value="{{ __('Continue to checkout')}}" class="btn">
                            <input type="hidden" name="existing_address">
                        </form>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="checkout-container checkout">
                        <h4>
                            {{ __('Cart') }}
                            <span class="price" style="color:black">
                                <i class="fa fa-shopping-cart"></i>
                                @if(Auth::user())
                                    <b>{{ Auth::user()->cart ? count(Auth::user()->cart->cart_items) : '0' }}</b>
                                @else
                                    <b>{{ Session::get('cart_items') ? count(Session::get('cart_items')) : 0 }}</b>
                                @endif
                            </span>
                        </h4>
                        @if(Auth::user())
                            @if( Auth::user()->cart )
                                @foreach(Auth::user()->cart->cart_items as $cart_item)
                                    <p><a href="#.">
                                        {{ $cart_item->product->product_name }}<br>
                                        @if($cart_item->variation_id != null)
                                                    {{ $cart_item->variation->variation_name }}
                                        @endif
                                    </a>
                                    <span class="price">
                                        @php
                                            $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                            $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                            $price = $cart_item->unit_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1);
                                        @endphp
                                        {{ currency_format($price,$currency_symbol,$currency_code) }}
                                    </span></p>
                                @endforeach
                            @endif
                        @else
                            @if( Session::get('cart_items') )
                                @foreach(Session::get('cart_items') as $cart_item)
                                    <p><a href="#.">
                                        {{ @$cart_item->product->product_name }}<br>
                                        @if($cart_item->variation_id != null)
                                            {{ @$cart_item->variation->variation_name }}
                                        @endif
                                    </a>
                                    <span class="price">
                                        @php
                                            $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                            $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                            $price = $cart_item->unit_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1);
                                        @endphp
                                        {{ currency_format($price,$currency_symbol,$currency_code) }}

                                    </span></p>
                                @endforeach
                            @endif
                        @endif
                        <hr>
                        @if(Auth::user())
                            @php
                                $cart_subtotal = Auth::user()->cart->total['subtotal'];
                                $cart_taxtotal = Auth::user()->cart->total['taxtotal'];
                                $cart_grandtotal = Auth::user()->cart->total['grandtotal'];
                                

                            @endphp
                            @if($discount_percentage>0)
                                <p>{{ __('Applied Discount') }}
                                    <span class="price" style="color:black">
                                    <b>
                                        {{$discount_percentage}} %
                                    </b>
                                </span>
                                </p>
                            @endif
                            <p>{{ __('Sub Total') }}
                                <span class="price" style="color:black">
                                <b>
                                    @php
                                        $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                        $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                        $price = Auth::user()->cart ? $cart_subtotal : '0';
                                    @endphp
                                        {{ currency_format($price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            </p>
                            @php
                                $vat_label = __("VAT");
                                if(Auth::user()->contact->contact_countries->is_default_vat != 1)
                                {
                                    $vat_label = Auth::user()->contact->contact_countries->vat_label ? Auth::user()->contact->contact_countries->vat_label : __('VAT');
                                }
                            @endphp
                            <p>{{ $vat_label }}
                                <span class="price" style="color:black">
                                <b>
                                    @php
                                        $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                        $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                        $price = Auth::user()->cart ? ($cart_subtotal* $vat_percentage/100) : '0';
                                    @endphp
                                    {{ currency_format($price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            </p>
                            @php
                                $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                $tax_price = Auth::user()->cart ? ($cart_taxtotal - ($cart_subtotal* $vat_percentage/100)) : '0';
                            @endphp
                            @if(currency_format($tax_price,'','',1) > 0)
                            <p>{{ __('Taxes') }}
                                <span class="price" style="color:black">
                                <b>
                                    {{ currency_format($tax_price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            </p>
                            @endif
                            <p>{{ __('Total') }}
                                <span class="price" style="color:black">
                                <b>
                                    @php
                                        $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                        $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                        $price = Auth::user()->cart ? $cart_grandtotal : '0';
                                    @endphp
                                    {{ currency_format($price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            </p>
                        @else
                            @if($discount_percentage>0)
                                <p>{{ __('Applied Discount') }}
                                    <span class="price" style="color:black">
                                    <b>
                                        {{$discount_percentage}} %
                                    </b>
                                </span>
                                </p>
                            @endif
                            <p>{{ __('Sub Total') }}
                                <span class="price" style="color:black">
                                <b>
                                    @php
                                        $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                        $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                        $price = $subtotal - (($taxtotal - ($subtotal * $vat_percentage / 100 ))) ;
                                    @endphp
                                    {{ currency_format($price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            </p>
                            <p>{{ $vat_label }}
                                <span class="price" style="color:black">
                                <b>
                                    @php
                                        $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                        $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                        $price = ($subtotal * $vat_percentage / 100 );
                                        $vat_price = $price;
                                    @endphp
                                    {{ currency_format($price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            @php
                                $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                $tax_price = ($taxtotal - ($subtotal * $vat_percentage / 100 ));
                            @endphp
                            @if( currency_format($tax_price,'','') > 0)
                            <p>{{ __('Taxes') }}
                                <span class="price" style="color:black">
                                <b>
                                    {{ currency_format($tax_price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            @endif
                            </p>
                            <p>{{ __('Total') }}
                                <span class="price" style="color:black">
                                <b>
                                    @php
                                        $currency_symbol = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
                                        $currency_code = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
                                        $price = $subtotal + $vat_price;
                                    @endphp
                                    {{ currency_format($price,$currency_symbol,$currency_code) }}
                                </b>
                            </span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
<script>
    var sameAddr = 1;       // Toggled on the click event of checkbox for use same address for billing and shipping
    var existingAddr = 1;
    $('[name=existing_address]').val('1');

    // $('body').on('input','[name=confirm_password], [name=password]', function(){
    //     if($('[name=confirm_password]').val() != $('[name=password]').val()){
    //         $('[name=password]').after('<label id="password-error" class="error" for="password">Both passwords must match.</label>');
    //         setTimeout(function(){
    //             $("#password-error").remove();
    //         },4000);
    //     }else{
    //         $("#password-error").remove();
    //     }
    // });
    $('body').on('click', '[name=new_account]', function(){
        if($(this).is(':checked'))
        {
            $('#password_row').show();
        }else{
            $('#password_row').hide();
        }
    });


    // Same Address Checkbox On Click Event
    $('body').on('click', '[name=sameadr]', function(){
        // If the check is checked
        if($(this).is(':checked'))
        {
            sameAddr = 1;
            // Hide the Shipping address area
            $('#shipping_address_area').hide();

            // Show the Billing address area
            $('#billing_address_area').show();
            // If the user has selected to use other address (add new address)
            if(existingAddr == 1){
                $('#billing_address_area .new-addr').hide();
                $('#billing_address_area .select-existing').show();
            }else{
                $('#billing_address_area .new-addr').show();
                $('#billing_address_area .select-existing').hide();
            }
        }else{
            sameAddr = 0;
            // show the Shipping address area
            $('#shipping_address_area').show();

            // Show the Billing address area
            $('#billing_address_area').show();

            // If the user has selected to use other address (add new address)
            if(existingAddr == 0){
                $('#billing_address_area .new-addr').show();
                $('#billing_address_area .select-existing').hide();

                $('#shipping_address_area .new-addr').show();
                $('#shipping_address_area .select-existing').hide();
            }else{
                $('#billing_address_area .new-addr').hide();
                $('#billing_address_area .select-existing').show();

                $('#shipping_address_area .new-addr').hide();
                $('#shipping_address_area .select-existing').show();
            }
        }
    });

    // Use Another Address Button (Add New Address Buton) On Click Event
    $('body').on('click','.add-new-address-btn', function(e){
        $(this).hide();     // Hide the add new address button
        $('.use-existing-address-btn').show();     // Show the select existing address button
        e.preventDefault();
        existingAddr = 0;
        $('[name=existing_address]').val('0');

        $('#billing_address_area').show();
        $('#billing_address_area .select-existing').hide();
        $('#billing_address_area .new-addr').show();
        // If user has selected to use different address for both shipping and billing
        if(sameAddr == 0)
        {
            $('#shipping_address_area').show();
            $('#shipping_address_area .select-existing').hide();
            $('#shipping_address_area .new-addr').show();
        }
    });

    // Select Existing Address Button  On Click Event
    $('body').on('click','.use-existing-address-btn', function(e){
        $(this).hide();     // Hide the add new address button
        $('.add-new-address-btn').show();     // Show the select existing address button
        e.preventDefault();
        existingAddr = 1;
        $('[name=existing_address]').val('1');

        $('#billing_address_area').show();
        $('#billing_address_area .select-existing').show();
        $('#billing_address_area .new-addr').hide();
        // If user has selected to use different address for both shipping and billing
        if(sameAddr == 0)
        {
            $('#shipping_address_area').show();
            $('#shipping_address_area .select-existing').show();
            $('#shipping_address_area .new-addr').hide();
        }
    });
    $.validator.addMethod("email", function (value, element) {
        return this.optional(element) || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i.test(value);
    }, "Email Address is invalid: Please enter a valid email address(eg: abc@gmail.com).");
      // Mix Password Method
    $.validator.addMethod("passwords", function (value, element) {
        return this.optional(element) || /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "{{ __('*Password should contain at least one digit, *Should contain at least one upper & lower case letter,*Should contain at least 8 from the mentioned characters, *Should contain special character  & numbers.')}}");

    // Form Validation
    $('#checkoutForm').validate({
        rules: {
            "email":{
                required:true,
                email:true
            },
            "billing[email]":{
                email:true
            },
            "shipping[email]":{
                email:true
            },
            "password":{
                required:true,
                passwords:true
            },
            "confirm_password":{
                required:true,
                equalTo: "#password"
            }
        },
        messages: {
            "confirm_password":{
                equalTo: "{{__('The password must match')}}"
            }
        }
    });

</script>
@endsection

