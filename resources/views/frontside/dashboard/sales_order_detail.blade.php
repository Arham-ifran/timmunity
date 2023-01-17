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
                <a style="color:white;font-weight:500;" href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a> /
                <a style="color:white;font-weight:500;" href="{{ route('user.dashboard.sales_order') }}">{{ __('Sales Orders') }}</a> /
                <strong>S{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</strong>
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
                                {{ currency_format(currency_format(@$quotation->total*@$quotation->exchange_rate,'','',1),$quotation->currency_symbol,$quotation->currency)  }}
                            </h3>
                            {{-- <p class="text-center list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.sales_order') }}" title="Sales Orders">
                                <a href="#." class="btn btn-primary btn-pill"> {{ __('Download') }} </a>
                            </p> --}}
                            <p class="text-center list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.invoices') }}" title="Invoices &amp; Bills">
                                <a href="#sales_order"> {{ __('Sales Order') }} </a><br>
                                <a href="#pricing"> {{ __('Pricing') }} </a><br>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="o_portal_docs list-group">
                            <div class="row">
                                <h3 class="list-group-item list-group-item-action d-flex align-items-center justify-content-between" id="sales_order" href="#." title="Price">
                                    {{ __('Sales Order') }} S{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}
                                </h3>
                            </div>
                            <div class=" list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="{{ route('user.dashboard.sales_order') }}" title="Sales Orders">
                                <p class="row"><strong>{{ __('Order Date') }}: </strong>{{ \Carbon\Carbon::parse($quotation->created_at)->format('d/M/Y') }}</p>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        @if($quotation->invoice_address == $quotation->delivery_address )
                                        <strong>{{ __('Invoicing and Shipping Address:') }} </strong><br>
                                        @else
                                        <strong>Invoicing Address: </strong><br>
                                        @endif

                                        @if(@$quotation->invoice_address == null || @$quotation->invoice_address == 0 )
                                            @if(@$quotation->customer->street_1 != null && @$quotation->customer->street_1 != 0 )
                                            {{ @$quotation->customer->street_1 }} <br>
                                            {{ @$quotation->customer->city }}, {{ @$quotation->customer->zipcode }}<br>
                                            {{ @$quotation->customer->contact_countries->name }}
                                            @else
                                            {{ $quotation->customer->name }}
                                            @endif
                                        @elseif(@$quotation->invoice_address_detail != null && @$quotation->invoice_address_detail != '')
                                            {{ @$quotation->invoice_address_detail->street_1 }} <br>
                                            {{ @$quotation->invoice_address_detail->city }}, {{ @$quotation->invoice_address_detail->zipcode }} <br>
                                            {{ @$quotation->invoice_address_detail->contact_countries->name }}
                                        @endif

                                    </div>
                                    @if($quotation->invoice_address != $quotation->delivery_address )
                                    <div class="col-md-6">
                                        <strong>Delivery Address: </strong><br>

                                        @if(@$quotation->delivery_address == null || @$quotation->delivery_address == 0 )
                                            @if(@$quotation->invoice_address != null && @$quotation->invoice_address != 0 )
                                                {{ $quotation->invoice_address_detail->street_1 }} <br>
                                                {{ $quotation->invoice_address_detail->city }}, {{ $quotation->invoice_address_detail->zipcode }} <br>
                                                {{ @$quotation->invoice_address_detail->contact_countries->name }}
                                            @else
                                                @if($quotation->customer->street_1 != null && $quotation->customer->street_1 != 0 )
                                                    {{ @$quotation->customer->street_1 }} <br>
                                                    {{ @$quotation->customer->city }}, {{ @$quotation->customer->zipcode }}<br>
                                                    {{ @$quotation->customer->contact_countries->name }}
                                                @else
                                                    {{ @$quotation->customer->name }}
                                                @endif
                                            @endif
                                        @else
                                            {{ $quotation->delivery_address_detail->street_1 }} <br>
                                            {{ $quotation->delivery_address_detail->city }}, {{ $quotation->delivery_address_detail->zipcode }} <br>
                                            {{ $quotation->delivery_address_detail->contact_countries->name }}
                                        @endif

                                    </div>
                                    @endif
                                </div>
                                @if(count($quotation->invoices) > 0)
                                <p>
                                    <strong>{{ __('Invoices') }}</strong>
                                </p>
                                @foreach($quotation->invoices as $invoice)
                                {{-- <a  class="row" target="_blank" href="{{ route('user.dashboard.invoice.detail', Hashids::encode($invoice->id)) }}">{{ 'INV/'. str_pad($invoice->id, 5, '0', STR_PAD_LEFT); }} <strong> Date: </strong>06/22/2020 </a> --}}
                                <a  class="row" target="_blank" href="{{ route('user.dashboard.invoice.detail', Hashids::encode($invoice->id)) }}">{{ 'TIM/'.\Carbon\Carbon::parse($invoice->created_at)->format('Y').'/'.str_pad($invoice->id, 3, '0', STR_PAD_LEFT) }} <strong> Date: </strong>{{\Carbon\Carbon::parse($invoice->created_at)->format('d-M-Y')}} </a>
                                @endforeach
                                @endif
                                <div id="pricing"></div>
                            </div>
                            @if (count($quotation->order_lines))
                            <div class=" list-group-item list-group-item-action d-flex align-items-center justify-content-between"  title="Sales Orders">
                                <h3>{{ __('Pricing') }}</h3>
                                <div class="table-responsive">
                                    <table id="pricing_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Products') }}</th>
                                                <th>{{ __('Quantity') }}</th>
                                                <th>{{ __('Unit Price') }}</th>
                                                <th>{{ __('Taxes') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $quotation_total = 0;
                                                $tax_amount_total = 0;
                                                $total_subtotal = 0;
                                            @endphp
                                            @foreach($quotation->order_lines as $order_line )
                                                <tr>
                                                    <td class="product_name">{{ @$order_line->product->product_name.' '.@$order_line->variation->variation_name }}</td>
                                                    <td class="product_qty">{{ @$order_line->qty }}</td>
                                                    <td class="product_unit_price">
                                                        {{-- {{ $quotation->currency.' '.number_format(@$order_line->unit_price*$quotation->exchange_rate,2) }} --}}
                                                        {{ currency_format(@$order_line->unit_price*$quotation->exchange_rate,$quotation->currency_symbol,$quotation->currency) }}
                                                    </td>
                                                    <td class="product_taxes">
                                                        @if ($order_line->quotation_taxes != null)
                                                            @foreach ($order_line->quotation_taxes as $tax)
                                                                <span class="tagged">
                                                                    {{ $tax->tax->name }}</span>
                                                            @endforeach
                                                        @endif
                                                        <span class="tagged">{{ $quotation->vat_percentage }}% {{ $quotation->vat_label }}</span>
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
                                                        $tax_amount_total += $subtotal * $quotation->vat_percentage / 100;
                                                        $total += $subtotal * $quotation->vat_percentage / 100;
                                                        $quotation_total += $total;

                                                    @endphp
                                                    <td class="product_total">
                                                        {{ currency_format($total*$quotation->exchange_rate,$quotation->currency_symbol,$quotation->currency) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan=3></td>
                                                <td><strong>{{ __('Sub Total') }}</strong></td>
                                                <td>
                                                    {{ currency_format($total_subtotal*$quotation->exchange_rate,$quotation->currency_symbol,$quotation->currency) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="no-line" colspan=3></td>
                                                <td><strong>{{ __('Tax') }}  </strong></td>
                                                <td>
                                                    {{ currency_format($tax_amount_total*$quotation->exchange_rate,$quotation->currency_symbol,$quotation->currency) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="no-line" colspan=3></td>
                                                <td><strong>{{ __('Total') }}</strong></td>
                                                <td>
                                                    {{ currency_format(currency_format(@$quotation->total*@$quotation->exchange_rate,'','',1),$quotation->currency_symbol,$quotation->currency)  }}

                                                    {{-- {{ currency_format($quotation_total*$quotation->exchange_rate,$quotation->currency_symbol,$quotation->currency) }} --}}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
