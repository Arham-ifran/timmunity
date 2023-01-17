<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\ActivitiesRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScheduleActivities;
use App\Models\ActivityAttachments;
use App\Models\Followers;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\EmailTemplate;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Hashids;
use Auth;
use File;

class SchedualActivityController extends Controller
{
	 /**
     * @var activities.
     */
    protected $activities;
    /**
     * SchedualActivityController Constructor.
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
	  // Method For Schedule Activities
    public function scheduleActivity(Request $request) {
    	$input = $request->all();
        // dd($input);
    	$id = Hashids::decode($input['id']);
        $module = $input['module'];
        $model_id =  Hashids::decode($input['schedule_model_id'])[0];
    	if($input['action'] == "Edit") {
    		$status = (isset($input['schedule_flag']) && $input['schedule_flag'] <> 0) ? 1 : 0;
            $model = ScheduleActivities::find($id)[0];
            if(isset($model->details) && $input['details'] == null)
                $long_details = $model->details;
            else
                $long_details = $input['details'];
	        $model->schedule_user_id  = Hashids::decode($input['log_user_id'])[0];
	        $model->assign_user_id  = $input['assign_to'];
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
	        $model->activity_type_id  = $input['actvity_type'];
	        $model->due_date  = date('Y-m-d',strtotime($input['due_date']));
	        $model->summary  = $input['summary'];
	        $model->details  = $long_details;
	        $model->status  = $status;
	        $model->update();
    	}
    	elseif($input['action'] == "Add") {
    	    $model = new ScheduleActivities();
	        $model->schedule_user_id  = Hashids::decode($input['log_user_id'])[0];
	        $model->assign_user_id  = $input['assign_to'];
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
	        $model->activity_type_id  = $input['actvity_type'];
	        $model->due_date  = date('Y-m-d',strtotime($input['due_date']));
	        $model->summary  = $input['summary'];
	        $model->details  = $input['details'];
	        $model->save();
            if($input['actvity_type'] == 1){
                $user = Admin::where('id',$input['assign_to'])->first();
                $name = $user->firstname;
                $email = $user->email;
                $link = '';
                if($module == "vouchers"){
                    $link = route('admin.', $input['schedule_model_id']);
                }
                else if($module == "quotations") {
                    $link = route('admin.quotations.edit', $input['schedule_model_id']);
                }
                else if($module == "contacts") {
                    $link = route('admin.contacts.edit', $input['schedule_model_id']);
                }
                else if($module == "customers") {
                    $link = route('admin.contacts.edit', $input['schedule_model_id']);
                }
                else if($module == "products") {
                    $link = route('admin.products.edit', $input['schedule_model_id']);
                }
                else if($module == "productVariants") {
                    $link = route('admin.product-variant.edit', $input['schedule_model_id']);
                }
                else if($module == "saleTeams") {
                    $link = route('admin.sales-team.edit', $input['schedule_model_id']);
                }
                $this->send_schedule_activity_email($name, $email, $input['details'], $link );
            }

	    }
	    elseif($input['action'] == "Done") {
    	    $model = new ScheduleActivities();
	        $model->schedule_user_id  = Hashids::decode($input['log_user_id'])[0];
	        $model->assign_user_id  = $input['assign_to'];
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
	        $model->activity_type_id  = $input['actvity_type'];
	        $model->due_date  = date('Y-m-d',strtotime($input['due_date']));
	        $model->summary  = $input['summary'];
	        $model->details  = $input['details'];
	        $model->status  = 1;
	        $model->save();
	    }
	    else {
    	    $model = new ScheduleActivities();
	        $model->schedule_user_id  = Hashids::decode($input['log_user_id'])[0];
	        $model->assign_user_id  = $input['assign_to'];
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
	        $model->activity_type_id  = $input['actvity_type'];
	        $model->due_date  = date('Y-m-d',strtotime($input['due_date']));
	        $model->summary  = $input['summary'];
	        $model->details  = $input['details'];
	        $model->status  = 1;
	        $model->save();
	    }
        $contact_log_id = Contact::where('admin_id',$model->schedule_user_id)->select('id')->where('status', 1)->first();
        $follower_id = Contact::where('admin_id',$model->assign_user_id)->select('id')->where('status', 1)->first();
        // Delete If follower already exist
        $this->activities->delete_unfollow_records($contact_log_id->id, $follower_id->id, $input['module_type'], 2, $model_id);
        // Following Users
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
        $follower->contact_id = $contact_log_id->id;
        $follower->follower_id = $follower_id->id;
        $follower->module_type = $input['module_type'];
        $follower->follower_type = 2;
        $follower->save();
            // Is following the current user
        if($module == "kaspersky") {
            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('kss_subscription_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        else if($module == "vouchers") {
            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('voucher_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        else if($module == "quotations") {
            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('quotation_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        else if($module == "contacts") {
            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('contact_model_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        else if($module == "customers") {
                $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('customer_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        else if($module == "products") {
            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('product_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        else if($module == "productVariants") {
            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('variant_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        else if($module == "saleTeams") {
            $logged_in_follower_ids = Followers::where('contact_id', Hashids::decode($input['schedule_partner_id'])[0])->where('module_type', $input['module_type'])->where('sales_team_id', $model_id)->where('follower_type', 2)->pluck('follower_id')->toArray();
        }
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', Auth::user()->id)->exists()) {

            $is_following = 1;
        }
        else {

            $is_following = 0;
        }
        // Updated Schedule activity list
        $updated_schedule_activities = $this->activities->all_planned_activities($model_id, $module);
        // Updated Followers list and counter
        $updated_follower_list = $this->activities->updated_followers_list($model_id, Hashids::decode($input['schedule_partner_id'])[0], $input['module_type']);
        $follower_count = $this->activities->follower_counter($model_id, Hashids::decode($input['schedule_partner_id'])[0], $input['module_type']);
        // Updated Done Activities
        $doneActivitiesValArr = array();
        if(isset($input['schedule_flag']) && $input['schedule_flag'] <> 0) {
            if($module == "kaspersky") {
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('kss_subscription_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();
            }
            else if($module == "vouchers"){
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('voucher_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();
            }
            else if($module == "quotations") {
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('quotation_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();
            }
            else if($module == "contacts") {
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('contact_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();
            }
            else if($module == "customers") {
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('customer_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();
            }
            else if($module == "products") {
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('product_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();
            }
            else if($module == "productVariants") {
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('variant_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();

            }
            else if($module == "saleTeams") {
                $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('sales_team_id', $model_id)->where('id', $model->id)->where('status', 1)->orderBy('id','desc')->first();
            }
             $img = '<img class="img-circle" src="' . checkImage(asset("storage/uploads/admin/" . Hashids::encode($done_activities->schedule_by_users->id) . '/' . $done_activities->schedule_by_users->image),'avatar5.png') . '"  alt='.__('User Image').'>';
             $user_url = route('admin.admin-user.edit',['admin_user'=> Hashids::decode($input['log_user_id'])[0]]);
             $username = $done_activities->schedule_by_users->firstname .' '. $done_activities->schedule_by_users->lastname;
             $activity_type_name = $done_activities->activity_types->name;
             $assigned_user_name = $done_activities->assign_to_users->firstname .' '.$done_activities->assign_to_users->lastname;
            if(isset($done_activities->summary))
                $summary = ': <span>'.$done_activities->summary.'</span>';
            else
                $summary = '';
            if($done_activities->activity_type_id == 1)
               $activityIcon = "fa-envelope";
            elseif($done_activities->activity_type_id == 2)
               $activityIcon = "fa-tasks";
            elseif($done_activities->activity_type_id == 3)
               $activityIcon = "fa-phone";
            elseif($done_activities->activity_type_id == 4)
               $activityIcon = "fa-users";
            elseif($done_activities->activity_type_id == 5)
               $activityIcon = "fa-upload";
            if($done_activities->assign_user_id <> Auth::user()->id)
            $assigned_to = '<span>('.__('originally assigned to').' '.$assigned_user_name.')</span>';
            else
            $assigned_to = '';
            if(isset($done_activities->activity_feedback))
                $feedback ='<span class="description margin-bottom-15"><span>'.$done_activities->activity_feedback.'</span></span>';
            else
                $feedback = '';
            if(isset($done_activities->details))
                $details = '<span class="description"><b>'.__('Original note').':</b><br><span>'.$done_activities->details.'</span></span>';
            else
                $details = '';



            $doneActivitiesValArr = array(
                'sa_img' => $img,
                'sa_user_url' => $user_url,
                'sa_username' => $username,
                "sa_ago" => $model->updated_at->diffForHumans(),
                'sa_type_name' => $activity_type_name,
                'sa_type_icone' => $activityIcon,
                'sa_assigned_to' => $assigned_to,
                'sa_summary' => $summary,
                'sa_feedback' => $feedback,
                'sa_details' => $details,
            );
        }
        return response()->json([
            'updated_schedule_activities'=> $updated_schedule_activities,
            'doneActivitiesValArr' => $doneActivitiesValArr,
            'is_following' => $is_following,
            'follower_count' => $follower_count,
            'updated_follower_list' => $updated_follower_list
        ]);
    }
    public function updatesPlannedActivity(Request $request) {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $action = $input['action'];
        $model = ScheduleActivities::find($id)[0];
    	return response()->json([
		        	'model'=> $model,
		        	'action' => $action
		        ]);
    }
    public function cancelPlannedActivity(Request $request) {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $module = $input['cancel_module_name'];
        $model_id = Hashids::decode($input['cancel_model_id']);
        $model = ScheduleActivities::find($id)[0];
        $model->delete();
        $updated_schedule_activities = $this->activities->all_planned_activities($model_id, $module);
        return response()->json([
        	'cancelled' => __('Your planned activity has been cancelled successfully'),
        	'updated_schedule_activities'=> $updated_schedule_activities
        ]);
    }
      // Method For Log Message Remove File
    public function removeScheduleActivityFile(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id'])[0];
        $schedule_activity_id = Hashids::decode($input['log_id'])[0];
        $model_id = Hashids::decode($input['model_id'])[0];
        $module = $input['module_name'];
        $model = ActivityAttachments::where('id', $id)->where('schedule_activity_id', $schedule_activity_id)->first();
        $file = public_path() .'/storage/uploads/attachements/ScheduleActivites/'. $model->module_name . '/' . Hashids::encode($model->schedule_activity_id) . '/' . $model->file_name;
        File::delete($file);
        $model->delete();
        $attachments_count =  $this->activities->all_activity_attachements($model_id, $module)->count();
        $attachment_list =  $this->activities->updated_attachment_list($model_id, $module);
        return response()->json(['deleted' => __('Your file has been deleted successfully.'), "attachment_counter" => $attachments_count, 'attachment_list' => $attachment_list]);
    }

    public function donePlannedActivity(Request $request) {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $model_id = Hashids::decode($input['done_model_id'])[0];
        $module = $input['done_module_name'];
        $model = ScheduleActivities::find($id)[0];
        $model->status  = 1;
        $model->activity_feedback  = isset($input['activity_feedback']) ? $input['activity_feedback'] : null;
        $model->update();
        $updated_schedule_activities = $this->activities->all_planned_activities($model_id, $module);
        // Updated Done Activities
        if($module == "kaspersky") {
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('kss_subscription_id', $model_id)->where('id',$id)->where('status', 1)->orderBy('id','desc')->first();
        }
        else if($module == "vouchers"){
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('voucher_id', $model_id)->where('id',$id)->where('status', 1)->orderBy('id','desc')->first();

        }
        else if($module == "quotations") {
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('quotation_id', $model_id)->where('id',$id)->where('status', 1)->orderBy('id','desc')->first();
        }
        else if($module == "contacts") {
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('contact_id', $model_id)->where('id',$id)->where('status', 1)->orderBy('id','desc')->first();
        }
        else if($module == "customers") {
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('customer_id', $model_id)->where('id', $id)->where('status', 1)->orderBy('id','desc')->first();
        }
        else if($module == "products") {
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('product_id', $model_id)->where('id', $id)->where('status', 1)->orderBy('id','desc')->first();
        }
        else if($module == "productVariants") {
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('variant_id', $model_id)->where('id', $id)->where('status', 1)->orderBy('id','desc')->first();

        }
        else if($module == "saleTeams") {
            $done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('sales_team_id', $model_id)->where('id', $id)->where('status', 1)->orderBy('id','desc')->first();
        }
        // Done By File Upload Code Section
         // File Uploading
            $x =0;
            if($request->hasfile('files'))
             {
                // Make Directory
                $file_path = public_path() . '/storage/uploads/attachements/ScheduleActivites/' . $module .'/' . $input['id'];
                    if (!File::exists(public_path() . '/storage/uploads/attachements/ScheduleActivites/' . $module .'/'. $input['id'])) {
                        File::makeDirectory($file_path, 0777, true);
                }
                foreach($request->file('files') as $file)
                {
                    $fileName = time().rand(1,100).'.'.$file->getClientOriginalExtension();
                    $filePath = $file->storeAs('uploads/attachements/ScheduleActivites/'. $module .'/'. $input['id'], $fileName, 'public');
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
                    $insert[$x]['schedule_activity_id'] = Hashids::decode($input['id'])[0];
                    $insert[$x]['file_name'] = $fileName;
                    $insert[$x]['file_extension'] = $file->getClientOriginalExtension();
                    $insert[$x]['module_name'] = $module;
                    $insert[$x]['created_at'] = date('Y-m-d H:i:s');
                    $insert[$x]['updated_at'] = date('Y-m-d H:i:s');
                    $x++;
                }
                 ActivityAttachments::insert($insert);
            }
            $arr = [];
            $img_ext = ["jpg", "jpeg", "png", "gif","webp"];

            if($module == "kaspersky") {
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('kss_subscription_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            else if($module == "vouchers"){
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('voucher_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            else if($module == "quotations") {
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('quotation_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            else if($module == "contacts") {
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('contact_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            else if($module == "customers") {
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('customer_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            else if($module == "products") {
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('product_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            else if($module == "productVariants") {
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('variant_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            else if($module == "saleTeams") {
                $sa_user_details = ScheduleActivities::with('schedule_by_users','activity_attachments')->where('sales_team_id',$model_id)->orderBy('updated_at','desc')->first();
            }
            $attachments = ActivityAttachments::where('schedule_activity_id', Hashids::decode($input['id'])[0])->orderBy('id','desc')->get();
            foreach($sa_user_details->activity_attachments as $attachment) {
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
                 $remove_sa_file = "remove_sa_file_".Hashids::encode($attachment->id);
                 if(in_array($attachment->file_extension, $img_ext)) {
                   $attachment_html = '<span class="description" id="'.$remove_sa_file.'"><div class="customer-box" style="width: 410px;"><div class="customer-img"><img src="'.asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ).'" style="height: 100%"></div><div class="customer-content col-md-3"><h3 class="customer-heading">'.__('File Name').': '. $attachment->file_name .'</h3><span class="email">'.__('File Type').': '. $attachment->file_extension .'</span><a type="button" class="price" href="'.asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ).'" download>'.__('Download').'</a>&nbsp;<a href="javascript:void(0)" type="button" class="price" onclick="removeSaFile($(this))" data-sa-attachment-id="'. Hashids::encode($attachment->id).'"  data-log-sa-id ="'.Hashids::encode($attachment->schedule_activity_id).'" data-sa-file-name="'.$attachment->file_name.'" data-sa-file-url="'.route('admin.log.remove-sa-file').'" data-sa-model-id="' .Hashids::encode($attachment_model_id). '" data-sa-module-name ="' .$attachment->module_name.'"><i class="class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></div></div></span>';

                   array_push($arr,$attachment_html);
                 }
                 else {

                    $attachment_html = '<span class="description margin-bottom-15"  id="'.$remove_sa_file.'"><b>'.__('File').':</b><a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="'.asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ).'" download>'. $attachment->file_name .' &nbsp;<i class="fa fa-download"></i></a><a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="javascript:void(0)" onclick="removeSaFile($(this))" data-sa-attachment-id="'. Hashids::encode($attachment->id).'" data-log-sa-id ="'.Hashids::encode($attachment->schedule_activity_id).'" data-sa-file-name="'.$attachment->file_name.'" data-sa-file-url="'.route('admin.log.remove-sa-file').'" data-sa-model-id="' .Hashids::encode($attachment_model_id). '" data-sa-module-name ="' .$attachment->module_name.'"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></span>';

                    array_push($arr,$attachment_html);
                 }
            }
        $_html = implode(" ",$arr);
        // End By File Upload Code Section
         $img = '<img class="img-circle" src="' . checkImage(asset("storage/uploads/admin/" . Hashids::encode($done_activities->schedule_by_users->id) . '/' . $done_activities->schedule_by_users->image),'avatar5.png') . '"  alt='.__('User Image').'>';
        $user_url = route('admin.admin-user.edit',['admin_user'=> Hashids::encode($model->schedule_user_id)]);
        $username = $done_activities->schedule_by_users->firstname .' '. $done_activities->schedule_by_users->lastname;
        $attachments =  $this->activities->all_activity_attachements($model_id, $module);
        $attachment_list =  $this->activities->updated_attachment_list($model_id, $module);
        $activity_type_name = $done_activities->activity_types->name;
        $assigned_user_name = $done_activities->assign_to_users->firstname .' '.$done_activities->assign_to_users->lastname;
        if(isset($done_activities->summary))
            $summary = ': <span>'.$done_activities->summary.'</span>';
        else
            $summary = '';
        if($done_activities->activity_type_id == 1)
           $activityIcon = "fa-envelope";
        elseif($done_activities->activity_type_id == 2)
           $activityIcon = "fa-tasks";
        elseif($done_activities->activity_type_id == 3)
           $activityIcon = "fa-phone";
        elseif($done_activities->activity_type_id == 4)
           $activityIcon = "fa-users";
        elseif($done_activities->activity_type_id == 5)
           $activityIcon = "fa-upload";
        if($done_activities->assign_user_id <> Auth::user()->id)
        $assigned_to = '<span>('.__('originally assigned to').' '.$assigned_user_name.')</span>';
        else
        $assigned_to = '';
        if(isset($done_activities->activity_feedback))
            $feedback ='<span class="description margin-bottom-15"><span>'.$done_activities->activity_feedback.'</span></span>';
        else
            $feedback = '';
        if(isset($done_activities->details))
            $details = '<span class="description"><b>'.__('Original note').':</b><br><span>'.$done_activities->details.'</span></span>';
        else
            $details = '';
        return response()->json([
        	'mark_as_done' => __('Your planned activity has been mark as done successfully'),
            'sa_img' => $img,
            'sa_user_url' => $user_url,
            'sa_username' => $username,
            "sa_ago" => $model->updated_at->diffForHumans(),
            'sa_type_name' => $activity_type_name,
            'sa_type_icone' => $activityIcon,
            'sa_assigned_to' => $assigned_to,
            'sa_summary' => $summary,
            'sa_feedback' => $feedback,
            'sa_details' => $details,
        	'updated_schedule_activities'=> $updated_schedule_activities,
            'attachments' => $_html,
            "attachment_counter" => $attachments->count(),
            "attachment_list" => $attachment_list
        ]);
    }

    public function send_schedule_activity_email($name, $email, $activity_content, $link ){
        $email_template = EmailTemplate::where('type','schedule_activity_email')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{content}}","{{link}}","{{app_name}}");
        $replace = array($name,$activity_content, $link,env('APP_NAME'));
        $econtent = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\SendScheduleActivityEmailJob($email,$subject,$econtent));
    }
}
