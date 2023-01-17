<?php
use Carbon\Carbon;
use App\Models\User;
use App\Models\Contact;
use App\Models\EmailTemplate;
use App\Models\Quotation;
use App\Models\FsecureLog;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\QuotationOrderLineVoucher;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Hash;
use App\Models\ResellerPackage;
use App\Models\ResellerPackageRule;

function _asset($path, $secure = null)
{
    return asset(trim($path, '/'), $secure); //. '?var=' . config('constants.ASSET_VERSION');
}

function settingValue($key)
{

    $setting = \DB::table('site_settings')->select($key)->first();
    if ($setting)
        return $setting->$key;
    else
        return '';
}

function makeToPopover($string = '',$limit = 100){
    $string =  strip_tags($string);
    return (strlen($string) > $limit)? substr($string,0,$limit).'...' : $string;
}

function checkImage($path = '', $placeholder = '')
{

    if (empty($placeholder)) {
        $placeholder = 'avatar5.png';
    }

    if (!empty($path)) {
        $url = explode('storage', $path);
        $url = public_path() . '/storage' . $url[1];
        $isFile = explode('.', $url);
        if (file_exists($url) && count($isFile) > 1)
            return $path;
        else
            return asset('backend/dist/img/' . $placeholder);
    } else {
        return asset('backend/dist/img/' . $placeholder);
    }
}

function currency_format($amount = 0, $symbol = '€', $code = "EUR", $only_price = 0 )
{
    // $separator = '.';
    // $precision = 2;

    // $numberParts = explode($separator, $amount);
    // $numberParts[1] = isset($numberParts[1]) ? $numberParts[1] : '00';
    // if( strlen($numberParts[1]) < $precision ) {
    //     $zeros_to_be_added = $precision - strlen($numberParts[1]);
    //     $zeros = str_repeat('0', $zeros_to_be_added);
    //     $numberParts[1] = $numberParts[1].$zeros;
    // }

    // $response = $numberParts[0];
    // if (count($numberParts) > 1 && $precision > 0) {
    //     $response .= $separator;
    //     $response .= substr($numberParts[1], 0, $precision);
    // }
    $response = number_format($amount,2,'.','');
    return $only_price == 1 ? $response : $symbol.' '.$response.' '.$code; //  [currency symbol] [price value] [currency abbreviation]
}
function rand_str() {
    $characters = '0123456789-=+{}[]:;@#~.?/&gt;,&lt;|\!"£$%^&amp;*()abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomstr = '';
    for ($i = 0; $i < 8; $i++) {
      $randomstr .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomstr;
}

function generate_kss() {
    return sprintf( '%05x-%05x-%05x-%05x',
        mt_rand( 0, 0xfffff ), mt_rand( 0, 0xfffff ),
        mt_rand( 0, 0xfffff ),
        mt_rand( 0, 0x0C2ff ) | 0x40000,
        mt_rand( 0, 0x3ffff) | 0x80000,
    );
}

function translation($item_id,$language_module_id,$lang,$column_name,$org_value)
{
    $record = \App\Models\LanguageTranslation::where(['item_id' => $item_id, 'language_module_id' => $language_module_id, 'language_code' => $lang, 'column_name' => $column_name])->first();
    if(!empty($record))
        return $record->item_value;
    else
        return $org_value;
}

function translationByDeepL($text, $target_lang, $destination_lang = 'en')
{
    if($target_lang == 'br')
    {
        $target_lang = 'pt-BR';
    }

    $params = array(
        'auth_key' => 'd554170c-80ad-7185-f19c-b776394eb975',
        'target_lang' => $target_lang,
        'source_lang' => $destination_lang
    );
    $data = array(
        'auth_key' => 'd554170c-80ad-7185-f19c-b776394eb975',
        'text' => $text,
        'target_lang' => $target_lang,
        'source_lang' => $destination_lang
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.deepl.com/v2/translate?" . http_build_query($params),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_CUSTOMREQUEST => "POST",
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $responseArr = json_decode($response, true);
    if($responseArr == null){
        return $text;
    }

    if(array_key_exists('translations', $responseArr))
    {
        return $responseArr['translations'][0]['text'];
    }
    else
    {

        return $text;
    }
}
function transformEmailTemplateModel($model,$lang)
{
    $subject = translation($model->id,14,$lang,'subject',$model->subject);
    $content = $model->content;

    $search = [];
    $replace = [];
    $ids = [];
    $labels = $model->emailTemplateLabels;

    foreach($labels as $object)
    {
            $search[$object->id] = '{{'.$object->label.'}}';
            $replace[$object->id] = $object->value;
            $ids[] = $object->id;
        }

        if($lang != 'en')
        {
            $translations = \App\Models\LanguageTranslation::where(['language_module_id' => 29, 'language_code' => $lang, 'column_name' => 'value'])->whereIn('item_id',$ids)->get();

            foreach($translations as $translation)
            {
                $replace[$translation->item_id] = $translation->item_value;
            }
        }

    $content = str_replace($search,$replace,$content);
    return [
    'id' => $model->id,
    'subject' => $subject,
    'content' => $content,
    'lang' => $lang,
    'type' => $model->type,
    'info' => $model->info,
    'status' => $model->status
    ];
}

function access_denied()
{
    abort(403, 'You have no right to perform this action.');
}

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;

    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

function check_registration_for_guest_and_register($data){
    $user_id = null;
    $contact_id = null;
    $user = null;
    $check_registered_contact = Contact::where('email', $data['email'])->where('type','!=', 4)->first() ;
    if($check_registered_contact)
    {
        return [
            "error" => true,
            'user_id' => $check_registered_contact->user->id
        ];
    }
    $check_guest_contact = Contact::where('email', $data['email'])->where('type', 4)->first();
    if($check_guest_contact){
        $contact_id = $check_guest_contact->id;
        $user_id = $check_guest_contact->user->id;
        $user = $check_guest_contact->user;
        if($data['new_account'] != null){
            $check_guest_contact->name = $data['firstname'];
            $check_guest_contact->street_1 = isset($data['address']) ? $data['address'] : '';
            $check_guest_contact->state_id = isset($data['state']) ? $data['state'] : '';
            $check_guest_contact->zipcode = isset($data['zip']) ? $data['zip'] : '';
            $check_guest_contact->country_id = isset($data['country_id']) ? $data['country_id'] : null;
            $check_guest_contact->type = 2;
            $check_guest_contact->save();
            $check_guest_contact->user->password = Hash::make($data['password']);
            $check_guest_contact->user->save();
            $check_guest_contact->user->invitation_code = sha1(time());
        }
    }else{
        $user = new User;
            $user->name = $data['firstname'];
            $user->email = $data['email'];
            if($data['new_account'] != null){
                $user->password =  Hash::make($data['password']);
                $user->invitation_code = sha1(time());
            }
            $user->account_status = 1;
            $user->is_active = 1;
            $user->is_approved = 1;
        $user->save();
        $contact = new Contact;
            $contact->user_id = $user->id;
            $contact->name = $data['firstname'];
            $contact->email = $data['email'];
            $contact->street_1 = isset($data['address']) ? $data['address'] : '';
            $contact->state_id = isset($data['state']) ? $data['state'] : '';
            $contact->zipcode = isset($data['zip']) ? $data['zip'] : '';
            $contact->country_id = isset($data['country_id']) ? $data['country_id'] : null;
            $contact->status = 1;
            $contact->type = $data['new_account'] != null ? 2 : 4;
        $contact->save();


        $contact_id = $contact->id;
        $user_id = $user->id;
    }
    if($data['new_account'] != null){
        // $user = User::where('id', $user_id)->first();
        //     $user->name = $data['firstname'];
        //     $user->email = $data['email'];
        //     $user->account_status = 1;
        //     $user->password =  Hash::make($data['password']);
        //     $user->is_active = 1;
        //     $user->is_approved = 1;
        //     $user->invitation_code = sha1(time());
        // $user->save();
        Auth::login($user);

        $name = $user->name;
        $email = $user->email;
        $link = route('verify.user', ['code' => $user->invitation_code]);
        $email_template = \App\Models\EmailTemplate::where('type','customer_sign_up_confirmation')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{link}}","{{app_name}}");
        $replace = array($name,$link,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));
    }
    return array(
        "error" => false,
        "user_id"=> $user_id,
        "contact_id"=> $contact_id,
        "user"=> $user
    );
}

function channelpilot_analyticsRawData($date){
    $curl = curl_init();

    curl_setopt_array($curl, [
    CURLOPT_URL => "http://analytics.api.channelpilot.com/analyticsRawData?day=".$date,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => "",
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "cp-api-token: ".env('CHANNELPILOT_MERCHANT_ID'),
        "cp-shop-token: ".env('CHANNELPILOT_SHOP_TOKEN')
    ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return [
            'success' => false,
            'message' => $err
        ];
    } else {
        return [
            'success' => true,
            'message' => $response
        ];
    }
}

function checkCurrencyAcceptibility($currency)
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

function createAccountOnSecondaryPlatforms($secondary_platforms, $user, $reseller,$package_duration_in_months)
{
    $password = rand_str();


    if(in_array("NED", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'primaryEmail' => $user->email,
            'password' => $password,
            'username' => $user->email,
            'firstName' => $user->name,
            'lastName' => '',
            'subscribeToMinPricePlan' => true,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('ned_link_url').'/accounts/member/register',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            ),
        ));

        $response_ned = curl_exec($curl);
        curl_close($curl);

        $response_ned_arr = json_decode($response_ned,true);


    }
    if(in_array("TRF", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' =>$password,
            // 'timezone' => $user->timezone,
            'timezone' => '',
            'country_id' => $user->country_id,
            'subscribeToMinPricePlan' => true,
            'platform' => 13,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('transfer_immunity_url').'/api/auth/register',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);



    }
    if(in_array("QRC", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
            // 'timezone' => $user->timezone,
            'timezone' => null,
            'country_id' => $user->country_id,
            'subscribeToMinPricePlan' => true,
            'platform' => 13,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('qr_code_url').'/api/auth/register',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);


    }
    if(in_array("AKQ", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'action' => 'signup',
            'email_local' => $user->email,
            'pass1' => $password,
            'pass2' => $password,
            // 'timezone' => $user->timezone,
            'timezone' => null,
            'country_id' => $user->country_id,
            'firstname' => $user->name,
            'surname' => '',
            'subscribeToMinPricePlan' => true,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('aikq_url').'/api/index.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);



    }
    if(in_array("INB", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'action' => 'signup',
            'email_local' => $user->email,
            'pass1' => $password,
            'pass2' => $password,
            // 'timezone' => $user->timezone,
            'timezone' => null,
            'country_id' => $user->country_id,
            'firstname' => $user->name,
            'surname' => '',
            'subscribeToMinPricePlan' => true,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('inbox_de_url').'/api/index.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);


    }
    if(in_array("OVM", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'action' => 'signup',
            'email_local' => $user->email,
            'pass1' => $password,
            'pass2' => $password,
            // 'timezone' => $user->timezone,
            'timezone' => null,
            'country_id' => $user->country_id,
            'firstname' => $user->name,
            'surname' => '',
            'subscribeToMinPricePlan' => true,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('overmail_url').'/api/index.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);



    }
    if(in_array("MAI", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'action' => 'signup',
            'email_local' => $user->email,
            'pass1' => $password,
            'pass2' => $password,
            // 'timezone' => $user->timezone,
            'timezone' => null,
            'country_id' => $user->country_id,
            'firstname' => $user->name,
            'surname' => '',
            'subscribeToMinPricePlan' => true,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('maili_de_url').'/api/index.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);

    }
    if(in_array("MOV", $secondary_platforms))
    {
        $data = array(
            'name' => $user->name,
            'package_duration_in_months' => $package_duration_in_months,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
            'timezone' => null,
            'country_id' => $user->country_id,
            'subscribeToMinPricePlan' => true,
            'platform' => 13,
            'voucher' => $user->voucher,
            'reseller' => $reseller,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => env('move_immunity_url').'/api/auth/register',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);

    }
    if(in_array("EMK", $secondary_platforms))
    {
        $data = array(
            'package_duration_in_months' => $package_duration_in_months,
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
            'timezone' => null,
            'country_id' => $user->country_id,
            'subscribeToMinPricePlan' => true,
            'platform' => 13
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('email_marketing_url').'/api/auth/voucher-register',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_arr = json_decode($response,true);
    }
    $replacements = array(
        'NED' => 'NED Link',
        'TRF' => 'Transfer Immunity',
        'QRC' => 'QR Code',
        'AKQ' => 'AikQ',
        'OVM' => 'Over Mail',
        'INB' => 'Inbox',
        'MAI' => 'Maili',
        'MOV' => 'Move Immunity',
        'EMK' => 'Email marketing'
    );
    foreach ($secondary_platforms as $key => $value) {
        // dd(isset($replacements[$value]));
        if (isset($replacements[$value])) {
            $secondary_platforms[$key] = $replacements[$value];
        }
    }
    $secondary_platforms = implode(', ',$secondary_platforms);

    $email_template = EmailTemplate::where('type','lite_accounts_created_on_other_platforms')->first();
    $lang = app()->getLocale();
    $email_template = transformEmailTemplateModel($email_template,$lang);
    $content = $email_template['content'];
    $subject = $email_template['subject'];
    $search = array("{{secondary_projects}}","{{name}}","{{user_name}}","{{password}}","{{app_name}}");
    $replace = array($secondary_platforms,$user->name,$user->email,$password,env('APP_NAME'));
    $content = str_replace($search,$replace,$content);
    try {
        dispatch(new \App\Jobs\SendVoucherRedeemEmailJob($user->email,$subject,$content));
    } catch (\Throwable $th) {
        //throw $th;
    }
    return 1;
    // return 'true';
}

function generateVouchers($quotation_id){
    $quotation = Quotation::where('id', $quotation_id)->first();
    $voucher_list = '<ul>';
    foreach($quotation->order_lines as $order_line)
    {
        $order_quantity = $order_line->qty;
        $ordered_quantity = QuotationOrderLineVoucher::where('quotation_order_line_id', $order_line->id)->where('quotation_id',$quotation->id)->count();
        $order_quantity = $order_quantity - $ordered_quantity;
        for($i = 0; $i < $order_quantity ; $i++)
        {
            $voucher_code = uniqid(mt_rand());
            $voucher = new QuotationOrderLineVoucher;
                $voucher->voucher_code = $order_line->product->prefix.$voucher_code;
                $voucher->quotation_order_line_id = $order_line->id;
                $voucher->quotation_id = $quotation->id;
                $voucher->redeemed = 0;
                $voucher->status = 1;
            $voucher->save();
            $voucher_list .= '<li>'.$voucher->voucher_code.'</li>';
        }
    }
    $voucher_list .= '</ul>';

    $name = $quotation->customer->name;
    $email = $quotation->customer->email;
    $link = route('voucher.generic.redeem.page');
    $order_number = "S".str_pad($quotation->id, 5, '0', STR_PAD_LEFT);
    $email_template = EmailTemplate::where('type','order_vouchers_email')->first();
    $lang = app()->getLocale();
    $email_template = transformEmailTemplateModel($email_template,$lang);
    $content = $email_template['content'];
    $subject = $email_template['subject'];
    $search = array("{{name}}","{{order_number}}","{{voucher_list}}","{{app_name}}","{{link}}");
    $replace = array($name,$order_number,$voucher_list,env('APP_NAME'),$link);
    $content = str_replace($search,$replace,$content);
    $file = generateVoucherSheetExcel($quotation->id);
    dispatch(new \App\Jobs\SendLicenseEmailJob($email,$subject,$content,$file));
}

function generateVoucherSheetExcel($quotation_id)
{
    $quotation = Quotation::where('id', $quotation_id)->first();
    $order_lines = $quotation->order_lines;

    $voucher_array[] = array();
    $main_platform = 'TIMmunity';
    $voucher_array[] = [
        'Order Number',
        "S".str_pad($quotation->id, 5, '0', STR_PAD_LEFT)
    ];
    $voucher_array[] = ['#', 'Product' , 'Voucher Code'];
    $index = 1;
    foreach ($order_lines as $order_line) {
        $product_name = $order_line->product->product_name;
        $product_name .= $order_line->variation != null ? $order_line->variation->variation_name : '';
        foreach($order_line->vouchers as $voucher){
            $voucher_array[] = [
                '#' => $index,
                'Product' => $product_name,
                'Voucher Code' => $voucher->voucher_code
            ];
            $index++;
        }
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    for ($i = 0; $i < count($voucher_array); $i++) {
        //set value for indi cell
        $row = $voucher_array[$i];
        //writing cell index start at 1 not 0
        $j = 1;
        foreach ($row as $x => $x_value) {
            $sheet->setCellValueByColumnAndRow($j, $i + 1, $x_value);
            $j = $j + 1;
        }
    }
    $old_file = public_path().'/storage/quotations/vouchers/S'.str_pad($quotation->id, 5, '0', STR_PAD_LEFT).'_vouchers.xlsx';
    if (file_exists($old_file)) {
        unlink($old_file);
    }
    ob_clean();
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $writer->save(public_path().'/storage/quotations/vouchers/S'.str_pad($quotation->id, 5, '0', STR_PAD_LEFT).'_vouchers.xlsx');
     return public_path('storage/quotations/vouchers/S'.str_pad($quotation->id, 5, '0', STR_PAD_LEFT).'_vouchers.xlsx');
}

function resellerProductPrice($reseller_id, $product_id)
{
    $reseller_contact = Contact::where('id', $reseller_id)->first();
    $reseller_package = $reseller_contact->reseller_package;
    $reseller_package_rules = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->rules : [];
    
    $default_percentage = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->percentage : 0;
    $default_model = $reseller_package  && $reseller_package->is_active == 1 ? $reseller_package->model : null;
    
    $product = Products::with(['variations' => function ($query) {
        $query->where('is_active', 1);
    }])->where('id', $product_id)->first();
    
    $product_price = $product->reseller_price_without_vat['total_price_exclusive_vat'];
    
    $end_product_price = 0;
    $variations = ProductVariation::where('product_id', $product_id)->where('is_active', 1)->get();
    if(count($variations) > 0)
    {  
        $variation_sales_prices = [];
        $variation_reseller_prices = [];
        
        $prices = [];
        $already_applied_on_product = 0;
        
        foreach( $variations as $ind => $variation )
        {
            $price = $variation->reseller_sales_price;
            $price = $price ? $price : $variation->variation_sales_price;
            $price = $price ? $price : $product_price;
            
            $variation_sales_price = $variation->variation_sales_price != 0 && $variation->variation_sales_price != null ? $variation->variation_sales_price : $product_price;
            $reseller_sales_price = $variation->reseller_sales_price != 0 && $variation->reseller_sales_price != null ? $variation->reseller_sales_price : $product_price;
            $allow_default_price = 1; 
            foreach($reseller_package_rules as $rule)
            {
                if ( $rule->apply_on == 2 )    // Apply on Specific Product Variation
                {
                    if( $rule->variation_id == $variation->id )  // If variation matches
                    {
                        $variation_sales_price = checkAndAlterPrice($rule, $variation_sales_price);
                        $reseller_sales_price = checkAndAlterPrice($rule, $reseller_sales_price);
                        $price = checkAndAlterPrice($rule, $price);
                        $allow_default_price = 0;
                    }
                }
                elseif ( $rule->apply_on == 1 ) // Apply on Specific Product
                {
                    $allow_default_price = 0;
                    if($already_applied_on_product == 0){
                        if( $rule->product_id == $product_id )  // If product matches
                        {
                            $variation_sales_price = checkAndAlterPrice($rule, $variation_sales_price);
                            $reseller_sales_price = checkAndAlterPrice($rule, $reseller_sales_price);
                            $price = checkAndAlterPrice($rule, $price);
                            $allow_default_price = 0;
                        }

                    }
                }
                elseif ( $rule->apply_on == 0 ) // Apply on All  Products
                {
                    $variation_sales_price = checkAndAlterPrice($rule, $variation_sales_price);
                    $reseller_sales_price = checkAndAlterPrice($rule, $reseller_sales_price);
                    $price = checkAndAlterPrice($rule, $price);
                    $allow_default_price = 0;
                    
                }
            }  
            if( count($reseller_package_rules) > 0 && $allow_default_price == 1 )
            {
                switch( $default_model )
                {
                    case 0: //increment
                        $variation_sales_price += $variation_sales_price * $default_percentage / 100;
                        $reseller_sales_price += $reseller_sales_price * $default_percentage / 100;
                        $price += $price * $default_percentage / 100;
                        break;
                    case 1: //decrement
                        $variation_sales_price -= $variation_sales_price * $default_percentage / 100;
                        $reseller_sales_price -= $reseller_sales_price * $default_percentage / 100;
                        $price -= $price * $default_percentage / 100;
                        break;
                }
            }
            array_push($variation_sales_prices,$variation_sales_price);
            array_push($variation_reseller_prices,$reseller_sales_price);
            array_push($prices,$price);
        }
        // $mergerd_prices = array_merge($variation_sales_prices,$variation_reseller_prices);
        // $product_price = min($mergerd_prices);
        // $end_product_price = max($mergerd_prices);
        $product_price = min($prices);
        $end_product_price = max($prices);

    }
    else
    {
        $allow_default_price = 1;
        foreach($reseller_package_rules as $rule)
        {
            if ( $rule->apply_on == 0 )    // Apply on All Products
            {
                $new_price = checkAndAlterPrice($rule, $product_price);
                if($new_price != $product_price){
                    $allow_default_price = 0;
                    $product_price = $new_price;
                }
            }
            elseif ( $rule->apply_on == 1 ) // Specific Product
            {
                if( $rule->product_id == $product_id )  // If product matches
                {
                    $new_price = checkAndAlterPrice($rule, $product_price);
                    if($new_price != $product_price){
                        $allow_default_price = 0;
                        $product_price = $new_price;
                    }
                }
            }
        }
        if( $allow_default_price == 1 && $reseller_package)
        {
            switch( $reseller_package->model )
            {
                case 0: //increment
                    $product_price += $product_price * $reseller_package->percentage / 100;
                    break;
                case 1: //decrement
                    $product_price -= $product_price * $reseller_package->percentage / 100;
                    break;
            }
        }
    }
    return ["product_price"=>$product_price, "end_product_price"=>$end_product_price];
    
}

function checkAndAlterPrice($rule, $price)
{
    if( $rule->use_default == 0 )  // If not apply default
    {
        switch( $rule->model )
        {
            case 0: //increment
                $price += $price * $rule->percentage / 100;
                break;
            case 1: //decrement
                $price -= $price * $rule->percentage / 100;
                break;
        }
    }
    return $price;
}

function resellerProductVariationPrice($reseller_id , $variation_id)
{
    $reseller_contact = Contact::where('id', $reseller_id)->first();
    $reseller_package = $reseller_contact->reseller_package;
    $reseller_package_rules = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->rules : [];
    
    $default_percentage = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->percentage : 0;
    $default_model = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->model : null;
    
    $variation = ProductVariation::where('id', $variation_id)->first();
    $product = $variation->product;
    
    $product_price = $product->reseller_price_without_vat['total_price_exclusive_vat'];
    
    
    if($variation->reseller_sales_price != '' && $variation->reseller_sales_price > 0){
        $product_price = $variation->reseller_sales_price;
    }elseif($variation->variation_sales_price != '' && $variation->variation_sales_price > 0){
        $product_price = $variation->variation_sales_price;
    }
    
    $allow_default_price = 1; 

    foreach($reseller_package_rules as $rule)
    {
        if ( $rule->apply_on == 2 )    // Apply on Specific Product Variation
        {
            if( $rule->variation_id == $variation->id )  // If variation matches
            {
                $product_price = checkAndAlterPrice($rule, $product_price);
                $allow_default_price = 0;
            }
        }
        elseif ( $rule->apply_on == 1 ) // Apply on Specific Product
        {
            $allow_default_price = 0;
            
            if( $rule->product_id == $product->id )  // If product matches
            {
                $product_price = checkAndAlterPrice($rule, $product_price);
                $allow_default_price = 0;
            }

        }
        elseif ( $rule->apply_on == 0 ) // Apply on All  Products
        {
            $product_price = checkAndAlterPrice($rule, $product_price);
            $allow_default_price = 0;
            
        }
    }  
    if( count($reseller_package_rules) > 0 && $allow_default_price == 1 )
    {
        switch( $default_model )
        {
            case 0: //increment
                $product_price += $product_price * $default_percentage / 100;
                break;
            case 1: //decrement
                $product_price -= $product_price * $default_percentage / 100;
                break;
        }
    }
    // $product_price = $variation_sales_price < $reseller_sales_price ? $variation_sales_price : $reseller_sales_price;
    return $product_price;
}

function resellerOrderPrice($reseller_id, $product_id, $variation_id = null)
{
    $reseller_contact = Contact::where('id', $reseller_id)->first();
    $reseller_package = $reseller_contact->reseller_package;
    $reseller_package_rules = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->rules : [];
    
    $default_percentage = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->percentage : 0;
    $default_model = $reseller_package && $reseller_package->is_active == 1 ? $reseller_package->model : null;
    
    $product = Products::where('id', $product_id)->first();

    $product_price = $product->reseller_price_without_vat['total_price_exclusive_vat'];
    
    if($variation_id != null){
        
        $variation = ProductVariation::where('id', $variation_id)->first();
        
        // $variation_sales_price = $variation->variation_sales_price != 0 && $variation->variation_sales_price != null ? $variation->variation_sales_price : $product_price;
        // $reseller_sales_price = $variation->reseller_sales_price != 0 && $variation->reseller_sales_price != null ? $variation->reseller_sales_price : $product_price;
        
        // $product_price = $variation_sales_price < $reseller_sales_price ? $variation_sales_price : $reseller_sales_price;
        if($variation->reseller_sales_price != '' && $variation->reseller_sales_price > 0){
            $product_price = $variation->reseller_sales_price;
        }elseif($variation->variation_sales_price != '' && $variation->variation_sales_price > 0){
            $product_price = $variation->variation_sales_price;
        }
    }
    
    $allow_default_price = 1; 
    foreach($reseller_package_rules as $rule)
    {
        if ( $rule->apply_on == 2 )    // Apply on Specific Product Variation
        {
            if( $rule->variation_id == $variation->id )  // If variation matches
            {
                $product_price = checkAndAlterPrice($rule, $product_price);
                $allow_default_price = 0;
            }
        }
        elseif ( $rule->apply_on == 1 ) // Apply on Specific Product
        {
            $allow_default_price = 0;
            
            if( $rule->product_id == $product->id )  // If product matches
            {
                $product_price = checkAndAlterPrice($rule, $product_price);
                $allow_default_price = 0;
            }

        }
        elseif ( $rule->apply_on == 0 ) // Apply on All  Products
        {
            $product_price = checkAndAlterPrice($rule, $product_price);
                $allow_default_price = 0;
            
        }
    }  
    if( count($reseller_package_rules) > 0 && $allow_default_price == 1 )
    {
        switch( $default_model )
        {
            case 0: //increment
                $product_price += $product_price * $default_percentage / 100;
                break;
            case 1: //decrement
                $product_price -= $product_price * $default_percentage / 100;
                break;
        }
    }
    
    return $product_price;
}

