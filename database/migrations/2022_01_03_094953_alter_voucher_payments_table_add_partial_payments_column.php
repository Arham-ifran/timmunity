<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVoucherPaymentsTableAddPartialPaymentsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voucher_payments', function (Blueprint $table) {
            $table->double('total_amount');
            $table->double('amount_paid')->default(0);
            $table->integer('is_partial_paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher_payments', function (Blueprint $table) {
            //
        });
    }
}
