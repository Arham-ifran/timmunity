<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sales', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('product_id')->nullable();
            $table->boolean('invoice_policy')->default(0)->comment('0:Individual, 1:Company');
            $table->integer('email_template_id')->nullable();
            $table->string('description', 200)->nullable();
            $table->timestamps();
            $table->index(['product_id', 'email_template_id'], 'product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sales');
    }
}
