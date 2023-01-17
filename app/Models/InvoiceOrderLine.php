<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceOrderLine extends Model
{
    use HasFactory;

    public function quotation_order_line(){
        return $this->belongsTo(QuotationOrderLine::class,'quotation_order_line_id');
    }
    public function getTotalAttribute()
    {
        $quotation_order_line = QuotationOrderLine::where('id', $this->quotation_order_line_id)->first();
        if($quotation_order_line){
            $product = Products::with('sales')->where('id', $quotation_order_line->product_id)->first();
            $qty = $this->invoiced_qty ;
            $subtotal = $qty * $quotation_order_line->unit_price;
            $total = $subtotal;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$quotation_order_line->id)->get();

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
            $total += $subtotal * $this->quotation_order_line->quotation->vat_percentage / 100;
            return $total;
        }
        return 0;
    }
    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id');
    }
}
