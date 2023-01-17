<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationOtherInfoTag extends Model
{
    use HasFactory;
    protected $fillable = ['quotation_id ','tag_id '];

    public function tag(){
        return $this->belongsTo(ContactTag::class,'tag_id');
    }
}
