
@foreach ($products as $product)
    @php
        $show_product = 1;
    @endphp
    @if($product->project_id != null )
        @if(Auth::user())
            @if(Auth::user()->contact->type == 3)
                @php
                    $show_product = 1;
                @endphp
            @else
                @php
                    $show_product = 0;
                @endphp
            @endif
        @else
            @php
                $show_product = 0;
            @endphp
        @endif
    @endif
    @if($show_product == 1)
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-5" data-aos="zoom-in" data-aos-duration="2000">
            <div class="shop-product-box shop-box-des">
                <div class="wrapper">
                    <div class="product-img">
                        @if(Auth::user())
                            @if(Auth::user()->contact->type != 3 )
                                <a href="{{ route('frontside.shop.product-details', $product->slug) }}">
                            @else
                                <a href="#.">
                            @endif
                        @else
                            <a href="{{ route('frontside.shop.product-details', $product->slug) }}">
                        @endif
                        <img src="{!! checkImage(asset('storage/uploads/sales-management/products/' . $product->image), 'placeholder-products.jpg') !!}">
                        </a>
                    </div>
                    <div class="product-footer row">
                        <h4 class="product-heading col-md-8" title="{{ $product->product_name }}">{{ $product->product_name }}</h4>
                        <span class="product-price color-red">
                            {{ Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol') }}
                            @php

                                // $product_price = $product->price_without_vat['total_price_exclusive_vat'] + ($product->generalInformation->sales_price * $vat_percentage / 100);
                                $product_price = $product->price_without_vat['total_price_exclusive_vat'];

                                $end_product_price = 0;
                                $allow_only_reseller_price = 1;
                                foreach($product->variations as $product_variation)
                                {
                                    if($product_variation->reseller_sales_price == ''  || $product_variation->reseller_sales_price == null )
                                    {
                                        $allow_only_reseller_price = 0;
                                    }
                                }
                                if($product->variations_count != 0){
                                    if(Auth::user() && isset(Auth::user()->contact)){
                                        if(Auth::user()->contact->type == 3){
                                            $prices = resellerProductPrice(Auth::user()->contact->id, $product->id);
                                            
                                            $product_price = $prices['product_price']; 
                                            $end_product_price = $prices['end_product_price']; 
                                        }
                                    }else{
                                        foreach($product->variations as $ind => $product_variation){
                                            if(Auth::user() && isset(Auth::user()->contact)){
                                                if(Auth::user()->contact->type == 3){
                                                    
                                                }
                                                else{
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
                                    }


                                    $end_product_price  = $end_product_price + ($end_product_price * $vat_percentage / 100);
                                }else{
                                    if(Auth::user() && isset(Auth::user()->contact)){
                                        if(Auth::user()->contact->type == 3){
                                            $prices = resellerProductPrice(Auth::user()->contact->id, $product->id);
                                            $product_price = $prices['product_price']; 
                                        }
                                    }
                                }
                                if($product->product_name == "device immunity with Norton 360"){
                                }
                                $product_price = $product_price + ($product_price * $vat_percentage / 100);
                                

                            @endphp
                            {{
                                currency_format(
                                    $product_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),
                                    '',
                                    '',
                                    1 );
                            }}
                            @if($product->variations_count != 0 && $product_price < $end_product_price)
                                -
                                {{
                                    currency_format(
                                        $end_product_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),
                                        '',
                                        '',
                                        1 );
                                }}
                            @endif
                            {{ Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code') }}
                        </span>
                    </div>
                </div>

                <div class="products-buttons">
                    <div class="shop-btn-wrapper">
                        @if(Auth::user())
                            @if(Auth::user()->contact->type != 3 )
                                <div class="@if(Auth::user()->contact->type == 2) @else @endif">
                                        <a href="{{ route('frontside.shop.product-details', $product->slug) }}" class="btn btn-primary">{{ __('Add to Cart') }}</a>
                                </div>
                            @else
                                <div>
                                    <a href="#." class="btn btn-primary  voucher-btn" data-id="{{ Hashids::encode($product->id) }}">{{ __('Order Vouchers') }}</a>
                                </div>
                            @endif

                        @else
                                <div>
                                    <a href="{{ route('frontside.shop.product-details', $product->slug) }}" class="btn btn-primary">{{ __('Add to Cart') }}</a>
                                </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
