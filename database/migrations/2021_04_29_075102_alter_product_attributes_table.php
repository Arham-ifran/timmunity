<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_attributes');
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('attribute_name',50)->nullable();
            $table->boolean('display_type')->nullable()->comment('1=radio,2=select,3=color');
            $table->boolean('variants_creation_mode')->nullable()->comment('1=Instant, 2=Dynamic,3=Never');
            $table->bigInteger('created_by')->nullable()->unsigned();
            $table->bigInteger('updated_by')->nullable()->unsigned()->comment('Last user id updated by');
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
        Schema::dropIfExists('product_attributes');
    }
}
