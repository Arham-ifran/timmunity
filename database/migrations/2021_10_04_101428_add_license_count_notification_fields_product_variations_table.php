<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLicenseCountNotificationFieldsProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->boolean('license_keys_notify_flag')->default(0)->comment('0:No, 1:1st Reminder,2:2nd Reminder, 3:Last Reminder etc')->after('variation_detail_json');
            $table->timestamp('last_low_key_notify_time')->nullable()->after('license_keys_notify_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variations', function (Blueprint $table) {
            //
        });
    }
}
