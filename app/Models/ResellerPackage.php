<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResellerPackage extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'package_name',
        'percentage',
        'model',
        'is_active'
    ];

    public function rules()
    {
        return $this->hasMany('App\Models\ResellerPackageRule', 'package_id');
    }
}
