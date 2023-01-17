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
        table{
                width:700px;
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
       <div >
            <section >
                <div >
                    <div >
                        <div >
                            <img src="{{ checkImage(asset('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="">
                        </div>
                    </div>
                    <div>
                        <div>
                            <div >
                                <h3>Reseller Orders ({{ $data['reseller_detail']->email }})</h3>
                                <p><strong>Total Orders :</strong> {{ $data['total'] }}</p>
                                <p><strong>Approved Orders :</strong> {{ $data['approved'] }}</p>
                                <p><strong>Pending Orders :</strong> {{ $data['pending'] }}</p>
                                <p><strong>Rejected Orders :</strong> {{ $data['rejected'] }}</p>
                            </div>
                        </div>
                        <div>
                            <table>
                                <thead>
                                    <tr role="row">
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Discount (%)') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Used') }}</th>
                                        <th>{{ __('Remaining') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['orders'] as $key => $value)
                                        
                                        <tr>
                                            <td>{{ $value->product->product_name . ' ' . @$value->variation->variation_name; }}</td>
                                            <td>{{ \Carbon\Carbon::parse($value->created_at)->format('d/M/Y') }}</td>
                                            <td>{{ $value->discount_percentage }}</td>
                                            @php
                                                $taxhtml = '';
                                                $count = count($value->voucher_taxes);
                                                foreach($value->voucher_taxes as $ind => $voucher_tax){
                                                    $taxhtml .= $voucher_tax->tax->amount;
                                                    $taxhtml .= $voucher_tax->tax->type==1 ? ' %' : '';
                                                    if($ind < $count-1){
                                                        $taxhtml.=', ';
                                                    }
                                                }
                                                if($count > 0){
                                                    $taxhtml .= ', '.$value->vat_percentage.'% VAT';

                                                }else{
                                                    $taxhtml .= $value->vat_percentage.'% VAT';

                                                }
                                            @endphp
                                            <td>{{ $value->quantity }}</td>
                                            <td>{{ $value->used_quantity }}</td>
                                            <td>{{ $value->remaining_quantity }}</td>
                                            <td>
                                                @if($value->status == 0)
                                                    Pending
                                                @elseif($value->status == 1)
                                                    Approved
                                                @elseif($value->status == 2)
                                                    Rejected
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <p><strong>Unit Price : </strong>{{ currency_format($value->unit_price,$value->currency_symbol,$value->currency) }}</p>
                                                <p><strong>Tax : </strong>{{ $taxhtml }}</p>
                                                <p><strong>Total Payable : </strong>{{ currency_format($value->total_payable,$value->currency_symbol,$value->currency) }}</p>
                                                <p><strong>Active Status : </strong>@if($value->is_active == 0 ) In-Active @elseif($value->is_active == 1 ) Active @endif</p>
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
