<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewForeignKeyConstrantsInActivityAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_attachments', function (Blueprint $table) {
            $table->bigInteger('customer_id')->nullable()->unsigned()->after('contact_id');
            $table->foreign('customer_id')->references('id')->on('contacts');
            $table->bigInteger('product_id')->nullable()->unsigned()->after('customer_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->bigInteger('variant_id')->nullable()->unsigned()->after('product_id');
            $table->foreign('variant_id')->references('id')->on('products');
            $table->bigInteger('sales_team_id')->nullable()->after('variant_id');
            $table->foreign('sales_team_id')->references('id')->on('sales_teams');
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
