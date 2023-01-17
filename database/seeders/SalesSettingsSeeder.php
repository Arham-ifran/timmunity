<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SalesSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::table('sales_settings')->insert([
            'variable_name' => "product_catalog_variants",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "product_catalog_product_configurator",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "product_catalog_deliver_content_email",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "pricing_discount",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "pricing_discount",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "pricing_pricelist",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "orders_online_signature",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "orders_online_payment",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "orders_customer_address",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "orders_lock_confirmed_sale",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "orders_proforma_invoice",
            'variable_value' => '0',
        ]);
        \DB::table('sales_settings')->insert([
            'variable_name' => "invoicing_policy",
            'variable_value' => '0',    //  0: what is ordered 1: what is delivered
        ]);
    }
}
