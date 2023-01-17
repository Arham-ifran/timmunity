<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLowLicenseEmailRecipientsColumnInSiteSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('low_license_email_recipients')->nullable();
            $table->string('registration_email_recipients')->nullable();
            $table->string('orders_bcc_email_recipients')->nullable();
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
            $table->dropIfExists('low_license_email_recipients');
            $table->dropIfExists('registration_email_recipients');
            $table->dropIfExists('orders_bcc_email_recipients');
        });
    }
}
