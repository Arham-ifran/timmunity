<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\InvitationMailController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\Gate;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\Timezone;
use App\Models\Languages;
use DataTables;
use Hashids;
use Form;
use Alert;
use File;
use Image;
use Storage;
use Auth;
use App\Notifications\ResetPasswordNotification;

class AdminUserController extends Controller
{
    // public function __construct() {
    //     $this->middleware(['auth', 'isAdmin']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('User Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $archive = $request->get('is_archive');
            $data = Admin::with('languages')->where('id','!=',Auth::user()->id)->where('id','!=', 1)->latest()
                ->where(function ($data) use ($archive) {
                    if (isset($archive) && $archive != "") {
                        $data->where('is_archive', $archive);
                    }
                })
            ->get();
            $datatable = Datatables::of($data);
            $datatable->setRowId(function ($row) {
                return 'tr_' . $row->id;
            });
            $datatable->addColumn('delete_check', function (Admin $row) {
                $indv_check = '';
                $indv_check = '<input type="checkbox" name="deleteCheck[]" class="sub_chk checkbox-input countChecks" onclick="checkBoxActions(this)" data-id="' . $row->id . '">';
                return $indv_check;
            });
            $datatable->addColumn('name', function ($row) {
                return $row->firstname . ' ' . $row->lastname;
            });
            $datatable->editColumn('is_active', function ($row) {
                return ($row->is_active) ? '<span class="badge badge-success">'.__('Active').'</span>' : '<span class="badge badge-danger">'.__('Inactive').'</span>';
            });
            $datatable->addColumn('latest_authentication', function ($row) {
                return isset($row->email_verified_at) ? date('m/d/Y  h:i:s A', strtotime($row->email_verified_at)) : '';
            });
            $datatable->addColumn('login', function ($row) {
                return $row->email;
            });
            $datatable->addColumn('language', function ($row) {
                return @$row->languages->name;
            });
            $datatable->addColumn('role', function ($row) {
                return $row->roles()->pluck('name')->toArray();
            });
            $datatable->addColumn('action', function ($row) {

                    $actions = '';
                    if (auth()->user()->hasAnyPermission(['Delete User','Edit User'])) {

                    $actions .= auth()->user()->can('Edit User') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/settings/admin-user/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                    if(auth()->user()->can('Delete User')) {
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'DELETE',
                            'url' => ['admin/settings/admin-user', Hashids::encode($row->id)],
                            'style' => 'display:inline'
                        ]);

                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                        $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    }
                }
                    return $actions;
                });
            $datatable = $datatable->rawColumns(['delete_check', 'name', 'login', 'language', 'latest_authentication', 'is_active', 'action']);
            return $datatable->make(true);
        }
        return view('admin.settings.admin_users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New User'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        $data['roles'] = Role::get();
        $data['timezones'] = Timezone::orderBy('offset')->get();
        $data['languages'] = Languages::where('is_active', 1)->where('is_archive', 0)->get();
        return view('admin.settings.admin_users.form')->with($data);
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
        if ($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = Admin::findOrFail($id)[0];
            $this->validate($request, [
                'firstname' => 'required|string|max:30',
                'lastname' => 'required|string|max:30',
                'image' => 'image|mimes:jpeg,png,jpg|max:1024',
                'is_active' => 'required'
            ]);
            $role = $request->get('role_id');
            $model->update($input);
            // Update Record in contacts
            $contact = Contact::where('admin_id', $id)->first();
            if($contact){
                $contact->updated_by = Auth::user()->id;
                $contact->name = $model->firstname.' ' .$model->lastname;
                $contact->email = $model->email;
                $contact->update();
            }
            // Update Role Process
            if(Auth::user()->id != $model->id) {
                if (isset($role)) {
                    $model->roles()->sync($role);
                } else {
                    $model->roles()->detach();
                }
            }
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 5, '', ['item_id' => $model->id]);

            Alert::success(__('Success'), __('Admin User updated successfully!'))->persistent('Close')->autoclose(5000);
        } else if ($input['action'] == 'Duplicate') {
            $this->validate($request, [
                'firstname' => 'required|string|max:30',
                'lastname' => 'required|string|max:30',
                'email' => 'required|string|email|max:255|unique:contacts',
                'image' => 'image|mimes:jpeg,png,jpg|max:1024',
                'is_active' => 'required'
            ]);
            // Insert new record
            $model = new Admin();
            $model->fill($input)->save();

            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 8, '', ['item_id' => $model->id]);
            // Assign Role
            $role = $request->get('role_id');
            if (isset($role)) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $model->assignRole($role_r);
            }
            // Sent Invitation Email
            $name = $model->firstname . ' ' . $model->lastname;
            InvitationMailController::sendInvitationMail($name, $model->email, $model->invitation_code,'admin');
            Alert::success(__('Success'), __('Admin User added duplicate record successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'firstname' => 'required|string|max:30',
                'lastname' => 'required|string|max:30',
                'email' => 'required|string|email|max:255|unique:contacts',
                'image' => 'image|mimes:jpeg,png,jpg|max:1024',
                'is_active' => 'required'
            ]);

            $model = new Admin();
            $input['invitation_code'] = sha1(time());
            $model->fill($input)->save();

            // Duplicate record into contact table
            $contact = new Contact();
            $contact->created_by = Auth::user()->id;
            $contact->admin_id = $model->id;
            $contact->name = $model->firstname.' ' .$model->lastname;
            $contact->email = $model->email;
            $contact->type = 1;
            $contact->save();
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 4, '', ['item_id' => $model->id]);

            // Assign Role
            $role = $request->get('role_id');
            if (isset($role)) {
                    $role_r = Role::where('id', '=', $role)->firstOrFail();
                    $model->assignRole($role_r);
            }
            // Sent Invitation Email
            if ($model != null) {
                $name = $model->firstname . ' ' . $model->lastname;
                InvitationMailController::sendInvitationMail($name, $model->email, $model->invitation_code,'admin');
            }
            Alert::success(__('Success'), __('Admin User added successfully!'))->persistent('Close')->autoclose(5000);
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


        return redirect('admin/settings/admin-user');
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
        $id = Hashids::decode($id);
        if(auth()->user()->id != $id[0]){
            if(!auth()->user()->can('Edit User'))
            access_denied();
        }
        $data = [];
        $data['action'] = 'Edit';
        $data['roles'] = Role::all();
        $data['timezones'] = Timezone::orderBy('offset')->get();
        $data['languages'] = Languages::where('is_active', 1)->where('is_archive', 0)->get();
        // dd($id);
        $data['model'] = Admin::find($id[0]);
        // dd($data['model']);
        $data['assignedRoles'] = $data['model']->roles()->pluck('id')->toArray();
        return view('admin.settings.admin_users.form')->with($data);
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
        if(!auth()->user()->can('Delete User'))
        access_denied();
        $id = Hashids::decode($id);
        $model = Admin::find($id)[0];
        if ($model->id == Auth::user()->id) {
           Alert::warning(__('Warning'), __('Cannot perform delete on logged-in user.'))->persistent('Close')->autoclose(5000);
           return redirect('admin/settings/admin-user');
        }
        else {
            File::deleteDirectory(public_path() . '/storage/uploads/admin/' . Hashids::encode($id));
            $model->email =  $model->email.strftime(time()).'deleted';
            $model->save();
            if($model->contacts){
                $model->contacts->email =  $model->contacts->email.strftime(time()).'deleted';
                $model->contacts->save();
                $model->contacts->delete();
            }
            $model->delete();
            Alert::success(__('Success'), __('Admin User deleted Successfully.'))->persistent('Close')->autoclose(5000);
            return redirect('admin/settings/admin-user');
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 6, '', ['item_id' => $model->id]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        if(!auth()->user()->can('Delete User'))
        access_denied();
        $ids = $request->ids;
        $idsArr = explode(",", $ids);

        if (in_array(Auth::user()->id, $idsArr)) {
            $response = response()->json(['error' => __("You cannot delete the user you're currently logged in as.")]);
        } else {
            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 7, '', ['item_id' => implode(',', $idsArr)]);

            Admin::whereIn('id', $idsArr)->delete();
            $response = response()->json(['success' => __('Admin Users Deleted successfully.')]);
        }

        return $response;
    }

    public function duplicate($id)
    {
        if(!auth()->user()->can('Duplicate User'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Duplicate';
        $data['roles'] = Role::all();
        $data['timezones'] = Timezone::Orderby('offset')->get();
        $data['languages'] = Languages::where('is_active', 1)->get();
        $data['model'] = Admin::find($id)[0];
        $data['keyword'] = '(Copy)';
        $data['assignedRoles'] = $data['model']->roles()->pluck('id')->toArray();
        return view('admin.settings.admin_users.form')->with($data);
    }

    public function isArchiveUser(Request $request)
    {
        if(!auth()->user()->can('Archive / Unarchive User'))
        access_denied();
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        if (Hashids::decode($input['login_id']) == $id) {
            $response = response()->json(['error' => __("You cannot deactivate the user you're currently logged in as.")]);
        } else {
            $archive = $input['is_archive'];
            if ($archive == 1)
                $archiveMSg = __('Archived');
            else
                $archiveMSg = __('Unarchived');
            Admin::where('id', $id)->update(['is_archive' => $archive]);

            //EVENT LOG START
            \App\Events\UserEvents::dispatch('admin', 9, $archiveMSg, ['item_id' => $id]);

            $response = response()->json(['success' => __('Admin User') .' '. $archiveMSg .' '. __('Successfully.')]);
        }
        return $response;
    }
}
