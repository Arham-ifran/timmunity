<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_teams', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('sales_team_name')->nullable();
            $table->string('type')->nullable();
            $table->integer('team_lead_id')->nullable()->index('team_lead_id');
            $table->string('email_alias')->nullable();
            $table->string('accept_email_from')->nullable();
            $table->decimal('invoicing_target', 10, 0)->nullable();
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
        Schema::dropIfExists('sales_teams');
    }
}
