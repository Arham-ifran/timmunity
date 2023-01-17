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
        table{
            width: 100%;
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
    </style>

   @yield('styles')
</head>
<body>
       <div >
            <section >
                <div >
                    <div>
                        <div >
                            <img src="{{ checkImage(asset('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="">
                        </div>
                    </div>
                    <div >
                        <div ></div>
                        <div>
                            <div >
                                <div >
                                    <h3>Reseller Vouchers Payments ({{ $data['reseller_detail']->email }})</h3>
                                    <p><strong>Date : </strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div >
                                <div >
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Reseller</th>
                                                <th>Orders</th>
                                                <th>Voucher Count</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($voucher_payments) == 0)
                                                <tr>
                                                    <td colspan="5" style="
                                                    text-align: center;
                                                ">No Records</td>
                                                </tr>
                                            @endif
                                            @foreach($voucher_payments as $index => $voucher_payment)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $voucher_payment->details[0]->voucher_order->reseller->name }}</td>
                                                    <td>
                                                        @foreach($voucher_payment->details as $detail)
                                                            {{ str_replace(' ','',$detail->voucher_order->reseller->name).'-'.str_pad($detail->voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($detail->voucher_order->created_at)) }}<br>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $count = 0;
                                                            foreach($voucher_payment->details as $detail){
                                                                $voucher_ids_array = explode(',', $detail->voucher_ids);
                                                                $count += count($voucher_ids_array);
                                                            }
                                                        @endphp
                                                        {{ $count.' '.__('Vouchers') }} 
                                                    </td>
                                                    <td>
                                                        {{ currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,$voucher_payment->currency_symbol,$voucher_payment->currency); }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $html = __('Un-paid');
                                                            if($voucher_payment->is_paid == 1){
                                                                $html = $voucher_payment->is_partial_paid == 1 ? __('Partially Paid') : __('Paid') ;
                                                            }
                                                            $html = $voucher_payment->refunded_at == null ? $html : __('Refunded At').' '.Carbon::parse($voucher_payment->refunded_at)->format('d-M-Y');
                                                        @endphp
                                                        {{ $html }}
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
