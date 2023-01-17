<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use Illuminate\Database\Eloquent\SoftDeletes;
class ContactTag extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name','active'];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class,'contacts_tags','contact_id','tag_id');

    }
}
