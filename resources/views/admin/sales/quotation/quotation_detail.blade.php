@extends('admin.layouts.app')
    @section('title', __('Quotation Detail'))
    @section('styles')
        <link rel="stylesheet" href="{{ asset('backend/bower_components/select2/dist/css/select2.min.css') }}" />
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
        <link href="{{ asset('backend/dist/css/loader.css') }}" rel="stylesheet" type="text/css">
        <style>
            span.select2.select2-container.select2-container--default.select2-container {
                width: 100% !important;
            }

            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background-color: #499a72;
            }

            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: white;
            }

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
                background-image: url('{{ asset("backend/dist/img/pdf.svg") }}');
            }
            tr.order_lnes {
                cursor: pointer;
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
                            @isset($model)
                                @switch($model->status)
                                    @case(0)
                                        {{ __('Quotation') }}
                                    @break
                                    @case(1)
                                        {{ __('Sales Order') }}
                                    @break
                                    @case(2)
                                        {{ __('Sales Order') }}
                                    @break
                                    @case(3)
                                        {{ __('Quotation') }}
                                    @break
                                    @case(4)
                                        {{ __('Quotation') }}
                                    @break
                                    @default

                                @endswitch
                            @else
                                {{ __('Quotation') }} /
                            @endisset
                            <small>
                                S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}
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
                            @canany(['Edit Quotation','Create Quotation'])
                            <div class="col-md-6 form-save-btn-div">
                                @can('Edit Quotation')
                                <a class="skin-gray-light-btn btn" href="{{ route('admin.quotations.edit', Hashids::encode($model->id)) }}"><i
                                        class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                @endcan
                                @can('Create Quotation')
                                <a style="margin-left: 10px;" class="skin-green-light-btn btn ml-2"
                                    href="{{ route('admin.quotations.create') }}"><i class="fa fa-plus"
                                        aria-hidden="true"></i></a>
                                @endcan
                            </div>
                            @endcanany
                            @canany(['Delete Quotation','Duplicate Quotation','Mark Quotation As Sent','Generate Payment Link'])
                            <div class="col-md-6 text-center">
                                <div class="quotation-right-side content-center">
                                    <div class="btn-flat filter-btn dropdown custom-dropdown-buttons">
                                        <!-- <i class="fa fa-filter" aria-hidden="true"></i> -->
                                        <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            {{ __('Action') }} <span class="caret"></span>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @can('Delete Quotation')
                                                <a class="dropdown-item delete-btn" href="#">{{ __('Delete') }}</a>
                                            @endcan
                                            @can('Duplicate Quotation')
                                                <a class="dropdown-item quotation-duplicate-btn" href="#">{{ __('Duplicate') }}</a>
                                            @endcan
                                            @can('Mark Quotation As Sent')
                                            @if($model->status != 3)
                                                <a class="dropdown-item change-status-btn" data-status="3"  href="#.">{{ __('Mark Quotation as Sent') }}</a>
                                            @endif
                                            @endcan
                                            @can('Generate Payment Link')
                                            @php
                                                $sales_settings = \App\Models\SalesSettings::where('variable_name','orders_online_payment')->first();
                                                $online_payment = $sales_settings ? $sales_settings->variable_value : 0;
                                            @endphp
                                            @if(!$paymentPaid && $model->total > 0 && $online_payment == 1)
                                                <a class="dropdown-item " target="_blank" href="{{ route('admin.quotation.payment.link', Hashids::encode($model->id)) }}">{{ __('Generate Payment Link') }}</a>
                                            @endif
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcanany
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="box">
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
                    <div class="box-body">
                        <div class="col-md 12">
                            <div class="row box pt-2 mt-3">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h1 class="green-title"> S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</h1>
                                    </div>
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
                                                <h4><a href="#">{{ translation($model->customer->id,4,app()->getLocale(),'name',$model->customer->name) }}</a></h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Invoice Address') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                <h4>
                                                    <a href="#">
                                                        @if(@$model->invoice_address == null || @$model->invoice_address == 0 )
                                                            @if(@$model->customer->street_1 != null && @$model->customer->street_1 != 0 )
                                                            {{ translation($model->customer->id,4,app()->getLocale(),'street_1',$model->customer->street_1) . ', ' . translation($model->customer->id,4,app()->getLocale(),'city',$model->customer->city) . ', ' . @$model->customer->contact_countries->name }}
                                                            @else
                                                            {{ $model->customer->name }}
                                                            @endif
                                                        @elseif(@$model->invoice_address_detail != null && @$model->invoice_address_detail != '')
                                                        {{ translation($model->invoice_address_detail->id,5,app()->getLocale(),'street_1',@$model->invoice_address_detail->street_1) . ', ' .translation($model->invoice_address_detail->id,5,app()->getLocale(),'city',@$model->invoice_address_detail->city) . ', ' . @$model->invoice_address_detail->contact_countries->name }}
                                                        @endif
                                                    </a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Delivery Address') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                <h4>
                                                    <a href="#">
                                                        @if(@$model->delivery_address == null || @$model->delivery_address == 0 )
                                                            @if(@$model->invoice_address != null && @$model->invoice_address != 0 )
                                                            {{ translation($model->customer->id,4,app()->getLocale(),'street_1',$model->customer->street_1) . ', ' . translation($model->customer->id,4,app()->getLocale(),'city',$model->customer->city) . ', ' . @$model->customer->contact_countries->name }}
                                                            @else
                                                                @if(@$model->customer->street_1 != null && @$model->customer->street_1 != 0 )
                                                                {{ translation($model->customer->id,4,app()->getLocale(),'street_1',$model->customer->street_1) . ', ' . translation($model->customer->id,4,app()->getLocale(),'city',$model->customer->city) . ', ' . @$model->customer->contact_countries->name }}
                                                                @else
                                                                {{ $model->customer->name }}
                                                                @endif
                                                            @endif
                                                        @else
                                                        {{ translation(@$model->delivery_address_detail->id,5,app()->getLocale(),'street_1',@$model->delivery_address_detail->street_1) . ', ' . translation(@$model->delivery_address_detail->id,5,app()->getLocale(),'city',@$model->delivery_address_detail->city)  . ', ' . @$model->delivery_address_detail->contact_countries->name }}
                                                        @endif
                                                    </a>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  Tab Col No 02 -->
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Expiration') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                <h5>{{ \Carbon\Carbon::parse($model->expires_at)->format('d-M-Y') }}</h5>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Price List') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                <h4>{{ translation( @$model->pricelist->id,12,app()->getLocale(),'name',@$model->pricelist->name) }}</h4>
                                                @if( $model->pricelist && ( isset($model->pricelist->rules[0]->percentage_value) || isset($model->pricelist->parent->rules[0]->percentage_value) ) )
                                                    @if($model->pricelist->parent_id == null)
                                                        <span>( {{ $model->pricelist->rules[0]->percentage_value }} % discount )</span>
                                                    @else
                                                        <span>( {{ $model->pricelist->parent->rules[0]->percentage_value }} % discount )</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Payment Terms') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                @if($model->payment_term_detail)
                                                <h4>
                                                    {{ $model->payment_term_detail->term_value }}
                                                    @switch($model->payment_term_detail->term_type)
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
                                                    @endif
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4 static-content">
                                                <h4>{{ __('Payment Due Day') }}</h4>
                                            </div>
                                            <div class="col-sm-8 dynamic-content">
                                                <h4>{{ $model->payment_due_day }}</h4>
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
                                                    if (isset($model)) {
                                                        if ($model->order_lines != null) {
                                                            $value = '';
                                                            $count_order_lines = count($model->order_lines);
                                                            foreach ($model->order_lines as $ind => $order_line) {
                                                                if ($ind == 0) {
                                                                    $value = $order_line->id;
                                                                } else {
                                                                    $value .= ',' . $order_line->id;
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <table id="product_order_line_table"
                                                    class="table table-bordered table-striped sub-table">
                                                    <thead>
                                                        <tr>
                                                            <th><a href="#.">{{ __('Product') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Description') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Quantity') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Delivered Quantity') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Invoiced Quantity') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Lead Time') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Unit Price') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Taxes') }}<span class="caret"></span></a></th>
                                                            <th><a href="#.">{{ __('Sub Total') }}<span class="caret"></span></a></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $quotation_total = 0;
                                                            $tax_amount_total = 0;
                                                            $total_subtotal = 0;
                                                        @endphp
                                                        @if (@$model->order_lines != null)
                                                            @foreach ($model->order_lines as $order_line)
                                                                @if ($order_line->product_id != null)
                                                                    <tr class="order_lnes" data-order-list-id="{{ @$order_line->id }}" >
                                                                        <td class="product_name">
                                                                            {{ @$order_line->product->product_name.' '.@$order_line->variation->variation_name }}
                                                                            <input type="hidden" name="vouchers" class="vouchers"
                                                                            value = "
                                                                                @if($order_line->vouchers != null)
                                                                                    <ul style='padding-left:15px'>
                                                                                        @foreach($order_line->vouchers as $voucher)
                                                                                            @if($voucher->redeemed_at == null)
                                                                                                {{ '<li>'.$voucher->voucher_code.'</li>' }}
                                                                                            @else
                                                                                                {{ '<li style="color:red">'.$voucher->voucher_code.'<br><span style="color:green">License Key: '.$voucher->license->license_key.'</span></li>' }}
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </ul>
                                                                                    @endif
                                                                                ">
                                                                        </td>
                                                                        <td class="product_description">{{ @$order_line->description }}</td>
                                                                        <td class="product_qty">{{ @$order_line->qty }}</td>
                                                                        <td class="product_delivered_qty">{{ @$order_line->delivered_qty }}</td>
                                                                        <td class="product_invoiced_qty">{{ @$order_line->invoiced_qty }}</td>
                                                                        <td class="product_lead_time">
                                                                            @if (@$order_line->product != null)
                                                                                {{ $order_line->product->lead_time != null ? \Carbon\Carbon::parse($order_line->product->lead_time)->format('d-M-Y') : '' }}
                                                                            @endif
                                                                        </td>
                                                                        <td class="product_unit_price">
                                                                            {{-- {{ number_format(@$order_line->unit_price * $model->exchange_rate,2) . ' ' . $model->currency }}  --}}
                                                                            {{ currency_format(@$order_line->unit_price * $model->exchange_rate,$model->currency_symbol,$model->currency) }}
                                                                        </td>
                                                                        <td class="product_taxes">
                                                                            @if ($order_line->quotation_taxes != null)
                                                                                @foreach ($order_line->quotation_taxes as $tax)
                                                                                    <span class="tagged">
                                                                                        {{ translation( $tax->tax->id,9,app()->getLocale(),'name',$tax->tax->name) }}</span>
                                                                                @endforeach
                                                                                @if(@$model->vat_percentage != 0)
                                                                                    <span class="tagged">{{ @$model->vat_label }}: {{ @$model->vat_percentage  }} %</span>
                                                                                @endif
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
                                                                            $tax_amount_total += $subtotal * $model->vat_percentage / 100;
                                                                            $total += $subtotal * $model->vat_percentage / 100;
                                                                            $quotation_total += $total;


                                                                        @endphp
                                                                        <td class="product_total">
                                                                            {{-- {{ number_format($total * $model->exchange_rate,2) . ' ' . $model->currency  }} --}}
                                                                            {{ currency_format(@$total * $model->exchange_rate,$model->currency_symbol,$model->currency) }}
                                                                        </td>
                                                                    </tr>
                                                                @elseif(@$order_line->section != null)
                                                                    <tr>
                                                                        <td colspan="7"> {{ @$order_line->section }}</td>
                                                                    </tr>
                                                                @elseif(@$order_line->notes != null)
                                                                    <tr>
                                                                        <td colspan="7"> {{ translation(@$order_line->id,2,app()->getLocale(),'text',@$order_line->notes) }}</td>
                                                                    </tr>
                                                                @endif

                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                                <div class="row">

                                                    <div class="col-md-8">
                                                        @if(@$model->terms_and_conditions)
                                                            <p><strong>{{ __('Terms and Conditions') }}</strong></p>
                                                            <p>{{ translation($model->id,1,app()->getLocale(),'terms_and_conditions',@$model->terms_and_conditions) }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4" style="text-align:right">
                                                        <p>
                                                            <strong> {{ __('Untaxed Amount') }}: </strong>
                                                            <span id="untaxed_amount">
                                                                {{ currency_format(@$total_subtotal * $model->exchange_rate,$model->currency_symbol,$model->currency) }}
                                                            </span>
                                                            </p>
                                                        <p>
                                                            <strong> {{ __('Taxes') }}: </strong>
                                                            <span id="taxed_amount">
                                                                {{ currency_format(@$tax_amount_total * $model->exchange_rate,$model->currency_symbol,$model->currency) }}
                                                            </span>
                                                        </p>
                                                        <hr>
                                                        <p>
                                                            <strong> {{ __('Total') }}: </strong>
                                                            <span id="total_amount">
                                                                {{ currency_format(@$quotation_total * $model->exchange_rate,$model->currency_symbol,$model->currency) }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- optional products -->
                                            <div id="optional-products" class="tab-pane fade">
                                                @php
                                                    if (isset($model)) {
                                                        if ($model->optional_products != null) {
                                                            $value = '';
                                                            $count_optional_products = count($model->optional_products);
                                                            foreach ($model->optional_products as $ind => $optional_product) {
                                                                if ($ind == 0) {
                                                                    $value = $optional_product->id;
                                                                } else {
                                                                    $value .= ',' . $optional_product->id;
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <table id="optional_products_table"
                                                    class="table table-bordered table-striped sub-table">
                                                    <thead>
                                                        <tr>
                                                            <th><a href="#">{{ __('Product') }} <span class="caret"></span></a></th>
                                                            <th><a href="#">{{ __('Description') }} <span class="caret"></span></a></th>
                                                            <th><a href="#">{{ __('Quantity') }} <span class="caret"></span></a></th>
                                                            <th><a href="#">{{ __('Unit Price') }} <span class="caret"></span></a></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if (@$model->optional_products != null)
                                                            @foreach ($model->optional_products as $optional_product)
                                                                <tr>
                                                                    <td>{{ @$optional_product->product->product_name }}
                                                                    </td>
                                                                    <td>{{ @$optional_product->description }}</td>
                                                                    <td>{{ @$optional_product->qty }}</td>
                                                                    <td>
                                                                        {{-- {{ number_format(@$optional_product->product->unit_price * $model->exchange_rate,2) . ' ' . $model->currency  }} --}}
                                                                        {{ currency_format(@$optional_product->product->unit_price * $model->exchange_rate,$model->currency_symbol,$model->currency) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>

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
                                                                <h5>{{ @$model->other_info->sales_person->firstname.' '.@$model->other_info->sales_person->lastname }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Sales Team') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <h5>{{ @$model->other_info->sales_team->name }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Online Signature') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <h5><input type="checkbox" value="checked" @if(@$model->other_info->online_signature == 1) checked @endif disabled></h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Online Payment') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <h5><input type="checkbox" value="checked" @if(@$model->other_info->online_payment == 1) checked @endif disabled></h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ __('Customer Reference') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <h5>{{ @$model->other_info->customer_reference }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <h4>{{ _('Tags') }}</h4>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <h5>
                                                                    @if(@$model->other_info->tags != null)
                                                                        @foreach($model->other_info->tags as $tag)
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
                                                                <h5>{{ isset($model->other_info->delivery_date) ? \Carbon\Carbon::parse($model->other_info->delivery_date)->format('d-M-Y') : ''}}</h5>
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
                                                        <textarea class="textarea" name="text_template[sale_quotation]"
                                                            placeholder="Place some text here"
                                                            style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                                                            value="@if (isset($model->text_templates)) @foreach ($model->text_templates as $template) @if ($template->type == 0) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif ">@if (isset($model->text_templates)) @foreach ($model->text_templates as $template) @if ($template->type == 0) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif</textarea>
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
                                                        <textarea class="textarea" name="text_template[sale_confirmation]"
                                                            placeholder="Place some text here"
                                                            style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                                                            value="@if (isset($model->text_templates)) @foreach ($model->text_templates as $template) @if ($template->type == 1) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif">@if (isset($model->text_templates)) @foreach ($model->text_templates as $template) @if ($template->type == 1) {{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif</textarea>
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
                                                        <textarea class="textarea" name="text_template[performa_invoice]"
                                                            placeholder="Place some text here"
                                                            style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                                                            value="@if (isset($model->text_templates)) @foreach ($model->text_templates as $template)@if ($template->type == 2){{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }} @endif @endforeach @endif">
                                                                                @if (isset($model->text_templates))@foreach ($model->text_templates as $template)@if ($template->type == 2){{ translation($template->id,3,app()->getLocale(),'text',@$template->text) }}@endif @endforeach @endif
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
                </div>
            </section>
            <!-- Bottom- section -->
            @if(@$action == "Edit")
              <section class="bottom-section">
                <div class="row box">
                  <div class="row activity-back-color">
                    <div class="col-md-12">
                        <div class="custom-tabs mt-3 mb-2">
                            <div class="row">
                                <div class="col-md-8">
                                  <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#send_message">{{ __('Send Message') }}</a></li>
                                    <li><a data-toggle="tab" href="#log_note">{{ __('Log Note') }}</a></li>
                                    <li><a data-toggle="tab" href="#schedual_activity">{{ __('Schedule Activity') }}</a></li>
                                  </ul>
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
                              <div id="send_message" class="tab-pane fade active in">
                                <div class="row tab-form pt-3">
                                  <div class="row">
                                    <div class="col-md-3">
                                      <a class="skin-green-light-btn btn" type="button" data-toggle="modal"  data-target="#send-message-model" onclick="clearMessageForm()"><i class="fa fa-paper-plane"></i>&nbsp; {{ __('Send Message') }}</a>
                                      {!! $send_messages_view !!}
                                    </div>
                                  </div>
                                  {!! $send_message_tab_partial_view !!}
                                </div>
                              </div>
                              <!-- Log Note -->
                              <div id="log_note" class="tab-pane fade">
                                <div class="row tab-form pt-3">
                                  <div class="row">
                                    <div class="col-md-3">
                                      <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#log-note-model" onclick="clearNoteForm()"><i class="fa fa-plus"></i>&nbsp; {{ __('Add Note') }}</a>
                                      {!! $log_notes_view !!}
                                    </div>
                                  </div>
                                  {!! $notes_tab_partial_view !!}
                                </div>
                              </div>
                              <!-- Schedule Activity -->
                             <div id="schedual_activity" class="tab-pane fade">
                                <div class="row tab-form pt-3">
                                  <div class="row">
                                    <div class="col-md-3">
                                      <a class="skin-green-light-btn btn" type="button" data-toggle="modal" data-target="#schedule-activity-model" onclick="ClearScheduleActivity()"><i class="fa fa-clock-o"></i>&nbsp; {{ __('Add Schedule Activity') }}</a>
                                      {!! $schedual_activities_view !!}
                                    </div>
                                  </div>
                                  {!! $schedual_activity_tab_partial_view !!}
                                </div>
                              </div>
                            </div>
                        </div>
                     </div>
                  </div>
                </div>
              </section>
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
        <form method="POST" action="{{ route('admin.quotation.status.change') }}" id="quotation-form">
            <input type="hidden" name="id" value="{{ $model->id }}">
            <input type="hidden" name="status" value="{{ $model->status }}">
        </form>
        <form id="deleteForm" action="{{ route('admin.quotations.destroy',Hashids::encode(@$model->id)) }}" method="POST">
            @csrf
            @method('DELETE')
        </form>
        <form id="duplicateForm" action="{{ route('admin.quotation.duplicate',$model->id) }}" method="POST">
            @csrf
        </form>
    @endsection

    @section('scripts')
        <script src="{{ asset('backend/dist/js/custom.js') }}"></script>
        <script src="{{ asset('backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
        <script>
             $('.send-email-recepient').select2();
             $('textarea[name="send_email[email_body]"').summernote({
                tabsize: 2,
                height: 250
            });
            $('body').on('click', '.delete-btn', function() {
                Swal.fire({
                    title: custom_swt_alert_title,
                    text: custom_swt_alert_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: custom_swt_alert_confim_btn_text,
                    cancelButtonText: custom_swt_alert_cancel_btn_text,
                    closeOnConfirm: false,
                    closeOnCancel: true
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $("#deleteForm").submit();
                    } else if (result.isDenied) {
                        // Swal.fire('Changes are not saved', '', 'info')
                    }
                });
            });
            $('body').on('click', '.quotation-duplicate-btn', function() {
                $("#duplicateForm").submit();
            });

            $('body').on('click', '.change-status-btn', function() {
                status = $(this).attr('data-status');
                $('input[name=status]').val(status);
                $('#quotation-form').submit();
            });
            /**  View Order Line Modal **/
            $('body').on('click', '#product_order_line_table tbody tr.order_lnes', function() {

                $("#view-order-line-modal #product").html( $(this).find('.product_name').html() );
                $("#view-order-line-modal #qty").html( $(this).find('.product_qty').html() );
                $("#view-order-line-modal #invoiced").html( $(this).find('.product_invoiced_qty').html() );
                $("#view-order-line-modal #unit_price").html( $(this).find('.product_unit_price').html() );
                $("#view-order-line-modal #delivered").html( $(this).find('.product_delivered_qty').html() );
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
                    $("#view-order-line-modal #o_licenses").html( 'No License Attached' );
                }

                $("#view-order-line-modal").modal('show');
            });

            /**  Send Email Start **/
            $('body').on('click', '.send-email-btn-submit', function() {
                $('#send-by-email form').submit();
            });
            $('body').on('click', '.send-proforma-email-btn-submit', function() {
                $('#send-proforma-invoice form').submit();
            });
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
            $('#send-by-email, #send-proforma-invoice').on('shown.bs.modal', function (e) {
                $.ajax({
                    url: "{{ route('admin.quotation.get_pdf',Hashids::encode($model->id)) }}",
                    type: 'GET',
                    success: function (data) {
                        $('.pdf_link_a').attr('href', data['link']);
                    },
                    complete:function(data){
                        // Hide loader container
                    }
                });
            });

            /***  Send Email End  */

        </script>
<script type="text/javascript">
       // Actions URL's
var add_new_contact_url = '{{ route('admin.log.add-new-contact') }}';
var do_follow_url = '{{ route('admin.log.user-following') }}';
var do_unfollow_url = '{{ route('admin.log.user-un-follow') }}';
</script>
<script src="{{ asset('backend/dist/js/common.js') }}"></script>
@endsection
