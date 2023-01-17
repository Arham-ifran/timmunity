<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelPilotOrderItemVoucher extends Model
{
    use HasFactory;

    public function license(){
        return $this->belongsTo(License::class,'license_id');
    }
    public function redeemed_by(){
        return $this->hasOne(User::class,'customer_id');
    }
    public function item(){
        return $this->belongsTo(ChannelPilotOrderItem::class,'channel_pilot_order_item_id');
    }
}
