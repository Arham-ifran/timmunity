<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPriceListsTableAddParentAndType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_pricelists', 'parent_id'))
        {
            Schema::table('product_pricelists', function (Blueprint $table) {
                $table->integer('parent_id')->nullable();
            });
        }
        if (!Schema::hasColumn('product_pricelists', 'type'))
        {
            Schema::table('product_pricelists', function (Blueprint $table) {
                $table->integer('type')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
