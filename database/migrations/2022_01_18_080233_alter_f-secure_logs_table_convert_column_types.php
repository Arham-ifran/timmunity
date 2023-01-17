<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFSecureLogsTableConvertColumnTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `fsecure_logs` CHANGE `parmas` `parmas` LONGBLOB NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `fsecure_logs` CHANGE `header` `header` LONGBLOB NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `fsecure_logs` CHANGE `response` `response` LONGBLOB NULL DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
