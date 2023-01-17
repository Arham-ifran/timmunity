<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPasswordInManufacturerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturer', function (Blueprint $table) {
            $table->string('password')->nullable();
            $table->string('remember_me')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manufacturer', function (Blueprint $table) {
            $table->dropIfExists('password');
            $table->dropIfExists('remember_me');
        });
    }
}
