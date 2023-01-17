<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Languages extends Model
{
    use HasFactory;
    protected $table = "languages";
    protected $fillable = ['name','iso_code','local_code','image','is_active'];

    public function admin_users()
    {
        return $this->hasOne(Admin::class,'lang_id');

    }
}
