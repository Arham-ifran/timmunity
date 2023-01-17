<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeForeignKeyContraintsInFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('followers', function (Blueprint $table) {
            $table->dropForeign('followers_kss_subscription_id_foreign');
            $table->foreign('kss_subscription_id')
            ->references('id')->on('kaspersky_subscriptions')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_contact_id_foreign');
            $table->foreign('contact_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_follower_id_foreign');
            $table->foreign('follower_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_voucher_id_foreign');
            $table->foreign('voucher_id')
            ->references('id')->on('vouchers')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_quotation_id_foreign');
            $table->foreign('quotation_id')
            ->references('id')->on('quotations')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_contact_model_id_foreign');
            $table->foreign('contact_model_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_customer_id_foreign');
            $table->foreign('customer_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_product_id_foreign');
            $table->foreign('product_id')
            ->references('id')->on('products')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_sales_team_id_foreign');
            $table->foreign('sales_team_id')
            ->references('id')->on('sales_teams')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
            $table->dropForeign('followers_variant_id_foreign');
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
        Schema::table('followers', function (Blueprint $table) {
            //
        });
    }
}
