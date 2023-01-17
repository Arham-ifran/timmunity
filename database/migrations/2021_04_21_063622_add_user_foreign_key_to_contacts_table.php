<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserForeignKeyToContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function(Blueprint $table)
		{
			$table->foreign('created_by', 'contacts_ibfk_1')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by', 'contacts_ibfk_2')->references('id')->on('admins')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
}
