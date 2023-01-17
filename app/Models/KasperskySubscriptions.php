<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;

class KasperskySubscriptions extends Model
{
    use HasFactory;
     protected $fillable = [
        'partner_id',
        'subscriber_id',
        'product_id',
        'license_key',
        'start_date',
        'end_date',
        'kss_error',
        'status'
    ];

      public function partners()
    {
        return $this->belongsTo(Contact::class,'partner_id');

    }

     public function products()
    {
        return $this->belongsTo(Products::class,'product_id');

    }
}
