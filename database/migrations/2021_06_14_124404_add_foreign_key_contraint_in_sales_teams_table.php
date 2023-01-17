<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyContraintInSalesTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_teams', function (Blueprint $table) {
            $table->bigInteger('team_lead_id')->nullable()->unsigned()->after('id');
            $table->foreign('team_lead_id')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_teams', function (Blueprint $table) {
            //
        });
    }
}
