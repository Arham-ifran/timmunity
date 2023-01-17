<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SalesTeamTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sales_teams')->delete();

        \DB::table('sales_teams')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Website',
                'is_archive' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
        ));
        \DB::table('sales_teams')->insert(array(
            1 =>
            array(
                'id' => 2,
                'name' => 'Admin',
                'is_archive' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
        ));

    }
}
