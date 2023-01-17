<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\InvitationMailController;
use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use App\Models\User;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\ContactCountry;
use App\Models\ContactTag;
use App\Models\ContactTitle;
use App\Models\ContactAddress;
use App\Models\Companies;
use App\Models\PaymentTerm;
use App\Models\Admin;
use App\Models\ProductPriceList;
use App\Models\SalesTeam;
use App\Models\ContactFedState;
use App\Models\ContactSalesPurchase;
use App\Models\Followers;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\ActivityLogNotes;
use App\Models\ActivityTypes;
use App\Models\ScheduleActivities;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Auth;
use Hashids;
use File;
use Image;
use Alert;

class ProductCustomersController extends Controller
{
    /**
     * @var PartialViewsRepositories.
     */
    protected $customerRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $customerRepository
     */
    public function __construct(PartialViewsRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Customers Listing'))
        access_denied();

        $req = $request->all();
        if (array_key_exists("s", $req) || array_key_exists("filter", $req)) {
            $contacts = Contact::with('contact_countries')->orderBy('id','desc');
            if (isset($request->s) &&  !empty($request->s)) {
                $contacts = $contacts->where('type',2)->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->s . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->s . '%');
                });
            }

            if (isset($request->filter[0])) {
                $contacts = $contacts->where(function ($query) use ($request) {
                    foreach ($request->filter as $filter) {
                        if ($filter != 3) {
                            $query->where('company_type', $filter);
                        }
                    }
                });
            }

            if (isset($request->filter[0]) && in_array(3, $request->filter)) {
                $contacts->where('status', 2);
            }
            $contacts = $contacts->where('type', 2)->paginate(10);
            return view('admin.contacts.contact-lists', compact('contacts'));
        } else {

            $customers = Contact::with('contact_countries', 'admin_users')->where('type', 2)->where('status', 1)->orderBy('id','desc')->paginate(10);
            return view('admin.sales.customers.index', compact('customers'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add Customer'))
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

        return view('admin.sales.customers.customer_form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $contact_addresses = json_decode($request['contact-addresses'][0]);
        $data = [];

        $model = null;
        $user_model = null;

        $input = $request->all();
        $input['type'] = 2;
        $input['country_id'] = (int) $input['country_id'];
        $input['state_id'] = (int) $input['state_id'];
        $input['admin_id'] = Auth::user()->id;
        $input['created_by'] = Auth::user()->id;
        if (@$input['action'] == 'Edit') {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'unique:contacts,email,'.$input['id'] ],
            ]);
            $model = Contact::where('id', $input['id'])->first();
            $model->fill($input)->save();

            if($model->user_id == null || $model->user_id == 0){
                $user_model = new User();
                $user_model->update($input);

            }
            Alert::success(__('Success'), __('Customer updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'unique:contacts'],
            ]);
            $model = new Contact();
            $model->fill($input)->save();

            $user_model = new User();
            $user_model->fill($input)->save();

            $user_model->invitation_code = sha1(time());
            $user_model->save();

            $model->user_id = $user_model->id;
            $model->save();

            if ($user_model != null) {
                $name = $user_model->name;
                $email = $user_model->email;
                $invitation_code = $user_model->invitation_code;
                InvitationMailController::sendInvitationMail($name, $email, $invitation_code, 'user');
            }

            Alert::success(__('Success'), __('Customer added successfully!'))->persistent('Close')->autoclose(5000);
        }

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
                $model->image  = $file_temp_name;
                $model->save();
            }
        }

        if ($model) {
            $model->contact_tags()->sync($request->tag_id);
        }

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
        ContactAddress::whereNotIn('id', explode(',', $request->contact_addresses_ids))->delete();
        ContactAddress::whereIn('id', explode(',', $request->contact_addresses_ids))->update(['contact_id' => $model->id]);


        return redirect()->route('admin.customers.index');
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
        if(!auth()->user()->can('Edit Customer'))
        access_denied();

        $data = [];
        $id = Hashids::decode($id);

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
        $data['contact_address_ids'] =  ContactAddress::where('contact_id', $id)->pluck('id')->toArray();
        // Code For Activities Section
        $log_uid = Auth::user()->id;
        $log_user_name = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('customer_id', $id)->where('module_type', 4)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

            $data['is_following'] = 1;
        } else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->customerRepository->follower_list($id[0], $log_uid, $module_type = 4);
        $data['send_messages'] = ActivityMessages::with('activity_message_users', 'activity_attachments')->where('customer_id', $id)->orderBy('id', 'desc')->get();
        $attachments = ActivityAttachments::where('customer_id', $id)->orderBy('send_msg_id', 'desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users', 'activity_attachments')->where('customer_id', $id)->orderBy('id', 'desc')->get();;
        $recipients = Contact::where('admin_id', '<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active', 1)->where('is_archive', '<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status', 1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types', 'schedule_by_users', 'assign_to_users')->where('customer_id', $id)->where('status', 0)->orderBy('due_date', 'asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types', 'schedule_by_users', 'assign_to_users','activity_attachments')->where('customer_id', $id)->where('status', 1)->orderBy('id', 'desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->customerRepository->sendMsgs($id, $log_uid, $module = 'customers', $log_user_name, $recipients, $module_type = 4, $log_uid);
        $data['log_notes_view'] = $this->customerRepository->logNotes($id, $log_uid, $module = 'customers', $log_user_name);
        $data['schedual_activities_view'] = $this->customerRepository->schedualActivities($id, $log_uid, $module = 'customers', $schedule_users, $schedule_activity_types, $log_uid, $module_type = 4);
        $data['notes_tab_partial_view'] = $this->customerRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->customerRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->customerRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module = 'customers');
        $data['attachments_partial_view'] = $this->customerRepository->attachmentsPartialView($attachments);

        return view('admin.sales.customers.customer_form')->with($data);
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
        if(!auth()->user()->can('Delete Customer'))
        access_denied();

        $id = Hashids::decode($id)[0];
        Contact::where('id', $id)->delete();
        ContactAddress::where('contact_id', $id)->delete();
        ContactSalesPurchase::where('contact_id', $id)->delete();
        Alert::success(__('Success'), __('Customer deleted successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.customers.index');
    }
    /**
     * Save Contact Address with contact_id 0
     *
     */
    public function saveContactAddress(Request $request)
    {
        $input = $request->all();
        $image = '';
        if ($request->contact_image) {

            $image = 'customer-address-' . time() . '.' . explode('/', explode(':', substr($request->contact_image, 0, strpos($request->contact_image, ';')))[1])[1];

            $img = Image::make($request->contact_image)->save(public_path('storage/uploads/contact-address/') . $image);
        }
        $contact_addresses_arr = array(
            'type' => $request->type,
            'contact_name' => $request->contact_name,
            'email' => $request->email,
            'job_position' => $request->job_position,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'street_1' => $request->street_1,
            'street_2' => $request->street_2,
            'notes' => $request->notes,
            'city' => $request->city,
            'zipcode' => $request->zipcode,
            'country_id' => $request->country_id ? $request->country_id : null,
            'title_id' => $request->title_id ? $request->title_id : null,
            'state_id' => $request->state_id ? $request->state_id : null,
            'contact_image' => $image ? $image : null,

        );
        $model = ContactAddress::firstOrCreate(
            ['id' => $request->contact_add_id],
        );
        $model->fill($contact_addresses_arr)->save();
        return $model->id;
    }

    /**
     * Get the details for the Customer Address Modal
     *
     */
    public function getCustomerAddressDetail(Request $request)
    {
        $address = ContactAddress::where('id', $request->id)->first();
        $address->street_1 = translation( $address->id,5,app()->getLocale(),'street_1', $address->street_1);
        $address->street_2 = translation( $address->id,5,app()->getLocale(),'street_2', $address->street_2);
        $address->city = translation( $address->id,5,app()->getLocale(),'city', $address->city);
        $address->notes = translation( $address->id,5,app()->getLocale(),'notes', $address->notes);
        return $address;
    }

    /**
     * Delete the Contact Address
     *
     */
    public function deleteCustomerAddress(Request $request)
    {
        $contact_id = ContactAddress::where('id', $request->id)->first()->contact_id;
        $address = ContactAddress::where('id', $request->id)->delete();

        $data['id'] = $request->id;
        $data['address_ids'] = implode(',', ContactAddress::where('contact_id', $contact_id)->pluck('id')->toArray());
        return $data;
    }

    /**
     * Check for Duplicate Email
     *
     * return true if duplicate is present else false
     */
    public function checkDuplicateEmail(Request $request)
    {
        $id = $request->id;
        $email = $request->email;
        // dd($email);
        $contact_query = Contact::where('email', $email);
        $user_query = User::where('email', $email);
        if($id != null && $id != 0)
        {
            $contact_query->where('id', '!=', $id);
        }
        $contact = $contact_query->first();
        $user = $user_query->first();
        // dd($contact,$user);

        if($contact || $user){
            return 'true';
        }
        return 'false';
    }


}
