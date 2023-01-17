<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('site_settings')->delete();
        \DB::table('site_settings')->insert([
            'site_logo' => 'site_logo.png',
            'site_name' => 'TIMmunity ',
            'site_title' => 'TIMmunity ',
            'site_keywords' => 'TIMmunity ',
            'site_description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
            'site_email' => 'support@productimmunity.com',
            'inquiry_email' => 'support@productimmunity.com',
            'site_phone' => '999777444',
            'site_mobile' => '66644777',
            'site_address' => 'Erftstr. 15 38120 Braunschweig',
            'company_registration_number' => 'DE327709293',
            'site_url' => 'https://timmunity.org',
            'vat_id' => 'DE327709293dfasdf',
            'tax_id' => '14/201/04214dfasdf',
            'street' => 'N/A',
            'zip_code' => 'N/A',
            'city' => 'N/A',
            'country' => 'Germany',
            'bank_name' => 'N/A',
            'iban' => 'DE12 2704 0080 0480 6725 00',
            'pinterest' => 'https://www.pinterest.com/',
            'facebook' => 'https://www.facebook.com/facebook',
            'twitter' => 'https://twitter.com',
            'linkedin' => 'https://linkedin.com/',
            'number_of_days' => '0',
            'defualt_vat' => '19',
            'payment_relief_days' => '90',
            'user_deletion_days' => '10',
            'operating_hours' => 'Monday to Friday 9 AM - 4 PM (UTC+02:00)',
            'commercial_register_address' => 'Handelsregister Braunschweig HRB 208156',
            'code' => 'COBADEFFXXX',
            'created_at' => Carbon::now()


        ]);
    }
}
