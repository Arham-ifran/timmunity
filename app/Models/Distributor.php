<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Distributor extends Authenticatable
{
    use SoftDeletes;
    use  Notifiable;

    // protected $guard = 'manufacture';
    // protected $table = 'manufacturer';


    protected $fillable = [
        'name','email',  'password',
    ];
    protected $hidden = [
        'password',
    ];

    public function details(){
        return $this->hasMany('App\Models\DistributorProductDetail','distributor_id');
    }
}
