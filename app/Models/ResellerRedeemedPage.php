<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use App\Models\User;
class ResellerRedeemedPage extends Model
{
    use HasFactory;
    protected $fillable = ['title','url','description','reseller_id','logo','terms_of_use','privacy_policy','imprint','email','phone','color','domain'];

    public function contacts()
    {
        return $this->belongsTo(Contact::class,'reseller_id');

    }


    public function user()
    {
        return $this->belongsTo(User::class,'reseller_id');

    }

    public function reseller_redeemed_page_navigations()
    {
        return $this->hasMany(ResellerRedeemedPageNavigation::class,'reseller_redeem_page_id');

    }


}
