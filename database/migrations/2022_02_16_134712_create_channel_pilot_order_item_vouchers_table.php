<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelPilotOrderItemVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_pilot_order_item_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code');
            $table->integer('channel_pilot_order_item_id');
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
        Schema::dropIfExists('channel_pilot_order_item_vouchers');
    }
}
