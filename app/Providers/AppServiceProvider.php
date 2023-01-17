<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Contact;
use App\Models\Products;
use App\Models\Quotation;
use App\Models\SalesTeam;
use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer([
            'admin.website.lawfulinterception.resellervoucherorderpayment',
            'admin.website.lawfulinterception.resellervoucher',
            'admin.website.lawfulinterception.resellerorder',
            'admin.website.lawfulinterception.resellerpdf',
            'admin.website.lawfulinterception.customerpdf',
            'admin.website.lawfulinterception.customerorder',
            'admin.website.lawfulinterception.customerinvoices',
            'admin.website.lawfulinterception.customercarts',
            'admin.sales.pdf.invoice',
            'admin.license.low-licenses-attachment',
            'frontside.reseller.pdf.invoice',
            'frontside.layouts.partials.header',
            'frontside.layouts.partials.footer',
            'frontside.layouts.app',
            'admin.sales.invoices.action-btns'
            ], function ($view) {
            $site_settings = \App\Models\SiteSettings::all();
            $view->with('site_settings',$site_settings);
        });
        view()->composer(['frontside.layouts.partials.header'], function ($view) {
            $currencies = \App\Models\Currency::where('is_active', 1)->get();
            $view->with('currencies',$currencies);
        });
        view()->composer([
            'frontside.shop.shop-products',
            'frontside.shop.index',
            'frontside.shop.cart',
            'frontside.shop.checkout',
            'frontside.shop.product_details',
            'frontside.shop.partials.shop-products',
            'frontside.dashboard.invoice_detail',
            'frontside.dashboard.sales_order_detail',
            'frontside.layouts.partials.header',
            'frontside.reseller.dashboard',
            'admin.sales.quotation.modal-box.send-email',
            'admin.sales.quotation.modal-box.send-proforma-email',
            'admin.website.lawfulinterception.customerorder',
            'admin.website.lawfulinterception.customerinvoices',
            'admin.voucher.orders',
        ], function ($view) {
            $default_currency = \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first();
            $default_vat_percentage = \App\Models\SiteSettings::first()->defualt_vat;
            $view->with('default_currency',$default_currency)->with('default_vat_percentage', $default_vat_percentage);
        });

        view()->composer(['frontside.layouts.header'], function ($view) {
            $homepage_pages = \App\Models\CmsPages::where('is_homepage_listing',1)->where('is_active',1)->get();
            $view->with('homepage_pages',$homepage_pages);
        });
        view()->composer(['admin.sales.quotation.modal-box.send-email','admin.sales.quotation.modal-box.send-proforma-email'], function ($view) {
            $email_templates = \App\Models\EmailTemplate::all();
            $view->with('email_templates',$email_templates);
        });
        view()->composer([
                'admin.sales.products.product_form',
                'admin.sales.quotation.quotation_form',
                'admin.sections.navbar',
                'admin.sales.quotation.action-btns'], function ($view) {
            $sales_settings = \App\Models\SalesSettings::pluck('variable_value','variable_name')->toArray();
            $view->with('sales_settings',$sales_settings);
        });
        Contact::deleting(function ($contact) {
            $contact->schedule_activities()->delete();
            $contact->activity_attachments()->delete();
            $contact->activity_log_notes()->delete();
            $contact->activity_messages()->delete();
            $contact->followers()->delete();
            $contact->user()->delete();
            $contact->contact_addresses()->delete();
        });
        Products::deleting(function ($product) {
            $product->schedule_activities()->delete();
            $product->activity_attachments()->delete();
            $product->activity_log_notes()->delete();
            $product->activity_messages()->delete();
        });
        Quotation::deleting(function ($quotation) {
            $quotation->schedule_activities()->delete();
            $quotation->activity_attachments()->delete();
            $quotation->activity_log_notes()->delete();
            $quotation->activity_messages()->delete();
        });
        SalesTeam::deleting(function ($sales_team) {
            $sales_team->schedule_activities()->delete();
            $sales_team->activity_attachments()->delete();
            $sales_team->activity_log_notes()->delete();
            $sales_team->activity_messages()->delete();
        });
        User::deleting(function ($user) {
            $user->contact()->delete();
        });
        Admin::deleting(function ($admin) {
            $admin->contacts()->delete();
            $admin->team_members()->delete();
            $admin->team_leads()->delete();
        });
        Customer::deleting(function ($contact) {
            $contact->addresses()->delete();
        });

        Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');
        $locale = "en";
        if(isset($_SERVER["REMOTE_ADDR"])){
            $ip_info = ip_info();
            if(isset($ip_info['country'])){
                if ($ip_info['country'] == "Germany") {
                    $locale = 'de';
                } else if ($ip_info['country'] == "France") {
                    $locale = 'fr';
                } else if ($ip_info['country'] == "Spain") {
                    $locale = 'es';
                } else if ($ip_info['country'] == "Brazil") {
                    $locale = 'br';
                } else if ($ip_info['country'] == "Portugal") {
                    $locale = 'pt';
                } else if ($ip_info['country'] == "Italy") {
                    $locale = 'it';
                } else if ($ip_info['country'] == "Netherlands") {
                    $locale = 'nl';
                } else if ($ip_info['country'] == "Poland") {
                    $locale = 'pl';
                } else if ($ip_info['country'] == "Russia" || $ip_info['country'] == "Russian Federation") {
                    $locale = 'ru';
                } else if ($ip_info['country'] == "Japan") {
                    $locale = 'ja';
                } else if ($ip_info['country'] == "China") {
                    $locale = 'zh';
                } else if ($ip_info['country'] == "Bulgaria") {
                    $locale = 'bg';
                } else if ($ip_info['country'] == "Czechia" || $ip_info['country'] == "Czech Republic") {
                    $locale = 'cs';
                } else if ($ip_info['country'] == "Denmark") {
                    $locale = 'da';
                } else if ($ip_info['country'] == "Estonia") {
                    $locale = 'et';
                } else if ($ip_info['country'] == "Finland") {
                    $locale = 'fi';
                } else if ($ip_info['country'] == "Greece") {
                    $locale = 'el';
                } else if ($ip_info['country'] == "Hungary") {
                    $locale = 'hu';
                } else if ($ip_info['country'] == "Latvia") {
                    $locale = 'lv';
                } else if ($ip_info['country'] == "Lithuania") {
                    $locale = 'lt';
                } else if ($ip_info['country'] == "Romania") {
                    $locale = 'ro';
                } else if ($ip_info['country'] == "Slovakia") {
                    $locale = 'sk';
                } else if ($ip_info['country'] == "Slovenia") {
                    $locale = 'sl';
                } else if ($ip_info['country'] == "Sweden") {
                    $locale = 'sv';
                }
            }   
        }
        // dd($locale);
        App::setLocale($locale);
        session()->put('locale', $locale);
    }
}
