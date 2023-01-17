<?php

use Illuminate\Support\Facades\Route;
    /*
    |--------------------------------------------------------------------------
    | Manufacturer Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    Route::prefix('/manufacturer')->name('manufacturers.')->namespace('Manufacturers')->group(function () {

        Route::namespace('Auth')->group(function () {

            Route::get('/','LoginController@showLoginForm')->name('login.index');
            Route::post('/login', 'LoginController@login')->name('login.submit');
            // Route::post('/login','LoginController@showLoginManufacturer')->name('login');
            Route::match(['get','post'],'/verify-account', 'VerificationController@verifyUser')->name('verify');
            Route::match(['get','post'],'/password', 'VerificationController@getPassword')->name('password');

            Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.update');
        });

        Route::middleware(['manufacture'])->group(function(){
            Route::match(['get','post'],'/profile','DashboardController@manufacturerProfile')->name('profile');
            Route::get('/dashboard','DashboardController@dashboard')->name('dashboard');
            // Route::get('sales-analysis','SalesController@salesAnalysis')->name('sales-analysis');
            Route::get('analysis','SalesController@salesAnalysis')->name('product.analysis');
            Route::get('voucher-orders','SalesController@voucherOrdersOnManufacturerProduct')->name('voucher.orders.manufacturer.product');

            Route::get('manufacturer-graph','SalesController@graphManufacturerData')->name('graph');


            Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
        });

    });

?>
