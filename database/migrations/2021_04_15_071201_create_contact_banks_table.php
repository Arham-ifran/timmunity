<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_banks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100)->nullable();
            $table->string('bank_identifier_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('street_2')->nullable();
            $table->string('street_1')->nullable();
            $table->string('city', 25)->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->mediumInteger('state_id')->nullable();
            $table->smallInteger('country_id')->nullable();
            $table->timestamps();
            $table->index(['state_id', 'country_id'], 'state_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_banks');
    }
}
