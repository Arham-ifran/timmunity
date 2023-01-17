<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationOtherInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_other_info', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quotation_id')->nullable();
            $table->integer('reseller_id')->nullable();
            $table->integer('sales_team_id')->nullable();
            $table->string('customer_reference', 100)->nullable();
            $table->boolean('online_signature')->nullable()->default(0)->comment('0:No, 1:Yes');
            $table->boolean('online_payment')->default(0)->comment('0:No, 1:Yes');
            $table->dateTime('delivery_date')->nullable();
            $table->timestamps();
            $table->index(['quotation_id', 'reseller_id', 'sales_team_id'], 'quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_other_info');
    }
}
