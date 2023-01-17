<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50)->nullable();
            $table->boolean('can_be_sold')->nullable()->default(0)->comment('0:No, 1:Yes');
            $table->boolean('can_be_purchased')->default(0)->comment('0:No, 1:Yes');
            $table->string('product_type', 50)->nullable();
            $table->string('product_category', 50)->nullable();
            $table->double('sales_price')->nullable();
            $table->double('cost_price')->nullable();
            $table->string('customer_taxes', 100)->nullable();
            $table->string('internal_reference', 50)->nullable();
            $table->string('barcode', 30)->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('image', 100)->nullable();
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
        Schema::dropIfExists('products');
    }
}
