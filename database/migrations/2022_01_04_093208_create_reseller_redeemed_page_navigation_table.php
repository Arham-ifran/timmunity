<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerRedeemedPageNavigationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_redeemed_page_navigation', function (Blueprint $table) {
            $table->id();
            $table->integer('reseller_redeem_page_id')->nullable()->index('reseller_redeem_page_id');
            $table->string('title')->nullable();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('reseller_redeemed_page_navigation');
    }
}
