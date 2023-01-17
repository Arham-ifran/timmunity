<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvoiceOrderLineTableChangeAmountsToDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `invoices` CHANGE `amount_paid` `amount_paid` FLOAT(11) NOT NULL;");
        DB::Statement("ALTER TABLE `invoices` CHANGE `invoice_total` `invoice_total` FLOAT(11) NOT NULL;");
        DB::Statement("ALTER TABLE `invoice_order_lines` CHANGE `amount` `amount` FLOAT(11) NOT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
