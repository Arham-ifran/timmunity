<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyConstraintsInActivityScheduledTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_scheduled', function (Blueprint $table) {
            $table->bigInteger('schedule_user_id')->unsigned()->after('id');
            $table->foreign('schedule_user_id')->references('id')->on('admins');
            $table->bigInteger('assign_user_id')->unsigned()->after('schedule_user_id');
            $table->foreign('assign_user_id')->references('id')->on('admins');
            $table->bigInteger('kss_subscription_id')->nullable()->after('assign_user_id');
            $table->foreign('kss_subscription_id')->references('id')->on('kaspersky_subscriptions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_scheduled', function (Blueprint $table) {
            //
        });
    }
}
