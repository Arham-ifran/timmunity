<div class="row">
    <div class="col-md-12">
        <h4>Customer Details</h4>
    </div>
    <div class="col-md-6">
        <p><strong>Name: </strong>{{$model->customer_name}}</p>
        <p><strong>Email: </strong>{{$model->customer_email}}</p>
        <p><strong>Phone: </strong>{{$model->customer_phone}}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Address: </strong>{{$model->customer_street}}</p>
        <p><strong>City: </strong>{{$model->customer_city}}</p>
        <p><strong>State: </strong>{{$model->customer_state}}</p>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h4>Order Items</h4>
    </div>
    <div class="col-md-12">
        <table  style="width:100%"  class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i=1;
                @endphp
                @foreach($model->items as $item)
                <tr>
                    <td>
                        {{$i++}}
                    </td>
                    <td>{{$item->article}}</td>
                    <td>{{$item->qty}}</td>
                    <td>{{$currency->symbol}} {{number_format($item->costsSingle_net,2)}} {{$currency->code}}</td>
                </tr>
                <tr>
                    <td colspan="1">
                        Voucher Code
                    </td>
                    <td colspan="3">
                        <ul>
                            @foreach($item->vouchers as $ind=>$voucher)
                                <li>

                                    @if($voucher->license == null)
                                        {{$voucher->voucher_code}}
                                    @else
                                        {{$voucher->voucher_code}}<br>
                                        {{$voucher->license->license_key}}
                                    @endif

                                    @if($ind < count($item->vouchers)-1)
                                    <br>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td><strong>Sub Total</strong></td>
                    <td>{{$currency->symbol}} {{number_format($model->totalSumOrderInclDiscount_net,2)}} {{$currency->code}}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td><strong>Tax ({{$model->vat_percentage}}% VAT)</strong></td>
                    <td>{{$currency->symbol}} {{number_format($model->totalSumOrderInclDiscount_net * $model->vat_percentage / 100,2)}} {{$currency->code}}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td><strong>Total</strong></td>
                    <td>{{$currency->symbol}} {{$model->totalSumOrderInclDiscount_gross}} {{$currency->code}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
