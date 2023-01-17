<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SalesTeam;
use App\Models\Admin;

class SalesTeamsMembers extends Model
{
    use HasFactory;
     protected $fillable = ['sales_team_id','member_id'];

     public function sale_teams()
     {
        return $this->belongsTo(SalesTeam::class,'sales_team_id');
     }

     public function team_members()
     {
        return $this->belongsTo(Admin::class,'member_id');
     }

}
