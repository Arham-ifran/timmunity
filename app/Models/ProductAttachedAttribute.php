<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttachedAttribute extends Model
{
    use HasFactory;
    protected $table = 'product_attached_atributes';
    protected $fillable = [
        'attribute_id',
        'product_id'
    ] ;
    public function attributeValue()
    {
        return $this->hasMany(ProductAttachedAttributeValue::class,'product_attached_atribute_id');
    }
    public function allAttributeValue()
    {
        return $this->hasMany(ProductAttributeValue::class,'product_attribute_id','attribute_id');
    }
    public function attached_attribute_values(){
        return $this->belongsToMany(ProductAttributeValue::class, ProductAttachedAttributeValue::class,'product_attached_atribute_id','value_id');
    }
    public function attributeDetail()
    {
        return $this->hasOne(ProductAttribute::class,'id','attribute_id');
    }

}
