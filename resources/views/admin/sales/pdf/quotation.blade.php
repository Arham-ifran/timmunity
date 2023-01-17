<!DOCTYPE html>
<html>

<head>
    <title>Quotation | TIMmunity</title>
</head>
<style type="text/css">
    body {
        background: #fff;
        color: #444;
    }

    .content-wrapper {
        max-width: 686px;
        background: #fff;
        margin: auto;
        margin-top: 30px;
        padding: 20px;
    }

    table {
        width: 100%;
        text-align: left;
        font-family: Arial, sans-serif;
    }

    h1.Quotation-title {
        color: #444;
        font-size: 30px;
        padding-bottom: 20px;
    }

    td.border-bottom {
        border-bottom: 1px solid #ddd;
        padding-bottom: 20px;
    }
    tbody td.border-bottom {
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }

    .sub-heading span {
        color: #444;
        line-height: 25px;
    }
    tfoot, tfoot td {
        padding-top: 5px;
    }
    p, tr, td, span, strong, tbody, table{
        font-size:15px !important;
    }

</style>

<body>
    <section class="container">
        <div>
            <table>
                <tr>
                    <td class="border-bottom">
                        <img src="{{ public_path('frontside/dist/img/logo.png') }}" alt="" />
                    </td>
                </tr>
                <tr>
                    <td class="sub-heading" valign="top">
                        <p>
                            <span>{{ @$model->customer->name }}</span><br />
                            @foreach ($model->customer->contact_addresses as $c_add)
                                @if ($c_add->id == $model->invoice_address)
                                    <span>{{ $c_add->street_1 . ', ' . $c_add->street_2 }}</span><br />
                                    <span>{{ $c_add->city . ', ' . $c_add->contact_countries->name }}</span><br />
                                    @if ($model->customer->phone != '' && $model->customer->phone != null)
                                        <span>{{ $model->customer->phone }}</span>
                                    @elseif($model->customer->mobile != '' && $model->customer->mobile != null)
                                        <span>{{ $model->customer->mobile }}</span>
                                    @endif
                                @endif
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            @if($model->status == 1 || $model->status == 2)
                <h1 class="Quotation-title">{{ __('Order') }} # S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</h1>
            @else
                <h1 class="Quotation-title">{{ isset($order) ? __('Order') : __('Quotation')}} # S{{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</h1>
            @endif
            <table class="table" style="width: 100%">
                <thead>
                    <tr>
                        @if($model->status == 1 || $model->status == 2)
                            <td><strong>{{  __('Order Date')}}</strong></td>
                        @else
                            <td><strong>{{  isset($order) ? __('Order Date') :  __('Quotation Date')}}</strong></td>
                        @endif
                        <td><strong>{{__('Expiration')}}</strong></td>
                        @if($model->other_info->sales_team_id != 1)
                        <td><strong>{{__('Salesperson')}}</strong></td>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($model->created_at)->format('d/M/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($model->expires_at)->format('d/M/Y') }}</td>
                        @if($model->other_info->sales_team_id != 1)
                        <td>{{ @$model->other_info->sales_person->firstname }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <hr />
            <table>
                <thead>
                    <tr>
                        <td style="width: 400px"><strong>{{__('Description')}}</strong></td>
                        <td style="width: 150px"><strong>{{__('Quantity')}}</strong></td>
                        <td style="width: 150px"><strong>{{__('Unit Price')}}</strong></td>
                        <td style="width: 150px"><strong>{{__('Taxes')}}</strong></td>
                        <td style="width: 150px"><strong>{{__('Amount')}}</strong></td>
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
                    @if (@$model->order_lines != null)
                        @foreach ($model->order_lines as $order_line)
                            @if ($order_line->product_id != null)
                                <tr class="border-bottom">
                                    <td style="width: 400px" class="border-bottom">{{ @$order_line->product->product_name.' '.@$order_line->variation->variation_name }}</td>
                                    <td style="width: 150px" class="border-bottom">{{ @$order_line->qty }}</td>
                                    <td style="width: 150px" class="border-bottom">
                                        {{ currency_format(@$order_line->unit_price*$model->exchange_rate,$model->currency_symbol,$model->currency) }}
                                    </td>
                                    @php
                                        $product_price = $order_line->product != null ? $order_line->unit_price * $model->exchange_rate : 0;
                                        $subtotal =(double)( $order_line->qty * (double)$product_price);
                                        $total = (double)$subtotal;
                                        $tax_amount = 0;
                                        foreach ($order_line->quotation_taxes as $tax) {
                                            switch ($tax->tax->computation) {
                                                case 0:
                                                    $tax_amount_total += (double)$tax->tax->amount ;
                                                    $tax_amount += (double)$tax->tax->amount;
                                                    $total += (double)$tax->tax->amount;
                                                break;

                                                case 1:
                                                    $tax_amount_total += ($subtotal * $tax->tax->amount) / 100;
                                                    $tax_amount += ($subtotal * $tax->tax->amount) / 100;
                                                    $total += ($subtotal * $tax->tax->amount) / 100;
                                                break;
                                            }
                                        }
                                        $total += $subtotal * $model->vat_percentage / 100;
                                        $tax_amount_total += $subtotal * $model->vat_percentage / 100;
                                        $tax_amount += $subtotal * $model->vat_percentage / 100;
                                        $quotation_total += $total;
                                        $quotation_sub_total += $subtotal;

                                    @endphp
                                    <td style="width: 150px" class="border-bottom">{{ currency_format(@$tax_amount,$model->currency_symbol,$model->currency) }}</td>
                                    <td style="width: 150px" class="border-bottom">{{ currency_format(@$subtotal,$model->currency_symbol,$model->currency) }}</td>

                                </tr>
                            @elseif(@$order_line->section != null)
                                <tr class="border-bottom">
                                    <td colspan="5" class="border-bottom"> {{ @$order_line->section }} </td>
                                </tr>
                            @elseif(@$order_line->notes != null)
                                <tr class="border-bottom">
                                    <td  class="border-bottom" colspan="5"> {{ @$order_line->notes }}</td>
                                </tr>
                            @endif

                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td class="thick-line"></td>
                        <td class="thick-line"></td>
                        <td class="no-line"></td>
                        <td class="thick-line text-center">{{__('Subtotal')}}</td>
                        <td class="thick-line text-right">{{ currency_format(@$quotation_sub_total,$model->currency_symbol,$model->currency) }}</td>
                    </tr>
                    <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-center">{{__('Total Tax')}}</td>
                        @php
                                $tax = currency_format(@$model->total *$model->exchange_rate,'','',1) - currency_format($quotation_sub_total,'','',1);
                        @endphp
                        <td class="no-line text-right">{{ currency_format(  $tax ,$model->currency_symbol, $model->currency) }}</td>
                    </tr>
                    @if( $model->pricelist && ( isset($model->pricelist->rules[0]->percentage_value) || isset($model->pricelist->parent->rules[0]->percentage_value) ) )
                    <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-center">{{__('Applied Dicount')}}</td>
                        @if($model->pricelist->parent_id == null)
                        <td class="no-line text-right">{{ $model->pricelist->rules[0]->percentage_value }} %</td>
                        @else
                        <td class="no-line text-right">{{ $model->pricelist->parent->rules[0]->percentage_value }} %</td>
                        @endif
                    </tr>
                    @endif
                    <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-center">{{__('Total')}}</td>
                        <td class="no-line text-right">{{ currency_format($model->total*$model->exchange_rate,$model->currency_symbol,$model->currency) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</body>

</html>
