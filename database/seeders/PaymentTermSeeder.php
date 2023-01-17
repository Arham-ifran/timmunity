<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('payment_terms')->delete();
        \DB::table('payment_terms')->insert(array(
            array('id' => 1, 'term_type' => 1, 'term_value' => 0, 'term_advance' => NULL, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),

        ));
    }
}
