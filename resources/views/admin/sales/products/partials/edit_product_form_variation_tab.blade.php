<div class="row pt-2">
    <div class="col-md-12">
        <a class="btn btn-primary mt-2 mb-2" data-toggle="modal" data-target="#add_variant">Add New Variant</a>
    </div>
    <div class="col-md-12">
        <table id="productsTable" class="table table-bordered table-striped" style="width:100%">
            <thead>
                <tr role="row">
                    <th>{{ __('SKU') }}</th>
                    <th>{{ __('Variation') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="add_variant" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Add New Variant') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        {{-- <form id="add_variant_form" action="{{ route('admin.license.import') }}" method="POST" enctype="multipart/form-data"> --}}
                            {{ csrf_field() }}
                            <div class="row add-attributes-row">
                                <input type="hidden" name="attribute_count" value="{{count($model_attached_attributes)}}">
                                @foreach( $model_attached_attributes as $index => $model_attached_attribute)
                                    <div class="col-md-12">
                                        <input id="attribueID{{ $index }}" value="{{ $model_attached_attribute->id }}" type="hidden">
                                        <label for="" class="control-label">{{translation( $model_attached_attribute->attributeDetail->id,13,app()->getLocale(),'attribute_name',$model_attached_attribute->attributeDetail->attribute_name)}}</label>
                                        <select id="attribueValue{{ $index }}" class="form-control select2" data-tags="true"  style="width: 100%">
                                            @foreach($model_attached_attribute->allAttributeValue as $a_v)
                                                <option value="{{ $a_v->id }}">{{ $a_v->attribute_value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="variation_sales_price" class="control-label">Variation Sales Price</label>
                                        <input class="form-control" type="number" name="variation_sales_price" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="variation_cost_price" class="control-label">Variation Cost Price</label>
                                        <input class="form-control" type="number" name="variation_cost_price" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="reseller_sales_price" class="control-label">Reseller Sales Price</label>
                                        <input class="form-control" type="number" name="reseller_sales_price" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sku" class="control-label">SKU</label>
                                        <input class="form-control" type="text" name="sku" >
                                    </div>
                                    <div class="form-group">
                                        <label for="ean" class="control-label">EAN</label>
                                        <input class="form-control" type="text" name="ean" >
                                    </div>
                                    <div class="form-group">
                                        <label for="gtin" class="control-label">GTIN</label>
                                        <input class="form-control" type="text" name="gtin" >
                                    </div>
                                    <div class="form-group">
                                        <label for="mpn" class="control-label">MPN</label>
                                        <input class="form-control" type="text" name="mpn" >
                                    </div>
                                </div>
                            </div>
                        {{-- </form> --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="add-variant-btn" >{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
