<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;

// class Manufacturer extends Authenticatable implements MustVerifyEmail
class Manufacturer extends Authenticatable
{
    use SoftDeletes;
    use  Notifiable;

    protected $guard = 'manufacture';
    protected $table = 'manufacturer';


    protected $fillable = [
        'manufacturer_name','manufacturer_email',  'password',
    ];
    protected $hidden = [
        'password',
    ];


    public function products(){
        return $this->hasMany('App\Models\Products','manufacturer_id','id');
    }
    public function members(){
        return $this->hasMany('App\Models\Manufacturer','associated_manufacturer_id','id');
    }
    public function main_manufacturer(){
        return $this->belongsTo('App\Models\Manufacturer','associated_manufacturer_id','id');
    }

    public function getMemberIdsAttribute(){
        return Manufacturer::where('associated_manufacturer_id',$this->id)->pluck('id')->toArray();
    }
}
