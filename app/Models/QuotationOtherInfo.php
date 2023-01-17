<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationOtherInfo extends Model
{
    use HasFactory;
    protected $table = 'quotation_other_info';
    protected $fillable = [
        'quotation_id ',
        'salesperson_id ',
        'sales_team_id ',
        'customer_reference',
        'online_signature',
        'online_payment',
        'delivery_date'
    ];

    public function sales_person(){
        return $this->belongsTo(Admin::class,'salesperson_id')->withTrashed();
    }
    public function sales_team(){
        return $this->belongsTo(SalesTeam::class,'sales_team_id')->withTrashed();
    }
    public function tags(){
        return $this->hasMany(QuotationOtherInfoTag::class,'quotation_id','quotation_id');
    }

}
