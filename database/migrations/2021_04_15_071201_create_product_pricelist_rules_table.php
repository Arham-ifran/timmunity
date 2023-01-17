<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricelistRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_pricelist_rules', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('pricelist_id')->nullable()->index('pricelist_id');
            $table->tinyInteger('apply_on')->default(0)->comment('0:All products, 1:Product Category, 2:Product, 3:Product variant');
            $table->string('category', 30)->nullable();
            $table->integer('min_qty')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('product', 30)->nullable();
            $table->string('variant', 30)->nullable();
            $table->tinyInteger('price_computation')->nullable()->default(0)->comment('0:Fixed Price, 1:Discount Percentage, 2:Formula');
            $table->double('fixed_value')->nullable();
            $table->double('percentage_value')->nullable();
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
        Schema::dropIfExists('product_pricelist_rules');
    }
}
