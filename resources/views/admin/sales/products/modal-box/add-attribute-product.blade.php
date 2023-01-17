<style>
    span.select2.select2-container {
        width: 100% !important;
    }
</style>
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Add Attribute') }}</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
            </button>
        </div>
        <div class="modal-body ">
            <div class="col-md-12">
                <div class="clearfix mt-2">
                    <div class="form-group col-md-6 mt-3">
                        <label>{{ __('Attribute Name') }}</label>
                        <select class="form-control" id="attribute_name_select">
                            @foreach($attributes as $ind => $attribute)
                                <option value="{{ $attribute->id }}" >{{  $attribute->attribute_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6 mt-3">
                        <label>{{ __('Attribute Values') }}</label>
                        <select class="form-control" id="attribute_value_select" multiple>
                            <option value="" >{{ __('Select a attribute') }}</option>
                        </select>
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
