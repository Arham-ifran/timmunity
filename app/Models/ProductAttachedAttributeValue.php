<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttachedAttributeValue extends Model
{
    use HasFactory;
    protected $table = 'product_attached_atribute_values';
    protected $fillable = [
        'product_attached_attribute_id',
        'value',
        'extra_price'
    ] ;
    public function attributeValueDetail(){
        return $this->belongsTo(ProductAttributeValue::class,'value_id');
    }
    public function attachedAttribute(){
        return $this->belongsTo(ProductAttachedAttribute::class,'product_attached_atribute_id');
    }
}
