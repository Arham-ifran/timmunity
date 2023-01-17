<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLanguageTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE TABLE `language_translations` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `language_module_id` int(10) unsigned DEFAULT NULL,
          `language_id` int(11) DEFAULT NULL,
          `language_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `column_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `item_id` bigint(20) DEFAULT NULL,
          `item_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `custom` tinyint(1) DEFAULT 0,
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `language_id` (`language_id`),
          KEY `language_module_id` (`language_module_id`),
          CONSTRAINT `language_translations_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE,
          CONSTRAINT `language_translations_ibfk_2` FOREIGN KEY (`language_module_id`) REFERENCES `language_modules` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_language_translations');
    }
}
