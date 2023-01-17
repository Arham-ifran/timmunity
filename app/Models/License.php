<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;
    protected $fillable = [
        'license_key'
    ];
    public function voucher(){
        return $this->belongsTo(Voucher::class,'voucher_id');
    }
    public function quotation_voucher(){
        return $this->hasOne(QuotationOrderLineVoucher::class,'license_id');
    }
    public function customer(){
        return $this->belongsTo(User::class,'customer_id')->withTrashed();
    }
    public function reseller(){
        return $this->belongsTo(User::class,'reseller_id');
    }
    public function product(){
        return $this->belongsTo(Products::class,'product_id');
    }
    public function variation(){
        return $this->belongsTo(ProductVariation::class,'variation_id')->withTrashed();
    }
    public function quotation_order_line(){
        return $this->belongsTo(QuotationOrderLine::class,'quotation_order_line_id');
    }
    public function channel_pilot_order_item(){
        return $this->belongsTo(ChannelPilotOrderItem::class,'channel_pilot_order_item_id');
    }
}
