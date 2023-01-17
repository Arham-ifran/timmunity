<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationOptionalProduct extends Model
{
    use HasFactory;
    public function product(){
        return $this->belongsTo(Products::class,'product_id');
    }
    public function variation(){
        return $this->belongsTo(Products::class,'variation_id');
    }
}
