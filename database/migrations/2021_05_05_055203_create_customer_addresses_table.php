<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned();
            $table->boolean('type')->default(0)->comment('1:Contact, 2:Invoice Address, 3:Delivery Address, 4:Other Address, 5:Private Address');
            $table->string('contact_name', 65)->nullable();
            $table->integer('name_title_id')->nullable()->comment('Mr , Madam , Miss , Mister , Professor etc');
            $table->string('street_1', 100)->nullable();
            $table->string('street_2', 100)->nullable();
            $table->string('city', 35)->nullable();
            $table->mediumInteger('state_id')->nullable();
            $table->string('zipcode', 7)->nullable();
            $table->smallInteger('country_id')->nullable();
            $table->string('job_position', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('image', 100)->nullable();
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
        Schema::dropIfExists('customer_addresses');
    }
}
