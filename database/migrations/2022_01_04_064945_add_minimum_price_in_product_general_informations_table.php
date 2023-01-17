<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimumPriceInProductGeneralInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_general_informations', function (Blueprint $table) {
            $table->string('minimum_price')->nullable();
            $table->string('maximum_price')->nullable();
            $table->string('promotion_start_date')->nullable();
            $table->string('promotion_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_general_informations', function (Blueprint $table) {
            $table->dropIfExists('minimum_price');
            $table->dropIfExists('maximum_price');
            $table->dropIfExists('promotion_start_date');
            $table->dropIfExists('promotion_end_date');
        });
    }
}
