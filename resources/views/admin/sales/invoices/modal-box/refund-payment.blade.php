<div class="modal fade" id="refund-payment" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('admin.quotation.invoice.refund-payment') }}" method="POST" id="refund-payment-form">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ Hashids::encode($model->id) }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title col-md-9 pl-0" id="exampleModalLongTitle">{{ __('Refund Payment') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>{{__('Do you really want to make a refund?')}}</strong></p>
                            <small>{{ __('Kindly mention reason for refund') }}<span style="color: red">*</span></small>
                           <textarea name="refund_reason" id="refund_reason" style="width:100%" rows="10" placeholder="Enter Refund Reason" required></textarea>
                        </div>
                        <div class="col-md-12">
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 text-left">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                            <input type="submit" class="btn btn-primary register-payment-btn-submit" onClick="ShowLoader()" value="Yes Make Refund"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
