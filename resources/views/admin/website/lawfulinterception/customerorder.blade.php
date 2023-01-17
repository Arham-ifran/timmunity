<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <style>
        header{
            display:none;
        }
        .content,body,html{
            background: white;
        }
        table thead{
            background: #009A71;
        }
        table thead th{
            color: white;
            background-color: #009A71;
        }

        table>thead>tr>th, table>tbody>tr>th, table>tfoot>tr>th, table>thead>tr>td, table>tbody>tr>td, table>tfoot>tr>td {
            border: 1px solid #0000004f;
        }
        .copy-right {
            position: fixed; 
            bottom: 0px; 
            left: 0px; 
            right: 0px;
            color: white;
            font-size: 16px;
            background-color: #009A71;
            padding: 0;
        }
        .text-center{
            text-align: center;
        }
        table{
            width: 100%;
        }
    </style>

   @yield('styles')
</head>
<body>
    <span ></span>
       <div>
            <section>
                <div>
                    <div>
                        <div >
                            <img src="{{ checkImage(asset('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="">
                        </div>
                    </div>
                    <div >
                        <div ></div>
                        <div >
                            <div >
                                <div >
                                    <h3>Customer Orders ({{ $data['customer_detail']->email }})</h3>
                                    <p><strong>Total Orders :</strong> {{ $data['total_orders_count'] }}</p>
                                    <p><strong>Total Quotations :</strong> {{ $data['quotation_count'] }}</p>
                                    <p><strong>Total Sales Orders :</strong> {{ $data['sales_order_count'] }}</p>
                                </div>
                            </div>
                            <div >
                                <div >
                                    <table style="width:100%"id="vouchers" class="table table-striped table-bordered dt-responsive nowrap no-footer dataTable">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('Order Number') }}</th>
                                                <th>{{ __('Product') }}</th>
                                                <th>{{ __('Order Date') }}</th>
                                                <th>{{ __('Delivery Date') }}</th>
                                                <th>{{ __('Expected Date') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Invoice Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $qty = 0;
                                            @endphp
                                            @foreach($data['quotation_details'] as $quotation)
                                                <tr>
                                                    <td>S{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</td>
                                                    <td>
                                                        @php
                                                            $quotation_total = 0;
                                                            $tax_amount_total = 0;
                                                            $total_subtotal = 0;
                                                        @endphp
                                                        @foreach($quotation->order_lines as $order_line)
                                                        {{ @$order_line->product->product_name . ' ' . @$order_line->variation->variation_name}} <br/>

                                                            @php
                                                                $qty = $order_line->qty;
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
                                                        @endforeach
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($quotation->created_at)->format('d-M-Y') }}</td>
                                                    <td>{{ $quotation->other_info == null ? '' :\Carbon\Carbon::parse($quotation->other_info->delivery_date)->format('d-M-Y'); }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($quotation->created_at)->addDays($quotation->payment_due_day)->format('d-M-Y') }}</td>
                                                    <td>
                                                        @if($quotation->status == 0)
                                                        {{__('Quotation')}}
                                                        @elseif($quotation->status == 1)
                                                        {{__('Sales Order')}}
                                                        @elseif($quotation->status == 2)
                                                        {{__('Locked')}}
                                                        @elseif($quotation->status == 3)
                                                            {{__('Quotation Sent')}}
                                                        @elseif($quotation->status == 4)
                                                            {{__('Cancelled')}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($quotation->total == $quotation->invoicedamount)
                                                            {{__('Fully Invoiced')}}
                                                        @elseif($quotation->invoicedamount != 0 && $quotation->total > $quotation->invoicedamount){
                                                            {{__('Partially Invoiced')}}
                                                        @else
                                                            {{__('Nothing to Invoice')}}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">
                                                        <p><strong> {{__('Quantity') }} : </strong>{{$qty}}</p>
                                                        <p><strong>{{ __('Sub Total') }} : </strong> {{ currency_format($total_subtotal* $quotation->exchange_rate , $quotation->currency_symbol, $quotation->currency) }}</p>
                                                        <p><strong>{{ __('Tax') }} : </strong>  {{ currency_format($tax_amount_total* $quotation->exchange_rate , $quotation->currency_symbol, $quotation->currency) }}</p>
                                                        <p><strong>{{ __('Total') }} : </strong>  {{  currency_format($quotation_total* $quotation->exchange_rate , $quotation->currency_symbol, $quotation->currency) }}</p>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div >

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <footer class="copy-right row main-footer text-center" >
                <div class="col-md-12 text-center pt-1">
                    <span class="footer-logo-text">Copyright © TIMmunity GmbH. All Rights Reserved.</span>
                </div>
        </footer>
    </body>
</html>
