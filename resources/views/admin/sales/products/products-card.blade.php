@if(count($products) > 0)
    @foreach($products as $product)
        <div class="col-sm-6 col-md-4">
            <a href="{{route('admin.products.edit', Hashids::encode( $product->id))}}">
                <div class="pro-box row">
                    <div class="product-dashboard-img col-sm-4">
                        <img src="{!!checkImage(asset('storage/uploads/sales-management/products/' .@$product->image),'placeholder-products.jpg')!!}">
                    </div>
                    <div class="product-content cool-sm-8">
                        <h3 class="product-heading">
                            {{$product->product_name}}
                        </h3>
                        @if($product->variations_count > 0)
                        <h5 class="caption">
                            {{ $product->variations_count }} {{ __('Variations') }}
                        </h5>
                        @else
                        <h5 class="caption">
                            &nbsp;
                        </h5>
                        @endif
                        <h5 class="caption">
                            {{ @$product->generalInformation->internal_reference}}
                        </h5>
                        <span class="price">€{{ isset($product->generalInformation->sales_price) ? number_format($product->generalInformation->sales_price,2) : 0}} </span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
@else
    <p class="text-center">{{ __('No record found!') }}</p>
@endif
