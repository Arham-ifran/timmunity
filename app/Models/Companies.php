<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContactCountry;
use App\Models\ContactFedState;
use App\Models\Currency;
use App\Models\companykss;
class Companies extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'street_address',
        'city',
        'zipcode',
        'state_id',
        'country_id',
        'phone',
        'email',
        'website',
        'vat_id',
        'registration_no',
        'consultant_no',
        'customer_no',
        'currency_id'
    ];

    public function countries(){
        return $this->belongsTo(ContactCountry::class,'country_id');
    }
    public function states(){
        return $this->belongsTo(ContactFedState::class,'state_id');
    }

    public function currencies(){
        return $this->belongsTo(Currency::class,'currency_id');
    }
    public function companykss(){
        return $this->hasOne(companykss::class,'company_id');
    }

    public function contacts(){
        return $this->hasOne(Contact::class,'company_id');
    }
}
