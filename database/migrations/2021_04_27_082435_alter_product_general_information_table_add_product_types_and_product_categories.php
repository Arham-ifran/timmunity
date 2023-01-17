<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductGeneralInformationTableAddProductTypesAndProductCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasColumn('product_general_informations', 'product_type')) {
            Schema::table('product_general_informations', function (Blueprint $table) {
                $table->dropColumn('product_type');
            });
        }
        if (Schema::hasColumn('product_general_informations', 'product_category')) {
            Schema::table('product_general_informations', function (Blueprint $table) {
                $table->dropColumn('product_category');
            });
        }

        Schema::table('product_general_informations', function (Blueprint $table) {

            $table->unsignedInteger('product_type_id')->nullable()->after('product_id');
            $table->unsignedInteger('product_category_id')->nullable()->after('product_id');

            $table->foreign('product_type_id', 'product_general_informations_ibfk_1')->references('id')->on('product_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('product_category_id', 'product_general_informations_ibfk_2')->references('id')->on('product_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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

            $table->dropForeign('product_general_informations_ibfk_1');
            $table->dropForeign('product_general_informations_ibfk_2');
            $table->dropColumn('product_type_id');
            $table->dropColumn('product_category_id');

        });
    }
}
