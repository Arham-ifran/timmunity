<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTableAddProductTypesAndProductCategoriesWithForeignKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('created_by', 'products_ibfk_3')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'products_ibfk_4')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_ibfk_3');
            $table->dropForeign('products_ibfk_4');
        });
    }
}