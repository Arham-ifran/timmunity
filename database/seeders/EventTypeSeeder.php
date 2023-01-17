<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('event_types')->delete();

        \DB::table('event_types')->insert(array(
            array('id' => '1', 'event_name' => 'Admin Login', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '2', 'event_name' => 'Admin Logout', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '3', 'event_name' => 'Admin Profile Update', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '4', 'event_name' => 'Admin User Add', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '5', 'event_name' => 'Admin User Update', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '6', 'event_name' => 'Admin User Delete', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '7', 'event_name' => 'Admin User Bulk Delete', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '8', 'event_name' => 'Admin User Duplicate', 'created_at' => NULL, 'updated_at' => NULL),
            array('id' => '9', 'event_name' => 'Admin User Status Change', 'created_at' => NULL, 'updated_at' => NULL)
        ));
    }
}
