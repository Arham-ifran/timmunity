<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 30)->nullable();
            $table->boolean('display_type')->default(0)->comment('0:Radio 1:Select');
            $table->tinyInteger('variants_creation_mode')->default(0)->comment('0:Instant, 1:Dynamic, 2:Never');
            $table->string('value', 50)->nullable();
            $table->boolean('is_checked')->default(0)->comment('0:Un-Checked, 1:Checked');
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
