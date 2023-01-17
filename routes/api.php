    <?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post( '/redeem-voucher','VoucherController@verifyAndRedeemVoucher');

Route::post( '/manufacturers/all', 'ProductController@getManufacturers' );

Route::post( '/products/all', 'ProductController@getAllProducts' );
Route::post( '/products/update', 'ProductController@changeProductExtraPrices' );
Route::post( '/products/variationDetails', 'ProductController@getVariationDetails' );


Route::post( '/orders/placeNewOrder', 'ShopController@placeVoucherOrder' );
Route::post( '/orders/changeStatus', 'VoucherController@changeVoucherOrderStatus' );
Route::post( '/orders/voucher/changeStatus', 'VoucherController@changeOrderVoucherStatus' );
Route::post( '/voucher/redeem', 'VoucherController@redeemVoucher' );
