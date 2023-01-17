<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*SETUP ARTISAN*/
Route::get('voucher-payment-invoices-cron','Frontside\VoucherController@VoucherPaymentInvoicesCron');

Route::get('/system-setup', 'CommonController@complete_setup');
Route::get('/licensedelete', 'CommonController@deletelicense');
Route::get('/ck12345','CommonController@check');

Route::get('/migrate', function() {
    $re = Artisan::call('migrate');
	dd($re);
});
Route::get('/clear-cache', function() {
    $re = Artisan::call('optimize:clear');
	dd($re);
});
Route::get('/seeder/{seederclass}', function($seederclass) {
    $re = Artisan::call('db:seed --class='.$seederclass);
	dd($re);
});
Route::get('/queue-listen', function() {
    $re = Artisan::call('queue:listen');
	dd($re);
});
Route::get('/storage-link', function() {
    $re = Artisan::call('storage:link');
    dd($re);
});
Route::get('/LicenseCronJob', function() {
    $re = Artisan::call('schedule:run');
    dd($re);
});
Route::get('/LicenseReseller/{user_id}', function($user_id) {
    $re = Artisan::call('command:VoucherPaymentInvoices '.$user_id);
    dd($re);
});
Route::group(
[
// 'domain' => '{subdomain}.'.config('app.base_domain'),
    'prefix' => LaravelLocalization::setLocale(),
    'domain' => config('app.app_domain'),   // timmunity.com .at .de .org .net
    'middleware' => ['localeCookieRedirect','localeSessionRedirect','localizationRedirect']
],
function() {
    Route::get('switch-currency/{code}', 'Admin\CurrencyController@switchCurrency')->name('switch.currency');
    Route::get('redeem-voucher', 'Frontside\GenericVoucherController@redeemPage')->name('voucher.generic.redeem.page');
    Route::post('redeem-voucher-post', 'Frontside\GenericVoucherController@redeemVoucher')->name('voucher.generic-redeem.post');
    Route::get('order/pay/{transaction_id}', 'Frontside\CheckoutController@paymentPay')->name('paymentPay');

    Route::middleware('track_last_visit')->group(function(){

        /*User Auth Routes*/
        Auth::routes(['verify' => true]);
        Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
        Route::post('/login', 'Auth\LoginController@login')->name('login.submit');
        Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
        Route::post('user/email/resend', 'Auth\VerificationController@resend')->name('user.verification.resend');
        Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
        // // Must verfied before redirect to dashboard
        Route::get('user/email/verify', function () {
            return view('auth.verify');
        })->name('user.verification.notice');

        /** Customer Signup */
        Route::get('/customer/register', 'Auth\RegisterController@showRegistrationForm')->name('customer.signup');
        Route::post('/customer/register', 'Auth\RegisterController@register')->name('customer.register.post');
        /** Reseller Signup */
        Route::get('/reseller/register', 'Auth\RegisterController@showResellerRegistrationForm')->name('reseller.signup');
        Route::post('/reseller/register', 'Auth\RegisterController@registerReseller')->name('reseller.register.post');

        /*User Reset Password Routes */
        Route::get('/verify-account', 'Auth\RegisterController@verifyUser')->name('verify.user');
        Route::post('/verify-account', 'Auth\RegisterController@resetPassword')->name('password.store');

        Route::post('resend-email-invitation', 'Admin\InvitationMailController@resendInvitationEmail')->name('invitation.resend-email');

        /*User Forgot Password Routes*/
        Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

        Route::get('/countries-lat-lng','HomeController@countriesLatLng');

        Route::namespace('Frontside')->group(function(){
            Route::get('/','HomePageController@index')->name('frontside.home.index');

            // KasperSky Routes
            Route::get('/kaspersky-exchange-program','HomePageController@KasperskyExchangePage')->name('frontside.page.KasperskyExchangePage');
            Route::post('/kaspersky-exchange-program-post','HomePageController@KasperskyExchangePagePost')->name('frontside.page.KasperskyExchangePage.post');

            // General and CMS Pages Routes
            Route::get('/transfer-immunity','HomePageController@transferimmunityPage')->name('frontside.page.transferimmunity');
            Route::get('/emailimmunity','HomePageController@emailimmunityPage')->name('frontside.page.emailimmunity');
            Route::get('/deviceimmunity','HomePageController@deviceimmunityPage')->name('frontside.page.deviceimmunity');
            Route::get('/officeimmunity','HomePageController@officeimmunityPage')->name('frontside.page.officeimmunity');
            Route::get('/backupimmunity','HomePageController@backupimmunityPage')->name('frontside.page.backupimmunity');
            Route::get('/productimmunity','HomePageController@productimmunityPage')->name('frontside.page.productimmunity');
            Route::get('/vpnimmunity','HomePageController@vpnimmunityPage')->name('frontside.page.vpnimmunity');
            Route::get('/aikQ','HomePageController@aikQPage')->name('frontside.page.aikQ');
            Route::get('/inbox','HomePageController@inboxPage')->name('frontside.page.inbox');
            Route::get('/maili','HomePageController@mailiPage')->name('frontside.page.maili');
            Route::get('/moveimmunity','HomePageController@moveimmunityPage')->name('frontside.page.moveimmunity');
            Route::get('/nedlink','HomePageController@nedlinkPage')->name('frontside.page.nedlink');
            Route::get('/overmail','HomePageController@overmailPage')->name('frontside.page.overmail');
            Route::get('/qr','HomePageController@qrPage')->name('frontside.page.qr');
            Route::get('/page/{slug}','HomePageController@details')->name('frontside.page.details');
            Route::get('/about','AboutController@index')->name('frontside.about.index');
            Route::get('/comingsoon','HomePageController@comingsoonPage')->name('frontside.comingsoon.index');
            Route::get('/contact','ContactController@index')->name('frontside.contact.index');
            Route::post('/contact-submit','ContactController@submit')->name('frontside.contact.submit');


            Route::get('/quotation/{id}','DashboardController@quotationDetail')->name('user.dashboard.quotations.detail');
            Route::get('/invoice-details/{invoice_id}','DashboardController@invoiceDetail')->name('user.dashboard.invoice.detail');
            Route::get('/shop','ShopController@index')->name('frontside.shop.index')->middleware('reseller_profile_completed');
            Route::post('/shopSearchSuggestions','ShopController@shopSearchSuggestions')->name('frontside.shop.search');

            ////  Logged IN Routes /////

            /** Voucher Redeem Routes */
            Route::get('reseller-redeem-edit/{id}','VoucherController@editRedeemPage')->name('voucher.edit.redeemed')->middleware('reseller_profile_completed');
            Route::get('reseller-redeem-view/{id}','VoucherController@viewRedeemPage')->name('voucher.view.redeemed')->middleware('reseller_profile_completed');
            Route::post('/update-redeem-page', 'VoucherController@updateRedeemPage')->name('update.redeem.page')->middleware('reseller_profile_completed');
            Route::get('/domain-exists-redeem-page', 'VoucherController@domain_exists')->name('redeem.domain.exists')->middleware('reseller_profile_completed');

            // User Account Related Routes
            Route::get('/account','DashboardController@index')->name('user.dashboard')->middleware('auth');
            Route::prefix('/account')->middleware('auth')->name('user.dashboard.')->group(function () {
                Route::get('/sales-orders','DashboardController@salesOrderListing')->name('sales_order');
                Route::get('/sales-order-details','DashboardController@salesOrderDetail')->name('sales_order.detail');
                Route::get('/quotations','DashboardController@quotationOrderListing')->name('quotations');
                Route::get('/invoices','DashboardController@invoiceListing')->name('invoices');

                Route::get('/user-profile','DashboardController@profilePage')->name('profile');
                Route::post('/user-profile','DashboardController@saveProfile')->name('profile.save');
                Route::post('/reseller-profile','DashboardController@resellerSaveProfile')->name('reseller.profile.save');
            });

            // Payment Routes
            Route::get('/payment-success/{quotationid}','CheckoutController@paymentRedirect')->name('frontside.payment.redirect');
            Route::get('/order-success/{quotationid}','CheckoutController@orderSuccess')->name('frontside.order.success');

            // Cart and Checkout Routes
            Route::get('/cart','CartController@cart')->name('frontside.shop.cart');
            Route::prefix('/cart')->name('frontside.shop.cart.')->group(function () {
                Route::post('/add','CartController@addToCart')->name('add');
                Route::post('/remove/{id}','CartController@removeFromCart')->name('remove');
                Route::post('/update-quantity/{id}/{qty}','CartController@updateCartItemQty')->name('update.qty');
                Route::post('/get-total/{id}','CartController@getCartTotal')->name('get.total');
                Route::post('/apply-coupon/{coupon_code}','CartController@applyCoupon')->name('apply.coupon');
            });
            Route::get('/checkout','CheckoutController@checkout')->name('frontside.shop.checkout');
            Route::post('/checkout','CheckoutController@checkoutPost')->name('frontside.shop.checkout.post');

            // Shop Routes
            // Route::get('/shop','ShopController@index')->name('frontside.shop.index');
            Route::get('/product-details/{slug}','ShopController@productDetails')->name('frontside.shop.product-details');
            Route::get('/get_extra_price','ShopController@getExtraPrice')->name('frontside.shop.extra_price');
        });
        Route::prefix('/reseller')->name('frontside.reseller.')->namespace('Reseller')->group(function(){
            Route::group(['middleware' => ['auth:web','reseller_profile_completed','IsReseller']], function ($router){
                Route::get('/dashboard','DashboardController@dashboard')->name('dashboard');
                Route::get('/vouchers/{voucher_order_id}','DashboardController@vouchers')->name('vouchers');
                Route::post('/orderVoucher','DashboardController@orderVoucher')->name('orderVoucher');
                Route::get('/voucher-payments/{voucher_order_id}','DashboardController@voucherPayments')->name('voucher.payments');
                Route::get('/payment-invoices/{reseller_id?}','DashboardController@invoicesReseller')->name('invoices');
                Route::get('/export-order-vouchers/{id}','DashboardController@exportVouchers')->name('voucher.order.vouchers.export');
                Route::get('/voucher-payment/{id}','DashboardController@voucherPayment')->name('voucher.payment');
                Route::get('/voucher-payment/success/{id}','DashboardController@voucherPaymentSuccess')->name('voucher.payment.success');
            });
        });


        Route::get('get-product-variations/{product_id}', 'Admin\ProductsController@getVariations')->name('get-product-variations');
        Route::get('get-product-variations-details/{variation_id}', 'Admin\ProductsController@getVariationsDetails')->name('get-product-variation-detail');
        Route::get('/email/verify', function () {
            return view('auth.verify');
        })->middleware('auth')->name('verification.notice');

    });
});
// Reseller Routes for Redeem Pages
Route::middleware('track_last_visit')->group(function(){
    Route::group(
    [
        'domain' => '{subdomain}.'.env('reseller_domain'),  // mineimmunity.com .at .de .org .net
    ],
    function() {
        Route::get('/', 'Frontside\VoucherController@redeemPage');
        Route::get('/terms-of-use', 'Frontside\VoucherController@termsOfUse')->name('voucher.redeem.page.terms-of-use');
        Route::get('/privacy-policy', 'Frontside\VoucherController@privacyPolicy')->name('voucher.redeem.page.privacy.policy');
        Route::get('/imprint', 'Frontside\VoucherController@imprint')->name('voucher.redeem.page.imprint');

        // Terms Of Use, Privacy Policy and Imprint Routes
        // Route::get('/{title?}/{reseller_id?}/terms-of-use', 'Frontside\VoucherController@termsOfUse')->name('voucher.redeem.page.terms-of-use');
        // Route::get('/{title?}/{reseller_id?}/privacy-policy', 'Frontside\VoucherController@privacyPolicy')->name('voucher.redeem.page.privacy.policy');
        // Route::get('/{title?}/{reseller_id?}/imprint', 'Frontside\VoucherController@imprint')->name('voucher.redeem.page.imprint');
        Route::post('/redeem-voucher', 'Frontside\VoucherController@redeemVoucher')->name('voucher.redeem.post');
        Route::group(
            [
            // 'domain' => '{subdomain}.'.config('app.base_domain'),
                'prefix' => LaravelLocalization::setLocale(),
                'middleware' => ['localeCookieRedirect','localeSessionRedirect','localizationRedirect']
            ],
            function() {
                Route::get('/{title?}/{reseller_id?}', 'Frontside\VoucherController@redeemPage')->name('voucher.redeem.page');
            });

        // End
    });
});
