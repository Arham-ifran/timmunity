<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterQuotationsTableStatusses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::Statement("ALTER TABLE `quotations`
                        ADD `is_quotation_sent` INT NOT NULL DEFAULT '0' COMMENT '0: No\r\n1: Yes' AFTER `status`,
                        ADD `is_proforma_quotation_sent` INT NOT NULL DEFAULT '0' COMMENT '0: No\r\n1: Yes' AFTER `is_quotation_sent`,
                        ADD `is_confirmed_without_kss` INT NOT NULL DEFAULT '0' COMMENT '0: No\r\n1: Yes' AFTER `is_proforma_quotation_sent`;"
                        );
        DB::Statement("ALTER TABLE `quotations`
                        CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0: Quotation\r\n1: Sales Order\r\n2: Locked\r\n3: Quotation Sent\r\n4: Cancelled';"
                        );
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
