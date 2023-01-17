<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_countries', function (Blueprint $table) {
            $table->smallInteger('id', true);
            $table->string('name', 50)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('vat_label', 20)->nullable();
            $table->string('country_calling_code', 10)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('state_name', 50)->nullable();
            $table->char('state_code', 55)->nullable();
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('contact_countries');
    }
}
