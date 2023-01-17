<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelPilotOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_pilot_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('channel_pilot_order_id');
            $table->string('idExternal');
            $table->string('article');
            $table->string('ean');
            $table->integer('qty');
            $table->double('costsSingle_gross');
            $table->double('costsSingle_net');
            $table->double('discountSingle_gross');
            $table->double('discountSingle_net');
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
        Schema::dropIfExists('channel_pilot_order_items');
    }
}
