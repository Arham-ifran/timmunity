<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Add Product') }}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
            </button>
        </div>
        <div class="modal-body ">
            <div class="col-md-12">
                <div class="clearfix mt-2">
                    <div class="form-group col-md-6">
                        <label>{{ __('Product Name') }}</label>
                        <select class="form-control" id="productSelect">
                            <option value="">---{{ __('Select a product') }}---</option>
                            @foreach($products as $ind => $product)
                                <option data-variation-id="{{ $product['variation_id'] }}" data-taxes="{{ $product['taxes'] }}" data-name="{{  $product['name'] }}" value="{{ $product['product_id'] }}" data-price="{{ $product['price'] }}">{{  $product['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('Description') }}</label>
                        <input class="form-control" type="text" name="description" />
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('Quantity') }}</label>
                        <input class="form-control" type="number" name="qty" value="1"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('Unit Price') }}</label>
                        <input class="form-control" type="text" name="unit-price" />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            <button type="button" class="btn btn-success save-optional-product-submit">{{ __('Save changes') }}</button>
        </div>
    </div>
</div>
<script>
</script>
