<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPricelistRule extends Model
{
    use HasFactory;
    protected $fillable = [
        'pricelist_id ',
        'apply_on',
        'min_qty',
        'start_date',
        'end_date',
        'price_computation',
        'product_id',
        'category_id',
        'variation_id',
        'fixed_value',
        'percentage_value'
    ]   ;


}
