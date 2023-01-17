<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableContactContactSectorsActivitiesAddAutoincreamentPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `contact_sectors_activities` ADD PRIMARY KEY(`id`);");
        DB::Statement("ALTER TABLE `contact_sectors_activities` CHANGE `id` `id` BIGINT(11) NOT NULL AUTO_INCREMENT;");
        DB::Statement("ALTER TABLE `contact_sectors_activities` CHANGE `id` `id` BIGINT NOT NULL AUTO_INCREMENT;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
