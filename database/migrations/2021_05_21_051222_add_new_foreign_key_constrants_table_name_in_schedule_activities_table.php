<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewForeignKeyConstrantsTableNameInScheduleActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_activities', function (Blueprint $table) {
            $table->bigInteger('schedule_user_id')->unsigned()->after('id');
            $table->foreign('schedule_user_id')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->bigInteger('assign_user_id')->unsigned()->after('schedule_user_id');
            $table->foreign('assign_user_id')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->bigInteger('kss_subscription_id')->nullable()->after('assign_user_id');
            $table->foreign('kss_subscription_id')->references('id')->on('kaspersky_subscriptions')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->Integer('activity_type_id')->nullable()->after('quotations_id');
            $table->foreign('activity_type_id')->references('id')->on('activity_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_activities', function (Blueprint $table) {
            //
        });
    }
}
