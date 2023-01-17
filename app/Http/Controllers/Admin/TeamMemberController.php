<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\InvitationMailController;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\SalesTeamsMembers;
use Hashids;
use Illuminate\Support\Facades\Validator;
use Auth;
use File;
use Image;
use Storage;
class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['salesteam'] = SalesTeam::all();
        return view('admin.sales.sales-team.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        if ($request->ajax()) {
            $html = view('admin.sales.sales-team.modal-box.add-member')->render();
            return response()->json([
                'html' => $html,
                'status' => 1,
                'messagetype' => 'success',
            ], 200, ['Content-Type' => 'application/json']);
        }
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
        if($input['action'] == "Edit") {
            $messages = [
                'firstname.required' => __('First Name is required!'),
                'lastname.required' => __('Last Name is required!'),
                'email.required' => __('Email is required!'),
                'email.email' => __('Please enter valid email address!'),
            ];
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:30',
                'lastname' => 'required|string|max:30',
            ],$messages);
        }
        else {
            $messages = [
                'firstname.required' => __('First Name is required!'),
                'lastname.required' => __('Last Name is required!'),
                'email.required' => __('Email is required!'),
                'email.email' => __('Please enter valid email address!'),
            ];
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:30',
                'lastname' => 'required|string|max:30',
                'email' => 'required|string|email|max:255|unique:admins',
            ],$messages);
        }
        $status = 1;
        if ($validator->fails()) {

            $data = $validator->messages();;
            $status = 0;
        }
        else {
            if($input['action'] == "Edit") {
                $id = Hashids::decode($input['id']);
                $model = Admin::findOrFail($id)[0];
                $model->firstname = $input['firstname'];
                $model->lastname = $input['lastname'];
                $model->email = $input['email'];
                $model->phone = $input['phone'];
                $model->mobile = $input['mobile'];
                $model->is_sales_team_member = 1;
                $model->invitation_code = sha1(time());
                $model->lang_id = Auth::user()->lang_id;
                $model->timezone_id = Auth::user()->timezone_id;
                $model->is_active = 1;
                $model->update();
                // Update duplicate record into contacts
                $ContacModel = Contact::where('admin_id', $id)->first();
                $ContacModel->updated_by = Auth::user()->id;
                $ContacModel->name = $input['firstname'].' '.$input['lastname'];
                $ContacModel->email = $input['email'];
                $ContacModel->update();
            }
            else {
                $model = new Admin();
                $model->firstname = $input['firstname'];
                $model->lastname = $input['lastname'];
                $model->email = $input['email'];
                $model->phone = $input['phone'];
                $model->mobile = $input['mobile'];
                $model->is_sales_team_member = 1;
                $model->invitation_code = sha1(time());
                $model->lang_id = Auth::user()->lang_id;
                $model->timezone_id = Auth::user()->timezone_id;
                $model->is_active = 1;
                $model->save();
                // Add duplicate record into contacts
                $ContacModel = new Contact();
                $ContacModel->created_by = Auth::user()->id;
                $ContacModel->admin_id = $model->id;
                $ContacModel->name = $input['firstname'].' '.$input['lastname'];
                $ContacModel->email = $input['email'];
                $ContacModel->save();
            // Sent Invitation Email
            if ($model != null) {
                $name = $model->firstname . ' ' . $model->lastname;
                InvitationMailController::sendInvitationMail($name, $model->email, $model->invitation_code,'admin');
            }
        }
            //MAKE DIRECTORY
            $upload_path = public_path() . '/storage/uploads/admin/' . Hashids::encode($model->id);
            if (!File::exists(public_path() . '/storage/uploads/admin/' . Hashids::encode($model->id))) {

                File::makeDirectory($upload_path, 0777, true);
            }
            if (!empty($request->files) && $request->hasFile('image')) {
                $file      = $request->file('image');
                $file_name = $file->getClientOriginalName();
                $type      = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size      = $file->getSize();
                $size_mbs  = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'profile-' . time() . '.' . $type;
                    $old_file  = public_path() . '/storage/uploads/admin/' . Hashids::encode($model->id) . '/' . $model->image;
                    if (file_exists($old_file) && !empty($model->image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/admin/') . Hashids::encode($model->id) . '/' . $file_temp_name;

                    // if ($size_mbs >= 2) {
                    //     $img = Image::make($file)->fit(300, 300)->save($path);
                    // } else {
                    //     $img = Image::make($file)->resize(300, 300)->save($path);
                    // }
                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);
                    $update = array('image' => $file_temp_name);

                    Admin::where('id', $model->id)->update($update);
                }
            }
            $img_name = isset($file_temp_name) ? $file_temp_name : $model->image;
            $img = '<img src="' . checkImage(asset("storage/uploads/admin/" . Hashids::encode(@$model->id) . '/' . @$img_name),'avatar5.png') . '"  alt="User Image" width="100%" height="100%">';
            
            $data = ['name' => $model->firstname . ' '.$model->lastname , 'id' => Hashids::encode($model->id) , 'image' => $img];
        }
       return response()->json([
            'data' => $data,
            'status' => $status,
            'messagetype' => 'success',
        ], 200, ['Content-Type' => 'application/json']);
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
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = SalesTeam::find($id)[0];
        return view('admin.sales.sales-team.sales_team_form')->with($data);
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

    public function updateTeamMember(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $action = $input['action'];
        $model = Admin::find($id)[0];
        $img = '<img id="memberImagePreview" src="' . checkImage(asset("storage/uploads/admin/" . Hashids::encode($model->id) . '/' . @$model->image),'avatar5.png') . '"  width="100%" height="100%" alt="' .asset("backend/dist/img/avatar5.png"). '">';
        return response()->json([
                    'model'=> $model, 
                    'image' => $img,
                    'action' => $action
                ]);
    }
    // Method for Remove Team Member 
    public function removeTeamMember(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id'])[0];       
        SalesTeamsMembers::where('sales_team_id', $team_id)->where('member_id', $id)->delete();
        return response()->json([
                    'status'=> 1, 
                ]);
    }
}