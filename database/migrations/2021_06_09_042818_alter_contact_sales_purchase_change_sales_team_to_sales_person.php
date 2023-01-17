<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContactSalesPurchaseChangeSalesTeamToSalesPerson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_sales_purchase', function (Blueprint $table) {
            $table->dropIndex('sales_team_id');
            DB::Statement('ALTER TABLE `contact_sales_purchase` CHANGE `sales_team_id` `sales_person_id` INT(11) NULL DEFAULT NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_person', function (Blueprint $table) {
            //
        });
    }
}
