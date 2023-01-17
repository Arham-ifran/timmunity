<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_messages', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('kss_subscription_id')->nullable();
            $table->integer('voucher_id')->nullable();
            $table->integer('quotation_id')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
            $table->index(['kss_subscription_id', 'voucher_id', 'quotation_id'], 'kss_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_messages');
    }
}
