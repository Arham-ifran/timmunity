<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactCountry;
use App\Models\ContactCountryGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;
use File;

class ContactCountryGroupsController extends Controller
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

        if(!auth()->user()->can('Contact Country Groups Listing'))
        access_denied();
        if ($request->ajax()) {
            $data = ContactCountryGroup::all();
            $datatable = Datatables::of($data);
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Contact Country Groups','Delete Contact Country Groups'])) {
                $actions .=  auth()->user()->can('Edit Contact Country Groups') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-countries-groups/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Contact Country Groups')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contacts-countries-groups', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                    }
                }
                return $actions;
            });

            $datatable = $datatable->rawColumns(['name','action']);
            return $datatable->make(true);
        }


        return view('admin.contacts.countries-groups.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Contact Country Groups'))
        access_denied();
        $data = [];
        $data['contact_countries'] = ContactCountry::all();
        $data['action'] = 'Add';
        return view('admin.contacts.countries-groups.form')->with($data);
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
        $this->validate($request, [
            'name' => 'required|string|max:100',
        ]);

        if ($input['action'] == 'Add') {
            $model = new ContactCountryGroup();
            $model->fill($input)->save();
            if ($model)
                $model->contact_countries()->attach($request->country_id);
                Alert::success(__('Success'), __('Contact Countries Groups added successfully!'))->persistent('Close')->autoclose(5000);
            return redirect('admin/contacts-countries-groups');
        }
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
        if(!auth()->user()->can('Edit Contact Country Groups'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = ContactCountryGroup::with('contact_countries')->find($id)[0];
        $data['contact_countries'] = ContactCountry::all();

        return view('admin.contacts.countries-groups.edit')->with($data);
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
        $contact_country_group = ContactCountryGroup::find($id);
        $contact_country_group->name = $request->name;
        $contact_country_group->update();

//        if ($contact_country_group !== null) {
            $contact_country_group->contact_countries()->sync($request->country_id);
//        }
        Alert::success(__('Success'), __('Contact Countries Groups updated successfully!'))->persistent('Close')->autoclose(5000);
        return redirect('admin/contacts-countries-groups');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!auth()->user()->can('Delete Contact Country Groups'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactCountryGroup::find($id)[0];
        $model->delete();
        if ($model !== null) {
            $model->contact_countries()->detach($request->country_id);
        }

        Alert::success(__('Success'), __('Contact Countries Groups deleted successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/contacts-countries-groups');
    }
}
