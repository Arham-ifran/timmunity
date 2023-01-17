<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('languages')->delete();

        \DB::table('languages')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'English',
                'iso_code' => 'en',
                'local_code' => 'en',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:20:16',
                'updated_at' => '2021-03-18 16:20:16',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'German',
                'iso_code' => 'de',
                'local_code' => 'de',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:20:16',
                'updated_at' => '2021-03-18 16:20:16',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'French',
                'iso_code' => 'fr',
                'local_code' => 'fr',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:20:16',
                'updated_at' => '2021-03-18 16:20:16',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Spanish',
                'iso_code' => 'es',
                'local_code' => 'es',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:20:16',
                'updated_at' => '2021-03-18 16:20:16',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Portuguese - Brazil',
                'iso_code' => 'br',
                'local_code' => 'br',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:20:16',
                'updated_at' => '2021-03-26 10:37:04',
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'Portuguese - Portugal ',
                'iso_code' => 'pt',
                'local_code' => 'pt',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:23:06',
                'updated_at' => '2021-03-26 10:37:55',
            ),
            6 =>
            array (
                'id' => 7,
                'name' => 'Italian',
                'iso_code' => 'it',
                'local_code' => 'it',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:25:01',
                'updated_at' => '2021-03-18 16:25:01',
            ),
            7 =>
            array (
                'id' => 8,
                'name' => 'Dutch',
                'iso_code' => 'nl',
                'local_code' => 'nl',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:25:01',
                'updated_at' => '2021-03-18 16:25:01',
            ),
            8 =>
            array (
                'id' => 9,
                'name' => 'Polish',
                'iso_code' => 'pl',
                'local_code' => 'pl',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:25:31',
                'updated_at' => '2021-03-18 16:25:31',
            ),
            9 =>
            array (
                'id' => 10,
                'name' => 'Russian',
                'iso_code' => 'ru',
                'local_code' => 'ru',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:31:59',
                'updated_at' => '2021-03-26 06:24:52',
            ),
            10 =>
            array (
                'id' => 11,
                'name' => 'Japanese',
                'iso_code' => 'ja',
                'local_code' => 'ja',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:31:59',
                'updated_at' => '2021-03-18 16:31:59',
            ),
            11 =>
            array (
                'id' => 12,
                'name' => 'Bulgarian',
                'iso_code' => 'bg',
                'local_code' => 'bg',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:32:49',
                'updated_at' => '2021-03-18 16:32:49',
            ),
            12 =>
            array (
                'id' => 13,
                'name' => 'Czech',
                'iso_code' => 'cs',
                'local_code' => 'cs',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:33:47',
                'updated_at' => '2021-03-18 16:33:47',
            ),
            13 =>
            array (
                'id' => 14,
                'name' => 'Danish',
                'iso_code' => 'da',
                'local_code' => 'da',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:34:23',
                'updated_at' => '2021-03-18 16:34:23',
            ),
            14 =>
            array (
                'id' => 15,
                'name' => 'Greek',
                'iso_code' => 'el',
                'local_code' => 'el',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:35:02',
                'updated_at' => '2021-03-18 16:35:02',
            ),
            15 =>
            array (
                'id' => 16,
                'name' => 'Estonian',
                'iso_code' => 'et',
                'local_code' => 'et',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:35:51',
                'updated_at' => '2021-04-12 07:57:10',
            ),
            16 =>
            array (
                'id' => 17,
                'name' => 'Finnish',
                'iso_code' => 'fi',
                'local_code' => 'fi',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:36:30',
                'updated_at' => '2021-07-15 06:36:23',
            ),
            17 =>
            array (
                'id' => 18,
                'name' => 'Hungarian',
                'iso_code' => 'fu',
                'local_code' => 'fu',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:37:16',
                'updated_at' => '2021-03-18 16:37:16',
            ),
            18 =>
            array (
                'id' => 19,
                'name' => 'Lithuanian',
                'iso_code' => 'lt',
                'local_code' => 'lt',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-18 16:38:12',
                'updated_at' => '2021-03-26 10:39:44',
            ),
            19 =>
            array (
                'id' => 20,
                'name' => 'Latvian',
                'iso_code' => 'lv',
                'local_code' => 'lv',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-26 06:12:22',
                'updated_at' => '2021-03-26 06:12:22',
            ),
            20 =>
            array (
                'id' => 21,
                'name' => 'Romanian',
                'iso_code' => 'ro',
                'local_code' => 'ro',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-26 06:39:44',
                'updated_at' => '2021-03-26 06:39:44',
            ),
            21 =>
            array (
                'id' => 22,
            'name' => 'Chinese (simplified)',
                'iso_code' => 'zh',
                'local_code' => 'zh',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-26 06:39:44',
                'updated_at' => '2021-03-26 06:39:44',
            ),
            22 =>
            array (
                'id' => 23,
                'name' => 'Slovak',
                'iso_code' => 'sk',
                'local_code' => 'sk',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-26 06:39:44',
                'updated_at' => '2021-03-26 06:39:44',
            ),
            23 =>
            array (
                'id' => 24,
                'name' => 'Slovenian',
                'iso_code' => 'sl',
                'local_code' => 'sl',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-26 06:39:44',
                'updated_at' => '2021-03-26 06:39:44',
            ),
            24 =>
            array (
                'id' => 25,
                'name' => 'Swedish',
                'iso_code' => 'sv',
                'local_code' => 'sv',
                'is_active' => 1,
                'is_archive' => 0,
                'created_at' => '2021-03-26 06:39:44',
                'updated_at' => '2021-03-26 06:39:44',
            ),
        ));


    }
}
