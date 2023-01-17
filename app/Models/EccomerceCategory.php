<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EccomerceCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'category_name',
        'category_slug',
        'parent_category'
    ];
    public function parent(){
        return $this->belongsTo(EccomerceCategory::class,'parent_category');
    }
    public function child_categories(){
        return $this->hasMany(EccomerceCategory::class,'parent_category');
    }
}
