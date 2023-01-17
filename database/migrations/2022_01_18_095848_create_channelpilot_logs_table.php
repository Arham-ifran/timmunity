<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelpilotLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channelpilot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('end_point');
            $table->string('request_type');
            $table->string('parmas')->nullable();
            $table->string('header')->nullable();
            $table->string('response');
            $table->string('response_code')->nullable();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `channelpilot_logs` CHANGE `parmas` `parmas` LONGBLOB NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `channelpilot_logs` CHANGE `header` `header` LONGBLOB NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `channelpilot_logs` CHANGE `response` `response` LONGBLOB NULL DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channelpilot_logs');
    }
}
