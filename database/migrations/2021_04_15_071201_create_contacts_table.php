<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->string('street_1', 100)->nullable();
            $table->string('street_2', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('city', 20)->nullable();
            $table->integer('company_id')->nullable()->comment('companies_table');
            $table->mediumInteger('state_id')->nullable();
            $table->smallInteger('country_id')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->string('job_position', 100)->nullable();
            $table->boolean('company_type')->nullable()->default(1)->comment('1=individual,2=company');
            $table->integer('title_id')->nullable()->comment('contacts_titles_table');
            $table->string('web_link')->nullable();
            $table->string('vat_id', 100)->nullable();
            $table->boolean('type')->default(0)->comment('0:Individual, 1:Company');
            $table->string('image', 100)->nullable();
            $table->timestamps();
            $table->index(['company_id', 'state_id', 'country_id', 'title_id'], 'company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
