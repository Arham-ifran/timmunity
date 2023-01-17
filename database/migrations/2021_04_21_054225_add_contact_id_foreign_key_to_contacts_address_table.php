<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactIdForeignKeyToContactsAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `contacts` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `id` `id` BIGINT NOT NULL AUTO_INCREMENT;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `contact_id` `contact_id` BIGINT(20) UNSIGNED NULL;");
        Schema::table('contact_addresses', function(Blueprint $table)
		{ 
			$table->foreign('contact_id', 'contact_addresses_ibfk_1')->references('id')->on('contacts')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_addresses', function (Blueprint $table) {
            //
        });
    }
}
