<?php
namespace App\Http\Traits;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use URL;
use App\Models\ChannelpilotLog;
use Illuminate\Support\Str;

trait ChannelPilotFeedTrait{

    public function channelPilotLogin(){

        $channel_pilot_account_token = env('CHANNELPILOT_MERCHANT_ID');
        $channel_pilot_shop_token    = env('CHANNELPILOT_SHOP_TOKEN');
        $endpoint       = env('channel_pilot_endpoint').'authentication/login';
        $paramters      = [
                "apiToken"  => $channel_pilot_account_token,
                "shopToken" => $channel_pilot_shop_token
            ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_HEADER => true,
            CURLOPT_POSTFIELDS => json_encode($paramters),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
              ),
        ));
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $data = array(
            'end_point' => $endpoint,
            'request_type' => 'POST',
            'parmas' => json_encode($paramters),
            'header' => '',
            'response' => $response,
            'response_code' => $httpcode
        );
        $this->LogTransaction($data);
        $access_token = json_decode($response)->access_token;
        return $access_token;
    }


    public function feedSetup(){

        $access_token   = $this->channelPilotLogin();
        $endpoint       = env('channel_pilot_endpoint').'feeds/setup';
        $header         = array(
            'Authorization: Bearer '.$access_token
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $endpoint,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => $header,
        ));

        $response = json_decode(curl_exec($curl));
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $data = array(
            'end_point' => $endpoint,
            'request_type' => 'GET',
            'parmas' => '',
            'header' => json_encode($header),
            'response' => json_encode($response),
            'response_code' => $httpcode
        );
        $this->LogTransaction($data);



        $data['global_feed_id'] = $response->result->setups[0]->globalFeedId;
        $data['access_token']   = $access_token;

        return $data;
    }

    public function feedDirectives(){
        $access_token   = $this->channelPilotLogin();
        $endpoint       = env('channel_pilot_endpoint').'datafields/setup';
        $header =  array(
            'Authorization: Bearer '.$access_token
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = json_decode(curl_exec($curl));
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $data = array(
            'end_point' => $endpoint,
            'request_type' => 'GET',
            'parmas' => '',
            'header' => json_encode($header),
            'response' => json_encode($response),
            'response_code' => $httpcode
        );
        $this->LogTransaction($data);

        $setups_datafields_array = $response->result->setups;
        return $setups_datafields_array;

    }


    public function uploadDataIntoFeed($feed_array){



        $data = $this->feedSetup();
        $endpoint = env('channel_pilot_endpoint').'feeds/'.$data['global_feed_id'].'/data';
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$data['access_token']
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => json_encode($feed_array),
        ));
        $response = json_decode(curl_exec($curl));
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $data = array(
            'end_point' => $endpoint,
            'request_type' => 'PATCH',
            'parmas' => json_encode($feed_array),
            'header' => json_encode($header),
            'response' => json_encode($response),
            'response_code' => $httpcode
        );
        $this->LogTransaction($data);


        return [
            'response' => $response,
        ];

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  array('end_point','request_type','parmas','header','response','response_code')
     */
    protected function LogTransaction($data)
    {
        ChannelpilotLog::create($data);
    }
}


?>
