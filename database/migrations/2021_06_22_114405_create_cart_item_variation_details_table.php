<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemVariationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_item_variation_details', function (Blueprint $table) {
            $table->id();
            $table->integer('cart_item_id');
            $table->integer('attribute_id');
            $table->integer('attribute_value_id');
            $table->double('extra_price');
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
        Schema::dropIfExists('cart_item_variation_details');
    }
}
