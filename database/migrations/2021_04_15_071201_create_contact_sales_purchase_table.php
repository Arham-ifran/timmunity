<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactSalesPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_sales_purchase', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('sales_team_id')->nullable();
            $table->string('payment_terms')->nullable();
            $table->integer('pricelist_id')->nullable();
            $table->string('vat_id', 100)->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('website')->nullable();
            $table->string('industry', 100)->nullable();
            $table->timestamps();
            $table->index(['sales_team_id', 'pricelist_id'], 'sales_team_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_sales_purchase');
    }
}
