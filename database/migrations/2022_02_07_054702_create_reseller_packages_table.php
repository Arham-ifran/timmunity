<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name', 100);
            $table->double('percentage')->nullable();
            $table->enum('model', ['0', '1'])->comment('0: Incremental, 1: Decremental');
            $table->enum('is_active', ['0', '1'])->comment('0: In-Active, 1: Active');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reseller_packages');
    }
}
