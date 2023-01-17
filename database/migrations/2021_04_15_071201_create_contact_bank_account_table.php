<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactBankAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_bank_account', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('account_number', 20)->nullable();
            $table->string('account_type', 30)->nullable();
            $table->string('account_title', 50)->nullable();
            $table->integer('bank_id')->nullable()->comment('contact_bank_id');
            $table->smallInteger('currency_id')->nullable()->comment('contact_currency_id');
            $table->string('account_holder_name', 25)->nullable();
            $table->timestamps();
            $table->index(['bank_id', 'currency_id'], 'bank_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_bank_account');
    }
}
