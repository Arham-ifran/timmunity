<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title></title>
</head>

<body
    style="margin:0; font-size: 18px;line-height: 18px; background:#fff;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
    <div
        style="width: 1000px; margin:0 auto; background:#fff; color:#000;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
        <table
            style="width: 1000px;margin: 0px 0 50px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
            <tr
                style="vertical-align:top; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                <td
                    style="width: 500px; text-align: left;vertical-align:middle; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                    <img src="https://www.productimmunity.com/public/images/pdf-logo1.png" alt="" width="300px">
                </td>
                <td
                    style="width: 500px; text-align: right;vertical-align:middle; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                    <img src="https://www.productimmunity.com/public/images/pdf-logo2.png" alt="" width="300px">
                </td>
            </tr>
            <tr
                style="vertical-align:top; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                <td colspan="2"
                    style="vertical-align:top;height: 1200px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                    <table style="width: 1000px;">
                        <tr
                            style="vertical-align: middle; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                            <td
                                style="width: 500px;padding-top: 80px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <table
                                    style="width: 500px;  border-collapse: collapse;font-size: 18px;line-height: 18px;">
                                    <tr>
                                        <td>
                                            <span
                                                style="color: #9f9f9f; font-size: 14px; padding-bottom: 10px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $site_settings[0]->site_title }} <span
                                                    style="font-family: Segoe, 'Segoe UI', 'sans-serif';">•</span>
                                                {{ $site_settings[0]->site_address }} <span
                                                    style="font-family: Segoe, 'Segoe UI', 'sans-serif';">•</span>
                                                {{ $site_settings[0]->zip_code }}
                                                {{ $site_settings[0]->city }}
                                            </span>
                                            <p
                                                style="color: #000; font-size: 14px; margin:0;padding-top: 25px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif'; ">
                                                {{ @$model->quotation->customer->name }}<br>
                                                @foreach ($model->quotation->customer->contact_addresses as $c_add)
                                                    @if ($c_add->id == $model->invoice_address)
                                                        <span>{{ $c_add->street_1 . ', ' . $c_add->street_2 }}</span><br />
                                                        <span>{{ $c_add->city . ', ' . $c_add->contact_countries->name }}</span><br />
                                                        @if ($model->quotation->customer->phone != '' && $model->quotation->customer->phone != null)
                                                            <span>{{ $model->quotation->customer->phone }}</span>
                                                        @elseif($model->quotation->customer->mobile != '' &&
                                                            $model->quotation->customer->mobile != null)
                                                            <span>{{ $model->quotation->customer->mobile }}</span>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td
                                style="width: 500px;padding-top: 80px;text-align: right;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <table
                                    style="width: 498px;border-collapse: collapse;border: 1px solid #9f9f9f;font-size: 14px;line-height: 18px; padding: 4px 8px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    <thead>
                                        <tr>
                                            <th
                                                style="text-align: left;font-weight: 600; padding-bottom: 5px;font-size: 16px;padding: 8px 0px 0px 10px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Invoice
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td
                                                style="padding: 8px 0 0px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                INV - {{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Date
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                &nbsp;</td>
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ \Carbon\Carbon::parse($model->created_at)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;padding: 8px 0 0px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Reference:</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Source Platform:</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #9f9f9f;padding: 0px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                S{{ str_pad($model->quotation->id, 5, '0', STR_PAD_LEFT) }}:</td>
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #9f9f9f;padding: 0px 10px 8px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $site_settings[0]->site_title }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="color: #9f9f9f;border-bottom: 1px solid #9f9f9f;padding: 8px 0 8px 10px;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ @$model->quotation->customer->name }}</td>
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
                                                Date From</td>
                                            <td
                                                style="color: #9f9f9f;text-align: right;padding: 8px 10px 0px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Date To</td>
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
                                    style="width: 1000px;border-collapse: collapse;font-size: 14px;line-height: 18px;margin-bottom: 50px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    <thead>
                                        <tr
                                            style=" border-bottom: 1px solid #9f9f9f; border-top: 1px solid #9f9f9f; padding: 12px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                            <th
                                                style="font-weight: 500;text-align: left;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Pos</th>
                                            <th
                                                style="font-weight: 600;text-align: left;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Description
                                            </th>
                                            <th
                                                style="font-weight: 600;text-align: right;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Quantity
                                            </th>
                                            <th
                                                style="font-weight: 600;text-align: right;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Unit Price
                                            </th>
                                            <th
                                                style="font-weight: 600;text-align: right;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Taxes</th>
                                            <th
                                                style="font-weight: 600;text-align: right;padding: 4px 0;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Total Price
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $quotation_total = 0;
                                            $tax_amount_total = 0;
                                            $subtotal = 0;
                                        @endphp
                                        @if (@$model->invoice_order_lines != null)
                                            @foreach ($model->invoice_order_lines as $index => $invoice_order_line)
                                                @php
                                                    $tax_amount = 0;
                                                @endphp
                                                @if ($invoice_order_line->quotation_order_line->product_id != null)
                                                    <tr>
                                                        <td
                                                            style="text-align: left;width: 100px;  vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                            {{ $index+1 }}
                                                        </td>
                                                        <td
                                                            style="text-align: left;width: 350px;vertical-align: top;white-space: nowrap;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                            {{ @$invoice_order_line->quotation_order_line->product->product_name .' '. @$invoice_order_line->quotation_order_line->variation->variation_name }}
                                                        </td>

                                                        <td
                                                            style="text-align: right;vertical-align:width: 150px; top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                            {{ @$invoice_order_line->invoiced_qty }}
                                                        </td>
                                                        <td
                                                            style="text-align: right;width: 150px;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                            {{ @$invoice_order_line->quotation_order_line->unit_price }}
                                                        </td>
                                                        <td
                                                            style="text-align: right;width: 150px;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                            {{ @$invoice_order_line->quotation->vat_percentage }}%
                                                            @foreach ($model->quotation->customer->contact_addresses as $c_add)
                                                                @if ($c_add->id == $model->invoice_address)
                                                                {{ strtoupper($c_add->contact_countries->country_code) }}t
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        @php
                                                            $product_price = $invoice_order_line->quotation_order_line->product != null ? $invoice_order_line->quotation_order_line->unit_price : 0;
                                                            $subtotal = $invoice_order_line->invoiced_qty * $product_price;
                                                            $total = $subtotal;
                                                            foreach ($invoice_order_line->quotation_order_line->quotation_taxes as $tax) {
                                                                switch (@$tax->tax->computation) {
                                                                    case 0:
                                                                        $tax_amount += $tax->tax->amount;
                                                                        $tax_amount_total += $tax->tax->amount;
                                                                        $total += $tax->tax->amount;
                                                                        break;

                                                                    case 1:
                                                                        $tax_amount += ($subtotal * $tax->tax->amount) / 100;
                                                                        $tax_amount_total += ($subtotal * $tax->tax->amount) / 100;
                                                                        $total += ($subtotal * $tax->tax->amount) / 100;
                                                                        break;
                                                                }
                                                            }
                                                            $total += $subtotal * $model->quotation->vat_percentage / 100;
                                                            $tax_amount_total += $subtotal * $model->quotation->vat_percentage / 100;
                                                            $tax_amount = $subtotal * $model->quotation->vat_percentage / 100;
                                                            $quotation_total += $total;

                                                        @endphp
                                                        <td
                                                            style="text-align: right;width: 150px;vertical-align: top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                            {{ $tax_amount }}
                                                        </td>
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

                                            @endforeach
                                        @endif
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
                                    style="border-collapse: collapse;width: 500px;float: right;font-size: 14px;line-height: 18px; padding: 4px 8px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 10px 0px;border-bottom: 1px solid #9f9f9f;border-top: 1px solid #9f9f9f;text-align: left;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                 NET </td>
                                            <td
                                                style="color: #000;text-align: right;padding: 10px 0px 10px 0px;border-top: 1px solid #9f9f9f;border-bottom: 1px solid #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $quotation_total - $tax_amount_total  }}</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-bottom: 1px solid #000;padding: 10px 0 10px 0px;text-align: left; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                @foreach ($model->quotation->customer->contact_addresses as $c_add)
                                                    @if ($c_add->id == $model->invoice_address)
                                                    {{ strtoupper($c_add->contact_countries->country_code) }}
                                                    @endif
                                                @endforeach
                                                {{ $model->quotation->vat_percentage }}%
                                            <td
                                                style="text-align: right;border-bottom: 1px solid #000;padding: 10px 0px 10px 0px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ number_format($tax_amount_total, 2, '.', '')  }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="padding: 10px 0 0px 0px;font-weight: 600;text-align: left; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                Total
                                            </td>
                                            <td
                                                style="text-align: right;padding: 4px 0px 0px 0px;font-weight: 600;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                {{ $quotation_total }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            {{-- <td colspan="2"
                                style="width: 500px;padding-top: 40px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <table
                                    style="width: 500px;margin-bottom:20px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    <tbody>
                                        @if (!empty($payment->txn_id))
                                            <tr>
                                                <td colspan="2" style="white-space: nowrap;">
                                                    <span
                                                        style="text-align: left;white-space: nowrap;margin-bottom: 10px;font-size: 14px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                        {{ $lang_arr['payments']['please_use_following_communication_for_payment'] }}
                                                        : <span
                                                            style="white-space: nowrap;font-weight: 600;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">PRD/{{ date('Y') }}/{{ $payment->id }}</span></span>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td
                                                style="padding-top: 20px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                                <span
                                                    style="text-align: left;white-space: nowrap;font-size: 14px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">{{ $lang_arr['payments']['payment_terms'] }}:
                                                    {{ $lang_arr['payments']['weekly'] }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td> --}}
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table
                        style="width: 1000px;border-collapse: collapse;font-size: 14px;line-height: 18px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                        <tr>
                            <td width="450px"
                                style="border-bottom: 1px solid #9f9f9f; padding-bottom:5px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p
                                    style="margin: 0; padding-bottom: 8px;color: #9f9f9f;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                    Invoice
                                    INV - {{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</p>
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
                                    Email : <span
                                        style="color:#00bcd4; padding-left: 15px;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                        {{$site_settings[0]->site_email}}</span><br>
                                    Platform <span
                                        style="font-size: 13">
                                        {{$site_settings[0]->site_title}}</span><br>
                                    Company Website: <span
                                        style="font-size: 13">{{$site_settings[0]->site_url}}</span>

                                </p>
                            </td>
                            <td
                                style="padding-top:10px;vertical-align:top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p style="color: #9f9f9f;">{{ settingValue('commercial_register_address') }}<br>
                                    VAT-ID: {{$site_settings[0]->vat_id}}<br>
                                    TAX-ID: {{$site_settings[0]->tax_id}}
                                </p>
                            </td>
                            <td
                                style="padding-top:10px; vertical-align:top;font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif, 'Segoe UI', 'sans-serif';">
                                <p style="color: #9f9f9f;">{{ settingValue('bank_name') }}<br>
                                    IBAN: {{ $site_settings[0]->iban }}<br>
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
