@extends('admin.layouts.app')
@section('title', __('Invoice Detail'))
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
        .ribbon span.refunded{
            background-color: red;
        }

        /* top right*/
        .ribbon-top-right {
            top: -10px;
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
                <div class="col-md-12">
                    <h2>
                        {{ __('Invoices') }}
                        TIM/{{ \Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p>
                        @isset($model)
                            @if($model->refunded_at == null)
                                @switch($model->status)
                                    @case(0)
                                        <span class="tagged quote">{{ __('Draft') }}</span>
                                        @break
                                    @case(1)
                                        <span class="tagged success">{{ __('Confirmed') }}</span>
                                        @break
                                    @case(2)
                                        <span class="tagged danger">{{ __('Cancelled') }}</span>
                                        @break
                                    @default
                                @endswitch
                            @else
                                <span class="tagged danger">{{ __('Refunded') }}</span>
                            @endif
                        @endisset
                    </p>
                </div>
            </div>

        </section>
        <section class="content">
            <div class="box">
            <!-- Header Actions Button -->
            @canany(['Cancel Quotation Invoice','Register Payment Invoice','Download Invoice','Reset To Draft Invoice'])
                <div class="row">
                    <div class="box-header">
                        @include('admin.sales.invoices.action-btns')
                    </div>
                </div>
            @endcanany
                <div class="box-body">
                    <div class="col-md 12">
                        <div class="row box pt-2 mt-3">
                            <div class="row">
                                <div class="col-md-9">
                                    <p>
                                        {{ __('Customer Invoice') }}
                                    </p>
                                    <h1 class="green-title">
                                            TIM/{{ \Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT) }}
                                    </h1>
                                </div>
                                @if($model->refunded_at == null)
                                    @if(@$model->is_paid == 1)
                                    <div class="col-md-3 paid-div">
                                        <div class="ribbon ribbon-top-right">
                                            <span class="@if(@$model->is_partially_paid == 1) partially-paid @endif">
                                                @if(@$model->is_partially_paid == 1) {{ __('Partially Paid') }} @else {{ __('Paid') }} @endif
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    <div class="col-md-3 paid-div">
                                        <div class="ribbon ribbon-top-right">
                                            <span class="refunded">
                                                {{ __('Refunded') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <hr>

                            <div class="row pt-2 pb-3">
                                <!--  Tab Col No 01 -->
                                <div class="col-md-6 pl-0">
                                    <div class="row">
                                        <div class="col-sm-4 static-content">
                                            <h4>{{ __('Customer') }}</h4>
                                        </div>
                                        <div class="col-sm-8 dynamic-content">
                                            <h4><a href="#">{{  @$model->quotation->customer->name }}</a></h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 static-content">
                                            <h4>{{ __('Delivery Address') }}</h4>
                                        </div>
                                        <div class="col-sm-8 dynamic-content">
                                            <h4>
                                                <a href="#">
                                                    @if(@$model->quotation->delivery_address == null || @$model->quotation->delivery_address == 0 )
                                                        @if(@$model->quotation->invoice_address != null && @$model->quotation->invoice_address != 0 )
                                                        {{ @$model->quotation->invoice_address_detail->street_1 . ', ' . @$model->quotation->invoice_address_detail->city . ', ' . @$model->quotation->invoice_address_detail->contact_countries->name }}
                                                        @else
                                                            @if(@$model->quotation->customer->street_1 != null && @$model->quotation->customer->street_1 != 0 )
                                                            {{ @$model->quotation->customer->street_1 . ', ' . @$model->quotation->customer->city . ', ' . @$model->customer->quotation->contact_countries->name }}
                                                            @else
                                                            {{ $model->quotation->customer->name }}
                                                            @endif
                                                        @endif
                                                    @else
                                                    {{ @$model->quotation->delivery_address_detail->street_1 . ', ' . @$model->quotation->delivery_address_detail->city . ', ' . @$model->quotation->delivery_address_detail->contact_countries->name }}
                                                    @endif
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 static-content">
                                            <h4>{{ __('Payment Reference') }}</h4>
                                        </div>
                                        <div class="col-sm-8 dynamic-content">
                                            <h4>
                                                <a  href="#">
                                                    {{-- @if($model->status == 1 ) --}}
                                                        {{-- INV / {{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }} --}}
                                                        TIM/{{ \Carbon\Carbon::parse($model->created_at)->format('Y').'/'.$model->id }}
                                                    {{-- @else --}}
                                                        {{-- / --}}
                                                    {{-- @endif --}}
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
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
                                    <div class="row">
                                        <div class="col-sm-4 static-content">
                                            <h4>{{ __('Due Date') }}</h4>
                                        </div>
                                        <div class="col-sm-8 dynamic-content">
                                            <h5>{{ \Carbon\Carbon::parse($model->created_at)->format('d-M-Y') }}</h5>
                                        </div>
                                    </div>
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
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#orderlines">{{ __('Order Lines') }}</a></li>
                                        <li><a data-toggle="tab" href="#other-info">{{ __('Other Info') }}</a></li>

                                    </ul>

                                    <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                                        <!-- Order lines -->
                                        <div id="orderlines" class="tab-pane fade in active">
                                            <table id="product_order_line_table"
                                                class="table table-bordered table-striped sub-table">
                                                <thead>
                                                    <tr>
                                                        <th><a href="#.">{{ __('Product') }} <span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Label') }} <span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Quantity') }} <span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Unit Price') }}<span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Taxes') }}<span class="caret"></span></a></th>
                                                        <th><a href="#.">{{ __('Sub Total') }}<span class="caret"></span></a></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (@$model->invoice_order_lines != null)
                                                        @foreach ($model->invoice_order_lines as $invoice_order_line)
                                                            <tr class="order_lnes" data-invoice-order-list-id="{{ @$invoice_order_line->id }}" >
                                                                <td class="product_name">{{ @$invoice_order_line->quotation_order_line->product->product_name.' '.@$invoice_order_line->quotation_order_line->variation->variation_name }}
                                                                </td>
                                                                <td class="product_description">{{ @$invoice_order_line->quotation_order_line->description }}</td>
                                                                <td class="product_qty">{{ @$invoice_order_line->invoiced_qty }}</td>
                                                                {{-- <td class="product_unit_price">{{ number_format(@$invoice_order_line->quotation_order_line->unit_price * $model->quotation->exchange_rate,2).' '.$model->quotation->currency}}</td> --}}
                                                                <td class="product_unit_price">{{ currency_format(@$invoice_order_line->quotation_order_line->unit_price * $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency)}}</td>
                                                                <td class="product_taxes">
                                                                    @if (@$invoice_order_line->quotation_order_line->quotation_taxes != null)
                                                                        @foreach ($invoice_order_line->quotation_order_line->quotation_taxes as $tax)
                                                                            <span class="tagged">
                                                                                {{ @$tax->tax->name }}
                                                                            </span>
                                                                        @endforeach
                                                                    @endif
                                                                    @if(@$model->quotation->vat_percentage != 0)
                                                                        <span class="tagged">{{ @$model->quotation->vat_label }}: {{ @$model->quotation->vat_percentage }} %</span>
                                                                    @endif
                                                                </td>
                                                                {{-- <td class="product_total">{{ number_format($invoice_order_line->total* $model->quotation->exchange_rate,2).' '.$model->quotation->currency }}</td> --}}
                                                                <td class="product_total">{{ currency_format($invoice_order_line->total* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>

                                            <div class="col-md-8">

                                            </div>
                                            <div class="col-md-4" style="text-align:right">
                                                <hr>
                                                <p> <strong>{{ __('Untaxed Amount') }}:</strong> <span
                                                        id="untaxed_amount">{{  currency_format((@$model->total -  @$model->totaltax)* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}</span></p>
                                                <p> <strong> {{ __('Taxes') }}: </strong> <span
                                                        id="taxed_amount">{{  currency_format(@$model->totaltax* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}</span></p>
                                                <hr>
                                                <p> <strong> {{ __('Total') }}: </strong> <span
                                                        id="total_amount">{{  currency_format(@$model->total* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}</span></p>
                                                @if($model->is_partially_paid == 1 || $model->is_paid == 1)
                                                    <p> <strong> {{ __('Total Paid') }}: </strong> <span
                                                        id="total_amount">
                                                        @if($model->quotation->transaction_id == null )
                                                            {{ currency_format($model->amount_paid,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                        @else
                                                            {{ currency_format($model->amount_paid,$model->quotation->currency_symbol,$model->quotation->currency) }}

                                                        @endif
                                                    </span></p>
                                                    <p> <strong> {{ __('Remaining Amount') }}: </strong> <span
                                                        id="total_amount">
                                                        @if($model->quotation->transaction_id == null )
                                                            {{   currency_format((currency_format($model->total* $model->quotation->exchange_rate,'','',1) - $model->amount_paid),$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                        @else
                                                            {{   currency_format((currency_format($model->total* $model->quotation->exchange_rate,'','',1) - $model->amount_paid),$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                        @endif
                                                    </span></p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- other info-->
                                        <div id="other-info" class="tab-pane fade">
                                            <div class="row">
                                                <!-- Gernal Tab Col No 01 -->
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <h3 class="col-sm-12 green-title">{{ __('Order') }}</h3>
                                                        <div class="col-sm-6">
                                                            <h4>{{ __('Sales Person') }}</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5>{{  (isset($model->quotation->other_info->sales_person)) ? @$model->quotation->other_info->sales_person->firstname.' '.$model->quotation->other_info->sales_person->lastname: '' }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <h4>{{ __('Sales Team') }}</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5>{{(isset($model->quotation->other_info->sales_team)) ? @$model->quotation->other_info->sales_team->sales_team_name :'' }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <h4>{{ __('Online Signature') }}</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5><input type="checkbox" value="checked" @if(@$model->quotation->other_info->online_signature == 1) checked @endif disabled></h5>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <h4>{{ __('Online Payment') }}</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5><input type="checkbox" value="checked" @if(@$model->quotation->other_info->online_payment == 1) checked @endif disabled></h5>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <h4>{{ __('Customer Reference') }}</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5>{{ @$model->quotation->other_info->customer_reference }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <h4>{{ __('Tags') }}</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5>
                                                                @if(@$model->quotation->other_info->tags != null)
                                                                    @foreach($model->quotation->other_info->tags as $tag)
                                                                        <span class="tagged">{{ $tag->tag->name }}</span>
                                                                    @endforeach
                                                                @endif
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <h4>{{ __('Delivery Date') }}</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <h5>{{ (isset($model->quotation->other_info->delivery_date)) ? \Carbon\Carbon::parse($model->quotation->other_info->delivery_date)->format('d-M-Y') : ''}}</h5>
                                                        </div>
                                                    </div>
                                                </div>
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

    <form method="POST"  id="invoice-status-form">@csrf</form>
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
        function ShowLoader(){
            max = $("[name=registered_amount]").attr('max');
            val = $("[name=registered_amount]").val();
            refund_reason = $("[name=refund_reason]").val();
            if(refund_reason != ''){

                if(parseFloat(val) > parseFloat(max) ){
                    $("#ajax_loader").hide();
                }else{
                    $("#ajax_loader").show();
                }
                if(!$('#register-payment-form').valid()){
                    $("#ajax_loader").hide();
                    
                }
            }
        }
    </script>
@endsection
