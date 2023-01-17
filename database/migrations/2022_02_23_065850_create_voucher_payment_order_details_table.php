<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherPaymentOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_payment_order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('voucher_payment_id')->nullable();
            $table->integer('voucher_order_id');
            $table->integer('reseller_id');
            $table->string('voucher_ids');
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
        Schema::dropIfExists('voucher_payment_order_details');
    }
}
