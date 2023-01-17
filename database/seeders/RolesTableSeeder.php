<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Administrator',
                'guard_name' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
        ));


    }
}
