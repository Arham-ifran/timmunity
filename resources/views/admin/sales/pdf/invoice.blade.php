<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title></title>
    <style>
        @font-face {
            font-family: 'Firefly Sung';
            font-style: normal;
            font-weight: 500;
            src: url(http://eclecticgeek.com/dompdf/fonts/cjk/fireflysung.ttf)
                    format('truetype');
        }
    </style>
</head>
{{-- //Arial, sans-serif, 'Segoe UI' --}}
{{-- //Firefly Sung, DejaVu Sans , 'sans-serif';' --}}
<body
    style="margin:0; font-size: 18px;line-height: 18px; background:#fff;font-family: Arial , 'sans-serif';">
    <div
        style="width: 1000px; margin:0 auto; background:#fff; color:#000;font-family: Arial , 'sans-serif';">
        <table
            style="width: 1000px;margin: 0px 0 50px;font-family: Arial , 'sans-serif';">
            <tr
                style="vertical-align:top; font-family: Arial , 'sans-serif';">
                <td
                    style="width: 500px; text-align: left;vertical-align:middle; font-family: Arial , 'sans-serif';">
                    @if( $site_settings[0]->site_logo != null && $site_settings[0]->site_logo != '') 
                        <img src="{{ public_path('storage/uploads/' . $site_settings[0]->site_logo) }}" alt="TIMmunity" width="300px">
                    @else
                        <img src="{{ checkImage(public_path('storage/uploads/' . $site_settings[0]->site_logo),'logo.png') }}" alt="TIMmunity" width="300px">
                    @endif
                </td>
                <td
                    style="width: 500px; text-align: right;vertical-align:middle; font-family: Arial , 'sans-serif';">
                </td>
            </tr>
            <tr
                style="vertical-align:top; font-family: Arial , 'sans-serif';">
                <td colspan="2"
                    style="vertical-align:top;height: 1100px; font-family: Arial , 'sans-serif';">
                    <table style="width: 2000px;">
                        <tr
                            style="vertical-align: middle; font-family: Arial , 'sans-serif';">
                            <td
                                style="width: 500px;padding-top: 80px; font-family: Arial , 'sans-serif';">
                                <table
                                    style="width: 500px;  border-collapse: collapse;font-size: 18px;line-height: 18px;">
                                    <tr>
                                        <td>
                                            <span
                                                style="color: #9f9f9f; font-size: 16px; padding-bottom: 10px; font-family: Arial , 'sans-serif';">
                                                {{ $site_settings[0]->site_title }} <span
                                                    style="font-family: Segoe, 'Segoe UI', 'sans-serif';">•</span>
                                                {{ $site_settings[0]->site_address }} <span
                                                    style="font-family: Segoe, 'Segoe UI', 'sans-serif';">•</span>
                                                {{ $site_settings[0]->zip_code }}
                                                {{ $site_settings[0]->city }}
                                            </span>
                                            <p
                                                style="color: #000; font-size: 16px; margin:0;padding-top: 5px;font-family: Arial , 'sans-serif'; ">
                                                {{ @$model->quotation->customer->name }}<br>

                                                @foreach ($model->quotation->customer->contact_addresses as $c_add)
                                                    @if ($c_add->id == $model->quotation->invoice_address)
                                                        <span>{{ $c_add->street_1 . ', ' . $c_add->street_2 }}</span><br />

                                                        @if ($c_add->zipcode != "" && $c_add->zipcode != null)
                                                            <span>{{ $c_add->zipcode . ', ' . $c_add->city }}</span><br />
                                                        @else
                                                            <span>{{ $c_add->city }}</span><br />
                                                        @endif
                                                        <span>{{ $c_add->contact_countries->name }}</span><br />

                                                    @endif
                                                @endforeach
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td
                                style="width: 500px;padding-top: 80px;text-align: right;font-family: Arial , 'sans-serif';">
                                <table
                                    style="width: 498px;border-collapse: collapse;border: 1px solid #9f9f9f;font-size: 16px;line-height: 18px; padding: 4px 8px; font-family: Arial , 'sans-serif';">
                                    <thead>
                                        <tr>
                                            <th
                                                style="text-align: left;font-weight: 600; padding-bottom: 5px;font-size: 16px;padding: 8px 0px 0px 10px;font-family: Arial , 'sans-serif';">
                                                {{__('Invoice')}}
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td
                                                style="padding: 8px 0 0px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                {{'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT)}}
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px; font-family: Arial , 'sans-serif';">
                                                {{__('Date')}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                &nbsp;</td>
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Arial , 'sans-serif';">
                                                {{ \Carbon\Carbon::parse($model->created_at)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;padding: 8px 0 0px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                {{__('Reference')}}:</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px; font-family: Arial , 'sans-serif';">
                                                {{__('Source Platform')}}:</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                S{{ str_pad($model->quotation->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Arial , 'sans-serif';">
                                                {{ $site_settings[0]->site_title }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;border-bottom: 1px solid #9f9f9f;padding: 8px 0 8px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                {{ @$model->quotation->customer->name }}</td>
                                            <td
                                                style="color: #9f9f9f;border-bottom: 1px solid #9f9f9f;font-family: Arial , 'sans-serif';">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;padding: 8px 0 0px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                {{ $site_settings[0]->site_title }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                {{ $site_settings[0]->company_registration_number }}</td>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;font-family: Arial , 'sans-serif';">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;padding: 8px 0 0px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                {{__('Date From')}}</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px;font-family: Arial , 'sans-serif';">
                                                {{__('Date To')}}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Arial , 'sans-serif';">
                                                {{ \Carbon\Carbon::parse($model->created_at)->format('d/M/Y') }}</td>
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Arial , 'sans-serif';">

                                                @if($model->quotation->payment_due_day)
                                                    {{ \Carbon\Carbon::parse($model->created_at)->addDays($model->quotation->payment_due_day)->format('d/M/Y') }}</td>
                                                @else
                                                    {{ \Carbon\Carbon::parse($model->created_at)->format('d/M/Y') }}</td>
                                                @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"
                                style="padding-top: 100px;font-family: Arial , 'sans-serif';">
                                <table
                                    style="width: 2000px;border-collapse: collapse;font-size: 16px;line-height: 18px;margin-bottom: 50px;font-family: Arial , 'sans-serif';">
                                    <thead>
                                        <tr
                                            style=" border-bottom: 1px solid #9f9f9f; border-top: 1px solid #9f9f9f; padding: 12px 0;font-family: Arial , 'sans-serif';">
                                            <th
                                                style="width:50px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Arial , 'sans-serif';">
                                                {{__('Pos')}}</th>
                                            <th
                                                style="width:400px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Arial , 'sans-serif';">
                                                {{__('Description')}}
                                            </th>
                                            <th
                                                style="width:100px;font-weight: 600;text-align: left !important;padding: 4px 0px;font-family: Arial , 'sans-serif';">
                                                {{__('Quantity')}}
                                            </th>
                                            <th
                                                style="width:150px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Arial , 'sans-serif';">
                                                {{__('Unit Price')}}
                                            </th>
                                            <th
                                                style="width:150px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Arial , 'sans-serif';">
                                                {{__('Taxes')}}</th>
                                            <th
                                                style="width:150px;font-weight: 600;text-align: left !important;padding: 4px 0;font-family: Arial , 'sans-serif';">
                                                {{__('Total Price')}}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $quotation_total = 0;
                                            $quotation_sub_total = 0;
                                            $tax_amount_total = 0;
                                            $tax_amount = 0;
                                            $subtotal = 0;
                                        @endphp
                                        @if (@$model->invoice_order_lines != null)
                                            @foreach ($model->invoice_order_lines as $index => $invoice_order_line)
                                                @php
                                                    $tax_amount = 0;
                                                @endphp
                                                @if ( $invoice_order_line->quotation_order_line != null )
                                                    @if ( $invoice_order_line->quotation_order_line->product_id != null)
                                                        <tr>
                                                            <td
                                                                style="width:50px;text-align: left !important;  vertical-align: top;font-family: Arial , 'sans-serif';">
                                                                {{ $index+1 }}
                                                            </td>
                                                            <td
                                                                style="width:400px;text-align: left !important;vertical-align: top;font-family: Arial , 'sans-serif';white-space:pre-line;">
                                                                {{ @$invoice_order_line->quotation_order_line->product->product_name .' '. @$invoice_order_line->quotation_order_line->variation->variation_name }}
                                                            </td>

                                                            <td
                                                                style="width:100px;text-align: left !important;vertical-align:top;top;font-family: Arial , 'sans-serif';">
                                                                {{ @$invoice_order_line->invoiced_qty }}
                                                            </td>
                                                            <td
                                                                style="width:150px;text-align: left !important;vertical-align: top;font-family: Arial , 'sans-serif';">
                                                                {{ currency_format(@$invoice_order_line->quotation_order_line->unit_price * $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                            </td>
                                                            @php
                                                                $product_price = $invoice_order_line->quotation_order_line->product != null ? $invoice_order_line->quotation_order_line->unit_price * $model->quotation->exchange_rate : 0;

                                                                $subtotal = $invoice_order_line->quotation_order_line->qty * $product_price;
                                                                $total = $subtotal;
                                                                $tax_amount = 0;
                                                                foreach ($invoice_order_line->quotation_order_line->quotation_taxes as $tax) {
                                                                    switch ($tax->tax->computation) {
                                                                        case 0:
                                                                            $tax_amount_total += $tax->tax->amount ;
                                                                            $tax_amount += $tax->tax->amount ;
                                                                            $total += $tax->tax->amount;
                                                                            break;

                                                                        case 1:
                                                                            $tax_amount_total += ($subtotal * $tax->tax->amount) / 100;
                                                                            $tax_amount += ($subtotal * $tax->tax->amount) / 100;
                                                                            $total += ($subtotal * $tax->tax->amount) / 100;
                                                                            break;
                                                                    }
                                                                }
                                                                $total += $subtotal * $model->quotation->vat_percentage / 100;
                                                                $tax_amount_total += $subtotal * $model->quotation->vat_percentage / 100;
                                                                $tax_amount += $subtotal * $model->quotation->vat_percentage / 100;
                                                                $quotation_total += $total;
                                                                $quotation_sub_total += $subtotal;

                                                            @endphp
                                                            <td
                                                                style="width:150px;text-align: left !important;vertical-align: top;font-family: Arial , 'sans-serif';">
                                                                {{ currency_format(@$tax_amount,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                            </td>
                                                            <td
                                                                style="width:150px;text-align: left !important;vertical-align: top;font-family: Arial , 'sans-serif';">
                                                                {{ currency_format($subtotal+$tax_amount,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                            </td>
                                                        </tr>
                                                        </tr>
                                                    @elseif(@$order_line->section != null)
                                                        <tr class="border-bottom">
                                                            <td colspan="6" class="border-bottom"> {{ @$invoice_order_line->quotation_order_line->section }} </td>
                                                        </tr>
                                                    @elseif(@$order_line->notes != null)
                                                        <tr class="border-bottom">
                                                            <td  class="border-bottom" colspan="6"> {{ @$invoice_order_line->quotation_order_line->notes }}</td>
                                                        </tr>
                                                    @endif
                                                @endif

                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width: 500px; text-align: left;vertical-align:middle;font-family: Arial , 'sans-serif';">
                            </td>
                            <td
                                style="width: 500px; text-align: right;vertical-align:middle;padding-top: 40px;font-family: Arial , 'sans-serif';">
                                <table
                                    style="border-collapse: collapse;width: 500px;font-size: 14px;line-height: 18px; padding: 4px 8px;font-family: Arial , 'sans-serif';">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 10px 0px;border-bottom: 1px solid #9f9f9f;border-top: 1px solid #9f9f9f;text-align: left;font-family: Arial , 'sans-serif';">
                                                 {{__('NET')}} </td>
                                            <td
                                                style="color: #000;text-align: right;padding: 10px 0px 10px 0px;border-top: 1px solid #9f9f9f;border-bottom: 1px solid #9f9f9f;font-family: Arial , 'sans-serif';">
                                                {{ currency_format(@$quotation_sub_total,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                            </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #000;padding: 10px 0 10px 0px;text-align: left; font-family: Arial , 'sans-serif';">
                                                {{__('TAX')}}
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #000;padding: 10px 0px 10px 0px;font-family: Arial , 'sans-serif';">
                                                @php
                                                    $tax = currency_format($model->quotation->total* $model->quotation->exchange_rate,'','',1);
                                                    $tax -= currency_format($quotation_sub_total,'','',1);
                                                @endphp

                                                {{ currency_format($tax,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                            </td>
                                        </tr>
                                        @if( ($model->quotation->pricelist && $model->quotation->pricelist->name!='Public Pricelist') && ( isset($model->quotation->pricelist->rules[0]->percentage_value) || isset($model->quotation->pricelist->parent->rules[0]->percentage_value) ) )
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Arial , 'sans-serif';">
                                                {{__('Applied Discount')}}
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Arial , 'sans-serif';">
                                                @isset($model->quotation->pricelist->rules[0])
                                                {{ $model->quotation->pricelist->rules[0]->percentage_value }} %
                                                @else
                                                {{ $model->quotation->pricelist->parent->rules[0]->percentage_value }} %
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Arial , 'sans-serif';">
                                                {{__('Total')}}
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Arial , 'sans-serif';">
                                                {{ currency_format(@$model->quotation->total* $model->quotation->exchange_rate,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Arial , 'sans-serif';">
                                                {{__('Payment Status')}}
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Arial , 'sans-serif';">
                                                {{ $model->is_partially_paid == 1 ? __('Partially Paid') : ($model->is_paid == 1 ? __('Paid') : __('Pending')) }}
                                            </td>
                                        </tr>
                                        @if($model->is_partially_paid == 1 || $model->is_paid == 1)
                                            <tr>
                                                <td
                                                    style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Arial , 'sans-serif';">
                                                    {{__('Total Paid')}}
                                                </td>
                                                <td
                                                    style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Arial , 'sans-serif';">
                                                    @if($model->quotation->transaction_id == null )
                                                        {{ currency_format(@$model->amount_paid,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                    @else
                                                        {{ currency_format(@$model->amount_paid ,$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Arial , 'sans-serif';">
                                                    {{__('Remaining Amount')}}
                                                </td>
                                                <td
                                                    style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Arial , 'sans-serif';">
                                                    @if($model->quotation->transaction_id == null )
                                                        {{ currency_format((currency_format($model->total* $model->quotation->exchange_rate,'','',1) - $model->amount_paid),$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                        @else
                                                        {{ currency_format((currency_format($model->total* $model->quotation->exchange_rate,'','',1) - $model->amount_paid),$model->quotation->currency_symbol,$model->quotation->currency) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"
                                style="width: 500px; text-align: left;padding-top:20px;vertical-align:middle;font-family: Arial , 'sans-serif';font-size:14px">
                                {{__('Please use the following communication for your payment')}} : {{'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT)}}
                            </td>

                        </tr>
                        <tr>
                            <td colspan="2"
                                style="width: 500px; text-align: left;padding-top:20px;vertical-align:middle;font-family: Arial , 'sans-serif';font-size:14px">
                                {{__('Payment Terms')}}: {{$model->quotation->payment_term_detail->term_value}} @switch($model->quotation->payment_term_detail->term_type)@case(1)Days @break @case(2) Months @break @case(3) Years @break @default @endswitch
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table
                    style="width: 1000px;border-collapse: collapse;font-size: 16px;line-height: 18px;font-family: Arial , 'sans-serif';">
                    <tr>
                            <td width="350px"
                                style="border-bottom: 1px solid #9f9f9f; padding-bottom:5px;font-family: Arial , 'sans-serif';">
                                <p
                                    style="margin: 0; padding-bottom: 8px;color: #9f9f9f;font-family: Arial , 'sans-serif';">
                                    {{__('Invoice')}}
                                    {{-- INV - {{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</p> --}}
                                    {{'TIM/'.\Carbon\Carbon::parse($model->created_at)->format('Y').'/'.str_pad($model->id, 3, '0', STR_PAD_LEFT)}}
                            </td>
                            <td width="300px"
                                style="border-bottom: 1px solid #9f9f9f;font-family: Arial , 'sans-serif';">
                            </td>
                            <td width="250px"
                                style="border-bottom: 1px solid #9f9f9f;text-align:right;padding-bottom:5px;font-family: Arial , 'sans-serif';">
                                <p
                                    style="margin: 0; padding-bottom: 8px;color: #9f9f9f;font-family: Arial , 'sans-serif';">
                                    </p>
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="padding-top:10px;vertical-align:top;font-family: Arial , 'sans-serif';">
                                <p
                                    style="color: #9f9f9f;font-family: Arial , 'sans-serif';font-size:14px;">
                                    {{$site_settings[0]->site_title}}<br>
                                    {{$site_settings[0]->site_address}}<br>
                                    {{$site_settings[0]->zip_code}} {{$site_settings[0]->city}}<br>
                                    {{__('Email')}} : <span
                                        style="color:#00bcd4;font-family: Arial , 'sans-serif';">
                                        {{$site_settings[0]->site_email}}</span><br>
                                    {{__('Platform Website')}} <span
                                        style="font-size: 14px">
                                        {{$site_settings[0]->site_url}}</span><br>
                                    {{__('Company Website')}}: <span
                                        style="font-size: 14px">{{$site_settings[0]->site_url}}</span>

                                </p>
                            </td>
                            <td
                                style="padding-top:10px;vertical-align:top;font-family: Arial , 'sans-serif';font-size:14px;">
                                <p style="color: #9f9f9f;font-size:14px">Handelsregister Braunschweig HRB 208156<br>
                                    {{__('VAT-ID:')}} {{$site_settings[0]->vat_id}}<br>
                                    {{__('TAX-ID:')}} {{$site_settings[0]->tax_id}}
                                </p>
                            </td>
                            <td
                                style="padding-top:10px;font-size:14px; vertical-align:top;font-family: Arial , 'sans-serif';">
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
@php
    // dd('a');
@endphp
