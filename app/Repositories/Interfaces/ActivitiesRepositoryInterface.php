<?php

namespace App\Repositories\Interfaces;


interface ActivitiesRepositoryInterface
{
    public function updated_followers_list($model_id, $contact_id, $module_type);
    public function follower_counter($model_id, $contact_id, $module_type);
    public function delete_unfollow_records($logged_user_id, $partner_id, $module_type, $follower_type, $model_id);
    public function delete_unfollow_back_records($logged_user_id, $partner_id, $module_type, $model_id);
    public function all_activity_attachements($model_id, $module);
    public function updated_attachment_list($model_id, $module);
    public function all_planned_activities($model_id, $module);
}

?>
