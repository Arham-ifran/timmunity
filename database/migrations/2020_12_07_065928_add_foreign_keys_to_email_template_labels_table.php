<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEmailTemplateLabelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('email_template_labels', function(Blueprint $table)
		{
			$table->foreign('email_template_id', 'email_template_labels_ibfk_1')->references('id')->on('email_templates')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('email_template_labels', function(Blueprint $table)
		{
			$table->dropForeign('email_template_labels_ibfk_1');
		});
	}

}
