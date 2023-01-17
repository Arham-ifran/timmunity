<style>
    .row.product_row {
        border: 1px solid #009a71;
        border-radius: 5px;
    }
    .remove-product{
        float: right;
        cursor:pointer;
        color:red;
    }
</style>

<div class="modal fade" id="getVoucherModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="voucher_form" action="{{ route('frontside.reseller.orderVoucher') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ __('Order Voucher') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                      
                        <div class="col-md-6">
                            <label for="reseller_name" class="control-label">{{ __('Name') }}<span style="color:red">*</span></label>
                            <input required type="hidden" readonly class="form-control" name="reseller_id" value="{{ Hashids::encode(Auth::user()->id) }}">
                            <input required type="text" readonly class="form-control" name="reseller_name" value="{{ Auth::user()->name }}">
                        </div>
                        <div class="col-md-6">
                            <label for="reseller_email" class="control-label">{{ __('Email') }}<span style="color:red">*</span></label>
                            <input required type="email" readonly class="form-control" name="reseller_email" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="col-xs-12 mt-2">
                            <label for="reseller_phone_no" class="control-label">{{ __('Phone no') }}</label>
                            <input type="number" class="form-control" name="reseller_phone_no" value="{{ Auth::user()->contact->phone }}">
                        </div>
                        <div class="col-xs-12">
                            <label for="address" class="control-label">{{ __('Street Address') }}<span style="color:red">*</span></label>
                            <input required type="text"  class="form-control" name="address" value="{{ Auth::user()->contact->street_1.', '.Auth::user()->contact->city }}" >
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="control-label">{{ __('City') }}<span style="color:red">*</span></label>
                            <input required type="text"  class="form-control" name="city" value="{{ Auth::user()->contact->city }}">
                        </div>
                        <div class="col-md-6">
                            <label for="zip_code" class="control-label">{{ __('Zip Code') }}</label>
                            <input  type="text"  class="form-control" name="zip_code" value="{{ Auth::user()->contact->zipcode }}">
                        </div>
                        <input type="hidden" name="vat_percentage" value="{{ $vat_percentage }}">
                        <div class="col-md-12">
                            <label for="country_id" class="control-label">{{ __('Country') }}<span style="color:red">*</span></label>
                            <select required name="country_id" id="" class="form-control">
                                <option value="">{{ __('Select Country') }}</option>
                                @foreach ($countries as $country)
                                    <option
                                        data-vat_vercentage="{{ $country->vat_in_percentage }}"
                                        data-is_default_vat="{{ $country->is_default_vat }}"
                                        data-default_vat="{{ $default_vat }}"
                                        data-vat_label="{{ $country->vat_label }}"
                                        value="{{ Hashids::encode($country->id) }}"
                                        @if(Auth::user()->contact->country_id == $country->id) selected="seelcted" @endif
                                        >
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small><span class="vat_percentage">{{ $vat_percentage }}</span>{{__('% ')}} <span class="vat-label-d">{{__(Auth::user()->contact->contact_countries->vat_label)}}</span> {{__('is already applied')}}</small>
                        </div>
                        <div class="col-xs-12" id="product_section_column">
                            <div class="row product_row mb-2 mt-2 pb-2">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="product_id" class="control-label">{{ __('Product') }}<span style="color:red">*</span></label>
                                        <select required name="product_id[]" id="" class="form-control">
                                            <option value="">{{ __('Select Product') }}</option>
                                            @foreach( $products as $product )
                                                <option value="{{ Hashids::encode($product->id) }}" data-secondary-projects='@json($product->secondary_projects_array)' data-variation-count="{{ $product->variations_count }}">{{ $product->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 secondary_projects_div" style="display:none;">
                                    <div class="form-group">
                                        <label for="variations_id" class="control-label">{{ __('Secondary Products') }}</label>
                                        <div class="data">
        
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 variation_selection" style="display:none;">
                                    <div class="form-group">
                                        <label for="variation_id" class="control-label">{{ __('Variation') }}<span style="color:red">*</span></label>
                                        <select name="variation_id[]" id="" class="form-control">
        
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 product_price" style="display:none;">
                                    <div class="form-group">
                                        <label for="product_price" class="product_price_label control-label">{{ __('Price') }} &nbsp;<span style="color:green"><strong></strong></span></label>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label for="quantity" class="control-label">{{ __('Quantity') }} <small class="asterik" style="color:red">*</small></label>
                                    <input required type="number" min="1" class="form-control" name="quantity[]" />
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 text-right">
                            <p class="btn btn-primary" id="add_new_product_btn" title="{{ __('Add new product') }}"><i class="fa fa-plus"></i></p>
                        </div>
                        <div class="col-md-12">
                            <label for="message" class="control-label">{{ __('Message') }} <small>{{ __('(If any)') }}</small></label>
                            <textarea class="form-control" name="message" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Place Order') }}</button>
                </div>
            </div>
        </form>

    </div>
</div>