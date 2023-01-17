<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('permissions')->delete();

        \DB::table('permissions')->insert(array (
  0 =>
  array (
    'id' => 1,
    'name' => 'Add New User',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-09T07:17:36.000000Z',
    'updated_at' => '2021-09-09T07:17:36.000000Z',
  ),
  1 =>
  array (
    'id' => 2,
    'name' => 'User Listing',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-09T07:19:39.000000Z',
    'updated_at' => '2021-09-14T13:29:57.000000Z',
  ),
  2 =>
  array (
    'id' => 3,
    'name' => 'Edit User',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-09T07:26:05.000000Z',
    'updated_at' => '2021-09-09T07:26:05.000000Z',
  ),
  3 =>
  array (
    'id' => 4,
    'name' => 'Delete User',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  4 =>
  array (
    'id' => 5,
    'name' => 'Filter Record User',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-09T07:35:15.000000Z',
    'updated_at' => '2021-09-09T07:37:40.000000Z',
  ),
  5 =>
  array (
    'id' => 6,
    'name' => 'Add New Company',
    'guard_name' => 'admin',
    'module_id' => 2,
    'created_at' => '2021-09-09T07:39:07.000000Z',
    'updated_at' => '2021-09-09T07:39:07.000000Z',
  ),
  6 =>
  array (
    'id' => 7,
    'name' => 'Company Listing',
    'guard_name' => 'admin',
    'module_id' => 2,
    'created_at' => '2021-09-09T07:39:25.000000Z',
    'updated_at' => '2021-09-14T13:12:06.000000Z',
  ),
  7 =>
  array (
    'id' => 8,
    'name' => 'Edit Company',
    'guard_name' => 'admin',
    'module_id' => 2,
    'created_at' => '2021-09-09T07:39:43.000000Z',
    'updated_at' => '2021-09-09T07:39:43.000000Z',
  ),
  8 =>
  array (
    'id' => 9,
    'name' => 'Delete Company',
    'guard_name' => 'admin',
    'module_id' => 2,
    'created_at' => '2021-09-09T07:40:02.000000Z',
    'updated_at' => '2021-09-09T07:40:02.000000Z',
  ),
  9 =>
  array (
    'id' => 10,
    'name' => 'Add New CMS Page',
    'guard_name' => 'admin',
    'module_id' => 3,
    'created_at' => '2021-09-09T07:45:23.000000Z',
    'updated_at' => '2021-09-09T07:45:23.000000Z',
  ),
  10 =>
  array (
    'id' => 11,
    'name' => 'CMS Page Listing',
    'guard_name' => 'admin',
    'module_id' => 3,
    'created_at' => '2021-09-09T07:46:04.000000Z',
    'updated_at' => '2021-09-14T13:11:44.000000Z',
  ),
  11 =>
  array (
    'id' => 12,
    'name' => 'Edit CMS Page',
    'guard_name' => 'admin',
    'module_id' => 3,
    'created_at' => '2021-09-09T07:46:43.000000Z',
    'updated_at' => '2021-09-09T07:46:43.000000Z',
  ),
  12 =>
  array (
    'id' => 13,
    'name' => 'Delete CMS Page',
    'guard_name' => 'admin',
    'module_id' => 3,
    'created_at' => '2021-09-09T07:47:03.000000Z',
    'updated_at' => '2021-09-09T07:47:03.000000Z',
  ),
  13 =>
  array (
    'id' => 14,
    'name' => 'Duplicate CMS page',
    'guard_name' => 'admin',
    'module_id' => 3,
    'created_at' => '2021-09-09T07:47:34.000000Z',
    'updated_at' => '2021-09-09T07:47:34.000000Z',
  ),
  14 =>
  array (
    'id' => 18,
    'name' => 'Add New Email Templates',
    'guard_name' => 'admin',
    'module_id' => 4,
    'created_at' => '2021-09-09T07:55:05.000000Z',
    'updated_at' => '2021-09-09T08:04:24.000000Z',
  ),
  15 =>
  array (
    'id' => 19,
    'name' => 'Email Templates Listing',
    'guard_name' => 'admin',
    'module_id' => 4,
    'created_at' => '2021-09-09T07:56:01.000000Z',
    'updated_at' => '2021-09-14T13:16:22.000000Z',
  ),
  16 =>
  array (
    'id' => 20,
    'name' => 'Edit Email Templates',
    'guard_name' => 'admin',
    'module_id' => 4,
    'created_at' => '2021-09-09T08:02:18.000000Z',
    'updated_at' => '2021-09-09T08:02:18.000000Z',
  ),
  17 =>
  array (
    'id' => 21,
    'name' => 'Add New Email Template Labels',
    'guard_name' => 'admin',
    'module_id' => 5,
    'created_at' => '2021-09-09T08:04:09.000000Z',
    'updated_at' => '2021-09-09T08:04:09.000000Z',
  ),
  18 =>
  array (
    'id' => 22,
    'name' => 'Email Template Labels Listing',
    'guard_name' => 'admin',
    'module_id' => 5,
    'created_at' => '2021-09-09T08:06:15.000000Z',
    'updated_at' => '2021-09-14T13:16:01.000000Z',
  ),
  19 =>
  array (
    'id' => 23,
    'name' => 'Edit Email Template Labels',
    'guard_name' => 'admin',
    'module_id' => 5,
    'created_at' => '2021-09-09T08:07:05.000000Z',
    'updated_at' => '2021-09-09T08:07:05.000000Z',
  ),
  20 =>
  array (
    'id' => 24,
    'name' => 'Delete Email Template Labels',
    'guard_name' => 'admin',
    'module_id' => 5,
    'created_at' => '2021-09-09T08:07:46.000000Z',
    'updated_at' => '2021-09-09T08:07:46.000000Z',
  ),
  21 =>
  array (
    'id' => 25,
    'name' => 'Filter Record Email Template',
    'guard_name' => 'admin',
    'module_id' => 4,
    'created_at' => '2021-09-09T08:08:26.000000Z',
    'updated_at' => '2021-09-09T08:08:26.000000Z',
  ),
  22 =>
  array (
    'id' => 26,
    'name' => 'View Site Settings',
    'guard_name' => 'admin',
    'module_id' => 6,
    'created_at' => '2021-09-09T08:10:54.000000Z',
    'updated_at' => '2021-09-09T08:10:54.000000Z',
  ),
  23 =>
  array (
    'id' => 27,
    'name' => 'Add New Languages',
    'guard_name' => 'admin',
    'module_id' => 7,
    'created_at' => '2021-09-09T08:16:15.000000Z',
    'updated_at' => '2021-09-09T08:16:15.000000Z',
  ),
  24 =>
  array (
    'id' => 28,
    'name' => 'Languages Listing',
    'guard_name' => 'admin',
    'module_id' => 7,
    'created_at' => '2021-09-09T08:16:50.000000Z',
    'updated_at' => '2021-09-14T13:20:29.000000Z',
  ),
  25 =>
  array (
    'id' => 29,
    'name' => 'Edit Languages',
    'guard_name' => 'admin',
    'module_id' => 7,
    'created_at' => '2021-09-09T08:17:04.000000Z',
    'updated_at' => '2021-09-09T08:17:04.000000Z',
  ),
  26 =>
  array (
    'id' => 30,
    'name' => 'Delete Languages',
    'guard_name' => 'admin',
    'module_id' => 7,
    'created_at' => '2021-09-09T08:17:38.000000Z',
    'updated_at' => '2021-09-09T08:17:38.000000Z',
  ),
  27 =>
  array (
    'id' => 31,
    'name' => 'Activate / Update Languages',
    'guard_name' => 'admin',
    'module_id' => 7,
    'created_at' => '2021-09-09T08:29:37.000000Z',
    'updated_at' => '2021-09-13T09:56:16.000000Z',
  ),
  28 =>
  array (
    'id' => 32,
    'name' => 'Filter Record Languages',
    'guard_name' => 'admin',
    'module_id' => 7,
    'created_at' => '2021-09-09T08:30:28.000000Z',
    'updated_at' => '2021-09-09T08:30:28.000000Z',
  ),
  29 =>
  array (
    'id' => 33,
    'name' => 'Language Modules Listing',
    'guard_name' => 'admin',
    'module_id' => 8,
    'created_at' => '2021-09-09T08:31:43.000000Z',
    'updated_at' => '2021-09-14T13:19:43.000000Z',
  ),
  30 =>
  array (
    'id' => 34,
    'name' => 'Language Translations Listing',
    'guard_name' => 'admin',
    'module_id' => 9,
    'created_at' => '2021-09-09T09:39:03.000000Z',
    'updated_at' => '2021-09-14T13:20:06.000000Z',
  ),
  31 =>
  array (
    'id' => 35,
    'name' => 'Edit Language Translations',
    'guard_name' => 'admin',
    'module_id' => 9,
    'created_at' => '2021-09-09T09:40:14.000000Z',
    'updated_at' => '2021-09-09T09:40:14.000000Z',
  ),
  32 =>
  array (
    'id' => 36,
    'name' => 'Language Partial Translate',
    'guard_name' => 'admin',
    'module_id' => 9,
    'created_at' => '2021-09-09T09:40:42.000000Z',
    'updated_at' => '2021-09-09T09:40:42.000000Z',
  ),
  33 =>
  array (
    'id' => 37,
    'name' => 'Language Bulk Translate',
    'guard_name' => 'admin',
    'module_id' => 12,
    'created_at' => '2021-09-09T09:41:25.000000Z',
    'updated_at' => '2021-09-09T09:41:25.000000Z',
  ),
  34 =>
  array (
    'id' => 38,
    'name' => 'Filter Record Language Translate',
    'guard_name' => 'admin',
    'module_id' => 9,
    'created_at' => '2021-09-09T09:41:46.000000Z',
    'updated_at' => '2021-09-09T09:41:46.000000Z',
  ),
  35 =>
  array (
    'id' => 39,
    'name' => 'Filter Record Language Modules',
    'guard_name' => 'admin',
    'module_id' => 9,
    'created_at' => '2021-09-09T09:51:44.000000Z',
    'updated_at' => '2021-09-09T09:51:44.000000Z',
  ),
  36 =>
  array (
    'id' => 40,
    'name' => 'Create Label Translations',
    'guard_name' => 'admin',
    'module_id' => 10,
    'created_at' => '2021-09-09T09:58:36.000000Z',
    'updated_at' => '2021-09-14T13:19:29.000000Z',
  ),
  37 =>
  array (
    'id' => 41,
    'name' => 'Create Text Translations',
    'guard_name' => 'admin',
    'module_id' => 11,
    'created_at' => '2021-09-09T09:59:07.000000Z',
    'updated_at' => '2021-09-09T09:59:07.000000Z',
  ),
  38 =>
  array (
    'id' => 42,
    'name' => 'View General Settings',
    'guard_name' => 'admin',
    'module_id' => 12,
    'created_at' => '2021-09-09T10:02:38.000000Z',
    'updated_at' => '2021-09-14T13:16:43.000000Z',
  ),
  39 =>
  array (
    'id' => 43,
    'name' => 'View Sales Settings',
    'guard_name' => 'admin',
    'module_id' => 13,
    'created_at' => '2021-09-09T10:02:46.000000Z',
    'updated_at' => '2021-09-09T10:02:46.000000Z',
  ),
  40 =>
  array (
    'id' => 44,
    'name' => 'Add New Contact',
    'guard_name' => 'admin',
    'module_id' => 14,
    'created_at' => '2021-09-09T11:11:11.000000Z',
    'updated_at' => '2021-09-09T11:11:11.000000Z',
  ),
  41 =>
  array (
    'id' => 45,
    'name' => 'Contact Listing',
    'guard_name' => 'admin',
    'module_id' => 14,
    'created_at' => '2021-09-09T11:12:26.000000Z',
    'updated_at' => '2021-09-14T13:12:23.000000Z',
  ),
  42 =>
  array (
    'id' => 46,
    'name' => 'Edit Contact',
    'guard_name' => 'admin',
    'module_id' => 14,
    'created_at' => '2021-09-09T11:13:23.000000Z',
    'updated_at' => '2021-09-09T11:13:23.000000Z',
  ),
  43 =>
  array (
    'id' => 47,
    'name' => 'Delete Contact',
    'guard_name' => 'admin',
    'module_id' => 14,
    'created_at' => '2021-09-09T11:13:36.000000Z',
    'updated_at' => '2021-09-09T11:13:36.000000Z',
  ),
  44 =>
  array (
    'id' => 48,
    'name' => 'Duplicate Contact',
    'guard_name' => 'admin',
    'module_id' => 14,
    'created_at' => '2021-09-09T11:13:59.000000Z',
    'updated_at' => '2021-09-09T11:13:59.000000Z',
  ),
  45 =>
  array (
    'id' => 49,
    'name' => 'Archive Contact',
    'guard_name' => 'admin',
    'module_id' => 14,
    'created_at' => '2021-09-09T11:14:22.000000Z',
    'updated_at' => '2021-09-09T11:14:22.000000Z',
  ),
  46 =>
  array (
    'id' => 50,
    'name' => 'Send Message',
    'guard_name' => 'admin',
    'module_id' => 15,
    'created_at' => '2021-09-09T11:17:56.000000Z',
    'updated_at' => '2021-09-09T11:17:56.000000Z',
  ),
  47 =>
  array (
    'id' => 51,
    'name' => 'Add Note',
    'guard_name' => 'admin',
    'module_id' => 16,
    'created_at' => '2021-09-09T11:18:10.000000Z',
    'updated_at' => '2021-09-09T11:18:10.000000Z',
  ),
  48 =>
  array (
    'id' => 52,
    'name' => 'Add Schedule Activity',
    'guard_name' => 'admin',
    'module_id' => 17,
    'created_at' => '2021-09-09T11:19:03.000000Z',
    'updated_at' => '2021-09-09T11:19:03.000000Z',
  ),
  49 =>
  array (
    'id' => 53,
    'name' => 'Add New Contact Tags',
    'guard_name' => 'admin',
    'module_id' => 18,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  50 =>
  array (
    'id' => 54,
    'name' => 'Contact Tags Listing',
    'guard_name' => 'admin',
    'module_id' => 18,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:15:13.000000Z',
  ),
  51 =>
  array (
    'id' => 55,
    'name' => 'Edit Contact Tags',
    'guard_name' => 'admin',
    'module_id' => 18,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  52 =>
  array (
    'id' => 56,
    'name' => 'Delete Contact Tags',
    'guard_name' => 'admin',
    'module_id' => 18,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  53 =>
  array (
    'id' => 57,
    'name' => 'Add New Contact Titles',
    'guard_name' => 'admin',
    'module_id' => 19,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  54 =>
  array (
    'id' => 58,
    'name' => 'Contact Titles Listing',
    'guard_name' => 'admin',
    'module_id' => 19,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:15:37.000000Z',
  ),
  55 =>
  array (
    'id' => 59,
    'name' => 'Edit Contact Titles',
    'guard_name' => 'admin',
    'module_id' => 19,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  56 =>
  array (
    'id' => 60,
    'name' => 'Delete Contact Titles',
    'guard_name' => 'admin',
    'module_id' => 19,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  57 =>
  array (
    'id' => 61,
    'name' => 'Add New Contact Sector of Activities',
    'guard_name' => 'admin',
    'module_id' => 20,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  58 =>
  array (
    'id' => 62,
    'name' => 'Contact Sector of Activities Listing',
    'guard_name' => 'admin',
    'module_id' => 20,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:14:59.000000Z',
  ),
  59 =>
  array (
    'id' => 63,
    'name' => 'Edit Contact Sector of Activities',
    'guard_name' => 'admin',
    'module_id' => 20,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  60 =>
  array (
    'id' => 64,
    'name' => 'Delete Contact Sector of Activities',
    'guard_name' => 'admin',
    'module_id' => 20,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  61 =>
  array (
    'id' => 65,
    'name' => 'Add New Contact Currencies',
    'guard_name' => 'admin',
    'module_id' => 21,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  62 =>
  array (
    'id' => 66,
    'name' => 'Contact Currencies Listing',
    'guard_name' => 'admin',
    'module_id' => 21,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:14:05.000000Z',
  ),
  63 =>
  array (
    'id' => 67,
    'name' => 'Edit Contact Currencies',
    'guard_name' => 'admin',
    'module_id' => 21,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  64 =>
  array (
    'id' => 68,
    'name' => 'Delete Contact Currencies',
    'guard_name' => 'admin',
    'module_id' => 21,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  65 =>
  array (
    'id' => 77,
    'name' => 'Add New Contact Countries',
    'guard_name' => 'admin',
    'module_id' => 22,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  66 =>
  array (
    'id' => 78,
    'name' => 'Contact Countries Listing',
    'guard_name' => 'admin',
    'module_id' => 22,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:13:28.000000Z',
  ),
  67 =>
  array (
    'id' => 79,
    'name' => 'Edit Contact Countries',
    'guard_name' => 'admin',
    'module_id' => 22,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  68 =>
  array (
    'id' => 80,
    'name' => 'Delete Contact Countries',
    'guard_name' => 'admin',
    'module_id' => 22,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  69 =>
  array (
    'id' => 81,
    'name' => 'Add New Contact Fed. States',
    'guard_name' => 'admin',
    'module_id' => 23,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  70 =>
  array (
    'id' => 82,
    'name' => 'Contact Fed. States Listing',
    'guard_name' => 'admin',
    'module_id' => 23,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:14:40.000000Z',
  ),
  71 =>
  array (
    'id' => 83,
    'name' => 'Edit Contact Fed. States',
    'guard_name' => 'admin',
    'module_id' => 23,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  72 =>
  array (
    'id' => 84,
    'name' => 'Delete Contact Fed. States',
    'guard_name' => 'admin',
    'module_id' => 23,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  73 =>
  array (
    'id' => 85,
    'name' => 'Add New Contact Country Groups',
    'guard_name' => 'admin',
    'module_id' => 24,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  74 =>
  array (
    'id' => 86,
    'name' => 'Contact Country Groups Listing',
    'guard_name' => 'admin',
    'module_id' => 24,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:13:45.000000Z',
  ),
  75 =>
  array (
    'id' => 87,
    'name' => 'Edit Contact Country Groups',
    'guard_name' => 'admin',
    'module_id' => 24,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  76 =>
  array (
    'id' => 88,
    'name' => 'Delete Contact Country Groups',
    'guard_name' => 'admin',
    'module_id' => 24,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  77 =>
  array (
    'id' => 89,
    'name' => 'Add New Contact Banks',
    'guard_name' => 'admin',
    'module_id' => 25,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  78 =>
  array (
    'id' => 90,
    'name' => 'Contact Banks Listing',
    'guard_name' => 'admin',
    'module_id' => 25,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:13:06.000000Z',
  ),
  79 =>
  array (
    'id' => 91,
    'name' => 'Edit Contact Banks',
    'guard_name' => 'admin',
    'module_id' => 25,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  80 =>
  array (
    'id' => 92,
    'name' => 'Delete Contact Banks',
    'guard_name' => 'admin',
    'module_id' => 25,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  81 =>
  array (
    'id' => 93,
    'name' => 'Add New Bank Accounts',
    'guard_name' => 'admin',
    'module_id' => 26,
    'created_at' => '2021-09-09T11:20:41.000000Z',
    'updated_at' => '2021-09-09T11:20:41.000000Z',
  ),
  82 =>
  array (
    'id' => 94,
    'name' => 'Contact Bank Accounts Listing',
    'guard_name' => 'admin',
    'module_id' => 26,
    'created_at' => '2021-09-09T11:20:53.000000Z',
    'updated_at' => '2021-09-14T13:12:43.000000Z',
  ),
  83 =>
  array (
    'id' => 95,
    'name' => 'Edit Contact Bank Accounts',
    'guard_name' => 'admin',
    'module_id' => 26,
    'created_at' => '2021-09-09T11:21:06.000000Z',
    'updated_at' => '2021-09-09T11:21:06.000000Z',
  ),
  84 =>
  array (
    'id' => 96,
    'name' => 'Delete Contact Bank Accounts',
    'guard_name' => 'admin',
    'module_id' => 26,
    'created_at' => '2021-09-09T11:21:17.000000Z',
    'updated_at' => '2021-09-09T11:21:17.000000Z',
  ),
  85 =>
  array (
    'id' => 97,
    'name' => 'Filter Record Contacts',
    'guard_name' => 'admin',
    'module_id' => 14,
    'created_at' => '2021-09-09T11:35:15.000000Z',
    'updated_at' => '2021-09-09T11:35:15.000000Z',
  ),
  86 =>
  array (
    'id' => 98,
    'name' => 'Voucher Dashboard',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T11:48:14.000000Z',
    'updated_at' => '2021-09-09T11:48:14.000000Z',
  ),
  87 =>
  array (
    'id' => 99,
    'name' => 'Voucher Order Listing',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T11:49:28.000000Z',
    'updated_at' => '2021-09-09T11:49:28.000000Z',
  ),
  88 =>
  array (
    'id' => 100,
    'name' => 'Advance Filter Voucher Orders',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T11:52:53.000000Z',
    'updated_at' => '2021-09-09T11:52:53.000000Z',
  ),
  89 =>
  array (
    'id' => 101,
    'name' => 'Vouchers Payment',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T11:54:47.000000Z',
    'updated_at' => '2021-09-09T11:54:47.000000Z',
  ),
  90 =>
  array (
    'id' => 102,
    'name' => 'View Payment Voucher Invoice',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:32:42.000000Z',
    'updated_at' => '2021-09-13T14:29:54.000000Z',
  ),
  91 =>
  array (
    'id' => 103,
    'name' => 'Vouchers Listing',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:32:52.000000Z',
    'updated_at' => '2021-09-09T12:32:52.000000Z',
  ),
  92 =>
  array (
    'id' => 104,
    'name' => 'Voucher Disable',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:40:05.000000Z',
    'updated_at' => '2021-09-09T12:40:05.000000Z',
  ),
  93 =>
  array (
    'id' => 105,
    'name' => 'Voucher Redeemed',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:40:31.000000Z',
    'updated_at' => '2021-09-09T12:40:31.000000Z',
  ),
  94 =>
  array (
    'id' => 106,
    'name' => 'Download Vouchers',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:41:06.000000Z',
    'updated_at' => '2021-09-09T12:41:06.000000Z',
  ),
  95 =>
  array (
    'id' => 107,
    'name' => 'Voucher Approved',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:41:27.000000Z',
    'updated_at' => '2021-09-09T12:41:27.000000Z',
  ),
  96 =>
  array (
    'id' => 108,
    'name' => 'Voucher Reject',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:41:40.000000Z',
    'updated_at' => '2021-09-09T12:41:40.000000Z',
  ),
  97 =>
  array (
    'id' => 109,
    'name' => 'Voucher Activated / Inactive',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:41:59.000000Z',
    'updated_at' => '2021-09-13T12:52:20.000000Z',
  ),
  98 =>
  array (
    'id' => 110,
    'name' => 'Voucher Listing Filters',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:42:56.000000Z',
    'updated_at' => '2021-09-13T13:26:43.000000Z',
  ),
  99 =>
  array (
    'id' => 111,
    'name' => 'Bulk Disable Vouchers',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:44:08.000000Z',
    'updated_at' => '2021-09-09T12:44:08.000000Z',
  ),
  100 =>
  array (
    'id' => 112,
    'name' => 'Bulk Activate Vouchers',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:44:31.000000Z',
    'updated_at' => '2021-09-09T12:44:31.000000Z',
  ),
  101 =>
  array (
    'id' => 113,
    'name' => 'Bulk Redeemed Vouchers',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-09T12:45:14.000000Z',
    'updated_at' => '2021-09-09T12:45:14.000000Z',
  ),
  102 =>
  array (
    'id' => 114,
    'name' => 'License Dashboard',
    'guard_name' => 'admin',
    'module_id' => 28,
    'created_at' => '2021-09-09T12:54:28.000000Z',
    'updated_at' => '2021-09-09T12:54:28.000000Z',
  ),
  103 =>
  array (
    'id' => 115,
    'name' => 'Licenses Listing',
    'guard_name' => 'admin',
    'module_id' => 28,
    'created_at' => '2021-09-09T12:55:09.000000Z',
    'updated_at' => '2021-09-09T12:55:09.000000Z',
  ),
  104 =>
  array (
    'id' => 116,
    'name' => 'Import License Keys',
    'guard_name' => 'admin',
    'module_id' => 28,
    'created_at' => '2021-09-09T12:56:10.000000Z',
    'updated_at' => '2021-09-09T12:56:10.000000Z',
  ),
  105 =>
  array (
    'id' => 117,
    'name' => 'Advance Filter Licenses',
    'guard_name' => 'admin',
    'module_id' => 28,
    'created_at' => '2021-09-09T12:57:00.000000Z',
    'updated_at' => '2021-09-09T12:57:00.000000Z',
  ),
  106 =>
  array (
    'id' => 118,
    'name' => 'License Active/Inactive',
    'guard_name' => 'admin',
    'module_id' => 28,
    'created_at' => '2021-09-09T12:57:48.000000Z',
    'updated_at' => '2021-09-09T12:57:48.000000Z',
  ),
  107 =>
  array (
    'id' => 119,
    'name' => 'License Expired',
    'guard_name' => 'admin',
    'module_id' => 28,
    'created_at' => '2021-09-09T12:58:04.000000Z',
    'updated_at' => '2021-09-09T12:58:04.000000Z',
  ),
  108 =>
  array (
    'id' => 120,
    'name' => 'License Mark As Read',
    'guard_name' => 'admin',
    'module_id' => 28,
    'created_at' => '2021-09-09T12:58:25.000000Z',
    'updated_at' => '2021-09-09T12:58:25.000000Z',
  ),
  109 =>
  array (
    'id' => 121,
    'name' => 'Website Dashboard',
    'guard_name' => 'admin',
    'module_id' => 29,
    'created_at' => '2021-09-09T13:00:17.000000Z',
    'updated_at' => '2021-09-09T13:00:17.000000Z',
  ),
  110 =>
  array (
    'id' => 122,
    'name' => 'Website Abandoned Cart Listing',
    'guard_name' => 'admin',
    'module_id' => 30,
    'created_at' => '2021-09-09T13:02:05.000000Z',
    'updated_at' => '2021-09-09T13:02:05.000000Z',
  ),
  111 =>
  array (
    'id' => 123,
    'name' => 'Projects Listing',
    'guard_name' => 'admin',
    'module_id' => 31,
    'created_at' => '2021-09-09T13:04:17.000000Z',
    'updated_at' => '2021-09-09T13:04:17.000000Z',
  ),
  112 =>
  array (
    'id' => 124,
    'name' => 'Visitors Listing',
    'guard_name' => 'admin',
    'module_id' => 32,
    'created_at' => '2021-09-09T13:30:14.000000Z',
    'updated_at' => '2021-09-09T13:30:14.000000Z',
  ),
  113 =>
  array (
    'id' => 125,
    'name' => 'Visitors Detail',
    'guard_name' => 'admin',
    'module_id' => 32,
    'created_at' => '2021-09-09T13:30:57.000000Z',
    'updated_at' => '2021-09-09T13:30:57.000000Z',
  ),
  114 =>
  array (
    'id' => 126,
    'name' => 'Views Listing',
    'guard_name' => 'admin',
    'module_id' => 33,
    'created_at' => '2021-09-09T13:36:06.000000Z',
    'updated_at' => '2021-09-09T13:36:06.000000Z',
  ),
  115 =>
  array (
    'id' => 127,
    'name' => 'Lawful Interception Listing',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T13:37:39.000000Z',
    'updated_at' => '2021-09-09T13:37:39.000000Z',
  ),
  116 =>
  array (
    'id' => 128,
    'name' => 'Reseller Details PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T13:40:00.000000Z',
    'updated_at' => '2021-09-09T13:40:00.000000Z',
  ),
  117 =>
  array (
    'id' => 129,
    'name' => 'Reseller Orders PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T13:40:10.000000Z',
    'updated_at' => '2021-09-09T13:40:10.000000Z',
  ),
  118 =>
  array (
    'id' => 130,
    'name' => 'Reseller Vouchers PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T13:40:18.000000Z',
    'updated_at' => '2021-09-09T13:40:18.000000Z',
  ),
  119 =>
  array (
    'id' => 131,
    'name' => 'Reseller Vouchers Payment PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T13:40:27.000000Z',
    'updated_at' => '2021-09-09T13:40:27.000000Z',
  ),
  120 =>
  array (
    'id' => 132,
    'name' => 'Reseller Download All Data',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T13:40:36.000000Z',
    'updated_at' => '2021-09-09T13:40:36.000000Z',
  ),
  121 =>
  array (
    'id' => 133,
    'name' => 'FAQs Listing',
    'guard_name' => 'admin',
    'module_id' => 35,
    'created_at' => '2021-09-09T13:41:52.000000Z',
    'updated_at' => '2021-09-09T13:41:52.000000Z',
  ),
  122 =>
  array (
    'id' => 134,
    'name' => 'Create FAQ',
    'guard_name' => 'admin',
    'module_id' => 35,
    'created_at' => '2021-09-09T13:42:15.000000Z',
    'updated_at' => '2021-09-09T13:42:15.000000Z',
  ),
  123 =>
  array (
    'id' => 135,
    'name' => 'Edit FAQ',
    'guard_name' => 'admin',
    'module_id' => 35,
    'created_at' => '2021-09-09T13:42:26.000000Z',
    'updated_at' => '2021-09-09T13:42:26.000000Z',
  ),
  124 =>
  array (
    'id' => 136,
    'name' => 'Delete FAQ',
    'guard_name' => 'admin',
    'module_id' => 35,
    'created_at' => '2021-09-09T13:42:35.000000Z',
    'updated_at' => '2021-09-09T13:42:35.000000Z',
  ),
  125 =>
  array (
    'id' => 137,
    'name' => 'Contact Us Queries Listing',
    'guard_name' => 'admin',
    'module_id' => 36,
    'created_at' => '2021-09-09T13:43:05.000000Z',
    'updated_at' => '2021-09-09T13:43:05.000000Z',
  ),
  126 =>
  array (
    'id' => 138,
    'name' => 'Edit Contact Us Query',
    'guard_name' => 'admin',
    'module_id' => 36,
    'created_at' => '2021-09-09T13:43:21.000000Z',
    'updated_at' => '2021-09-09T13:43:21.000000Z',
  ),
  127 =>
  array (
    'id' => 139,
    'name' => 'Payment Gateway Settings',
    'guard_name' => 'admin',
    'module_id' => 37,
    'created_at' => '2021-09-09T13:47:01.000000Z',
    'updated_at' => '2021-09-09T13:47:01.000000Z',
  ),
  128 =>
  array (
    'id' => 140,
    'name' => 'Sales Dashboard',
    'guard_name' => 'admin',
    'module_id' => 38,
    'created_at' => '2021-09-09T14:00:04.000000Z',
    'updated_at' => '2021-09-09T14:00:04.000000Z',
  ),
  129 =>
  array (
    'id' => 141,
    'name' => 'Quotations Listing',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:01:04.000000Z',
    'updated_at' => '2021-09-09T14:01:04.000000Z',
  ),
  130 =>
  array (
    'id' => 142,
    'name' => 'Create Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:07:57.000000Z',
    'updated_at' => '2021-09-09T14:07:57.000000Z',
  ),
  131 =>
  array (
    'id' => 143,
    'name' => 'Edit Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:09:24.000000Z',
    'updated_at' => '2021-09-09T14:09:24.000000Z',
  ),
  132 =>
  array (
    'id' => 144,
    'name' => 'View Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:09:40.000000Z',
    'updated_at' => '2021-09-09T14:09:40.000000Z',
  ),
  133 =>
  array (
    'id' => 145,
    'name' => 'Delete Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:09:50.000000Z',
    'updated_at' => '2021-09-09T14:09:50.000000Z',
  ),
  134 =>
  array (
    'id' => 146,
    'name' => 'Confirm Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:10:41.000000Z',
    'updated_at' => '2021-09-09T14:10:41.000000Z',
  ),
  135 =>
  array (
    'id' => 147,
    'name' => 'Lock Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:11:23.000000Z',
    'updated_at' => '2021-09-09T14:11:23.000000Z',
  ),
  136 =>
  array (
    'id' => 148,
    'name' => 'Unlock Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:11:37.000000Z',
    'updated_at' => '2021-09-09T14:11:37.000000Z',
  ),
  137 =>
  array (
    'id' => 149,
    'name' => 'Cancel Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:11:51.000000Z',
    'updated_at' => '2021-09-09T14:11:51.000000Z',
  ),
  138 =>
  array (
    'id' => 150,
    'name' => 'Send By Email',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:12:22.000000Z',
    'updated_at' => '2021-09-09T14:13:25.000000Z',
  ),
  139 =>
  array (
    'id' => 151,
    'name' => 'Send Pro-Forma Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:13:16.000000Z',
    'updated_at' => '2021-09-09T14:13:16.000000Z',
  ),
  140 =>
  array (
    'id' => 152,
    'name' => 'Mark Quotation As Sent',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:32:49.000000Z',
    'updated_at' => '2021-09-09T14:32:49.000000Z',
  ),
  141 =>
  array (
    'id' => 153,
    'name' => 'Generate Payment Link',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:33:16.000000Z',
    'updated_at' => '2021-09-09T14:33:16.000000Z',
  ),
  142 =>
  array (
    'id' => 154,
    'name' => 'Duplicate Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:33:42.000000Z',
    'updated_at' => '2021-09-09T14:33:42.000000Z',
  ),
  143 =>
  array (
    'id' => 155,
    'name' => 'Customer Preview',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:35:19.000000Z',
    'updated_at' => '2021-09-09T14:35:19.000000Z',
  ),
  144 =>
  array (
    'id' => 156,
    'name' => 'Quotation Invoices',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:36:20.000000Z',
    'updated_at' => '2021-09-09T14:36:20.000000Z',
  ),
  145 =>
  array (
    'id' => 157,
    'name' => 'View Quotation Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-09T14:36:39.000000Z',
    'updated_at' => '2021-09-14T08:01:10.000000Z',
  ),
  146 =>
  array (
    'id' => 158,
    'name' => 'Orders Listing',
    'guard_name' => 'admin',
    'module_id' => 40,
    'created_at' => '2021-09-09T14:39:01.000000Z',
    'updated_at' => '2021-09-09T14:39:01.000000Z',
  ),
  147 =>
  array (
    'id' => 159,
    'name' => 'View Sales Analytics',
    'guard_name' => 'admin',
    'module_id' => 41,
    'created_at' => '2021-09-09T14:40:43.000000Z',
    'updated_at' => '2021-09-09T14:40:43.000000Z',
  ),
  148 =>
  array (
    'id' => 160,
    'name' => 'View Sale Analysis',
    'guard_name' => 'admin',
    'module_id' => 41,
    'created_at' => '2021-09-09T14:41:15.000000Z',
    'updated_at' => '2021-09-09T14:41:15.000000Z',
  ),
  149 =>
  array (
    'id' => 161,
    'name' => 'Customers Listing',
    'guard_name' => 'admin',
    'module_id' => 42,
    'created_at' => '2021-09-09T14:44:42.000000Z',
    'updated_at' => '2021-09-09T14:44:42.000000Z',
  ),
  150 =>
  array (
    'id' => 162,
    'name' => 'Add Customer',
    'guard_name' => 'admin',
    'module_id' => 42,
    'created_at' => '2021-09-09T14:45:22.000000Z',
    'updated_at' => '2021-09-14T10:22:28.000000Z',
  ),
  151 =>
  array (
    'id' => 163,
    'name' => 'Edit Customer',
    'guard_name' => 'admin',
    'module_id' => 42,
    'created_at' => '2021-09-09T14:45:33.000000Z',
    'updated_at' => '2021-09-14T10:24:46.000000Z',
  ),
  152 =>
  array (
    'id' => 164,
    'name' => 'Delete Customer',
    'guard_name' => 'admin',
    'module_id' => 42,
    'created_at' => '2021-09-09T14:46:15.000000Z',
    'updated_at' => '2021-09-14T10:25:41.000000Z',
  ),
  153 =>
  array (
    'id' => 165,
    'name' => 'Customer Resend Invitation',
    'guard_name' => 'admin',
    'module_id' => 42,
    'created_at' => '2021-09-09T14:46:43.000000Z',
    'updated_at' => '2021-09-09T14:46:43.000000Z',
  ),
  154 =>
  array (
    'id' => 166,
    'name' => 'Products Listing',
    'guard_name' => 'admin',
    'module_id' => 43,
    'created_at' => '2021-09-10T05:37:57.000000Z',
    'updated_at' => '2021-09-10T05:37:57.000000Z',
  ),
  155 =>
  array (
    'id' => 167,
    'name' => 'Add Product',
    'guard_name' => 'admin',
    'module_id' => 43,
    'created_at' => '2021-09-10T05:38:23.000000Z',
    'updated_at' => '2021-09-10T05:38:23.000000Z',
  ),
  156 =>
  array (
    'id' => 168,
    'name' => 'Edit Product',
    'guard_name' => 'admin',
    'module_id' => 43,
    'created_at' => '2021-09-10T05:38:35.000000Z',
    'updated_at' => '2021-09-10T05:38:35.000000Z',
  ),
  157 =>
  array (
    'id' => 169,
    'name' => 'Delete Product',
    'guard_name' => 'admin',
    'module_id' => 43,
    'created_at' => '2021-09-10T05:40:05.000000Z',
    'updated_at' => '2021-09-10T05:40:05.000000Z',
  ),
  158 =>
  array (
    'id' => 170,
    'name' => 'Archive / Unarchive Product',
    'guard_name' => 'admin',
    'module_id' => 43,
    'created_at' => '2021-09-10T05:40:54.000000Z',
    'updated_at' => '2021-09-14T10:53:58.000000Z',
  ),
  159 =>
  array (
    'id' => 171,
    'name' => 'Configure Variants Listing',
    'guard_name' => 'admin',
    'module_id' => 43,
    'created_at' => '2021-09-10T05:41:45.000000Z',
    'updated_at' => '2021-09-10T05:41:45.000000Z',
  ),
  160 =>
  array (
    'id' => 172,
    'name' => 'Configure Variant',
    'guard_name' => 'admin',
    'module_id' => 43,
    'created_at' => '2021-09-10T05:42:03.000000Z',
    'updated_at' => '2021-09-10T05:42:03.000000Z',
  ),
  161 =>
  array (
    'id' => 173,
    'name' => 'Product Variant Listing',
    'guard_name' => 'admin',
    'module_id' => 44,
    'created_at' => '2021-09-10T05:42:40.000000Z',
    'updated_at' => '2021-09-10T05:42:40.000000Z',
  ),
  162 =>
  array (
    'id' => 174,
    'name' => 'Edit Product Variant',
    'guard_name' => 'admin',
    'module_id' => 44,
    'created_at' => '2021-09-10T05:42:54.000000Z',
    'updated_at' => '2021-09-10T05:42:54.000000Z',
  ),
  163 =>
  array (
    'id' => 175,
    'name' => 'Price Lists Listing',
    'guard_name' => 'admin',
    'module_id' => 45,
    'created_at' => '2021-09-10T05:46:11.000000Z',
    'updated_at' => '2021-09-10T05:46:11.000000Z',
  ),
  164 =>
  array (
    'id' => 176,
    'name' => 'Add Price List',
    'guard_name' => 'admin',
    'module_id' => 45,
    'created_at' => '2021-09-10T05:46:24.000000Z',
    'updated_at' => '2021-09-10T05:46:24.000000Z',
  ),
  165 =>
  array (
    'id' => 177,
    'name' => 'Edit Price List',
    'guard_name' => 'admin',
    'module_id' => 45,
    'created_at' => '2021-09-10T05:46:41.000000Z',
    'updated_at' => '2021-09-10T05:46:41.000000Z',
  ),
  166 =>
  array (
    'id' => 178,
    'name' => 'Delete Price List',
    'guard_name' => 'admin',
    'module_id' => 45,
    'created_at' => '2021-09-10T05:46:57.000000Z',
    'updated_at' => '2021-09-10T05:46:57.000000Z',
  ),
  167 =>
  array (
    'id' => 179,
    'name' => 'Archive / Unarchive Price List',
    'guard_name' => 'admin',
    'module_id' => 45,
    'created_at' => '2021-09-10T05:47:33.000000Z',
    'updated_at' => '2021-09-14T11:20:29.000000Z',
  ),
  168 =>
  array (
    'id' => 180,
    'name' => 'Sales Team Analysis',
    'guard_name' => 'admin',
    'module_id' => 46,
    'created_at' => '2021-09-10T05:49:12.000000Z',
    'updated_at' => '2021-09-10T05:49:12.000000Z',
  ),
  169 =>
  array (
    'id' => 181,
    'name' => 'Advanced Sales Analysis Filters',
    'guard_name' => 'admin',
    'module_id' => 46,
    'created_at' => '2021-09-10T05:50:20.000000Z',
    'updated_at' => '2021-09-10T05:50:20.000000Z',
  ),
  170 =>
  array (
    'id' => 182,
    'name' => 'Sales Team Listing',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-10T05:51:15.000000Z',
    'updated_at' => '2021-09-10T05:51:15.000000Z',
  ),
  171 =>
  array (
    'id' => 183,
    'name' => 'Add Sales Team',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-10T05:51:35.000000Z',
    'updated_at' => '2021-09-10T05:51:35.000000Z',
  ),
  172 =>
  array (
    'id' => 184,
    'name' => 'Edit Sales Team',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-10T05:52:03.000000Z',
    'updated_at' => '2021-09-10T05:52:03.000000Z',
  ),
  173 =>
  array (
    'id' => 185,
    'name' => 'Delete Sales Team',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-10T05:52:19.000000Z',
    'updated_at' => '2021-09-10T05:52:19.000000Z',
  ),
  174 =>
  array (
    'id' => 186,
    'name' => 'View Sales Team',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-10T05:53:05.000000Z',
    'updated_at' => '2021-09-14T12:04:20.000000Z',
  ),
  175 =>
  array (
    'id' => 187,
    'name' => 'Sales Team Member Listing',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-10T05:53:37.000000Z',
    'updated_at' => '2021-09-10T05:53:37.000000Z',
  ),
  176 =>
  array (
    'id' => 188,
    'name' => 'Taxes Listing',
    'guard_name' => 'admin',
    'module_id' => 48,
    'created_at' => '2021-09-10T05:54:21.000000Z',
    'updated_at' => '2021-09-10T05:54:21.000000Z',
  ),
  177 =>
  array (
    'id' => 189,
    'name' => 'Add Tax',
    'guard_name' => 'admin',
    'module_id' => 48,
    'created_at' => '2021-09-10T05:54:38.000000Z',
    'updated_at' => '2021-09-10T05:54:38.000000Z',
  ),
  178 =>
  array (
    'id' => 190,
    'name' => 'Edit Tax',
    'guard_name' => 'admin',
    'module_id' => 48,
    'created_at' => '2021-09-10T05:54:48.000000Z',
    'updated_at' => '2021-09-10T05:54:48.000000Z',
  ),
  179 =>
  array (
    'id' => 191,
    'name' => 'Delete Tax',
    'guard_name' => 'admin',
    'module_id' => 48,
    'created_at' => '2021-09-10T05:54:56.000000Z',
    'updated_at' => '2021-09-10T05:54:56.000000Z',
  ),
  180 =>
  array (
    'id' => 192,
    'name' => 'Ecommerce Categories Listing',
    'guard_name' => 'admin',
    'module_id' => 49,
    'created_at' => '2021-09-10T05:56:15.000000Z',
    'updated_at' => '2021-09-10T05:56:15.000000Z',
  ),
  181 =>
  array (
    'id' => 193,
    'name' => 'Add Ecommerce Categories',
    'guard_name' => 'admin',
    'module_id' => 49,
    'created_at' => '2021-09-10T05:56:26.000000Z',
    'updated_at' => '2021-09-10T05:56:26.000000Z',
  ),
  182 =>
  array (
    'id' => 194,
    'name' => 'Edit Ecommerce Categories',
    'guard_name' => 'admin',
    'module_id' => 49,
    'created_at' => '2021-09-10T05:56:38.000000Z',
    'updated_at' => '2021-09-10T05:56:38.000000Z',
  ),
  183 =>
  array (
    'id' => 195,
    'name' => 'Delete Ecommerce Categories',
    'guard_name' => 'admin',
    'module_id' => 49,
    'created_at' => '2021-09-10T05:56:49.000000Z',
    'updated_at' => '2021-09-10T05:56:49.000000Z',
  ),
  184 =>
  array (
    'id' => 196,
    'name' => 'Attributes Listing',
    'guard_name' => 'admin',
    'module_id' => 50,
    'created_at' => '2021-09-10T05:57:28.000000Z',
    'updated_at' => '2021-09-10T05:57:28.000000Z',
  ),
  185 =>
  array (
    'id' => 197,
    'name' => 'Add Attributes',
    'guard_name' => 'admin',
    'module_id' => 50,
    'created_at' => '2021-09-10T05:57:37.000000Z',
    'updated_at' => '2021-09-10T05:57:37.000000Z',
  ),
  186 =>
  array (
    'id' => 198,
    'name' => 'Edit Attributes',
    'guard_name' => 'admin',
    'module_id' => 50,
    'created_at' => '2021-09-10T05:57:46.000000Z',
    'updated_at' => '2021-09-10T05:57:46.000000Z',
  ),
  187 =>
  array (
    'id' => 199,
    'name' => 'Delete Attributes',
    'guard_name' => 'admin',
    'module_id' => 50,
    'created_at' => '2021-09-10T05:57:59.000000Z',
    'updated_at' => '2021-09-10T05:57:59.000000Z',
  ),
  188 =>
  array (
    'id' => 200,
    'name' => 'Make Voucher Payment',
    'guard_name' => 'admin',
    'module_id' => 27,
    'created_at' => '2021-09-13T14:18:06.000000Z',
    'updated_at' => '2021-09-13T14:18:06.000000Z',
  ),
  189 =>
  array (
    'id' => 201,
    'name' => 'Create Quotation Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-14T06:29:10.000000Z',
    'updated_at' => '2021-09-14T06:29:10.000000Z',
  ),
  190 =>
  array (
    'id' => 202,
    'name' => 'Set To Quotation',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-14T06:40:03.000000Z',
    'updated_at' => '2021-09-14T06:40:03.000000Z',
  ),
  191 =>
  array (
    'id' => 204,
    'name' => 'Confirm Quotation Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-14T07:46:55.000000Z',
    'updated_at' => '2021-09-14T07:46:55.000000Z',
  ),
  192 =>
  array (
    'id' => 205,
    'name' => 'Cancel Quotation Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-14T07:47:48.000000Z',
    'updated_at' => '2021-09-14T07:47:48.000000Z',
  ),
  193 =>
  array (
    'id' => 206,
    'name' => 'Register Payment Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-14T07:48:24.000000Z',
    'updated_at' => '2021-09-14T07:48:24.000000Z',
  ),
  194 =>
  array (
    'id' => 207,
    'name' => 'Download Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-14T07:48:54.000000Z',
    'updated_at' => '2021-09-14T07:48:54.000000Z',
  ),
  195 =>
  array (
    'id' => 208,
    'name' => 'Reset To Draft Invoice',
    'guard_name' => 'admin',
    'module_id' => 39,
    'created_at' => '2021-09-14T07:51:13.000000Z',
    'updated_at' => '2021-09-14T07:51:13.000000Z',
  ),
  196 =>
  array (
    'id' => 209,
    'name' => 'Contact & Addresses',
    'guard_name' => 'admin',
    'module_id' => 42,
    'created_at' => '2021-09-14T10:26:12.000000Z',
    'updated_at' => '2021-09-14T10:26:12.000000Z',
  ),
  197 =>
  array (
    'id' => 210,
    'name' => 'Add Team Member',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-14T12:04:37.000000Z',
    'updated_at' => '2021-09-14T12:04:37.000000Z',
  ),
  198 =>
  array (
    'id' => 211,
    'name' => 'Sales Team Filter Record',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-14T12:10:40.000000Z',
    'updated_at' => '2021-09-14T12:10:40.000000Z',
  ),
  199 =>
  array (
    'id' => 212,
    'name' => 'Archive / Unarchive Sales Team',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-14T12:23:23.000000Z',
    'updated_at' => '2021-09-14T12:23:23.000000Z',
  ),
  200 =>
  array (
    'id' => 213,
    'name' => 'Duplicate Sales Team',
    'guard_name' => 'admin',
    'module_id' => 47,
    'created_at' => '2021-09-14T12:25:09.000000Z',
    'updated_at' => '2021-09-14T12:25:09.000000Z',
  ),
  201 =>
  array (
    'id' => 214,
    'name' => 'Archive / Unarchive User',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-14T13:50:12.000000Z',
    'updated_at' => '2021-09-14T13:50:12.000000Z',
  ),
  202 =>
  array (
    'id' => 215,
    'name' => 'Duplicate User',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-14T13:50:36.000000Z',
    'updated_at' => '2021-09-14T13:50:36.000000Z',
  ),
  203 =>
  array (
    'id' => 216,
    'name' => 'Reseller Listing',
    'guard_name' => 'admin',
    'module_id' => 51,
    'created_at' => '2021-09-14T13:50:53.000000Z',
    'updated_at' => '2021-09-14T13:50:53.000000Z',
  ),
  204 =>
  array (
    'id' => 217,
    'name' => 'Send Password Reset Instruction',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-14T13:50:53.000000Z',
    'updated_at' => '2021-09-14T13:50:53.000000Z',
  ),
  205 =>
  array (
    'id' => 218,
    'name' => 'Send / Re-Send Invitation Email',
    'guard_name' => 'admin',
    'module_id' => 1,
    'created_at' => '2021-09-14T13:50:53.000000Z',
    'updated_at' => '2021-09-14T13:50:53.000000Z',
  ),
  206 =>
  array (
    'id' => 219,
    'name' => 'Add New Role',
    'guard_name' => 'admin',
    'module_id' => 52,
    'created_at' => '2021-09-09T07:17:36.000000Z',
    'updated_at' => '2021-09-09T07:17:36.000000Z',
  ),
  207 =>
  array (
    'id' => 220,
    'name' => 'Roles Listing',
    'guard_name' => 'admin',
    'module_id' => 52,
    'created_at' => '2021-09-09T07:19:39.000000Z',
    'updated_at' => '2021-09-14T13:29:57.000000Z',
  ),
  208 =>
  array (
    'id' => 221,
    'name' => 'Edit Role',
    'guard_name' => 'admin',
    'module_id' => 52,
    'created_at' => '2021-09-09T07:26:05.000000Z',
    'updated_at' => '2021-09-09T07:26:05.000000Z',
  ),
  209 =>
  array (
    'id' => 222,
    'name' => 'Delete Role',
    'guard_name' => 'admin',
    'module_id' => 52,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  210 =>
  array (
    'id' => 223,
    'name' => 'Customer Details PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
   211 =>
  array (
    'id' => 224,
    'name' => 'Customer Orders PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  212 =>
  array (
    'id' => 225,
    'name' => 'Customer Order Invoices PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  213 =>
  array (
    'id' => 226,
    'name' => 'Download Customer All Data',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  214 =>
  array (
    'id' => 227,
    'name' => 'Customer Carts PDF',
    'guard_name' => 'admin',
    'module_id' => 34,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  215 =>
  array (
    'id' => 228,
    'name' => 'View Log Note',
    'guard_name' => 'admin',
    'module_id' => 16,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  216 =>
  array (
    'id' => 229,
    'name' => 'View Send Messages',
    'guard_name' => 'admin',
    'module_id' => 15,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
  217 =>
  array (
    'id' => 230,
    'name' => 'View Schedule Activity',
    'guard_name' => 'admin',
    'module_id' => 17,
    'created_at' => '2021-09-09T07:26:17.000000Z',
    'updated_at' => '2021-09-09T07:26:17.000000Z',
  ),
));

    }
}
