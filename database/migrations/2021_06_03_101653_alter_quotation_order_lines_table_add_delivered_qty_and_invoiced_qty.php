<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterQuotationOrderLinesTableAddDeliveredQtyAndInvoicedQty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `quotation_order_lines` ADD `delivered_qty` INT NULL DEFAULT '0' AFTER `notes`, ADD `invoiced_qty` INT NULL DEFAULT '0' AFTER `delivered_qty`;");
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
