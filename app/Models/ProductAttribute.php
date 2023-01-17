<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
class ProductAttribute extends Model
{
    use HasFactory;
    protected $fillable = ['attribute_name','display_type','variants_creation_mode','created_by','updated_by'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::user()->id;
            $model->updated_by = Auth::user()->id;
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id;
        });
    }
    public function attributeValue()
    {
        return $this->hasMany(ProductAttributeValue::class,'product_attribute_id');
    }
}
