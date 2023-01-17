<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTemplateLabelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_template_labels', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('email_template_id')->unsigned()->nullable()->index('email_template_id');
			$table->string('label', 100);
			$table->text('value', 65535);
			$table->boolean('status')->default(1);
			$table->softDeletes();
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
		Schema::drop('email_template_labels');
	}

}
