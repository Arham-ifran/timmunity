<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherPaymentReference extends Model
{
    use HasFactory;

    public function voucher_payment(){
        return $this->belongsTo(VoucherPayment::class,'voucher_payment_id');
    }
}
