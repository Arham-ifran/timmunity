<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->longtext('license_key');
            $table->integer('product_id');
            $table->integer('variation_id')->nullable();
            $table->integer('reseller_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('voucher_id')->nullable();
            $table->integer('status')->default(0)->comment('0: inactive 1: active 2: expired');
            $table->integer('is_used')->deafult(0)->comment('0: un-used 1: used');
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
        Schema::dropIfExists('licenses');
    }
}
