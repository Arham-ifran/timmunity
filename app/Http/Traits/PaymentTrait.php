<?php
namespace App\Http\Traits;
use App\Models\Student;
use App\Models\Currency;
use Hashids;
use App\Models\PaymentGateway;

trait PaymentTrait {
    public function checkCurrencyAcceptibility($currency)
    {
        if(
            $currency == 'AED' || $currency == 'AUD' || $currency == 'BGN' ||
            $currency == 'BRL' || $currency == 'CAD' || $currency == 'CHF' ||
            $currency == 'CZK' || $currency == 'DKK' || $currency == 'EUR' ||
            $currency == 'GBP' || $currency == 'HKD' || $currency == 'HRK' ||
            $currency == 'HUF' || $currency == 'ILS' || $currency == 'ISK' ||
            $currency == 'JPY' || $currency == 'MXN' || $currency == 'MYR' ||
            $currency == 'NOK' || $currency == 'NZD' || $currency == 'PHP' ||
            $currency == 'PLN' || $currency == 'RON' || $currency == 'RUB' ||
            $currency == 'SEK' || $currency == 'SGD' || $currency == 'THB' ||
            $currency == 'TWD' || $currency == 'USD' || $currency == 'ZAR'
        ) {
                return true;
        }
        return false;

    }
    public function generatePaymentDetails($quotation, $redirectUrl = null) {
        $mollie_gateway = PaymentGateway::where('id',1)->first();
        if($mollie_gateway->status == 1){
            $api_key = $mollie_gateway->mode == 1 ? $mollie_gateway->live_api_key : $mollie_gateway->sandbox_api_key;
            if($redirectUrl == null)
            {
                $redirectUrl = route('frontside.payment.redirect',Hashids::encode($quotation->id));
            }
            $default_currency = Currency::where('is_default', 1)->first();
            $default_currency = $default_currency ? $default_currency->code : "EUR";

            $currency_use = $this->checkCurrencyAcceptibility($quotation->currency) ? $quotation->currency : $default_currency;
            // $quotation_total = str_replace(",","",$quotation->total);
            // $quotation_total = floatval($quotation_total);
            // $quotation_total = $quotation->total_currency + ($quotation->total_currency * $quotation->vat_percentage / 100) ;
            $quotation_total = $quotation->total_currency  ;
            // dd($quotation_total, $quotation->exchange_rate);
            $value = $this->checkCurrencyAcceptibility($quotation->currency) ? currency_format($quotation_total,'','',1) : $quotation_total;
            // Payment Process  Start
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($api_key);
                $payment = $mollie->payments->create([
                    "amount" => [
                        "currency" => $currency_use,
                        "value" => $value
                    ],
                    "description" => "TIMmunity Order # ".$quotation->id,
                    "redirectUrl" => $redirectUrl
                ]);
                return [
                    'success'=> true,
                    'payment'=> $payment
                ];
                return  $payment;
            } catch (\Exception  $th) {
                return [
                    'success'=> false,
                    'message'=> $th->getMessage()
                    // 'message'=> __('Something went wrong. Try again later')
                ];
            }
        }else{
            return [
                'success'=> false,
                'message'=> __('Payment method not active')
            ];
        }
    }
    public function generateVoucherPaymentDetails($voucher_payment, $redirectUrl = null) {
        $mollie_gateway = PaymentGateway::where('id',1)->first();
        if($mollie_gateway->status == 1){
            $api_key = $mollie_gateway->mode == 1 ? $mollie_gateway->live_api_key : $mollie_gateway->sandbox_api_key;
            if ($redirectUrl == null) {
                $redirectUrl = route('frontside.reseller.voucher.payment.success', Hashids::encode($voucher_payment->id));
            }
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                // $mollie->setApiKey(env('MOLLIE_API_KEY'));
                $mollie->setApiKey($api_key);
                $payment = $mollie->payments->create([
                    "amount" => [
                        "currency" => $voucher_payment->currency,
                        "value" => currency_format($voucher_payment->total_payable*$voucher_payment->exchange_rate,'','',1)
                    ],
                    "description" => "TIMmunity Voucher ".$voucher_payment->code,
                    "redirectUrl" => $redirectUrl,
                ]);
                return [
                    'success'=> true,
                    'payment'=> $payment
                ];

            }catch (\Exception  $th) {
                return [
                    'success'=> false,
                    'message'=> $th->getMessage()
                    // 'message'=> __('Something went wrong. Try again later')
                ];
            }
        }else{
            return [
                'success'=> false,
                'message'=> __('Payment method not active')
            ];
        }
    }
    public function getPaymentStatus($transaction_id)
    {
        $mollie_gateway = PaymentGateway::where('id',1)->first();
        $api_key = $mollie_gateway->mode == 1 ? $mollie_gateway->live_api_key : $mollie_gateway->sandbox_api_key;
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($api_key);
        $payment = $mollie->payments->get($transaction_id);

        return $payment->isPaid();
    }
    public function getMolliePaymentDetail($transaction_id)
    {
        $mollie_gateway = PaymentGateway::where('id',1)->first();
        if($mollie_gateway->status == 1){
            $api_key = $mollie_gateway->mode == 1 ? $mollie_gateway->live_api_key : $mollie_gateway->sandbox_api_key;
            // Payment Process  Start
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($api_key);
                $payment = $mollie->payments->get($transaction_id);
                // dd($payment);
                return [
                    'success'=> true,
                    'payment'=> $payment
                ];
            } catch (\Exception  $th) {
                return [
                    'success'=> false,
                    'message'=> $th->getMessage(),
                    'payment'=> '$payment1'
                ];
            }
        }else{
            return [
                'success'=> false,
                'message'=> __('Payment method not active'),
                'payment'=> '$payment12'
            ];
        }
    }
    public function cancelPaymentLink($transaction_id)
    {
        $mollie_gateway = PaymentGateway::where('id',1)->first();
        if($mollie_gateway->status == 1){
            $api_key = $mollie_gateway->mode == 1 ? $mollie_gateway->live_api_key : $mollie_gateway->sandbox_api_key;
            // Payment Process  Start
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($api_key);
                $canceled_payment = $mollie->payments->delete($transaction_id);
                return [
                    'success'=> true,
                    'payment'=> $canceled_payment
                ];
                return  $payment;
            } catch (\Exception  $th) {
                return [
                    'success'=> false,
                    'message'=> $th->getMessage()
                ];
            }
        }else{
            return [
                'success'=> false,
                'message'=> __('Payment method not active')
            ];
        }
    }
}
