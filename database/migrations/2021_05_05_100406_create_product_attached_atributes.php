<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttachedAtributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_attached_atributes');
        Schema::create('product_attached_atributes', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('attribute_id');
            $table->string('attribute_name',255);
            $table->integer('product_id');
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
        Schema::dropIfExists('product_attached_atributes');
    }
}
