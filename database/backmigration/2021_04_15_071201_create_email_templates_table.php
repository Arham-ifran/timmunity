<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->boolean('email_template_type')->default(0)->comment('1=welcome;2=signup;');
            $table->boolean('status')->default(1);
            $table->mediumText('title')->nullable();
            $table->mediumText('header')->nullable();
            $table->mediumText('content')->nullable();
            $table->mediumText('footer')->nullable();
            $table->mediumText('welcome_content')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
}
