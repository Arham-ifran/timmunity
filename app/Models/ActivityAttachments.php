<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLogNotes;
use App\Models\ActivityMessages;
use App\Models\ScheduleActivities;

class ActivityAttachments extends Model
{
    use HasFactory;
    protected $fillable = [
        'kss_subscription_id',
        'log_note_id',
        'send_msg_id',
        'schedule_activity_id',
        'file_name',
        'file_extension',
        'module_name',
        'created_at',
        'updated_at',
    ];

    public function log_note_attachments()
    {
        return $this->belogsTo(ActivityLogNotes::class,'log_note_id');

    }

    public function send_message_attachments()
    {
        return $this->belogsTo(ActivityMessages::class,'send_msg_id');

    }
    public function schedule_activity_attachments()
    {
        return $this->belogsTo(ScheduleActivities::class,'schedule_activity_id');

    }
}
