<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Distributor;
use Yajra\DataTables\DataTables;
use App\Models\EmailTemplate;
use App\Models\DistributorProductDetail;
use Illuminate\Support\Str;
use Alert;
use Hashids;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $data = Distributor::orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('name', function ($row) {
                return  $row->name;
            });
            $datatable->addColumn('email', function ($row) {
                return  $row->email;
            });
            $datatable->addColumn('company', function ($row) {
                return  $row->company;
            });
            $datatable->addColumn('action', function ($row) {

                $actions = '<div style="display:inline-flex">';
                  $actions .= '<a class="btn btn-primary btn-icon" href="' . route('admin.distributor.edit',Hashids::encode($row->id)) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>';
                $actions .= '</div>';

                return $actions;
            });

            $datatable = $datatable->rawColumns(['name','action']);
            return $datatable->make(true);
        }
        return view('admin.distributors.index');
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
        $data['products']  = Products::where('is_active',1)->get();
        return view('admin.distributors.form')->with($data);
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
        $model = null;
        if(isset($input['action']) && $input['action']   == 'Edit'){

            $distributor_id = $request->distributor_id;
            $check_email_exist = Distributor::where('email', $input['email'])->where('id','!=', $distributor_id)->first();
            if($check_email_exist){
                Alert::success(__('Error'), __('Email Already Exist!'))->persistent('Close')->autoclose(5000);
                return redirect()->back();
            }
            $model  = Distributor::where('id', $distributor_id)->first();
            // if($model->email != $input['email'])
            // {
            //     $email_template = EmailTemplate::where('type','manufacturer_email_change_notification')->first();
            //     $lang = app()->getLocale();
            //     $email_template = transformEmailTemplateModel($email_template,$lang);

            //     $content = $email_template['content'];
            //     $subject = $email_template['subject'];
            //     $new_email = $input['manufacturer_email'];
            //     $old_email = $model->manufacturer_email;
            //     $name = $model->manufacturer_name;
            //     $search = array("{{name}}","{{new_email}}","{{old_email}}","{{app_name}}");
            //     $replace = array($name,$new_email,$old_email,env('APP_NAME'));
            //     $content = str_replace($search,$replace,$content);
            //     dispatch(new \App\Jobs\RegistrationEmailJob($new_email,$subject,$content,'Reseller'));
            // }
            $model  = Distributor::where('id', $distributor_id)->update([
                'name'     => $input['name'],
                'email'    => $input['email'],
                'company'               => $input['company'],
                'shop_url'               => $input['shop_url'],
                'auth_key'               => $input['auth_key'],
            ]);
            $model  = Distributor::where('id', $distributor_id)->first();
            if( isset($input['password']) && isset($input['confirm_password']) ){
                $model  = Distributor::where('id', $distributor_id)->update([
                    'password'          =>  bcrypt($input['password'])
                ]);

            }
            Alert::success(__('Success'), __('Distributor Updated successfully!'))->persistent('Close')->autoclose(5000);
        }else{
            $check_email_exist = Distributor::where('email', $input['email'])->first();

            if($check_email_exist){
                Alert::success(__('Error'), __('Email Already Exist!'))->persistent('Close')->autoclose(5000);
                return redirect()->back();
            }

            $model = new Distributor();

            $model->name  = $input['name'];
            $model->email = $input['email'];
            $model->company = $input['company'];
            $model->shop_url = $input['shop_url'];
            if( isset($input['password']) && isset($input['confirm_password']) ){
                $model->password            = bcrypt($input['password']);
            }
            $key                       = Str::random(30);
            $model->invitation_code    = $key;
            $model->is_email_verified    = 1;
            $model->auth_key    = $input['auth_key'];
            $model->save();


            $name       = $model->name;
            $email      = $model->email;

            $link = route('distributor.verify', ['code' => $key]);
            // $link = route('distributor.login.index');

            $email_template = EmailTemplate::where('type','distributor_verify_email')->first();

            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);

            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $user_name = "Email: ".$input['email'];
            $password = "Password: ".$input['password'];
            $search = array("{{name}}","{{link}}","{{app_name}}","{{user_name}}","{{password}}");
            $replace = array($name,$link,env('APP_NAME'),$user_name,$password);
            $content = str_replace($search,$replace,$content);
            // dd($email,\App\Models\Contact::where('email', $email)->first());
            dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content,'Reseller'));

            Alert::success(__('Success'), __('Distributor added successfully!'))->persistent('Close')->autoclose(5000);

        }
        DistributorProductDetail::where('distributor_id', $model->id)->delete();
        if(isset($input['product_status'])){
            foreach($input['product_status'] as $product_id => $product_status)
            {
                $distributorProductDetail = new DistributorProductDetail;
                $distributorProductDetail->product_id = $product_id;
                $distributorProductDetail->distributor_id = $model->id;
                $distributorProductDetail->is_active = $product_status;
                $distributorProductDetail->extra_price = $input['extra_price'][$product_id] == null ? 0 : $input['extra_price'][$product_id];
                $distributorProductDetail->save();
            }
        }
        return redirect()->route('admin.distributor.index');
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
        $data['action'] = 'Edit';

        $data['id'] = $id;
        $data['model']  = Distributor::where('id',$id)->first();
        $data['products']  = Products::where('is_active',1)->get();
        return view('admin.distributors.form')->with($data);
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
}
