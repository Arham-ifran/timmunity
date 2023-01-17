<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerRedeemedPageNavigation extends Model
{
    use HasFactory;
    protected $table = 'reseller_redeemed_page_navigation'; 

    public function reseller_redeemed_page()
    {
        return $this->hasOne(ResellerRedeemedPage::class,'reseller_redeem_page_id');

    }
}
