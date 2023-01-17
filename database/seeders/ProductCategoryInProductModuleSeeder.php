<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\productCategorie;
class ProductCategoryInProductModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_categories')->delete();
        productCategorie::insert([
            ['title' => 'All'],
            ['title' => 'All / Expenses'],
            ['title' => 'All / Saleable'],
        ]);
    }
}
