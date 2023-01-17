<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\ActivityAttachments;

class ActivityMessages extends Model
{
    use HasFactory;
     protected $fillable = [
     	'log_user_id',
        'kss_subscription_id',
        'voucher_id',
        'quotation_id',
        'subject',
        'message'
    ];

    public function activity_message_users()
    {
        return $this->belongsTo(Admin::class,'log_user_id');

    }

      public function activity_attachments()
    {
        return $this->hasMany(ActivityAttachments::class,'send_msg_id');

    }
}
