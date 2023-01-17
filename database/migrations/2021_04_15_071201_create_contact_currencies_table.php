<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_currencies', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('currency', 20)->nullable();
            $table->string('code', 3)->nullable();
            $table->string('symbol', 5)->nullable();
            $table->boolean('is_default')->default(0);
            $table->boolean('is_active')->nullable()->default(0);
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
        Schema::dropIfExists('contact_currencies');
    }
}
