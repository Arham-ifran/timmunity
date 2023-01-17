<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelPilotOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_pilot_orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderIdExternal');
            $table->string('source');
            $table->dateTime('orderTime');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('customer_mobile');
            $table->string('customer_street');
            $table->string('customer_city');
            $table->string('customer_state');
            $table->string('currency');
            $table->double('totalSumItems_gross');
            $table->double('totalSumItems_net');
            $table->double('totalSumItemsInclDiscount_gross');
            $table->double('totalSumItemsInclDiscount_net');
            $table->double('totalSumOrder_gross');
            $table->double('totalSumOrder_net');
            $table->double('totalSumOrderInclDiscount_gross');
            $table->double('totalSumOrderInclDiscount_net');
            $table->double('vat_percentage');

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
        Schema::dropIfExists('channel_pilot_orders');
    }
}
