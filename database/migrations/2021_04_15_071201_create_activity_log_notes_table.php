<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log_notes', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('log_user_id')->nullable();
            $table->integer('kss_subscription_id')->nullable();
            $table->integer('voucher_id')->nullable();
            $table->integer('quotation_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('note')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
            $table->index(['log_user_id', 'kss_subscription_id', 'voucher_id', 'quotation_id'], 'log_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_log_notes');
    }
}
