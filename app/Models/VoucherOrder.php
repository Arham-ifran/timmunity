<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherOrder extends Model
{
    use HasFactory;
    public function vouchers()
    {
        return $this->hasMany(Voucher::class,'order_id');
    }
    public function product()
    {
        return $this->belongsTo(Products::class,'product_id');
    }
    public function variation()
    {
        return $this->belongsTo(ProductVariation::class,'variation_id')->withTrashed();
    }
    public function contact_country()
    {
        return $this->belongsTo(ContactCountry::class,'country_id');
    }
    public function voucher_taxes()
    {
        return $this->hasMany(VoucherOrderTax::class,'order_id');
    }
    public function reseller()
    {
        return $this->belongsTo(User::class,'reseller_id')->withTrashed();
    }
    public function distributor()
    {
        return $this->belongsTo(Distributor::class,'distributor_id');
    }
    public function invoices()
    {
        return $this->hasMany(VoucherPayment::class,'voucher_order_id');
    }
    public function invoice_detail()
    {
        return $this->hasMany(VoucherPaymentOrderDetail::class,'voucher_order_id');
    }
    public function getTotalPayableAttribute()
    {
        $quantity = $this->quantity;
        $vat_percentage = $this->vat_percentage;
        $product_unit_price = $this->unit_price * $this->exchange_rate;
        $discount = $this->discount_percentage == null ? 0 : $this->discount_percentage;
        $sub_total_amount = $product_unit_price ;
        $sub_total_amount = $sub_total_amount - ( $sub_total_amount * $discount / 100 );
        $sub_total_amount = $sub_total_amount + ( $sub_total_amount * $vat_percentage / 100 ) ;
        $sub_total_amount = $sub_total_amount * $quantity;
        $total = $sub_total_amount;

        foreach($this->voucher_taxes as $ind => $voucher_tax){
            if($voucher_tax->tax->type == 1)
            {
                $total += $sub_total_amount * $voucher_tax->tax->amount / 100;
            }
            else
            {
                $total += $voucher_tax->tax->amount ;
            }
        }
        return $total;
    }
    public function getRemainingTotalAttribute()
    {
        $quantity = $this->remaining_quantity;
        // $quantity = $this->quantity;
        $product_unit_price = $this->unit_price * $this->exchange_rate;
        $vat_percentage = $this->vat_percentage;
        $discount = $this->discount_percentage == null ? 0 : $this->discount_percentage;
        $sub_total_amount = $product_unit_price * $quantity;
        $sub_total_amount = $sub_total_amount - ($sub_total_amount * $discount / 100);
        $total = $sub_total_amount;

        foreach($this->voucher_taxes as $ind => $voucher_tax){
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

        $total_amount_paid = 0;
        foreach($this->invoice_detail as $invoice){
            $total_amount_paid += $invoice->payment->amount_paid;
        }
        return $total - $total_amount_paid ;
    }
    public function getTaxesAttribute()
    {
        $vat_label = $this->contact_country->vat_label;
        $vat_label = $vat_label == '' ? 'VAT' : $vat_label;
        $html = '';
        $count = count($this->voucher_taxes);
        foreach($this->voucher_taxes as $ind => $voucher_tax){
            $html .= $voucher_tax->tax->amount;
            $html .= $voucher_tax->tax->type==1 ? ' %' : '';
            if($ind < $count-1){
                $html .=', ';
            }
        }
        if($count > 0){
            $html .= ', ';
        }
        $html .= $this->vat_percentage.'% '.$vat_label;
        return $html;
    }
}
