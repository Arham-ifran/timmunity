<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname', 30)->nullable();
            $table->string('lastname', 30)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('city', 20)->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->boolean('is_term_condition')->default(0);
            $table->string('job_position', 100)->nullable();
            $table->integer('title_id')->nullable();
            $table->string('tags')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('invitation_code', 100)->nullable();
            $table->string('web_link')->nullable();
            $table->string('vat_id', 100)->nullable();
            $table->boolean('type')->default(0)->comment('0:Individual, 2:Company');
            $table->boolean('role')->default(0)->comment('0: Administrator, 1: Reseller,  2:Customer, 3:Partner');
            $table->string('image', 100)->nullable();
            $table->text('internal_notes')->nullable();
            $table->integer('lang_id')->nullable();
            $table->integer('timezone_id')->nullable();
            $table->text('email_signature')->nullable();
            $table->tinyInteger('notification')->default(1)->comment('1:Handle by Emails, 2:Handle in TIMmunity');
            $table->boolean('is_active')->default(0)->comment('0=inactive;1=active');
            $table->boolean('account_status')->default(0)->comment('0:Never Connected, 1:Confirmed');
            $table->boolean('is_archive')->default(0)->comment('0:Unarchived, 1:Archived');
            $table->dateTime('last_login_on')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->index(['state_id', 'country_id', 'title_id', 'lang_id', 'timezone_id'], 'state_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
