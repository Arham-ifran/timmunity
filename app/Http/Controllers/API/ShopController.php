<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\ResellerRedeemedPage;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\VoucherOrder;

class ShopController extends BaseController
{
    /**
     * {
     *     "product_id"
     *     "variation_id"
     *     "phone"
     *     "address"
     *     "country_id"
     *     "city"
     *     "quantity"
     *     "unit_price"
     *     "disount_percentage"
     *     "vat_percentage"
     *     "currency_symbol"
     *     "currency_code"
     *     "exchange_rate"
     *     "message"
     * }
     */
    public function placeVoucherOrder(Request $request)
    {
        $distributor = $this->verifyRequest($request);
        if($distributor['success'] == 'false'){
            $distributor['success'] = false;
            return $distributor;
        }
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
            'address' => 'required',
            'country_id' => 'required',
            'city' => 'required',
            'quantity' => 'required',
            'unit_price' => 'required',
            'vat_percentage' => 'required',
            'currency_symbol' => 'required',
            'currency_code' => 'required',
            'exchange_rate' => 'required'
        ]);

        if ($validator->fails()) {
            return array(
                'success' => false,
                // 'message' => __('Missing parameters')
                'message' => $validator->message()
            );
        }

        $input = $request->all();

        $product = Products::where('id', $input['product_id'])->first();
        $unit_price = $product->generalInformation->sales_price;
        if(isset($input['variation_id']))
        {
            $variation = ProductVariation::where('id', $input['variation_id'] )->first();
            if($variation->reseller_sales_price != null)
            {
                $unit_price = $variation->reseller_sales_price;
            }
            else
            {
                if($variation->variation_sales_price == null){
                    $extra_price = $variation->extra_price;
                    $unit_price = $product->generalInformation->sales_price + $extra_price;
                }else{
                    $unit_price = $variation->variation_sales_price;
                }
            }
        }
        $unit_price -= $unit_price * $product->generalInformation->voucher_discount_percentage / 100;

        $voucher_order = new VoucherOrder();
            $voucher_order->product_id = $input['product_id'];
            $voucher_order->variation_id = (isset($input['variation_id'])) ? $input['variation_id'] : null;
            $voucher_order->reseller_id = 0;
            $voucher_order->distributor_id = $distributor->id;
            $voucher_order->phone = $input['phone'];
            $voucher_order->street_address = $input['address'];
            $voucher_order->city = $input['city'];
            $voucher_order->country_id = $input['country_id'];
            $voucher_order->quantity = $input['quantity'];
            $voucher_order->remaining_quantity = $input['quantity'];
            $voucher_order->unit_price = $unit_price;
            $voucher_order->saas_discount_percentage = $product->generalInformation->saas_discount_percentage;
            $voucher_order->discount_percentage = $product->generalInformation->voucher_discount_percentage == null ? 0 : $product->generalInformation->voucher_discount_percentage ;
            $voucher_order->used_quantity = 0;
            $voucher_order->total_amount = 0;
            $voucher_order->vat_percentage = $input['vat_percentage'];
            $voucher_order->message = $input['message'] ? $input['message'] : 'No Message Posted';
            $voucher_order->status = 1;
            $voucher_order->is_active = 1;
            $voucher_order->currency_symbol = $input['currency_symbol'];
            $voucher_order->currency = $input['currency_code'];
            $voucher_order->exchange_rate = $input['exchange_rate'];
        $voucher_order->save();
        $voucher_order->total_amount = $voucher_order->total_payable;
        $voucher_order->save();

        for($i = 0; $i < $input['quantity'] ; $i++)
        {
            $voucher_code = uniqid(mt_rand());
            $voucher = new Voucher;
            $voucher->status = 1;
            $voucher->order_id = $voucher_order->id;
            $voucher->code = $product ? $product->prefix.$voucher_code : $voucher_code;
            $voucher->save();
        }
        $vouchers = Voucher::where('order_id',$voucher_order->id)->pluck('code','id')->toArray();
        $redeem_link = '';
        if($product->project_id != null){
            // dd($product->project->prefix);
            if("TRF" == $product->project->prefix){
                $redeem_link = env('transfer_immunity_url');
            }
            if("QRC" == $product->project->prefix){
                $redeem_link = env('qr_code_url');
            }
            if("AKQ" == $product->project->prefix){
                $redeem_link = env('aikq_url');
            }
            if("INB" == $product->project->prefix){
                $redeem_link = env('inbox_de_url');
            }
            if("OVM" == $product->project->prefix){
                $redeem_link = env('overmail_url');
            }
            if("MAI" == $product->project->prefix){
                $redeem_link = env('maili_de_url');
            }
            if("MOV" == $product->project->prefix){
                $redeem_link = env('move_immunity_url');
            }
            if("NED" == $product->project->prefix){
                $redeem_link = env('ned_link_url');
            }
            if("EMK" == $product->project->prefix){
                $redeem_link = env('email_marketing_url');
            }
        }
        $data['voucher_order_data'] = array(
            'reference_order_id' => $voucher_order->id,
            'vouchers' => $vouchers,
            'redeem_link' => $redeem_link,
        );

        return array(
            'success' => true,
            'message' => __('Order placed.'),
            'data' => $data
        );
    }

}
