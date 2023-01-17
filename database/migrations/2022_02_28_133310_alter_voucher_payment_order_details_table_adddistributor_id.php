<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVoucherPaymentOrderDetailsTableAdddistributorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voucher_payment_order_details', function (Blueprint $table) {
            $table->integer('distributor_id')->nullable();
            $table->integer('reseller_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher_payment_order_details', function (Blueprint $table) {
            //
        });
    }
}
