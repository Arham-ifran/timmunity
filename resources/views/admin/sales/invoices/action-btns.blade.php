<div class="row main-breadcrumb-div">
    <div class="col-md-8 pl-0">
        <div class="breadcrumb">
            @if (isset($model))
                
                @if($model->status == 1)
                    <!-- If the Invoiced is not paid show the register payment buttion -->
                    @can('Register Payment Invoice')

                        @if($model->refunded_at == null)
                            @if($model->is_paid == 0 )
                                <a class="active" data-toggle="modal" data-target="#register-payment">{{ __('REGISTER PAYMENT') }}</a>
                                <!-- Else If the Invoiced is partially paid  show the register payment buttion -->
                            @elseif($model->is_partially_paid == 1)
                                <a class="active" data-toggle="modal" data-target="#register-payment">{{ __('REGISTER PAYMENT') }}</a>
                            @endif
                        @endif
                    @endcan

                    @php
                        $grace_period = $site_settings[0]->refund_grace_period_days;
                        $invoice_date = \Carbon\Carbon::parse($model->created_at);
                        $last_refund_date = \Carbon\Carbon::parse($model->created_at)->addDays($grace_period);
                        $diff = $invoice_date->diffInDays($last_refund_date);
                        $can_refund = 1;
                        if($diff > 0 && $model->amount_paid > 0 && $model->refunded_at == null){
                            foreach($model->quotation->order_lines as $order_line){
                                foreach($order_line->vouchers as $voucher){
                                    if($voucher->redeemed_at != null){
                                        $can_refund = 0;
                                        break;
                                    }
                                }
                                if($can_refund == 0)
                                {
                                    break;
                                }
                            }
                        }else{
                            $can_refund = 0;
                        }

                    @endphp
                        @if($can_refund == 1)
                        <a class="" data-toggle="modal" data-target="#refund-payment">{{ __('REFUND PAYMENT') }}</a>
                    @endif

                    @can('Download Invoice')
                        <a class="active" download="TIM/{{Carbon\Carbon::parse($model->created_at)->format('Y')}}/{{str_pad($model->id, 3, '0', STR_PAD_LEFT)}}" href="{{ $model->invoice_pdf_link }}">{{ __('DOWNLOAD INVOICE') }}</a>
                    @endcan
                    @can('Reset To Draft Invoice')
                        <a class="action-btn" href="#." data-href="{{ route('admin.quotation.invoice.status',[Hashids::encode($model->id),0]) }}">{{ __('RESET TO DRAFT') }}</a>
                    @endcan
                    <a class="active" data-toggle="modal" data-target="#payment-history">{{ __('VIEW PAYMENTS') }}</a>
                @elseif($model->status == 0)
                    @can('Confirm Quotation Invoice')
                    <a class="active action-btn" href="#." data-href="{{ route('admin.quotation.invoice.status',[Hashids::encode($model->id),1]) }}">{{ __('CONFIRM') }}</a>
                    @endcan
                    @can('Cancel Quotation Invoice')
                    <a class="action-btn" href="#." data-href="{{ route('admin.quotation.invoice.status',[Hashids::encode($model->id),2]) }}">{{ __('CANCEL') }}</a>
                    @endcan
                @elseif($model->status == 2)
                @can('Reset To Draft Invoice')
                    <a class="action-btn active" href="#." data-href="{{ route('admin.quotation.invoice.status',[Hashids::encode($model->id),0]) }}">{{ __('RESET TO DRAFT') }}</a>
                @endcan
                @endif
            @endif
        </div>
    </div>
    <div class="col-md-4 pull-right text-right">

    </div>
</div>

@include('admin.sales.invoices.modal-box.register-payment')
@include('admin.sales.invoices.modal-box.refund-payment')
@include('admin.sales.invoices.modal-box.view-payments')
