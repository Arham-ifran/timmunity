<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGeneralInformation extends Model
{
    protected $table = 'product_general_informations';
    use HasFactory;
    protected $fillable = [
        'product_id',
        'product_category_id',
        'product_type_id',
        'internal_reference',
        'barcode',
        'sales_price',
        'cost_price',
        // 'download_link',
        // 'eccomerce_category',
        'voucher_discount_percentage',
        'internal_notes',
        'saas_discount_percentage'
        // 'minimum_price',
        // 'maximum_price',
        // 'promotion_start_date',
        // 'promotion_end_date',
    ];
}
