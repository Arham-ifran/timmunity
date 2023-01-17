<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Languages;
use App\Models\ActivityLogNotes;
use App\Models\ActivityMessages;
use App\Models\SchedualActivities;
use App\Models\Contact;
use App\Models\SalesTeam;
use App\Models\SalesTeamsMembers;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable;
    use SoftDeletes;
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'is_term_condition',
        'invitation_code',
        'last_login_on',
        'account_status',
        'email_signature',
        'lang_id',
        'timezone_id',
        'notification',
        'is_active',
        'is_sales_team_member',
        'mobile',
        'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
 /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, 'admin.password.reset', 'admins'));
    }


    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification('admin.verification.verify'));
    }
     public function languages()
    {
        return $this->belongsTo(Languages::class,'lang_id');

    }
     public function activity_log_notes()
    {
        return $this->hasOne(ActivityLogNotes::class,'log_user_id');

    }
    public function activity_send_messages()
    {
        return $this->hasOne(ActivityMessages::class,'log_user_id');

    }
    // Admin Relationship with Contact : Rizwan
    public function contacts()
    {
        return $this->hasOne(Contact::class,'admin_id');

    }
    public function schedule_by_users()
    {
        return $this->hasOne(SchedualActivities::class,'schedule_user_id');

    }
     public function assign_to_users()
    {
        return $this->hasOne(SchedualActivities::class,'assign_user_id');

    }
    public function team_members()
     {
        return $this->hasMany(SalesTeamsMembers::class,'member_id');
     }
     public function team_leads()
     {
        return $this->hasOne(SalesTeam::class,'team_lead_id');
     }
}
