<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKasperskySubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kaspersky_subscriptions', function (Blueprint $table) {
            $table->Integer('id', true);
            $table->bigInteger('partner_id')->nullable();
            $table->string('subscriber_id', 50)->nullable();
            $table->integer('product_id')->nullable();
            $table->string('license_key')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('status')->nullable()->default(0)->comment('0:Draft, 1:Active, 2:In Error, 3:Soft Cancel/Pause , 4:Hard Cancel');
            $table->timestamps();
            $table->index(['partner_id', 'subscriber_id', 'product_id'], 'partner_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kaspersky_subscriptions');
    }
}
