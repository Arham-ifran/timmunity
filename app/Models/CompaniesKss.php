<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Companies;
class CompaniesKss extends Model
{
    use HasFactory;
    protected $table = "companies_kss";
    protected $fillable = [
        'company_id',
        'environment',
        'user_name',
        'password',
        'test_url',
        'prod_url',
    ];

    public function companies(){
        return $this->belongsTo(Companies::class,'company_id');
    }
}
