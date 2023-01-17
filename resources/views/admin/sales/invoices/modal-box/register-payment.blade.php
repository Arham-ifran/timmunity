<div class="modal fade" id="register-payment" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('admin.quotation.invoice.register-payment') }}" method="POST" id="register-payment-form">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ Hashids::encode($model->id) }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title col-md-9 pl-0" id="exampleModalLongTitle">{{ __('Register Payment') }}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label" for="registered_amount">{{ __('Amount') }}</label>
                            <input required type="number" class="form-control" name="registered_amount" min="0.01" step="0.01" max="{{ currency_format((($model->total* $model->quotation->exchange_rate) - $model->amount_paid),'','',1) }}" data-amountpaid="{{ $model->amount_paid }}" data-total="{{ $model->total }}" value="{{ currency_format((($model->total * $model->quotation->exchange_rate) - $model->amount_paid),'','',1) }}">

                        </div>
                        <div class="col-md-6">
                            <label class="control-label" for="method">{{ __('Payment Method') }}</label>
                            <select name="method" id="" class="form-control">
                                <option value="Cash">{{__('Cash Payment')}}</option>
                                <option value="Bank Transfer">{{__('Bank Transfer')}}</option>
                                <option value="Online Payment"> {{__('Online Payment')}} </option>
                                {{-- 'Cash', 'Bank Transfer', 'Online Payment' --}}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 text-left">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                            <input type="submit" class="btn btn-primary register-payment-btn-submit" onClick="ShowLoader()" value="Register Payment" Payment/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
