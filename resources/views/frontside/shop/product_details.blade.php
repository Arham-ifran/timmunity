@extends('frontside.layouts.app')
@section('title') {{ $product->product_name }} @endsection
@section('style')
{{-- <link rel="stylesheet" href="{{ asset('frontside/dist/css/jquery-supergallery-plugin2.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('frontside/dist/css/flexslider.css') }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .product-details h3{
        font-family: 'Avenir Next' !important;
        font-weight: bold !important;
        font-size: 17px;
    }
    span.price-area {
        background: #009a71;
        color: white;
        padding: 5px 10px;
    }
    #shop-page {
        padding-bottom: 20px;
    }
</style>
@endsection
@section('content')
    <!-- Product Setails Section -->
    <section class="content-section" id="shop-page">
        <form id="addToCart" method="post" action="{{ route('frontside.shop.cart.add') }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="container">
                <div class="row clearfix">
                    <div class="mt-5">
                        <div class="col-lg-6 col-md-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                    <div class="feature-product-img">
                                        @php
                                            $eccomerce_images = $product->eccomerce_images;
                                            $eccomerce_image_count = count($eccomerce_images);
                                            $total_thumb_count = ceil($eccomerce_image_count / 5);      // Will return 5 for 5 and 6 for numbers between 5.1 and 6
                                            $total_remaining_images =  $eccomerce_image_count % 5;
                                            $image_index = 0;
                                        @endphp
                                        @if($eccomerce_image_count > 0)
                                        <div id="slider" class="flexslider">
                                            <ul class="slides">
                                                @foreach($eccomerce_images as $ind => $eccomerce_image)
                                                <li>
                                                    <img src="{{ url('storage/uploads/sales-management/products/eccomerce').'/'.$eccomerce_image->image }}" />
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div id="carousel" class="flexslider">
                                            <ul class="slides">
                                                @foreach($eccomerce_images as $ind => $eccomerce_image)
                                                <li>
                                                    <img src="{{ url('storage/uploads/sales-management/products/eccomerce').'/'.$eccomerce_image->image }}" />
                                                </li>
                                                @endforeach
                                            </ul>
                                         </div>
                                        @else
                                         <img style="min-height:400px" src="{!! checkImage(asset('storage/uploads/sales-management/products/' . $product->image), 'placeholder-products.jpg') !!}">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                            </div>

                        </div>
                        <div class="col-lg-6 col-md-12 col-xs-126">
                            <div class="product-details">
                                <div class="">
                                    <h3>
                                        {{ $product->product_name }}
                                        <span title="Price Inclusive Tax" style="float:right" class="price-area">
                                            {{ Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol') }}
                                            @php
                                                $vat_label = __("VAT");
                                                if(Auth::user()){
                                                    $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
                                                    if(Auth::user()->contact->contact_countries->is_default_vat == 1)
                                                    {
                                                        $vat_percentage = $default_vat_percentage;
                                                    }else{
                                                        $vat_label = Auth::user()->contact->contact_countries->vat_label ? Auth::user()->contact->contact_countries->vat_label : __('VAT');
                                                    }
                                                }
                                                $product_price = $product->price_without_vat['total_price_exclusive_vat'];
                                                $end_product_price = 0;
                                                // $product_price_without_vat = $product->price_without_vat['total_price_exclusive_vat'];
                                                // $vat_amount = number_format($product->generalInformation->sales_price * $vat_percentage / 100,2);
                                                // $product_price = $product->price_without_vat['total_price_exclusive_vat'] + ($product->generalInformation->sales_price * $vat_percentage / 100);
                                                // if($product->variations_count != 0)
                                                //     $end_product_price = $product->price_without_vat['end_price'] + ($product->price_without_vat['end_price_without_tax'] * $vat_percentage / 100);
                                                foreach($product->variations as $ind => $product_variation){
                                                    if(Auth::user() && isset(Auth::user()->contact)){
                                                    
                                                        if($product_variation->variation_sales_price != ''  && $product_variation->variation_sales_price != null ){
                                                            if($product_variation->variation_sales_price < $product_price ){
                                                                $product_price = $product_variation->variation_sales_price;
                                                            }
                                                            if($product_variation->variation_sales_price > $end_product_price ){
                                                                $end_product_price = $product_variation->variation_sales_price;
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if($product_variation->variation_sales_price != ''  && $product_variation->variation_sales_price != null ){
                                                            if($product_variation->variation_sales_price < $product_price ){
                                                                $product_price = $product_variation->variation_sales_price;
                                                            }
                                                            if($product_variation->variation_sales_price > $end_product_price ){
                                                                $end_product_price = $product_variation->variation_sales_price;
                                                            }
                                                        }
                                                    }
                                                }
                                                $product_price += $product_price *$vat_percentage / 100;
                                                $end_product_price += $end_product_price *$vat_percentage / 100;
                                            @endphp
                                            @if($product->variations_count == 0)
                                                {{
                                                    currency_format(
                                                        $product_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),
                                                        '',
                                                        '',
                                                        1 );
                                                }}
                                            @else
                                                {{
                                                    currency_format(
                                                        $product_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),
                                                        '',
                                                        '',
                                                        1 );
                                                }}
                                                @if($product_price != $end_product_price)
                                                    -
                                                    {{
                                                        currency_format(
                                                            $end_product_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),
                                                            '',
                                                            '',
                                                            1 );
                                                    }}
                                                @endif
                                            @endif
                                            &nbsp; {{ Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code') }}
                                        </span>
                                    </h3>
                                    <small style="float:right">Inclusive of {{$vat_percentage}} % {{ $vat_label }}</small>
                                    <div class="row">
                                        <div class="product-details-bottom-section mt-2 ">
                                            {!! translation( $product->sales->id,11,app()->getLocale(),'description',$product->sales->description) !!}
                                        </div>
                                    </div>
                                    <div class="panel-group custom-panel-style" id="accordion">
                                        @php
                                            $attributes = $product->attributes->toArray();
                                            $sorted = usort($attributes, function ($item1, $item2) {
                                                return $item1['attribute_name'] <=> $item2['attribute_name'];       // ascending
                                            });
                                        @endphp
                                        @foreach ($attributes as $product_attached_attribute)
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a>
                                                            {{ translation(
                                                                $product_attached_attribute['attribute_detail']['id'],
                                                                13,
                                                                app()->getLocale(),
                                                                'attribute_name',
                                                                $product_attached_attribute['attribute_detail']['attribute_name']
                                                                ) }}
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapse{{ $product_attached_attribute['id'] }}"
                                                    class="panel-collapse collapse in">
                                                    <div class="panel-body">
                                                        @php
                                                            $attributeValues = $product_attached_attribute['attribute_value'];
                                                            $sorted = usort($attributeValues, function ($item1, $item2) {
                                                                return $item1['value'] <=> $item2['value'];
                                                            });
                                                        @endphp
                                                        @foreach ($attributeValues as $ind => $attribute_value)
                                                            @if($attribute_value['is_active'] == 1)
                                                                <label class="custom-radio-button">
                                                                    @php
                                                                        // $param_array = [$attribute_value['attribute_value_detail']['id'] ,$product_attached_attribute['attribute_detail']['attribute_name']]
                                                                        $param_array = [$attribute_value['attribute_value_detail']['id'] ,$product_attached_attribute['attribute_detail']['id']]
                                                                    @endphp
                                                                    @if(count($attributeValues) == 1 || $ind == 0)
                                                                        <input type="radio" value="{{ @$attribute_value['attribute_value_detail']['id'] }}"
                                                                            name="attribute_values[{{ @$product_attached_attribute['attribute_id'] }}]" checked="checked" onchange="getExtraPrice({{json_encode($param_array)}})" required>
                                                                        <span class="checkmark"></span>
                                                                    @else
                                                                        <input type="radio" value="{{ @$attribute_value['attribute_value_detail']['id'] }}"
                                                                            name="attribute_values[{{ @$product_attached_attribute['attribute_id'] }}]" onclick="getExtraPrice({{json_encode($param_array)}})" required>
                                                                        <span class="checkmark"></span>
                                                                    @endif
                                                                    {{ @$attribute_value['attribute_value_detail']['attribute_value'] }}
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="cart-quilty-incremnt">
                                                <button type="button" id="decrement"><i class="fa fa-minus"
                                                        aria-hidden="true"></i></button>
                                                <input type="text" name="quantity"min="1" value="1" maxlength="2" max="10" size="1"
                                                    id="quantity" readonly/>
                                                <button type="button" id="increment"><i class="fa fa-plus"
                                                        aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <a class="cart-btn dark-green"  href="#" id="addToCartbtn">
                                                {{ __('Add to Cart') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row clearfix">
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-md-12">
                        {!! translation( $product->sales->id,11,app()->getLocale(),'long_description',$product->sales->long_description) !!}
                        {{-- {{$product->sales->long_description}} --}}
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
@section('script')
{{-- <script src="{{ asset('frontside/dist/js/jquery-supergallery-plugin2.js') }}"></script> --}}
<script src="{{ asset('frontside/dist/js/jquery.flexslider.js') }}"></script>
    <script>

        attribute_ids = [];
        $("input[type=radio]:checked").trigger('change');
        @foreach ($product->attributes as $product_attached_attribute)
            @if( count($product_attached_attribute->attributeValue) == 1)
                attribute_ids['{{$product_attached_attribute->attributeDetail->id}}'] = '{{$product_attached_attribute->attributeValue[0]->attributeValueDetail->id}}';
            @else
                attribute_ids['{{$product_attached_attribute->attributeDetail->id}}'] = 0;
            @endif
        @endforeach
        var currency = "{{ Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol') }}";
        var currency_code = "{{ Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code') }}";
        var product_price = '{{currency_format(($product_price* (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1)),'','',1)}}';
        product_price = parseFloat(product_price);
        $('#increment').click((e) => {
            var quantity = $('#quantity')
            quantity.attr('value', parseInt(quantity.attr('value')) + 1)
        })
        $('#decrement').click((e) => {
            var quantity = $('#quantity')
            if (parseInt(quantity.attr('value')) > 0) {
                if(quantity.attr('value') - 1 > 0){
                    quantity.attr('value', parseInt(quantity.attr('value')) - 1);
                }
            }
        })
        $('body').on('click', '#addToCartbtn', function(){
            $('form').submit();
        });
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        function getExtraPrice(data){
            attribute_ids[data[1]] = data[0];
            data = JSON.stringify(Object.assign({}, attribute_ids))
            $.ajax({
                url: '{{ route("frontside.shop.extra_price") }}?product_id={{$product->id}}&attribute_ids='+data,
                type: 'GET',
                contentType: "application/json",
                success: function (data) {
                    price = 0;
                    if(data['extra'] == true){
                        price = parseFloat(data['amount'])+product_price;
                    }
                    else
                    {
                        price = parseFloat(data['amount']);
                    }
                    $('.price-area').html(currency+' '+price.toFixed(2)+' '+currency_code);
                },
                complete:function(data){
                }
            })
        }
        $('#carousel').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: false,
            itemWidth: 100,
            itemMargin: 5,
            asNavFor: '#slider'
        });
        $('#slider').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: false,
            slideshow: true,                //Boolean: Animate slider automatically
            slideshowSpeed: 5000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
            animationSpeed: 600,
            // itemWidth: 300,
            sync: "#carousel",
            start: function(slider){
            $('body').removeClass('loading');
            }
        });
    </script>
@endsection
