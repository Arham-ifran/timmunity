<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    public function customer(){
        return $this->belongsTo(User::class,'customer_id')->withTrashed();
    }
    public function voucherOrder(){
        return $this->belongsTo(VoucherOrder::class,'order_id');
    }
    public function license(){
        return $this->hasOne(License::class,'voucher_id');
    }
    public function getTotalPayableAttribute()
    {
        $quantity = 1;
        $vat_percentage = $this->voucherOrder->vat_percentage;
        $product_unit_price = $this->voucherOrder->unit_price;
        $discount = $this->voucherOrder->discount_percentage == null ? 0 : $this->voucherOrder->discount_percentage;
        $sub_total_amount = $product_unit_price * $quantity;
        $sub_total_amount = $sub_total_amount - ($sub_total_amount * $discount / 100);
        $total = $sub_total_amount ;
        foreach($this->voucherOrder->voucher_taxes as $ind => $voucher_tax){
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
    public function getTaxAmountAttribute()
    {
        $quantity = 1;
        $vat_percentage = $this->voucherOrder->vat_percentage;
        $product_unit_price = $this->voucherOrder->unit_price;
        $discount = $this->voucherOrder->discount_percentage == null ? 0 : $this->voucherOrder->discount_percentage;
        $sub_total_amount = $product_unit_price * $quantity;
        $sub_total_amount = $sub_total_amount - ($sub_total_amount * $discount / 100);
        $total = 0 ;
        foreach($this->voucherOrder->voucher_taxes as $ind => $voucher_tax){
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
