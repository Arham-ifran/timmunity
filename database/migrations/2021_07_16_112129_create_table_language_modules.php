<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLanguageModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE TABLE `language_modules` (
          `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
          `table` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `columns` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `created_at` TIMESTAMP NULL DEFAULT NULL,
          `updated_at` TIMESTAMP NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_language_modules');
    }
}
