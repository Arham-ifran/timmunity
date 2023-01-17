<?php
namespace App\Http\Traits;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use URL;
use App\Models\ChannelpilotLog;
use Illuminate\Support\Str;
use Carbon\Carbon;

trait FSecureTrait{


    function getAccessTokenFSecure(){
        $curl = curl_init();
        $end_point = env('fsecure_api_endpoint').'/oauth2/token';
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('fsecure_api_endpoint').'/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_POSTFIELDS => 'client_id=TiMmunity&client_secret=4658ec712ab57474400441a383a3764f&grant_type=client_credentials&scope=license_products_read%20license_orders_write%20license_orders_read',
            CURLOPT_POSTFIELDS => 'client_id=TiMmunity&client_secret='.env('fsecure_secret').'&grant_type=client_credentials&scope=license_products_read%20license_orders_write%20license_orders_read',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        $header = 'Content-Type: application/x-www-form-urlencoded';
        // $params = 'client_id=TiMmunity&client_secret=4658ec712ab57474400441a383a3764f&grant_type=client_credentials&scope=license_products_read%20license_orders_write%20license_orders_read';
        $params = 'client_id=TiMmunity&client_secret='.env('fsecure_secret').'&grant_type=client_credentials&scope=license_products_read%20license_orders_write%20license_orders_read';
        $data = array(
            'end_point' => $end_point,
            'request_type' => 'POST',
            'parmas' => $params,
            'header' => $header,
            'response' => $response,
            'response_code' => 404,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );
        if($response){
            $data['response_code'] = 200;
            $this->saveFsecureLogs($data);
            $decoded_result = json_decode($response);
            return $decoded_result->access_token;
        }

    }

    function getAvailableProductsForFSecure(){
        $access_token = $this->getAccessTokenFSecure();
        if($access_token){
            $end_point = env('fsecure_api_endpoint')."/licenses/get_available_products?language=en";
            $authorization = "Authorization: Bearer ".$access_token;
            $curl = curl_init($end_point);
            $header = array(
                'Accept: application/json',
                'Content-Type: application/json',
                $authorization
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            $response = curl_exec($curl);

            curl_close($curl);
            $params = 'null';
            $header = 'Accept: application/json, Content-Type: application/json, Access_TOKEN';
            $data = array(
                'end_point' => $end_point,
                'request_type' => 'GET',
                'parmas' => $params,
                'header' => $header,
                'response' => $response,
                'response_code' => 404,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );

            if($response){
                $data['response_code'] = 200;
                $this->saveFsecureLogs($data);
                $decoded_result = json_decode($response);
                $availableProductsObj = array();
                $availableProductsObj['access_token'] = $access_token;
                $availableProductsObj['product_obj'] = $decoded_result;
                return $availableProductsObj;
            }

        }else{
            echo json_encode("msg: ","Something went wrong");
        }

    }

    function getLicenseFSecure($customerObj, $productObj, $freeMonths = null){
        $fsecureObj = $this->getAvailableProductsForFSecure();
        // $variationIdIndex = array_search($productObj->variation->sku, array_column($fsecureObj['product_obj']->items, 'sku'));
        $variationIdIndex = array_search($productObj->variation->ean, array_column($fsecureObj['product_obj']->items, 'ean'));
        // dd($variationIdIndex);
        $variationId = $fsecureObj['product_obj']->items[$variationIdIndex]->variationId;
        $end_point = env('fsecure_api_endpoint').'/licenses/new_order';

        $params = '{';
            $params .= '"variationId": "'.@$variationId.'",';
            $params .= '"storeId": 6991,';
            $params .= '"language": "en",';
            $params .= '"customerReference": "'.@$customerObj->user_id.'",';
            $params .= '"customerName": "'.@$customerObj->name.'",';
            $params .= '"customerEmail": "'.@$customerObj->email.'"';
            if($freeMonths != null){
                $params .= '"extraFields": [{';
                    $params .= '"groupingId": "freeMonths",';
                    $params .= '"value": "'.$freeMonths.'",';
                    $params .= '"label": "Free Months",';
                $params .= '}]';
            }
        $params .= '}';

        $header = '{"Accept: application/json",
            "Content-Type: application/json",
            "AccessToken}';
        if($fsecureObj && $variationId !== false && $customerObj){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('fsecure_api_endpoint').'/licenses/new_order',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $params,//////,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$fsecureObj['access_token']
                ),
            ));
            $response = curl_exec($curl);
            $data = array(
                'end_point' => $end_point,
                'request_type' => 'POST',
                'parmas' => $params,
                'header' => $header,
                'response' => $response,
                'response_code' => 404,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
            curl_close($curl);
            if($response){
                $data['response_code'] = 200;
                $this->saveFsecureLogs($data);
                $licenseObject = json_decode($response);
                return $licenseObject;
            }
        }
        else
        {
            return 'false';
        }
    }


    function cancelLicenseHelper($product_id, $licenseKey){
        $access_token = $this->getAccessTokenFSecure();
        if($access_token){
            $url = env('fsecure_api_endpoint')."/licenses/suspend_order";
            $authorization = "Authorization: Bearer ".$access_token;
            $params = '{
                "licenseKey": "'.$licenseKey.'",
            }';
            $header = '{"Content-Type: application/json",'.$authorization.'}';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "licenseKey": "'.$licenseKey.'"
                }',
                CURLOPT_HTTPHEADER => array(
                    $authorization,
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            $data = array(
                'end_point' => $url,
                'request_type' => 'POST',
                'parmas' => $params,
                'header' => $header,
                'response' => $response,
                'response_code' => 404,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
            curl_close($curl);
            if($response){
                $data['response_code'] = 200;
                $this->saveFsecureLogs($data);
                $decoded_result = json_decode($response);
                return $decoded_result;
            }
        }else{
            echo json_encode("msg: ","Something went wrong");
        }
    }

    function saveFsecureLogs($data){
        $updateData = \DB::table('fsecure_logs')->insert($data);
        return true;
    }
}


?>
