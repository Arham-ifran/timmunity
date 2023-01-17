<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTableUpdateFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('products', 'product_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('product_type');
            });
        }
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('can_be_purchase')->nullable()->after('product_name');
            $table->boolean('can_be_sale')->nullable()->after('can_be_purchase');
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
            $table->dropColumn(['can_be_purchase', 'can_be_sale']);
        });
    }
}
