<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContactCountry;
class ContactCountryGroup extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function contact_countries()
    {
        return $this->belongsToMany(ContactCountry::class,'contact_countries_contact_countries_groups','country_group_id','country_id');
    }
}
