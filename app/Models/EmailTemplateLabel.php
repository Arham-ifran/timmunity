<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmailTemplate;

class EmailTemplateLabel extends Model
{
    protected $fillable = [
        'email_template_id', 'label', 'value', 'status'
    ];

    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }
}
