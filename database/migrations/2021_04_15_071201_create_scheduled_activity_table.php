<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_activity', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('kss_subscription_id')->nullable();
            $table->integer('voucher_id')->nullable();
            $table->integer('quotations_id')->nullable();
            $table->string('activity_type')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('summary')->nullable();
            $table->integer('assign_to')->nullable();
            $table->text('details')->nullable();
            $table->boolean('status')->default(0)->comment('0:Schedule, 1:Mark as done, 2:Done and schedule next, 4:Discard');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('update_at')->nullable();
            $table->index(['kss_subscription_id', 'voucher_id', 'quotations_id'], 'kss_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_activity');
    }
}
