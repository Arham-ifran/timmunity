<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use App\Models\ContactTag;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;
use File;
class ContactTagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Tags Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = ContactTag::orderBy('id','desc')->get();
            $datatable = Datatables::of($data);

            $datatable->addColumn('active', function ($row) {
                if($row->active == 1){
                    return '<span class="badge badge-success">'.__('Active').'</span>';
                }else{
                    return '<span class="badge badge-danger">'.__('In Active').'</span>';
                }
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Delete Contact Tags','Edit Contact Tags'])) {
                $actions .= auth()->user()->can('Edit Contact Tags') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-tags/" . Hashids::encode($row->id)) . '/edit" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Contact Tags')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contacts-tags', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit','class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                }
               }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['name','active', 'action']);
            return $datatable->make(true);
        }
            return view('admin.contacts.tags.index',$data);

    }


    public function create()
    {
        if(!auth()->user()->can('Add New Contact Tags'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        return view('admin.contacts.tags.form')->with($data);
    }


    public function store(Request $request)
    {

        $input = $request->all();
        if ($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = ContactTag::findOrFail($id)[0];
            $this->validate($request, [
                'name' => 'required|string|max:100',
                'active' => 'required',
            ]);

            $model->update($input);

            Alert::success(__('Success'), __('Contact Tag updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => 'required|string|max:100',
                'active' => 'required',
            ]);

            $model = new ContactTag();
            $model->fill($input)->save();

            Alert::success(__('Success'), __('Contact Tag added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/contacts-tags');
    }

    public function edit($id)
    {
        if(!auth()->user()->can('Edit Contact Tags'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';

        $data['model'] = ContactTag::with('contacts')->find($id)[0];

        return view('admin.contacts.tags.form')->with($data);
    }


    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Contact Tags'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactTag::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Tag deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/contacts-tags');
    }
}
