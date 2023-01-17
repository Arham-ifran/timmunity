<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPrductsTableAddProductTypeParentProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('product_type')->nullable()->default('0')->comment('0: License Based 1: SaaS Based');
            $table->integer('project_id')->nullable();
            $table->string('secondary_project_ids',200)->nullable()->comment('comma separated ids');
        });
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
