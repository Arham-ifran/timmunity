<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTeamsMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_teams_members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sales_team_id')->nullable();
            $table->foreign('sales_team_id')->references('id')->on('sales_teams');
            $table->bigInteger('member_id')->nullable()->unsigned();
            $table->foreign('member_id')->references('id')->on('admins');
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
        Schema::dropIfExists('sales_teams_members');
    }
}
