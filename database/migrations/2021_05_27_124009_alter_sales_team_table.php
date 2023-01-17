<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_teams', function (Blueprint $table) {
            $table->bigInteger('id')->change();
            $table->string('sales_team_name',65)->change();
            $table->renameColumn('sales_team_name', 'name');
            $table->boolean('type')->change()->comment('1=quotation,2= pipeline');
            $table->bigInteger('team_lead_id')->change()->comment('Sub admin user id');
            $table->dropColumn('accept_email_from');
            $table->dropColumn('email_alias');
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
            $table->integer('id')->change();
            $table->string('name',100)->change();
            $table->renameColumn('name', 'sales_team_name');
            $table->string('type',255)->change();
            $table->integer('team_lead_id')->change();
            $table->string('accept_email_from')->nullable();
            $table->string('email_alias')->nullable();
        });
    }
}
