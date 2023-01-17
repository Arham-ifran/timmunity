<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeginKeyConstraintContactIdFromContactAdresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_addresses', function (Blueprint $table) {
                $table->dropForeign('contact_addresses_ibfk_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_adresses', function (Blueprint $table) {
            //
        });
    }
}
