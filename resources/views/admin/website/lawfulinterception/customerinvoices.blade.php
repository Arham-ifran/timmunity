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
<body >
    <span></span>
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
                                    <h3>Customer Order Invoices ({{ $data['customer_detail']->email }})</h3>
                                    <p><strong>Date : </strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                                    <p><strong>Total Invoices :</strong> {{ $data['invoice_count'] }}</p>
                                    <p><strong>Total Paid Invoices :</strong> {{ $data['inovice_paid_count'] }}</p>
                                    <p><strong>Total Un-Paid Invoices :</strong> {{ $data['inovice_unpaid_count'] }}</p>
                                    <p><strong>Total Partially Paid Invoices :</strong> {{ $data['inovice_partially_paid_count'] }}</p>
                                </div>
                            </div>
                            <div >
                                <div >
                                    <table class="table  table-bordered  no-footer dataTable">
                                        <thead>
                                            <tr>
                                                <th>Order Number</th>
                                                <th>Invoice Number</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th>Tax Excluded</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Payment Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['invoices'] as $invoice)
                                                <tr>
                                                    <td>S{{ str_pad($invoice->quotation_id, 5, '0', STR_PAD_LEFT) }}</td>
                                                    <td>
                                                        @php
                                                        $text = 'TIM/'.\Carbon\Carbon::parse($invoice->created_at)->format('Y').'/'.str_pad($invoice->id, 3, '0', STR_PAD_LEFT);
                                                        @endphp
                                                        {{$text}}
                                                    <td>{{\Carbon\Carbon::parse($invoice->created_at)->format('d-M-Y')}}</td>
                                                    <td>{{\Carbon\Carbon::parse($invoice->created_at)->format('d-M-Y')}}</td>
                                                    <td>{{ currency_format(($invoice->total - $invoice->totaltax) * $invoice->quotation->exchange_rate,$invoice->quotation->currency_symbol,$invoice->quotation->currency) }}  </td>
                                                    <td>{{ currency_format($invoice->invoice_total * $invoice->quotation->exchange_rate,$invoice->quotation->currency_symbol,$invoice->quotation->currency) }} </td>
                                                    <td>
                                                        @if($invoice->status == 0)
                                                            {{__("Draft")}}
                                                        @elseif($invoice->status == 1)
                                                        {{__("Confirmed")}}
                                                        @elseif($invoice->status == 2)
                                                            {{__("Cancelled")}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                    @if($invoice->is_paid == 1)
                                                        @if($invoice->is_partially_paid == 1)
                                                            {{__("Partially Paid")}}
                                                        @else
                                                            {{__("Paid")}}
                                                        @endif
                                                    @else
                                                        {{__("Un-Paid")}}
                                                    @endif
                                                    </td>
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
