<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100)->nullable();
            $table->string('street_address', 100)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->mediumInteger('state_id')->nullable();
            $table->smallInteger('country_id')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website', 200)->nullable();
            $table->string('vat_id', 100)->nullable();
            $table->string('registration_no', 100)->nullable();
            $table->string('consultant_no', 100)->nullable();
            $table->string('customer_no', 100)->nullable();
            $table->smallInteger('currency_id')->nullable();
            $table->string('image', 100)->nullable();
            $table->timestamps();
            $table->index(['state_id', 'country_id', 'currency_id'], 'state_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
