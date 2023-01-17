<div class="modal fade" id="payment-history" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title col-md-9 pl-0" id="exampleModalLongTitle">{{ __('Payments History') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($model->invoice_payment_history != null)
                        @foreach($model->invoice_payment_history as $ph)
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label" for="registered_amount">{{ __('Amount') }}</label>
                                    <input type="text"  readonly="readonly" class="form-control" value="{{ currency_format($ph->amount,$model->quotation->currency_symbol,$model->quotation->currency) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label" for="method">{{ __('Payment Method') }}</label>
                                    <input type="text"  readonly="readonly" class="form-control" value="{{ $ph->method }} @if($ph->transaction_id != null)( {{ $ph->transaction_id }} ) @endif">
                                </div>
                            </div>
                            
                            @endforeach
                        @endif
                    @if($model->refunded_at != null)
                        <div class="row refunded_row">
                            <div class="col-md-12">
                                <label class="control-label" for="registered_amount"> <strong> {{ __('Invoice Refunded At') }}</strong> {{ \Carbon\Carbon::parse($model->refunded_at) }}</label>
                                <p>{{ $model->refund_reason }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 text-left">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .refunded_row {
    background: #009a71;
    margin: 12px 15px 0px;
    padding: 15px 0px;
    color: white;
}
</style>
