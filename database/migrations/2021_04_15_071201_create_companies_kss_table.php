<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesKssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_kss', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('company_id')->nullable()->index('company_id');
            $table->boolean('environment')->nullable()->default(0);
            $table->string('user_name')->nullable();
            $table->string('password')->nullable();
            $table->string('test_url')->nullable();
            $table->string('prod_url')->nullable();
            $table->string('cert_url')->nullable();
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
        Schema::dropIfExists('companies_kss');
    }
}
