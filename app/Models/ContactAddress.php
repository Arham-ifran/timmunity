<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactAddress extends Model
{
    use HasFactory;
    use SoftDeletes;
    // protected $dateFormat = 'U';
    protected $fillable = [
        "contact_id",
        'contact_name',
        "job_position",
        "type",
        "email",
        "phone",
        "mobile",
        "notes",
        "contact_image",
        "street_1",
        "street_2",
        "country_id",
        "city",
        "zipcode",
        "title_id",
        "state_id",


    ];

    public function contact_countries(){
        return $this->belongsTo(ContactCountry::class,'country_id');
    }

    public function contact_titles(){
        return $this->belongsTo(ContactTitle::class,'title_id');
    }

    public function contact_fed_states(){
        return $this->belongsTo(ContactFedState::class,'state_id');
    }

    public function contacts(){
        return $this->belongsTo(Contact::class,'contact_id');
    }

}
