<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationOrderLineVoucher extends Model
{
    use HasFactory;
    public function license(){
        return $this->belongsTo(License::class,'license_id');
    }
    public function order_line(){
        return $this->belongsTo(QuotationOrderLine::class,'quotation_order_line_id');
    }
    public function quotation(){
        return $this->belongsTo(Quotation::class,'quotation_id');
    }
    public function customer(){
        return $this->belongsTo(User::class,'customer_id')->withTrashed();
    }
}
