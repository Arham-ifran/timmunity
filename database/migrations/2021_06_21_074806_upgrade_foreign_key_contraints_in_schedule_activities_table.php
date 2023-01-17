<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeForeignKeyContraintsInScheduleActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_activities', function (Blueprint $table) {
            $table->dropForeign('schedule_activities_schedule_user_id_foreign');
            $table->foreign('schedule_user_id')
            ->references('id')->on('admins')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_assign_user_id_foreign');
            $table->foreign('assign_user_id')
            ->references('id')->on('admins')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_kss_subscription_id_foreign');
            $table->foreign('kss_subscription_id')
            ->references('id')->on('kaspersky_subscriptions')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_activity_type_id_foreign');
            $table->foreign('activity_type_id')
            ->references('id')->on('activity_types')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_contact_id_foreign');
            $table->foreign('contact_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->foreign('voucher_id')->references('id')->on('vouchers')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->dropForeign('schedule_activities_quotation_id_foreign');
            $table->foreign('quotation_id')
            ->references('id')->on('quotations')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_customer_id_foreign');
            $table->foreign('customer_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_product_id_foreign');
            $table->foreign('product_id')
            ->references('id')->on('products')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_sales_team_id_foreign');
            $table->foreign('sales_team_id')
            ->references('id')->on('sales_teams')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('schedule_activities_variant_id_foreign');
            $table->foreign('variant_id')
            ->references('id')->on('products')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
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
