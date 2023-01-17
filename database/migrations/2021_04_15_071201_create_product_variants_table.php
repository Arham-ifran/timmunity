<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id')->nullable()->index('product_id');
            $table->string('duration', 30)->nullable();
            $table->string('licenses_count', 30)->nullable();
            $table->tinyInteger('type')->default(0)->comment('0:Direct, 1:Voucher');
            $table->double('cost_price')->nullable();
            $table->double('sale_price')->nullable();
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
        Schema::dropIfExists('product_variants');
    }
}
