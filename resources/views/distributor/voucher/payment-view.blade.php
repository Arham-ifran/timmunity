@extends('admin.layouts.app')
@section('title', __('Voucher Invoice Detail'))
@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
    <style>
        span.tagged {
            border: 3px solid;
            border-radius: 30px;
            padding: 0 10px;
        }

        span.tagged.quote {
            border-color: #f5f91a;
            background: #f5f91a85;
        }

        span.tagged.success {
            border-color: #06f50e;
            background: #06f50e66;
        }

        span.tagged.warning {
            border-color: #f9aa1a;
            background: #f9aa1a8c;
        }

        span.tagged.danger {
            border-color: #f91a1a;
            background: #f91a1a7a;
        }

        .o_image {
            display: inline-block;
            width: 38px;
            height: 38px;
            background-image: url('https://plp123.odoo.com//web/static/src/img/mimetypes/unknown.svg');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .o_image[data-mimetype='application/pdf'] {
            background-image: url('https://dfvdf.odoo.com//web/static/src/img/mimetypes/pdf.svg');
        }

        .paid-div {
            position: relative;
            box-shadow: 0 0 15px rgba(0,0,0,.1);
        }

        .ribbon {
            width: 150px;
            height: 150px;
            overflow: hidden;
            position: absolute;
        }
        .ribbon::before,
        .ribbon::after {
            position: absolute;
            z-index: -1;
            content: '';
            display: block;
        }
        .ribbon span {
            position: absolute;
            display: block;
            width: 225px;
            padding: 15px 0;
            box-shadow: 0 5px 10px rgba(0,0,0,.1);
            color: #fff;
            font: 700 18px/1 'Lato', sans-serif;
            text-shadow: 0 1px 1px rgba(0,0,0,.2);
            text-transform: uppercase;
            text-align: center;
            line-height: 5px;
            background-color: #55db34;
        }
        .ribbon span.partially-paid{
            background-color: #dbaf34;
        }

        /* top right*/
        .ribbon-top-right {
            top: 0px;
            right: -10px;
        }
        .ribbon-top-right::before,
        .ribbon-top-right::after {
            border-top-color: transparent;
            border-right-color: transparent;
        }
        .ribbon-top-right::before {
            top: 0;
            left: 0;
        }
        .ribbon-top-right::after {
            bottom: 0;
            right: 0;
        }
        .ribbon-top-right span {
            left: -25px;
            top: 45px;
            transform: rotate(45deg);
        }
        #ajax_loader{
            z-index: 2050;
        }
    </style>

@endsection
@section('content')
    <div class="loader-parent" id="ajax_loader">
        <div class="loader">
            <div class="square"></div>
            <div class="path">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-9">
                    <h2>
                        {{ __('Invoice') }}
                        <small>
                            {{-- {{str_replace(' ','',$model->voucher_order->reseller->name).'-'.str_pad($model->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($model->created_at));}} --}}
                            {{'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT)}}
                        </small>
                    </h2>
                </div>
                <div class="col-md-3">
                    @if (  (currency_format($model->total_payable* $model->voucher_order->exchange_rate,'','',1)) > $model->amount_paid )
                        <a href="#." data-toggle="modal" data-target="#register-payment" class="btn btn-small btn-sm btn-primary mt-3">Register Payment</a>
                    @endif
                    @if (  $model->is_paid == 1 )
                    <a  data-toggle="modal" data-target="#payment-history" class="btn btn-small btn-sm btn-primary mt-3">{{ __('VIEW PAYMENTS') }}</a>
                    @endif
                </div>

            </div>

        </section>
        <section class="content">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h1 class="green-title">
                                {{-- {{str_replace(' ','',$model->voucher_order->reseller->name).'-'.str_pad($model->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($model->created_at));}} --}}
                                {{'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT)}}
                            </h1>
                        </div>
                        @if(@$model->is_paid == 1)
                        <div class="col-md-3 paid-div">
                            <div class="ribbon ribbon-top-right">
                                <span class="@if(@$model->is_partial_paid == 1) partially-paid @endif">
                                    @if(@$model->is_partial_paid == 1) {{ __('Partially Paid') }} @else {{ __('Paid') }} @endif
                                </span>
                            </div>
                        </div>
                        @endif

                    </div>
                    <hr>

                    <div class="row pt-2 pb-3">
                        <!--  Tab Col No 02 -->
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4 static-content">
                                    {{-- <h4 class="color-gray">Expiration</h4> --}}
                                    <h4>{{ __('Invoice Date') }}</h4>
                                </div>
                                <div class="col-sm-8 dynamic-content">
                                    <h5>{{ \Carbon\Carbon::parse($model->created_at)->format('d-M-Y') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4 static-content">
                                    <h4>{{ __('Due Date') }}</h4>
                                </div>
                                <div class="col-sm-8 dynamic-content">
                                    <h5>{{ \Carbon\Carbon::parse($model->created_at)->addDays(7)->format('d-M-Y') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Row 2 Fields -->
                <div class="row">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-tabs mt-3">
                                    <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                                        <!-- Order lines -->
                                        <div id="orderlines" class="tab-pane fade in active">
                                            <table id="product_order_line_table"
                                                class="table table-bordered table-striped sub-table">
                                                <thead>
                                                    <tr>
                                                        <th><a href="#.">{{ __('Pos') }} <span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Description') }} <span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Quantity') }} <span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Unit Price') }}<span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Taxes') }}<span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Total') }}<span class="caret"></span></a></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $product_name = $model->voucher_order->product->product_name.' ' ;
                                                        $product_name .= $model->voucher_order->product->project == null ? @$model->voucher_order->variation->variation_name : '' ;
                                                    @endphp
                                                    <tr class="order_lnes">
                                                        <td class="product_total">1</td>
                                                        <td class="product_name">
                                                            {{ $model->voucher_order->product->product_name.' '.@$model->voucher_order->variation->variation_name }} voucher payment
                                                        </td>
                                                        <td class="product_description">
                                                            {{ count(explode(',',$model->voucher_ids)) }}
                                                        </td>
                                                        <td class="product_qty">
                                                            {{ currency_format(( @$model->voucher_order->unit_price - ( @$model->voucher_order->unit_price * @$model->voucher_order->discount_percentage / 100) ) * $model->voucher_order->exchange_rate,$model->voucher_order->currency_symbol,$model->voucher_order->currency) }}
                                                        </td>
                                                        <td class="product_unit_price">
                                                            {{ @$model->voucher_order->taxes}}
                                                        </td>
                                                        <td class="product_taxes">
                                                           {{ currency_format(@$model->total_payable* $model->voucher_order->exchange_rate,$model->voucher_order->currency_symbol,$model->voucher_order->currency) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div class="col-md-8">

                                            </div>
                                            <div class="col-md-4" style="text-align:right">
                                                <hr>
                                                <p>
                                                    <strong>{{ __('NET') }}:</strong>
                                                    <span id="untaxed_amount">
                                                        {{ currency_format((@$model->voucher_order->unit_price - ( @$model->voucher_order->unit_price * @$model->voucher_order->discount_percentage / 100) ) * $model->voucher_order->exchange_rate,$model->voucher_order->currency_symbol,$model->voucher_order->currency) }}
                                                    </span>
                                                </p>
                                                <p>
                                                    <strong> {{ __('Taxes') }}: </strong>
                                                    <span id="taxed_amount">
                                                        {{ currency_format($model->tax_amount* $model->voucher_order->exchange_rate,$model->voucher_order->currency_symbol,$model->voucher_order->currency) }}
                                                    </span>
                                                </p>
                                                <hr>
                                                <p>
                                                    <strong> {{ __('Total') }}: </strong>
                                                    <span id="total_amount">
                                                        {{ currency_format($model->total_payable* $model->voucher_order->exchange_rate,$model->voucher_order->currency_symbol,$model->voucher_order->currency) }}
                                                    </span>
                                                </p>
                                                <p>
                                                    <strong> {{ __('Total Paid') }}: </strong>
                                                    <span id="total_amount">
                                                        {{currency_format($model->amount_paid,$model->voucher_order->currency_symbol,$model->voucher_order->currency) }}
                                                    </span>
                                                </p>
                                                @php
                                                    $remaining_amount = currency_format($model->total_payable* $model->voucher_order->exchange_rate,'','',1) - currency_format($model->amount_paid,'','',1);
                                                @endphp
                                                @if($remaining_amount > 0)
                                                <p>
                                                    <strong> {{ __('Remaining Payment') }}: </strong>
                                                    <span id="remaining_amount">
                                                        {{currency_format($remaining_amount,$model->voucher_order->currency_symbol,$model->voucher_order->currency) }}
                                                    </span>
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="register-payment" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="{{ route('admin.voucher.orders.payment.register', $model->id) }}" method="POST" id="register-payment-form">
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
                                @php
                                    $total =  currency_format($model->total_payable* $model->voucher_order->exchange_rate,'','',1);
                                @endphp
                                <label class="control-label" for="registered_amount">{{ __('Amount').' ('.$model->voucher_order->currency.')' }}</label>
                                <input required type="number" class="form-control" name="registered_amount" min="0.01" step="0.01" max="{{ $total - $model->amount_paid }}" step="0.01" data-amountpaid="{{ $model->amount_paid }}" data-total="{{ $total }}" value="{{ currency_format(($total - $model->amount_paid),'','',1) }}">
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
                                <input type="submit" class="btn btn-primary register-payment-btn-submit" value="Register Payment" Payment/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                        @if($model->voucher_payment_references != null)
                            @foreach($model->voucher_payment_references as $ph)
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label" for="registered_amount">{{ __('Amount') }}</label>
                                        <input type="text"  readonly="readonly" class="form-control" value="{{ currency_format($ph->amount,$model->voucher_order->currency_symbol,$model->voucher_order->currency)  }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label" for="method">{{ __('Payment Method') }}</label>
                                        <input type="text"  readonly="readonly" class="form-control" value="{{ $ph->method }} @if($ph->transaction_id != null)( {{ $ph->transaction_id }} ) @endif">
                                    </div>
                                </div>

                            @endforeach
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


@endsection

@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script>
        /***  change Invoice Status  */
        $('body').on('click','.breadcrumb a.action-btn',function(){
            href_attr = $(this).attr('data-href');
            $('#invoice-status-form').attr('action', href_attr);
            $('#invoice-status-form').submit();
        })
        $('body').on('click','.register-payment-btn-submit',function(){
            registered_amount = $('[name=registered_amount]').val();
            amount_paid = $('[name=registered_amount]').data('amountpaid') + registered_amount;
            total = $('[name=registered_amount]').data('total');
            if( amount_paid > total )
            {
                $("#ajax_loader").hide();
            }else{
                $("#ajax_loader").show();
            }
            if(!$('#register-payment-form').valid()){
                $("#ajax_loader").hide();

            }
        })
    </script>
@endsection
