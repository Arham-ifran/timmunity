<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductPricelistRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `product_pricelist_rules` CHANGE `category` `category_id` INT NULL DEFAULT NULL;");
        DB::Statement("ALTER TABLE `product_pricelist_rules` CHANGE `product` `product_id` INT NULL DEFAULT NULL;");
        DB::Statement("ALTER TABLE `product_pricelist_rules` CHANGE `variant` `variation_id` INT NULL DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
