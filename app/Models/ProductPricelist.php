<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPriceList extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name','currency_id','created_by','updated_by',' is_active'];
    protected $table = "product_pricelists";
    protected static function booted()
    {
        
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id');
    }
    public function parent()
    {
        return $this->belongsTo(ProductPriceList::class,'parent_id');
    }
    public function childs()
    {
        return $this->hasMany(ProductPriceList::class,'parent_id');
    }
    public function rules()
    {
        return $this->hasMany('App\Models\ProductPricelistRule', 'pricelist_id');
    }
    public function configuration()
    {
        return $this->hasOne('App\Models\ProductPricelistConfiguration', 'pricelist_id');
    }
}
