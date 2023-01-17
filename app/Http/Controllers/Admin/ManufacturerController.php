<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Manufacturer;
use App\Models\ContactCountry;
use Yajra\DataTables\DataTables;
use App\Models\EmailTemplate;
use Illuminate\Support\Str;
use Session;
use Alert;
use Hashids;
use File;
use Image;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dta = [];
        if($request->ajax())
        {
            $data = Manufacturer::whereNull('associated_manufacturer_id')->orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('manufacturer_name', function ($row) {
                // auth()->user()->can('Edit Manufacturer') ? '<a href="' .route('admin.manufacturer.edit', Hashids::encode( $row->id)). '">'.$row->manufacturer_name.'</a>' :
                return  $row->manufacturer_name;
            });
            $datatable->addColumn('manufacturer_email', function ($row) {
                return  $row->manufacturer_email;
            });
            $datatable->addColumn('company', function ($row) {
                return  $row->company;
            });
            $datatable->addColumn('associated', function ($row) {
                return  $row->main_manufacturer == null ? '' :$row->main_manufacturer->manufacturer_name;
            });
            $datatable->addColumn('action', function ($row) {

                $actions = '<div style="display:inline-flex">';
                    if($row->main_manufacturer == null)
                        $actions .= '<a class="btn btn-primary btn-icon" href="' . route('admin.manufacturer.edit',Hashids::encode($row->id)) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>';
                    $actions .= '<a class="btn btn-default btn-icon ml-2" href="' . route('admin.manufacturer.delete',Hashids::encode($row->id)) . '"  title='.__('Delete').'><i class="fa fa-trash"></i></a>';
                $actions .= '</div>';

                return $actions;
            });

            $datatable = $datatable->rawColumns(['manufacturer_name','action']);
            return $datatable->make(true);
        }

        return view('admin.sales.manufacturers.index');
    }
    public function getMembersList($manufacturer_id)
    {
        $data = Manufacturer::where('associated_manufacturer_id',$manufacturer_id)->orderBy('id','desc')->get();
        $datatable = Datatables::of($data);
        $datatable->addColumn('name', function ($row) {
            return  $row->manufacturer_name;
        });
        $datatable->addColumn('email', function ($row) {
            return  $row->manufacturer_email;
        });
        $datatable->addColumn('action', function ($row) {
            $actions = '<div style="display:inline-flex">';
                $actions .= '<a class="btn btn-primary btn-icon" target="_blank" href="' . route('admin.manufacturer.edit',Hashids::encode($row->id)) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>';
                $actions .= '<a class="btn btn-default btn-icon ml-2" target="_blank" href="' . route('admin.manufacturer.delete',Hashids::encode($row->id)) . '"  title='.__('Delete').'><i class="fa fa-trash"></i></a>';
            $actions .= '</div>';
            return $actions;
        });

        $datatable = $datatable->rawColumns(['action']);
        return $datatable->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];

        $data['action'] = 'Add';
        $data['contact_countries'] = ContactCountry::all();
        return view('admin.sales.manufacturers.manufacturer_form')->with($data);
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
        if(isset($input['action']) && $input['action']   == 'Edit'){

            $manufacturer_id = $request->manufacturer_id;
            $check_email_exist = Manufacturer::where('manufacturer_email', $input['manufacturer_email'])->where('id','!=', $manufacturer_id)->first();
            if($check_email_exist){
                Alert::success(__('Error'), __('Email Already Exist!'))->persistent('Close')->autoclose(5000);
                return redirect()->back();
            }
            $model  = Manufacturer::where('id', $manufacturer_id)->first();
            if($model->manufacturer_email != $input['manufacturer_email'])
            {
                $email_template = EmailTemplate::where('type','manufacturer_email_change_notification')->first();
                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);

                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $new_email = $input['manufacturer_email'];
                $old_email = $model->manufacturer_email;
                $name = $model->manufacturer_name;
                $search = array("{{name}}","{{new_email}}","{{old_email}}","{{app_name}}");
                $replace = array($name,$new_email,$old_email,env('APP_NAME'));
                $content = str_replace($search,$replace,$content);
                dispatch(new \App\Jobs\RegistrationEmailJob($new_email,$subject,$content,'Reseller'));
            }
            
            $model  = Manufacturer::where('id', $manufacturer_id)->update([
                'manufacturer_name'     => $input['manufacturer_name'],
                'manufacturer_email'    => $input['manufacturer_email'],
                'company'               => $input['company'],
                'manufacturer_number'    => $input['manufacturer_number'],
                'city'    => $input['city'],
                'street_address'    => $input['street_address'],
                'state'    => $input['state'],
                'zipcode'    => $input['zipcode'],
                'country_id'    => $input['country_id'],
                'website'    => $input['website'],
                'role'    => @$input['role'],
            ]);
            $model  = Manufacturer::where('id', $manufacturer_id)->first();
            if (!empty($request->files) && $request->hasFile('image')) {
                $upload_path = public_path() . '/storage/uploads/manufacturer/' . Hashids::encode($model->id);
                if (!File::exists(public_path() . '/storage/uploads/manufacturer/' . Hashids::encode($model->id))) {
                    File::makeDirectory($upload_path, 0777, true);
                }
                $file = $request->file('image');
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size = $file->getSize();
                $size_mbs = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                
                    $file_temp_name = 'manufacturer-' . time() . '.' . $type;

                    $old_file = public_path() . '/storage/uploads/manufacturer/' . Hashids::encode($model->id) . '/' . $model->image;

                    if (file_exists($old_file) && !empty($model->image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/manufacturer/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);

                    $model->image = $file_temp_name;
                    $model->save();
                }
            }
            Manufacturer::whereIn('id',explode(',',$input['member_ids']))->update(
                [
                    "associated_manufacturer_id" => $manufacturer_id,
                    "company" => $model->company
                ]
            );

            if( isset($input['password']) && isset($input['confirm_password']) ){
                $model  = Manufacturer::where('id', $manufacturer_id)->update([
                    'password'          =>  bcrypt($input['password'])
                ]);

            }
            Alert::success(__('Success'), __('Manufacturer Updated successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.manufacturer.index');

        }else{
            // $check_email_exist = Manufacturer::where('manufacturer_email', $input['manufacturer_email'])->first();

            // if($check_email_exist){
            //     Alert::success(__('Error'), __('Email Already Exist!'))->persistent('Close')->autoclose(5000);
            //     return redirect()->back();
            // }

            $model = new Manufacturer();

            $model->manufacturer_name  = $input['manufacturer_name'];
            $model->manufacturer_email = @$input['manufacturer_email'];
            $model->company            = $input['company'];
            $model->manufacturer_number = $input['manufacturer_number'];
            $model->city = $input['city'];
            $model->street_address = $input['street_address'];
            $model->state = $input['state'];
            $model->zipcode = $input['zipcode'];
            $model->country_id = $input['country_id'];
            $model->website = $input['website'];
            // if( isset($input['password']) && isset($input['confirm_password']) ){
                // $model->password            = bcrypt($input['password']);
                $model->password            = bcrypt('P@SSw0rd123');
            // }
            $key                       = Str::random(30);
            $model->invitation_code    = $key;
            $model->is_verify_email    = 1;
            $model->save();
            if (!empty($request->files) && $request->hasFile('image')) {
                $upload_path = public_path() . '/storage/uploads/manufacturer/' . Hashids::encode($model->id);
                if (!File::exists(public_path() . '/storage/uploads/manufacturer/' . Hashids::encode($model->id))) {
    
                    File::makeDirectory($upload_path, 0777, true);
                }
                $file = $request->file('image');
                $type = $file->getClientOriginalExtension();
                $size = $file->getSize();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                
                    $file_temp_name = 'manufacturer-' . time() . '.' . $type;

                    $old_file = public_path() . '/storage/uploads/manufacturer/' . Hashids::encode($model->id) . '/' . $model->image;

                    if (file_exists($old_file) && !empty($model->image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/manufacturer/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);

                    $input['image'] = $file_temp_name;
                    $model->update($input);
                }
            }
            Manufacturer::whereIn('id',explode(',',$input['member_ids']))->update(
                [
                    "associated_manufacturer_id" => $model->id,
                    "company" => $model->company
                ]
            );
            $name       = $model->manufacturer_name;
            $email      = $model->manufacturer_email;
            
            // $link = route('manufacturers.verify', ['code' => $key]);

            $manufacturer_members =  Manufacturer::whereIn('id',explode(',',$input['member_ids']))->get();
            foreach($manufacturer_members as $member){
                $key                       = Str::random(30);
                $member->invitation_code    = $key;
                $member->is_verify_email    = 1;
                $member->save();
                $name       = $member->manufacturer_name;
                $email      = $member->manufacturer_email;
                $link = route('manufacturers.login.index');
    
                $email_template = EmailTemplate::where('type','manufacturer_verify_email')->first();
    
                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);
    
                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $search = array("{{name}}","{{link}}","{{app_name}}");
                $replace = array($name,$link,env('APP_NAME'));
                $content = str_replace($search,$replace,$content);
                dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content,'Reseller'));
                // $user_name = "Email: ".$input['manufacturer_email'];
                // $password = "Password: ".$input['password'];
                // $user_name = "Email: ".$input['manufacturer_email'];
                // $password = "Password: ".$input['password'];
                // $search = array("{{name}}","{{link}}","{{app_name}}","{{user_name}}","{{password}}");
                // $replace = array($name,$link,env('APP_NAME'),$user_name,$password);
                // dd($email,\App\Models\Contact::where('email', $email)->first());
            }

            Alert::success(__('Success'), __('Manufacturer added successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.manufacturer.index');

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('Edit Product'))
        access_denied();

        $data = [];
        $id = Hashids::decode($id)[0];
        $data['action'] = 'Edit';

        $data['id'] = $id;
        $data['manufacturer_details']  = Manufacturer::where('id',$id)->first();
        $data['contact_countries'] = ContactCountry::all();
        return view('admin.sales.manufacturers.manufacturer_form')->with($data);
    }


    public function deleteManufacturer($id){

        $manufacturer_id        = Hashids::decode($id)[0];
        $delete_manufacture     = Manufacturer::where('id',$manufacturer_id)->delete();

        Alert::success(__('Success'), __('Manufacturer Deleted successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.manufacturer.index');
    }


    public function resetPasswordLink(Request $request, $id){

        $manufacturer_id        = Hashids::decode($id)[0];
        $manufacturer_details   = Manufacturer::where('id',$manufacturer_id)->first();
        $name                   = $manufacturer_details->manufacturer_name ?? '';
        $email                  = $manufacturer_details->manufacturer_email ?? '';
        $invitation_code        = Str::random(30);
        $link                   = route('manufacturers.password.reset', ['token' => $invitation_code, 'email' => $email]);
        $email_template         = EmailTemplate::where('type','reset_password_manufacturer')->first();
        $lang                   = app()->getLocale();
        $email_template         = transformEmailTemplateModel($email_template,$lang);
        $content                = $email_template['content'];
        $subject                = $email_template['subject'];
        $search                 = array("{{name}}","{{link}}","{{app_name}}");
        $replace                = array($name,$link,env('APP_NAME'));
        $content                = str_replace($search,$replace,$content);

        dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));

        Alert::success(__('Success'), __('Manufacturer reset password link sent successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
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

    }

    public function addNewMember(Request $request)
    {
        $input = $request->all();
        $check_member = Manufacturer::where('manufacturer_email',$input['member_email'])->first();
        if($check_member){
            return 'false';
        }
        // $associated_manufacturer = Manufacturer::where('id',$input['manufacturer_id'])->first();
        $model = new Manufacturer();
        $model->manufacturer_name  = $input['member_name'];
        $model->manufacturer_email = $input['member_email'];
        $model->role               = $input['role'];
        $model->company            = 'Member';
        $model->password            = bcrypt($input['member_password']);
        $model->is_verify_email    = 1;
        $model->save();
        $model->hashid = Hashids::encode($model->id);
        return $model;

    }
}
