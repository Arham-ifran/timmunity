<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationOrderLineVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_order_line_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code');
            $table->integer('quotation_order_line_id');
            $table->integer('quotation_id');
            $table->integer('redeemed');
            $table->dateTime('redeemed_at')->nullable();
            $table->integer('license_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('quotation_order_line_vouchers');
    }
}
