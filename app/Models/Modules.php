<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Modules extends Model
{
    use HasFactory;
    protected $fillable = [
        'module_name',
        'created_at',
        'updated_at',
    ];
    public function permissions()
    {
        return $this->hasMany(Permission::class,'module_id');

    }
}
