<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSiteSettingsTableAddAccountIngos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->integer('account_inactivity_time_limit')->nullable()->default(30);
            $table->integer('account_inactivity_first_notification')->nullable()->default(5);
            $table->integer('account_inactivity_second_notification')->nullable()->default(3);
            $table->integer('account_inactivity_third_notification')->nullable()->default(1);
            $table->text('account_soft_delete_time_limit')->nullable()->default(30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {

        });
    }
}
