<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyCustomerIdInCustomerAddressedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->foreign('customer_id', 'customer_addresses_ibfk_1')->references('id')->on('customers')->onUpdate('RESTRICT')->onDelete('CASCADE'); 
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropForeign('customer_addresses_ibfk_1');
        });
    }
}
