<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherPaymentOrderDetail extends Model
{
    use HasFactory;
    public function voucher_order(){
        return $this->belongsTo(VoucherOrder::class,'voucher_order_id');
    }
    public function payment(){
        return $this->belongsTo(VoucherPayment::class,'voucher_payment_id');
    }
    public function getVouchersAttribute()
    {
        $voucher_ids = explode(',', $this->voucher_ids);
        $vouchers = Voucher::whereIn('id',$voucher_ids )->get();
        return $vouchers;
    }
    public function getTotalPayableAttribute()
    {
        $total = 0;
        $detail = $this;

        $quantity = count(explode(',', $detail->voucher_ids));
        $vat_percentage = $detail->voucher_order->vat_percentage;
        $product_unit_price = $detail->voucher_order->unit_price;
        $discount = $detail->voucher_order->discount_percentage == null ? 0 : $this->voucher_order->discount_percentage;
        $sub_total_amount = $product_unit_price * $quantity;
        $sub_total_amount = $sub_total_amount - ($sub_total_amount * $discount / 100);
        $total += $sub_total_amount ;
        foreach($detail->voucher_order->voucher_taxes as $ind => $voucher_tax){
            if($voucher_tax->tax->type == 1)
            {
                $total += $sub_total_amount * $voucher_tax->tax->amount / 100;
            }
            else
            {
                $total += $voucher_tax->tax->amount ;
            }
        }

        $total += $sub_total_amount * $vat_percentage / 100;
        return $total;
    }
}
