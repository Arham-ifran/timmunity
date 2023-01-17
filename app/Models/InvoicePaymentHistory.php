<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentHistory extends Model
{
    use HasFactory;
    protected $table = 'invoice_payment_history';

    public function invoice(){
        return $this->belongsTo(Invoice::class,'invoice_id');
    }
}
