<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('contacts')->delete();
        \DB::table('admins')->delete();

        \DB::table('admins')->insert(array(
            0 =>
            array(
                'id' => 1,
                'firstname' => 'TIMmunity',
                'lastname' => 'Admin',
                'email' => 'admin@arhamsoft.com',
                'password' => Hash::make('123456789'),
                'account_status' => 1,
                'is_active' => 1,
                'email_verified_at' => Carbon::now(),
                'lang_id' => 2,
                'timezone_id' => 248,
                'invitation_code' => null,
                'email_signature' => '<p>Admin TIMmunity__</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
        ));

        \DB::table('contacts')->insert(array(
            0 =>
            array(
                'id' => 1,
                'created_by' => 1,
                'admin_id' => 1,
                'name' => 'TIMmunity Admin',
                'email' => 'admin@arhamsoft.com',
                'type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
        ));
    }
}
