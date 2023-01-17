<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LanguageModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('language_modules')->delete();

        \DB::table('language_modules')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Quotations',
                'table' => 'quotations',
                'columns' => 'terms_and_conditions',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'Quotation Order Lines',
                'table' => 'quotation_order_lines',
                'columns' => 'notes',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'Quotation Text Templates',
                'table' => 'quotation_text_templates',
                'columns' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Contacts',
                'table' => 'contacts',
                'columns' => 'street_1,street_2,city,internal_notes',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Contact Addresses',
                'table' => 'contact_addresses',
                'columns' => 'street_1,street_2,city,notes',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'Contact Sectors Activities',
                'table' => 'contact_sectors_activities',
                'columns' => 'description',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            6 =>
            array (
                'id' => 7,
                'name' => 'Contact Banks',
                'table' => 'contact_banks',
                'columns' => 'street_1,street_2,city',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            7 =>
            array (
                'id' => 8,
                'name' => 'Admins',
                'table' => 'admins',
                'columns' => 'email_signature',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            8 =>
            array (
                'id' => 9,
                'name' => 'Taxes',
                'table' => 'taxes',
                'columns' => 'name',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            9 =>
            array (
                'id' => 10,
                'name' => 'Products General Information',
                'table' => 'product_general_informations',
                'columns' => 'internal_notes',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            10 =>
            array (
                'id' => 11,
                'name' => 'Products Sales',
                'table' => 'product_sales',
                'columns' => 'description,long_description,channel_pilot_long_description',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            11 =>
            array (
                'id' => 12,
                'name' => 'Product Pricelists',
                'table' => 'product_pricelists',
                'columns' => 'name',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            12 =>
            array (
                'id' => 13,
                'name' => 'Product Attributes',
                'table' => 'product_attributes',
                'columns' => 'attribute_name',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            13 =>
            array (
                'id' => 14,
                'name' => 'Email Templates',
                'table' => 'email_templates',
                'columns' => 'subject',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            14 =>
            array (
                'id' => 15,
                'name' => 'Activity Log Notes',
                'table' => 'activity_log_notes',
                'columns' => 'note',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            15 =>
            array (
                'id' => 16,
                'name' => 'Activity Messages',
                'table' => 'activity_messages',
                'columns' => 'message',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            16 =>
            array (
                'id' => 17,
                'name' => 'Companies',
                'table' => 'companies',
                'columns' => 'street_address,city',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            17 =>
            array (
                'id' => 18,
                'name' => 'Roles',
                'table' => 'roles',
                'columns' => 'name',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            20 =>
            array (
                'id' => 19,
                'name' => 'Permissions',
                'table' => 'permissions',
                'columns' => 'name',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            21 =>
            array (
                'id' => 22,
                'name' => 'CMS Pages',
                'table' => 'cms_pages',
                'columns' => 'title,meta_title,description,meta_description,short_description',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            22 =>
            array (
                'id' => 23,
                'name' => 'Site Settings',
                'table' => 'site_settings',
                'columns' => 'site_description,site_address',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            23 =>
            array (
                'id' => 24,
                'name' => 'Schedule Activities',
                'table' => 'schedule_activities',
                'columns' => 'summary,details,activity_feedback',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            24 =>
            array (
                'id' => 25,
                'name' => 'Eccomerce Categories',
                'table' => 'eccomerce_categories',
                'columns' => 'category_name',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            25 =>
            array (
                'id' => 26,
                'name' => 'Product Categories',
                'table' => 'product_categories',
                'columns' => 'title',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            26 =>
            array (
                'id' => 27,
                'name' => 'FAQs',
                'table' => 'faqs',
                'columns' => 'question,answer',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            27 =>
            array (
                'id' => 28,
                'name' => 'Contact Us Queries',
                'table' => 'contact_us_queries',
                'columns' => 'subject,message',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            28 =>
            array (
                'id' => 29,
                'name' => 'Email Template Labels',
                'table' => 'email_template_labels',
                'columns' => 'value',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            29 =>
            array (
                'id' => 30,
                'name' => 'Reseller Redeemed Pages',
                'table' => 'reseller_redeemed_pages',
                'columns' => 'description,terms_of_use,privacy_policy,imprint',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            30 =>
            array (
                'id' => 31,
                'name' => 'Reseller Redeemed Page Navigation',
                'table' => 'reseller_redeemed_page_navigation',
                'columns' => 'title',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            31 =>
            array (
                'id' => 32,
                'name' => 'Contact Countries',
                'table' => 'contact_countries',
                'columns' => 'vat_label',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            )
        ));
    }
}
