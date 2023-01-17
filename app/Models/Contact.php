<?php

namespace App\Models;

use App\Models\Admin;
use App\Models\Followers;
use App\Models\ContactTag;
use App\Models\ResellerRedeemedPage;
use App\Models\KasperskySubscriptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'contacts';
    use SoftDeletes;
    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'street_1',
        'street_2',
        'email',
        'mobile',
        'phone',
        'city',
        'zipcode',
        'job_position',
        'company_type',
        'company_id',
        // 'image',
        'web_link',
        'state_id',
        'country_id',
        'admin_id',
        'type',
        'title_id',
        'vat_id',
        'status',
        'internal_notes'
    ];

    public function contact_tags(){
        return $this->belongsToMany(ContactTag::class,'contacts_tags','contact_id','tag_id');
    }
    public function companies(){
        return $this->belongsTo(Companies::class,'company_id');
    }
    public function reseller_package(){
        return $this->belongsTo(ResellerPackage::class,'reseller_package_id');
    }
    public function contact_countries(){
        return $this->belongsTo(ContactCountry::class,'country_id');
    }
    public function contact_titles(){
        return $this->belongsTo(ContactTitle::class,'title_id');
    }
    public function contact_fed_states(){
        return $this->belongsTo(ContactFedState::class,'state_id');
    }
    public function contact_addresses(){
        return $this->hasMany(ContactAddress::class,'contact_id');
    }
    public function kss_subscriptions(){
        return $this->hasOne(KasperskySubscriptions::class,'partner_id');

    }
    public function sales_purchase(){
        return $this->hasOne(ContactSalesPurchase::class,'contact_id');

    }
    // Contact Relationship with admin : Rizwan
    public function admin_users(){
        return $this->belongsTo(Admin::class,'admin_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function followers(){
        return $this->hasMany(Followers::class,'follower_id');
    }
    // Schedule Activity and Activity Relations
    public function schedule_activities(){
        return $this->hasMany(ScheduleActivities::class,'contact_id');
    }
    public function activity_attachments(){
        return $this->hasMany(ActivityAttachments::class,'contact_id');
    }
    public function activity_log_notes(){
        return $this->hasMany(ActivityLogNotes::class,'contact_id');
    }
    public function activity_messages(){
        return $this->hasMany(ActivityMessages::class,'contact_id');
    }
    public function reseller_redeem_page(){
        return $this->hasOne(ResellerRedeemedPage::class,'reseller_id');
    }
}
