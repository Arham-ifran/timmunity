<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherPaymentReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_payment_references', function (Blueprint $table) {
            $table->id();
            $table->integer('voucher_payment_id');
            $table->enum('method', ['Cash', 'Bank Transfer', 'Online Payment']);
            $table->string('transaction_id', 250)->nullable();
            $table->double('amount');
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
        Schema::dropIfExists('voucher_payment_references');
    }
}
