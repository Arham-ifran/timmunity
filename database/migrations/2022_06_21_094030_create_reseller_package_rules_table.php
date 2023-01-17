<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerPackageRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_package_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id')->nullable();
            $table->enum('apply_on',[0,1,2]);   // 0: all Products, 1: Specific Product, 2: Specific Variation
            $table->enum('model',[0,1])->nullable();    // 0: Incremental, 1: Decremental  
            $table->double('percentage')->nullable();  
            $table->integer('product_id')->nullable();  
            $table->integer('variation_id')->nullable();  
            $table->integer('use_default')->default(0);  
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
        Schema::dropIfExists('reseller_package_rules');
    }
}
