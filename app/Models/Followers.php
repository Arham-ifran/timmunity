<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;

class Followers extends Model
{
    use HasFactory;
    protected $fillable = [
        'kss_subscription_id',
        'admin_user_id',
        'follower_id',
        'module_type',
        'follower_type'
    ];

    public function contacts()
    {
        return $this->belongsTo(Contact::class,'follower_id')->withTrashed();

    }
}

