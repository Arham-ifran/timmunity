<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorProductDetail extends Model
{
    use HasFactory;
    protected $fillable =['is_active','extra_price'];
    public function product(){
        return $this->belongsTo('App\Models\Products','product_id');
    }
}
