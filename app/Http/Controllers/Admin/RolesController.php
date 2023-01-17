<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Modules;
use DataTables;
use Hashids;
use Form;
use Alert;
use Auth;
use DB;

class RolesController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'isAdmin']);
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Roles Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = Role::orderBy('id', 'desc')->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('role', function ($row) {
                $role = $row->name;
                return $role;
            });
            $datatable->addColumn('permissions', function ($row) {
                $permission = makeToPopover($row->permissions()->pluck('name')->implode('|'));
                return $permission;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                // if (!DB::table('model_has_roles')->where('role_id',$row->id)->where('model_id', Auth::user()->id)->exists() && $row->name != 'Administrator') {
                    if (auth()->user()->hasAnyPermission(['Edit Role','Delete Role'])) {
                        $actions .= auth()->user()->can('Edit Role') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/settings/roles/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                        if(auth()->user()->can('Delete Role')) {
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'DELETE',
                            'url' => ['admin/settings/roles', Hashids::encode($row->id)],
                            'style' => 'display:inline'
                        ]);

                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick'=>'deleteAlert(this)','class' => 'delete-form-btn btn btn-default btn-icon']);
                        $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                        }
                    }
                // }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['role', 'permissions', 'action']);
            return $datatable->make(true);
        }
        return view('admin.settings.roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Role'))
        access_denied();
        $data = [];
        $data['modules'] = Modules::with('permissions')->get();
        $data['action'] = 'Add';
        return view('admin.settings.roles.form')->with($data);
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
            $role = Role::findOrFail($id);
            $this->validate($request, [
                'name' => 'required|max:255|unique:roles,name,' . $id,
                'permissions' => 'required',
            ]);

            $input = $request->except(['permissions']);

            $permissions = $request->get('permissions');
            $role->fill($input)->save();
            $p_all = Permission::all();

            foreach ($p_all as $p) {
                $role->revokePermissionTo($p);
            }

            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form permission in db
                $role->givePermissionTo($p);
            }

            Alert::success(__('Success'), __('Role updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {
            $this->validate(
                $request,
                [
                    'name' => 'required|unique:roles|max:255',
                    'permissions' => 'required',
                ]
            );

            $role = new Role();
            $role->name = $input['name'];
            $permissions = $input['permissions'];
            $role->save();

            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                $role = Role::where('name', '=', $input['name'])->first();
                $role->givePermissionTo($p);
            }

            Alert::success(__('Success'), __('Role added successfully!'))->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/settings/roles');
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
        if(!auth()->user()->can('Edit Role'))
        access_denied();
        $data = [];
        $data['role'] = Role::findOrFail(Hashids::decode($id)[0]);
        $data['assignedPermission'] = $data['role']->permissions()->pluck('id')->toArray();
        // $data['permissions'] = Permission::all();
        // $data['modules_permissions'] = Modules::with('permissions')->get();
        $data['modules'] = Modules::with('permissions')->get();
        $data['action'] = 'Edit';
        return view('admin.settings.roles.form')->with($data);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Role'))
        access_denied();
        $id = Hashids::decode($id)[0];
        if (DB::table('model_has_roles')->where('role_id', $id)->exists()) {
            $msg = __('<p style="font-size: 20px;font-weight:700">The role cannot be deleted!</p>This role assigned to the user.');
            Alert::html(__('Warning'), $msg, 'warning')->persistent('Close')->autoclose(6000);
        }
        else {
        $role = Role::findOrFail($id);
        $role->delete();
        Alert::success(__('Success'), __('Role deleted Successfully.'))->persistent('Close')->autoclose(5000);
        }
        return redirect('admin/settings/roles');
    }
}
