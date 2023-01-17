<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeForeignKeyContraintsInActivityAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove foreign key contrainst from activity attachment table
        Schema::table('activity_attachments', function (Blueprint $table) {
            $table->dropForeign('activity_attachments_send_msg_id_foreign');
            $table->foreign('send_msg_id')
            ->references('id')->on('activity_messages')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_log_note_id_foreign');
            $table->foreign('log_note_id')
            ->references('id')->on('activity_log_notes')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_kss_subscription_id_foreign');
            $table->foreign('kss_subscription_id')
            ->references('id')->on('kaspersky_subscriptions')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_contact_id_foreign');
            $table->foreign('contact_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_quotation_id_foreign');
            $table->foreign('quotation_id')
            ->references('id')->on('quotations')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_voucher_id_foreign');
            $table->foreign('voucher_id')
            ->references('id')->on('vouchers')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_customer_id_foreign');
            $table->foreign('customer_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_product_id_foreign');
            $table->foreign('product_id')
            ->references('id')->on('products')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_sales_team_id_foreign');
            $table->foreign('sales_team_id')
            ->references('id')->on('sales_teams')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('activity_attachments_variant_id_foreign');
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
        Schema::table('activity_attachments', function (Blueprint $table) {
            //
        });
    }
}
