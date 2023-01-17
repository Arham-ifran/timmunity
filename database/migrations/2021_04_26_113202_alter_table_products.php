<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProducts extends Migration
{
    /**
     * Drop column if exists.
     *
     * @return void
     */
    function DropTheColumnIfExists($myTable, $column)
    {
        if (Schema::hasColumn($myTable, $column)) //check the column
        {
            Schema::table($myTable, function (Blueprint $table) use ($column)
            {
                $table->dropColumn($column); //drop it
            });
        }
    
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->DropTheColumnIfExists('products','can_be_sold');
        $this->DropTheColumnIfExists('products','can_be_purchased');
        $this->DropTheColumnIfExists('products','product_type');
        $this->DropTheColumnIfExists('products','product_category');
        $this->DropTheColumnIfExists('products','sales_price');
        $this->DropTheColumnIfExists('products','cost_price');
        $this->DropTheColumnIfExists('products','customer_taxes');
        $this->DropTheColumnIfExists('products','internal_reference');
        $this->DropTheColumnIfExists('products','barcode');
        $this->DropTheColumnIfExists('products','internal_notes');

        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->boolean('product_type')->nullable()->comment('1=Can be sold , 2=Can be purchased')->after('name');
            $table->renameColumn('name', 'product_name');
            $table->bigInteger('created_by')->unsigned()->after('image');
            $table->bigInteger('updated_by')->nullable()->unsigned()->comment('last user updated by')->after('created_by');
        });
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
