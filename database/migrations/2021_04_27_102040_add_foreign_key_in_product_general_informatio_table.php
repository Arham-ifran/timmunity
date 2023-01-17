<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInProductGeneralInformatioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `products` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;");
        Schema::table('product_general_informations', function (Blueprint $table) {
            $table->foreign('product_id', 'product_general_informations_ibfk_3')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
            $table->dropForeign('product_general_informations_ibfk_3');
        });
    }
}
