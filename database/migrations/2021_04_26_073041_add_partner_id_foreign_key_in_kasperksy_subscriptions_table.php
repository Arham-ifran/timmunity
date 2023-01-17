<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartnerIdForeignKeyInKasperksySubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kaspersky_subscriptions', function (Blueprint $table) {
            $table->bigInteger('partner_id')->unsigned()->after('id');
            $table->foreign('partner_id')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kaspersky_subscriptions', function (Blueprint $table) {
            //
        });
    }
}
