<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactCountry;
use App\Models\ContactFedState;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;
use File;


class ContactFedStatesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       if(!auth()->user()->can('Contact Fed. States Listing'))
       access_denied();
       $contact_countries = ContactCountry::all();
       if ($request->ajax()) {
        $data = ContactFedState::orderBy('id','desc')->get();
        $datatable = Datatables::of($data);
        $datatable->addColumn('action', function ($row) {
            $actions = '';
            if (auth()->user()->hasAnyPermission(['Edit Contact Fed. States','Delete Contact Fed. States'])) {
            $actions .= auth()->user()->can('Edit Contact Fed. States') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-fed-states/". Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
            if(auth()->user()->can('Delete Contact Fed. States')) {
            $actions .= '&nbsp;' . Form::open([
                'method' => 'DELETE',
                'url' => ['admin/contacts-fed-states', Hashids::encode($row->id)],
                'style' => 'display:inline'
            ]);

            $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit','class' => 'delete-form-btn btn btn-default btn-icon']);
            $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

            $actions .= Form::close();
              }
            }
            return $actions;
        })
        ->addColumn('contact_countries', function($row) {

            return $row->contact_countries->name;

         });

        $datatable = $datatable->rawColumns(['name','action']);
        return $datatable->make(true);
    }
        return view('admin.contacts.fed-states.index',)->with('data','contact_countries');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Contact Fed. States'))
        access_denied();
        $data = [];
        $data['contact_countries'] = ContactCountry::all();
        $data['action'] = 'Add';
        return view('admin.contacts.fed-states.form')->with($data);


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
            $model = ContactFedState::findOrFail($id)[0];
            $this->validate($request, [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:15',
                'country_id' => 'required',
            ]);

            $model->update($input);

            Alert::success(__('Success'), __('Contact Fed. State updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:15',
                'country_id' =>  'required',
            ]);

            $model = new ContactFedState();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Contact Fed. State added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/contacts-fed-states');
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
        if(!auth()->user()->can('Edit Contact Fed. States'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['contact_countries'] = ContactCountry::all();
        $data['model'] = ContactFedState::find($id)[0];
        return view('admin.contacts.fed-states.form')->with($data);

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


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Contact Fed. States'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactFedState::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Fed. State Deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/contacts-fed-states');
     }
}
