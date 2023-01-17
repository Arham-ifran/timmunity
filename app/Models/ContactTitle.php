<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTitle extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['title','abbreviation','salutation'];

    public function contact_titles(){
        return $this->belongsTo(ContactTitle::class,'country_id');
    }


    public function contacts(){
        return $this->hasOne(Contact::class,'title_id');
    }

    public function contact_address(){
        return $this->hasOne(ContactAddress::class,'title_id');
    }

}

