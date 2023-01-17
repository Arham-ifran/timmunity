<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVariantIdForeignKeyConstraintInActivityMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_messages', function (Blueprint $table) {
            $table->dropForeign('activity_messages_variant_id_foreign');
        });
        Schema::table('activity_messages', function (Blueprint $table) {
            $table->integer('variant_id')->change();
            $table->foreign('variant_id')
            ->references('id')->on('product_variations')
            ->onDelete('cascade')
            ->onUpdate('cascade')
            ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_messages', function (Blueprint $table) {
            //
        });
    }
}
