<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldKssCompanyLicense extends Model
{
    use HasFactory;
    protected $table = "old_kss_company_licenses";
    // public $timestamps = false;
    public function license(){
        return $this->belongsTo(OldKssSubscription::class,'kss_id');
    }
}
