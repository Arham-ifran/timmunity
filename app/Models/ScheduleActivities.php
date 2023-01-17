<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityTypes;
use App\Models\ActivityAttachments;
use App\Models\Admin;

class ScheduleActivities extends Model
{
    use HasFactory;
    protected $fillable = [
        'schedule_user_id',
        'assign_user_id',
        'kss_subscription_id',
        'voucher_id',
        'quotation_id',
        'activity_type_id',
        'due_date',
        'summary',
        'details',
        'status',
    ];

    public function activity_types()
    {
        return $this->belongsTo(ActivityTypes::class,'activity_type_id');

    }
    
     public function schedule_by_users()
    {
        return $this->belongsTo(Admin::class,'schedule_user_id');

    }
     public function assign_to_users()
    {
        return $this->belongsTo(Admin::class,'assign_user_id');

    }
    public function activity_attachments()
    {
        return $this->hasMany(ActivityAttachments::class,'schedule_activity_id');

    }
     
}
