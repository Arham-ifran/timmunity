<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PaymentGatewayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('payment_gateways')->delete();

        \DB::table('payment_gateways')->insert(array(
            0 =>
            array(
                'id' => 1,
                'gateway_name' => 'Mollie',
                'sandbox_api_key' => 'test_T9PfwQ7NJ37VFkV4UNVs9tQv7SkKzf',
                'live_api_key' => null,
                'mode' => '0',
                'status' => '1',
            ),
        ));

    }
}
