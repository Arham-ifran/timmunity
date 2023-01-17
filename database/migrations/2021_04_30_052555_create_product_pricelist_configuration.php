<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricelistConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_pricelist_configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('country_group_id')->nullable();
            $table->string('website',65)->nullable();
            $table->boolean('selectable')->nullable();
            $table->string('promotion_code',20)->nullable();
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
        Schema::dropIfExists('product_pricelist_configuration');
    }
}
