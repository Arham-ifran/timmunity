<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('variation_id')->nullable();
            $table->integer('reseller_id')->comment('Users Table Reference');
            $table->string('street_address');
            $table->string('city');
            $table->integer('country_id');
            $table->integer('quantity');
            $table->integer('remaining_quantity');
            $table->integer('used_quantity');
            $table->integer('total_amount');
            $table->integer('vat_percentage');
            $table->double('vat_amount');
            $table->string('message');
            $table->integer('status')->default(0)->comment('0: Pending, 1: Approved, 2: Rejected');
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
        Schema::dropIfExists('voucher_orders');
    }
}
