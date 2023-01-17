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

    Route::prefix('/distributor')->name('distributor.')->namespace('Distributor')->group(function () {

        Route::namespace('Auth')->group(function () {

            Route::get('/','LoginController@showLoginForm')->name('login.index');
            Route::post('/login', 'LoginController@login')->name('login.submit');

            Route::match(['get','post'],'/verify-account', 'VerificationController@verifyUser')->name('verify');
            Route::match(['get','post'],'/password', 'VerificationController@getPassword')->name('password');

            Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.update');
        });


        // Route::middleware(['distributor'])->group(function(){


            Route::match(['get','post'],'/profile','DashboardController@distributorProfile')->name('profile');
            Route::get('/dashboard','DashboardController@dashboard')->name('dashboard');
            Route::get('/voucher-orders','VoucherController@voucherOrders')->name('voucher.orders');
            Route::get('/vouchers/{voucher_order_id}','VoucherController@vouchers')->name('voucher.order.vouchers');
            Route::get('/voucher-payment/{voucher_order_id}','VoucherController@vouchers')->name('voucher.payment');
            Route::get('/voucher-export/{voucher_order_id}','VoucherController@vouchers')->name('voucher.order.vouchers.export');
            Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
        // });

    });

?>
