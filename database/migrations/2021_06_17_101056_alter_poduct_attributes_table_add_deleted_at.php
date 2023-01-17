<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPoductAttributesTableAddDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `product_attributes` ADD `deleted_at` TIMESTAMP NULL AFTER `created_by`;");
        DB::Statement("ALTER TABLE `product_attribute_values` ADD `deleted_at` TIMESTAMP NULL AFTER `attribute_value`;");
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
