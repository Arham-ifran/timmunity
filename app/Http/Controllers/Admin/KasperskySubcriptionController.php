<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KasperskySubscriptions;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\ActivityLogNotes;
use App\Models\Products;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\Followers;
use App\Models\ActivityTypes;
use App\Models\ScheduleActivities;
use App\Models\ProductVariation;
use Carbon\Carbon;
use DateTime;
use Hashids;
use Alert;
use View;
use Auth;
class KasperskySubcriptionController extends Controller
{

     /**
     * @var PartialViewsRepositories.
     */
    protected $kasperskyRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $kasperskyRepository
     */
    public function __construct(PartialViewsRepositoryInterface $kasperskyRepository)
    {
        $this->kasperskyRepository = $kasperskyRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $subscription = explode(' ', '1 Year');
        $data['value'] = '1';
        $data['duration'] = $subscription[0];
        $data['type'] =  $subscription[1];
        $data['all_subscriptions'] = KasperskySubscriptions::with('partners','products')->latest()->get();
        // dd($data);
        return view('admin.kaspersky.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $subscription = explode(' ', '1 Year');
        $data['value'] = '1';
        $data['duration'] = $subscription[0];
        $data['type'] =  $subscription[1];
        $data['action'] = 'Add';
        $data['partners'] =  Contact::where('status', 1)->get();
        $data['products'] = ProductVariation::with('product','variation_details')->get();
        return view('admin.kaspersky.form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data = [];
        $subscription = explode(' ', '1 Year');
        $duration = $subscription[0];
        $type =  $subscription[1];
        $diffInDays = Carbon::parse($input['start_date'])->diffInDays(Carbon::parse($input['end_date']));
        $diffInMonths = Carbon::parse($input['start_date'])->floatdiffInMonths(Carbon::parse($input['end_date']));
        if(($type == "Year" && $diffInDays == $duration*365) || ($type == "Month" && $diffInMonths == $duration)) {
            $licenseStatus = '1';
        }
        else {
            $licenseStatus = '2';
        }

        if($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = KasperskySubscriptions::findOrFail($id)[0];
            $this->validate($request, [
                'product_id' => 'required',
                'partner_id' => 'required',
                'subscriber_id' => 'required|max:50',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $model->partner_id = $input['partner_id'];
            $model->product_id = $input['product_id'];
            $model->subscriber_id = $input['subscriber_id'];
            $model->start_date = date('Y-m-d',strtotime($input['start_date']));
            $model->end_date = date('Y-m-d',strtotime($input['end_date']));
            $model->status = $licenseStatus;
            $model->update();
            Alert::success('Success', 'F-Secure Subscription updated successfully!')->persistent('Close')->autoclose(5000);
            return redirect()->back();

        } else if($input['action'] == 'Duplicate') {

            $this->validate($request, [
                'product_id' => 'required',
                'partner_id' => 'required',
                'subscriber_id' => 'required|max:50',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $newLicense = new KasperskySubscriptions();
            $newLicense->partner_id = $input['partner_id'];
            $newLicense->subscriber_id = $input['subscriber_id'];
            $newLicense->start_date = date('Y-m-d',strtotime($input['start_date']));
            $newLicense->end_date = date('Y-m-d',strtotime($input['end_date']));
            $newLicense->license_key = strtoupper(generate_kss());
            $newLicense->status = $licenseStatus;
            $newLicense->save();
            Alert::success('Success', 'F-Secure Subscription has been duplicated successfully!')->persistent('Close')->autoclose(5000);
            return redirect('admin/f-secure');

        } else {

            $this->validate($request, [
                'product_id' => 'required',
                'partner_id' => 'required',
                'subscriber_id' => 'required|max:50',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);

            $model = new KasperskySubscriptions();
            $model->partner_id = $input['partner_id'];
            $model->product_id = $input['product_id'];
            $model->subscriber_id = $input['subscriber_id'];
            $model->start_date = date('Y-m-d',strtotime($input['start_date']));
            $model->end_date = date('Y-m-d',strtotime($input['end_date']));
            $model->license_key = strtoupper(generate_kss());
            $model->status = $licenseStatus;
            $model->save();

            Alert::success('Success', 'F-Secure Subscription added successfully!')->persistent('Close')->autoclose(5000);
            return redirect('admin/f-secure');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = [];
        $id = Hashids::decode($id)[0];
        $data['model'] = KasperskySubscriptions::with('partners','products')->where('id',$id)->first();
        $subscription = explode(' ', '1 Year');
        $data['value'] = '1';
        $data['duration'] = $subscription[0];
        $data['type'] =  $subscription[1];
        // Code For Activities Section
        $log_uid = Auth::user()->id;
        $partner = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('kss_subscription_id', $id)->where('module_type', 0)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->kasperskyRepository->follower_list($id,$log_uid, $module_type=0);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('kss_subscription_id',$id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('kss_subscription_id', $id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('kss_subscription_id',$id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('kss_subscription_id', $id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users','activity_attachments')->where('kss_subscription_id', $id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->kasperskyRepository->sendMsgs($id, $log_uid, $module ='kaspersky', $partner, $recipients, $module_type = 0,$log_uid);
        $data['log_notes_view'] = $this->kasperskyRepository->logNotes($id, $log_uid, $module ='kaspersky', $partner);
        $data['schedual_activities_view'] = $this->kasperskyRepository->schedualActivities($id, $log_uid, $module ='kaspersky', $schedule_users, $schedule_activity_types, $log_uid, $module_type=0);
        $data['notes_tab_partial_view'] = $this->kasperskyRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->kasperskyRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->kasperskyRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='kaspersky');
        $data['attachments_partial_view'] = $this->kasperskyRepository->attachmentsPartialView($attachments);
        return view('admin.kaspersky.view')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [];
        $id = Hashids::decode($id)[0];
        $subscription = explode(' ', '1 Year');
        $data['value'] = '1';
        $data['duration'] = $subscription[0];
        $data['type'] =  $subscription[1];
        $data['action'] = 'Edit';
        $data['partners'] =  Contact::where('status', 1)->get();
        $data['products'] = ProductVariation::with('product','variation_details')->get();
        $data['model'] = KasperskySubscriptions::where('id',$id)->first();
        // Code For activity Section
        $log_uid = Auth::user()->id;
        $partner = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('kss_subscription_id', $id)->where('module_type', 0)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->kasperskyRepository->follower_list($id,$log_uid, $module_type=0);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('kss_subscription_id',$id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('kss_subscription_id', $id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('kss_subscription_id',$id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('kss_subscription_id', $id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users','activity_attachments')->where('kss_subscription_id', $id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->kasperskyRepository->sendMsgs($id, $log_uid, $module ='kaspersky', $partner, $recipients, $module_type = 0,$log_uid);
        $data['log_notes_view'] = $this->kasperskyRepository->logNotes($id, $log_uid, $module ='kaspersky', $partner);
        $data['schedual_activities_view'] = $this->kasperskyRepository->schedualActivities($id, $log_uid, $module ='kaspersky', $schedule_users, $schedule_activity_types, $log_uid, $module_type=0);
        $data['notes_tab_partial_view'] = $this->kasperskyRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->kasperskyRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->kasperskyRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='kaspersky');
        $data['attachments_partial_view'] = $this->kasperskyRepository->attachmentsPartialView($attachments);
        return view('admin.kaspersky.form')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Hashids::decode($id);
        $model = KasperskySubscriptions::find($id)[0];
        $model->delete();
        Alert::success('Success', 'F-Secure Subscription deleted Successfully.')->persistent('Close')->autoclose(5000);
        return redirect('admin/f-secure');
    }

    public function licenseActivation(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        if (KasperskySubscriptions::where('id', $id)->exists()) {
        KasperskySubscriptions::where('id',$id)->update(['status'=> 1]);
           $response = response()->json(['success'=>"The License has been activated successfully."]);
        }
        else {
           $response = response()->json(['error'=>"Please add any subscription first."]);
        }
        return $response;
    }
    public function licenseHold(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        KasperskySubscriptions::where('id',$id)->update(['status'=> 3]);
       return response()->json(['success'=>"The License has been temporarily hold."]);
    }
    public function licenseResume(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        KasperskySubscriptions::where('id',$id)->update(['status'=> 1]);
        return response()->json(['success'=>"The License has been resumed successfully."]);
    }
    public function licenseBlocked(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        KasperskySubscriptions::where('id',$id)->update(['status'=> 4]);
        return response()->json(['success'=>"The License has been permanantly cancelled."]);
    }
    public function duplicate($id){

        $id = Hashids::decode($id);
        $subscription = explode(' ', '1 Year');
        $data['value'] = '1';
        $data['duration'] = $subscription[0];
        $data['type'] =  $subscription[1];
        $data['partners'] =  Contact::where('status', 1)->get();
        $data['products'] = Products::all();
        $data['action'] = 'Duplicate';
        $data['model'] = KasperskySubscriptions::find($id)->first();
        return view('admin.kaspersky.form')->with($data);
   }
}
