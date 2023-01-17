<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DataTables;
use Hashids;
use Form;
use Alert;
use DB;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $data = [];
        if ($request->ajax()) {
            $data = Permission::all();
            // $modules = DB::table('modules')->get()->toArray();
            // return json_encode($data);
            $datatable = Datatables::of($data);
            $datatable->editColumn('permission', function ($row) {
                $permission = $row->name;
                return $permission;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/settings/permissions/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>';

                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/settings/permissions', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick'=>'deleteAlert(this)','class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                return $actions;
            });
            $datatable = $datatable->rawColumns(['permission', 'action']);
            return $datatable->make(true);
        }
        return view('admin.settings.permissions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['roles'] = Role::get();
        $data['action'] = 'Add';
        return view('admin.settings.permissions.form')->with($data);
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
            $id = $input['id'];
            $permission = Permission::findOrFail($id);

            $this->validate($request, [
                'name' => 'required|max:255',
            ]);

            $input = $request->all();

            $roles = $input['roles'] ?? null;
            $permission->fill($input)->save();
            $p_all = Role::all();

            foreach ($p_all as $p) {
                $permission->removeRole($p);
            }
            if (isset($roles)) {
                foreach ($roles as $role) {
                    $p = Role::where('id', '=', $role)->firstOrFail(); //Get corresponding form roles in db
                    $permission->assignRole($p);
                }
            }

            Alert::success(__('Success'), __('Permission updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {
            $this->validate($request, [
                'name' => 'required|max:255',
            ]);

            $name = $request->get('name');
            $permission = new Permission();

            $permission->name = $name;
            $roles = $request->get('roles');
            $permission->save();

            if (!empty($request->get('roles'))) {
                foreach ($roles as $role) {
                    $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record

                    $permission = Permission::where('name', '=', $name)->first();
                    $r->givePermissionTo($permission);
                }
            }

            Alert::success(__('Success'), __('Permission added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/settings/permissions');
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
        $data['roles'] = Role::all();
        $data['permission'] = Permission::find(Hashids::decode($id)[0]);
        $data['assignedRoles'] = $data['permission']->roles()->pluck('id')->toArray();
        $data['action'] = 'Edit';
        return view('admin.settings.permissions.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail(Hashids::decode($id)[0]);
        if ($permission->name == "Administration & Permissions") {

            Alert::warning('Warning', 'This Permission cannot be deleted.')->persistent('Close')->autoclose(5000);
            return redirect('admin/settings/permissions');
        }

        $permission->delete();
        Alert::success(__('Success'), __('Permission deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/settings/permissions');
    }
}
