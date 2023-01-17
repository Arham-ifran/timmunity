<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelPilotOrderItem extends Model
{
    use HasFactory;
    public function vouchers(){
        return $this->hasMany(ChannelPilotOrderItemVoucher::class,'channel_pilot_order_item_id');
    }
    public function order(){
        return $this->belongsTo(ChannelPilotOrder::class,'channel_pilot_order_id');
    }
}
