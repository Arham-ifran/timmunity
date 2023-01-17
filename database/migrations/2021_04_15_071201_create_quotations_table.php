<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('customer_id')->nullable();
            $table->integer('pricelist_id')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('invoice_address')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('payment_terms')->nullable();
            $table->integer('payment_due_day')->nullable();
            $table->boolean('invoice_status')->default(0)->comment('0:Sales Order, 1:Quotation Sent,  2:Nothing to invoice, 3:To Invoice, 4:Fully Invoiced');
            $table->boolean('status')->default(0)->comment('0:save, 1:sent email,  2:send pro-forma invoice ,3:confirm without kss,  4: confirm');
            $table->timestamps();
            $table->index(['customer_id', 'pricelist_id'], 'customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
