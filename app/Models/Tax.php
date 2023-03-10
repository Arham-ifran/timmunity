<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class Tax extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'type',
        'computation',
        'applicable_on',
        'amount',
        'is_active'
    ];
}
