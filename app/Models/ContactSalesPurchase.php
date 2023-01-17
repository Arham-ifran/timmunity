<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSalesPurchase extends Model
{
    use HasFactory;
    protected $table = "contact_sales_purchase";
    protected $fillable = ['contact_id','sales_team_id ','pricelist_id ','payment_terms'];
}
