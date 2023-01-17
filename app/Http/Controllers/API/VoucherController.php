<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\License;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\VoucherOrder;
use App\Models\Voucher;
use App\Http\Traits\FSecureTrait;

use Auth;
use Hashids;

class VoucherController extends BaseController
{
    use FSecureTrait;
    public function verifyAndRedeemVoucher(Request $request)
    {
        // $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';
        $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';
        $lang_file = base_path('resources/lang/'.$lang.'.json');
        if(!file_exists($lang_file))
        {
            $lang = 'en';
            $lang_file = base_path('resources/lang/'.$lang.'.json');
        }
        $lang_arr = json_decode(file_get_contents($lang_file),true);

        $voucher = Voucher::where(['code' => $request->voucher])->first();

        $status = 0;
        $message = '';
        $data = [];
        if($voucher)
        {
            if($voucher->status == 1) // Active
            {
                $order = $voucher->voucherOrder;
                $reseller = $order->reseller;

                $product = $order->product;
                $order->discount_percentage = $order->saas_discount_percentage;
                $product->discount_percentage = $order->saas_discount_percentage;
                $order->product->discount_percentage = $order->saas_discount_percentage;

                $product->variation = $order->variation;
                $mainProject = $product->project;
                if($reseller->is_active == 1 ) // Reseller is Active & Approved
                {
                    if($mainProject->prefix == $request->platform)
                    {
                        if($order->status == 1) // Approved
                        {
                            $status = 1;
                            $message = $lang_arr['Voucher is valid'];

                            $secondary_projects = [];
                            // dd($product->secondary_project_ids);
                            $secondary_project_ids = explode(',',$product->secondary_project_ids);
                            if(!empty($secondary_project_ids))
                            {

                                $secondary_projects = Project::whereIn('id',$secondary_project_ids)->pluck('prefix');
                            }
                            // dd($voucher);
                            $voucher->order = $order;
                            $data = array(
                                'reseller' => $reseller,
                                'voucher' => $voucher,
                                'order' => $order,
                                'product' => $product,
                                'secondary_projects' => $secondary_projects
                            );
                            // dd(json_decode($request->user_data)[0]->email);
                            if($request->apply_voucher)
                            {
                                $user_email = isset($request->user_data['email']) ? $request->user_data['email'] : json_decode($request->user_data)->email;
                                // dd($request->user_data);
                                unset( $voucher->order);
                                // $this->applyVoucherAndUpdateRecords($request, $order, $voucher);
                                $voucher->redeemed_at = \Carbon\Carbon::now();
                                $voucher->customer_id = null;
                                // $voucher->email = $request->user_data['email'];
                                $voucher->email = $user_email;
                                $voucher->status = 0;
                                $voucher->save();

                                $voucher_order = VoucherOrder::where('id', $voucher->order_id)->first();
                                $voucher_order->used_quantity = $voucher_order->used_quantity == null ? 1 : $voucher_order->used_quantity+1;
                                $voucher_order->remaining_quantity = $voucher_order->remaining_quantity -1;
                                $voucher_order->save();

                                $message = isset($lang_arr['Voucher Applied Successfully']) ? $lang_arr['Voucher Applied Successfully'] : 'Voucher Applied Successfully';
                            }
                        }
                        else
                        {
                            $status = 0;
                            $message = isset($lang_arr['Voucher not approved']) ? $lang_arr['Voucher not approved'] : 'Voucher not approved';
                        }
                    }
                    else
                    {
                        $status = 0;
                        $message = isset($lang_arr['Voucher is not valid for this platform']) ? $lang_arr['Voucher is not valid for this platform'] : 'Voucher is not valid for this platform';
                    }
                }
                else
                {
                    $status = 0;
                    $message = isset($lang_arr['Voucher is disabled']) ? $lang_arr['Voucher is disabled'] : 'Voucher is disabled';
                }
            }
            else
            {
                if($voucher->status == 2)
                {
                    $status = 0;
                    $message = isset($lang_arr['Voucher is disabled']) ? $lang_arr['Voucher is disabled'] : 'Voucher is disabled';
                }
                else
                {
                    $status = 2;
                    $message = isset($lang_arr['Voucher already used']) ? $lang_arr['Voucher already used'] : 'Voucher already used';
                }
            }
        }
        else
        {
            $status = 0;
            $message = isset($lang_arr['Voucher does not exists']) ? $lang_arr['Voucher does not exists'] : 'Voucher does not exists';
        }

        return response()->json([
            'data' => $data,
            'status' => $status,
            'message' => $message
        ]);
        // $response = array('message' => '', 'success'=>false);
        // $validator = Validator::make($request->all(), [
        //     'voucher_code' => 'required',
        //     'platform' => 'required',
        //     'name' => 'required',
        //     'password' => 'required',
        //     'password_confirmation' => 'required',
        //     'timezone' => 'required',
        //     'voucher' => 'required',
        //     'reseller' => 'required'
        // ]);
        // if ($validator->fails()) {
        //     $response['message'] = $validator->messages();
        //     return $response;
        // }
        // $input = $request->all();

        // if(!\Str::substrCount($input['voucher_code'], $input['platform'], 0))
        // {
        //     $response['message'] = 'Voucher mismatch';
        //     return $response;
        // }
        // $voucher = Voucher::where('code', $input['voucher_code'])->first();

        // if(!$voucher)
        // {
        //     return (object)[
        //         'success' => 'false',
        //         'message' => "Voucher not found"
        //     ];
        // }
        // elseif($voucher->redeemed_at != null)
        // {
        //     return (object)[
        //         'success' => 'false',
        //         'message' => "Voucher already used"
        //     ];

        // }
        // elseif($voucher->status == 2)
        // {
        //     return (object)[
        //         'success' => 'false',
        //         'message' => "Voucher disabled"
        //     ];

        // }

        // // Sending API calls for signup on secondary platforms
        // $secondary_platforms = array_filter(explode(',',$voucher->voucherOrder->product->secondary_project_ids));
        // if(in_array('1', $secondary_platforms)) // Transfer Immunity
        // {

        // }
        // if(in_array('2', $secondary_platforms)) // Move Immunity
        // {

        // }
        // if(in_array('3', $secondary_platforms)) // NED.link
        // {

        // }
        // if(in_array('4', $secondary_platforms)) // aikQ
        // {

        // }
        // if(in_array('5', $secondary_platforms)) // Inbox
        // {

        // }
        // if(in_array('6', $secondary_platforms)) // Over Mail
        // {

        // }
        // if(in_array('7', $secondary_platforms)) // Maili
        // {

        // }
        // if(in_array('8', $secondary_platforms)) // QR Code
        // {

        // }
        // $voucher->redeemed_at = \Carbon\Carbon::now();
        // $voucher->customer_id = null;
        // $voucher->status = 0;
        // $voucher->save();

        // $voucher_order = VoucherOrder::where('id', $voucher->order_id)->first();
        // $voucher_order->used_quantity = $voucher_order->used_quantity == null ? 1 : $voucher_order->used_quantity+1;
        // $voucher_order->remaining_quantity = $voucher_order->remaining_quantity -1;
        // $voucher_order->save();

        // return (object)[
        //     'success' => 'true',
        //     'message' => "Voucher valid and applied"
        // ];



    }

    /**
     * 1 Approve
     * 2 Reject
     * 4 Activate
     * 5 Deactive
     */
    public function changeVoucherOrderStatus(Request $request)
    {
        $distributor = $this->verifyRequest($request);
        if($distributor['success'] == 'false'){
            $distributor['success'] = false;
            return $distributor;
        }
        $voucher_order_id = $request->voucher_order_id;
        $status = $request->status;
        if( $status == 0 || $status == 1 || $status == 2){
            VoucherOrder::where('id', $voucher_order_id)->update(['status' => $status]);
            if($status == 1){
                VoucherOrder::where('id', $voucher_order_id)->update(['is_active' => 1]);
            }
            Voucher::where('order_id', $voucher_order_id)->where('status','!=', 0)->update(['status' => $status]);
        }
        elseif( $status == 4 || $status == 5){
            VoucherOrder::where('id', $voucher_order_id)->update(['is_active' => $status == 4 ? 1 : 0]);
            Voucher::where('order_id', $voucher_order_id)->where('status','!=', 0)->update(['status' => $status == 4 ? 1 : 2]);
        }
        $data = [];
        $data['voucher_order'] = VoucherOrder::where('id', $voucher_order_id)->first();
        return array(
            'success' => true,
            'message' => __('Order status updated.'),
            'data' => $data
        );
    }
    /**
     * Redeemed: 0
     * Approved: 1
     * Disable: 2
     *
     */
    public function changeOrderVoucherStatus(Request $request)
    {
        $distributor = $this->verifyRequest($request);
        if($distributor['success'] == 'false'){
            $distributor['success'] = false;
            return $distributor;
        }
        $voucher_id = $request->voucher_id;
        $voucher_order_id = $request->voucher_order_id;
        $status = $request->status;
        $voucher = Voucher::whereHas('voucherOrder',function($query) use($voucher_order_id){
            $query->where('id', $voucher_order_id);
        })->where('id', $voucher_id)->first();
        if($voucher->voucherOrder->status == 1 && $voucher->voucherOrder->is_active == 1){
            Voucher::where('id', $voucher_id)->update(['status' => $status]);
            if($status == 0){
                $voucher->voucherOrder->used_quantity += 1;
                $voucher->voucherOrder->remaining_quantity -= 1;
                $voucher->voucherOrder->save();
            }
            $order_id = Voucher::where('id', $voucher_id)->first()->order_id;
            return array(
                'success' => true,
                'message' => __('Status updated successfully!')
            );
        }
        return array(
            'success' => false,
            'message' => __('Status cannot be updated. The Order against this voucher is in pending, inactive or rejected state')
        );
    }

    /**
     * Redeem Voucher From Distributor
     * voucher_code
     * email
     * name
     * country_id
     */

     public function redeemVoucher(Request $request)
     {
        $distributor = $this->verifyRequest($request);
        if($distributor['success'] == 'false'){
            $distributor['success'] = false;
            return $distributor;
        }
        $input = $request->all();
        $voucher = Voucher::wherehas('voucherOrder', function($query) use($distributor){
            $query->where('distributor_id',$distributor->id);
            $query->where('status',1);
            $query->where('is_active',1);
        })->where('code', $input['voucher_code'])->orWhere('status', 0)->where('status',1)->first();
        if(!$voucher || $voucher->status == 2)
        {
            return array(
                'success' => false,
                'message' => __('The voucher is not active. Contact the Administrator.')
            );
        }
        elseif($voucher->redeemed_at != null)
        {
            return array(
                'success' => false,
                'message' => __('The voucher has been redeemed before.')
            );
        }
        if($voucher->voucherOrder->product->product_type == 1)  //If the product is not license based
        {
            return array(
                'success' => false,
                'message' => __('Voucher is not valid on this platform. Connect with reseller to confirm.')
            );
        }
        $registration_data['email'] = $request->email;
        $registration_data['firstname'] = $request->name;
        $registration_data['country_id'] = $request->country_id;
        $registration_data['new_account'] = null;
        $registration_data['password'] = null;

        $check_registration_for_guest_and_register = check_registration_for_guest_and_register($registration_data);
        $user = User::where('id',$check_registration_for_guest_and_register['user_id'])->first();

        if( $voucher ){
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
                    $license->expiry_date = \Carbon\Carbon::now()->addMonths($duration_months);
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
            $duration_months = 1;
            if($license_attached['success'] == 'true'){
                $voucher->redeemed_at = \Carbon\Carbon::now();
                $voucher->customer_id = $user->id;
                $voucher->status = 0;
                $voucher->save();

                $voucher_order = VoucherOrder::where('id', $voucher->order_id)->first();
                $voucher_order->used_quantity = $voucher_order->used_quantity == null ? 1 : $voucher_order->used_quantity+1;
                $voucher_order->remaining_quantity = $voucher_order->remaining_quantity -1;
                $voucher_order->save();

                $name = $voucher_order->distributor->name;
                $email = $voucher_order->distributor->email;
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

                $secondary_platforms = array_filter(explode(',',$voucher_order->product->secondary_project_ids));
                if(count($secondary_platforms) > 0){
                    foreach ($secondary_platforms as $key => $value) {
                        $secondary_platforms[$key] = Project::where('id',$value)->first()->prefix;
                    }
                    $user = (object)array();
                    $user->voucher = $input['voucher_code'];

                    $user->name = $input['name'];
                    $user->email = $input['email'];
                    $user->country_id = $input['country_id'];

                    $distributor = $user->name.' ('.$user->email.')';
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
                    // dispatch(new \App\Jobs\SecondaryPlatformAccountGeneration($secondary_platforms, $user,  $user->name.' ('.$user->email.')',$duration_months));
                    // createAccountOnSecondaryPlatforms($secondary_platforms, $user,  $user->name.' ('.$user->email.')',$duration_months);
                }
                $license = License::select('license_key')->where('id', $license_attached['license']->id)->first();
                return array(
                    'success' => true,
                    'message' => __('Voucher Redeemed Successfuly'),
                    'license' => $license,
                    'secondary_projects' => $secondary_platforms,
                    'duration_months' => $duration_months
                );
            }
        }
        return array(
            'success' => false,
            'message' => __('Low License')
        );
        // $body = "One of the customer tried to redeem a voucher but the licenses were out of stock. Kindly purchase upload more vouchers to avoid further issues.";
        // $body .= "<br> <strong>".$voucher->voucherOrder->product->product_name.' '.@$voucher->voucherOrder->variation->variation_name."</strong> ";
        // $body .= "<br> Kindly Purchase new licenses to avoid inconvinience. ";
        // $this->requestAdmintoUploadMoreVouchers($voucher->voucherOrder->product->id, @$voucher->voucherOrder->variation->id, $body);
        // return redirect()->back()->with(session()->flash('alert-error', __('Voucher cannot be redeemed. No License Currently Available')));

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


}
