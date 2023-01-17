<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyConstrantsInKasperskySubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kaspersky_subscriptions', function (Blueprint $table) {
            $table->dropForeign('kaspersky_subscriptions_partner_id_foreign');
            $table->dropColumn('partner_id');
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
