<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by')->nullable()->unsigned();
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->bigInteger('admin_id')->nullable()->unsigned();
            $table->boolean('type')->nullable()->default(1)->comment('1=individual,2=company');
            $table->string('name', 65)->nullable();
            $table->integer('company_id')->nullable()->comment('companies_table');
            $table->string('street_1', 100)->nullable();
            $table->string('street_2', 100)->nullable();
            $table->string('city', 20)->nullable();
            $table->mediumInteger('state_id')->nullable();
            $table->string('zipcode', 7)->nullable();
            $table->smallInteger('country_id')->nullable();
            $table->string('vat_id', 100)->nullable();
            $table->string('job_position', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('web_link')->nullable();
            $table->integer('title_id')->nullable();
            $table->bigInteger('tag_id')->nullable()->comment('customer_titles_table');
            $table->string('image', 100)->nullable();
            $table->boolean('status')->nullable()->comment('1=active,2=archive');
            $table->timestamps();
            $table->index(['company_id', 'created_by','updated_by','admin_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
