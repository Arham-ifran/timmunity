<?php

namespace App\Models;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contact()
    {
        return $this->hasOne(Contact::class,'user_id');
    }
    public function cart()
    {
        return $this->hasOne(Cart::class,'customer_id')->where('is_checkout', 0);
    }

    public function reseller_redeem_page(){
        return $this->hasOne(ResellerRedeemedPage::class,'reseller_id');
    }


    /**
     * Send the password reset notification.
     * @note: This override Authenticatable methodology
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification('verification.verify'));
    }

    public function getTotalCreditAmountAttribute()
    {
        $user_id = $this->id;
        $voucher_payment_total = VoucherPayment::whereHas('details.voucher_order',function($query) use($user_id){
            $query->where('reseller_id', $user_id);
        })->sum('total_amount');
        $voucher_payment_paid = VoucherPayment::whereHas('details.voucher_order',function($query) use($user_id){
            $query->where('reseller_id', $user_id);
        })->sum('amount_paid');
        return $voucher_payment_total-$voucher_payment_paid;
    }
    public function getCreditLimitAttribute()
    {
        $credit_limit = $this->contact->reseller_credit_limit;
        if($credit_limit == null){
            $credit_limit = SiteSettings::first()->reseller_credit_limit;
        }
        return $credit_limit;
    }
}
