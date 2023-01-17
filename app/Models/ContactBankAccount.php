<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactBankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_number',
        "account_type",
        "account_title",
        "bank_id",
        "currency_id",
        "account_holder_name",

    ];
    public function contact_banks()
    {

        return $this->belongsTo(ContactBank::class,'bank_id');

    }

    public function contact_currencies() {
        return $this->belongsTo(Currency::class,'currency_id');
    }
}
