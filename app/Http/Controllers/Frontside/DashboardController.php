<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactCountry;
use App\Models\User;
use App\Models\Contact;
use App\Models\ContactTitle;
use App\Models\EmailTemplate;
use App\Models\Quotation;
use App\Models\Invoice;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Auth;
use Hashids;
use Image;
use File;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = [];
        $data['quotation_count'] = Quotation::where('customer_id', Auth::user()->contact->id)->where(function($query){
            $query->where('status','!=',1);
            $query->where('status','!=',2);
        })->count();
        $data['sales_order_count'] = Quotation::where('customer_id', Auth::user()->contact->id)->where(function($query){
            $query->where('status',1);
            $query->orWhere('status',2);
        })->count();
        $data['invoice_count'] = Invoice::whereHas('clean_quotation')->join('quotations', 'quotations.id', 'invoices.quotation_id')->where('quotations.customer_id', Auth::user()->contact->id)->count();
        return Auth::user()->email_verified_at == null ? view('frontside.dashboard.index', $data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.dashboard.index', $data);
    }

    /**
     * Show the sales order listing.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function salesOrderListing(Request $request)
    {

        $data = [];
        if ($request->ajax()) {
            $data = Quotation::where('customer_id', Auth::user()->contact->id)->where(function($query){
                $query->where('status',1);
                $query->orWhere('status',2);
            })->orderBy('id','desc')->get();

            $datatable = Datatables::of($data);
            $datatable->editColumn('ordernumber', function ($row) {
                return 'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
                return '<a href="' .route('user.dashboard.quotations.detail',Hashids::encode($row->id)). '">S'.str_pad($row->id, 5, '0', STR_PAD_LEFT).'</a>';
            });
            $datatable->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->addColumn('total', function ($row) {
                // return floatval(str_replace(",","",$row->total))* ($row->exchange_rate) . ' '. $row->currency;
                $total = currency_format($row->total*$row->exchange_rate,$row->currency_symbol,$row->currency);
                return $total;
            });
            $datatable->addColumn('link', function ($row) {
                return route('user.dashboard.quotations.detail',Hashids::encode($row->id));
            });
            $datatable = $datatable->rawColumns(['ordernumber']);
            return $datatable->make(true);

        }
        return Auth::user()->email_verified_at == null ? view('frontside.dashboard.sales_orders', $data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.dashboard.sales_orders', $data);
    }

    /**
     * Show the sales order Details.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function salesOrderDetail()
    {
       return Auth::user()->email_verified_at == null ? view('frontside.dashboard.sales_order_detail')->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.dashboard.sales_order_detail');
    }

    /**
     * Show the quotation order listing.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function quotationOrderListing(Request $request)
    {

        $data = [];
        if ($request->ajax()) {
            $data = Quotation::where('customer_id', Auth::user()->contact->id)->where(function($query){
                $query->where('status','!=',1);
                $query->where('status','!=',2);
            })->orderBy('id','desc')->get();

            $datatable = Datatables::of($data);
            $datatable->editColumn('ordernumber', function ($row) {
                return 'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
                return '<a href="' .route('user.dashboard.quotations.detail',Hashids::encode($row->id)). '">S'.str_pad($row->id, 5, '0', STR_PAD_LEFT).'</a>';
            });
            $datatable->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->addColumn('total', function ($row) {
                // return floatval(str_replace(",","",$row->total))* ($row->exchange_rate) . ' '. $row->currency;

                $total = currency_format($row->total*$row->exchange_rate,$row->currency_symbol,$row->currency);
                return $total;
            });
            $datatable->addColumn('link', function ($row) {
                return route('user.dashboard.quotations.detail',Hashids::encode($row->id));
            });
            $datatable = $datatable->rawColumns(['ordernumber']);
            return $datatable->make(true);

        }
        return Auth::user()->email_verified_at == null ? view('frontside.dashboard.quotations',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.dashboard.quotations',$data);
    }

    /**
     * Show the quotation Details.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function quotationDetail($quotation_id)
    {
        $quotation_id = Hashids::decode($quotation_id);

        $data['quotation'] = Quotation::with(
            'invoices',
            'customer',
            'customer.contact_addresses',
            'customer.contact_addresses.contact_countries',
            'pricelist',
            'order_lines',
            'order_lines.product',
            'order_lines.variation',
            'order_lines.quotation_taxes',
            'order_lines.quotation_taxes.tax',
            'payment_term_detail',
            'invoice_address_detail',
            'delivery_address_detail',
        )->where('id', $quotation_id)->first();
        $quotation = $data['quotation'];
        if($quotation){
            return isset(Auth::user()->email_verified_at) ?
                        Auth::user()->email_verified_at == null ?
                            view('frontside.dashboard.sales_order_detail',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) :
                            view('frontside.dashboard.sales_order_detail',$data) :
                            view('frontside.dashboard.sales_order_detail',$data);
        }
        return redirect()->route('user.dashboard')->with(session()->flash('alert-error', __('Invalid order provided')));
    }

    /**
     * Show the invoices listing.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function invoiceListing(Request $request)
    {
        $data = [];
        if ($request->ajax()) {
            $data = Invoice::whereHas('clean_quotation')->with(
                                'quotation',
                                'invoice_order_lines'
                            )->whereHas('quotation',function($query){
                                $query->where('customer_id', Auth::user()->contact->id);
                            })->orderBy('id','desc')
                            ->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('invoicenumber', function ($row) {
                // $text = '/';
                // if($row->status == 1 ){
                    // $text = 'INV/'. str_pad($row->id, 5, '0', STR_PAD_LEFT);
                    $text = 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT);
                // }
                return $text;
                return '<a href="' .route("user.dashboard.invoice.detail",Hashids::encode($row->id)). '">'.$text.'</a>';
            });

            $datatable->editColumn('invoicedate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->addColumn('link', function ($row) {
                return route("user.dashboard.invoice.detail",Hashids::encode($row->id));
            });

            $datatable->editColumn('status', function ($row) {
                switch($row->status){
                    case(0):
                        return '<span class="tagged quote">'.__('Draft').'</span>';
                        break;
                    case(1):
                        return '<span class="tagged success">'.__('Confirmed').'</span>';
                        break;
                    case(2):
                        return '<span class="tagged danger">'.__('Cancelled').'</span>';
                        break;
                }
            });
            $datatable->editColumn('invoice_total', function ($row) {
                // return floatval(str_replace(",","",$row->invoice_total))* (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1);
                $total = currency_format($row->invoice_total,$row->quotation->currency_symbol,$row->quotation->currency);
                return $total;
            });
            $datatable = $datatable->rawColumns(['status','invoicenumber']);
            return $datatable->make(true);
        }

        $data['ajaxroute'] = route('user.dashboard.quotations');
        return Auth::user()->email_verified_at == null ? view('frontside.dashboard.invoices')->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.dashboard.invoices');

    }

    /**
     * Show the invoice Details.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function invoiceDetail($invoice_id)
    {
        $invoice_id = Hashids::decode($invoice_id)[0];

        $data['model'] = Invoice::whereHas('clean_quotation')->with(
                'invoice_order_lines',
                'invoice_order_lines.quotation_order_line',
                'invoice_order_lines.quotation_order_line.product',
                'invoice_order_lines.quotation_order_line.variation',
                'invoice_order_lines.quotation_order_line.quotation_taxes',
                'invoice_order_lines.quotation_order_line.quotation_taxes.tax',
                'quotation',
                'quotation.customer',
                'quotation.customer.contact_addresses',
                'quotation.customer.contact_addresses.contact_countries',
                'quotation.pricelist',
                'quotation.other_info',
                'quotation.other_info.sales_person',
                'quotation.other_info.sales_team',
                'quotation.other_info.tags',
                'quotation.payment_term_detail',
                'quotation.invoice_address_detail',
                'quotation.delivery_address_detail',

            )->where('id', $invoice_id)->first();
        if($data['model']){
            if(Auth::guard('admin')->user()){
                return  view('frontside.dashboard.invoice_detail', $data);
            }
            return Auth::user()->email_verified_at == null ? view('frontside.dashboard.invoice_detail', $data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.dashboard.invoice_detail', $data);
        }
        return redirect()->route('user.dashboard')->with(session()->flash('alert-error', __('Invalid invoice reference')));
    }

    /**
     * User Profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profilePage()
    {
        $data['contact'] = Contact::where('user_id', Auth::user()->id)->first();
        $data['contact_countries'] = ContactCountry::all();
        $data['contact_titles'] = ContactTitle::all();
        if(Auth::user()->contact->type == 2){
            return view('frontside.dashboard.user_profile',$data);
        }else{
            return view('frontside.dashboard.reseller_profile',$data);
        }
    }
    /**
     * Save user profile
     *
     */
    public function saveProfile(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required'],
            'street_1' => ['required'],
            'city' => ['required'],
            'country_id' => ['required'],
        ]);
        $input = $request->all();

        $id = Auth::user()->id;

        $contact = Contact::where('user_id', $id)->first();
        $contact->fill($input);
        $contact->save();

        $user = User::where('id', $id)->first();
        $user->fill($input);
        if(isset($input['new_password'])){
            $user->password =  Hash::make($input['new_password']);
        }
        $user->save();

        $upload_path = public_path() . '/storage/uploads/contact/' . Hashids::encode($contact->id);
        if (!File::exists(public_path() . '/storage/uploads/contact/' . Hashids::encode($contact->id))) {

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

                $old_file = public_path() . '/storage/uploads/contact/' . Hashids::encode($contact->id) . '/' . $contact->image;

                if (file_exists($old_file) && !empty($contact->image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/contact/') . Hashids::encode($contact->id) . '/' . $file_temp_name;

                $img = Image::make($file)->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path);

                $input['image'] = $file_temp_name;
                $contact->image = $file_temp_name;
                $contact->save();

            }
        }
        // return redirect()->route('user.dashboard.profile')->with('success','Profile updated successfully!');
        return redirect()->route('user.dashboard.profile')->with(session()->flash('alert-success', __('Profile updated successfully!')));

    }
    public function resellerSaveProfile(Request $request)
    {
        $this->validate($request, [
            'title_id' => ['required'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required'],
            'street_1' => ['required'],
            'city' => ['required'],
            'country_id' => ['required'],
        ]);
        $input = $request->all();

        $id = Auth::user()->id;
        $check_for_change_contact = Contact::where('user_id', $id)
            ->where('title_id', $input['title_id'])
            ->where('name', $input['name'])
            ->where('phone', $input['phone'])
            ->where('street_1', $input['street_1'])
            ->where('city', $input['city'])
            ->where('country_id', $input['country_id'])
            ->first();
        $send_admin_email = false;
        if($check_for_change_contact){
            $send_admin_email = true;
        }
        $contact = Contact::where('user_id', $id)->update(
            [
                'title_id' => $input['title_id'],
                'name' => $input['name'],
                'phone' => $input['phone'],
                'street_1' => $input['street_1'],
                'city' => $input['city'],
                'zipcode' => $input['zipcode'],
                'country_id' => $input['country_id'],
                'vat_id' => $input['vat_id'],
                'company_name' => $input['company_name'],
                'company_url' => $input['company_url'],
                'invoice_as' => $input['invoice_as'],
                // 'commercial_registration_extract' => $input['commercial_registration_extract'],
                'state' => $input['state']
            ]
        );
        $contact = Contact::where('user_id', $id)->first();

        $user = User::where('id', $id)->first();
        $user->fill($input);
        if(isset($input['new_password'])){
            $user->password =  Hash::make($input['new_password']);
        }
        $user->save();

        if (!empty($request->files) && $request->hasFile('image')) {
            $file = $request->file('image');
            $type = $file->getClientOriginalExtension();
            $size = $file->getSize();
            
            if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                $upload_path = public_path() . '/storage/uploads/contact/' . Hashids::encode($contact->id);
                if (!File::exists(public_path() . '/storage/uploads/contact/' . Hashids::encode($contact->id))) {
                    File::makeDirectory($upload_path, 0777, true);
                }
                $file_temp_name = 'contact-' . time() . '.' . $type;

                $old_file = public_path() . '/storage/uploads/contact/' . Hashids::encode($contact->id) . '/' . $contact->image;

                if (file_exists($old_file) && !empty($contact->image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/contact/') . Hashids::encode($contact->id) . '/' . $file_temp_name;

                $img = Image::make($file)->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path);

                $input['image'] = $file_temp_name;
                $contact->image = $file_temp_name;
                $contact->save();

            }
        }
        if (!empty($request->commercial_registration_extract) && $request->hasFile('commercial_registration_extract')) {
            $file = $request->file('commercial_registration_extract');
            $type = $file->getClientOriginalExtension();
            $file_temp_name = 'commercial_registration_extract-1_' . time() . '.' . $type;
            $old_file = public_path() . '/storage/uploads/commercial_registration_extract/' . Hashids::encode($contact->id) . '/' . $contact->commercial_registration_extract;
            if (file_exists($old_file) && !empty($contact->image)) {
                //delete previous file
                unlink($old_file);
            }
            $upload_path = public_path() . '/storage/uploads/commercial_registration_extract/' . Hashids::encode($contact->id);
            if (!File::exists($upload_path)) {
    
                File::makeDirectory($upload_path, 0777, true);
            }
            $path = public_path('storage/uploads/commercial_registration_extract/') . Hashids::encode($contact->id) . '/' . $file_temp_name;
            
            $file->storeAs('public/uploads/commercial_registration_extract/'.Hashids::encode($contact->id),$file_temp_name);

            $contact->commercial_registration_extract = $file_temp_name;
            $contact->save();

        }

        if($send_admin_email == true)
        {
            $name = Auth::user()->contact->name;
            $email = Auth::user()->contact->email;
            $phone = Auth::user()->contact->phone;
            $address = Auth::user()->contact->street_1.', '.Auth::user()->contact->city.', '.@Auth::user()->contact->contact_countries->country_name;
            $commercial_extract_name = Auth::user()->contact->commercial_registration_extract;
            $commercial_extract = '<a class="btn btn-primary" target="_blank" href="'.asset('/storage/uploads/commercial_registration_extract/' . Hashids::encode(Auth::user()->contact->id) . '/' . $commercial_extract_name).'"> View</a>';

            $link = route('admin.contacts.edit',['contact'=> Hashids::encode(Auth::user()->contact->id)]);

            $email_template = EmailTemplate::where('type','reseller_account_approval_request')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name }}","{{email}}","{{link}}","{{phone}}","{{address}}","{{commercial_extract}}","{{app_name}}");
            $replace = array($name,$email,$link,$phone,$address,$commercial_extract,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\ProfileApprovalEmailJob($subject,$content));

        }
        return redirect()->route('user.dashboard.profile')->with(session()->flash('alert-success', __('Profile updated successfully!')));

    }
}
