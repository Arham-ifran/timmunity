<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPricelistConfiguration extends Model
{
    use HasFactory;
    protected $fillable = [
        'pricelist_id','country_group_id','country_id','website','selectable','promotion_code'
    ];
    public function country_group()
    {
        return $this->belongsTo(ContactCountryGroup::class,'country_group_id');
    }
    public function country()
    {
        return $this->belongsTo(ContactCountry::class,'country_id');
    }
    public function priceList()
    {
        return $this->belongsTo(ProductPriceList::class,'pricelist_id');
    }
}
