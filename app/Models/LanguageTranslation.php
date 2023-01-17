<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageTranslation extends Model
{
    protected $fillable = [
        'language_module_id', 'language_id', 'language_code', 'column_name','item_id','item_value', 'custom'
    ];

    public function languageModule()
    {
    	return $this->belongsTo('App\Models\LanguageModule', 'language_module_id');
    }

    public function language()
    {
    	return $this->belongsTo('App\Models\Languages', 'language_id');
    }
}
