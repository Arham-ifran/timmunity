<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationDetail extends Model
{
    use HasFactory;

    public function attached_attribute()
    {
        return $this->belongsTo('App\Models\ProductAttachedAttribute', 'product_attached_attribute_id');
    }
    public function variations()
    {
        return $this->belongsTo('App\Models\ProductVariation', 'product_variation_id');
    }
}
