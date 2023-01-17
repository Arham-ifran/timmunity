<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesContactsMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_contacts_members', function (Blueprint $table) {
             $table->id();
            $table->bigInteger('companies_id')->nullable();
            // $table->foreign('companies_id')->references('id')->on('companies');
            $table->bigInteger('contact_id')->nullable()->unsigned();
            // $table->foreign('contact_id')->references('id')->on('contacts');
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
        Schema::dropIfExists('companies_contacts_members');
    }
}
