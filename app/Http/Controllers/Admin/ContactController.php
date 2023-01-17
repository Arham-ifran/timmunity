<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use Alert;
use App\Http\Controllers\Controller;
use App\Models\Companies;
use App\Models\Contact;
use App\Models\ContactAddress;
use App\Models\ContactCountry;
use App\Models\ContactFedState;
use App\Models\PaymentTerm;
use App\Models\SalesTeam;
use App\Models\ProductPriceList;
use App\Models\ContactSalesPurchase;
use App\Models\ContactTag;
use App\Models\ContactTitle;
use App\Models\Admin;
use App\Models\User;
use App\Models\Followers;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\ActivityLogNotes;
use App\Models\ActivityTypes;
use App\Models\ScheduleActivities;
use App\Models\ResellerRedeemedPage;
use App\Models\Project;
use App\Models\ResellerPackage;
use App\Http\Controllers\Admin\InvitationMailController;
use App\Models\EmailTemplate;
use App\Models\Products;
use App\Models\ProductVariation;
use Auth;
use Carbon\Carbon;
use File;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Image;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Response;
use View;
use App\Models\VoucherOrder;
use Yajra\DataTables\DataTables;
use App\Models\Quotation;
use App\Models\QuotationOrderLineVoucher;
use App\Models\Voucher;

class ContactController extends Controller
{
    /**
     * @var PartialViewsRepositories.
     */
    protected $contactRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $contactRepository
     */
    public function __construct(PartialViewsRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (!auth()->user()->can('Contact Listing'))
            access_denied();
        $req = $request->all();
        if (array_key_exists("s", $req) || array_key_exists("filter", $req)) {

            $contacts = Contact::with('contact_countries', 'user')->orderBy('id', 'desc');
            if (isset($request->s) &&  !empty($request->s)) {
                $contacts = $contacts->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('phone', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('zipcode', 'LIKE', '%' . $request->s . '%');
                });
            }
            if (isset($request->filter) && $request->filter != 3) {

                $contacts = $contacts->where('company_type', $request->filter);
            }
            if (isset($request->type) && $request->type != '0') {
                $contacts = $contacts->where('type', $request->type);
            }
            if (isset($request->active_status) && $request->active_status != '') {
                $contacts = $contacts->whereHas('user', function ($q) use ($request) {
                    $q->where('is_active', $request->active_status);
                });
            }
            if (isset($request->created_by) && $request->created_by != '') {
                if ($request->created_by == '0') {
                    $contacts = $contacts->where('created_by', null);
                } else {
                    $contacts = $contacts->where('created_by', Hashids::decode($request->created_by)[0]);
                }
            }
            if (isset($request->company) && $request->company != '') {
                $contacts = $contacts->where('company_id', Hashids::decode($request->company)[0]);
            }
            if (isset($request->country) && $request->country != '') {
                $contacts = $contacts->where('country_id', Hashids::decode($request->country)[0]);
            }
            if (isset($request->start_date) && $request->start_date != '') {
                $contacts->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            $contacts = $contacts->get();
            $contact_count = count($contacts);
            $contacts->request = $request;
            $data = [
                "view" => view('admin.contacts.contact-lists', compact('contacts'))->render(),
                "count" => $contact_count
            ];
            return $data;
        } else {
            $contacts = Contact::with('contact_countries', 'admin_users', 'user');
            if (isset($request->type) && $request->type != '0') {
                $contacts = $contacts->where('type', $request->type);
            }
            $contacts = $contacts->where('status', 1)->orderBy('id', 'desc')->get();
            $contact_count = count($contacts);
            $contacts->admins =  Admin::where('is_active', 1)->get();
            $contacts->companies =  Companies::all();
            $contacts->countries =  ContactCountry::all();
            // dd( $contacts->countries[0]);
            return view('admin.contacts.index', compact('contacts', 'contact_count'));
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (!auth()->user()->can('Add New Contact'))
            access_denied();


        $data = [];
        $data['action'] = 'Add';
        $data['contact_countries'] = ContactCountry::all();
        $data['contact_fed_states'] = ContactFedState::all();
        $data['contact_tags'] = ContactTag::all();
        $data['contact_titles'] = ContactTitle::all();
        $data['companies'] = Companies::all();
        $data['payment_term'] = PaymentTerm::all();
        $data['salespersons'] = Admin::where('is_active', 1)->get();
        $data['salesteams'] = SalesTeam::all();
        $data['price_lists'] = ProductPriceList::all();
        $data['reseller_packages'] = ResellerPackage::where('is_active', '1')->get();
        // dd($data['reseller_packages']);
        return view('admin.contacts.form')->with($data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contact_addresses = json_decode($request['contact-addresses'][0]);
        $data = [];

        $input = $request->all();
        if ($input['action'] == 'Edit') {
            $input['updated_by'] = Auth::user()->id;
            $id = Hashids::decode($input['id']);
            $model = Contact::findOrFail($id)[0];
            $name = explode(' ', $input['name']);
            if ($model->admin_id <> null) {
                $adminModel = Admin::where('id', $model->admin_id)->first();
                $adminModel->firstname = $name[0];
                if(isset($name[1])){
                    $adminModel->lastname = $name[1];
                }
                $adminModel->update();
            }
            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
            ]);

            $upload_path = public_path() . '/storage/uploads/contact/' . Hashids::encode($model->id);
            if (!File::exists(public_path() . '/storage/uploads/contact/' . Hashids::encode($model->id))) {

                File::makeDirectory($upload_path, 0777, true);
            }
            if (!empty($request->files) && $request->hasFile('image')) {
                $file = $request->file('image');
                $type = $file->getClientOriginalExtension();
                $size = $file->getSize();
                $file_temp_name = '';
                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    if ($model->admin_id == null) {
                        $file_temp_name = 'contact-' . time() . '.' . $type;

                        $old_file = public_path() . '/storage/uploads/contact/' . Hashids::encode($model->id) . '/' . $model->image;

                        if (file_exists($old_file) && !empty($model->image)) {
                            //delete previous file
                            unlink($old_file);
                        }

                        $path = public_path('storage/uploads/contact/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                        $img = Image::make($file)->resize(300, 300, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);

                        $input['image'] = $file_temp_name;
                        $model->image = $file_temp_name;
                        $model->save();
                        // $model->update($input);
                    } else {
                        $file_temp_name = 'profile-' . time() . '.' . $type;

                        $old_file = public_path() . '/storage/uploads/admin/' . Hashids::encode($adminModel->id) . '/' . $adminModel->image;

                        if (file_exists($old_file) && !empty($adminModel->image)) {
                            //delete previous file
                            unlink($old_file);
                        }

                        $path = public_path('storage/uploads/admin/') . Hashids::encode($adminModel->id) . '/' . $file_temp_name;

                        // $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        //     $constraint->aspectRatio();
                        // })->save($path);
                        $img = Image::make($file)->save($path);

                        $input['image'] = $file_temp_name;
                        $adminModel->image = $input['image'];
                        $adminModel->save();
                    }
                }
                // dd('aa',$model,$file_temp_name);
            }
            // if ($model) {
            $model->contact_tags()->sync($request->tag_id);
            // }
            Alert::success(__('Success'), __('Contact updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'unique:contacts'],
            ]);

            $input['created_by'] = Auth::user()->id;

            $model = new Contact();

            $upload_path = public_path() . '/storage/uploads/contact/' . Hashids::encode($model->id);
            if (!File::exists(public_path() . '/storage/uploads/contact/' . Hashids::encode($model->id))) {

                File::makeDirectory($upload_path, 0777, true);
            }

            if (!empty($request->files) && $request->hasFile('image')) {

                $file = $request->file('image');
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size = $file->getSize();
                $size_mbs = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'contact-' . time() . '.' . $type;

                    $old_file = public_path() . '/storage/uploads/contact/' . Hashids::encode($model->id) . '/' . $model->image;

                    if (file_exists($old_file) && !empty($model->image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/contact/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);

                    $input['image'] = $file_temp_name;
                }
            }
        }

        if (!empty($request->commercial_registration_extract) && $request->hasFile('commercial_registration_extract')) {
            $file = $request->file('commercial_registration_extract');
            $type = $file->getClientOriginalExtension();
            $file_temp_name = 'commercial_registration_extract-' . time() . '.' . $type;
            $old_file = public_path() . '/storage/uploads/commercial_registration_extract/' . Hashids::encode($model->id) . '/' . $model->commercial_registration_extract;
            if (file_exists($old_file) && !empty($model->commercial_registration_extract)) {
                //delete previous file
                unlink($old_file);
            }
            $upload_path = public_path() . '/storage/uploads/commercial_registration_extract/' . Hashids::encode($model->id);
            if (!File::exists($upload_path)) {
    
                File::makeDirectory($upload_path, 0777, true);
            }
            $path = public_path('storage/uploads/commercial_registration_extract/') . Hashids::encode($model->id) . '/' . $file_temp_name;
            $file->storeAs('public/uploads/commercial_registration_extract/'.Hashids::encode($model->id).'/',$file_temp_name);

            $model->commercial_registration_extract = $file_temp_name;
            $model->save();

        }
        if ($model->type == 3) {
            if ($input['reseller_package_id'] != '') {
                $input['reseller_package_id'] = Hashids::decode($input['reseller_package_id'])[0];
            }
            $model->reseller_package_id = $input['reseller_package_id'];
        }
        $model->fill($input)->save();
        $model->reseller_credit_limit = @$input['reseller_credit_limit'];
        $model->reseller_invoice_cron_day = @$input['reseller_invoice_cron_day'];
        $model->reseller_invoice_cron_days_duration = @$input['reseller_invoice_cron_days_duration'];
        $model->company_name = @$input['company_name'];
        $model->company_url = @$input['company_url'];
        $model->state = @$input['state'];
        $model->invoice_as = @$input['invoice_as'];
        $model->save();
        if ($model) {
            $model->contact_tags()->attach($request->tag_id);
        }

        ContactAddress::whereNotIn('id', explode(',', $request->contact_addresses_ids))->delete();
        ContactAddress::whereIn('id', explode(',', $request->contact_addresses_ids))->update(['contact_id' => $model->id]);
        if (isset($input['sales']['sales_person_id']) || isset($input['sales']['payment_terms']) || isset($input['sales']['pricelist_id'])) {
            $input['sales']['contact_id'] = $model->id;
            $model_sales = ContactSalesPurchase::firstOrCreate(
                ['contact_id' => $model->id],
            );
            $model_sales->sales_person_id = (int) $input['sales']['sales_person_id'];
            $model_sales->payment_terms = (int) $input['sales']['payment_terms'];
            $model_sales->pricelist_id = isset($input['sales']['pricelist_id']) ? (int) $input['sales']['pricelist_id'] : null;
            $model_sales->save();
        }
        if ($model->type == 0 || $model->type == 2 || $model->type == 3) {
            $user_model = User::where('id', $model->user_id)->first();
            if (!$user_model) {
                $user_model = new User;
                $user_model->name = $input['name'];
                $user_model->email = $input['email'];
                $user_model->invitation_code = sha1(time());
                $user_model->save();
                $model->user_id = $user_model->id;
                $model->save();
                InvitationMailController::sendInvitationMail($user_model->name, $user_model->email, $user_model->invitation_code, 'user');
            } else {
                if($user_model->email != $input['email']){
                    $user_model->invitation_code = sha1(time());
                    $user_model->save();
                    InvitationMailController::sendInvitationMail($user_model->name, $input['email'], $user_model->invitation_code, 'user');
                }
                $user_model->name = $input['name'];
                $user_model->email = $input['email'];
                $user_model->save();
                
            }
            $user_model->is_active = $input['active-status'];
            $user_model->disabled_at = $input['active-status'] == 0 ? \Carbon\Carbon::now() : NULL;
            $user_model->account_status = $input['active-status'];
            // Transformation of reseller approval confirmation email.
            if ($user_model->is_approved != 1 && $input['active-status'] == 1) {
                $user_model->is_approved = 1;
                $name = $user_model->name;
                $email = $user_model->email;
                $link = route('login');

                $email_template = EmailTemplate::where('type', 'account_approval_confirmation')->first();
                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template, $lang);
                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $search = array("{{name}}", "{{link}}", "{{app_name}}");
                $replace = array($name, $link, env('APP_NAME'));
                $content = str_replace($search, $replace, $content);
                dispatch(new \App\Jobs\AccountApprovalConfimationJob($email, $subject, $content));
            }
            $user_model->save();
            if ($input['action'] == 'Add' && $model->type == 3) {
                $redeem_model = new ResellerRedeemedPage();
                $redeem_model->title = $user_model->name;
                // $redeem_model->url =  $input['redeem_page_url'].'/'.Hashids::encode($user_model->id);
                $redeem_model->url =  'https://www.' . env('reseller_domain') . '/' . strtolower(str_replace(' ', '-', $user_model->name)) . '/' . Hashids::encode($user_model->id);
                $redeem_model->description =  '<p>This is the voucher redeem page for <b>' . $user_model->name . '</b>.</p><p>Please add voucher redeem code in below field.</p><p>{{voucher_form}}</p>';
                $redeem_model->reseller_id = $user_model->id;
                $redeem_model->is_reseller_changed = 0;
                $redeem_model->logo = 'logo.png';
                $redeem_model->email = $user_model->email;
                $redeem_model->save();
            }
        }
        if ($model->type == 1) {
            if (Auth::user()->id != $model->admin_id) {
                $admin_model = Admin::where('id', $model->admin_id)->first();
                if (!$admin_model) {
                    $admin_model = new Admin;
                    $admin_model->firstname = $input['name'];
                    $admin_model->email = $input['email'];
                    $admin_model->save();

                    $model->admin_id = $admin_model->id;
                    $model->save();
                }
                $admin_model->is_active = $input['active-status'];
                $admin_model->account_status = $input['active-status'];
                $admin_model->save();
            }
        }
        if ($input['action'] == 'Edit') {
            Alert::success(__('Success'), __('Contact updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {
            Alert::success(__('Success'), __('Contact added successfully!'))->persistent('Close')->autoclose(5000);
        }

        return redirect()->route('admin.contacts.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('Edit Contact'))
            access_denied();
        $data = [];
        $id = Hashids::decode($id);
        Session::put('c_id', $id);
        $data['action'] = 'Edit';
        $data['contact_countries'] = ContactCountry::all();
        $data['contact_fed_states'] = ContactFedState::all();
        $data['contact_tags'] = ContactTag::all();
        $data['contact_titles'] = ContactTitle::all();
        $data['companies'] = Companies::all();
        $data['payment_term'] = PaymentTerm::all();
        $data['salespersons'] = Admin::where('is_active', 1)->get();
        $data['salesteams'] = SalesTeam::all();
        $data['price_lists'] = ProductPriceList::all();
        $data['model'] = Contact::with('contact_tags', 'contact_addresses', 'admin_users', 'sales_purchase')->find($id)[0];
        $data['reseller_packages'] = ResellerPackage::where('is_active', '1')->get();

        // Code For Activities Section
        $log_uid = Auth::user()->id;
        $log_user_name = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('contact_model_id', $id)->where('module_type', 3)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

            $data['is_following'] = 1;
        } else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->contactRepository->follower_list($id, $log_uid, $module_type = 3);
        $data['send_messages'] = ActivityMessages::with('activity_message_users', 'activity_attachments')->where('contact_id', $id)->orderBy('id', 'desc')->get();
        $attachments = ActivityAttachments::where('contact_id', $id)->orderBy('send_msg_id', 'desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users', 'activity_attachments')->where('contact_id', $id)->orderBy('id', 'desc')->get();;
        $recipients = Contact::where('admin_id', '<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active', 1)->where('is_archive', '<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status', 1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types', 'schedule_by_users', 'assign_to_users')->where('contact_id', $id)->where('status', 0)->orderBy('due_date', 'asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types', 'schedule_by_users', 'assign_to_users', 'activity_attachments')->where('contact_id', $id)->where('status', 1)->orderBy('id', 'desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->contactRepository->sendMsgs($id, $log_uid, $module = 'contacts', $log_user_name, $recipients, $module_type = 3, $log_uid);
        $data['log_notes_view'] = $this->contactRepository->logNotes($id, $log_uid, $module = 'contacts', $log_user_name);
        $data['schedual_activities_view'] = $this->contactRepository->schedualActivities($id, $log_uid, $module = 'contacts', $schedule_users, $schedule_activity_types, $log_uid, $module_type = 3);
        $data['notes_tab_partial_view'] = $this->contactRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->contactRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->contactRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module = 'contacts');
        $data['attachments_partial_view'] = $this->contactRepository->attachmentsPartialView($attachments);
        return view('admin.contacts.form')->with($data);
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
        if (!auth()->user()->can('Delete Contact'))
            access_denied();
        $model = Contact::find($id);

        if (Admin::where('id', $model->admin_id)->exists()) {
            $response = response()->json(['error' => __('The operation cannot be completed: another model requires the record being deleted. If possible, archive it instead.')]);
        } else {
            File::deleteDirectory(public_path() . '/storage/uploads/contact/' . $model);
            $model->email =  $model->email . strftime(time()) . 'deleted';
            $model->save();
            if ($model->user) {
                $model->user->email =  $model->user->email . strftime(time()) . 'deleted';
                $model->user->save();
                $model->user->delete();
            }

            $model->delete();
            $response = response()->json(['success' => true]);
            Alert::success(__('Success'), __('Contact deleted successfully.'))->persistent('Close')->autoclose(5000);
        }

        return $response;
    }
    public function contactsAddress(Request $request)
    {
        $data = [];
        $input = $request->all();
        if ($input['modal-action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = ContactAddress::findOrFail($id)[0];

            $this->validate($request, [
                'contact_name' => ['required', 'string', 'max:100'],

            ]);

            $model->update($input);
        } else {
            $this->validate($request, [
                'contact_name' => ['required', 'string', 'max:100'],

            ]);
            $model = new ContactAddress();
            $upload_path = public_path() . '/storage/uploads/contact-address/';
            if (!File::exists(public_path() . '/storage/uploads/contact-address/')) {

                File::makeDirectory($upload_path, 0777, true);
            }
            if (!empty($request->files) && $request->hasFile('contact_image')) {
                $file = $request->file('contact_image');
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size = $file->getSize();
                $size_mbs = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'contact-address-' . time() . '.' . $type;

                    $old_file = public_path() . '/storage/uploads/contact-address/';
                    if (file_exists($old_file) && !empty($model->contact_image)) {

                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/contact-address/') . $file_temp_name;

                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);

                    $input['contact_image'] = $file_temp_name;
                }
            }
        }
        $model->fill($input)->save();

        return response()->json($model);
    }
    public function createModal()
    {

        $data = [];
        $data['modal_action'] = 'Add';
        return view('admin.contacts.form')->with($data);
    }
    public function contactsAddressEdit($id)
    {

        $data = [];
        $data['action'] = 'Edit';
        $data['contact_countries'] = ContactCountry::all();
        $data['contact_fed_states'] = ContactFedState::all();
        $data['contact_titles'] = ContactTitle::all();
        $data['contacts_address'] = ContactAddress::all();
        $data['model_address'] = ContactAddress::find($id);
        return response()->json($data);
    }
    public function addressDlt($id)
    {

        $cid = Session::get('cid');
        $models = ContactAddress::find($id);
        if ($models) {
            $models->delete();
        }
        $model = Contact::with('contact_tags', 'contact_addresses')->find($cid);

        return response()->json($model);
    }
    public function LoadContactAddressModel(Request $request)
    {
        if ($request->action == 'edit') {
            $id = $request->id;
            //Session::put('c_id', $id);
            $data['model'] = ContactAddress::find($id);
            // print_r($data['model']);
            // exit;
        }

        $data['action'] = $request->action;
        $data['contact_countries'] = ContactCountry::all();
        $data['contact_fed_states'] = ContactFedState::all();
        //$data['contact_tags'] = ContactTag::all();
        $data['contact_titles'] = ContactTitle::all();
        //$data['companies'] = Companies::all();

        $viewLoad = view('admin.contacts.model-boxes.contact-address', $data)->render();

        return Response::json(['html' => $viewLoad]);
    }
    /**
     * archive and duplicate contact
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function contactActions(Request $request)
    {
        $id = Hashids::decode($request->data_id)[0];
        $contact = Contact::find($id);

        if ($request->action_type == 'archive') {
            $contact->update(['status' => 2]);
        } elseif ($request->action_type == 'unarchive') {
            $contact->update(['status' => 1]);
        } elseif ($request->action_type == 'duplicate') {
            $contact = $contact->replicate();
            $contact->name = $contact->name . ' (copy)';
            $contact->created_at = Carbon::now();
            $contact->save();
        }
        $contact->makeHidden(["created_by", "updated_by", "country_id", "company_type", "created_at", "updated_at", "company_id"]);

        return Response::json(['data' => $contact]);
    }
    /**
     * get match keywords from email contents
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function contains($needles, $haystack)
    {
        $count = count(array_intersect($needles, explode(" ", preg_replace("/[^A-Za-z0-9' -]/", "", $haystack))));
        $match = array_intersect($needles, explode(" ", preg_replace("/[^A-Za-z0-9' -]/", "", $haystack)));

        return ['count' => $count, 'match' => $match];
    }
    public function exportContacts(Request $request)
    {
        $contacts = Contact::with('contact_countries')->orderBy('name', 'asc');
        if (isset($request->s) &&  !empty($request->s)) {
            $contacts = $contacts->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->s . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->s . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $request->s . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->s . '%')
                    ->orWhere('zipcode', 'LIKE', '%' . $request->s . '%');
            });
        }
        if (isset($request->filter) && $request->filter != 3) {

            $contacts = $contacts->where('company_type', $request->filter);
        }
        if (isset($request->type) && $request->type != '0') {
            $contacts = $contacts->where('type', $request->type);
        }
        if (isset($request->active_status) && $request->active_status != '') {
            $contacts = $contacts->whereHas('user', function ($q) use ($request) {
                $q->where('is_active', $request->active_status);
            });
        }
        if (isset($request->created_by) && $request->created_by != '') {
            if ($request->created_by == 0) {
                $contacts = $contacts->where('created_by', null);
            } else {
                $contacts = $contacts->where('created_by', Hashids::decode($request->created_by)[0]);
            }
        }
        if (isset($request->company) && $request->company != '') {
            $contacts = $contacts->where('company_id', Hashids::decode($request->company)[0]);
        }
        if (isset($request->country) && $request->country != '') {
            $contacts = $contacts->where('country_id', Hashids::decode($request->country)[0]);
        }
        if (isset($request->start_date) && $request->start_date != '') {
            $contacts->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
        }
        $contacts = $contacts->get()->toArray();
        // dd($contacts[0]);
        $customer_array[] = array(
            'Name',
            'Email',
            'Mobile',
            'Phone',
            'Country',
            'City',
            'Web Link',
            'Zip Code',
            'Job Position'
        );

        foreach ($contacts as $contact) {
            $customer_array[] = [
                'Name' => $contact['name'],
                'Email' => $contact['email'],
                'Mobile' => $contact['mobile'],
                'Phone' => $contact['phone'],
                'Country' => @$contact['contact_countries']['name'],
                'City' => $contact['city'],
                'Web Link' => $contact['web_link'],
                'Zip Code' => $contact['zipcode'],
                'Job Position' => $contact['job_position'],
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < count($customer_array); $i++) {
            //set value for indi cell
            $row = $customer_array[$i];
            //writing cell index start at 1 not 0
            $j = 1;
            foreach ($row as $x => $x_value) {
                $sheet->setCellValueByColumnAndRow($j, $i + 1, $x_value);
                $j = $j + 1;
            }
        }

        ob_clean();
        $writer = new Xlsx($spreadsheet);
        //$writer->save('hello world.xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Contacts.xlsx"');
        $writer->save("php://output");
    }
    public function voucherRedeemedContacts(Request $request)
    {

        $contact_id       = $request->contact_id;
        $contact_detials  = Contact::where('id', $contact_id)->first();

        if ($request->ajax()) {
            $data_query       = Voucher::join('voucher_orders','voucher_orders.id','vouchers.order_id')
                            ->join('licenses','licenses.voucher_id','vouchers.id')
                            ->where('vouchers.customer_id', $contact_detials->user_id)
                            ->where('vouchers.status', 0)
                            ->select(
                                    'voucher_orders.product_id',
                                    'voucher_orders.variation_id',
                                    'vouchers.code',
                                    'licenses.license_key'
                                )
                            ->orderBy('vouchers.created_at', 'desc');

            $data = $data_query->get();

            $data_query2 = QuotationOrderLineVoucher::join('quotation_order_lines','quotation_order_line_vouchers.quotation_order_line_id','quotation_order_lines.id')
                    ->join('licenses','licenses.id','quotation_order_line_vouchers.license_id')
                    ->select(
                            'quotation_order_lines.product_id',
                            'quotation_order_lines.variation_id',
                            'quotation_order_line_vouchers.voucher_code as code',
                            'licenses.license_key'
                        )
                    ->orderBy('quotation_order_line_vouchers.created_at', 'desc');

            $data2 = $data_query2->get();
            $data_m = $data->concat($data2); 
            $datatable = Datatables::of($data_m);

            $datatable->addColumn('product_name', function ($row)  use ($request) {
                $product = Products::where('id',$row->product_id)->first(); 
                $product_variation = ProductVariation::where('id',$row->variation_id)->first(); 
                return $product->product_name.' '.@$product_variation->variation_name;
            });
            $datatable->addColumn('voucher_code', function ($row)  use ($request) {
                return isset($row->code) ? $row->code : '';
            });
            $datatable->addColumn('platform', function ($row)  use ($request) {
                return isset($row->license_key) ? $row->license_key : 'No License Key';
            });

            $datatable->addColumn('secondary_platform', function ($row)  use ($request) {
                $product = Products::where('id',$row->product_id)->first(); 
                $project_ids = explode(',', $product->secondary_project_ids);
                $project_name = '';
                foreach ($project_ids as $ind => $project_id) {
                    $project = Project::where('id', $project_id)->first();
                    if($project){
                        if($ind == 0){
                            $project_name .= @$project->name; 
                        }else{
                            $project_name .= ', '.@$project->name; 
                        }
                    }
                }
                return $project_name;
            });

            $datatable = $datatable->rawColumns(['Id', 'product_name']);
            return $datatable->make(true);
        }
    }
    public function orderQuotationForContacts(Request $request)
    {

        $contact_id = $request->contact_id;

        if ($request->ajax()) {
            $data_query = Quotation::with(
                'customer',
                'pricelist',
                'invoice_address_detail',
                'order_lines',
                'delivery_address_detail'
            )->where('customer_id', $contact_id)->orderBy('created_at', 'desc');


            $data = $data_query->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('Id', function ($row) {
                return $row->id;
            });
            $datatable->addColumn('order_no', function ($row) {
                return 'S' . str_pad($row->order_lines[0]->id, 5, '0', STR_PAD_LEFT);
            });
            $datatable->addColumn('customer', function ($row)  use ($request) {
                return  $row->customer->name;
            });
            $datatable->addColumn('order_date', function ($row)  use ($request) {
                return  date('d-M-Y', strtotime($row->created_at));
            });
            $datatable->addColumn('amount', function ($row)  use ($request) {
                return  currency_format($row->order_lines[0]->unit_price * $row->order_lines[0]->qty,$row->currency_symbol,$row->currency);
            });

            $datatable = $datatable->rawColumns(['Id', 'order_no', 'order_date', 'amount']);
            return $datatable->make(true);
        }
    }
    public function orderVouchersForContacts(Request $request)
    {

        $contact_id       = $request->contact_id;
        $contact_detials  = Contact::where('id', $contact_id)->first();
        // $data_query       = VoucherOrder::with('vouchers',
        // 'product',
        // )->where('reseller_id', $contact_detials->user_id)->orderBy('created_at','desc')->get();
        if ($request->ajax()) {
            $data_query       = VoucherOrder::with(
                'vouchers',
                'product',
            )->where('reseller_id', $contact_detials->user_id)->orderBy('created_at', 'desc');


            $data = $data_query->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('Id', function ($row) {
                return $row->id;
            });

            $datatable->addColumn('status', function ($row) {
                switch ($row->status) {
                    case 0:
                        return '<span class="badge bg-yellow">' . __('Pending') . '</span>';
                        break;
                    case 1:
                        return '<span class="badge bg-green">' . __('Approved') . '</span>';
                        break;
                    case 2:
                        return '<span class="badge bg-red">' . __('Rejected') . '</span>';
                        break;
                }
            });
            $datatable->addColumn('product_name', function ($row) {
                // $html = $row->product->product_name . ' ' . @$row->variation->variation_name;
                $html = $row->product->product_name . ' ';
                $html .= $row->product->project == null ? @$row->variation->variation_name : '';

                if ($row->product->secondary_project_ids != '') {
                    $projects = $row->product->secondary_projects_array;
                    $html .= '<br><strong>Secondary Platforms</strong><br>' . implode(',', $projects);
                }
                return $html;
            });
            $datatable->addColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d/M/Y');
            });
            $datatable->addColumn('discount', function ($row) {
                return @$row->discount_percentage;
            });
            $datatable->editColumn('quantity', function ($row) {
                return @$row->quantity;
            });
            $datatable->editColumn('used', function ($row) {
                return @$row->used_quantity;
            });
            $datatable->editColumn('remaining', function ($row) {
                return @$row->remaining_quantity;
            });
            $datatable->addColumn('unit_price', function ($row) {
                return number_format($row->unit_price * $row->exchange_rate, 2) . ' ' . $row->currency;
            });

            $datatable = $datatable->rawColumns(['product_name', 'active_status', 'statuss', 'action']);
            return $datatable->make(true);
        }
    }
    public function impersonateUser($user_id)
    {
        $user_id = Hashids::decode($user_id)[0];
        $user = User::where('id', $user_id)->first();
        Auth::guard('web')->login($user);
        return redirect()->route('frontside.home.index');
    }
    public function resendInviteEmail($user_id)
    {
        $user_id = Hashids::decode($user_id)[0];
        
        $user = User::where('id', $user_id)->first();
        if($user->email_verified_at != null){
            Alert::success(__('Success'), __('The account email is already verified'))->persistent('Close')->autoclose(5000);
            return redirect()->back();    
        }
        $user->invitation_code = sha1(time());
        $user->save();
        // Tranformation of Reseller Email Template
        $name = $user->name;
        $email = $user->email;
        $link = route('verify.user', ['code' => $user->invitation_code]);
        $email_template = EmailTemplate::where('type','customer_sign_up_confirmation')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        if($user->contact->type == 3)
            $subject = __('Reseller Sign Up Confirmation');
        $search = array("{{name}}","{{link}}","{{app_name}}");
        $replace = array($name,$link,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));
        Alert::success(__('Success'), __('Verification Email Sent'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
    public function checkDuplicateEmail(Request $request)
    {
        try {
            $id = Hashids::decode($request->id)[0];
        } catch (\Throwable $th) {
            $id = $request->id;
        }
        $email = $request->email;
        $contact_query = Contact::where('email', $email);
        
        if($id != null && $id != 0)
        {
            $contact_query->where('id', '!=', $id);
        }
        
        $contact = $contact_query->first();
        
        $user_query = User::where('email', $email);
        $user = $user_query->first();

        if($contact && $user){
            return 'false';
        }
        return 'true';
    }
}
