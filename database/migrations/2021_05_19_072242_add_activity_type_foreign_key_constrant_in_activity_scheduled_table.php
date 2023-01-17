<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityTypeForeignKeyConstrantInActivityScheduledTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_scheduled', function (Blueprint $table) {
            $table->Integer('activity_type_id')->nullable()->after('quotations_id');
            $table->foreign('activity_type_id')->references('id')->on('activity_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_scheduled', function (Blueprint $table) {
            //
        });
    }
}
