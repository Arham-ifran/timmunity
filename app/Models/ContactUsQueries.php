<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsQueries extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'contact_us_queries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'created_at',
        'updated_at'
    ];
}
