<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_order_lines', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('quotation_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('description', 200)->nullable();
            $table->integer('qty')->nullable();
            $table->dateTime('lead_time')->nullable();
            $table->string('kss', 100)->nullable();
            $table->double('unit_price')->nullable();
            $table->double('taxes')->nullable();
            $table->string('section', 100)->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['quotation_id', 'product_id'], 'quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_order_lines');
    }
}
