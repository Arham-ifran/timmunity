<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\productType;

class ProductTypeInProductModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_types')->delete();
        productType::insert([
            ['title' => 'Consumable'],
            ['title' => 'Service'],
            ['title' => 'Storable Product'],
        ]);
    }
}
