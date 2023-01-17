<?php

namespace App\Http\Controllers\Admin;

use File;
use Form;
use Alert;
use Image;
use Hashids;
use Storage;
use DataTables;
use App\Models\Admin;
use App\Models\User;
use App\Models\Currency;
use App\Models\Companies;
use App\Models\SalesTeam;
use App\Models\ContactTag;
use App\Models\PaymentTerm;
use App\Models\CompaniesKss;
use App\Models\ContactTitle;
use Illuminate\Http\Request;
use App\Models\ContactCountry;
use App\Models\ContactFedState;
use App\Models\ProductPriceList;
use App\Models\ResellerRedeemedPage;
use App\Models\EmailTemplate;

use App\Http\Controllers\Controller;
use App\Models\CompaniesContactsMembers;
use App\Http\Controllers\Admin\InvitationMailController;
use App\Models\Contact;
use Carbon\Carbon;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Company Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = Companies::with('countries', 'states', 'currencies')->latest()->orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('company_name', function ($row) {
                return ucfirst($row->name);
            });
            $datatable->addColumn('country', function ($row) {
                return $row->countries->name;
            });
            $datatable->addColumn('state', function ($row) {
                return @$row->states->name;
            });
            $datatable->addColumn('currency', function ($row) {
                if ($row->currency_id != null) {
                    return $row->currencies->symbol.' - '.$row->currencies->code;
                } else {
                    return '€ - EUR ';
                }
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Delete Company','Edit Company'])) {
                $actions .= auth()->user()->can('Edit Company') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/companies/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Company')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/companies', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                }
            }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['company_name', 'country', 'state', 'currency', 'action']);
            return $datatable->make(true);
        }
        return view('admin.settings.companies.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Company'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        $data['countries'] = ContactCountry::all();
        $data['states'] = ContactFedState::all();
        $data['contact_countries'] = ContactCountry::all();
        $data['currences'] = Currency::all();
        $data['team_leads'] = Admin::where('is_active', 1)->get();
        $data['contact_fed_states'] = ContactFedState::all();
        $data['contact_tags'] = ContactTag::all();
        $data['contact_titles'] = ContactTitle::all();
        $data['companies'] = Companies::all();
        $data['payment_term'] = PaymentTerm::all();
        $data['salespersons'] = Admin::where('is_active', 1)->get();
        $data['salesteams'] = SalesTeam::all();
        $data['price_lists'] = ProductPriceList::all();
        return view('admin.settings.companies.form')->with($data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contact_array = json_decode($request['contacts_array'][0]);
        $input = $request->all();
        $contact_ids = explode(',', $input['contact_member_id']);
        $remove_contacts_ids = explode(',', $input['remove_contacts_ids']);
        // dd($input);
        if ($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = Companies::findOrFail($id)[0];
            $this->validate($request, [
                'name' => 'required|string|max:100',
                'street_address' => 'required|string|max:100',
                'country_id' => 'required',
                'city' => 'required|string|max:20',
                'phone' => 'required',
                // 'currency_id' => 'required'
            ]);

            $model->update($input);


            Alert::success(__('Success'), __('Company updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => 'required|string|max:100',
                'street_address' => 'required|string|max:100',
                'country_id' => 'required',
                'city' => 'required|string|max:20',
                'email' => 'required|string|email|max:255|unique:companies',
                'phone' => 'required',
                // 'currency_id' => 'required'
            ]);
            $model = new Companies();
            $model->fill($input)->save();

            Alert::success(__('Success'), __('Company added successfully!'))->persistent('Close')->autoclose(5000);
        }
        if ($request['contacts_array'][0] <> null) {
            $image = '';
            foreach ($contact_array as  $value) {
                $contact_model = new Contact;
                    $contact_model->type =$value->type;
                    $contact_model->company_type = $value->company_type;
                    $contact_model->status = $value->status;
                    $contact_model->name = $value->name;
                    $contact_model->email = $value->email;
                    $contact_model->job_position = $value->job_position;
                    $contact_model->phone = $value->phone;
                    $contact_model->mobile = $value->mobile;
                    $contact_model->street_1 = $value->street_1;
                    $contact_model->street_2 = $value->street_2;
                    $contact_model->city = $value->city;
                    $contact_model->zipcode= $value->zipcode;
                    $contact_model->country_id = $value->country_id ? $value->country_id : null;
                    $contact_model->title_id = $value->title_id ? $value->title_id : null;
                    $contact_model->state_id = $value->state_id ? $value->state_id : null;
                    $contact_model->company_id = $model->id;
                    $contact_model->type = $value->type;
                    $contact_model->vat_id = $value->vat_id;
                    $contact_model->web_link = $value->web_link;
                    $contact_model->status = $value->status;
                $contact_model->save();

                $user_model = new User;
                    $user_model->name = $value->name;
                    $user_model->email = $value->email;
                    $user_model->is_active = $value->status;
                    $user_model->invitation_code = sha1(time());
                    $user_model->save();
                $contact_model->user_id = $user_model->id;
                if ( $contact_model->type == 3) {
                    $redeem_model = new ResellerRedeemedPage();
                    $redeem_model->title = $user_model->name;
                    $redeem_model->url =  'https://www.'.env('reseller_domain').'/'.strtolower(str_replace(' ','-',$user_model->name)).'/'.Hashids::encode($user_model->id);
                    $redeem_model->description =  '<p>This is the voucher redeem page for <b>'.$user_model->name.'</b>.</p><p>Please add voucher redeem code in below field.</p><p>{{voucher_form}}</p>';
                    $redeem_model->reseller_id = $user_model->id;
                    $redeem_model->is_reseller_changed = 0;
                    $redeem_model->logo = 'logo.png';
                    $redeem_model->email = $user_model->email;
                    $redeem_model->save();
                }
                $upload_path = public_path() . '/storage/uploads/contact/' . Hashids::encode($contact_model->id);
                if (!File::exists(public_path() . '/storage/uploads/contact/' . Hashids::encode($contact_model->id))) {
                    File::makeDirectory($upload_path, 0777, true);
                }

                $old_file = public_path() . '/storage/uploads/contact/' . Hashids::encode($contact_model->id) . '/' . $contact_model->image;
                if (file_exists($old_file) && !empty($contact_model->image)) {
                    unlink($old_file);
                }
                if ($value->contact_image != null && $value->contact_image != '') {
                    $image = 'contact-' . time() . '.' . explode('/', explode(':', substr($value->contact_image, 0, strpos($value->contact_image, ';')))[1])[1];
                    $img = Image::make($value->contact_image)->save(public_path('storage/uploads/contact/'). Hashids::encode($contact_model->id) .'/'. $image);
                }
                $contact_model->image = $image;
                $contact_model->update();
                InvitationMailController::sendInvitationMail($user_model->name, $user_model->email, $user_model->invitation_code,'user');
            }
        }

        // Remove Companies Contact Member from pivot table
        if($input['remove_contacts_ids'] <> null) {
            foreach ($remove_contacts_ids as $row) {
                Contact::where('id', Hashids::decode($row)[0])->delete();
            }
        }
        //MAKE DIRECTORY
        $upload_path = public_path() . '/storage/uploads/companies/' . Hashids::encode($model->id);
        if (!File::exists(public_path() . '/storage/uploads/companies/' . Hashids::encode($model->id))) {
              File::makeDirectory($upload_path, 0777, true);
        }

        // Upload Company Image
        if (!empty($request->files) && $request->hasFile('image')) {
            $file      = $request->file('image');
            $file_name = $file->getClientOriginalName();
            $type      = $file->getClientOriginalExtension();
            $real_path = $file->getRealPath();
            $size      = $file->getSize();
            $size_mbs  = ($size / 1024) / 1024;
            $mime_type = $file->getMimeType();

            if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                $file_temp_name = 'companies-' . time() . '.' . $type;
                $old_file  = public_path() . '/storage/uploads/companies/' . Hashids::encode($model->id) . '/' . $model->image;
                if (file_exists($old_file) && !empty($model->image)) {
                    //delete previous file
                    unlink($old_file);
                }

                $path = public_path('storage/uploads/companies/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                if ($size_mbs >= 2) {
                    $img = Image::make($file)->fit(300, 300)->save($path);
                } else {
                    $img = Image::make($file)->resize(300, 300)->save($path);
                }
                $img = Image::make($file)->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path);

                $update = array('image' => $file_temp_name);

                Companies::where('id', $model->id)->update($update);
            }
        }
        return redirect('admin/companies');
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
        if(!auth()->user()->can('Edit Company'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['contact_countries'] = ContactCountry::all();
        $data['contact_fed_states'] = ContactFedState::all();
        $data['companies'] = Companies::all();
        $data['contact_tags'] = ContactTag::all();
        $data['contact_titles'] = ContactTitle::all();
        $data['companies'] = Companies::all();
        $data['payment_term'] = PaymentTerm::all();
        $data['salespersons'] = Admin::where('is_active', 1)->get();
        $data['countries'] = ContactCountry::all();
        $data['states'] = ContactFedState::all();
        $data['currences'] = Currency::all();
        $data['model'] = Companies::find($id)[0];
        $data['kss'] = CompaniesKss::with('companies')->latest()->first();
        $data['contact_members'] = Contact::where('company_id', $id)->get();
        // return $data['contact_members'];
        return view('admin.settings.companies.form')->with($data);
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
        if(!auth()->user()->can('Delete Company'))
        access_denied();
        $id = Hashids::decode($id);
        File::deleteDirectory(public_path() . '/storage/uploads/companies/' . Hashids::encode($id));
        File::deleteDirectory(public_path() . '/storage/uploads/files/companies/' . Hashids::encode($id));
        $model = Companies::find($id)[0];
        $model->delete();
        // Delete Company KSS
        CompaniesKss::where('company_id', $id)->delete();
        Alert::success(__('Success'), __('Company deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/companies');
    }

    public function checkEmail(Request $request)
    {
        $companies_count = Companies::where('email', $request->email);
        // dd(HashIds::decode($request->id)[0]);
        if($request->id != ''){
            // dd(';a');
            $companies_count = $companies_count->where('id','!=',HashIds::decode($request->id)[0]);
        }
        $companies_count = $companies_count->count();
        if($companies_count == 0){
            return 'true';
        }
        return 'false';
    }

    // Contact member function
    public function CompanyContactMember(Request $request)
    {
            $input = $request->all();

            if($input['action'] == "Edit") {
            // return $input;
            $id = Hashids::decode($input['id']);
            $model = Contact::findOrFail($id)[0];

            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
            ]);
            $model->update($input);
            $model->contact_tags()->sync($request->tag_id);
            }
            if ($model) {
                $model->contact_tags()->attach($request->tag_id);
            }
                $upload_path = public_path() . '/storage/uploads/contact/';
                if (!File::exists(public_path() . '/storage/uploads/contact/')) {

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

                        $old_file = public_path() . '/storage/uploads/contact/'  . Hashids::encode($model->id).'/'.$model->image;

                        if (file_exists($old_file) && !empty($model->image)) {
                            //delete previous file
                            unlink($old_file);
                        }

                        $path = public_path('storage/uploads/contact/') .Hashids::encode($model->id) .'/'. $file_temp_name;

                        $img = Image::make($file)->resize(300, 300, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($path);

                        $update = array('image' => $file_temp_name);
                        Contact::where('id', $model->id)->update($update);
                    }
            }
            $status = 1;
            $img_name = isset($file_temp_name) ? $file_temp_name : $model->image;
            $img = '<img src="' . checkImage(asset("storage/uploads/contact/" . Hashids::encode($model->id).'/'. @$img_name),'avatar5.png') . '"  alt="Contact Image" width="100%" height="100%">';

            $data = ['name' => $model->name, 'id' => Hashids::encode($model->id), 'image' => $img, 'email'=>$model->email];
            return response()->json([
                'data' => $data,
                'status' => $status,
                'messagetype' => 'success',
            ], 200, ['Content-Type' => 'application/json']);
    }
    // Contact update function
    public function UpdateContactMember(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $action = $input['action'];
        $model = Contact::find($id)[0];
        $img = '<img id="ContactmemberImagePreviewDB" src="' . checkImage(asset("storage/uploads/contact/".Hashids::encode($id).'/'.$model->image),'avatar5.png') . '"  width="100%" height="100%" alt="' .asset("backend/dist/img/avatar5.png"). '">';
        return response()->json([
                    'model'=> $model,
                    'image' => $img,
                    'action' => $action
    ]);
    }
}
