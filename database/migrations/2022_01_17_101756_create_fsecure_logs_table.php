<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFsecureLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fsecure_logs', function (Blueprint $table) {
            $table->id();
            $table->string('end_point');
            $table->string('request_type');
            $table->string('parmas')->nullable();
            $table->string('header')->nullable();
            $table->string('response');
            $table->string('response_code');
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
        Schema::dropIfExists('fsecure_logs');
    }
}
