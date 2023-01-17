<?php
namespace App\Http\Traits;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use Hashids;

trait DistributorCommunicatioTrait {
    public function changeVoucherOrderStatus($distributor_base_url, $order_id, $status)
    {
        $curl = curl_init();

        $curl_post_fields = (object)array(
            'voucher_order_id' => $order_id,
            'status' => $status
        );
        curl_setopt_array($curl, array(
            CURLOPT_URL => $distributor_base_url."/orders/changeStatus",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($curl_post_fields),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        } else {
            return true;
        }
    }
    public function changeVoucherStatus($distributor_base_url, $voucher_id, $status)
    {
        $voucher = Voucher::where('id',$voucher_id)->first();
        $curl = curl_init();

        $curl_post_fields = (object)array(
            'voucher_order_id' => $voucher->voucherOrder->id,
            'voucher_id' => $voucher->id,
            'status' => $status
        );
        curl_setopt_array($curl, array(
            CURLOPT_URL => $distributor_base_url."/orders/voucher/changeStatus",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($curl_post_fields),
            CURLOPT_HTTPHEADER => array(
                "auth-email: ".env('distributor_email'),
                "auth-key: ".env('distributor_auth_key'),
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        } else {
            return true;
        }
    }
}
