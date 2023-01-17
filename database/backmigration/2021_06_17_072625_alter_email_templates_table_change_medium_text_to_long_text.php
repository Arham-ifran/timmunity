<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmailTemplatesTableChangeMediumTextToLongText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('long_text', function (Blueprint $table) {
            DB::Statement("ALTER TABLE `email_templates` CHANGE `header` `header` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
            DB::Statement("ALTER TABLE `email_templates` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
            DB::Statement("ALTER TABLE `email_templates` CHANGE `footer` `footer` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
            DB::Statement("ALTER TABLE `email_templates` CHANGE `welcome_content` `welcome_content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('long_text', function (Blueprint $table) {
            //
        });
    }
}
