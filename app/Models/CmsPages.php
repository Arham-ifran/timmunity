<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPages extends Model
{
    use HasFactory;

    protected $fillable = [
		'title',
		'tracking_code',
		'seo_url',
		'is_static',
		'description',
		'meta_description',
		'meta_title',
		'meta_keywords',
		'show_in_header',
		'show_in_footer',
		'is_active',
		'is_homepage_listing',
		'image',
		'short_description',
	];
}
