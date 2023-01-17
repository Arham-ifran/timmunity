<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelpilotLog extends Model
{
    use HasFactory;
    protected $fillable = ['end_point','request_type','parmas','header','response','response_code'];
}
