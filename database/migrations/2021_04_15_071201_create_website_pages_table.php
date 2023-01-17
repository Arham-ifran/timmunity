<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_pages', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('view_name', 30)->nullable();
            $table->string('page_url', 100)->nullable();
            $table->string('website', 30)->nullable();
            $table->boolean('page_indexed')->default(0)->comment('0:No, 1:Yes');
            $table->boolean('is_published')->default(0)->comment('0:No, 1:Yes');
            $table->boolean('is_track')->default(0)->comment('0:No, 1:Yes');
            $table->dateTime('published_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_pages');
    }
}
