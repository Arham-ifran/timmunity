<?php

namespace App\Repositories\Interfaces;


interface PartialViewsRepositoryInterface
{
    public function sendMsgs($model_id, $log_uid, $module, $partner, $recipients, $module_type, $partner_id);
    public function logNotes($model_id, $log_uid, $module, $partner);
    public function schedualActivities($model_id, $schedule_user_log_id, $module, $schedule_users, $schedule_activity_types, $partner_id, $module_type);
    public function notesTabPartialView($log_notes);
    public function sendMsgTabPartialView($send_messages);
    public function schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module);
    public function attachmentsPartialView($attachments);
    public function follower_list($model_id, $contact_id, $module_type);

}

?>
