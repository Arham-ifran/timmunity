<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmailTemplateLabel;

class EmailTemplate extends Model
{
    protected $fillable = [
        'type', 'subject', 'content', 'status'
    ];

    public function emailTemplateLabels()
    {
        return $this->hasMany(EmailTemplateLabel::class, 'email_template_id');
    }
}
