<?php

namespace App\Models;

use App\Models\Contact;
use App\Models\Companies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompaniesContactsMembers extends Model
{
    use HasFactory;
    protected $fillable = ['company_id','contact_id'];

    public function contacts_members()
    {
       return $this->belongsTo(Contact::class,'contact_id');
    }

    public function companies_members()
    {
       return $this->belongsTo(Companies::class,'companies_id');
    }
}
