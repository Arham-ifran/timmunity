<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['created_by','updated_by','admin_id','type','name','company_id','street_1','street_2','city','state_id','zipcode','country_id','vat_id','job_position','phone','mobile','email','web_link','title_id','tag_id','image','internal_notes'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::user()->id;
            $model->updated_by = Auth::user()->id;
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id;
        });
    }


    public function companies(){
        return $this->belongsTo(Companies::class,'company_id');
    }

    public function countries(){
        return $this->belongsTo(ContactCountry::class,'country_id');
    }

    public function titles(){
        return $this->belongsTo(ContactTitle::class,'title_id');
    }
    public function fed_states(){
        return $this->belongsTo(ContactFedState::class,'state_id');
    }
    public function addresses(){
        return $this->hasMany(ContactAddress::class,'contact_id');
    }
}