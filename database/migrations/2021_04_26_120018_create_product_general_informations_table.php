<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductGeneralInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_general_informations', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('product_id')->unsigned();
            $table->integer('product_type')->nullable();
            $table->integer('product_category')->nullable();
            $table->string('internal_reference',255)->nullable();
            $table->string('barcode',50)->nullable();
            $table->decimal('sales_price', 10, 6)->nullable();
            $table->decimal('customer_taxes', 4, 2)->nullable();
            $table->decimal('cost_price', 10, 6)->nullable();
            $table->text('internal_notes')->nullable();
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
        Schema::dropIfExists('product_general_informations');
    }
}
