<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('quotation_id');
            $table->integer('status')->default(0)->comment("0:Draft 1:Confirmed 2:Cancelled");
            $table->integer('is_paid')->default(0)->comments('0: No 1: Yes');
            $table->integer('is_partially_paid')->default(0)->comments('0: No 1: Yes');
            $table->integer('invoice_total');
            $table->integer('amount_paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
