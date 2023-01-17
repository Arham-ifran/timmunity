<!DOCTYPE html>
<html>

<head>
    <title>Invoice | TIMmunity</title>
</head>
<style type="text/css">
    body {
        background: #f9f9f9;
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
        font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif;
    }

    h1.Quotation-title {
        color: #444;
        font-size: 35px;
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
        margin-top:-10px;
    }
    .ribbon {
        width: 170px;
        height: 170px;
        overflow: hidden;
        position: absolute;
    }
    .ribbon-top-right {
        top: -20px;
        right: -20px;
    }
    .ribbon span {
        background-color: #28e728;;
    }
    .ribbon span.partially-paid {
        background-color: #dbaf34;
    }
    .ribbon span.un-paid {
        background-color: #009a71;
    }
    .ribbon-top-right span {
        left: -25px;
        top: 45px;
        transform: rotate(45deg);
    }
    .ribbon span {
        position: absolute;
        display: block;
        width: 275px;
        padding: 25px 0;
        box-shadow: 0 5px 10px rgb(0 0 0 / 10%);
        color: #fff;
        font: 700 18px/1 'Lato', sans-serif;
        text-shadow: 0 1px 1px rgb(0 0 0 / 20%);
        text-transform: uppercase;
        text-align: center;
        line-height: 5px;
        z-index: 1;
        align-items: center;
        height: 5px;
        overflow: hidden;
        user-select: none;
    }

</style>

<body>
    <section class="container">
        <div class="content-wrapper">
            <table>
                <tr>
                    <!-- <td valign="middle">Welcome to Odoo</td> -->
                    <td class="border-bottom">
                        <img src="{{ asset('frontside/dist/img/logo.png') }}" alt="" />
                    </td>
                    <td style="position: relative;">
                        <div class="ribbon ribbon-top-right">
                            <span class="
                                    @if(@$model->is_paid == 0 || @$model->is_paid == null)
                                        un-paid
                                    @else
                                        @if(@$model->is_partially_paid == 1)
                                            partially-paid
                                        @endif
                                    @endif
                                ">
                                @if(@$model->is_paid == 0 || @$model->is_paid == null)
                                    un-paid
                                @else
                                    @if(@$model->is_partially_paid == 1)
                                        Partially Paid
                                    @else
                                        Paid
                                    @endif
                                @endif
                            </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="sub-heading" valign="top">
                        <p>
                            <span>{{ @$model->quotation->customer->name }}</span><br />
                            @foreach ($model->quotation->customer->contact_addresses as $c_add)
                                @if ($c_add->id == $model->invoice_address)
                                    <span>{{ $c_add->street_1 . ', ' . $c_add->street_2 }}</span><br />
                                    <span>{{ $c_add->city . ', ' . $c_add->contact_countries->name }}</span><br />
                                    @if ($model->quotation->customer->phone != '' && $model->quotation->customer->phone != null)
                                        <span>{{ $model->quotation->customer->phone }}</span>
                                    @elseif($model->quotation->customer->mobile != '' && $model->quotation->customer->mobile != null)
                                        <span>{{ $model->quotation->customer->mobile }}</span>
                                    @endif
                                @endif
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            <h1 class="Quotation-title">Invoice # INV - {{ str_pad($model->id, 5, '0', STR_PAD_LEFT) }}</h1>
            <table class="table" style="width: 100%">
                <thead>
                    <tr>
                        <th>Invoice Date:</th>
                        <th>Salesperson</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($model->created_at)->format('M-d-Y') }}</td>
                        <td>{{ @$model->other_info->sales_person->firstname }}</td>
                    </tr>
                </tbody>
            </table>
            <hr />
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Taxes</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $quotation_total = 0;
                        $tax_amount_total = 0;
                        $subtotal = 0;
                    @endphp
                    @if (@$model->invoice_order_lines != null)
                        @foreach ($model->invoice_order_lines as $invoice_order_line)
                            @php
                                $tax_amount = 0;
                            @endphp
                            @if ($invoice_order_line->quotation_order_line->product_id != null)
                                <tr class="border-bottom">
                                    <td class="border-bottom">{{ @$invoice_order_line->quotation_order_line->product->product_name }}</td>
                                    <td class="border-bottom">{{ @$invoice_order_line->invoiced_qty }}</td>
                                    <td class="border-bottom">${{ @$invoice_order_line->quotation_order_line->unit_price }}</td>
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
                                    <td class="border-bottom">${{ $tax_amount }}</td>
                                    <td class="border-bottom">${{ $total }}</td>
                                </tr>
                            @elseif(@$order_line->section != null)
                                <tr class="border-bottom">
                                    <td colspan="5" class="border-bottom"> {{ @$invoice_order_line->quotation_order_line->section }} </td>
                                </tr>
                            @elseif(@$order_line->notes != null)
                                <tr class="border-bottom">
                                    <td  class="border-bottom" colspan="5"> {{ @$invoice_order_line->quotation_order_line->notes }}</td>
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
                    </tr>
                    <tr>
                        <td class="thick-line"></td>
                        <td class="thick-line"></td>
                        <td class="no-line"></td>
                    </tr>
                    <tr>
                        <td class="thick-line"></td>
                        <td class="thick-line"></td>
                        <td class="no-line"></td>
                        <td class="thick-line text-center">Subtotal</td>
                        <td class="thick-line text-right">${{ $quotation_total - $tax_amount_total  }}</td>
                    </tr>
                    <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-center">Taxes</td>
                        <td class="no-line text-right">${{ $tax_amount_total }}</td>
                    </tr>
                    <tr>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line"></td>
                        <td class="no-line text-center">Total</td>
                        <td class="no-line text-right">${{ $quotation_total }}</td>
                    </tr>
                    @if(@$model->is_paid == 1 || @$model->is_partially_paid == 1)
                        <tr>
                            <td class="no-line"></td>
                            <td class="no-line"></td>
                            <td class="no-line"></td>
                            <td class="no-line text-center">Amount Paid</td>
                            <td class="no-line text-right">${{ $model->amount_paid }}</td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </section>
</body>

</html>
