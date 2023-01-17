<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('customer_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('kss')->nullable();
            $table->string('email')->nullable();
            $table->dateTime('active_date')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('status')->nullable()->default(0)->comment('0:Draft, 1:Active, 2:Cancelled');
            $table->timestamps();
            $table->index(['customer_id', 'product_id'], 'customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
