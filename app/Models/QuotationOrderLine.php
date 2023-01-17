<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationOrderLine extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo(Products::class,'product_id');
    }
    public function variation(){
        return $this->belongsTo(ProductVariation::class,'variation_id')->withTrashed();
    }
    public function variation_details(){
        return $this->hasMany(ProductVariationDetail::class,'product_variation_id','variation_id');
    }
    public function quotation_taxes(){
        return $this->hasMany(QuotationOrderLineTax::class,'quotation_order_line_id');
    }
    public function licenses(){
        return $this->hasMany(License::class,'quotation_order_line_id');
    }
    public function quotation(){
        return $this->belongsTo(Quotation::class,'quotation_id')->withTrashed();
    }
    public function vouchers(){
        return $this->hasMany(QuotationOrderLineVoucher::class,'quotation_order_line_id');
    }

    public function getInvoiceTotalAttribute()
    {
        $product = Products::with('sales')->where('id', $this->product_id)->first();
        $qty = 0 ;
        if($product->sales->invoice_policy == 0){
            $qty = $this->qty ;
        }else{
            $delivered_qty = $this->delivered_qty;
            $invoiced_qty = $this->invoiced_qty;
            $qty = $delivered_qty - $invoiced_qty;
        }
        $subtotal = $qty * $this->unit_price;
        $total = $subtotal;
        $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$this->id)->get();

        foreach($taxes as $o_tax)
        {
            if($o_tax->tax != null){
                switch($o_tax->tax->computation)
                {
                    case 0:
                        $total += $o_tax->tax->amount;
                        break;
                    case 1:
                        $total += $subtotal * $o_tax->tax->amount / 100;
                        break;
                }
            }
        }
        $total += $subtotal *  $this->quotation->vat_percentage / 100;
        return $total;
    }
}
