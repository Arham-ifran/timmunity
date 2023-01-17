<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name',255);
            $table->integer('type')->default(0)->comment('0:None, 1:Sales, 2:Purchase');
            $table->integer('computation')->default(0)->comment('0:Fixed, 1:Percentage');
            $table->integer('applicable_on')->default(0)->comment('0:Customers, 1:Vendrors');
            $table->integer('amount');
            $table->integer('is_active')->default(0)->comment('0:No, 1:Yes');
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
        Schema::dropIfExists('taxes');
    }
}
