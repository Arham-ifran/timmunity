<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    use HasFactory;

    protected $table = 'product_sales';

    protected $fillable = [
        'product_id',
        'invoice_policy',
        'email_template_id',
        'description',
        'long_description',
        'channel_pilot_long_description'
    ];

}
