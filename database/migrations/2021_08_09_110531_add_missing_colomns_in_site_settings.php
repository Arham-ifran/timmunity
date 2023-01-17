<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColomnsInSiteSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('company_registration_number')->nullable()->after('site_address');
            $table->string('site_url')->nullable()->after('company_registration_number');
            $table->string('vat_id')->nullable()->after('site_url');
            $table->string('tax_id')->nullable()->after('vat_id');
            $table->string('street')->nullable()->after('tax_id');
            $table->string('zip_code')->nullable()->after('street');
            $table->string('city')->nullable()->after('zip_code');
            $table->string('country')->nullable()->after('city');
            $table->string('bank_name')->nullable()->after('country');
            $table->string('iban')->nullable()->after('bank_name');
            $table->string('code')->nullable()->after('iban');
            $table->string('pinterest')->nullable()->after('iban');
            $table->string('facebook')->nullable()->after('pinterest');
            $table->string('twitter')->nullable()->after('facebook');
            $table->string('linkedin')->nullable()->after('twitter');
            $table->integer('number_of_days')->nullable()->after('linkedin');
            $table->double('defualt_vat')->nullable()->after('number_of_days');
            $table->integer('payment_relief_days')->nullable()->after('defualt_vat');
            $table->integer('user_deletion_days')->nullable()->after('payment_relief_days');
            $table->text('operating_hours')->nullable()->after('user_deletion_days');
            $table->text('commercial_register_address')->nullable()->after('operating_hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            //
        });
    }
}
