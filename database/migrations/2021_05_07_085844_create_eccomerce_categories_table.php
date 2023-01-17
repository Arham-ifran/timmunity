<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEccomerceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eccomerce_categories', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('category_name',200);
            $table->string('category_slug',200);
            $table->integer('parent_category')->nullable();
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
        Schema::dropIfExists('eccomerce_categories');
    }
}
