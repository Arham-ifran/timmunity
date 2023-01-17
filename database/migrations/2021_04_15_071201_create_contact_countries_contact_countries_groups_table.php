<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactCountriesContactCountriesGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_countries_contact_countries_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('country_group_id');
            $table->unsignedSmallInteger('country_id');
            $table->timestamps();
            $table->index(['country_group_id', 'country_id'], 'country_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_countries_contact_countries_groups');
    }
}
