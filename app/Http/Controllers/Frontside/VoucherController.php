<?php

namespace App\Http\Controllers\Frontside;

use Intervention\Image\ImageManagerStatic as Image;
use App\Models\ResellerRedeemedPageNavigation;
use App\Http\Traits\AdminNotificationTrait;
use App\Http\Controllers\Controller;
use App\Models\ResellerRedeemedPage;
use App\Http\Traits\FSecureTrait;
use App\Models\VoucherPayment;
use App\Models\ContactCountry;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Models\VoucherOrder;
use App\Models\SiteSettings;
use App\Models\Voucher;
use App\Models\License;
use App\Models\Project;
use App\Models\User;
use App\Models\VoucherPaymentOrderDetail;
use Hashids;
use Auth;
use Carbon\Carbon;



class VoucherController extends Controller
{
    use FSecureTrait;
    use AdminNotificationTrait;
    public function redeemPage($domain = 'www', $title = null, $reseller_id = null)
    {
        $data = [];
        $data['countries'] = ContactCountry::all();

        $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('domain', 'https://'.$domain.'.'.env('reseller_domain'))->first();
        if( !$data['reseller'] ){
            try {
                $reseller_id = Hashids::decode($reseller_id)[0];
            } catch (\Throwable $th) {
                return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
            }
            $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('reseller_id', $reseller_id)->first();
        }
        if( !$data['reseller'] )
        {
            return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
        }
        $content = $data['reseller']->description;
        $content = translation( $data['reseller']->id,31,app()->getLocale(),'description',$content);
        $voucher_form = view('frontside.reseller.voucher-redeem-form',$data);
        $search = array("{{voucher_form}}");
        $replace = array($voucher_form);
        $data['content'] = str_replace($search,$replace,$content);

        $r_title = strtolower($data['reseller']->title);
        return view('frontside.reseller.redeem-page', $data);
    }

    public function redeemVoucher(Request $request)
    {
        $input = $request->all();
        $voucher = Voucher::wherehas('voucherOrder', function($query) use($input){
            $query->where('reseller_id', Hashids::decode($input['reseller_id'])[0]);
            $query->where('status',1);
            $query->where('is_active',1);
        })->where('code', $input['voucher_code'])->orWhere('status', 0)->where('status',1)->first();
        if(!$voucher || $voucher->status == 2)
        {
            return redirect()->back()->with(session()->flash('alert-error',__('The voucher is not active. Contact the Administrator.')));
        }
        elseif($voucher->redeemed_at != null)
        {
            return redirect()->back()->with(session()->flash('alert-warning',__('The voucher has been redeemed before.')));
        }
        if($voucher->voucherOrder->product->product_type == 1)  //If the product is not license based
        {
            return redirect()->back()->with(session()->flash('alert-warning',__('Voucher is not valid on this platform. Connect with reseller to confirm.')));
        }

        $user = null;
        if(Auth::user())
        {
            $user = Auth::user();
        }
        else
        {
            $registration_data['email'] = $request->email;
            $registration_data['firstname'] = $request->name;
            $registration_data['country_id'] = $request->country_id;
            $registration_data['new_account'] = $request->new_account;
            $registration_data['password'] = $request->password;

            $check_registration_for_guest_and_register = check_registration_for_guest_and_register($registration_data);
            if($check_registration_for_guest_and_register['error'] == true){
                return redirect()->back()->with(session()->flash('alert-warning', __('Email ID is already registered. Login to continue')));
            }
            $user = User::where('id',$check_registration_for_guest_and_register['user_id'])->first();
        }
        if($voucher){
            $customerObj = $user;
            $customerObj->user_id = $user->id;
            $productObj = $voucher->voucherOrder->product;
            $productObj->variation = $voucher->voucherOrder->variation;
            if($voucher->voucherOrder->product->product_type == 2){
                // it's yes, then API call for create order and get license and save it into license table.
                $f_secure_license_object = $this->getLicenseFSecure($customerObj,$productObj);
                if(isset($f_secure_license_object->rows[0]->licenseKey)){
                    $duration_months = 0;
                    foreach($productObj->variation->variation_details as $variation_detail)
                    {
                        if(strpos( $variation_detail->attached_attribute->attribute_name, 'month') !== false)
                        {
                            $duration_months = $variation_detail->attribute_value;
                        }
                    }
                    $license = new License;
                    $license->license_key = $f_secure_license_object->rows[0]->licenseKey;
                    $license->product_id = $voucher->voucherOrder->product->id;
                    $license->variation_id = @$voucher->voucherOrder->variation->id;
                    $license->status = 1;       // And status should be active == 1
                    $license->is_used = 0;      // Should not be in used
                    $license->expiry_date = Carbon::now()->addMonths($duration_months);
                    $license->save();
               }
               else
               {
                   return redirect()->back()->with(session()->flash('alert-warning',__('License cannot be fetched. Contact Admin')));
               }
            }
        }

        $license_attached = $this->attachLicenses($voucher->id);
        if(isset($license_attached['success']))
        {
            if($license_attached['success'] == 'true'){
                $voucher->redeemed_at = Carbon::now();
                $voucher->customer_id = $user->id;
                $voucher->status = 0;
                $voucher->save();

                $voucher_order = VoucherOrder::where('id', $voucher->order_id)->first();
                $voucher_order->used_quantity = $voucher_order->used_quantity == null ? 1 : $voucher_order->used_quantity+1;
                $voucher_order->remaining_quantity = $voucher_order->remaining_quantity -1;
                $voucher_order->save();

                $name = Auth::user() ? Auth::user()->name : $check_registration_for_guest_and_register['user']->name;
                $email = Auth::user() ? Auth::user()->email : $check_registration_for_guest_and_register['user']->email;
                $voucher_code = $input['voucher_code'];
                $product_name = $voucher_order->product->product_name.' '.@$voucher_order->variation->variation_name;
                $license_code = $license_attached['license']->license_key;

                $email_template = EmailTemplate::where('type','voucher_redeemed_email')->first();
                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);
                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $search = array("{{name}}","{{voucher_code}}","{{product_name}}","{{license_code}}","{{app_name}}");
                $replace = array($name,$voucher_code,$product_name,$license_code,env('APP_NAME'));
                $content = str_replace($search,$replace,$content);

                dispatch(new \App\Jobs\SendVoucherRedeemEmailJob($email,$subject,$content));

                $secondary_platforms = array_filter(explode(',',$voucher->voucherOrder->product->secondary_project_ids));
                if(count($secondary_platforms) > 0){
                    foreach ($secondary_platforms as $key => $value) {
                        $secondary_platforms[$key] = Project::where('id',$value)->first()->prefix;
                    }
                    $user = (object)array();
                    $user->voucher = $input['voucher_code'];
                    if(Auth::user()){
                        $user->name = Auth::user()->name;
                        $user->email = Auth::user()->email;
                        $user->country_id = Auth::user()->contact->country_id;
                    }
                    else
                    {
                        $user->name = $input['name'];
                        $user->email = $input['email'];
                        $user->country_id = $input['country_id'];
                    }
                    $reseller = $voucher->voucherOrder->reseller->name.' ('.$voucher->voucherOrder->reseller->email.')';
                    $duration_months = 1;
                    // dd($voucher->order_line);
                    if($voucher->voucherOrder->variation != null){
                        foreach($voucher->voucherOrder->variation->variation_details as $variation_detail)
                        {
                            if(strpos( $variation_detail->attached_attribute->attribute_name, 'month') !== false)
                            {
                                $duration_months = $variation_detail->attribute_value;
                            }
                            elseif(strpos( $variation_detail->attached_attribute->attribute_name, 'year') !== false)
                            {
                                $duration_months = $variation_detail->attribute_value * 12;
                            }
                        }
                    }
                    createAccountOnSecondaryPlatforms($secondary_platforms, $user, $reseller,$duration_months);
                }
                return redirect()->back()->with(session()->flash('alert-success', __('Voucher redeemed successfully. Check your email for details')));
            }
        }
        $body = "One of the customer tried to redeem a voucher but the licenses were out of stock. Kindly purchase upload more vouchers to avoid further issues.";
        $body .= "<br> <strong>".$voucher->voucherOrder->product->product_name.' '.@$voucher->voucherOrder->variation->variation_name."</strong> ";
        $body .= "<br> Kindly Purchase new licenses to avoid inconvinience. ";
        $this->requestAdmintoUploadMoreVouchers($voucher->voucherOrder->product->id, @$voucher->voucherOrder->variation->id, $body);
        return redirect()->back()->with(session()->flash('alert-error', __('Voucher cannot be redeemed. No License Currently Available')));

    }
    // Edit Redeem Page
    public function editRedeemPage($id)
    {
        $data = [];
        $data['id'] = $id;
        $id = Hashids::decode($id)[0];
        $data['reseller_id'] = $id;


        $data['model'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('reseller_id',$id)->first();
        return view('frontside.reseller.form')->with($data);
    }
    public function viewRedeemPage($id)
    {
        $data = [];
        $data['id'] = $id;
        $id = Hashids::decode($id)[0];
        $data['reseller_id'] = $id;


         $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('reseller_id', $data['reseller_id'])->first();
         $data['countries'] = ContactCountry::all();
         $content = $data['reseller']->description;
         $voucher_form = view('frontside.reseller.voucher-redeem-form',$data);
         $search = array("{{voucher_form}}");
         $replace = array($voucher_form);
         $content = $data['reseller']->description;
         $content = translation( $data['reseller']->id,31,app()->getLocale(),'description',$content);
         $voucher_form = view('frontside.reseller.voucher-redeem-form',$data);
         $search = array("{{voucher_form}}");
         $replace = array($voucher_form);
         $data['content'] = str_replace($search,$replace,$content);
        //  $data['content'] = str_replace($search,$replace,$content);

         return view('frontside.reseller.redeem-page', $data);
    }
    // Update Redeemed Page
    public function updateRedeemPage(Request $request)
    {
        $input = [];
        $input = $request->all();

        $voucher_form = str_contains($input['description'],'{{voucher_form}}');
        if($voucher_form == 1){
            $this->validate($request,
            [
                'title'=>"required|string|max:100",
            ]);

            $id = Hashids::decode($input['reseller_id'])[0];
            $input['reseller_id'] = $id;

            if($input['id'] != null) {
                // Exception case for domain presense
                if($request->domain != ''){
                    $input['domain'] = $input['domain'].'.'.env('reseller_domain');
                    $check_domain_exist = ResellerRedeemedPage::where('domain',$request->domain.'.'.env('reseller_domain'))
                                                            ->where('reseller_id','!=',$id )->first();
                    if(!empty($check_domain_exist)){
                        return redirect()->back()->with(session()->flash('alert-error','Domain already exist.Please use another domain!'));
                    }
                    $input['domain'] = 'https://'.$input['domain'];
                }
                $pattern = '/{{.+}}/i';
                $replacement = '{{voucher_form}}';
                // Translations for Description, Imprint, Privacy Policy and Terms of Use
                $input['description'] = $input['language_used'] == 'en' ? $input['description'] : preg_replace($pattern, $replacement, translationByDeepL($input['description'], 'en', $input['language_used']));
                $input['terms_of_use'] = $input['language_used'] == 'en' ? $input['terms_of_use'] : translationByDeepL($input['terms_of_use'], 'en', $input['language_used']);
                $input['privacy_policy'] = $input['language_used'] == 'en' ? $input['privacy_policy'] : translationByDeepL($input['privacy_policy'], 'en', $input['language_used']);
                $input['imprint'] = $input['language_used'] == 'en' ? $input['imprint'] : translationByDeepL($input['imprint'], 'en', $input['language_used']);

                // Redeem Page Model
                $model = ResellerRedeemedPage::where('reseller_id',$id)->first();
                $model->is_reseller_changed = 1;
                $column_array = array();
                if( $model->description != $input['description'] ){
                    $column_array[] = 'description';
                }
                if( $model->terms_of_use != $input['terms_of_use'] ){
                    $column_array[] = 'terms_of_use';
                }
                if( $model->privacy_policy != $input['privacy_policy'] ){
                    $column_array[] = 'privacy_policy';
                }
                if( $model->imprint != $input['imprint'] ){
                    $column_array[] = 'imprint';
                }
                $model->update($input);
                if( count($column_array) > 0 )
                {
                    dispatch(new \App\Jobs\TranslateRedeemPageDataJob($model->id, $column_array));
                }
                if($request->domain != '' && $model->domain != $request->domain){
                    $reseller_domain = $input['domain'];
                    
                    // check if the domain is pointed to the server
                    $dns_lookup_response = dns_get_record(str_replace('https://','',str_replace('http://','',$reseller_domain)), DNS_A);
                    if( array_search(env('server_ip'), array_column($dns_lookup_response, 'ip')) !== false )
                    {
                        $model->is_domain_verified = 1;
                        $model->url = $reseller_domain;
                        $model->save();

                    }
                } else{
                    $reseller_url = "https://www.".env('reseller_domain').'/'.strtolower(str_replace(' ','-',$input['title'])).'/'.Hashids::encode($id);
                    $model->url = $reseller_url;
                    $model->is_domain_verified = 0;
                    $model->save();
                }

                if ($request->image) {
                    $file   = $request->image;
                    $file_name = $file->getClientOriginalName();
                    $type = $file->getClientOriginalExtension();
                    $file_temp_name = 'redeem-' . time() . '.' . $type;
                    $path = public_path('storage/uploads/redeem-page') . '/' . $file_temp_name;
                    $img = Image::make($file)->save($path);
                    $model->logo = $file_temp_name;
                    $model->save();
                }
                // Saving the reseller redeem page navigation items
                if( getType($request->nav_title) == 'array')
                {
                    if(!empty(array_filter($request->nav_title))){
                        ResellerRedeemedPageNavigation::where('reseller_redeem_page_id',$model->id)->delete();
                        foreach($request->nav_title as  $ind => $title){
                            $nav_title = $title;
                            $reseller_redeemed_navigation = new ResellerRedeemedPageNavigation;
                            $reseller_redeemed_navigation->reseller_redeem_page_id = $model->id;
                            $reseller_redeemed_navigation->title  = $request->nav_title[$ind];
                            $reseller_redeemed_navigation->url  = $request->nav_url[$ind];
                            $reseller_redeemed_navigation->save();
                        }
                    }
                }
                else
                {
                    ResellerRedeemedPageNavigation::where('reseller_redeem_page_id',$model->id)->delete();
                }

            }
            return redirect()->route('voucher.view.redeemed', Hashids::encode(@$model->reseller_id))->with(session()->flash('alert-success', __('Reseller Redeemed Page Updated successfully!')));

        }else{

            return redirect()->back()->with(session()->flash('alert-error','Please enter {{voucher_form}} in description!'))->withInput($request->input());
        }


    }

    static function VoucherPaymentInvoicesCron()
    {

        $pending_payment_orders = VoucherOrder::whereHas('vouchers', function($query){
            $query->where('is_invoiced',0);
            $query->where(function($q){
                    $q->where('redeemed_at','!=', null);
                    $q->whereDate('redeemed_at', '<=', Carbon::now());
                });
        })->orderBy('reseller_id','desc')->get();
        $reseller_data = array();
        $payload = [];
        $currency = null;
        $currency_symbol = null;
        $exchange_rate = 1;

        if(count($pending_payment_orders) > 0){


            foreach($pending_payment_orders as $voucher_order)
            {
                if( $currency == null )
                {
                    $currency = $voucher_order->currency;
                    $currency_symbol = $voucher_order->currency_symbol;
                    $exchange_rate = $voucher_order->exchange_rate;
                }
                else if($currency != false)
                {
                    $currency = $voucher_order->currency == $currency ? $voucher_order->currency : false;
                    $currency_symbol = $voucher_order->currency_symbol == $currency_symbol ? $voucher_order->currency_symbol : false;
                }

                if( !isset( $reseller_data[$voucher_order->reseller_id] ) )
                {
                    $reseller_data[$voucher_order->reseller_id] = [];
                }

                $payload = [];
                $payload['voucherOrder'] = $voucher_order;
                $payload['voucherOrderID'] = $voucher_order->id;
                $payload['voucherOrderProductID'] = $voucher_order->product_id;
                $payload['voucherOrderVariationID'] = $voucher_order->variation_id;

                $payload['voucherIDs'] = Voucher::where('order_id', $voucher_order->id)->where(function($query){
                    $query->where('redeemed_at','!=', null);
                    $query->whereDate('redeemed_at', '<=', Carbon::now());
                })->where('is_invoiced',0)->pluck('id')->toArray();
                array_push($reseller_data[$voucher_order->reseller_id], $payload);
                // Voucher::whereIn('id', $payload['voucherIDs'])->update(['is_invoiced' => 1]);
            }
            foreach($reseller_data as $reseller_id => $data)
            {
                $voucher_payment = new VoucherPayment;
                $voucher_payment->is_paid = 0;
                $voucher_payment->payload = json_encode($payload);
                $voucher_payment->save();
                foreach($data as $d)
                {
                    $voucher_payment_order_detail = new VoucherPaymentOrderDetail;
                    $voucher_payment_order_detail->voucher_payment_id = $voucher_payment->id;
                    $voucher_payment_order_detail->voucher_order_id = $d['voucherOrderID'];
                    $voucher_payment_order_detail->voucher_ids = implode(',',$d['voucherIDs']);
                    $voucher_payment_order_detail->reseller_id = $reseller_id;
                    $voucher_payment_order_detail->save();
                }
                $voucher_payment->currency_symbol = $currency == false ? '€' : $currency_symbol ;
                $voucher_payment->currency = $currency == false ? 'EUR' : $currency;
                $voucher_payment->exchange_rate = $currency == false ? 1 : $exchange_rate;
                $voucher_payment->payload = json_encode($data);
                $voucher_payment->total_amount = number_format($voucher_payment->total_payable * $exchange_rate,2);
                $voucher_payment->save();

                $payment_relif_days = SiteSettings::first()->payment_relief_days;
                $name = $d['voucherOrder']->reseller->name;
                $email = $d['voucherOrder']->reseller->email;
                $link = route("frontside.reseller.voucher.payment", Hashids::encode($voucher_payment->id));
                $email_template = EmailTemplate::where('type','vouchers_payment_generated')->first();
                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);
                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $search = array("{{name}}","{{order_id}}","{{link}}","{{app_name}}","{{no_of_days}}","{{contact_link}}");
                $replace = array($name,'$order_id',$link,env('APP_NAME'),$payment_relif_days,route('frontside.contact.index'));
                $content = str_replace($search,$replace,$content);
                $details['excel_url'] = $voucher_payment->invoice_pdf;
                dispatch(new \App\Jobs\SendVoucherOrderEmailJob($email,$subject,$content,$details));
            }

        }
        // return true;
    }

    public function attachLicenses($voucher_id){
        $voucher = Voucher::where('id', $voucher_id)->first();
        // Licenses Count for the item added
        $licenses_count = License::where('product_id', $voucher->voucherOrder->product_id)
        ->where('variation_id',$voucher->voucherOrder->variation_id)
        ->where('status',1)
        ->where('quotation_order_line_id',null)
        ->where('voucher_id',null)
        ->where('is_used',0)
        ->count();
        // If available license count is is greater than 1
        if( $licenses_count > 0 )
        {
            $license = License::where('product_id', $voucher->voucherOrder->product_id)
                ->where('variation_id',$voucher->voucherOrder->variation_id)
                ->where('status',1)
                ->where('quotation_order_line_id',null)
                ->where('voucher_id',null)
                ->where('is_used',0)
                ->inRandomOrder()
                ->first();

            $license->voucher_id = $voucher_id;
            $license->is_used = 1;
            $license->save();
            return array(
                'success' => 'true',
                'license' => $license,
            );
        }
        return 'false';
    }

    public function termsOfUse($domain = 'www', $title = null, $reseller_id = null){

        $data = [];
        // try {
        //     //code...
        //     $reseller_id = Hashids::decode($reseller_id)[0];
        // } catch (\Throwable $th) {
        //     return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
        // }
        $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('domain', 'https://'.$domain.'.'.env('reseller_domain'))->first();
        if( !$data['reseller'] ){
            try {
                $reseller_id = Hashids::decode($reseller_id)[0];
            } catch (\Throwable $th) {
                return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
            }
            $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('reseller_id', $reseller_id)->first();
        }
        $data['countries'] = ContactCountry::all();
        // $data['reseller']  = ResellerRedeemedPage::where('reseller_id', $reseller_id)->first();
        $data['content']   = $data['reseller']->terms_of_use;

        return view('frontside.reseller.terms-of-use', $data);
    }

    public function privacyPolicy($domain = 'www', $title = null, $reseller_id = null){

        $data = [];
        // try {
        //     //code...
        //     $reseller_id = Hashids::decode($reseller_id)[0];
        // } catch (\Throwable $th) {
        //     return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
        // }
        $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('domain', 'https://'.$domain.'.'.env('reseller_domain'))->first();
        if( !$data['reseller'] ){
            try {
                $reseller_id = Hashids::decode($reseller_id)[0];
            } catch (\Throwable $th) {
                return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
            }
            $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('reseller_id', $reseller_id)->first();
        }
        $data['countries'] = ContactCountry::all();
        // $data['reseller']  = ResellerRedeemedPage::where('reseller_id', $reseller_id)->first();
        $data['content']   = $data['reseller']->privacy_policy;

        return view('frontside.reseller.privacy-policy', $data);

    }

    public function imprint($domain = 'www', $title = null, $reseller_id = null){

        $data = [];
        // try {
        //     //code...
        //     $reseller_id = Hashids::decode($reseller_id)[0];
        // } catch (\Throwable $th) {
        //     return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
        // }
        $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('domain', 'https://'.$domain.'.'.env('reseller_domain'))->first();
        if( !$data['reseller'] ){
            try {
                $reseller_id = Hashids::decode($reseller_id)[0];
            } catch (\Throwable $th) {
                return redirect()->back()->with(session()->flash('alert-error',__('Invalid Reseller Reference. Kindly Setup Customize Page From Admin Side!')));
            }
            $data['reseller'] = ResellerRedeemedPage::with(['reseller_redeemed_page_navigations','user'])->where('reseller_id', $reseller_id)->first();
        }
        $data['countries'] = ContactCountry::all();
        // $data['reseller']  = ResellerRedeemedPage::where('reseller_id', $reseller_id)->first();
        $data['content']   = $data['reseller']->imprint;

        return view('frontside.reseller.imprint', $data);

    }

    public function domain_exists(Request $request)
    {
        try {
            //code...
             $id = Hashids::decode($request->id)[0];
        } catch (\Throwable $th) {
            $id = null;
        }
        $check_domain_exist = ResellerRedeemedPage::where(function($qu) use($request){
                $qu->where('domain','LIKE','https://'.$request->domain.'.%');
                $qu->orWhere('domain','LIKE','https://'.$request->domain.'.%');
        })->where(function($q) use($id){
            if($q != null){
                $q->where('reseller_id','!=',$id );
            }
        })->first();
        // dd($check_domain_exist);
        // dd($request->domain.'.'.env('reseller_domain'));
        if(!empty($check_domain_exist)){
            return 'false';
        }
        return 'true';
    }
}
