<?php

namespace App\Repositories;
use App\Repositories\Interfaces\ActivitiesRepositoryInterface;
use App\Models\ActivityAttachments;
use App\Models\ScheduleActivities;
use App\Models\Followers;
use App\Http\Requests;
use Carbon\Carbon;
use Hashids;

class ActivityRepositories implements ActivitiesRepositoryInterface
{

    public function updated_followers_list($model_id, $contact_id, $module_type)
    {
      if($module_type == 0)
      {
        $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('kss_subscription_id',$model_id)->where('module_type', $module_type)->get();
      }
      else if($module_type == 1) {
         $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('voucher_id',$model_id)->where('module_type', $module_type)->get();
      }
      else if($module_type == 2) {
         $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('quotation_id',$model_id)->where('module_type', $module_type)->get();
      }
      else if($module_type == 3) {
         $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('contact_model_id',$model_id)->where('module_type', $module_type)->get();
      }
      else if($module_type == 4) {
         $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('customer_id',$model_id)->where('module_type', $module_type)->get();
      }
      else if($module_type == 5) {
         $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('product_id',$model_id)->where('module_type', $module_type)->get();
      }
      else if($module_type == 6) {
         $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('variant_id',$model_id)->where('module_type', $module_type)->get();
      }
      else if($module_type == 7) {
         $follower_lists = Followers::with('contacts')->where('contact_id', $contact_id)->where('sales_team_id',$model_id)->where('module_type', $module_type)->get();
      }
        $follower_arr = [];
		foreach ($follower_lists as $row) {
	      	$list = '<li data-follower-id="'.Hashids::encode($row->contacts->id).'"><a href="'.route('admin.contacts.edit',['contact'=> Hashids::encode($row->contacts->id)]).'" target="_blank">"'.$row->contacts->name.'"</a></li>';
	      	array_push($follower_arr,$list);
	      }
	     $updated_follower_list = implode(' ', $follower_arr);
	     return $updated_follower_list;
    }
     public function follower_counter($model_id, $contact_id, $module_type)
    {
      if($module_type == 0)
      {
        return Followers::with('contacts')->where('contact_id', $contact_id)->where('kss_subscription_id',$model_id)->where('module_type', $module_type)->count();
      }
      else if($module_type == 1) {
         return Followers::with('contacts')->where('contact_id', $contact_id)->where('voucher_id',$model_id)->where('module_type', $module_type)->count();
      }
      else if($module_type == 2) {
         return Followers::with('contacts')->where('contact_id', $contact_id)->where('quotation_id',$model_id)->where('module_type', $module_type)->count();
      }
      else if($module_type == 3) {
         return Followers::with('contacts')->where('contact_id', $contact_id)->where('contact_model_id',$model_id)->where('module_type', $module_type)->count();
      }
      else if($module_type == 4) {
         return Followers::with('contacts')->where('contact_id', $contact_id)->where('customer_id',$model_id)->where('module_type', $module_type)->count();
      }
      else if($module_type == 5) {
         return Followers::with('contacts')->where('contact_id', $contact_id)->where('product_id',$model_id)->where('module_type', $module_type)->count();
      }
      else if($module_type == 6) {
         return Followers::with('contacts')->where('contact_id', $contact_id)->where('variant_id',$model_id)->where('module_type', $module_type)->count();
      }
      else if($module_type == 7) {
         return Followers::with('contacts')->where('contact_id', $contact_id)->where('sales_team_id',$model_id)->where('module_type', $module_type)->count();
      }

    }
     public function delete_unfollow_records($logged_user_id, $partner_id, $module_type, $follower_type, $model_id)
    {
      if($module_type == 0) {
        return Followers::where('contact_id', $logged_user_id)
        ->where('kss_subscription_id',$model_id)
        ->where('follower_id', $partner_id)
        ->where('module_type', $module_type)
        ->where('follower_type', $follower_type)
        ->delete();
      }
      else if($module_type == 1) {
        return Followers::where('contact_id', $logged_user_id)
        ->where('voucher_id',$model_id)
        ->where('follower_id', $partner_id)
        ->where('module_type', $module_type)
        ->where('follower_type', $follower_type)
        ->delete();
      }
      else if($module_type == 2) {
        return Followers::where('contact_id', $logged_user_id)
        ->where('quotation_id',$model_id)
        ->where('follower_id', $partner_id)
        ->where('module_type', $module_type)
        ->where('follower_type', $follower_type)
        ->delete();
      }
      else if($module_type == 3) {
        return Followers::where('contact_id', $logged_user_id)
          ->where('contact_model_id',$model_id)
          ->where('follower_id', $partner_id)
          ->where('module_type', $module_type)
          ->where('follower_type', $follower_type)
          ->delete();
      }
      else if($module_type == 4) {
        return Followers::where('contact_id', $logged_user_id)
          ->where('customer_id',$model_id)
          ->where('follower_id', $partner_id)
          ->where('module_type', $module_type)
          ->where('follower_type', $follower_type)
          ->delete();
      }
      else if($module_type == 5) {
        return Followers::where('contact_id', $logged_user_id)
          ->where('product_id',$model_id)
          ->where('follower_id', $partner_id)
          ->where('module_type', $module_type)
          ->where('follower_type', $follower_type)
          ->delete();
      }
      else if($module_type == 6) {
        return Followers::where('contact_id', $logged_user_id)
          ->where('variant_id',$model_id)
          ->where('follower_id', $partner_id)
          ->where('module_type', $module_type)
          ->where('follower_type', $follower_type)
          ->delete();
      }
      else if($module_type == 7) {
        return Followers::where('contact_id', $logged_user_id)
          ->where('sales_team_id',$model_id)
          ->where('follower_id', $partner_id)
          ->where('module_type', $module_type)
          ->where('follower_type', $follower_type)
          ->delete();
      }

    }
     public function delete_unfollow_back_records($logged_user_id, $partner_id, $module_type, $model_id)
    {
      if($module_type == 0){
        return Followers::where('contact_id', $partner_id)
        ->where('kss_subscription_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
      else if( $module_type == 1) {
        return Followers::where('contact_id', $partner_id)
        ->where('voucher_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
      else if( $module_type == 2) {
        return Followers::where('contact_id', $partner_id)
        ->where('quotation_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
      else if( $module_type == 3){
        return Followers::where('contact_id', $partner_id)
        ->where('contact_model_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
      else if( $module_type == 4){
        return Followers::where('contact_id', $partner_id)
        ->where('customer_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
      else if( $module_type == 5){
        return Followers::where('contact_id', $partner_id)
        ->where('product_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
      else if( $module_type == 6){
        return Followers::where('contact_id', $partner_id)
        ->where('variant_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
      else if( $module_type == 7){
        return Followers::where('contact_id', $partner_id)
        ->where('sales_team_id',$model_id)
        ->where('follower_id', $logged_user_id)
        ->where('module_type', $module_type)
        ->where('follower_type', 2)
        ->delete();
      }
    }
     public function all_activity_attachements($model_id, $module)
    {
      if($module == "kaspersky") {
          $all_attachments = ActivityAttachments::where('kss_subscription_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      else if($module == "vouchers"){
        $all_attachments = ActivityAttachments::where('voucher_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();

      }
      else if($module == "quotations") {
        $all_attachments = ActivityAttachments::where('quotation_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();

      }
      else if($module == "contacts") {
        $all_attachments = ActivityAttachments::where('contact_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();

      }
      else if($module == "customers") {
           $all_attachments = ActivityAttachments::where('customer_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      else if($module == "products") {
          $all_attachments = ActivityAttachments::where('product_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      else if($module == "productVariants") {
          $all_attachments = ActivityAttachments::where('variant_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      else if($module == "saleTeams") {
          $all_attachments = ActivityAttachments::where('sales_team_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      return $all_attachments;

    }

     public function updated_attachment_list($model_id, $module)
    {
       if($module == "kaspersky") {
          $attachments =  ActivityAttachments::where('kss_subscription_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->get();
      }
      else if($module == "vouchers"){
        $attachments =  ActivityAttachments::where('voucher_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();

      }
      else if($module == "quotations") {
        $attachments =  ActivityAttachments::where('quotation_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();

      }
      else if($module == "contacts") {
        $attachments =  ActivityAttachments::where('contact_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();

      }
      else if($module == "customers") {
          $attachments =  ActivityAttachments::where('customer_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      else if($module == "products") {
          $attachments =  ActivityAttachments::where('product_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      else if($module == "productVariants") {
          $attachments =  ActivityAttachments::where('variant_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
      else if($module == "saleTeams") {
          $attachments =  ActivityAttachments::where('sales_team_id', $model_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
      }
       $attachment_arr = [];
       $img_ext = array("jpg", "jpeg", "png", "gif","webp");
       $x=0;
       $y= 0;
       $z = 0;
       if($attachments->count() > 0) {
       foreach ($attachments as $attachment) {
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
           if($attachment->log_note_id == null && $attachment->schedule_activity_id == null) {
              if($x == 0) {
                  $attachment_heading = '<li class="attachment_heading"><strong>'.__('Send Message').'</strong></li>';
                  array_push($attachment_arr, $attachment_heading);
                  }

                 if(in_array($attachment->file_extension, $img_ext)) {
                $attachment_list = '<li><span class="description" id="remove_msg_attachment_' . Hashids::encode($attachment->id) . '"><div class="customer-box"><div class="customer-img"><img src="' . asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ) . '" style="height: 85%;width:85%"></div><div class="customer-content col-md-3"><h3 class="customer-heading">'.__('File Name').': ' . $attachment->file_name . '</h3><span class="email">'.__('File Type').': ' . $attachment->file_extension . '</span><a type="button" class="icons" href="' . asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ) . '" download><i class="fa fa-download"></i>&nbsp;'.__('Download').'</a><a href="javascript:void(0)" type="button" class="icons" onclick="removeMsgFile($(this))" data-msg-attachment-id="' . Hashids::encode($attachment->id) . '"  data-log-msg-id ="' . Hashids::encode($attachment->send_msg_id) . '" data-msg-file-name="' . $attachment->file_name . '" data-msg-file-url="' . route('admin.log.remove-msg-file') . '" data-msg-model-id="' .Hashids::encode($attachment_model_id). '" data-msg-module-name ="' .$attachment->module_name. '"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></div></div></span></li>';
                  }
                  else {
                $attachment_list = '<li><span class="description margin-left-15" id="remove_msg_attachment_'. Hashids::encode($attachment->id) . '"><b>'.__('File').':</b><a class="attachment_file_list" href="' . asset('storage/uploads/attachements/SendMessage/'. $attachment->module_name . '/' . Hashids::encode($attachment->send_msg_id) . '/' . $attachment->file_name ) . '" download>' . $attachment->file_name . ' &nbsp;<i class="fa fa-download"></i></a><a href="javascript:void(0)" class=" attachment_file_list btn ml-2" onclick="removeMsgFile($(this))" data-msg-attachment-id ="' . Hashids::encode($attachment->id) . '" data-log-msg-id ="' . Hashids::encode($attachment->send_msg_id) . '" data-msg-file-name="' . $attachment->file_name . '" data-msg-file-url="' . route('admin.log.remove-msg-file') . '" data-msg-model-id="' .Hashids::encode($attachment_model_id). '" data-msg-module-name ="' .$attachment->module_name. '"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></span></li>';
                }
                  array_push($attachment_arr,$attachment_list);
              $x++;

              }
              else if($attachment->send_msg_id == null && $attachment->schedule_activity_id == null) {
              if($y == 0) {
                $attachment_heading= '<li class="attachment_heading"><strong>'.__('Log Note').'</strong></li>';
                 array_push($attachment_arr, $attachment_heading);
               }
                 if(in_array($attachment->file_extension, $img_ext)) {
                  $attachment_list = '<li><span class="description" id="remove_note_attachment_' . Hashids::encode($attachment->id) . '"><div class="customer-box"><div class="customer-img"><img src="' . asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) . '" style="height: 85%;width:85%"></div><div class="customer-content col-md-3"><h3 class="customer-heading">'.__('File Name').': ' . $attachment->file_name . '</h3><span class="email">'.__('File Type').': ' . $attachment->file_extension . '</span><a type="button" class="icons" href="' . asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) . '" download><i class="fa fa-download"></i>&nbsp;'.__('Download').'</a><a href="javascript:void(0)" type="button" class="icons" onclick="removeNoteFile($(this))" data-note-attachment-id="' . Hashids::encode($attachment->id) . '"  data-log-note-id ="' . Hashids::encode($attachment->log_note_id) . '" data-note-file-name="' . $attachment->file_name . '" data-note-file-url="' . route('admin.log.remove-note-file') . '" data-note-model-id="' .Hashids::encode($attachment_model_id). '" data-note-module-name ="' .$attachment->module_name. '"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></div>
                    </div></span></li>';
                  }
                  else {
                  $attachment_list = '<li><span class="description margin-left-15" id="remove_note_attachment_' . Hashids::encode($attachment->id) . '"><b>'.__('File').':</b><a class="attachment_file_list" href="' . asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ) . '" download>' . $attachment->file_name. ' &nbsp;<i class="fa fa-download"></i></a><a href="javascript:void(0)" class="attachment_file_list btn ml-2" onclick="removeNoteFile($(this))" data-note-attachment-id ="' . Hashids::encode($attachment->id) . '" data-log-note-id ="' . Hashids::encode($attachment->log_note_id) . '" data-note-file-name="' . $attachment->file_name . '" data-note-file-url="' . route('admin.log.remove-note-file') . '" data-note-model-id="' .Hashids::encode($attachment_model_id). '" data-note-module-name ="' .$attachment->module_name. '"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></span></li>';
                }
                 array_push($attachment_arr,$attachment_list);
              $y++;

             }
             else if($attachment->log_note_id == null && $attachment->send_msg_id == null) {
              if($z == 0) {
                  $attachment_heading = '<li class="attachment_heading"><strong>'.__('Schedule Actvities').'</strong></li>';
                  array_push($attachment_arr, $attachment_heading);
                  }

                 if(in_array($attachment->file_extension, $img_ext)) {
                $attachment_list = '<li><span class="description" id="remove_sa_attachment_' . Hashids::encode($attachment->id) . '"><div class="customer-box"><div class="customer-img"><img src="' . asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ) . '" style="height: 85%;width:85%"></div><div class="customer-content col-md-3"><h3 class="customer-heading">'.__('File Name').': ' . $attachment->file_name . '</h3><span class="email">'.__('File Type').': ' . $attachment->file_extension . '</span><a type="button" class="icons" href="' . asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ) . '" download><i class="fa fa-download"></i>&nbsp;'.__('Download').'</a><a href="javascript:void(0)" type="button" class="icons" onclick="removeSaFile($(this))" data-sa-attachment-id="' . Hashids::encode($attachment->id) . '"  data-log-sa-id ="' . Hashids::encode($attachment->schedule_activity_id) . '" data-sa-file-name="' . $attachment->file_name . '" data-sa-file-url="' . route('admin.log.remove-sa-file') . '" data-sa-model-id="' .Hashids::encode($attachment_model_id). '" data-sa-module-name ="' .$attachment->module_name. '"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></div></div></span></li>';
                  }
                  else {
                $attachment_list = '<li><span class="description margin-left-15" id="remove_sa_attachment_'. Hashids::encode($attachment->id) . '"><b>'.__('File').':</b><a class="attachment_file_list" href="' . asset('storage/uploads/attachements/ScheduleActivites/'. $attachment->module_name . '/' . Hashids::encode($attachment->schedule_activity_id) . '/' . $attachment->file_name ) . '" download>' . $attachment->file_name . ' &nbsp;<i class="fa fa-download"></i></a><a href="javascript:void(0)" class=" attachment_file_list btn ml-2" onclick="removeSaFile($(this))" data-sa-attachment-id ="' . Hashids::encode($attachment->id) . '" data-log-sa-id ="' . Hashids::encode($attachment->schedule_activity_id) . '" data-sa-file-name="' . $attachment->file_name . '" data-sa-file-url="' . route('admin.log.remove-sa-file') . '" data-sa-model-id="' .Hashids::encode($attachment_model_id). '" data-sa-module-name ="' .$attachment->module_name. '"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></span></li>';
                }
                  array_push($attachment_arr,$attachment_list);
              $z++;

              }

        }

        $updated_attachment_list = implode(' ', $attachment_arr);
    }
    else {

        $attachement_list = "<li class='empty_attachment_list'><div class='text-center'>Currently there's no attachments</div></li>";
        $updated_attachment_list = $attachement_list;
    }

         return $updated_attachment_list;
    }

    public function all_planned_activities($model_id, $module)
    {
      if($module == "kaspersky") {
          $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('kss_subscription_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      else if($module == "vouchers"){
          $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('voucher_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      else if($module == "quotations") {
          $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('quotation_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      else if($module == "contacts") {
          $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('contact_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      else if($module == "customers") {
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('customer_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      else if($module == "products") {
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('product_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      else if($module == "productVariants") {
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('variant_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      else if($module == "saleTeams") {
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('sales_team_id', $model_id)->where('status', 0)->orderBy('due_date','asc')->get();
      }
      $activities_arr = [];
      $i = 0;
      foreach($schedule_activities as $activity) {

          $due_date = Carbon::parse($activity->due_date);
          $now = Carbon::now();
          $current_date = $now->toDateString();
          $diffDays = $due_date->diffInDays($current_date);
            if($diffDays == 0){
              $date =__('Today');
              $bgClr = "bg-warning-full";
              $dueDateTextClr = "t-today";
            }
            elseif($diffDays == 1 && $due_date < $current_date)  {
              $date =__('Yesterday');
              $bgClr = "bg-danger-full";
              $dueDateTextClr = "t-overdue";
            }
            elseif($diffDays > 1  && $due_date < $current_date){
              $date =$diffDays.' '.__('days overdue');
              $bgClr = "bg-danger-full";
              $dueDateTextClr = "t-overdue";
            }
            elseif($diffDays == 1 && $due_date > $current_date) {
              $date ="Tommorrow";
              $bgClr = "bg-success-full";
              $dueDateTextClr = "t-planned";
            }
            elseif($diffDays > 1  && $due_date > $current_date) {
              $date = __('Due in').' '.$diffDays.' '.__('days');
              $bgClr = "bg-success-full";
              $dueDateTextClr = "t-planned";
            }
            // Fetch model ID by module name
            if($module == "kaspersky") {
               $schedule_model_id = $activity->kss_subscription_id;
            }
            else if($module == "vouchers"){
                $schedule_model_id = $activity->voucher_id;
            }
            else if($module == "quotations") {
                $schedule_model_id = $activity->quotation_id;
            }
            else if($module == "contacts") {
                $schedule_model_id = $activity->contact_id;
            }
            else if($module == "customers") {
                $schedule_model_id = $activity->customer_id;
            }
            else if($module == "products") {
                $schedule_model_id = $activity->product_id;
            }
            else if($module == "productVariants") {
                $schedule_model_id = $activity->variant_id;
            }
            else if($module == "saleTeams") {
                $schedule_model_id = $activity->sales_team_id;
            }
            // Activity Icon by activity types
            if($activity->activity_type_id == 1)
            $activityIcon = "fa-envelope";
            elseif($activity->activity_type_id == 2)
            $activityIcon = "fa-tasks";
            elseif($activity->activity_type_id == 3)
            $activityIcon = "fa-phone";
            elseif($activity->activity_type_id == 4)
            $activityIcon = "fa-users";
            else
            $activityIcon = "fa-upload";
          if ($activity->summary == null)
             $summary = $activity->activity_types->name;
          else
             $summary = $activity->summary;
          if($i == 0) {
            $planned_activities = '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"><div class="panel panel-default"><div class="panel-heading" role="tab" id="headingOne"><h4 class="panel-title"><a role="button" onclick="togglePlannedActivity()" data-toggle="collapse" data-parent="#accordion" href="#collapseActivities" aria-expanded="true" aria-controls="collapseOne">'.__('Planned Activities').'&nbsp;<span id="schedule_activites_counters" style="display: none"><span class="badge badge-danger" id="count_overdue"></span>&nbsp;<span class="badge badge-warning" id="count_today"></span>&nbsp;<span class="badge badge-success" id="count_planned"></span></span></a></h4></div><div id="collapseActivities" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne"><div class="panel-body" id="planned_activities">';
             array_push($activities_arr,$planned_activities);
           }
            if($activity->activity_type_id != 5) {
              $mark_as_done = '<a title="Mark Done" class="t_Activity_toolButton t_Activity_markDoneButton" onclick="showPopover()" data-toggle="popover" data-placement="right" ><i class="fa fa-check"></i> '.__('Mark Done').' </a>';
            }
            else {
              $mark_as_done = '<a title="'.__('Upload Document').'" class="t_Activity_toolButton t_Activity_markDoneButton"><input type="file" name="files[]" id="files" class="inputfile" onchange="uploadScheduleFile(this)" data-schedule-id = "' .Hashids::encode( $activity->id). '" data-schedule-model-id = "' .Hashids::encode($schedule_model_id). '" data-schedule-module-name = "' .$module. '" data-schedule-url ="' .route('admin.schedule.done.activity'). '" data-action ="Done" multiple/>
              <label for="files"><i class="fa fa-upload"></i>&nbsp;'.__('Upload Document').'</label></a>';
            }
          $planned_activities = '<div class="t_Activity t_ActivityBox_activity"><div class="t_Activity_sidebar"><div class="t_Activity_user"><img class="t_Activity_userAvatar" src="' . checkImage(asset('storage/uploads/admin/' . Hashids::encode($activity->schedule_by_users->id) . '/' . $activity->schedule_by_users->image),'avatar5.png') . '" alt='.__('User Image').'><div class="t_Activity_iconContainer '.$bgClr.'"><i class="t_Activity_icon fa '.$activityIcon.'"></i></div></div></div><div class="t_Activity_core"><!-- Activity Info --><div class="t_Activity_info"><div class="t_Activity_dueDateText '.$dueDateTextClr.'">' .$date. ':</div><div class="t_Activity_summary">“' .$summary. '”</div><div class="t_Activity_userName">'.__('for').' '.$activity->assign_to_users->firstname .' '. $activity->assign_to_users->lastname. '</div><a role="button" class="t_Activity_detailsButton btn btn-link" onclick="showActivityDetail(this)" data-schedule-id="'. Hashids::encode($activity->id). '"><i role="img" title="Info" class="fa fa-info-circle"></i></a></div><!-- Activity Details --><div class="t_Activity_details" id="t_activity_details_'.Hashids::encode( $activity->id). '"><dl class="dl-horizontal"><dt>'.__('Activity type').'</dt><dd class="t_Activity_type">' .$activity->activity_types->name. '</dd><dt>'.__('Created').'</dt><dd class="t_Activity_detailsCreation">'.date('m/d/Y h:i:s A',strtotime($activity->created_at)). '<img src="' .checkImage(asset('storage/uploads/admin/' . Hashids::encode($activity->schedule_by_users->id) . '/' . $activity->schedule_by_users->image),'avatar5.png'). '" title="' .$activity->schedule_by_users->firstname .' '. $activity->schedule_by_users->lastname. '" alt="' .$activity->schedule_by_users->firstname .' '. $activity->schedule_by_users->lastname. '" class="t_Activity_detailsUserAvatar t_Activity_detailsCreatorAvatar"><span class="t_Activity_detailsCreator">' .$activity->schedule_by_users->firstname .' '. $activity->schedule_by_users->lastname. '</span></dd><dt>'.__('Assigned to').'</dt><dd class="t_Activity_detailsAssignation"><img src="' .checkImage(asset('storage/uploads/admin/' . Hashids::encode($activity->assign_to_users->id) . '/' . $activity->assign_to_users->image),'avatar5.png'). '" title="' .$activity->assign_to_users->firstname .' '. $activity->assign_to_users->lastname. '" alt="' .$activity->assign_to_users->firstname .' '. $activity->assign_to_users->lastname. '" class="t_Activity_detailsUserAvatar t_Activity_detailsAssignationUserAvatar"> ' .$activity->assign_to_users->firstname .' '. $activity->assign_to_users->lastname. '</dd><dt>'.__('Due on').'</dt><dd class="t_Activity_detailsDueDate"><span class="t_Activity_deadlineDateText '.$dueDateTextClr.'">'.date('m/d/Y',strtotime($activity->due_date)). '</span></dd></dl></div><!-- Activity Description  --><div class="t_Activity_note"><p>' .strip_tags($activity->details). '<br></p></div><!-- Activity Tools --><div name="tools" class="t_Activity_tools"><div class="popover-markup">'.$mark_as_done.'<div class="head hide"><span style="color: #ea5959;">'.__('Mark Done').'</span></div><div class="content hide"><textarea id="activity_feedback_'.Hashids::encode( $activity->id).'" name="activity_feedback_'.Hashids::encode( $activity->id).'" rows="4" cols="40" placeholder="Write Feedback"></textarea></div><div class="footer hide"><a type="submit" class="skin-green-light-btn btn" onclick="markAsDone(this)" data-schedule-id = "' .Hashids::encode($activity->id). '" data-schedule-model-id = "' .Hashids::encode($schedule_model_id). '" data-schedule-module-name = "' .$module. '" data-mark-as-done-url ="' .route('admin.schedule.done.activity'). '"  data-action ="DoneAndNext">'.__('Done & Schedule Next').'</a>&nbsp;<a type="submit" class="skin-green-light-btn btn" onclick="markAsDone(this)" data-schedule-id = "' .Hashids::encode( $activity->id). '" data-schedule-model-id = "' .Hashids::encode($schedule_model_id). '" data-schedule-module-name = "' .$module. '" data-mark-as-done-url ="' .route('admin.schedule.done.activity'). '" data-action ="Done">'.__('Done').'</a><a style="border-bottom: 2px solid #009a71;padding: 0;" class="btn ml-2" href="javascript:void(0)" onclick="hidePopover(event)">'.__('Discard').'</a></div></div><a class="t_Activity_toolButton t_Activity_editButton btn btn-link" onclick="updateActivitySchedule(this)" data-schedule-id = "' .Hashids::encode( $activity->id). '" data-update-schedule-url ="' .route('admin.schedule.update.activity'). '"><i class="fa fa-pencil"></i> '.__('Edit').' </a><a class="t_Activity_toolButton t_Activity_cancelButton btn btn-link" onclick="cancelPlannedActivity(this)" data-schedule-id = "' .Hashids::encode( $activity->id). '" data-schedule-model-id = "' .Hashids::encode($schedule_model_id). '" data-schedule-module-name = "' .$module. '" data-summary = "' .$activity->summary. '" data-cancel-schedule-url ="' .route('admin.schedule.cancel.activity'). '"><i class="fa fa-times"></i> '.__('Cancel').' </a></div></div></div>';
          array_push($activities_arr,$planned_activities);
          $i++;
        }
        if($i == 0) {
         $planned_activities = '</div></div></div></div>';
         array_push($activities_arr,$planned_activities);
        }

        $updated_planned_activities = implode(' ', $activities_arr);
        return $updated_planned_activities;
    }
}
