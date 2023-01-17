<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuotationIdForeignKeyConstrantsInActivityAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_attachments', function (Blueprint $table) {
            $table->bigInteger('quotation_id')->nullable()->unsigned()->after('kss_subscription_id');
            $table->foreign('quotation_id')->references('id')->on('quotations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_attachments', function (Blueprint $table) {
            //
        });
    }
}
