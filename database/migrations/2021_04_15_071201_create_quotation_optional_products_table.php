<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationOptionalProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_optional_products', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quotation_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('description')->nullable();
            $table->integer('qty')->nullable();
            $table->double('unit_price')->nullable();
            $table->timestamps();
            $table->index(['quotation_id', 'product_id'], 'quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_optional_products');
    }
}
