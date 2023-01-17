<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterProductSalesTableAddLongDescriptionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `product_sales` ADD `long_description` LONGBLOB NULL AFTER `description`;");
        DB::statement("ALTER TABLE `product_sales` ADD `channel_pilot_long_description` TEXT NULL AFTER `long_description`;");

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
