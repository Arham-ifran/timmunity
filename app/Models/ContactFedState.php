<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContactCountry;
use App\Models\Companies;
// use App\Models\ContactFedState;
class ContactFedState extends Model
{
    use HasFactory;
    protected  $fillable  = ['name','code','country_id'];


    public function contact_countries(){
        return $this->belongsTo(ContactCountry::class,'country_id');
    }

    public function companies_states(){
        return $this->hasOne(Companies::class,'state_id');
    }

    public function contacts(){
        return $this->hasOne(Contact::class,'state_id');
    }

    public function contact_address(){
        return $this->hasOne(ContactAddress::class,'state_id');
    }


}
