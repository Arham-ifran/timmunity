<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Products;
use App\Models\License;
use App\Http\Traits\AdminNotificationTrait;
use App\Http\Traits\FSecureTrait;
use App\Models\ChannelPilotOrderItemVoucher;
use App\Models\SiteSettings;
use App\Models\Project;
use App\Models\QuotationOrderLine;
use App\Models\QuotationOrderLineVoucher;
use App\Models\EmailTemplate;
use App\Models\ContactCountry;
use App\Models\ProductVariation;
use Auth;
use Hashids;

class GenericVoucherController extends Controller
{
    use FSecureTrait;
    use AdminNotificationTrait;
    public function redeemPage($title = null, $reseller_id = null)
    {
        $data['countries'] = ContactCountry::all();
        return view('frontside.redeem-generic.redeem-page',$data);
    }

    public function redeemVoucher(Request $request)
    {

        $input = $request->all();
        $channel_pilot = 0;
        $voucher = QuotationOrderLineVoucher::where('voucher_code', $input['voucher_code'])->first();
        $download_link = isset($voucher->order_line->product->generalInformation->download_link)?$voucher->order_line->product->generalInformation->download_link:'';

        if(!$voucher)
        {
            $voucher = ChannelPilotOrderItemVoucher::where('voucher_code', $input['voucher_code'])->first();
            if($voucher){
                $channel_pilot = 1;                    // Setting $voucher->channel_pilot to 1 to check the voucher parent
            }else{
                return redirect()->back()->with(session()->flash('alert-error',__('The voucher is not active. Contact the Administrator.')));
            }
        }
        if($voucher){
            if($voucher->redeemed_at)
            {
                return redirect()->back()->with(session()->flash('alert-error',__('The voucher has been redeemed before.')));
            }
            if($voucher->status == 0)
            {
                return redirect()->back()->with(session()->flash('alert-error',__('The voucher is in-active.')));
            }
            if($channel_pilot == 0){
                if($voucher->quotation->is_refunded){
                    return redirect()->back()->with(session()->flash('alert-error',__('The voucher has been refunded.')));
                }
            }
            if($channel_pilot == 0){
                $customerObj = $voucher->order_line->quotation->customer;
                $productObj = $voucher->order_line->product;
                $productObj->variation = $voucher->order_line->variation;
                if($voucher->order_line->product->product_type == 2){
                    // it's yes, then API call for create order and get license and save it into license table.
                    $f_secure_license_object = $this->getLicenseFSecure($customerObj, $productObj);
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
                        $license->product_id = $voucher->order_line->product->id;
                        $license->variation_id = @$voucher->order_line->variation->id;
                        $license->status = 1;       // And status should be active == 1
                        $license->is_used = 0;      // Should not be in used
                        $license->expiry_date = \Carbon\Carbon::now()->addMonths($duration_months);
                        $license->save();
                    }
                    else
                    {
                        return redirect()->back()->with(session()->flash('alert-warning',__('License cannot be fetched. Contact Admin')));
                    }

                }
            }
            else        // Channel Pilot Order
            {
                $ChannelOrderItem = $voucher->item;
                $productObj = Products::whereHas('variations',function($q) use($ChannelOrderItem){
                    $q->where('ean', $ChannelOrderItem->ean);
                })->first();
                $productObj->variation = ProductVariation::where('ean', $ChannelOrderItem->ean)->first();
                if($productObj->product_type == 2){
                    // it's yes, then API call for create order and get license and save it into license table.
                    $f_secure_license_object = $this->getLicenseFSecure('', $productObj);
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
                        $license->product_id = $productObj->id;
                        $license->variation_id = @$productObj->variation->id;
                        $license->status = 1;       // And status should be active == 1
                        $license->is_used = 0;      // Should not be in used
                        $license->expiry_date = \Carbon\Carbon::now()->addMonths($duration_months);
                        $license->save();
                    }
                    else
                    {
                        return redirect()->back()->with(session()->flash('alert-warning',__('License cannot be fetched. Contact Admin')));
                    }

                }
            }
        }


        $license_attached = $this->attachLicense($voucher->id,$channel_pilot);
        $user = null;
        if(isset($license_attached['all_license_generated']))
        {
            if(Auth::user()){
                $voucher->redeemed_at = \Carbon\Carbon::now();
                $voucher->license_id = $license_attached['license_id'];
                $voucher->redeemed = 1;
                $voucher->customer_id = Auth::user()->id;
                $voucher->save();
                $user = Auth::user();
                $user->voucher = $voucher;
            }else{
                $registration_data['email'] = $request->email;
                $registration_data['firstname'] = $request->name;
                $registration_data['country_id'] = $request->country_id;
                $registration_data['new_account'] = $request->new_account;
                $registration_data['password'] = $request->password;

                $check_registration_for_guest_and_register = check_registration_for_guest_and_register($registration_data);
                if($check_registration_for_guest_and_register['error'] == true){
                    return redirect()->back()->with(session()->flash('alert-warning', __('Email ID is already registered. Login to continue')));
                }
                // dd($license_attached);
                $voucher->redeemed_at = \Carbon\Carbon::now();
                $voucher->license_id = $license_attached['license_id'];
                $voucher->redeemed = 1;
                $voucher->customer_id = $check_registration_for_guest_and_register['user_id'];
                $voucher->save();
                $user = User::where('id', $check_registration_for_guest_and_register['user_id'])->first();
                $user->voucher = $voucher;
            }
            $secondary_platforms = [];
            if($channel_pilot == 0)
            {
                $secondary_platforms = array_filter(explode(',',$voucher->order_line->product->secondary_project_ids));
            }
            else
            {
                $productObj = Products::whereHas('variations',function($q) use($voucher){
                    $q->where('ean', $voucher->item->ean);
                })->first();
                $secondary_platforms = array_filter(explode(',',$productObj->secondary_project_ids));
            }
            foreach ($secondary_platforms as $key => $value) {
                $secondary_platforms[$key] = Project::where('id',$value)->first()->prefix;
            }


            $duration_months = 1;
            // dd($voucher->order_line);
            if($voucher->order_line->variation != null){
                foreach($voucher->order_line->variation->variation_details as $variation_detail)
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
            createAccountOnSecondaryPlatforms($secondary_platforms, $user, null , $duration_months);


            if(!empty($download_link)){
                $name  =  Auth::user() ? Auth::user()->name : $request->name;
                $email = Auth::user() ? Auth::user()->email : $request->email;

                $order_number = '';
                if($channel_pilot == 0)
                {
                    $order_number = "S".str_pad($voucher->quotation->id, 5, '0', STR_PAD_LEFT);
                }
                else
                {
                    $order_number = $voucher->item->order->IdExternal.' ('.$voucher->item->order->source.')';
                }

                $email_template = EmailTemplate::where('type','order_licenses_email')->first();

                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);
                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $search = array("{{name}}","{{order_number}}","{{licenses_list}}","{{app_name}}",'{{voucher}}','{{download}}');
                $replace = array($name,$order_number,$license_attached['licenses_html'],env('APP_NAME'),$voucher->voucher_code, $download_link);

                $content = str_replace($search,$replace,$content);
                // dd($content);

                dispatch(new \App\Jobs\SendLicenseEmailJob($email,$subject,$content));

                return redirect()->back()->with(session()->flash('alert-success', __('Voucher redeemed successfully. Check your email for details')));
            }else{
                $name  =  Auth::user() ? Auth::user()->name : $request->name;
                $email = Auth::user() ? Auth::user()->email : $request->email;
                $order_number = '';
                if($channel_pilot == 0)
                {
                    $order_number = "S".str_pad($voucher->quotation->id, 5, '0', STR_PAD_LEFT);
                }
                else
                {
                    $order_number = $voucher->item->order->IdExternal.' ('.$voucher->item->order->source.')';
                }
                $email_template = EmailTemplate::where('type','order_license_email_without_download_link')->first();

                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);
                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $search = array("{{name}}","{{order_number}}","{{licenses_list}}","{{app_name}}",'{{voucher}}');
                $replace = array($name,$order_number,$license_attached['licenses_html'],env('APP_NAME'),$voucher->voucher_code);

                $content = str_replace($search,$replace,$content);
                // dd($content);

                dispatch(new \App\Jobs\SendLicenseEmailJob($email,$subject,$content));

                return redirect()->back()->with(session()->flash('alert-success', __('Voucher redeemed successfully. Check your email for details')));
           }
        }
        $body = "One of the customer tried to redeem a voucher but the licenses were out of stock. Kindly purchase upload more vouchers to avoid further issues.";
        $body .= "<br> <strong>".$voucher->voucherOrder->product->product_name.' '.@$voucher->voucherOrder->variation->variation_name."</strong> ";
        $body .= "<br> Kindly Purchase new licenses to avoid inconvinience. ";
        $this->requestAdmintoUploadMoreVouchers($voucher->voucherOrder->product->id, @$voucher->voucherOrder->variation->id, $body);
        return redirect()->back()->with(session()->flash('alert-error', __('Voucher cannot be redeemed. No License urreCntly Available')));

    }

    public function attachLicense($voucher_id,$channel_pilot = 0){
        // Get all the quotation order lines
        $all_license_generated = true;
        $voucher = null;
        if($channel_pilot == 0){
            $voucher = QuotationOrderLineVoucher::where('id', $voucher_id)->first();

        }
        else{
            $voucher = ChannelPilotOrderItemVoucher::where('id', $voucher_id)->first();
            if($voucher){
                $channel_pilot = 1;
            }
        }
        $licenses = [];
        $licenses_html = '';
        $license_id = 0;
        if($channel_pilot == 0)
        {
            $quotation_order_line = QuotationOrderLine::where('id', $voucher->quotation_order_line_id)->first();
            if($quotation_order_line->product != null){
                $product_name = $quotation_order_line->product->product_name.' '.@$quotation_order_line->variation->variation_name;
                $licenses[$product_name] = [];
                // Count for the Licenses added for the QuotationOrderLine
                $check_license_count = License::where('quotation_order_line_id',$quotation_order_line->id)->count();
                // Licenses Count for the item added
                $licenses_count = License::where('product_id', $quotation_order_line->product->id);
                if($quotation_order_line->variation != null){
                    $licenses_count = $licenses_count->where('variation_id',$quotation_order_line->variation->id);
                }
                $licenses_count = $licenses_count->where('status',1);
                $licenses_count = $licenses_count->where('is_used',0);
                $licenses_count = $licenses_count->count();
                // If available license count is is less the ordered quantity
                if( $licenses_count < 1 )
                {
                    $all_license_generated = false;
                }
                else
                {
                    $license = License::where('product_id', $quotation_order_line->product->id);
                    if($quotation_order_line->variation != null){
                        $license = $license->where('variation_id',$quotation_order_line->variation->id);
                    }
                    $license = $license->where('status',1);
                    $license = $license->where('quotation_order_line_id',null);
                    $license = $license->where('channel_pilot_order_item_id',null);
                    $license = $license->where('is_used',0);
                    $license = $license->inRandomOrder();
                    $license = $license->first();

                    $license->quotation_order_line_id = $quotation_order_line->id;
                    $license->is_used = 1;
                    $license->save();

                    $license_id = $license->id;
                    $licenses[$product_name][] = $license->license_key;
                }
            }
        }
        else
        {
            $item = $voucher->item;
            $item->product = Products::whereHas('variations',function($q) use($voucher){
                $q->where('ean', $voucher->item->ean);
            })->first();
            $item->variation = ProductVariation::where('ean', $voucher->item->ean)->first();

            if($voucher->item->product != null){
                $product_name = $voucher->item->article;
                $licenses[$product_name] = [];

                // Licenses Count for the item added
                $licenses_count = License::where('product_id', $voucher->item->product->id);
                if($voucher->item->variation != null){
                    $licenses_count = $licenses_count->where('variation_id',$voucher->item->variation->id);
                }
                $licenses_count = $licenses_count->where('status',1);
                $licenses_count = $licenses_count->where('is_used',0);
                $licenses_count = $licenses_count->count();

                // If available license count is is less the ordered quantity
                // dd($voucher->item->ean, $voucher->item->product, $voucher->item->variation,$licenses_count);
                if( $licenses_count < 1 )
                {
                    $all_license_generated = false;
                }
                else
                {
                    $license = License::where('product_id', $voucher->item->product->id);
                    if($voucher->item->variation != null){
                        $license = $license->where('variation_id',$voucher->item->variation->id);
                    }
                    $license = $license->where('status',1);
                    $license = $license->where('quotation_order_line_id',null);
                    $license = $license->where('channel_pilot_order_item_id',null);
                    $license = $license->where('is_used',0);
                    $license = $license->inRandomOrder();
                    $license = $license->first();

                    $license->channel_pilot_order_item_id = $voucher->item->id;
                    $license->is_used = 1;
                    $license->save();

                    $license_id = $license->id;
                    $licenses[$product_name][] = $license->license_key;
                }
            }
        }
        // Transformation of Order Placed Email Template
        $licenses_arr = [];
        if(count($licenses) > 0) {
            foreach($licenses as $product => $licences) {
               $unorderd_list =  '<p style="font-size: 18px; line-height: 25px;"><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;"><u><b>'.$product.'</b></u></span></p><ul>';
                array_push($licenses_arr,$unorderd_list);
                if(isset($licences[0])){
                    foreach($licences as $license) {
                        $licenses_list = '<li>'.$license.'</li>';
                        array_push($licenses_arr,$licenses_list);
                    }
                }
               $unorderd_list = '</ul>';
               array_push($licenses_arr,$unorderd_list);
            }
            $licenses_html = implode(' ', $licenses_arr);
        }
        else {
            $licenses_html = "<p>There's no license</p>";
        }

        return [
            'all_license_generated' => $all_license_generated,
            'license_id' => $license_id,
            'licenses_html' => $licenses_html
        ];
    }
}
