<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductSalesTableChangeInvoicingPolicyComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `product_sales` CHANGE `invoice_policy` `invoice_policy` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0:Ordered Quantity, 1:Delivered Quantity';");

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
