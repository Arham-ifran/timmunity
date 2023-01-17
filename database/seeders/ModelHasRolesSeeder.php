<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModelHasRolesSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('model_has_roles')->delete();

        \DB::table('model_has_roles')->insert(array(
            0 =>
            array(
                'role_id' => 1,
                'model_type' => 'App\Models\Admin',
                'model_id' => 1,
            ),
        ));


    }
}
