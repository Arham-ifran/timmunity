<?php

namespace App\Repositories;
use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use App\Models\Followers;
use App\Http\Requests;
use Hashids;
use Session;
use View;

class PartialViewsRepositories implements PartialViewsRepositoryInterface
{
    public function sendMsgs($model_id, $log_uid, $module, $partner, $recipients, $module_type, $partner_id){
        return View::make('admin.activities.send_messages', [
            'model_id' => $model_id,
            'log_uid' => $log_uid,
            'module' => $module,
            'partner' => $partner,
            'recipients' => $recipients,
            'module_type' => $module_type,
            'partner_id' =>$partner_id
        ])->render();
    }

    public function logNotes($model_id, $log_uid, $module, $partner){
       return View::make('admin.activities.log_notes', [
            'model_id' => $model_id,
            'log_uid' => $log_uid,
            'module' => $module,
            'partner' => $partner
        ])->render();
    }

    public function schedualActivities($model_id, $schedule_user_log_id, $module, $schedule_users, $schedule_activity_types, $partner_id, $module_type){
      return View::make('admin.activities.schedule_activities', [
            'model_id' => $model_id,
            'schedule_user_log_id' => $schedule_user_log_id,
            'module' => $module,
            'schedule_users' => $schedule_users,
            'schedule_activity_types' => $schedule_activity_types,
            'partner_id' => $partner_id,
            'module_type' => $module_type
        ])->render();
    }

    public function notesTabPartialView($log_notes){
      return View::make('admin.activities.notes_tab_partial_view', [
            'log_notes' => $log_notes,
        ])->render();
    }

    public function sendMsgTabPartialView($send_messages){
      return View::make('admin.activities.send_msg_tab_partial_view', [
            'send_messages' => $send_messages,
        ])->render();
    }
    public function schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module){
      return View::make('admin.activities.schedule_activity_tab_partial_view', [
            'schedule_activities' => $schedule_activities,
            'scheduled_done_activities' => $scheduled_done_activities,
            'module' => $module
        ])->render();
    }
    public function attachmentsPartialView($attachments){
      return View::make('admin.activities.attachments_partial_view', [
            'attachments' => $attachments,
        ])->render();
    }
    public function follower_list($model_id ,$contact_id, $module_type)
    {
        if($module_type == 0) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('kss_subscription_id', $model_id)->where('module_type', $module_type)->get();
        }
        else if($module_type == 1) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('voucher_id', $model_id)->where('module_type', $module_type)->get();
        }
        else if($module_type == 2) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('quotation_id', $model_id)->where('module_type', $module_type)->get();
        }
        else if($module_type == 3) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('contact_model_id', $model_id)->where('module_type', $module_type)->get();
        }
        else if($module_type == 4) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('customer_id', $model_id)->where('module_type', $module_type)->get();
        }
        else if($module_type == 5) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('product_id', $model_id)->where('module_type', $module_type)->get();
        }
        else if($module_type == 6) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('variant_id', $model_id)->where('module_type', $module_type)->get();
        }
        else if($module_type == 7) {
          return Followers::with('contacts')->where('contact_id', $contact_id)->where('sales_team_id', $model_id)->where('module_type', $module_type)->get();
        }
    }
}
