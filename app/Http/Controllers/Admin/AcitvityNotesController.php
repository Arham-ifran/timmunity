<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\ActivitiesRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLogNotes;
use App\Models\ActivityAttachments;
use File;
use Hashids;

class AcitvityNotesController extends Controller
{ 
/**
     * @var activities.
     */
    protected $activities;
    /**
     * AcitvityNotesController Constructor.
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
     public function addLogNote(Request $request)
    {
            $input = $request->all();   
            $module = $input['module']; 
            $model_id =  Hashids::decode($input['model_id'])[0];
	        $model = new ActivityLogNotes();
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
	        $model->note = $input['note'];
	        $model->save();
	        // Make Directory 
	        $file_path = public_path() . '/storage/uploads/attachements/LogNote/' . $input['module'] .'/' . Hashids::encode($model->id);
			    if (!File::exists(public_path() . '/storage/uploads/attachements/LogNote/' . $input['module'] .'/'. Hashids::encode($model->id))) {
			        File::makeDirectory($file_path, 0777, true);
		    }

		    // File Uploading
		    $x =0;
	        if($request->hasfile('filenames'))
	         {
	            foreach($request->file('filenames') as $file)
	            {
	                $fileName = time().rand(1,100).'.'.$file->getClientOriginalExtension();
	                $filePath = $file->storeAs('uploads/attachements/LogNote/'. $module .'/'. Hashids::encode($model->id), $fileName, 'public');

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
	                $insert[$x]['log_note_id'] = $model->id;
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
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('kss_subscription_id',Hashids::decode($input['model_id'])[0])->orderBy('id','desc')->first();
	        }
            else if($module == "vouchers"){
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('voucher_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "quotations") {
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('quotation_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "contacts") {
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('contact_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "customers") {
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('customer_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "products") {
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('product_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "productVariants") {
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('variant_id',$model_id)->orderBy('id','desc')->first();
            }
            else if($module == "saleTeams") {
	            $note_user_details = ActivityLogNotes::with('log_note_users','activity_attachments')->where('sales_team_id',$model_id)->orderBy('id','desc')->first();
            }
            $attachments = ActivityAttachments::where('log_note_id', $model->id)->orderBy('id','desc')->get();
            foreach($note_user_details->activity_attachments as $attachment) {
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
            	 $remove_note_file = "remove_note_file_".Hashids::encode($attachment->id);
            	 if(in_array($attachment->file_extension, $img_ext)) {
                   $attachment_html = '<span class="description" id="'.$remove_note_file.'"><div class="customer-box" style="width: 410px;"><div class="customer-img"><img src="'.asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ).'" style="height: 100%"></div><div class="customer-content col-md-3"><h3 class="customer-heading">'.__('File Name').': '. $attachment->file_name .'</h3><span class="email">'.__('File Type').': '. $attachment->file_extension .'</span><a type="button" class="price" href="'.asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ).'" download>'.__('Download').'</a>&nbsp;<a href="javascript:void(0)" type="button" class="price" onclick="removeNoteFile($(this))" data-note-attachment-id="'. Hashids::encode($attachment->id).'"  data-log-note-id ="'.Hashids::encode($attachment->log_note_id).'" data-note-file-name="'.$attachment->file_name.'" data-note-file-url="'.route('admin.log.remove-note-file').'" data-note-model-id="' .Hashids::encode($attachment_model_id). '" data-note-module-name ="' .$attachment->module_name.'"><i class="class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></div></div></span>';
                   
                   array_push($arr,$attachment_html);
            	 }
                 else {

                    $attachment_html = '<span class="description margin-bottom-15"  id="'.$remove_note_file.'"><b>'.__('File').':</b><a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="'.asset('storage/uploads/attachements/LogNote/'. $attachment->module_name . '/' . Hashids::encode($attachment->log_note_id) . '/' . $attachment->file_name ).'" download>'. $attachment->file_name .' &nbsp;<i class="fa fa-download"></i></a><a style="margin-left: 10px; color: #009a71;border-bottom: 2px solid #009a71;" class=" btn ml-2" href="javascript:void(0)" onclick="removeNoteFile($(this))" data-note-attachment-id="'. Hashids::encode($attachment->id).'" data-log-note-id ="'.Hashids::encode($attachment->log_note_id).'" data-note-file-name="'.$attachment->file_name.'" data-note-file-url="'.route('admin.log.remove-note-file').'" data-note-model-id="' .Hashids::encode($attachment_model_id). '" data-note-module-name ="' .$attachment->module_name.'"><i class="fa fa-close"></i>&nbsp;'.__('Remove').'</a></span>';

                    array_push($arr,$attachment_html);
                 }
            }
            $html = implode(" ",$arr);
	        $user_url = route('admin.admin-user.edit',['admin_user'=> $input['uid']]);
	        $username = $note_user_details->log_note_users->firstname .' '. $note_user_details->log_note_users->lastname;
            $attachments =  $this->activities->all_activity_attachements($model_id, $module);
            $attachment_list =  $this->activities->updated_attachment_list($model_id, $module);
	 	    $img = '<img class="img-circle" src="' . checkImage(asset("storage/uploads/admin/" . Hashids::encode($note_user_details->log_note_users->id) . '/' . $note_user_details->log_note_users->image),'avatar5.png') . '"  alt='.__('User Image').'>';
	        return response()->json([
	        	"note"=> strip_tags($model->note), 
	        	"ago"=> $model->created_at->diffForHumans(), 
	        	"img" => $img, 
	        	"username" => $username, 
	        	"user_url" => $user_url,
	        	"attachments" => $html,
	        	"attachment_counter" => $attachments->count(),
	        	"attachment_list" => $attachment_list

	        ]);

    }

    // Method For Log Note Remove File
    public function removeNoteFile(Request $request) 
    {
    	$input = $request->all();
    	$id = Hashids::decode($input['id'])[0];
    	$log_note_id = Hashids::decode($input['log_id'])[0];
    	$module = $input['module_name'];
    	$model_id = Hashids::decode($input['model_id'])[0];
    	$model = ActivityAttachments::where('id', $id)->where('log_note_id', $log_note_id)->first();
    	$file = public_path() .'/storage/uploads/attachements/LogNote/'. $model->module_name . '/' . Hashids::encode($model->log_note_id) . '/' . $model->file_name;
    	File::delete($file);
    	$model->delete();
        $attachments_count =  $this->activities->all_activity_attachements($model_id, $module)->count();
	    $attachment_list =  $this->activities->updated_attachment_list($model_id, $module);
    	return response()->json(['deleted' => __('Your file has been deleted successfully.'), "attachment_counter" => $attachments_count, 'attachment_list' => $attachment_list]);
    }
  
}