<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\ActivitiesRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\Followers;
use App\Models\Contact;
use File;
use Hashids;
use Auth;

class ActivitySendMessageController extends Controller
{
	 /**
     * @var activities.
     */
    protected $activities;
    /**
     * ActivitySendMessageController Constructor.
     *
     * @param ActivitiesRepositoryInterface $activities
     */
    public function __construct(ActivitiesRepositoryInterface $activities)
    {
        $this->activities = $activities;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	  // Method For Send Messages
    public function sendNewMessage(Request $request)
    {
        $input = $request->all();
        $logged_contact = Contact::where('admin_id',Auth::user()->id)->first();
        $module = $input['module'];
        $model_id = Hashids::decode($input['model_id'])[0];
		$recipient_arr = explode(",", $input['recipients']);
		$recipientIdArray = array();
		$recipientEmailArray = array();
		foreach($recipient_arr as $key => $row) {
		    if((filter_var($row, FILTER_VALIDATE_EMAIL)))$recipientEmailArray[] = $row;
		    else $recipientIdArray[] = $row;
		}
		$newIdsArray= array();
	    foreach($recipientEmailArray as $row) {
		    	$expEmail = explode('@', $row);
		    	$model = new Contact();
				$model->created_by = $logged_contact->id;
		    	$model->name = $expEmail[0];
		        $model->email = $row;
		        $model->save();
            array_push($newIdsArray, $model->id);
	    }
	   
	    $recipients = array_merge($recipientIdArray,$newIdsArray);
	        $model = new ActivityMessages();
	        $model->log_user_id = Hashids::decode($input['uid'])[0];
	        if($module == "kaspersky") {
	            $model->kss_subscription_id = $model_id;
	        }
            else if($module == "vouchers"){
	            $model->voucher_id = $model_id;
            }
            else if($module == "quotations") {
	            $model->quotation_id = $model_id;
            }
            else if($module == "contacts") {
	            $model->contact_id = $model_id;
            }
            else if($module == "customers") {
	            $model->customer_id = $model_id;
            }
            else if($module == "products") {
	            $model->product_id = $model_id;
            }
            else if($module == "productVariants") {
	            $model->variant_id = $model_id;
            }
            else if($module == "saleTeams") {
	            $model->sales_team_id = $model_id;
            }
	        $model->subject = $input['subject'];
	        $model->message = $input['message'];
	        $model->save();
	        foreach($recipients  as $recipient) {
        	if (Contact::where('id', $recipient)->where('admin_id','!=', null)->exists()) {
              $follower_type = 2;
              $this->activities->delete_unfollow_records($logged_contact->id, $recipient, $input['module_type'], $follower_type, $model_id);
        	}
        	else {
             $follower_type = 1;    
              $this->activities->delete_unfollow_records($logged_contact->id, $recipient, $input['module_type'], $follower_type,$model_id);
        	}
	          $follower = new Followers();
		        if($module == "kaspersky") {
		            $follower->kss_subscription_id = $model_id;
		        }
	            else if($module == "vouchers"){
		            $follower->voucher_id = $model_id;
	            }
	            else if($module == "quotations") {
		            $follower->quotation_id = $model_id;
	            }
	            else if($module == "contacts") {
		            $follower->contact_model_id = $model_id;
	            }
	            else if($module == "customers") {
	                 $follower->customer_id = $model_id;
	            }
	            else if($module == "products") {
		            $follower->product_id = $model_id;
	            }
	            else if($module == "productVariants") {
		            $follower->variant_id = $model_id;
	            }
	            else if($module == "saleTeams") {
		            $follower->sales_team_id = $model_id;
	            }
	          $follower->contact_id = $logged_contact->id;
	          $follower->follower_id =  $recipient;
	          $follower->module_type = $input['module_type'];
	          $follower->follower_type = $follower_type;
	          $follower->save();
	          // Delete if current follower match with existing
        	
	        }
	        // Follow back logged user to selected recipents
	        foreach($recipients  as $recipient) {
	        	 // Delete Follow Back User
            $this->activities->delete_unfollow_back_records($logged_contact->id, $recipient, $input['module_type'], $model_id);
		          $follower = new Followers();
		            if($module == "kaspersky") {
		               $follower->kss_subscription_id = $model_id;
			        }
		            else if($module == "vouchers"){
			            $follower->voucher_id = $model_id;
		            }
		            else if($module == "quotations") {
			            $follower->quotation_id = $model_id;
		            }
		            else if($module == "contacts") {
			            $follower->contact_model_id = $model_id;
		            }
		            else if($module == "customers") {
	                    $follower->customer_id = $model_id;
		            }
		            else if($module == "products") {
			            $follower->product_id = $model_id;
		            }
		            else if($module == "productVariants") {
			            $follower->variant_id = $model_id;
		            }
		            else if($module == "saleTeams") {
			            $follower->sales_team_id = $model_id;
		            }
		          $follower->contact_id = $recipient;
		          $follower->follower_id = $logged_contact->id;
		          $follower->module_type = $input['module_type'];
		          $follower->follower_type = 2;
		          $follower->save();
	          
	        }

	        // Is following the current user
	        if($module == "kaspersky") {
               $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 0)->where('kss_subscription_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
	        }
            else if($module == "vouchers") {
	            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 1)->where('voucher_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
            }
            else if($module == "quotations") {
	            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 2)->where('quotation_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
            }
            else if($module == "contacts") {
	            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 3)->where('contact_model_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
            }
            else if($module == "customers") {
                $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 4)->where('customer_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
            }
            else if($module == "products") {
	             $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 5)->where('product_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
            }
            else if($module == "productVariants") {
	            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 6)->where('variant_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
            }
            else if($module == "saleTeams") {
	            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['partner_id'])[0])->where('module_type', 7)->where('sales_team_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
            }
	        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', Auth::user()->id)->exists()) {

	            $is_following = 1;
	        }
	        else {

	            $is_following = 0;
	        }
	        // Update Followers list and counter
	        $updated_follower_list = $this->activities->updated_followers_list($model_id, Hashids::decode($input['partner_id'])[0], $input['module_type']);
            $follower_count = $this->activities->follower_counter($model_id, Hashids::decode($input['partner_id'])[0], $input['module_type']);
	        // Make Directory 
	        $file_path = public_path() . '/storage/uploads/attachements/SendMessage/' . $input['module'] .'/' . Hashids::encode($model->id);
			    if (!File::exists(public_path() . '/storage/uploads/attachements/SendMessage/' . $input['module'] .'/'. Hashids::encode($model->id))) {
			        File::makeDirectory($file_path, 0777, true);
		    }

		    // File Uploading
		    $x =0;
	        if($request->hasfile('filenames'))
	         {
	            foreach($request->file('filenames') as $file)
	            {
	                $fileName = time().rand(1,100).'.'.$file->getClientOriginalExtension();
	                $filePath = $file->storeAs('uploads/attachements/SendMessage/'. $input['module'] .'/'. Hashids::encode($model->id), $fileName, 'public'); 
	                if($module == "kaspersky") {
			            $insert[$x]['kss_subscription_id'] = $model_id;
			        }
		            else if($module == "vouchers"){
			            $insert[$x]['voucher_id'] = $model_id;
		            }
		            else if($module == "quotations") {
			            $insert[$x]['quotation_id'] = $model_id;
		            }
		            else if($module == "contacts") {
			            $insert[$x]['contact_id'] = $model_id;
		            }
		            else if($module == "customers") {
			            $insert[$x]['customer_id'] = $model_id;
		            }
		            else if($module == "products") {
			            $insert[$x]['product_id'] = $model_id;
		            }
		            else if($module == "productVariants") {
			            $insert[$x]['variant_id'] = $model_id;
		            }
		            else if($module == "saleTeams") {
			            $insert[$x]['sales_team_id'] = $model_id;
		            }
	                $insert[$x]['send_msg_id'] = $model->id;
	                $insert[$x]['file_name'] = $fileName;
	                $insert[$x]['file_extension'] = $file->getClientOriginalExtension();
	                $insert[$x]['module_name'] = $input['module'];
	                $insert[$x]['created_at'] = date('Y-m-d H:i:s');
	                $insert[$x]['updated_at'] = date('Y-m-d H:i:s');
	                $x++;
	            }

	             ActivityAttachments::insert($insert);
	        }
	        $arr = [];
            $img_ext = ["jpg", "jpeg", "png", "gif","webp"];
            if($module == "kaspersky") {
	            $msg_user_details = ActivityMessages::with('activity_message_users')->where('kss_subscription_id',$model_id)->orderBy('id','desc')->first();
	        }
            else if($module == "vouchers"){
	            $msg_user_details = ActivityMessages::with('activity_message_users','activity_attachments')->where('voucher_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "quotations") {
	            $msg_user_details = ActivityMessages::with('activity_message_users','activity_attachments')->where('quotation_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "contacts") {
	            $msg_user_details = ActivityMessages::with('activity_message_users','activity_attachments')->where('contact_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "customers") {
	            $msg_user_details = ActivityMessages::with('activity_message_users','activity_attachments')->where('customer_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "products") {
	            $msg_user_details = ActivityMessages::with('activity_message_users','activity_attachments')->where('product_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "productVariants") {
	            $msg_user_details = ActivityMessages::with('activity_message_users','activity_attachments')->where('variant_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "saleTeams") {
	            $msg_user_details = ActivityMessages::with('activity_message_users','activity_attachments')->where('sales_team_id',$model_id)->orderBy('id','desc')->first();
            }
            $attachments = ActivityAttachments::where('send_msg_id', $model->id)->orderBy('id','desc')->get();
            foreach($msg_user_details->activity_attachments as $attachment) {
            	if($module == "kaspersky") {
	               $attachment_model_id = $attachment->kss_subscription_id;
		        }
	            else if($module == "vouchers"){
		            $attachment_model_id = $attachment->voucher_id;
	            }
	            else if($module == "quotations") {
		            $attachment_model_id = $attachment->quotation_id;
	            }
	            else if($module == "contacts") {
		            $attachment_model_id = $attachment->contact_id;
	            }
	             else if($module == "customers") {
	                 $attachment_model_id = $attachment->customer_id;
	            }
	            else if($module == "products") {
		            $attachment_model_id = $attachment->product_id;
	            }
	            else if($module == "productVariants") {
		            $attachment_model_id = $attachment->variant_id;
	            }
	            else if($module == "saleTeams") {
		            $attachment_model_id = $attachment->sales_team_id;
	            }
            	 $remove_msg_file = "remove_msg_file_".Hashids::encode($attachment->id);
            	 if(in_array($attachment->file_extension, $img_ext)) {
                   $attachment_html = '<span class="description" id="'.$remove_msg_file.'"><div class="customer-box" style="width: 410px;"><div class="customer-img"><img src="'.asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ).'" style="height: 100%"></div><div class="customer-content col-md-3"><h3 class="customer-heading">'.__('File Name').': '. $attachment->file_name .'</h3><span class="email">'.__('File Type').': '. $attachment->file_extension .'</span><a type="button" class="price" href="'.asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ).'" download>'.__('Download').'</a>&nbsp;<a href="javascript:void(0)" type="button" class="price" onclick="removeMsgFile($(this))" data-msg-attachment-id="'. Hashids::encode($attachment->id).'"  data-log-msg-id ="'.Hashids::encode($attachment->send_msg_id).'" data-msg-file-name="'.$attachment->file_name.'" data-msg-file-url="'.route('admin.log.remove-msg-file').'" data-msg-model-id="' .Hashids::encode($attachment_model_id). '" data-msg-module-name ="' .$attachment->module_name.'"><i class="class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></div></div></span>';
                   
                   array_push($arr,$attachment_html);
            	 }
                 else {

                    $attachment_html = '<span class="description margin-bottom-15"  id="'.$remove_msg_file.'"><b>'.__('File').':</b><a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="'.asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ).'" download>'. $attachment->file_name .' &nbsp;<i class="fa fa-download"></i></a><a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="javascript:void(0)" onclick="removeMsgFile($(this))" data-msg-attachment-id="'. Hashids::encode($attachment->id).'" data-log-msg-id ="'.Hashids::encode($attachment->send_msg_id).'" data-msg-file-name="'.$attachment->file_name.'" data-msg-file-url="'.route('admin.log.remove-msg-file').'" data-msg-model-id="' .Hashids::encode($attachment_model_id). '" data-msg-module-name ="' .$attachment->module_name.'"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></span>';

                    array_push($arr,$attachment_html);
                 }
            }
            $html = implode(" ",$arr);
	        $user_url = route('admin.admin-user.edit',['admin_user'=> $input['uid']]);
	        $username = $msg_user_details->activity_message_users->firstname .' '. $msg_user_details->activity_message_users->lastname;
            $attachments =  $this->activities->all_activity_attachements($model_id, $module);
            $attachment_list =  $this->activities->updated_attachment_list($model_id, $module);
	 	    $img = '<img class="img-circle" src="' . checkImage(asset("storage/uploads/admin/" . Hashids::encode($msg_user_details->activity_message_users->id) . '/' . $msg_user_details->activity_message_users->image),'avatar5.png') . '"  alt='.__('User Image').'>';
	        return response()->json([
	        	'message'=> strip_tags($model->message), 
	        	"ago"=> $model->created_at->diffForHumans(), 
	        	"img" => $img, 
	        	"username" => $username, 
	        	"user_msg_url" => $user_url,
	        	"attachments" => $html,
	        	'follower_count' => $follower_count, 
	        	'is_following' => $is_following,
	     	    'updated_follower_list'=> $updated_follower_list,
	     	    'attachment_counter'=> $attachments->count(),
	     	    'attachment_list' => $attachment_list

	        ]);

    }
      // Method For Log Message Remove File
    public function removeMessageFile(Request $request) 
    {
    	$input = $request->all();
    	$id = Hashids::decode($input['id'])[0];
    	$send_msg_id = Hashids::decode($input['log_id'])[0];
    	$model_id = Hashids::decode($input['model_id'])[0];
    	$module = $input['module_name'];
    	$model = ActivityAttachments::where('id', $id)->where('send_msg_id', $send_msg_id)->first();
    	$file = public_path() .'/storage/uploads/attachements/SendMessage/'. $model->module_name . '/' . Hashids::encode($model->send_msg_id) . '/' . $model->file_name;
    	File::delete($file);
    	$model->delete();
    	$attachments_count =  $this->activities->all_activity_attachements($model_id, $module)->count();
        $attachment_list =  $this->activities->updated_attachment_list($model_id, $module);
    	return response()->json(['deleted' => __('Your file has been deleted successfully.'), "attachment_counter" => $attachments_count, 'attachment_list' => $attachment_list]);
    }
    public function addNewContact(Request $request)
    {
    	
    	if (filter_var($request->input('tag'), FILTER_VALIDATE_EMAIL)) {
    		if (Contact::where('email', '=', $request->input('tag'))->exists()) {
			   return response()->json(['error' => __('This email is already exist in the system.Please try another one!')]);
			}
		}
		else {

			return response()->json(['error' => __('Invalid Email Format.')]);
		}

    }
    public function userFollowing(Request $request)
    {
    	 $input = $request->all();
         $logged_user = Contact::where('admin_id', Auth::user()->id)->first();
         $follow_model_id = Hashids::decode($input['model_id'])[0];
         $follow_partner_id = Hashids::decode($input['partner_id'])[0];
         $follow_module_type = $input['module_type'];
        if (Contact::where('id', $follow_partner_id)->where('admin_id','!=', null)->exists()) {
          $follower_type = 2;
    	}
    	else {
         $follower_type = 1; 
    	}
         if(Hashids::decode($input['partner_id'])[0] != $logged_user->id) {
		 	 $follower = new Followers();
		        if($follow_module_type == 0) {
	               $follower->kss_subscription_id = $follow_model_id;
		        }
	            else if($follow_module_type == 1){
		            $follower->voucher_id = $follow_model_id;
	            }
	            else if($follow_module_type == 2) {
		            $follower->quotation_id = $follow_model_id;
	            }
	            else if($follow_module_type == 3) {
		            $follower->contact_model_id = $follow_model_id;
	            }
	            else if($follow_module_type == 4) {
		            $follower->customer_id = $follow_model_id;
	            }
	            else if($follow_module_type == 5) {
		            $follower->product_id = $follow_model_id;
	            }
	            else if($follow_module_type == 6) {
		            $follower->variant_id = $follow_model_id;
	            }
	            else if($follow_module_type == 7) {
		            $follower->sales_team_id = $follow_model_id;
	            }
		     $follower->contact_id = $logged_user->id;
		     $follower->follower_id = $follow_partner_id;
		     $follower->module_type = $follow_module_type;
		     $follower->follower_type = $follower_type;
		     $follower->save();
		 }
	     // Follow Back 
	     $follower = new Followers();
	        if($follow_module_type == 0) {
	           $follower->kss_subscription_id = $follow_model_id;
	        }
	        else if($follow_module_type == 1){
	            $follower->voucher_id = $follow_model_id;
	        }
	        else if($follow_module_type == 2) {
	            $follower->quotation_id = $follow_model_id;
	        }
	        else if($follow_module_type == 3) {
	            $follower->contact_model_id = $follow_model_id;
	        }
	        else if($follow_module_type == 4) {
	            $follower->customer_id = $follow_model_id;
            }
            else if($follow_module_type == 5) {
	            $follower->product_id = $follow_model_id;
            }
            else if($follow_module_type == 6) {
	            $follower->variant_id = $follow_model_id;
            }
            else if($follow_module_type == 7) {
	            $follower->sales_team_id = $follow_model_id;
            }
	     $follower->contact_id = $follow_partner_id;
	     $follower->follower_id =  $logged_user->id;
	     $follower->module_type = $follow_module_type;
	     $follower->follower_type = 2;
	     $follower->save();

	     $updated_follower_list = $this->activities->updated_followers_list($follow_model_id,$follow_partner_id, $follow_module_type);
	     $follower_count = $this->activities->follower_counter($follow_model_id, $follow_partner_id, $follow_module_type);
	     return response()->json([
	     	'follower_count' => $follower_count, 
	     	'updated_follower_list'=> $updated_follower_list
	     ]);
    }
    public function userUnFollow(Request $request)
    {
    	$input = $request->all();
    	$logged_user = Contact::where('admin_id', Auth::user()->id)->first();
    	$model_id = Hashids::decode($input['model_id'])[0];
    	$partner_id = Hashids::decode($input['partner_id'])[0];
    	$module_type = $input['module_type'];
    	if (Contact::where('id', $partner_id)->where('admin_id','!=', null)->exists()) {
          $follower_type = 2;
    	}
    	else {
         $follower_type = 1; 
    	}
        $this->activities->delete_unfollow_records($logged_user->id, $partner_id, $module_type, $follower_type, $model_id);
        // Delete Follow Back User
        $this->activities->delete_unfollow_back_records($logged_user->id, $partner_id, $module_type, $model_id);
        $updated_follower_list = $this->activities->updated_followers_list($model_id, $partner_id, $module_type);
        $follower_count = $this->activities->follower_counter($model_id, $partner_id, $module_type);
	    return response()->json([
	     	'follower_count' => $follower_count,
	     	'updated_follower_list'=> $updated_follower_list,
	    ]);
    }
}
