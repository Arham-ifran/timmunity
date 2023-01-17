<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableContactAddressedMakeNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `contact_id` `contact_id` INT(11) NULL COMMENT 'contacts_table';");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `contact_name` `contact_name` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `job_position` `job_position` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `email` `email` VARCHAR(65) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `phone` `phone` VARCHAR(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `mobile` `mobile` VARCHAR(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `street_1` `street_1` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `street_2` `street_2` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `notes` `notes` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `zipcode` `zipcode` VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `city` `city` VARCHAR(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `country_id` `country_id` SMALLINT(6) NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `state_id` `state_id` MEDIUMINT(9) NULL;");
        DB::Statement("ALTER TABLE `contact_addresses` CHANGE `title_id` `title_id` INT(11) NULL;");
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
