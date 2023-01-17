<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo(Products::class,'product_id');
    }
    public function variation(){
        return $this->belongsTo(ProductVariation::class,'variation_id');
    }
    public function variation_details(){
        return $this->hasMany(ProductVariationDetail::class,'product_variation_id','variation_id');
    }
}
