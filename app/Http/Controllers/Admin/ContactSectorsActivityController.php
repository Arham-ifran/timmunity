<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactSectorsActivitie;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;
use File;

class ContactSectorsActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Sector of Activities Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = ContactSectorsActivitie::all();
            $datatable = Datatables::of($data);
            $datatable->editColumn('description', function ($row) {

                $activities_description = translation($row->id,6,app()->getLocale(),'description',$row->description);
                return $activities_description;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Contact Sector of Activities','Delete Contact Sector of Activities'])) {
                $actions .= auth()->user()->can('Edit Contact Sector of Activities') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-sectors-activities/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Contact Sector of Activities')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contacts-sectors-activities', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                 }
               }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['description', 'action']);
            return $datatable->make(true);
        }
        return view('admin.contacts.sectors-activities.index', $data);
    }


    public function create()
    {
        $data = [];
        $data['action'] = 'Add';
        return view('admin.contacts.sectors-activities.form')->with($data);
    }


    public function store(Request $request)
    {

        $input = $request->all();
        if ($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = ContactSectorsActivitie::findOrFail($id)[0];
            $this->validate($request, [
                'name' => 'required|string|max:50',
                'description' => 'required|string|max:200',
            ]);

            $model->update($input);

            Alert::success(__('Success'), __('Contact Sector Activity updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => 'required|string|max:50',
                'description' => 'required|string|max:200',
            ]);

            $model = new ContactSectorsActivitie();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Contact Sector Activity added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/contacts-sectors-activities');
    }

    public function edit($id)
    {

        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';

        $data['model'] = ContactSectorsActivitie::find($id)[0];

        return view('admin.contacts.sectors-activities.form')->with($data);
    }


    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Contact Sector of Activities'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactSectorsActivitie::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Sector Activity deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/contacts-sectors-activities');
    }
}
