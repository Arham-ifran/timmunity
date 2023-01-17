<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermsOfUseInResellerRedeemedPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reseller_redeemed_pages', function (Blueprint $table) {
            $table->longText('terms_of_use')->nullable();
            $table->longText('privacy_policy')->nullable();
            $table->longText('imprint')->nullable();
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
            $table->dropIfExists('terms_of_use');
            $table->dropIfExists('privacy_policy');
            $table->dropIfExists('imprint');
        });
    }
}
