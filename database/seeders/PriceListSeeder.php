<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('product_pricelists')->delete();
        \DB::table('product_pricelists')->insert(array(
            array('id' => 1, 'name' => "Public Pricelist", 'currency_id' => null, 'is_active' => 1, 'created_by' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
        ));
    }
}
