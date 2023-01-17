<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TimezoneSeeder::class,
            AdminTableSeeder::class,
            CmsPagesTableSeeder::class,
            EventTypeSeeder::class,
            ActivityTypesTableSeeder::class,
            CountriesTableSeeder::class,
            LanguagesTableSeeder::class,
            CurrenciesTableSeeder::class,
            ProductTypeInProductModuleSeeder::class,
            ProductCategoryInProductModuleSeeder::class,
            ProductTableSeeder::class,
            PaymentTermSeeder::class,
            SalesSettingsSeeder::class,
            PriceListSeeder::class,
            SiteSettingsSeeder::class,
            SalesTeamTableSeeder::class,
            LanguageModulesTableSeeder::class,
            ProjectTableSeeder::class,
            EmailTemplatesTableSeeder::class,
            EmailTemplateLabelsTableSeeder::class,
            PaymentGatewayTableSeeder::class,
            PermissionsTableSeeder::class,
            ModulesTableSeeder::class,
            RolesTableSeeder::class,
            ModelHasRolesSeeder::class,
            RolesHasPermissionsSeeder::class,
        ]);
    }
}
