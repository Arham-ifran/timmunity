<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('projects')->delete();

        \DB::table('projects')->insert(array(
            array(
                'id' => 1,
                'name' => 'Transfer Immunity',
                'prefix' => 'TRF'
            ),
            array(
                'id' => 2,
                'name' => 'Move Immunity',
                'prefix' => 'MOV'
            ),
            array(
                'id' => 3,
                'name' => 'NED.link',
                'prefix' => 'NED'
            ),
            array(
                'id' => 4,
                'name' => 'aikQ',
                'prefix' => 'AKQ'
            ),
            array(
                'id' => 5,
                'name' => 'Inbox',
                'prefix' => 'INB'
            ),
            array(
                'id' => 6,
                'name' => 'Over Mail',
                'prefix' => 'OVM'
            ),
            array(
                'id' => 7,
                'name' => 'Maili',
                'prefix' => 'MAI'
            ),
            array(
                'id' => 8,
                'name' => 'QR Code',
                'prefix' => 'QRC'
            ),
            array(
                'id' => 9,
                'name' => 'Email marketing',
                'prefix' => 'EMK'
            )
        ));
    }
}
