<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPages;
use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\OldKssCompanyLicense;
use App\Models\OldKssSubscription;
use App\Models\ProductPriceList;
use App\Models\Products;
use App\Models\ProductPricelistConfiguration;
use Auth;
use App\Http\Traits\FSecureTrait;
use App\Models\License;
use App\Models\ProductVariation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

class HomePageController extends Controller
{
    use FSecureTrait;
    public function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function KasperskyExchangePage(){
        return view('frontside.home.KasperskyExchangePage');
    }
    public function KasperskyExchangePagePost(Request $request){
        $input = $request->all();
        $check_entry = null;
        $new_price_list_code = null;
        $discount = 0;
        $license_data = [];
        if($input['access_type'] == 0)
        { 
            // License
            $check_entry = OldKssSubscription::where('name',$input['license_key'])->first();
            if(@$check_entry->is_exchanged == 1){
                return redirect()->back()->with(session()->flash('alert-error',__('The License has been exchanged before.')));
            }
        }
        elseif($input['access_type'] == 1)
        {                          
            // Voucher
            $check_entry = OldKssCompanyLicense::where('name',$input['voucher_key'])->first();
            if(@$check_entry->is_exchanged == 1){
                return redirect()->back()->with(session()->flash('alert-error',__('The Voucher has been exchanged before.')));
            }
        }
        // If data is in our database i.e. the customer is internal customer
        if($check_entry != null)
        {
            // Making a Coupon Code for the Customer 
            $customer_price_list = ProductPriceList::where('name','KSS TIMmunity Customers')->first();
            $random = substr(md5(microtime()),rand(0,26),5);
            $new_price_list_code = $customer_price_list->configuration->promotion_code.$random;
            $check_coupon_code = ProductPricelistConfiguration::where('promotion_code',$new_price_list_code)->first();

            while($check_coupon_code){
                $random = substr(md5(microtime()),rand(0,26),5);
                $new_price_list_code = $customer_price_list->configuration->promotion_code.$random;
                $check_coupon_code = ProductPricelistConfiguration::where('promotion_code',$new_price_list_code)->first();
            }

            $new_price_list = new ProductPriceList();
            $new_price_list->name = $customer_price_list->name.'_'.$random;
            $new_price_list->currency_id  = $customer_price_list->currency_id;
            $new_price_list->parent_id  = $customer_price_list->id;
            $new_price_list->created_by  = 1;
            $new_price_list->save();

            $new_price_list_configuration = new ProductPricelistConfiguration;
            $new_price_list_configuration->pricelist_id = $new_price_list->id;
            $new_price_list_configuration->country_group_id = $customer_price_list->configuration->country_group_id;
            $new_price_list_configuration->country_id = $customer_price_list->configuration->country_id;
            $new_price_list_configuration->website = $customer_price_list->configuration->website;
            $new_price_list_configuration->selectable = $customer_price_list->configuration->selectable;
            $new_price_list_configuration->promotion_code = $new_price_list_code;
            $new_price_list_configuration->save();

            $check_entry->is_exchanged = 1;
            $check_entry->coupon = $new_price_list_code;
            $check_entry->customer_name = $input['customer_name'];
            $check_entry->customer_email = $input['customer_email'];
            $check_entry->save();

            $discount = $customer_price_list->rules[0]->percentage_value;
            
            $expiry_date = null;
            // Applying for F-Secure Product License
            if($input['access_type'] == 0)
            { 
                $expiry_date = $check_entry->end_date;
            }
            else if($input['access_type'] == 1)
            {
                $expiry_date = @$check_entry->license->end_date;
            }
            // If the expirey date is mentioned in our database
            if($expiry_date != null)
            {
                $duration_remaining = 0;
                $this_month = Carbon::now()->floorMonth(); 
                $end_month = Carbon::parse($expiry_date)->floorMonth();
                $products = $this->getAvailableProductsForFSecure()['product_obj']->items;
                $fsecureVariationIndex = null;
                $fsecure_type = null;
                
                if($end_month->greaterThan($this_month)){
                    $duration_remaining = $this_month->diffInMonths($end_month);   
                    // Limiting the license duration 
                    if($duration_remaining > 0 && $duration_remaining <= 3 )
                    {
                        $duration_remaining = 3;
                    }
                    else if($duration_remaining > 3 && $duration_remaining <= 6 )
                    {
                        $duration_remaining = 6;
                    }
                    else if($duration_remaining > 6 && $duration_remaining <= 9 )
                    {
                        $duration_remaining = 9;
                    }
                    else if($duration_remaining > 9 )
                    {
                        $duration_remaining = 12;
                    }
                    // If the product is Kaspersky Total Security
                    if($input['associated_product'] == 0)
                    {
                        $fsecure_type = 'F-Secure TOTAL';
                    }
                    // If the product is Kaspersky Internet Security
                    else if($input['associated_product'] == 1)
                    {
                        $fsecure_type = 'F-Secure SAFE';
                    }
                    // Getting the Minimal F-Secure Variant depending on the product selected
                    $fsecureAmount = 1;
                    usort($products, function ($a, $b) {
                        return $a->amount > $b->amount;
                    });
                    foreach($products as $ind => $product)
                    {
                        if($product->productTitle == $fsecure_type)
                        {
                            if($fsecureVariationIndex == null)
                            {
                                $fsecureVariationIndex = $ind;
                            }
                            if($product->amount == 1)
                            {
                                $fsecureVariationIndex = $ind;
                                break;
                            }
                            if($fsecureAmount > $product->amount)
                            {
                                $fsecureVariationIndex = $ind;
                            }
                            $fsecureAmount = $product->amount;
                        }
                    }
                    // F-Secure Product to be used to fetch licencse
                    $f_secure_product = $products[$fsecureVariationIndex];
                    $sku = $f_secure_product->sku;
                    $ean = $f_secure_product->ean;
                    $internal_product_variation = ProductVariation::where('ean',$ean)->first();
                    $customerObj = (object)array('user_id'=>'KSS Customer','name'=>$input['customer_name'],'email'=>$input['customer_email']);
                    $productObj = $internal_product_variation->product;
                    $productObj->variation = $internal_product_variation;
    
                    // Generating the License and saving it
                    $f_secure_license_object = $this->getLicenseFSecure($customerObj, $productObj, $duration_remaining);
                    if(isset($f_secure_license_object->rows[0]->licenseKey)){
    
                        $license = new License();
                        $license->license_key = $f_secure_license_object->rows[0]->licenseKey;
                        $license->product_id = $internal_product_variation->product->id;
                        $license->variation_id = $internal_product_variation->id;
                        $license->status = 1;       // And status should be active == 1
                        $license->is_used = 0;      // Should not be in used
                        $license->expiry_date = \Carbon\Carbon::now()->addMonths($duration_remaining);
                        $license->save();
                        $license_data['license'] = $f_secure_license_object->rows[0]->licenseKey;

                        $license_data['product'] = $fsecure_type;
                        $license_data['download_link'] = $productObj->download_link;
                        $license_data['duration'] = $duration_remaining;
                        if($input['access_type'] == 0)
                        { 
                            $check_entry->product = $productObj->product_name.' '.$internal_product_variation->variation_name;
                            $check_entry->license = $license_data['license'] ;
                            $check_entry->save() ;
                        }
                        else if($input['access_type'] == 1)
                        {
                            $check_entry->license->product = $productObj->product_name.' '.$internal_product_variation->variation_name;
                            $check_entry->license->license = $license_data['license'] ;
                            $check_entry->license->save();
                        }
                    }
                    
                } 


            }
        }
        // if the data isn't in our database
        else
        {
            $new_entry = null;

            $guest_price_list = ProductPriceList::where('name','KSS TIMmunity Guest')->first();
            $random = substr(md5(microtime()),rand(0,26),5);
            $new_price_list_code = $guest_price_list->configuration->promotion_code.$random;
            $check_coupon_code = ProductPricelistConfiguration::where('promotion_code',$new_price_list_code)->first();
            while($check_coupon_code){
                $random = substr(md5(microtime()),rand(0,26),5);
                $new_price_list_code = $guest_price_list->configuration->promotion_code.$random;
                $check_coupon_code = ProductPricelistConfiguration::where('promotion_code',$new_price_list_code)->first();
            }

            $new_price_list = new ProductPriceList;
            $new_price_list->name = $guest_price_list->name.'_'.$random;
            $new_price_list->currency_id  = $guest_price_list->currency_id;
            $new_price_list->parent_id  = $guest_price_list->id;
            // $new_price_list->created_by  = 1;
            
            $new_price_list->save();
            // dd($new_price_list);

            $new_price_list_configuration = new ProductPricelistConfiguration;
            $new_price_list_configuration->pricelist_id = $new_price_list->id;
            $new_price_list_configuration->country_group_id = $guest_price_list->configuration->country_group_id;
            $new_price_list_configuration->country_id = $guest_price_list->configuration->country_id;
            $new_price_list_configuration->website = $guest_price_list->configuration->website;
            $new_price_list_configuration->selectable = $guest_price_list->configuration->selectable;
            $new_price_list_configuration->promotion_code = $new_price_list_code;
            $new_price_list_configuration->save();

            if($input['access_type'] == 0)
            { 
                // License
                $last_subs = OldKssSubscription::orderBy('id','desc')->first();
                $new_entry = new OldKssSubscription;
                $new_entry->id = $last_subs->id+1;
                $new_entry->name = $input['license_key'];
                $new_entry->is_exchanged = 1;
                $new_entry->coupon = $new_price_list_code;
                $new_entry->customer_name = $input['customer_name'];
                $new_entry->is_new = 1;
                $new_entry->save();

            }
            elseif($input['access_type'] == 1)
            {                          
                // Voucher
                $new_entry = new OldKssCompanyLicense;
                $new_entry->name = $input['voucher_key'];
                $new_entry->is_exchanged = 1;
                $new_entry->coupon = $new_price_list_code;
                $new_entry->customer_name = $input['customer_name'];
                $new_entry->customer_email = $input['customer_email'];
                $new_entry->is_new = 1;
                $new_entry->save();
            }
            else
            {
                $new_entry = new OldKssCompanyLicense;
                $new_entry->name = 'No Voucher/License'.Carbon::now()->toString();
                $new_entry->is_exchanged = 1;
                $new_entry->coupon = $new_price_list_code;
                $new_entry->customer_name = $input['customer_name'];
                $new_entry->customer_email = $input['customer_email'];
                $new_entry->is_new = 1;
                $new_entry->save();
            }
            $discount = $guest_price_list->rules[0]->percentage_value;
        }
        if(count($license_data) == 0)
        {
            $order_approved_email = EmailTemplate::where('type','kss_exchange_email')->first();
            $lang = app()->getLocale();
            $order_approved_email = transformEmailTemplateModel($order_approved_email,$lang);
            $content = $order_approved_email['content'];
            $subject = $order_approved_email['subject'];
            $coupon = $new_price_list_code;
            $discount_percentage = $discount.'%';
            $name = $input['customer_name'];
    
            $search = array("{{name}}","{{coupon}}","{{discount_percentage}}","{{app_name}}");
            $replace = array($name,$coupon,$discount_percentage,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
        }
        else
        {
            $order_approved_email = EmailTemplate::where('type','kss_exchange_email_with_license')->first();
            $lang = app()->getLocale();
            $order_approved_email = transformEmailTemplateModel($order_approved_email,$lang);
            $content = $order_approved_email['content'];
            $subject = $order_approved_email['subject'];
            $coupon = $new_price_list_code;
            $discount_percentage = $discount.'%';
            $name = $input['customer_name'];

            $search = array("{{name}}","{{coupon}}","{{discount_percentage}}","{{license_duration}}","{{download_link}}","{{license_key}}","{{app_name}}");
            $replace = array($name,$coupon,$discount_percentage,$license_data['duration'],$license_data['download_link'],$license_data['license'],env('APP_NAME'));
            $content = str_replace($search,$replace,$content);

        }
        dispatch(new \App\Jobs\SendVoucherPaymentGeneratedEmailJob($input['customer_email'],$subject,$content));
        return redirect()->back()->with('submitted',1)->with(session()->flash('alert-success',__('You have successfully qualified for the Kaspersky exchange program. Please check your email for details.')));
    }
    public function index(Request $request){
        $data = [];
        
        // dd(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(App::getLocale(), null, [], true));
        $data['homepage_pages'] = \App\Models\CmsPages::where('is_homepage_listing',1)->where('is_active',1)->get();
        $data['faqs'] = Faq::where('status', 1)->orderBy('display_order','asc')->get();
        if ($request->awc) {
            // setcookie("awc",$request->awc,time()+ 60 * 60 * 24 * 365,"/",
            // "", true, true );
            Cookie::queue("awc",$request->awc, 10);
        }
        return (!empty(Auth::user()) && Auth::user()->email_verified_at == null) ? view('frontside.home.index',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.home.index',$data);
    }

    public function details($slug, Request $request){
        $data = [];
        $data['page_details'] = CmsPages::where('seo_url', $slug)->where('is_active', 1)->first();
        // $data['homepage_pages'] = \App\Models\Cart::where('is_homepage_listing',1)->where('is_active',1)->get();
        if($data['page_details'])
        {
            return (!empty(Auth::user()) && Auth::user()->email_verified_at == null) ? view('frontside.home.details',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.home.details',$data);
        }
        else
        {
            return (!empty(Auth::user()) && Auth::user()->email_verified_at == null) ? view('frontside.home.index')->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.home.index');
        }
    }
    public function transferimmunityPage(){
        return view('frontside.home.transferimmunity');
    }
    public function aikQPage(){
        return view('frontside.home.aikQ');
    }
    public function inboxPage(){
        return view('frontside.home.inbox');
    }
    public function mailiPage(){
        return view('frontside.home.maili');
    }
    public function moveimmunityPage(){
        return view('frontside.home.moveimmunity');
    }
    public function nedlinkPage(){
        return view('frontside.home.nedlink');
    }
    public function overmailPage(){
        return view('frontside.home.overmail');
    }
    public function qrPage(){
        return view('frontside.home.qr');
    }
    public function emailimmunityPage(){
        return view('frontside.home.emailimmunity');
    }
    public function deviceimmunityPage(){
        return view('frontside.home.deviceimmunity');
    }
    public function officeimmunityPage(){
        return view('frontside.home.officeimmunity');
    }
    public function backupimmunityPage(){
        return view('frontside.home.backupimmunity');
    }
    public function productimmunityPage(){
        return view('frontside.home.productimmunity');
    }
    public function vpnimmunityPage(){
        return view('frontside.home.vpnimmunity');
    }


    public function comingsoonPage(){

        return view('frontside.home.comingsoon');
    }
}
