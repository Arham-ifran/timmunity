<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeForeignKeyContraintsInActivityLogNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_log_notes', function (Blueprint $table) {
            $table->dropForeign('activity_log_notes_log_user_id_foreign');
            $table->foreign('log_user_id')
            ->references('id')->on('admins')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_log_notes_kss_subscription_id_foreign');
            $table->foreign('kss_subscription_id')
            ->references('id')->on('kaspersky_subscriptions')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_log_notes_contact_id_foreign');
            $table->foreign('contact_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_log_notes_quotation_id_foreign');
            $table->foreign('quotation_id')
            ->references('id')->on('quotations')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_log_notes_customer_id_foreign');
            $table->foreign('customer_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_log_notes_product_id_foreign');
            $table->foreign('product_id')
            ->references('id')->on('products')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_log_notes_sales_team_id_foreign');
            $table->foreign('sales_team_id')
            ->references('id')->on('sales_teams')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_log_notes_variant_id_foreign');
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
        Schema::table('activity_log_notes', function (Blueprint $table) {
            //
        });
    }
}
