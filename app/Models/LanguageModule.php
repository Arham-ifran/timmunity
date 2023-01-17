<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageModule extends Model
{
    protected $fillable = [
        'name', 'table', 'columns'
    ];
}
