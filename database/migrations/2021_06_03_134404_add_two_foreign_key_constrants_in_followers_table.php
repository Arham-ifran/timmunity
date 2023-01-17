<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoForeignKeyConstrantsInFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('followers', function (Blueprint $table) {
            $table->integer('voucher_id')->nullable()->after('kss_subscription_id');
            $table->foreign('voucher_id')->references('id')->on('vouchers');
            $table->bigInteger('quotation_id')->nullable()->unsigned()->after('voucher_id');
            $table->foreign('quotation_id')->references('id')->on('quotations');
            $table->bigInteger('contact_model_id')->nullable()->unsigned()->after('quotation_id');
            $table->foreign('contact_model_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('followers', function (Blueprint $table) {
            //
        });
    }
}
