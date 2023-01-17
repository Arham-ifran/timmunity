<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SalesTeamsMembers;
use App\Models\Admin;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesTeam extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name','team_lead_id','type','invoicing_target'];

    public function sale_teams()
    {
        return $this->hasMany(SalesTeamsMembers::class,'sales_team_id');

    }
    public function team_leads()
    {
        return $this->belongsTo(Admin::class,'team_lead_id');

    }
     // Schedule Activity and Activity Relations
     public function schedule_activities(){
        return $this->hasMany(ScheduleActivities::class,'sales_team_id');
    }
    public function activity_attachments(){
        return $this->hasMany(ActivityAttachments::class,'sales_team_id');
    }
    public function activity_log_notes(){
        return $this->hasMany(ActivityLogNotes::class,'sales_team_id');
    }
    public function activity_messages(){
        return $this->hasMany(ActivityMessages::class,'sales_team_id');
    }
}
