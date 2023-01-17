<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdColomnInContactCountriesContactCountriesGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_countries_contact_countries_groups', function (Blueprint $table) {
            $table->integer('country_id')->nullable()->after('country_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_countries_contact_countries_groups', function (Blueprint $table) {
            //
        });
    }
}
