<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResellerPackageRule extends Model
{
    use HasFactory;

    public function package()
    {
        return $this->belongsTo('App\Models\ResellerPackage', 'package_id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
    public function variation()
    {
        return $this->belongsTo('App\Models\ProductVariation', 'variation_id');
    }
}
