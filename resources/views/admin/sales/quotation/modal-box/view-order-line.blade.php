<!-- View Order Line Modal -->
<div class="modal fade" id="view-order-line-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Open') }}: {{ __('Order Lines') }}</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4 pl-0"><strong>{{ __('Product') }}</strong></div>
                            <div class="col-md-8"><p id="product"></p></strong></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pl-0"><strong>{{ __('Quantity') }}</strong></div>
                            <div class="col-md-8"><p id="qty"></p></strong></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pl-0"><strong>{{ __('Invoiced') }}</strong></div>
                            <div class="col-md-8"><p id="invoiced"></p></strong></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pl-0"><strong>{{ __('Delivered') }}</strong></div>
                            <div class="col-md-8"><p id="delivered"></p></strong></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pl-0"><strong>{{ __('Unit Price') }}</strong></div>
                            <div class="col-md-8"><p id="unit_price"></p></strong></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pl-0"><strong>{{ __('Taxes') }}</strong></div>
                            <div class="col-md-8"><p id="o_taxes"></p></strong></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                {{-- <p> <strong> {{ __('Licenses') }}</strong></p> --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <p> <strong> {{ __('Vouchers') }}</strong></p>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{route('admin.quotation.voucher.list', Hashids::encode(@$model->id))}}" target="_blank">{{__('View Details')}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="o_licenses"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr >
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('Description') }}</strong></p>
                                <p id="o_description"></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('Total') }}</strong></p>
                                <p id="o_total"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
<style>
    div#o_licenses {
        border: 1px solid #009a71;
        height: 200px;
        overflow: auto;
    }
</style>
