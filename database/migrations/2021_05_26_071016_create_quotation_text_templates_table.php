<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationTextTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_text_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('quotation_id');
            $table->boolean('type')->default(0)->comment('0:Sales Quotation, 1:Sale Confirmation,  2:Performa Invoice');
            $table->string('text');
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
        Schema::dropIfExists('quotation_text_templates');
    }
}
