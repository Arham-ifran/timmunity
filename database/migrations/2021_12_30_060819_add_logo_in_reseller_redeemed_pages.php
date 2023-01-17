<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoInResellerRedeemedPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reseller_redeemed_pages', function (Blueprint $table) {
            $table->string('logo')->nullable();
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reseller_redeemed_pages', function (Blueprint $table) {
            $table->dropIfExists('logo');
        });
    }
}
