<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScheduleActivities;

class ActivityTypes extends Model
{
    use HasFactory;
     protected $fillable = [
     	'name',
        'status',
    ];

     public function schedule_activity_types()
    {
        return $this->belongsTo(ScheduleActivities::class,'activity_type_id');

    }
}
