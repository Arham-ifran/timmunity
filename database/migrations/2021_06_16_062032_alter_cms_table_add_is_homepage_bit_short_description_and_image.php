<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCmsTableAddIsHomepageBitShortDescriptionAndImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->boolean('is_homepage_listing')->default(0)->comment('Show the Page on the Homepage of Website')->after('meta_description');
            $table->string('short_description',255)->default(0)->nullable()->after('is_homepage_listing');
            $table->string('image',255)->default(0)->nullable()->after('short_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
