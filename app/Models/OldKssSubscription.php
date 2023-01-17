<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldKssSubscription extends Model
{
    use HasFactory;
    protected $table = "old_kss_subscription";
    // public  $timestamps = false;
    public function voucher(){
        return $this->hasOne(OldKssCompanyLicense::class,'kss_id');
    }
}
