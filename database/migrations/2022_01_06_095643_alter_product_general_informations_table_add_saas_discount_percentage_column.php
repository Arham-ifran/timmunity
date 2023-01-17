<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductGeneralInformationsTableAddSaasDiscountPercentageColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_general_informations', function (Blueprint $table) {
            $table->double('saas_discount_percentage')->nullable()->default(0);
        });
        Schema::table('voucher_orders', function (Blueprint $table) {
            $table->double('saas_discount_percentage')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('product_general_informations', function (Blueprint $table) {
        //     // $table->double('saas_discount_percentage')->nullable()->default(0);
        // });
        // Schema::table('voucher_orders', function (Blueprint $table) {
        //     $table->double('saas_discount_percentage')->nullable()->default(0);
        // });
    }
}
