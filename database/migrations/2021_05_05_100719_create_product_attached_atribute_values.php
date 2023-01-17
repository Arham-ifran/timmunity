<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttachedAtributeValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_attached_atribute_values', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('product_attached_atribute_id');
            $table->integer('value');
            $table->double('extra_price')->nullable();
            $table->boolean('is_active')->default(0)->comment('0:No, 1:Yes');;
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
        Schema::dropIfExists('product_attached_atribute_values');
    }
}
