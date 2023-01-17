<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignContraintsInFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('followers', function (Blueprint $table) {
            $table->bigInteger('kss_subscription_id')->nullable()->after('id');
            $table->foreign('kss_subscription_id')->references('id')->on('kaspersky_subscriptions');
            $table->bigInteger('admin_user_id')->nullable()->unsigned()->after('kss_subscription_id');
            $table->foreign('admin_user_id')->references('id')->on('admins');
            $table->bigInteger('follower_id')->nullable()->unsigned()->after('admin_user_id');
            $table->foreign('follower_id')->references('id')->on('admins');
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
