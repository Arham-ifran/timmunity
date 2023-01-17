<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyConstrantsInScheduleActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_activities', function (Blueprint $table) {
            $table->dropForeign('activity_scheduled_schedule_user_id_foreign');
            $table->dropForeign('activity_scheduled_assign_user_id_foreign');
            $table->dropForeign('activity_scheduled_kss_subscription_id_foreign');
            $table->dropForeign('activity_scheduled_activity_type_id_foreign');
            $table->dropColumn('schedule_user_id');
            $table->dropColumn('assign_user_id');
            $table->dropColumn('kss_subscription_id');
            $table->dropColumn('activity_type_id');
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
