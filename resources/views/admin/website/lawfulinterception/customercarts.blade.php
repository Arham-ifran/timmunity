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
                                    <h3>Customer Carts ({{ $data['customer_detail']->email }})</h3>
                                    <p><strong>Date : </strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                                    <p><strong>Total Carts :</strong> {{ $data['total_carts'] }}</p>
                                    <p><strong>Total Completed Carts :</strong> {{ $data['total_completed_carts'] }}</p>
                                    <p><strong>Total Abandoned Carts :</strong> {{ $data['total_abandoned_carts'] }}</p>
                                </div>
                            </div>
                            <div >
                                <div >
                                    <table class="table  table-bordered  no-footer dataTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['carts'] as $cart)
                                                @foreach($cart->cart_items as $cart_item)
                                                    <tr>
                                                        <td>
                                                            {{ @$cart_item->product->product_name . ' ' . @$cart_item->variation->variation_name }}
                                                        </td>
                                                        <td>{{ @$cart_item->qty }}</td>
                                                        <td>{{ currency_format($cart_item->unit_price * $cart->exchange_rate,$cart->currency_symbol,$cart->currency) }} </td>
                                                    </tr>
                                                @endforeach
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
