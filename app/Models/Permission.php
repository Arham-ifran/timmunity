<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Modules;

class Permission extends Model
{
    use HasFactory;
      protected $fillable = [
        'name',
        'guard_name',
        'module_id',
        'created_at',
        'updated_at',
    ];

    public function permissions()
    {
        return $this->belogsTo(Modules::class,'module_id');

    }
}
