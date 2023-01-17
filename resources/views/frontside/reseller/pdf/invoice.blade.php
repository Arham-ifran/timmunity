<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title></title>
    
</head>

<body
    style="margin:0; font-size: 18px;line-height: 18px; background:#fff;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
   
    <div
        style="margin:5px 25px; background:#fff; color:#000;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
        <table
            style="width: 1000px;margin: 0px 0 50px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
            <tr
                style="vertical-align:top; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                <td
                    style="width: 500px; text-align: left;vertical-align:middle; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                    <img src="{{ checkImage(public_path('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="" width="300px">
                </td>
            </tr>
            <tr
                style="vertical-align:top; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                <td colspan="2"
                    style="vertical-align:top;height: 1100px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                    <table style="width: 1500px;">
                        <tr
                            style="vertical-align: middle; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                            <td
                                style="width: 500px;padding-top: 80px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <table
                                    style="width: 500px;  border-collapse: collapse;font-size: 18px;line-height: 18px;">
                                    <tr>
                                        <td>
                                            <span
                                                style="color: #9f9f9f; font-size: 16px; padding-bottom: 10px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $site_settings[0]->site_title }} <span
                                                    style="font-family: Segoe, 'Segoe UI', 'sans-serif';">•</span>
                                                {{ $site_settings[0]->site_address }} <span
                                                    style="font-family: Segoe, 'Segoe UI', 'sans-serif';">•</span>
                                                {{ $site_settings[0]->zip_code }}
                                                {{ $site_settings[0]->city }}
                                            </span>
                                            <p
                                                style="color: #000; font-size: 16px; margin:0;padding-top: 25px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif'; ">
                                                @php
                                                   $invoice_as=$model->details[0]->voucher_order->reseller->contact->invoice_as; 
                                                @endphp
                                                @if($model->details[0]->voucher_order->reseller && $invoice_as=="1")
                                                {{ @$model->details[0]->voucher_order->reseller->name }}<br>
                                                @elseif($model->details[0]->voucher_order->reseller && $invoice_as=="0")
                                                {{ @$model->details[0]->voucher_order->reseller->contact->company_name}}<br>
                                                @else
                                                {{ @$model->details[0]->voucher_order->distributor->name }}<br>
                                                @endif
                                                <span>{{ @$model->details[0]->voucher_order->street_address }}</span><br />
                                                <span>{{ @$model->details[0]->voucher_order->city . ',' }}</span><br />
                                                <span>{{ @$model->details[0]->voucher_order->contact_country->name }}</span>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td
                                style="width: 500px;padding-top: 80px;text-align: right;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <table
                                    style="float:right;border-collapse: collapse;border: 1px solid #9f9f9f;font-size: 16px;line-height: 18px; padding: 4px 8px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    <thead>
                                        <tr>
                                            <th
                                                style="text-align: left;font-weight: 600; padding-bottom: 5px;font-size: 16px;padding: 8px 0px 0px 10px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Voucher Invoice')}}
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td
                                                style="padding: 8px 0 0px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ 'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT) }}</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Date')}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                &nbsp;</td></td>
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{  \Carbon\Carbon::parse($model->created_at)->format('d-m-Y')}}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;padding: 8px 0 0px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Reference')}}:</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Source Platform:')}}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                @if($model->details[0]->voucher_order->reseller)
                                                {{ str_replace(' ','',$model->details[0]->voucher_order->reseller->name) }}-{{ 'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT) }}
                                                @else
                                                {{ str_replace(' ','',$model->details[0]->voucher_order->distributor->name) }}-{{ 'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT) }}
                                                @endif
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $site_settings[0]->site_title }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;border-bottom: 1px solid #9f9f9f;padding: 8px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                @php
                                                   $invoice_as=$model->details[0]->voucher_order->reseller->contact->invoice_as; 
                                                @endphp
                                                @if($model->details[0]->voucher_order->reseller && $invoice_as=="1")
                                                {{ @$model->details[0]->voucher_order->reseller->name }}<br>
                                                @elseif($model->details[0]->voucher_order->reseller && $invoice_as=="0")
                                                {{ @$model->details[0]->voucher_order->reseller->contact->company_name}}<br>
                                                @else
                                                {{ @$model->details[0]->voucher_order->distributor->name }}</td>
                                                @endif
                                            <td
                                                style="color: #9f9f9f;border-bottom: 1px solid #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;padding: 8px 0 0px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $site_settings[0]->site_title }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $site_settings[0]->company_registration_number }}</td>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;padding: 8px 0 0px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Date From')}}</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Date To')}}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ date('d/m/Y', strtotime($model->created_at . ' -7 days')) }}</td>
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ date('d/m/Y', strtotime($model->created_at)) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"
                                style="padding-top: 100px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <table
                                    style="width: 2000px;border-collapse: collapse;font-size: 16px;line-height: 18px;margin-bottom: 50px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    <thead>
                                        <tr
                                            style=" border-bottom: 1px solid #9f9f9f; border-top: 1px solid #9f9f9f; padding: 12px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                            <th
                                                style="width:50px;font-weight: 600;text-align: left;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Pos')}}</th>
                                            <th
                                                style="margin-left:5px;width:450px;font-weight: 600;text-align: left;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Order ID')}}
                                            </th>
                                            {{-- <th
                                                style="width:350px;font-weight: 600;text-align: left;padding: 4px 5px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Description')}}
                                            </th> --}}
                                            <th
                                                style="width:120px;font-weight: 600;text-align: left !important;padding: 4px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Quantity')}}
                                            </th>
                                            <th
                                                style="width:130px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Unit Price')}}
                                            </th>
                                            <th
                                                style="width:110px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Taxes')}}</th>
                                            <th
                                                style="width:120px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Total Price')}}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach( $model->details as $ind => $payment_detail )
                                        <tr>
                                            <td
                                                style="text-align: left;  vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                               {{ $ind++ }}
                                            </td>
                                            <td
                                                style="text-align: left;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                               @if($payment_detail->voucher_order->reseller)
                                               {{ str_replace(' ','',$payment_detail->voucher_order->reseller->name).'-'.str_pad($payment_detail->voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($payment_detail->voucher_order->created_at)); }}
                                               @else
                                               {{ str_replace(' ','',$payment_detail->voucher_order->distributor->name).'-'.str_pad($payment_detail->voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($payment_detail->voucher_order->created_at)); }}
                                               @endif
                                            </td>
                                            {{-- <td
                                                style="text-align: left;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                @php
                                                    $product_name = $payment_detail->voucher_order->product->product_name.' ' ;
                                                    $product_name .= $payment_detail->voucher_order->product->project == null ? @$payment_detail->voucher_order->variation->variation_name : '' ;
                                                @endphp
                                                {{ $product_name }} Voucher Payment
                                                <br>
                                                <ul>
                                                    @foreach($payment_detail->vouchers as $voucher)
                                                        <li>
                                                            {{ $voucher->code }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td> --}}

                                            <td
                                                style="text-align: left !important;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ count(explode(',',$payment_detail->voucher_ids)) }}
                                            </td>
                                            <td
                                                style="text-align: left !important;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ currency_format(( @$payment_detail->voucher_order->unit_price - ( @$payment_detail->voucher_order->unit_price * @$payment_detail->voucher_order->discount_percentage / 100) ) * $payment_detail->payment->exchange_rate,$payment_detail->payment->currency_symbol,$payment_detail->payment->currency) }}
                                            </td>
                                            <td
                                                style="text-align: left !important;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ @$payment_detail->voucher_order->taxes }}
                                            </td>
                                            <td
                                                style="text-align: left !important;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ currency_format(@$payment_detail->total_payable* $payment_detail->payment->exchange_rate,$payment_detail->payment->currency_symbol,$payment_detail->payment->currency ) }}
                                            </td>
                                        </tr>
                                       
                                        <tr >
                                            <td >
                                            </td>
                                            <td style="padding-top:20px" colspan="7">
                                                @php
                                                    $product_name = $payment_detail->voucher_order->product->product_name.' ' ;
                                                    $product_name .= $payment_detail->voucher_order->product->project == null ? @$payment_detail->voucher_order->variation->variation_name : '' ;
                                                @endphp
                                                {{ $product_name }} Voucher Payment
                                                <br>
                                                <br>
                                                <ul>
                                                    @foreach($payment_detail->vouchers as $voucher)
                                                        <li>
                                                            {{ $voucher->code }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <hr>
                                            </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width: 500px; text-align: left;vertical-align:middle;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                            </td>
                            <td
                                style="width: 500px; text-align: right;vertical-align:middle;padding-top: 40px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <table
                                    style="float:right;border-collapse: collapse;width: 500px;font-size: 16px;line-height: 18px; padding: 4px 8px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 10px 0px;border-bottom: 1px solid #9f9f9f;border-top: 1px solid #9f9f9f;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                 {{__('NET')}} </td>
                                                 @php
                                                     $unit_price = @$model->details[0]->voucher_order->unit_price - ( @$model->details[0]->voucher_order->unit_price * @$model->details[0]->voucher_order->discount_percentage / 100);
                                                     $unit_price *= $model->details[0]->voucher_order->exchange_rate;
                                                     $qty = count(explode(',',$model->voucher_ids));
                                                 @endphp
                                            <td
                                                style="color: #000;text-align: right;padding: 10px 0px 10px 0px;border-top: 1px solid #9f9f9f;border-bottom: 1px solid #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ currency_format($model->total_payable - ($model->tax_amount* $model->details[0]->voucher_order->exchange_rate),$model->currency_symbol,$model->currency) }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #000;padding: 10px 0 10px 0px;text-align: left; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                               {{__('Taxes')}}
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #000;padding: 10px 0px 10px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ currency_format($model->tax_amount* $model->details[0]->voucher_order->exchange_rate,$model->currency_symbol,$model->currency) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Total')}}
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ currency_format($model->total_payable,$model->currency_symbol,$model->currency) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Payment Status')}}
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                @if($model->refunded_at == null)
                                                    {{ ( $model->is_paid == 1 ) ? ( ( $model->is_partial_paid == 1 ) ? __('Partially Paid') : __('Paid') ) :  __('Pending')  }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Amount Paid')}}
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ currency_format($model->amount_paid ,$model->currency_symbol,$model->currency) }}
                                            </td>
                                        </tr>
                                        @php
                                            $total = currency_format($model->total_payable,'','',1);
                                            $amount_paid = currency_format($model->amount_paid ,'','',1);
                                            $remaining_amount = $total - $amount_paid; 
                                        @endphp
                                        @if($remaining_amount > 0)
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{__('Remaining Amount')}}
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ currency_format($remaining_amount ,$model->currency_symbol,$model->currency) }}
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2"
                                style="width: 500px; text-align: left;padding-top:20px;vertical-align:middle;font-family: Firefly Sung, DejaVu Sans , 'sans-serif';font-size:14px">
                                {{__('Please use the following communication for your payment')}} : {{'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT)}}
                            </td>

                        </tr>
                        <tr>
                            @if($model->details[0]->voucher_order->reseller)
                                @php
                                    $reseller_invoice_cron_day = $site_settings[0]->reseller_invoice_cron_day;
                                    $reseller_invoice_cron_days_duration = $site_settings[0]->reseller_invoice_cron_days_duration;
                                    $reseller_invoice_cron_day = $model->details[0]->voucher_order->reseller->contact->reseller_invoice_cron_day != '' ? $model->details[0]->voucher_order->reseller->contact->reseller_invoice_cron_day :$reseller_invoice_cron_day;
                                    $reseller_invoice_cron_days_duration = $model->details[0]->voucher_order->reseller->contact->reseller_invoice_cron_days_duration != '' ? $model->details[0]->voucher_order->reseller->contact->reseller_invoice_cron_days_duration : $reseller_invoice_cron_days_duration;
                                    switch ($reseller_invoice_cron_day){
                                        case 1:
                                            $reseller_invoice_cron_day = __("Monday");
                                            break;
                                        case 2:
                                            $reseller_invoice_cron_day = __("Tuesday");
                                            break;
                                        case 3:
                                            $reseller_invoice_cron_day = __("Wednesday");
                                            break;
                                        case 4:
                                            $reseller_invoice_cron_day = __("Thursday");
                                            break;
                                        case 5:
                                            $reseller_invoice_cron_day = __("Friday");
                                            break;
                                        case 6:
                                            $reseller_invoice_cron_day = __("Saturday");
                                            break;
                                        case 7:
                                            $reseller_invoice_cron_day = __("Sunday");
                                            break;
                                    }
                                    switch ($reseller_invoice_cron_days_duration)
                                    {
                                        case 7:
                                            $reseller_invoice_cron_days_duration = __("Weekly");
                                            break;
                                        case 14:
                                            $reseller_invoice_cron_days_duration = __("Fortnigtly");
                                            break;
                                        case 28:
                                            $reseller_invoice_cron_days_duration = __("Monthly");
                                            break;
                                        case 56:
                                            $reseller_invoice_cron_days_duration = __("2 Months");
                                            break;
                                    }
                                @endphp 
                            @endif
                            <td colspan="2"
                                style="width: 500px; text-align: left;padding-top:20px;vertical-align:middle;font-family: Firefly Sung, DejaVu Sans , 'sans-serif';font-size:14px">
                                @if($model->details[0]->voucher_order->reseller)
                                    @if($reseller_invoice_cron_days_duration == 0)
                                        {{__('Payment Terms')}}: {{__('Same Day')}}
                                    @else
                                        {{__('Payment Terms')}}: {{$reseller_invoice_cron_day}} 
                                    @endif
                                @else
                                        {{__('Payment Terms')}}: {{ __('Weekly') }} 
                                @endif
                                
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table
                        style="width: 1000px;border-collapse: collapse;font-size: 16px;line-height: 18px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                        <tr style="padding-bottom:20px;">
                            <td width="450px"
                                style="border-bottom: 1px solid #9f9f9f; padding-bottom:5px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p
                                    style="margin: 0; padding-bottom: 8px;color: #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    {{__('Voucher Invoice')}}
                                    {{'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT)}}
                            </td>
                            <td width="300px"
                                style="border-bottom: 1px solid #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                            </td>
                            <td width="250px"
                                style="border-bottom: 1px solid #9f9f9f;text-align:right;padding-bottom:5px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p
                                    style="margin: 0; padding-bottom: 8px;color: #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    </p>
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="padding-top:10px;vertical-align:top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p
                                    style="color: #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    {{$site_settings[0]->site_title}}<br>
                                    {{$site_settings[0]->site_address}}<br>
                                    {{$site_settings[0]->zip_code}} {{$site_settings[0]->city}}<br>
                                    {{__('Email')}} : <span
                                        style="color:#009A71;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                        {{$site_settings[0]->site_email}}</span><br>
                                    {{__('Platform Website')}}: <span
                                        style="font-size: 14px">
                                        {{$site_settings[0]->site_url}}</span><br>
                                    {{__('Company Website')}}: <span
                                        style="font-size: 14px">{{$site_settings[0]->site_url}}</span>

                                </p>
                            </td>
                            <td
                                style="padding-top:10px;vertical-align:top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p style="color: #9f9f9f;font-size:14px">Handelsregister Braunschweig HRB 208156<br>
                                    {{__('VAT-ID')}}: {{$site_settings[0]->vat_id}}<br>
                                    {{__('TAX-ID')}}: {{$site_settings[0]->tax_id}}
                                </p>
                            </td>
                            <td
                                style="padding-top:10px; font-size:14px;vertical-align:top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p style="color: #9f9f9f;">Commerzbank<br>
                                    {{__('IBAN')}}: {{ $site_settings[0]->iban }}<br>
                                    {{__('BIC')}}: {{ $site_settings[0]->code }}<br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
