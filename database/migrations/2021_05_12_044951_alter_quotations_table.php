<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->bigInteger('customer_id')->unsigned()->nullable()->change();
            $table->bigInteger('pricelist_id')->nullable()->change();
            $table->bigInteger('invoice_address')->unsigned()->nullable()->change();
            $table->bigInteger('delivery_address')->unsigned()->nullable()->change();
            $table->bigInteger('payment_terms')->unsigned()->nullable()->change();
            //$table->integer('payment_due_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->integer('id', true)->change();
            $table->integer('customer_id')->nullable()->change();
            $table->integer('pricelist_id')->nullable()->change();
            $table->dateTime('expires_at')->nullable()->change();
            $table->string('invoice_address')->nullable()->change();
            $table->string('delivery_address')->nullable()->change();
            $table->string('payment_terms')->nullable()->change();
        });
    }
}
