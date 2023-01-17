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
        table
        {
            width:700px;
        }
        table>thead>tr>th, table>tbody>tr>th, table>tfoot>tr>th, table>thead>tr>td, table>tbody>tr>td, table>tfoot>tr>td {
            border: 1px solid #0000004f;
        }
        .copy-right {
            position: fixed; 
            bottom: -12px; 
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
       <div>
            <section >
                <div >
                    <div >
                        <div >
                            <img src="{{ checkImage(asset('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="">
                        </div>
                    </div>
                    <div >
                        <div ></div>
                        <div>
                            <div >
                                <div >
                                    <h3>Reseller Vouchers ({{ $data['reseller_detail']->email }})</h3>
                                    <p><strong>Total Vouchers : </strong> {{ $data['total'] }}</p>
                                    <p><strong>Used Vouchers : </strong> {{ $data['used'] }}</p>
                                    <p><strong>Remaining Vouchers : </strong> {{ $data['total'] - $data['used'] }}</p>
                                </div>
                                <div >
                                    <p><strong>Date : </strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div >
                                <div >
                                    <table >
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Code</th>
                                                <th>Product</th>
                                                <th>Status</th>
                                                <th>End Customer</th>
                                                <th>Redeemed At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['vouchers'] as $index => $voucher)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $voucher->code }}</td>
                                                    <td>{{ $voucher->voucherOrder->product->product_name . ' ' . @$voucher->voucherOrder->variation->variation_name }}</td>
                                                    <td>
                                                        @if($voucher->status == 0)
                                                        Redeemed
                                                        @elseif($voucher->status == 1)
                                                        Approved
                                                        @elseif($voucher->status == 2)
                                                        Disabled
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ @$voucher->customer->name }}
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($voucher->created_at)->format('d M Y') }}
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
