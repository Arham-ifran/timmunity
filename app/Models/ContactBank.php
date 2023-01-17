<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactBank extends Model
{
    use HasFactory;
    protected $fillable = [
                'name',
                "bank_identifier_code",
                "phone",
                "email",
                "street_1",
                "street_2",
                "city",
                "zipcode",
                "state",
                "country_id",
                ];

    public function contact_countries(){
        return $this->belongsTo(ContactCountry::class,'country_id');
    }

    public function contact_bank_accounts()
    {

        return $this->hasOne(ContactBankAccount::class,'bank_id');

    }
}
