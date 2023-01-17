<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('modules')->delete();

        \DB::table('modules')->insert(array (
  0 => 
  array (
    'id' => 1,
    'module_name' => 'Admin Users',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  1 => 
  array (
    'id' => 2,
    'module_name' => 'Company',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  2 => 
  array (
    'id' => 3,
    'module_name' => 'CMS Page',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  3 => 
  array (
    'id' => 4,
    'module_name' => 'Email Templates',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  4 => 
  array (
    'id' => 5,
    'module_name' => 'Email Template Labels
',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  5 => 
  array (
    'id' => 6,
    'module_name' => 'Site Settings',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  6 => 
  array (
    'id' => 7,
    'module_name' => 'Languages',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  7 => 
  array (
    'id' => 8,
    'module_name' => 'Language Modules',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  8 => 
  array (
    'id' => 9,
    'module_name' => 'Language Translations
',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  9 => 
  array (
    'id' => 10,
    'module_name' => 'Label Translations',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  10 => 
  array (
    'id' => 11,
    'module_name' => 'Text Translations',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  11 => 
  array (
    'id' => 12,
    'module_name' => 'General Settings',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  12 => 
  array (
    'id' => 13,
    'module_name' => 'Sales Settings',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  13 => 
  array (
    'id' => 14,
    'module_name' => 'Contact',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  14 => 
  array (
    'id' => 15,
    'module_name' => 'Send Message',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  15 => 
  array (
    'id' => 16,
    'module_name' => 'Log Note',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  16 => 
  array (
    'id' => 17,
    'module_name' => 'Schedule Activity',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  17 => 
  array (
    'id' => 18,
    'module_name' => 'Contact Tags',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  18 => 
  array (
    'id' => 19,
    'module_name' => 'Contact Titles',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  19 => 
  array (
    'id' => 20,
    'module_name' => 'Contact Sector of Activities',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  20 => 
  array (
    'id' => 21,
    'module_name' => 'Contact Currencies',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  21 => 
  array (
    'id' => 22,
    'module_name' => 'Contact Countries',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  22 => 
  array (
    'id' => 23,
    'module_name' => 'Contact Fed. State',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  23 => 
  array (
    'id' => 24,
    'module_name' => 'Contact Country Groups',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  24 => 
  array (
    'id' => 25,
    'module_name' => 'Contact Banks',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  25 => 
  array (
    'id' => 26,
    'module_name' => 'Contact Banks Accounts',
    'created_at' => '2021-09-09 18:13:08',
    'updated_at' => '2021-09-09 18:13:12',
  ),
  26 => 
  array (
    'id' => 27,
    'module_name' => 'Vouchers',
    'created_at' => '2021-09-09 18:12:56',
    'updated_at' => '2021-09-09 18:13:05',
  ),
  27 => 
  array (
    'id' => 28,
    'module_name' => 'Licenses',
    'created_at' => '2021-09-13 11:23:37',
    'updated_at' => '2021-09-13 11:23:37',
  ),
  28 => 
  array (
    'id' => 29,
    'module_name' => 'Websites',
    'created_at' => '2021-09-13 11:24:28',
    'updated_at' => '2021-09-13 11:24:28',
  ),
  29 => 
  array (
    'id' => 30,
    'module_name' => 'Abandoned Carts',
    'created_at' => '2021-09-13 11:24:28',
    'updated_at' => '2021-09-13 11:24:28',
  ),
  30 => 
  array (
    'id' => 31,
    'module_name' => 'Projects',
    'created_at' => '2021-09-13 11:27:15',
    'updated_at' => '2021-09-13 11:27:15',
  ),
  31 => 
  array (
    'id' => 32,
    'module_name' => 'Visitors',
    'created_at' => '2021-09-13 11:27:15',
    'updated_at' => '2021-09-13 11:27:15',
  ),
  32 => 
  array (
    'id' => 33,
    'module_name' => 'Views',
    'created_at' => '2021-09-13 11:28:01',
    'updated_at' => '2021-09-13 11:28:01',
  ),
  33 => 
  array (
    'id' => 34,
    'module_name' => 'Lawful Interception',
    'created_at' => '2021-09-13 11:28:28',
    'updated_at' => '2021-09-13 11:28:28',
  ),
  34 => 
  array (
    'id' => 35,
    'module_name' => 'FAQS',
    'created_at' => '2021-09-13 11:28:28',
    'updated_at' => '2021-09-13 11:28:28',
  ),
  35 => 
  array (
    'id' => 36,
    'module_name' => 'Contact Us Queries',
    'created_at' => '2021-09-13 11:29:55',
    'updated_at' => '2021-09-13 11:29:55',
  ),
  36 => 
  array (
    'id' => 37,
    'module_name' => 'Payment Gateways',
    'created_at' => '2021-09-13 11:30:28',
    'updated_at' => '2021-09-13 11:30:28',
  ),
  37 => 
  array (
    'id' => 38,
    'module_name' => 'Sales',
    'created_at' => '2021-09-13 11:36:39',
    'updated_at' => '2021-09-13 11:36:39',
  ),
  38 => 
  array (
    'id' => 39,
    'module_name' => 'Quotations',
    'created_at' => '2021-09-13 11:37:52',
    'updated_at' => '2021-09-13 11:37:52',
  ),
  39 => 
  array (
    'id' => 40,
    'module_name' => 'Orders',
    'created_at' => '2021-09-13 11:38:05',
    'updated_at' => '2021-09-13 11:38:05',
  ),
  40 => 
  array (
    'id' => 41,
    'module_name' => 'Sales Analytics',
    'created_at' => '2021-09-13 11:38:05',
    'updated_at' => '2021-09-13 11:38:05',
  ),
  41 => 
  array (
    'id' => 42,
    'module_name' => 'Customers',
    'created_at' => '2021-09-13 11:38:42',
    'updated_at' => '2021-09-13 11:38:42',
  ),
  42 => 
  array (
    'id' => 43,
    'module_name' => 'Products',
    'created_at' => '2021-09-13 11:48:19',
    'updated_at' => '2021-09-13 11:48:19',
  ),
  43 => 
  array (
    'id' => 44,
    'module_name' => 'Product Variants',
    'created_at' => '2021-09-13 11:48:19',
    'updated_at' => '2021-09-13 11:48:19',
  ),
  44 => 
  array (
    'id' => 45,
    'module_name' => 'Price Lists',
    'created_at' => '2021-09-13 11:49:55',
    'updated_at' => '2021-09-13 11:49:55',
  ),
  45 => 
  array (
    'id' => 46,
    'module_name' => 'Reporting',
    'created_at' => '2021-09-13 11:50:27',
    'updated_at' => '2021-09-13 11:50:27',
  ),
  46 => 
  array (
    'id' => 47,
    'module_name' => 'Sales Team',
    'created_at' => '2021-09-13 11:50:27',
    'updated_at' => '2021-09-13 11:50:27',
  ),
  47 => 
  array (
    'id' => 48,
    'module_name' => 'Taxes',
    'created_at' => '2021-09-13 11:52:32',
    'updated_at' => '2021-09-13 11:52:32',
  ),
  48 => 
  array (
    'id' => 49,
    'module_name' => 'Ecommerce Categories',
    'created_at' => '2021-09-13 11:52:44',
    'updated_at' => '2021-09-13 11:52:44',
  ),
  49 => 
  array (
    'id' => 50,
    'module_name' => 'Attributes',
    'created_at' => '2021-09-13 11:53:26',
    'updated_at' => '2021-09-13 11:53:26',
  ),
  50 => 
  array (
    'id' => 51,
    'module_name' => 'Reseller',
    'created_at' => '2021-09-14 18:51:11',
    'updated_at' => '2021-09-14 18:51:11',
  ),
  51 => 
  array (
    'id' => 52,
    'module_name' => 'Roles',
    'created_at' => '2021-09-14 18:51:11',
    'updated_at' => '2021-09-14 18:51:11',
  ),
));


    }
}
