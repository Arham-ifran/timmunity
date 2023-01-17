<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogNoteForeignKeyInActivityAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_attachments', function (Blueprint $table) {
            $table->bigInteger('log_note_id')->nullable()->after('send_msg_id');
            $table->foreign('log_note_id')->references('id')->on('activity_log_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_attachments', function (Blueprint $table) {
            //
        });
    }
}
