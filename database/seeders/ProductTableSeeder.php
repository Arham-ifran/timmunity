<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('products')->delete();
        \DB::table('products')->insert(array(
            0 =>
            array(
                'id' => 1,
                'product_name'=> 'ARCONIS True Advanced Subscription',
                'slug'=> 'ARCONIS-True-Advanced-Subscription',
                'created_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
        ));
    }
}
