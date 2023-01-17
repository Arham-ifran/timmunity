<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleIdAndForeignKeyConstraintInActivityAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_attachments', function (Blueprint $table) {
            $table->bigInteger('schedule_activity_id')->nullable()->after('log_note_id');
            $table->foreign('schedule_activity_id')
            ->references('id')->on('schedule_activities')
            ->onDelete('cascade')
            ->onUpdate('cascade');
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
