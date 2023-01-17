<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsResellerChangedToResellerRedeemedPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reseller_redeemed_pages', function (Blueprint $table) {
            $table->boolean('is_reseller_changed')->default(0)->comment('0:No, 1:Yes')->after('reseller_id');
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
            //
        });
    }
}
