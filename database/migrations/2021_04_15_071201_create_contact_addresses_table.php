<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_addresses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('contact_id')->comment('contacts_table');
            $table->string('contact_name', 30);
            $table->string('job_position', 100);
            $table->boolean('type')->default(0)->comment('0:Contact, 1:Invoice Address, 2:Delivery Address, 3:Other Address, 4:Private Address');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('mobile', 20);
            $table->string('street_1', 100);
            $table->string('street_2', 100);
            $table->text('notes');
            $table->smallInteger('country_id');
            $table->mediumInteger('state_id');
            $table->string('city', 35)->nullable();
            $table->string('zipcode', 11);
            $table->integer('title_id');
            $table->string('contact_image', 100)->nullable();
            $table->timestamps();
            $table->index(['contact_id', 'country_id', 'state_id', 'title_id'], 'contact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_addresses');
    }
}
