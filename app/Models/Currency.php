<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Companies;

class Currency extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'currencies';
    public $primaryKey  = 'id';
    protected $fillable = ['currency','country_id','code','symbol','is_default','is_active'];


    public function contact_countries(){
        return $this->belongsTo(ContactCountry::class,'country_id');
    }

    public function company_currencies() {
        return $this->hasOne(Companies::class,'currency_id');
    }

    public function bank_account_currencies() {
        return $this->hasOne(ContactBankAccount::class,'currency_id');
    }


}
