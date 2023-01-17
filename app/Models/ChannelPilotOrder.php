<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelPilotOrder extends Model
{
    use HasFactory;

    public function items(){
        return $this->hasMany(ChannelPilotOrderItem::class,'channel_pilot_order_id');
    }
}
