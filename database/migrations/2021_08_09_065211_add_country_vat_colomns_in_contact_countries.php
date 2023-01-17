<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryVatColomnsInContactCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_countries', function (Blueprint $table) {
            $table->double('vat_in_percentage')->default(0)->after('longitude');
            $table->boolean('is_default_vat')->default(0)->comment('1=Yes;0=No')->after('vat_in_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_countries', function (Blueprint $table) {
            //
        });
    }
}
