@extends('admin.layouts.app')
@if ($action == 'Add')
@section('title', __('Create Quotation'))
@else
@section('title', __('Edit Quotation'))
@endif
@section('styles')
<link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
<style>
    span.select2.select2-container.select2-container--default.select2-container {
        width: 100% !important;
    }
    /* .vt_list #product_order_line_table input.form-control, #product_order_line_table span.select2-selection.select2-selection--multiple, #product_order_line_table .select2-selection.select2-selection--single{width: 40% !important;} */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #499a72;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color:white;
    }
    span.tagged {
        border: 3px solid;
        border-radius: 30px;
        padding: 0 10px;
        height: 25px;

    }
    .vt_list span.tagged {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .vt_list .select2-selection.select2-selection--multiple .select2-selection__rendered{
        display:inline-flex;
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
    .one_linetext{display: inline-flex;align-items: center;}
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
        background-image: url('{{ asset("backend/dist/img/pdf.svg") }}');
    }
    #product_order_line_table th, #product_order_line_table td{white-space: nowrap;}
    .d-flex{display: flex}
    #product_order_line_table input.form-control, #product_order_line_table select.form-control ,
    #product_order_line_table .select2-container--default .select2-selection--multiple, #product_order_line_table  .select2-selection.select2-selection--single {
        background: transparent;
        border: none;
    }

    #product_order_line_table input.form-control:hover, #product_order_line_table select.form-control:hover,
    #product_order_line_table .select2-container--default .select2-selection--multiple:hover, #product_order_line_table .select2-selection.select2-selection--single:hover {
        border: 1px solid #ccc;
    }
    #product_order_line_table input.form-control, #product_order_line_table span.select2-selection.select2-selection--multiple, #product_order_line_table .select2-selection.select2-selection--single {
        border: 1px solid #ccc !important;
        border-radius: 5px !important;
    }
    #product_order_line_table  input[readonly].form-control{
        border:none !important;
    }
    #product_order_line_table th{
        min-width: 250px;
    }
    .delete-order-line{
        color: red;
        font-size: 20px;
    }
    .loader-parent {
        z-index: 1000000;
    }
</style>
@endsection
@section('content')
    <div class="content-wrapper">
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
        <section class="content-header top-header">
            <div class="row">
                <div class="col-md-12">
                    <h2>
                        {{ __('Quotation') }} /
                        <small>
                            @isset($model)
                                S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}
                            @else
                            {{ __('Add') }}
                            @endisset
                        </small>
                    </h2>
                    <p>
                        @isset($model)
                                @switch($model->status)
                                    @case(0)
                                        <span class="tagged quote">{{ __('Quotation') }}</span>
                                        @break
                                    @case(1)
                                        <span class="tagged success">{{ __('Sales Order') }}</span>
                                        @break
                                    @case(2)
                                        <span class="tagged warning">{{ __('Locked') }}</span>
                                        @break
                                    @case(3)
                                        <span class="tagged quote">{{ __('Quotation Sent') }}</span>
                                        @break
                                    @case(4)
                                        <span class="tagged danger">{{ __('Cancelled') }}</span>
                                        @break
                                    @default

                                @endswitch
                                 
                                @if($model->is_refunded == true)
                                <span class="tagged danger">{{ __('Refunded Order') }}</span>
                                @endif
                            @endisset
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="box-header">
                    <div class="row">
                        <div class="col-md-4 form-save-btn-div">
                            <a class="skin-gray-light-btn btn save-quotation-d" href="javascript:void(0)">{{ __('Save') }}</a>
                            <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2"
                                href="{{ route('admin.quotations.index') }}">{{ __('Discard') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="main-box box">
                <form class="timmunity-custom-dashboard-form" id="quotation-form" method="POST"
                    action="{{ route('admin.quotations.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="{{ $action }}">
                    <input type="hidden" name="quotation_status" value="{{ isset($model) ? $model->status : 0 }}">
                    <input type="hidden" name="id" value="{{ Hashids::encode(@$model->id) }}">
                    <input type="hidden" name="redirect_url" value="{{ isset($model) ? route('admin.quotations.edit',Hashids::encode($model->id)) : route('admin.quotations.index') }}">


                    <!-- Customer Preview Bar -->
                    @canany(['Quotation Invoices','Customer Preview'])
                    @include('admin.sales.quotation.partials.customer-preview-header')
                    @endcanany
                    <!-- Header Actions Button -->
                    @if(!@$model->is_refunded)
                    <div class="row">
                        <div class="box-header">
                            @include('admin.sales.quotation.action-btns')
                        </div>
                    </div>
                    @endif
                    <!-- Row 1 Fields -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="quotations-form-container">
                                <div class="box box-success box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">{{ __('Create New Quotation') }}</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Customer') }}<small style="color:red">*</small></label>
                                                    <!--Status Locked / Sales Order -->
                                                    @if( @$model->status == 1 || @$model->status == 2 )
                                                       <p>
                                                           <strong> {{ @$model->customer->name }}</strong><br/>
                                                           {{ translation($model->customer->id,4,app()->getLocale(),'street_1',$model->customer->street_1)  }}<br>
                                                           {{-- @if(@$model->customer->street_2 != null) {{ translation(@$model->customer->id,4,app()->getLocale(),'street_2',@$model->customer->street_2)  }}<br> @endif
                                                           {{ @$model->customer->contact_countries->name }} --}}

                                                        </p>
                                                    @else
                                                        <select class="form-control" name="customer_id">
                                                            <option value="">---{{ __('Select a customer') }}---</option>
                                                            @foreach ($customer as $cust)
                                                                <option value="{{ $cust->id }}" @if( @$model->customer_id == $cust->id ) selected="selected" @endif data-vat_percentage="{{ isset($cust->contact_countries->vat_in_percentage) ? @$cust->contact_countries->vat_in_percentage :  0}}">{{ $cust->name }} </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    @if( @$model->status == 1 || @$model->status == 2 )
                                                    <label>{{ __('Order Date') }}</label>
                                                    <p>{{ \Carbon\Carbon::now()->format('m/d/Y ') }}</p>
                                                    @else
                                                    <label>{{ __('Expiration') }}</label>
                                                        <input class="form-control" type="date" name="expires_at" value="@if(isset($model)){{ trim(date('Y-m-d', strtotime($model->expires_at))) }}@else{{ \Carbon\Carbon::now()->addDays(30)->format('Y-m-d') }}@endif" />
                                                    @endif
                                                </div>
                                            </div>
                                            @if(@$sales_settings['orders_customer_address'] == 1)

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Invoice Address') }}</label>
                                                    @if(@$model->status == 1 || @$model->status == 2 )
                                                        @foreach($model->customer->contact_addresses as $cust_add)
                                                            @if($cust_add->type == 1 && (int)$model->invoice_address == (int)$cust_add->id)
                                                                <p>{{ translation($cust_add->id,5,app()->getLocale(),'street_1',$cust_add->street_1) .'  , '.translation($cust_add->id,5,app()->getLocale(),'city',$cust_add->city) .'  , '.$cust_add->contact_countries->name }}</p>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <select class="form-control" name="invoice_address">
                                                            <option>---{{ __('Select a customer address') }}---</option>
                                                            <!-- Dynamic Options on changing the customer -->
                                                            @if( isset( $model->customer_id ) )
                                                                @foreach($model->customer->contact_addresses as $cust_add)
                                                                    @if($cust_add->type == 1)
                                                                        <option value="{{ $cust_add->id }}" @if($model->invoice_address == $cust_add->id) selected="selected" @endif>{{ $cust_add->contact_name.'  , '.translation($cust_add->id,5,app()->getLocale(),'street_1',$cust_add->street_1) .'  , '.translation($cust_add->id,5,app()->getLocale(),'city',$cust_add->city) .'  , '.$cust_add->contact_countries->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Price lists') }}</label>
                                                    @if( @$model->status == 1 || @$model->status == 2 )
                                                        <p>
                                                            {{ translation( @$model->pricelist->id,12,app()->getLocale(),'name',@$model->pricelist->name) }}
                                                            @if( $model->pricelist && ( isset($model->pricelist->rules[0]->percentage_value) || isset($model->pricelist->parent->rules[0]->percentage_value) ) )
                                                                @if($model->pricelist->parent_id == null)
                                                                    <span>( {{ $model->pricelist->rules[0]->percentage_value }} % discount )</span>
                                                                @else
                                                                    <span>( {{ $model->pricelist->parent->rules[0]->percentage_value }} % discount )</span>
                                                                @endif
                                                            @endif
                                                        </p>
                                                    @else
                                                        <select class="form-control" name="pricelist_id">
                                                            <option value="">---{{ __('Select a price list') }}---</option>
                                                            @foreach($price_lists as $ind => $price_list)
                                                                <option value="{{ $price_list->id }}" @if( @$model->pricelist->id == $price_list->id ) selected="selected" @elseif($ind == 0) selected="selected" @endif>{{ translation( $price_list->id,12,app()->getLocale(),'name',$price_list->name) }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small id="update-prices-btn"> <a href="#.">{{ __('Update Prices') }}</a> </small>

                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if(@$sales_settings['orders_customer_address'] == '1')
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Delivery Address') }}</label>
                                                    @if(@$model->status == 1 || @$model->status == 2 )
                                                        @foreach($model->customer->contact_addresses as $cust_add)
                                                            @if($cust_add->type == 2 && (int)$model->delivery_address == (int)$cust_add->id)
                                                                <p>{{ translation($cust_add->id,5,app()->getLocale(),'street_1',$cust_add->street_1) .'  , '.translation( $cust_add->id,5,app()->getLocale(),'city', $cust_add->city).'  , '.$cust_add->contact_countries->name }}</p>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <select class="form-control" name="delivery_address">
                                                            <option value="">---{{ __('Select a customer address') }}--- </option>
                                                            <!-- Dynamic Options on changing the customer -->
                                                            @if( isset( $model->customer_id ) )
                                                                @foreach($model->customer->contact_addresses as $cust_add)
                                                                    @if($cust_add->type == 2)
                                                                        <option value="{{ $cust_add->id }}" @if($model->delivery_address == $cust_add->id) selected="selected" @endif>{{ $cust_add->contact_name.'  , '.translation($cust_add->id,5,app()->getLocale(),'street_1',$cust_add->street_1).'  , '.translation( $cust_add->id,5,app()->getLocale(),'city', $cust_add->city).'  , '.$cust_add->contact_countries->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>{{ __('Payment Terms')}}</label>
                                                    <select class="form-control" name="payment_terms">
                                                        <option value="">---{{ __('Select a payment term') }}---</option>
                                                        @foreach ($payment_term as $ind =>$p_term)
                                                            <option value="{{ $p_term->id }}" @if (isset($model) && $p_term->id == $model->payment_terms) selected @elseif($ind == 0) selected="selected" @endif>
                                                                {{ $p_term->term_value }}
                                                                @switch($p_term->term_type)
                                                                    @case(1)
                                                                        {{ __('Days') }}
                                                                        @break
                                                                    @case(2)
                                                                        {{ __('Months') }}
                                                                        @break
                                                                    @case(2)
                                                                        {{ __('Years') }}
                                                                        @break
                                                                    @default

                                                                @endswitch
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                @if(@$model->status != 1 && @$model->status != 2)
                                                    <div class="form-group">
                                                        <label>{{ __('Payment Due Day') }}</label>
                                                        <input type="text" name="payment_due_date" class="form-control" value="{{ isset($model) ? @$model->payment_due_day : 0 }}" placeholder="0" />
                                                    </div>
                                                @endif
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
                                            <li><a data-toggle="tab" href="#optional-products">{{ __('Optional Products') }}</a></li>
                                            <li><a data-toggle="tab" href="#other-info">{{ __('Other Info') }}</a></li>
                                            <li><a data-toggle="tab" href="#text-template">{{ __('Text Template') }}</a></li>

                                        </ul>

                                        <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                                            <!-- Order lines -->
                                            <div id="orderlines" class="tab-pane fade in active">
                                                @php
                                                    if(isset($model)){
                                                        if($model->order_lines != null){
                                                            $value = '';
                                                            $count_order_lines = count($model->order_lines);
                                                            foreach( $model->order_lines as $ind => $order_line ){
                                                                if($ind == 0){
                                                                    $value=$order_line->id;
                                                                }
                                                                else{
                                                                    $value.=','.$order_line->id;
                                                                }
                                                            }
                                                        }

                                                    }
                                                @endphp
                                                <input type="hidden" name="quotation_order_lines" value="{{ @$value }}">
                                                <input type="hidden" name="vat_percentage" value="{{ @$model ? $model->vat_percentage : 0 }}">
                                                <input type="hidden" name="vat_label" value="{{ @$model ? $model->vat_label : "VAT" }}">
                                                <div class="row">
                                                    <div class="anchor-links">
                                                        <a href="javascript:void(0)"
                                                            class="btn skin-green-light-btn order-line-option-d">
                                                            {{ __('Add a product') }}
                                                        </a>
                                                        <a type="button" class="btn skin-green-light-btn"
                                                            data-toggle="modal" data-target="#add-section-model">
                                                            {{ __('Add a Section') }}
                                                        </a>
                                                        <a type="button" class="btn skin-green-light-btn"
                                                            data-toggle="modal" data-target="#add-note-model">
                                                            {{ __('Add a Note') }}
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table id="product_order_line_table" class="table table-bordered table-striped sub-table">
                                                        <thead>
                                                            <tr>
                                                                <th><a href="#.">{{ __('Product') }} <span class="caret"></span></a></th>
                                                                <th><a href="#.">{{ __('Description') }} <span class="caret"></span></a></th>
                                                                <th><a href="#.">{{ __('Quantity') }} <span class="caret"></span></a></th>
                                                                @if(@$model->status == 1 || @$model->status == 2)
                                                                <th><a href="#.">{{ __('Delivered Quantity') }} <span class="caret"></span></a></th>
                                                                <th><a href="#.">{{ __('Invoiced Quantity') }} <span class="caret"></span></a></th>
                                                                @endif
                                                                <th><a href="#.">{{ __('Lead Time') }} <span class="caret"></span></a></th>
                                                                <th><a href="#.">{{ __('Unit Price').' ( ' . @$model->currency .' )' }} <span class="caret"></span></a></th>
                                                                <th><a href="#.">{{ __('Taxes') }} <span class="caret"></span></a></th>
                                                                <th><a href="#.">{{ __('Sub Total')  .' ( ' . @$model->currency .' )'}} <span class="caret"></span></a></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $quotation_total = 0;
                                                                $tax_amount_total = 0;
                                                                $total_subtotal = 0;

                                                            @endphp
                                                            @if(@$model->order_lines != null)
                                                                @foreach( $model->order_lines as $order_line )
                                                                @if($order_line->product_id != null)
                                                                    @if( $model->status == 2 )
                                                                        <tr class="order_lnes" data-order-list-id="{{ @$order_line->id }}" >
                                                                            <td class="product_name">{{ @$order_line->product->product_name }}
                                                                                <input type="hidden" name="vouchers" class="vouchers"
                                                                                    value = "
                                                                                    @if($order_line->vouchers != null)
                                                                                        <ul style='padding-left:15px'>
                                                                                            @foreach($order_line->vouchers as $voucher)
                                                                                                @if($voucher->redeemed_at == null)
                                                                                                    {{ '<li>'.$voucher->voucher_code.'</li>' }}
                                                                                                @else
                                                                                                    {{ '<li style="color:red">'.$voucher->voucher_code.'<br><span style="color:green">License Key: <br>'.$voucher->license->license_key.'</span></li>' }}
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </ul>
                                                                                        @endif
                                                                                    ">
                                                                            </td>
                                                                            <td class="product_description">{{ @$order_line->description }}</td>
                                                                            <td class="product_qty">{{ @$order_line->qty }}</td>
                                                                            <td class="product_qty">{{ @$order_line->delivered_qty }}</td>
                                                                            <td class="product_qty">{{ @$order_line->invoiced_qty }}</td>
                                                                            <td class="product_lead_time">
                                                                                @if (@$order_line->product != null)
                                                                                    {{ $order_line->product->lead_time != null ? \Carbon\Carbon::parse($order_line->product->lead_time)->format('d-M-Y') : '' }}
                                                                                @endif
                                                                            </td>
                                                                            <td class="product_unit_price">
                                                                                {{-- {{ number_format(@$order_line->unit_price * $model->exchange_rate,2) . ' ' . $model->currency }} --}}
                                                                                {{ currency_format(@$order_line->unit_price * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}
                                                                            </td>
                                                                            <td class="product_taxes">
                                                                                @if ($order_line->quotation_taxes != null)
                                                                                    @foreach ($order_line->quotation_taxes as $tax)
                                                                                        <span class="tagged">
                                                                                            {{ translation( $tax->tax->id,9,app()->getLocale(),'name',$tax->tax->name) }}</span>
                                                                                    @endforeach
                                                                                    <span class="tagged">
                                                                                        {{ @$model ? $model->vat_percentage : 0 }}% {{ @$model ? $model->vat_label : 'VAT' }}</span>
                                                                                @endif
                                                                            </td>
                                                                            @php
                                                                                $product_price = $order_line->product != null ? $order_line->unit_price : 0;
                                                                                $subtotal = $order_line->qty * $product_price;
                                                                                $total_subtotal += $order_line->qty * $product_price;
                                                                                $total = $subtotal;

                                                                                foreach ($order_line->quotation_taxes as $tax) {
                                                                                    switch ($tax->tax->computation) {
                                                                                        case 0:
                                                                                            $tax_amount_total += $tax->tax->amount;
                                                                                            $total += $tax->tax->amount;
                                                                                            break;

                                                                                        case 1:
                                                                                            $tax_amount_total += ($subtotal * $tax->tax->amount) / 100;
                                                                                            $total += ($subtotal * $tax->tax->amount) / 100;
                                                                                            break;
                                                                                    }
                                                                                }
                                                                                $quotation_total += $total;
                                                                                $quotation_total += ($subtotal * $model->vat_percentage) / 100;;

                                                                            @endphp
                                                                            <td class="product_total">
                                                                                {{-- {{ number_format($total,2) . ' ' . $model->currency  }} --}}
                                                                                {{ currency_format(@$total * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}
                                                                            </td>
                                                                        </tr>
                                                                    @else
                                                                    <tr data-order-list-id="{{ @$order_line->id }}" class="order_line_row">
                                                                        <td>
                                                                            <select name="order_line_product" id="" class="form-control">
                                                                                @foreach($products as $ind => $product)
                                                                                    <option
                                                                                        data-variation-id="{{ $product['variation_id'] }}"
                                                                                        data-taxes="{{ $product['taxes'] }}"
                                                                                        data-name="{{  $product['name'] }}"
                                                                                        value="{{ $product['product_id'] }}"
                                                                                        data-price="{{ $product['price'] }}"

                                                                                        @if($order_line->variation_id != null)
                                                                                            @if($order_line->product_id == $product['product_id']
                                                                                                && $order_line->variation_id == $product['variation_id'] )
                                                                                                selected="selected"
                                                                                            @endif
                                                                                        @else
                                                                                            @if($order_line->product_id == $product['product_id'])
                                                                                                selected="selected"
                                                                                            @endif
                                                                                        @endif
                                                                                        >
                                                                                        {{ $product['name'] }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td><input class="form-control" type="text" name="order_line_description" value="{{ @$order_line->description }}" id=""></td>
                                                                        <td><input class="form-control" type="number" name="order_line_qty" @if( $model->status == 1 || $model->status == 2 ) min="{{ @$order_line->invoiced_qty }}" @endif value="{{ @$order_line->qty }}" id=""></td>
                                                                        @if(@$model->status == 1 || @$model->status == 2)
                                                                        <td><input class="form-control" type="number" min="0"  max="{{ @$order_line->qty }}" name="order_line_delivered_qty" value="{{ @$order_line->delivered_qty }}" id=""></td>
                                                                        <td><input class="form-control" type="number" name="order_line_inviced_qty" value="{{ @$order_line->invoiced_qty }}" id="" readonly></td>
                                                                        @endif
                                                                        <td>
                                                                            @if(@$order_line->product != null)
                                                                            {{ $order_line->product->lead_time != null ? \Carbon\Carbon::parse($order_line->product->lead_time)->format('d-M-Y') : ''  }}</td>
                                                                            @endif
                                                                        <td>
                                                                            <input
                                                                                class="form-control"
                                                                                type="text"
                                                                                name="order_line_unit_price"
                                                                                {{-- value="{{number_format( @$order_line->unit_price * $model->exchange_rate,2)  }}"  --}}
                                                                                value="{{ currency_format(@$order_line->unit_price * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}"
                                                                                id="">
                                                                            </td>
                                                                        <td class="vt_list">
                                                                            <div class="d-flex">
                                                                            <div>
                                                                                <select class="d-flex" multiple="" class="form-control" data-placeholder="Select Tax" name="order_line_taxes">
                                                                                    @foreach( $customer_taxes as $customer_tax )
                                                                                        <option  value="{{ $customer_tax->id }}"
                                                                                            @if( $order_line->quotation_taxes != null )
                                                                                                @foreach($order_line->quotation_taxes as $tax)
                                                                                                    @if( $tax->tax_id == $customer_tax->id )
                                                                                                        selected="selected"
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            @endif>
                                                                                            {{ $customer_tax->name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="d-flex">
                                                                                <span class="tagged d-flex ml-2"><span>{{ @$model->vat_percentage }}</span> % {{ @$model ? $model->vat_label : 'VAT' }} </span>
                                                                            </div>
                                                                        </td>
                                                                        @php
                                                                            $product_price = $order_line->product != null ? $order_line->unit_price : 0;
                                                                            $subtotal = $order_line->qty * $product_price;
                                                                            $total_subtotal += $order_line->qty * $product_price;
                                                                            $total = $subtotal;
                                                                            foreach($order_line->quotation_taxes as $tax)
                                                                            {
                                                                                switch ($tax->tax->computation) {
                                                                                    case 0:
                                                                                        $tax_amount_total += $tax->tax->amount;
                                                                                        $total += $tax->tax->amount;
                                                                                        break;

                                                                                    case 1:
                                                                                        $tax_amount_total += $subtotal * $tax->tax->amount /100;
                                                                                        $total += $subtotal * $tax->tax->amount /100;
                                                                                        break;
                                                                                }
                                                                            }
                                                                            $total += $subtotal * $model->vat_percentage / 100;
                                                                            $tax_amount_total += $subtotal * $model->vat_percentage / 100;
                                                                            $quotation_total += $total;

                                                                        @endphp
                                                                        <td class="one_linetext">
                                                                            <input
                                                                                type="text"
                                                                                class="form-control"
                                                                                name="order_line_total"
                                                                                value="{{ currency_format(@$total * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}"
                                                                                readonly>
                                                                            <i class="fa fa-trash delete-order-line" aria-hidden="true" style="float:right;cursor: pointer;"></i>
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                @elseif(@$order_line->section != null)
                                                                    <tr data-order-list-id="{{ @$order_line->id }}" class="order_line_row">
                                                                        <td colspan="9">
                                                                            <input
                                                                            type="text"
                                                                            name="order_line_section"
                                                                            class="form-control"
                                                                            value="{{ @$order_line->section }}"
                                                                            >
                                                                            <i class="fa fa-trash delete-order-line" aria-hidden="true" style="float:right;cursor: pointer;"></i>
                                                                        </td>
                                                                    </tr>
                                                                @elseif(@$order_line->notes != null)
                                                                    <tr data-order-list-id="{{ @$order_line->id }}" class="order_line_row">
                                                                        <td colspan="9">
                                                                            <input
                                                                            type="text"
                                                                            name="order_line_notes"
                                                                            class="form-control"
                                                                            value="{{ translation(@$order_line->id,2,app()->getLocale(),'text',@$order_line->notes) }}"><i class="fa fa-trash delete-order-line" aria-hidden="true" style="float:right;cursor: pointer;"></i>
                                                                        </td>
                                                                    </tr>
                                                                @endif

                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                        <tfoot>

                                                        </tfoot>
                                                    </table>
                                                </div>

                                                <!-- add a section Modale -->
                                                <div class="modal fade" id="add-section-model" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Add a Section') }}
                                                                </h3>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true"><i class="fa fa-times"
                                                                            aria-hidden="true"></i></span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body ">
                                                                <!-- Form Start Here  -->
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="clearfix mt-2">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group col-md-12">
                                                                                    <label>{{ __('Section Name') }}</label>
                                                                                    <input type="text" name="section" class="form-control" id="section_name">
                                                                                    <div style="" id="section-error"
                                                                                        class="invalid-feedback animated  add">
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- End Here -->
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">{{ __('Close') }}</button>
                                                                <button type="button" class="btn btn-success section-save">{{ __('Save changes') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Add a note -->
                                                <div class="modal fade" id="add-note-model" tabindex="-1" role="dialog"
                                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h3 class="col-sm-8" id="exampleModalLongTitle">{{ __('Add a Note') }}</h3>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true"><i class="fa fa-times"
                                                                            aria-hidden="true"></i></span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body ">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="clearfix mt-2">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group col-md-12">
                                                                                    <!-- <label>Add a Note</label> -->
                                                                                    <input class="form-control" type="text" name="notes"
                                                                                        placeholder="add a note">
                                                                                        <div style="" id="note-error"
                                                                                        class="invalid-feedback animated  add">
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">{{ __('Close') }}</button>
                                                                <button type="button" class="btn btn-success notes-save">{{ __('Save changes') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-8">
                                                        <textarea class="textarea" placeholder="{{ __('Terms and Conditions') }}" name="terms_and_conditions"
                                                            style="" value="{{ translation(@$model->id,1,app()->getLocale(),'terms_and_conditions',@$model->terms_and_conditions) }}">{{ translation(@$model->id,1,app()->getLocale(),'terms_and_conditions',@$model->terms_and_conditions) }}</textarea>
                                                    </div>
                                                    <div class="col-md-4" style="text-align:right">
                                                        <p>
                                                            <strong> {{ __('Untaxed Amount')  }}: </strong>
                                                            <span id="untaxed_amount">
                                                                @if(isset($model))
                                                                {{ currency_format(@$total_subtotal * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}
                                                                @else
                                                                € 0.00 EUR
                                                                @endif
                                                            </span>
                                                        </p>
                                                        <p>
                                                            <strong> {{ __('Taxes') }}: </strong>
                                                            <span id="taxed_amount">
                                                                @if(isset($model))
                                                                {{ currency_format(@$tax_amount_total * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}
                                                                @else
                                                                € 0.00 EUR
                                                                @endif
                                                            </span>
                                                        </p>
                                                        <hr>
                                                        <p>
                                                            <strong>{{ __('Total') }}: </strong>
                                                            <span id="total_amount">
                                                                @if(isset($model))
                                                                {{ currency_format(@$quotation_total * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}
                                                                @else
                                                                € 0.00 EUR
                                                                @endif
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- optional products -->
                                            <div id="optional-products" class="tab-pane fade">
                                                @php
                                                    if(isset($model)){
                                                        if(@$model->optional_products != null){
                                                            $value = '';
                                                            $count_optional_products = count(@$model->optional_products);
                                                            foreach( @$model->optional_products as $ind => $optional_product ){
                                                                if($ind == 0){
                                                                    $value=$optional_product->id;
                                                                }
                                                                else{
                                                                    $value.=','.$optional_product->id;
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <input type="hidden" name="optional_products" value="{{ @$value }}">
                                                <table id="optional_products_table" class="table table-bordered table-striped sub-table">
                                                    <thead>
                                                        <tr>
                                                            <th><a href="#">{{ __('Product') }} <span class="caret"></span></a></th>
                                                            <th><a href="#">{{ __('Description') }} <span class="caret"></span></a></th>
                                                            <th><a href="#">{{ __('Quantity') }} <span class="caret"></span></a></th>
                                                            <th><a href="#">{{ __('Unit Price').' ( ' . @$model->currency .' )' }} <span class="caret"></span></a></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(@$model->optional_products != null)
                                                            @foreach( $model->optional_products as $optional_product )
                                                                <tr data-order-list-id="{{ @$optional_product->id }}">
                                                                    <td>{{ @$optional_product->product->product_name }}</td>
                                                                    <td>{{ @$optional_product->description }}</td>
                                                                    <td>{{ @$optional_product->qty }}</td>
                                                                    <td>
                                                                        {{-- {{ number_format(@$optional_product->unit_price,2) }}  --}}
                                                                        {{ currency_format(@$optional_product->unit_price * @$model->exchange_rate,@$model->currency_symbol,@$model->currency) }}
                                                                        <i class="fa fa-trash delete-order-line-optional" aria-hidden="true" style="float:right;cursor: pointer;"></i>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    <tfoot>
                                                        <div class="row">
                                                            <div class="anchor-links">
                                                                <a type="button" class="btn skin-green-light-btn optional-product-option-d">
                                                                    {{ __('Add a product') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </tfoot>
                                                </table>

                                            </div>
                                            <!-- other info-->
                                            <div id="other-info" class="tab-pane fade">
                                                <div class="tab-quotation-form other-info">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="contact-box clearfix mt-2">
                                                                <!-- -------------Heading--------- -->
                                                                <div class="col-md-12">
                                                                    <h3>{{ __('Sales') }}</h3>
                                                                </div>
                                                                <!------------ Form Coloum One----------->
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>{{ __('Sales Person') }}</label>
                                                                        <select class="form-control" name="otherinfo[sales_person]">
                                                                            <option value="">---{{ __('Select a sales person') }}---</option>
                                                                            @foreach( $salespersons as $salesperson )
                                                                                <option value="{{ $salesperson->id }}"
                                                                                        @if($action == 'Add')
                                                                                            @if(Auth::user()->id == $salesperson->id )
                                                                                                selected="selected"
                                                                                            @endif
                                                                                        @else
                                                                                            @if(@$model->other_info->salesperson_id == $salesperson->id )
                                                                                                selected="selected"
                                                                                            @endif
                                                                                        @endif
                                                                                    >
                                                                                    {{ $salesperson->firstname.' '.$salesperson->lastname }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>
                                                                            <input type="checkbox" class="flat-red" @if(@$model->other_info->online_signature == 1) checked @endif name="otherinfo[online_signature]">
                                                                            <span> {{ __('Online Signature') }}</span>
                                                                        </label>
                                                                        <label>
                                                                            <input type="checkbox" class="flat-red" @if(@$model->other_info->online_payment == 1) checked @endif name="otherinfo[online_payment]">
                                                                            <span>{{ __('Online Payment') }}</span>
                                                                        </label>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>{{ __('Custom Reference') }}</label>
                                                                        <input class="form-control" type="text" value="{{ @$model->other_info->customer_reference }}"name="otherinfo[custom_reference]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>{{ __('Sales Team') }}</label>
                                                                        <select class="form-control" name="otherinfo[sales_team]">
                                                                            <option value="">---{{ __('Select a sales team') }}---</option>
                                                                            @foreach( $salesteams as $salesteam )
                                                                                <option value="{{ $salesteam->id }}" @if(@$model->other_info->sales_team_id == $salesteam->id) selected="selected" @endif>{{ $salesteam->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>{{ __('Tags') }}</label>
                                                                        <select class="form-control seelct2" name="otherinfo[tags][]"  multiple="" data-tags="true" style="width:100%">
                                                                            <option value="">---{{ __('Select a Tag') }}---</option>
                                                                            @foreach($contact_tags as $value)
                                                                                <option value="{{ $value->id }}"
                                                                                    @if(isset($model->other_info->tags))
                                                                                        @foreach($model->other_info->tags as $o_tag)
                                                                                            @if($o_tag->tag_id == $value->id)
                                                                                                selected="selected"
                                                                                            @endif
                                                                                        @endforeach
                                                                                    @endif
                                                                                >{{ $value->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>{{ __('Delivery Date Reference') }}</label>
                                                                        <input class="form-control" type="date" value="@if(isset($model->other_info->delivery_date)){{ trim(date('Y-m-d', strtotime($model->other_info->delivery_date))) }}@endif" name="otherinfo[delivery_date_reference]">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- text template-->
                                            <div id="text-template" class="tab-pane fade">
                                                <div class="box mt-2">
                                                    <div class="box-header">
                                                        <h3 class="box-title">{{ __('Sale Quotations') }}</h3>
                                                        <div class="pull-right box-tools">
                                                            <button type="button" class="btn btn-default btn-sm"
                                                                data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                                                <i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body pad">
                                                        <textarea class="textarea" name="text_template[sale_quotation]" placeholder="{{ __('Place some text here') }}"
                                                            style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                                                            value="@if(isset($model->text_templates)) @foreach($model->text_templates as $template) @if($template->type == 0) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif ">@if(isset($model->text_templates)) @foreach($model->text_templates as $template) @if($template->type == 0) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif</textarea>
                                                    </div>
                                                </div>
                                                <div class="box mt-2">
                                                    <div class="box-header">
                                                        <h3 class="box-title">{{ __('Sale Confirmation') }}</h3>
                                                        <div class="pull-right box-tools">
                                                            <button type="button" class="btn btn-default btn-sm"
                                                                data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                                                <i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body pad">
                                                        <textarea class="textarea"  name="text_template[sale_confirmation]" placeholder="{{ __('Place some text here') }}"
                                                            style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" value="@if(isset($model->text_templates)) @foreach($model->text_templates as $template) @if($template->type == 1) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif">@if(isset($model->text_templates)) @foreach($model->text_templates as $template) @if($template->type == 1) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif</textarea>
                                                    </div>
                                                </div>
                                                <div class="box mt-2">
                                                    <div class="box-header">
                                                        <h3 class="box-title">{{ __('Performa Invoice') }}</h3>
                                                        <div class="pull-right box-tools">
                                                            <button type="button" class="btn btn-default btn-sm"
                                                                data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                                                <i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body pad">
                                                        <textarea class="textarea"  name="text_template[performa_invoice]" placeholder="{{ __('Place some text here') }}"
                                                            style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                                                            value="@if(isset($model->text_templates))@foreach($model->text_templates as $template)@if($template->type == 2){{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }}@endif @endforeach @endif">
                                                            @if(isset($model->text_templates))@foreach($model->text_templates as $template)@if($template->type == 2){{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }}@endif @endforeach @endif
                                                        </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!-- Bottom- section -->
        @if(@$action == "Edit")
        @canany(['Add Note','View Log Note','Add Schedule Activity','View Schedule Activity','Send Message','View Send Messages'])
          <section class="bottom-section">
            <div class="row box">
              <div class="row activity-back-color">
                <div class="col-md-12">
                    <div class="custom-tabs mt-3 mb-2">
                      <div class="row">
                        <div class="col-md-8">
                          @canany(['View Send Messages','Send Message','View Log Note','Add Note','View Schedule Activity','Add Schedule Activity'])
                          <ul class="nav nav-tabs">
                            @canany(['View Send Messages','Send Message'])
                            <li class="active"><a data-toggle="tab" href="#send_message">{{ __('Send Message') }}</a></li>
                            @endcanany
                            @canany(['View Log Note','Add Note'])
                            <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message')) class="active" @endif><a data-toggle="tab" href="#log_note">{{ __('Log Note') }}</a></li>
                            @endcanany
                            @canany(['View Schedule Activity','Add Schedule Activity'])
                            <li @if(!auth()->user()->can('View Send Messages') && !auth()->user()->can('Send Message') && !auth()->user()->can('View Log Note') && !auth()->user()->can('Add Note')) class="active" @endif><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                            @endcanany
                        </ul>
                          @endcanany
                        </div>
                        <div class="col-md-4 pull-right text-right follower-icons">
                          <!-- Attachments View -->
                          {!! $attachments_partial_view !!}
                          @if($is_following == 1 )
                             <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="2" id="following"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                             <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="2" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                          @else
                              <a class="followButton" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="2"id="followBtn" >{{ __('Follow') }}</a>
                                <a class="followButton following" data-model-id = "{{ Hashids::encode($model->id) }}" data-partner-id ="{{ Hashids::encode(Auth::user()->id) }}" data-module-type="2" id="followingBtn" style="display: none"><i class="fa fa-check"></i>&nbsp;{{ __('Following') }}</a>
                          @endif
                          <a class="dropdown-toggle" href="javascript:void(0)" title="Show Followers"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;<span id="follower_counter">{{ $followers->count() }} </span></a>
                          <!-- Follower List -->
                          <ul class="follower_list" id="f_list">
                            @forelse ($followers as $follower)
                              <li><a href="{{ route('admin.contacts.edit',['contact'=> Hashids::encode($follower->contacts->id)]) }}" target="_blank">{{ $follower->contacts->name }}</a></li>
                             @empty
                             <li><div class="text-center">{{ __("Currently there's no follower") }}</div></li>
                            @endforelse
                          </ul>
                        </div>
                      </div>
                        <div class="tab-content custom-tabs-style custom-tabs-pd-set">
                          <!--  Send Messages -->
                          @canany(['Send Message','View Send Messages'])
                          <div id="send_message" class="tab-pane fade active in">
                            <div class="row tab-form pt-3">
                              <div class="row">
                                <div class="col-md-3">
                                  @can('Send Message')
                                  <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp; {{ __('Send Message') }}</a>
                                  {!! $send_messages_view !!}
                                  @endcan
                                </div>
                              </div>
                              @can('View Send Messages')
                              {!! $send_message_tab_partial_view !!}
                              @endcan
                            </div>
                          </div>
                          @endcanany
                          <!-- Log Note -->
                          @canany(['Add Note','View Log Note'])
                          <div id="log_note" class="tab-pane fade">
                            <div class="row tab-form pt-3">
                              <div class="row">
                                <div class="col-md-3">
                                  @can('Add Note')
                                  <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp;{{ __('Add Note') }}</a>
                                  {!! $log_notes_view !!}
                                  @endcan
                                </div>
                              </div>
                              @can('View Log Note')
                              {!! $notes_tab_partial_view !!}
                              @endcan
                            </div>
                          </div>
                          @endcanany
                          <!-- Schedule Activity -->
                          @canany(['Add Schedule Activity','View Schedule Activity'])
                         <div id="schedual_activity" class="tab-pane fade">
                            <div class="row tab-form pt-3">
                              <div class="row">
                                <div class="col-md-3">
                                  @can('Add Schedule Activity')
                                  <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp;{{ __('Add Schedule Activity') }}</a>
                                  {!! $schedual_activities_view !!}
                                  @endcan
                                </div>
                              </div>
                              @can('View Schedule Activity')
                              {!! $schedual_activity_tab_partial_view !!}
                              @endcan
                            </div>
                          </div>
                          @endcanany
                        </div>
                    </div>
                 </div>
              </div>
            </div>
          </section>
          @endcanany
        @endif
    </div>
    <!-- Send By Email popup message send model -->
    @include('admin.sales.quotation.modal-box.send-email')

    <!-- Send Pro forma invoice popup -->
    @include('admin.sales.quotation.modal-box.send-proforma-email')

    <!-- Create Invoice popup -->
    @include('admin.sales.quotation.modal-box.create-invoice')

    <!-- View Order Line popup -->
    @include('admin.sales.quotation.modal-box.view-order-line')

    <div class="order-line-modalbox-d modal fade" id="order-line-modal"></div>

@endsection

@section('scripts')
    <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
    <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        var row_data;
        var currency_code = "{{@$model->currency}}";
        currency_code = currency_code == '' ? 'EUR' : currency_code;
        var currency_symbol = "{{@$model->currency_symbol}}";
        currency_symbol = currency_symbol == '' ? '€' : currency_symbol;
        var selected_pricelist_id = '';
        var vat_label = $('input[name=vat_label]').val();
        $('.vat_label_text').html('VAT');
        $('select[name="otherinfo[tags][]"]').select2();
        $('.send-email-recepient').select2();
        $('select[name=order_line_taxes]').select2();
        $('select[name=order_line_product]').select2();
        $('textarea[name="send_email[email_body]"').summernote({
            tabsize: 2,
            height: 250
        });
        $('body').on('input', '[name=vat_percentage]', function(){
            $("#product_order_line_table tr input").change();
        });
        /** Update the Prices based on the pricelist selected **/
        $('body').on('click', '#update-prices-btn', async function(){
            if($('[name=customer_id]').val() == null || $('[name=customer_id]').val() == ''){
                toastr.error('Select the customer first');
                return false;
            }
            pricelist_id = $('select[name=pricelist_id]').val();                    // id of the pricelist selected
            quotation_order_lines = $('input[name=quotation_order_lines]').val().split(',');    // array of ids of order lines added

            var data = {
                'pricelist_id': pricelist_id,
                'orderlineids': quotation_order_lines,
                'customer_id': $('[name=customer_id]').val()
            };
            let orderlines = await prepare_ajax_request('{{ route("admin.quotation.update.prices") }}', data);
            $.each( orderlines, function( key, value ) {
                $("#product_order_line_table tr[data-order-list-id="+value.id+"] input[name=order_line_unit_price]").val(value.unit_price.toFixed(2));
                $("#product_order_line_table tr[data-order-list-id="+value.id+"] input[name=order_line_unit_price]").change();
            });
        });
        /**  Form Validations  **/
        $('#quotation-form').validate({
            ignore: [],
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            rules: {
                "customer_id":{
                    required:true
                },
                "expires_at":{
                    required:true
                },
                "payment_terms":{
                    required:true
                },
                // "otherinfo[sales_person]":{
                //     required:true
                // }
            },
            messages: {
                "customer_id":{
                    required: "{{ __('Select a customer') }}"
                },
                "expires_at":{
                    required: "{{ __('Mention the expiry date') }}"
                },
                "payment_terms":{
                    required: "{{ __('Select a payment term') }}"
                },
                "otherinfo[sales_person]":{
                    required: "{{ __('Select the sales person') }}"
                }
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
                toastr.error(error);
                if($('.form-main-error:visible').length == 0)
                {
                    $('.form-save-btn-div').append('<small class="form-main-error">"{{ __('Some of the form fields are required') }}"</small>');
                    setTimeout(function(){
                        $('.form-main-error:visible').css('display','none');
                    },4000);
                }
            },

        });
        $('body').on('click', '#product_order_line_table tbody tr.order_lnes', function() {

            $("#view-order-line-modal #product").html( $(this).find('.product_name').html() );
            $("#view-order-line-modal #qty").html( $(this).find('.product_qty').html() );
            $("#view-order-line-modal #invoiced").html( '0.00' );
            $("#view-order-line-modal #unit_price").html( $(this).find('.product_unit_price').html() );
            $("#view-order-line-modal #cost").html( '0' );
            $("#view-order-line-modal #o_taxes").html( $(this).find('.product_taxes').html() );
            $("#view-order-line-modal #o_description").html( $(this).find('.product_description').html() );
            $("#view-order-line-modal #o_total").html( $(this).find('.product_total').html() );
                // if($(this).find('.licenses').val() != ''){
                //     $("#view-order-line-modal #o_licenses").html( $(this).find('.licenses').val() );
                // }else{
                //     $("#view-order-line-modal #o_licenses").html( 'No License Attached' );
                // }
                if($(this).find('.vouchers').val() != ''){
                    $("#view-order-line-modal #o_licenses").html( $(this).find('.vouchers').val() );
                }else{
                    $("#view-order-line-modal #o_licenses").html( 'No Voucher Attached' );
                }
            $("#view-order-line-modal").modal('show');
        });
        // Clicking Save btn
        $('body').on('click', '.save-quotation-d', function() {
            $('#quotation-form').submit();
        });
        $('body').on('click', '.change-status-btn', function() {
            status = $(this).attr('data-status');
            $('input[name=quotation_status]').val(status);
            $('#quotation-form').submit();
        });

        /**   Customer change address change Start   **/
        $('body').on('change', 'select[name=customer_id]', function(){
            customer_id = $(this).val();
            url = '{{ route("admin.quotation.contact.addresses", [":id",1]) }}';
            url = url.replace(':id', customer_id);
            @if(@$sales_settings['orders_customer_address'] == 1)
            $.ajax({
                url: url,
                type: 'POST',
                success: function (data) {
                    option = '<option value="">{{ __('Choose Address') }}</option>';
                    if(data['contact_addresses'].length == 0){
                        option = '<option value="">{{ __('Choose Address') }}</option>';
                    }
                    $("select[name=invoice_address]")
                    .find('option')
                    .remove()
                    .end()
                    .append(option)
                    $.each( data['contact_addresses'], function( key, value ) {
                        if( key == 0){
                            $('select[name="invoice_address"]').append('<option value="'+value.id+'" data-vat_label="'+value.vat_label+'" data-default_vat_percentage="'+value.default_vat_percentage+'" data-is_default_vat="'+value.is_default_vat+'" data-vat_percentage="'+value.vat_in_percentage+'">'+value.contact_name+"  , "+value.street_1+", "+value.city+", "+value.country_name+'</option') ;
                        }else{
                            $('select[name="invoice_address"]').append('<option value="'+value.id+'" data-vat_label="'+value.vat_label+'" data-default_vat_percentage="'+value.default_vat_percentage+'" data-is_default_vat="'+value.is_default_vat+'" data-vat_percentage="'+value.vat_in_percentage+'">'+value.contact_name+"  , "+value.street_1+", "+value.city+", "+value.country_name+'</option') ;
                        }
                    });
                    if( data['contact'].is_default_vat == 1){
                        $('[name=vat_percentage]').val(data['contact'].default_vat_percentage);
                        $('input[name=vat_label]').val('VAT');
                        $('.vat_label_text').html('VAT');
                        vat_label = 'VAT';
                    }else{
                        $('[name=vat_percentage]').val(data['contact'].vat_in_percentage);
                        $('input[name=vat_label]').val(data['contact'].vat_label);
                        $('.vat_label_text').html(data['contact'].vat_label);
                        vat_label = data['contact'].vat_label;
                    }
                    $('[name=vat_percentage]').trigger('input');
                    $('#update-prices-btn').trigger('click');
                },
                complete:function(data){
                    // Hide loader container
                }
            });
            url = '{{ route("admin.quotation.contact.addresses", [":id",2]) }}';
            url = url.replace(':id', customer_id);
            $.ajax({
                url: url,
                type: 'POST',
                success: function (data) {
                    option = '<option value="">{{ __('Choose Address') }}</option>';
                    if(data['contact_addresses'].length == 0){
                        option = '<option value="">{{ __('Choose Address') }}</option>';
                    }
                    $("select[name=delivery_address]")
                    .find('option')
                    .remove()
                    .end()
                    .append(option)
                    $.each( data['contact_addresses'], function( key, value ) {
                        if( key == 0){
                            $('select[name="delivery_address"]').append(new Option(value.contact_name+"  , "+value.street_1+", "+value.city+", "+value.country_name,value.id,true,true));
                        }else{
                            $('select[name="delivery_address"]').append(new Option(value.contact_name+"  , "+value.street_1+", "+value.city+", "+value.country_name,value.id));
                        }
                    });
                    if(data['sales_info'] != null)
                    {
                        $('select[name=pricelist_id]').val(data['sales_info'].pricelist_id);
                        $('select[name=payment_terms]').val(data['sales_info'].payment_terms);
                    }
                },
                complete:function(data){
                    // Hide loader container
                }
            })
            @endif
        });
        $('body').on('change', 'select[name="customer_id"]', function() {
            $('[name=vat_percentage]').val($('option:selected', this).data('vat_percentage'));
            $('[name=vat_percentage]').trigger('input');
        });
        /***   Customer change address change End   */
        /**   Add a Product Open Modal and Saving Functions Start   **/
        $('body').on('click', '.order-line-option-d', async function() {
            var data = {
                'id': $(this).data('action_id')
            };
            let order_line_modal = await prepare_ajax_request('{{ route("admin.order-line-option")}}', data, 'get');
            $('.order-line-modalbox-d').html(order_line_modal.html);
            $('#order-line-modal').modal('show');
            $('#Taxes').select2();
            $('.vat_percentage').html($('[name=vat_percentage]').val());
            $('.vat_label_text').html($('[name=vat_label]').val());
        });

        // On selecting the Product the unit price will be updated
        $('body').on('change','#productSelect', async function(){
            taxes = $('option:selected', this).attr('data-taxes');
            taxes = JSON.parse(taxes);
            data = {
                'tax_ids': taxes
            }
            let tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);

            setTimeout(() => {

            }, 1000);
            unit_price = $('option:selected', this).attr('data-price');
            product_name = $('option:selected', this).attr('data-name');
            qty = $('input[name=qty]').val();
            sub_total = qty*unit_price;
            total_price = sub_total;
            $.each( tax_details, function( key, value ) {
                switch (value.computation){
                    case 0:
                        total_price += value.amount;
                        break;
                    case 1:
                        total_price += sub_total * value.amount / 100;
                        break;
                }
            });
            vat_percentage = $('[name=vat_percentage]').val();
            total_price += sub_total * vat_percentage / 100;

            $("#Taxes").val(taxes);

            // $('select[name=taxes]').val();

            $('input[name=unit-price]').val(parseFloat(unit_price).toFixed(2));
            $('input[name=subtotal]').val(parseFloat(total_price).toFixed(2));
            $('input[name=description]').val(product_name);
            $("#Taxes").trigger('change');
        });

        // On changing the quantity adjust the sub total
        $('body').on('change','input[name=qty]', async function(){
            taxes = $("#Taxes").val();
            let tax_details = [];
            if(taxes.length>0){
                data = {
                    'tax_ids': taxes
                }
                tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);
            }
            setTimeout(() => {

            }, 1000);
            unit_price = $('input[name=unit-price]').val();
            qty = $(this).val();
            sub_total = qty*unit_price;
            total_price = sub_total;
            $.each( tax_details, function( key, value ) {
                switch (value.computation){
                    case 0:
                        total_price += value.amount;
                        break;
                    case 1:
                        total_price += sub_total * value.amount / 100;
                        break;
                }
            });
            vat_percentage = $('[name=vat_percentage]').val();
            total_price += sub_total * vat_percentage / 100;
            $('input[name=subtotal]').val(parseFloat(total_price).toFixed(2));
        });

        // On changing the unit price adjust the sub total
        $('body').on('change','input[name=unit-price]', async function(){
            taxes = $("#Taxes").val();
            let tax_details = [];
            if(taxes.length>0){
                data = {
                    'tax_ids': taxes
                }
                tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);
            }
            setTimeout(() => {

            }, 1000);
            qty = $('input[name=qty]').val();
            unit_price = $(this).val();
            sub_total = qty*unit_price;
            total_price = sub_total;
            $.each( tax_details, function( key, value ) {
                switch (value.computation){
                    case 0:
                        total_price += value.amount;
                        break;
                    case 1:
                        total_price += sub_total * value.amount / 100;
                        break;
                }
            });
            vat_percentage = $('[name=vat_percentage]').val();
            total_price += sub_total * vat_percentage / 100;

            $('input[name=subtotal]').val(parseFloat(total_price).toFixed(2));
        });

        // On changing the unit price adjust the sub total
        $('body').on('change','#Taxes', async function(){
            taxes = $("#Taxes").val();
            let tax_details = [];
            if(taxes.length>0){
                data = {
                    'tax_ids': taxes
                }
                tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);
            }
            setTimeout(() => {

            }, 1000);
            qty = $('input[name=qty]').val();
            unit_price = $('input[name=unit-price]').val();
            sub_total = qty*unit_price;
            total_price = sub_total;
            $.each( tax_details, function( key, value ) {
                switch (value.computation){
                    case 0:
                        total_price += value.amount;
                        break;
                    case 1:
                        total_price += sub_total * value.amount / 100;
                        break;
                }
            });
            vat_percentage = $('[name=vat_percentage]').val();
            total_price += sub_total * vat_percentage / 100;

            $('input[name=subtotal]').val(total_price.toFixed(2));
        });

        // On saving the product using modal
        $('body').on('click','.save-product-submit',function(){
            
            data = [];
            data['product_name'] = $('option:selected','#productSelect').attr('data-name');
            data['variation_id'] = $('option:selected','#productSelect').attr('data-variation-id');
            data['product_id'] = $('option:selected','#productSelect').val();
            data['description'] = $('input[name=description]').val();
            data['qty'] = $('input[name=qty]').val();
            data['lead-time'] = $('input[name=lead-time]').val();
            data['unit-price'] = $('input[name=unit-price]').val();
            tax_html  ='';
            $('select[name=taxes]').children("option:selected").each(function(index,value){
                if(index ==0 ){
                    tax_html = $(value).text();
                }else{
                    tax_html += ', '+$(value).text();
                }
            });
            data['taxes_name'] = tax_html;
            data['taxes'] = $('select[name=taxes]').val();
            data['subtotal'] = $('input[name=subtotal]').val();
            data['notes'] = null;
            data['section'] = null;

            row_data = data;
            if(data['qty'] > 0 && data['product_name'] != '' && data['product_id'] != '' && data['unit-price'] > -1 ){
                save_quotation_order_line_product(data);
            }else{
                if(data['qty'] < 1 ){toastr.error('{{__("Quantity must be greater than 0")}}');}
                if(data['product_id'] == '' ){toastr.error('{{__("Select Product")}}');}
                if(data['unit-price'] < 0 ){toastr.error('{{__("Price must be positive")}}');}
            }
            


        });

        /***   Add a Product Open Modal and Saving Functions End    */

        /**   Add a Section Open Modal and Saving Functions Start **/
        $('body').on('click', '.btn-success.section-save', function(){
            data = [];
            data['product_name'] = null;
            data['variation_id'] = null;
            data['product_id'] = null;
            data['description'] = null;
            data['qty'] = null;
            data['lead-time'] = null;
            data['unit-price'] = null;
            data['taxes'] = [];
            data['subtotal'] = null;
            data['notes'] = null;
            data['section'] = $('input[name=section]').val();
            if($('input[name=section]').val() == ''){
                $('#section-error').text('Section is required');
                setTimeout(() => {
                    $('.invalid-feedback').text('');
                }, 5000);
                return false;
            }else{
                row_data = data;
                save_quotation_order_line_product(data);

            }
        });

        /***   Add a Section Open Modal and Saving Functions End    */

        /**   Add Notes Open Modal and Saving Functions Start  **/
        $('body').on('click', '.btn-success.notes-save', function(){
            data = [];
            data['product_name'] = null;
            data['variation_id'] = null;
            data['product_id'] = null;
            data['description'] = null;
            data['qty'] = null;
            data['lead-time'] = null;
            data['unit-price'] = null;
            data['taxes'] = [];
            data['subtotal'] = null;
            data['section'] = null;
            data['notes'] = $('input[name=notes]').val();
            if($('input[name=notes]').val() == ''){
                $('#note-error').text('Note is required');
                setTimeout(() => {
                    $('.invalid-feedback').text('');
                }, 5000);
                return false;
            }else{
                row_data = data;
                save_quotation_order_line_product(data);

            }
        });
        /***   Add a Notes Open Modal and Saving Functions End     */


        function save_quotation_order_line_product(data)
        {
            $('#ajax_loader').show();

            order_line_ids = $('input[name=quotation_order_lines]').val().split(',');
            $.ajax({
                url: "{{ route('admin.save.order-line-option') }}",
                data: {
                    product_id : data['product_id'],
                    variation_id : data['variation_id'],
                    description : data['description'],
                    qty : data['qty'],
                    lead_time : data['lead-time'],
                    unit_price : data['unit-price'],
                    taxes : data['taxes'],
                    section : data['section'],
                    notes : data['notes'],
                    order_line_ids: order_line_ids,
                    vat_percentage: $('[name=vat_percentage]').val(),
                    _token : $('input[name=_token]').val(),
                },
                type: 'POST',
                success: function (data) {
                    if( $('input[name=quotation_order_lines]').val() == null || $('input[name=quotation_order_lines]').val() == '' ){
                        $('input[name=quotation_order_lines]').val(data['order_line_id']);
                    }else{
                        $('input[name=quotation_order_lines]').val($('input[name=quotation_order_lines]').val()+','+data['order_line_id']);
                    }
                    $('#order-line-modal').modal('hide');
                    $('#add-section-model').modal('hide');
                    $('#add-note-model').modal('hide');

                    $("#taxed_amount").html(currency_symbol+data['total_tax']+' '+currency_code);
                    $("#untaxed_amount").html(currency_symbol+data['untaxed_total']+' '+currency_code);
                    $("#total_amount").html((currency_symbol+data['total'])+' '+currency_code);

                    row_data['order-line-id'] = data['order_line_id'];
                    make_order_line_row_product();
                    toastr.success("{{ __('Order line added successfully') }}");
                },
                complete:function(data){
                    $('#ajax_loader').hide();

                }
            })
        }
        function update_quotation_order_line_product(data)
        {
            order_line_ids = $('input[name=quotation_order_lines]').val().split(',');
            $('#ajax_loader').show();

            $.ajax({
                url: "{{ route('admin.update.order-line-option') }}",
                data: {
                    order_line_id : data['order_line_id'],
                    product_id : data['product_id'],
                    variation_id : data['variation_id'],
                    description : data['description'],
                    qty : data['qty'],
                    delivered_qty : data['delivered_qty'],
                    invoiced_qty : data['invoiced_qty'],
                    unit_price : data['unit-price'],
                    taxes : data['taxes'],
                    section : data['section'],
                    notes : data['notes'],
                    order_line_ids: order_line_ids,
                    vat_percentage: $('[name=vat_percentage]').val(),
                    _token : $('input[name=_token]').val(),
                },
                type: 'POST',
                success: function (data) {

                    $("#taxed_amount").html(currency_symbol+data['total_tax']+' '+currency_code);
                    $("#untaxed_amount").html(currency_symbol+data['untaxed_total']+' '+currency_code);
                    $("#total_amount").html(currency_symbol+(data['total'])+' '+currency_code);

                    // row_data['order-line-id'] = data['order_line_id'];
                },
                complete:function(data){
                    // Hide loader container
                    $('#ajax_loader').hide();

                }
            })
        }
        function make_order_line_row_product()
        {
            taxes_array = @json($customer_taxes);
            products = @json($products);
            html = "<tr data-order-list-id='"+row_data['order-line-id']+"' class='order_line_row'>";
                if(row_data['section'] != null)
                {
                    html += '<td colspan="9">';
                        html += '<input type="text" name="order_line_section" class="form-control" value="'+row_data['section']+'" />';
                        html += '<i class="fa fa-trash delete-order-line" aria-hidden="true" style="float:right;cursor: pointer;"></i>';
                    html += '</td>';
                }
                if(row_data['notes'] != null)
                {
                    html += '<td colspan="9">';
                        html += '<input type="text" name="order_line_notes" class="form-control" value="'+row_data['notes']+'" />';
                        html += '<i class="fa fa-trash delete-order-line" aria-hidden="true" style="float:right;cursor: pointer;"></i>';
                    html += '</td>';
                }
                if(row_data['product_name'] != null)
                {
                    product_html = '<select class="form-control" name="order_line_product">';
                        $.each( products, function( key, value ) {
                            product_html += '<option ';
                                product_html += 'data-variation-id="'+value.variation_id+'" ';
                                product_html += 'data-taxes="'+value.taxes+'" ';
                                product_html += 'data-name="'+value.name+'" ';
                                product_html += 'value="'+value.product_id+'" ';
                                product_html += 'data-price="'+value.price+'" ';

                                if(row_data['variation_id'] != null && row_data['variation_id'] != ''){
                                    if(row_data['product_id'] == value.product_id
                                        && row_data['variation_id'] == value.variation_id ){
                                            product_html += ' selected="selected"';
                                        }
                                }else{
                                    if(row_data['product_id'] == value.product_id){
                                            product_html += ' selected="selected"';
                                    }

                                }
                                product_html += '>'+value.name;
                                product_html += '</option>';
                        });
                    product_html += '</select>';


                    // html += '<td>'+row_data['product_name']+'</td>';
                    html += '<td>'+product_html+'</td>';
                    html += '<td><input type="text" name="order_line_description" class="form-control" value="'+row_data['description']+'"/></td>';
                    html += '<td><input type="number" name="order_line_qty" class="form-control" value="'+row_data['qty']+'"/></td>';
                    @if(@$model->status == 1 || @$model->status == 2)
                    html += '<td><input class="form-control" type="number" min="0" max="'+row_data['qty']+'"  name="order_line_delivered_qty" value="0" id=""></td>';
                    html += '<td><input class="form-control" type="number" name="order_line_inviced_qty" value="0" id="" readonly></td>';
                    @endif
                    html += '<td>'+row_data['lead-time']+'</td>';
                    html += '<td class="d-flex"><span style="font-size: 15px;padding: 5px 5px 0 0px;">'+currency_symbol+'</span> <input type="text" name="order_line_unit_price" class="form-control" value="'+parseFloat(row_data['unit-price']).toFixed(2)+'"/><span  style="font-size: 15px;padding: 5px 0px 0 5px;"> '+currency_code+'</span></td>';
                    taxes = '<div><select multiple="" class="form-control" data-placeholder="Select Tax"  name="order_line_taxes">';
                        $.each( taxes_array, function( key, value ) {
                            taxes += '<option value="'+value.id+'"';
                            if(row_data['taxes'].includes(value.id.toString())){
                                taxes += 'selected="selected"';
                            }
                            taxes +='>'+value.name+'</option>';
                        });
                    taxes += '</select></div>';
                    taxes += '<div class="d-flex"><span class="tagged d-flex ml-2"><span>';
                    taxes += $('[name=vat_percentage]').val() +'</span>% '+vat_label;
                    taxes += '</span></div>';

                    // html += '<td>'+row_data['taxes_name']+'</td>';
                    html += '<td class="vt_list"><div class="d-flex">'+taxes+'</div></td>';
                    html += '<td class="d-flex"><span style="font-size: 15px;padding: 5px 5px 0 0px;">'+currency_symbol+'</span><input type="text" name="order_line_total" readonly class="form-control" value="'+parseFloat(row_data['subtotal']).toFixed(2)+' '+currency_code+'"/><i class="fa fa-trash delete-order-line" aria-hidden="true" style="float:right;cursor: pointer;"></i></td>';
                }
            html += "</tr>";
            $('#product_order_line_table tbody').append(html);
            $('select[name=order_line_taxes]').select2();
            $('select[name=order_line_product]').select2();
            return 'true';

        }

        $('body').on('click','.delete-order-line', function(){
            order_line_id = $(this).parents('tr').attr('data-order-list-id');
            order_line_ids = $('input[name=quotation_order_lines]').val().split(',');
            order_line_row = $(this).parents('tr');
            url = '{{ route("admin.quotation.orderline.delete", [":id",1]) }}';
            url = url.replace(':id', order_line_id);
            $('#ajax_loader').show();

            $.ajax({
                url: url,
                data:{
                    order_line_ids: order_line_ids,
                },
                type: 'POST',
                success: function (data) {
                    if(data['status'] == 'true'){
                        $('input[name=quotation_order_lines]').val( removeValue($('input[name=quotation_order_lines]').val(), order_line_id, ',') );
                        order_line_row.hide();
                        $("#taxed_amount").html(currency_symbol+data['total_tax']+' '+currency_code);
                        $("#untaxed_amount").html(currency_symbol+data['untaxed_total']+' '+currency_code);
                        $("#total_amount").html(currency_symbol+(data['total'])+' '+currency_code);
                        toastr.success("{{ __('Order line removed successfully.') }}");
                    }else{
                        toastr.error(data['message']);
                    }

                },
                complete:function(data){
                    $('#ajax_loader').hide();

                    // Hide loader container
                }
            })

        });

        function removeValue(list, value, separator) {
            separator = separator || ",";
            var values = list.split(separator);
            for(var i = 0 ; i < values.length ; i++) {
                if(values[i] == value) {
                values.splice(i, 1);
                return values.join(separator);
                }
            }
            return list;
        }
        /***    Order Line Functions End    */

        /**    Optional Products Functions Start **/
        $('body').on('click', '.optional-product-option-d', async function() {
            var data = {
                'action': "action",
                'type': "optional_product",
                'id': "data_action_id"
            };
            let order_line_modal = await prepare_ajax_request(ADMIN_URL + '/sales-management/quotations/order-line-option', data, 'get');
            $('.order-line-modalbox-d').html(order_line_modal.html);
            $('#order-line-modal').modal('show');
            $('#Taxes').select2();


        });

        // On saving the optional product product using modal
        $('body').on('click','.save-optional-product-submit',function(){
            data = [];
            data['product_name'] = $('option:selected','#productSelect').attr('data-name');
            data['variation_id'] = $('option:selected','#productSelect').attr('data-variation-id');
            data['product_id'] = $('option:selected','#productSelect').val();
            data['description'] = $('input[name=description]').val();
            data['qty'] = $('input[name=qty]').val();
            data['unit-price'] = $('input[name=unit-price]').val();

            row_data = data;
            save_quotation_optional_product(data);

        });
        $('body').on('click','.delete-order-line-optional', function(){
            optional_order_line_id = $(this).parents('tr').attr('data-order-list-id');
            optional_order_line_row = $(this).parents('tr');
            url = '{{ route("admin.quotation.optionalorderline.delete", [":id"]) }}';
            url = url.replace(':id', optional_order_line_id);
            $.ajax({
                url: url,
                type: 'POST',
                success: function (data) {
                    if(data['status'] == 'true'){
                        $('input[name=optional_products]').val( removeValue($('input[name=optional_products]').val(), optional_order_line_id, ',') );
                        optional_order_line_row.hide();
                        toastr.success("{{ __('Order line removed successfully.') }}");
                    }else{
                        toastr.error(data['message']);
                    }

                },
                complete:function(data){
                    // Hide loader container
                }
            })

        });
        function save_quotation_optional_product(data)
        {
            $.ajax({
                url: "{{ route('admin.save.quotation.optional-products') }}",
                data: {
                    product_id : data['product_id'],
                    variation_id : data['variation_id'],
                    description : data['description'],
                    qty : data['qty'],
                    unit_price : data['unit-price'],
                    _token : $('input[name=_token]').val(),
                },
                type: 'POST',
                success: function (data) {
                    if( $('input[name=optional_products]').val() == null || $('input[name=optional_products]').val() == '' ){
                        $('input[name=optional_products]').val(data);
                    }else{
                        $('input[name=optional_products]').val($('input[name=optional_products]').val()+','+data);
                    }

                    row_data['order-line-id'] = data;
                    make_order_line_row_optional_product();
                    $('#order-line-modal').modal('hide');
                },
                complete:function(data){
                    // Hide loader container
                }
            })
        }
        function make_order_line_row_optional_product()
        {
            html = "<tr data-order-list-id='"+row_data['order-line-id']+"'>";
                    html += '<td>'+row_data['product_name']+'</td>';
                    html += '<td>'+row_data['description']+'</td>';
                    html += '<td>'+row_data['qty']+'</td>';
                    html += '<td>'+parseFloat(row_data['unit-price']).toFixed(2)+'<i class="fa fa-trash delete-order-line-optional" aria-hidden="true" style="float:right;cursor: pointer;"></i></td>';
            html += "</tr>";
            $('#optional_products_table tbody').append(html);
            return 'true';

        }
        /***    Optional Products Functions End    */

        /**  Send Email Start **/
        $('body').on('click','.send-email-btn-submit',function(){
            $('#send-by-email form').submit();
        });
        $('body').on('click','.send-proforma-email-btn-submit',function(){
            $('#send-proforma-invoice form').submit();
        });
        @isset($model)
            $('body').on('change', '#send-proforma-invoice .select-email-template', function() {
                url = '{{ route("admin.email.tempate.detail.ajax", [":id"]) }}';
                url = url.replace(':id', $(this).val());
                $.ajax({
                    url: url,
                    type: 'POST',
                    success: function (data) {
                        email_body = data.header+data.content+data.footer;

                        var quotnumber = ":quotationnumber",
                        quotnumber = new RegExp(quotnumber, "g");
                        email_body = email_body.replace(quotnumber, 'S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}');

                        var quottotal = ":quotationtotal",
                        quottotal = new RegExp(quottotal, "g");
                        email_body = email_body.replace(quottotal, $('#total_amount').html());

                        $('#send-proforma-invoice textarea[name="send_email[email_body]"]').summernote('reset')
                        $('#send-proforma-invoice textarea[name="send_email[email_body]"]').summernote('editor.pasteHTML', email_body)
                        $('#send-proforma-invoice textarea[name="send_email[email_body]"]').val(email_body);
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                });
            });
            $('body').on('change', '#send-by-email .select-email-template', function() {
                url = '{{ route("admin.email.tempate.detail.ajax", [":id"]) }}';
                url = url.replace(':id', $(this).val());
                $.ajax({
                    url: url,
                    type: 'POST',
                    success: function (data) {
                        email_body = data.header+data.content+data.footer;

                        var quotnumber = ":quotationnumber",
                        quotnumber = new RegExp(quotnumber, "g");
                        email_body = email_body.replace(quotnumber, 'S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}');

                        var quottotal = ":quotationtotal",
                        quottotal = new RegExp(quottotal, "g");
                        email_body = email_body.replace(quottotal, $('#total_amount').html());
                        $('#send-by-email textarea[name="send_email[email_body]"]').summernote('reset')
                        $('#send-by-email textarea[name="send_email[email_body]"]').summernote('editor.pasteHTML', email_body)
                        $('#send-by-email textarea[name="send_email[email_body]"]').val(email_body);
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                });
            });
        @endisset
        // Editing the pre added order lines
        $('body').on('change', '.order_line_row input, .order_line_row select', async function(){
                $(this);
                field_name = $(this).attr('name');
                order_line_id = $(this).parents('.order_line_row').attr('data-order-list-id');
                vat_percentage = $('[name=vat_percentage]').val();
                switch(field_name){
                    case "order_line_product":
                        taxes = $('option:selected', this).attr('data-taxes');
                        taxes = JSON.parse(taxes);
                        data = {
                            'tax_ids': taxes
                        }
                        tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);

                        setTimeout(() => {

                        }, 1000);

                        unit_price = $('option:selected', this).attr('data-price');
                        product_name = $('option:selected', this).attr('data-name');
                        qty = $(this).parents('.order_line_row').find('input[name=order_line_qty]').val();
                        sub_total = qty*unit_price;
                        total_price = sub_total;

                        $.each( tax_details, function( key, value ) {
                            switch (value.computation){
                                case 0:
                                    total_price += value.amount;
                                    break;
                                case 1:
                                    total_price += sub_total * value.amount / 100;
                                    break;
                            }
                        });
                        total_price += sub_total * vat_percentage / 100;

                        $(this).parents('.order_line_row').find("select[name=order_line_taxes]").val(taxes);

                        // $('select[name=taxes]').val();

                        $(this).parents('.order_line_row').find('input[name=order_line_unit_price]').val(parseFloat(unit_price).toFixed(2));
                        $(this).parents('.order_line_row').find('input[name=order_line_total]').val(parseFloat(total_price).toFixed(2));
                        $(this).parents('.order_line_row').find('input[name=order_line_description]').val(product_name);
                        $(this).parents('.order_line_row').find("select[name=order_line_taxes]").trigger('change');
                        $(this).parents('.order_line_row').find("span.taggedselect[name=order_line_taxes]").html(vat_percentage);
                        $(this).parents('.order_line_row').find('span.tagged span').html(vat_percentage);
                        break;
                    case "order_line_qty":
                        taxes = $(this).parents('.order_line_row').find("select[name=order_line_taxes]").val();
                        tax_details = [];
                        if(taxes.length>0){
                            data = {
                                'tax_ids': taxes
                            }
                            tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);
                        }
                        setTimeout(() => {

                        }, 1000);
                        unit_price = $(this).parents('.order_line_row').find('input[name=order_line_unit_price]').val();
                        qty = $(this).val();
                        sub_total = qty*unit_price;
                        total_price = sub_total;
                        $.each( tax_details, function( key, value ) {
                            switch (value.computation){
                                case 0:
                                    total_price += value.amount;
                                    break;
                                case 1:
                                    total_price += sub_total * value.amount / 100;
                                    break;
                            }
                        });
                        total_price += sub_total * vat_percentage / 100;
                        $(this).parents('.order_line_row').find('span.tagged span').html(vat_percentage);
                        $(this).parents('.order_line_row').find('input[name=order_line_total]').val(parseFloat(total_price).toFixed(2));
                        break;
                    case "order_line_unit_price":
                        taxes = $(this).parents('.order_line_row').find("select[name=order_line_taxes]").val();
                        tax_details = [];
                        if(taxes.length>0){
                            data = {
                                'tax_ids': taxes
                            }
                            tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);
                        }
                        setTimeout(() => {

                        }, 1000);
                        qty = $(this).parents('.order_line_row').find('input[name=order_line_qty]').val();
                        unit_price = $(this).val();
                        sub_total = qty*unit_price;
                        total_price = sub_total;
                        $.each( tax_details, function( key, value ) {
                            switch (value.computation){
                                case 0:
                                    total_price += value.amount;
                                    break;
                                case 1:
                                    total_price += sub_total * value.amount / 100;
                                    break;
                            }
                        });
                        total_price += sub_total * vat_percentage / 100;
                        $(this).parents('.order_line_row').find('span.tagged span').html(vat_percentage);

                        $(this).parents('.order_line_row').find('input[name=order_line_total]').val(parseFloat(total_price).toFixed(2));
                        break;
                    case "order_line_taxes":
                        taxes = $(this).val();
                        tax_details = [];
                        if(taxes.length>0){
                            data = {
                                'tax_ids': taxes
                            }
                            tax_details = await prepare_ajax_request('{{ route("admin.taxes.details") }}', data);
                        }
                        setTimeout(() => {

                        }, 1000);
                        qty = $(this).parents('.order_line_row').find('input[name=order_line_qty]').val();
                        unit_price = $(this).parents('.order_line_row').find('input[name=order_line_unit_price]').val();
                        sub_total = qty*unit_price;
                        total_price = sub_total;
                        $.each( tax_details, function( key, value ) {
                            switch (value.computation){
                                case 0:
                                    total_price += value.amount;
                                    break;
                                case 1:
                                    total_price += sub_total * value.amount / 100;
                                    break;
                            }
                        });
                        total_price += sub_total * vat_percentage / 100;
                        $(this).parents('.order_line_row').find('span.tagged span').html(vat_percentage);

                        $(this).parents('.order_line_row').find('input[name=order_line_total]').val(parseFloat(total_price).toFixed(2));
                    break;
                }

                data = [];
                data['order_line_id'] = order_line_id;
                data['product_name'] = $(this).parents('.order_line_row').find('option:selected','select[name=order_line_product]').attr('data-name');
                data['variation_id'] = $(this).parents('.order_line_row').find('option:selected','select[name=order_line_product]').attr('data-variation-id');
                data['product_id'] = $(this).parents('.order_line_row').find('option:selected','select[name=order_line_product]').val();
                data['description'] = $(this).parents('.order_line_row').find('input[name=order_line_description]').val();
                data['qty'] = $(this).parents('.order_line_row').find('input[name=order_line_qty]').val();
                data['delivered_qty'] = $(this).parents('.order_line_row').find('input[name=order_line_delivered_qty]').val();
                data['invoiced_qty'] = $(this).parents('.order_line_row').find('input[name=order_line_inviced_qty]').val();
                data['unit-price'] = $(this).parents('.order_line_row').find('input[name=order_line_unit_price]').val();
                tax_html  ='';

                $(this).parents('.order_line_row').find('select[name=order_line_taxes]').children("option:selected").each(function(index,value){
                    if(index ==0 ){
                        tax_html = $(value).text();
                    }else{
                        tax_html += ', '+$(value).text();
                    }
                });
                data['taxes_name'] = tax_html;
                data['taxes'] = $(this).parents('.order_line_row').find('select[name=order_line_taxes]').val();
                data['subtotal'] = $(this).parents('.order_line_row').find('input[name=order_line_total]').val();
                data['notes'] = $(this).parents('.order_line_row').find('input[name=order_line_notes]').val();
                data['section'] = $(this).parents('.order_line_row').find('input[name=order_line_section]').val();
                update_quotation_order_line_product(data);
                // row_data = data;

            })
        /***  Send Email End  */
        $(function () {
            $("input[type=number]").on('input',function () {
                if($(this).val() < $(this).attr('min')){
                    $(this).val($(this).attr('min'));
                }
            });
        });
        $('#send-by-email, #send-proforma-invoice').on('shown.bs.modal', function (e) {
            $.ajax({
                url: "{{ route('admin.quotation.get_pdf',Hashids::encode(@$model->id)) }}",
                type: 'GET',
                success: function (data) {
                    $('.pdf_link_a').attr('href', data['link']);
                },
                complete:function(data){
                    // Hide loader container
                }
            });
        });

    </script>
<script type="text/javascript">
       // Actions URL's
var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
var do_follow_url = '{{ route('admin.log.user-following') }}';
var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
