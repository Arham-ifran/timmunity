<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_name',200);
            $table->longText('sandbox_api_key',200)->nullable();
            $table->longText('live_api_key',200)->nullable();
            $table->enum('mode', ['0', '1'])->default('0')->comment('0: Sandbox 1: Live');
            $table->enum('status', ['0', '1'])->default('0')->comment('0: In Active 1: Active');
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
        Schema::dropIfExists('payment_gateways');
    }
}
