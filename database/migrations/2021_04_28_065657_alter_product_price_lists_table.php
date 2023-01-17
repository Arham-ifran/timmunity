<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductPriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_pricelists');
        Schema::create('product_pricelists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50)->nullable();
            $table->integer('currency_id')->nullable();
            $table->bigInteger('created_by')->nullable()->unsigned();
            $table->bigInteger('updated_by')->nullable()->unsigned()->comment('Last user id updated by');
            $table->foreign('currency_id', 'product_pricelists_ibfk_1')->references('id')->on('currencies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_by', 'product_pricelists_ibfk_2')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'product_pricelists_ibfk_3')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
        Schema::dropIfExists('product_pricelists');
    }
}