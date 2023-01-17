<?php

namespace App\Models;

use App\Models\Companies;
use App\Models\ContactCountryGroup;
use App\Models\ContactFedState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCountry extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'country_code', 'vat_label', 'country_calling_code', 'currency', 'state_name', 'state_code', 'image','latitude','longitude','vat_in_percentage',
        'is_default_vat'];

    public function contact_fed_states()
    {
        return $this->hasOne(ContactFedState::class, 'country_id');
    }

    public function contact_country_groups()
    {
        return $this->belongsToMany(ContactCountryGroup::class, 'contact_countries_contact_countries_groups', 'country_group_id', 'country_id');
    }

    public function contact_banks()
    {
        return $this->hasOne(ContactBank::class, 'country_id');
    }

    public function contact_currencies()
    {
        return $this->hasOne(Currency::class, 'country_id');
    }

    public function company_countries()
    {
        return $this->hasOne(Companies::class, 'country_id');
    }

    public function contacts()
    {
        return $this->hasOne(Contact::class, 'country_id');
    }

    public function contact_address()
    {
        return $this->hasOne(ContactAddress::class, 'country_id');
    }
}
