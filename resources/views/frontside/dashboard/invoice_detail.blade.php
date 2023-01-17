@extends('frontside.layouts.app')
@section('style')
<style>
    table > tfoot > tr > .no-line {
        border-top: none;
    }
    span.tagged {
        border: 3px solid;
        border-radius: 30px;
        padding: 0 10px;
    }
</style>
@endsection
@section('content')

    <div class="row dark-green div-breadcrumbs" style="background: #009a71; color: white; padding: 10px;">
        <div class="container">
            <div>
                @php
                    // $invoiceNumber = '/';
                    // if($model->status == 1 ){
                        // $invoiceNumber = 'INV/'. str_pad($model->id, 5, '0', STR_PAD_LEFT);
                        $invoiceNumber = 'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT);
                    // }
                @endphp
                <a style="color:white;font-weight:500;" href="{{ route('user.dashboard') }}">{{__('Dashboard')}}</a> /
                <a style="color:white;font-weight:500;" href="{{ route('user.dashboard.invoices') }}">{{__('Invoices')}}</a> /
                <strong>{{ $invoiceNumber }}</strong>
            </div>
        </div>
    </div>
    <section class="content-section" id="account-page">
        <div class="container">
            <div class="mt-3 row bottom-space">
                <div class="container">
                    <div class="col-md-3">
                        <div class="o_portal_docs list-group">
                            <h3 class="text-center list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="#." title="Price">
                               {{ currency_format(@$model->invoice_total,$model->quotation->currency_symbol,$model->quotation->currency) }}
                            </h3>
                            <p class="text-center list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.sales_order') }}" title="Sales Orders">
                                <a download="{{ $invoiceNumber }}" href="{{ $model->invoice_pdf_link  }}" class="btn btn-primary btn-pill"> {{__('Download')}} </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="o_portal_docs list-group">
                            <h3 class="list-group-item list-group-item-action d-flex align-items-center justify-content-between" id="sales_order" href="#." title="Price">
                                Invoice {{ $invoiceNumber }}
                            </h3>

                            <div class=" list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.sales_order') }}" title="Sales Orders">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        @if($model->quotation->invoice_address == $model->quotation->delivery_address )
                                        <strong>{{__('Invoicing and Shipping Address:')}} </strong><br>
                                        @else
                                        <strong>{{__('Invoicing Address:')}} </strong><br>
                                        @endif

                                        @if(@$model->quotation->invoice_address == null || @$model->quotation->invoice_address == 0 )
                                            @if(@$model->quotation->customer->street_1 != null && @$model->quotation->customer->street_1 != 0 )
                                            {{ @$model->quotation->customer->street_1 }} <br>
                                            {{ @$model->quotation->customer->city }}, {{ @$model->quotation->customer->zipcode }}<br>
                                            {{ @$model->quotation->customer->contact_countries->name }}
                                            @else
                                            {{ $model->quotation->customer->name }}
                                            @endif
                                        @elseif(@$model->quotation->invoice_address_detail != null && @$model->quotation->invoice_address_detail != '')
                                            {{ @$model->quotation->invoice_address_detail->street_1 }} <br>
                                            {{ @$model->quotation->invoice_address_detail->city }}, {{ @$model->quotation->invoice_address_detail->zipcode }} <br>
                                            {{ @$model->quotation->invoice_address_detail->contact_countries->name }}
                                        @endif

                                    </div>
                                    @if($model->quotation->invoice_address != $model->quotation->delivery_address )
                                    <div class="col-md-6">
                                        <strong>{{__('Delivery Address:')}} </strong><br>

                                        @if(@$model->quotation->delivery_address == null || @$model->quotation->delivery_address == 0 )
                                            @if(@$model->quotation->invoice_address != null && @$model->quotation->invoice_address != 0 )
                                                {{ $model->quotation->invoice_address_detail->street_1 }} <br>
                                                {{ $model->quotation->invoice_address_detail->city }}, {{ $model->quotation->invoice_address_detail->zipcode }} <br>
                                                {{ @$model->quotation->invoice_address_detail->contact_countries->name }}
                                            @else
                                                @if($model->quotation->customer->street_1 != null && $model->quotation->customer->street_1 != 0 )
                                                    {{ @$model->quotation->customer->street_1 }} <br>
                                                    {{ @$model->quotation->customer->city }}, {{ @$model->quotation->customer->zipcode }}<br>
                                                    {{ @$model->quotation->customer->contact_countries->name }}
                                                @else
                                                    {{ @$quotation->customer->name }}
                                                @endif
                                            @endif
                                        @else
                                            {{ $model->quotation->delivery_address_detail->street_1 }} <br>
                                            {{ $model->quotation->delivery_address_detail->city }}, {{ $model->quotation->delivery_address_detail->zipcode }} <br>
                                            {{ $model->quotation->delivery_address_detail->contact_countries->name }}
                                        @endif

                                    </div>
                                    @endif
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <strong>{{__('Invoice Date:')}}</strong><br>{{ \Carbon\Carbon::parse($model->created_at)->format('d/M/Y') }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>{{__('Order#')}}</strong><br><a target="_blank" href="{{ route('user.dashboard.quotations.detail', Hashids::encode($model->quotation_id)) }}"> S{{ str_pad($model->quotation_id, 5, '0', STR_PAD_LEFT) }}</a>
                                    </div>
                                </div>
                                <div id="pricing"></div>
                            </div>
                            <div class=" list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.sales_order') }}" title="Sales Orders">
                                <div class="table-responsive">
                                    <table id="pricing_table" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Description')}}</th>
                                                    <th>{{__('Quantity')}}</th>
                                                    <th>{{__('Unit Price')}}</th>
                                                    <th>{{__('Taxes')}}</th>
                                                    <th>{{__('Amount')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (@$model->invoice_order_lines != null)
                                                @foreach ($model->invoice_order_lines as $invoice_order_line)
                                                    <tr >
                                                        <td>{{ @$invoice_order_line->quotation_order_line->product->product_name }}</td>
                                                        <td>{{ @$invoice_order_line->quotation_order_line->invoiced_qty }}</td>
                                                        <td>
                                                            {{ currency_format(@$invoice_order_line->quotation_order_line->unit_price* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                        </td>
                                                        <td>
                                                            @if (@$invoice_order_line->quotation_order_line->quotation_taxes != null)
                                                                @foreach ($invoice_order_line->quotation_order_line->quotation_taxes as $tax)
                                                                    <span class="tagged">
                                                                        {{ @$tax->tax->name }}
                                                                    </span>
                                                                @endforeach
                                                            @endif
                                                            <span class="tagged">{{ $invoice_order_line->quotation_order_line->quotation->vat_percentage }}% {{ $invoice_order_line->quotation_order_line->quotation->vat_label }}</span>
                                                        </td>
                                                        <td>
                                                            {{ currency_format($invoice_order_line->total* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan=3></td>
                                                    <td><strong>{{ __('SubTotal') }}</strong></td>
                                                    <td>{{ currency_format((@$model->total - @$model->totaltax)* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line" colspan=3></td>
                                                    <td><strong>{{ __('Tax') }}  </strong></td>
                                                    <td>{{ currency_format(@$model->totaltax* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}</td>
                                                </tr>
                                                @if( ($model->quotation->pricelist && $model->quotation->pricelist->name!='Public Pricelist') && ( isset($model->quotation->pricelist->rules[0]->percentage_value) || isset($model->quotation->pricelist->parent->rules[0]->percentage_value) ) )
                                                <tr>
                                                    <td class="no-line" colspan=3></td>
                                                    <td>
                                                        <strong>
                                                            {{__('Applied Discount')}}
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        @isset($model->pricelist->rules[0])
                                                        {{ $model->quotation->pricelist->rules[0]->percentage_value }} %
                                                        @else
                                                        {{ $model->quotation->pricelist->parent->rules[0]->percentage_value }} %
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td class="no-line" colspan=3></td>
                                                    <td><strong>{{ __('Total') }}</strong></td>
                                                    <td>{{ currency_format(@$model->total* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line" colspan=3></td>
                                                    <td><strong>{{__('Payment Status')}}</strong></td>
                                                    <td>
                                                        @if($model->refunded_at == null)
                                                            {{ $model->is_partially_paid == 1 ? __('Partially Paid') : ($model->is_paid == 1 ? __('Paid') : __('Pending')) }}
                                                        @else
                                                            {{ __('Refunded At').' '.\Carbon\Carbon::parse($model->refunded_at)->format('d-M-Y')}}
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if($model->is_partially_paid == 1 || $model->is_paid == 1)
                                                    <tr>
                                                        <td class="no-line" colspan=3></td>
                                                        <td><strong>{{__('Total Paid')}}</strong></td>
                                                        <td>
                                                            @if($model->quotation->transaction_id == null )
                                                                {{ currency_format(@$model->amount_paid,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                            @else
                                                                {{ currency_format(@$model->amount_paid ,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line" colspan=3></td>
                                                        <td><strong>{{__('Remaining Amount')}}</strong></td>
                                                        <td>
                                                            @if($model->quotation->transaction_id == null )
                                                                {{ currency_format((currency_format($model->total* $model->quotation->exchange_rate,'','',1) - $model->amount_paid),$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                            @else
                                                                {{ currency_format((currency_format($model->total* $model->quotation->exchange_rate,'','',1) - $model->amount_paid),$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tfoot>
                                        </table>
                                 </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
