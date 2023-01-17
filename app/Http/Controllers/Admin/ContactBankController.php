<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactBank;
use App\Models\ContactCountry;
use Illuminate\Http\Request;
use DataTables;
use Hashids;
use Form;
use Alert;
class ContactBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Banks Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = ContactBank::all();

            $datatable = Datatables::of($data);
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Contact Banks','Delete Contact Banks'])) {
                $actions .= auth()->user()->can('Edit Contact Banks') ?  '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-banks/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Contact Banks')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contacts-banks', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick'=>'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
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
        return view('admin.contacts.banks.index', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Contact Banks'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        $data['contact_countries'] = ContactCountry::all();
        return view('admin.contacts.banks.form')->with($data);
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
            $model = ContactBank::findOrFail($id)[0];
            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
                "bank_identifier_code" => ['required', 'string', 'max:20'],
                "phone" => ['required', 'string', 'max:20'],
                "email" => ['required', 'string', 'max:255'],
                "street_1" => ['required', 'string', 'max:255'],
                "city" => ['required', 'string', 'max:25'],
                "zipcode" => ['required', 'string', 'max:20'],
                "state" => ['required', 'string', 'max:20'],
                "country_id" => ['required'],

            ]);

            $model->update($input);

            Alert::success(__('Success'), __('Contact Bank updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => ['required', 'string', 'max:100'],
                "bank_identifier_code" => ['required', 'string', 'max:20'],
                "phone" => ['required', 'string', 'max:20'],
                "email" => ['required', 'string', 'max:255'],
                "street_1" => ['required', 'string', 'max:255'],
                "city" => ['required', 'string', 'max:25'],
                "zipcode" => ['required', 'string', 'max:20'],
                "state" => ['required', 'string', 'max:20'],
                "country_id" => ['required'],
            ]);

            $model = new ContactBank();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Contact Bank added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/contacts-banks');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('Edit Contact Banks'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['contact_countries'] = ContactCountry::all();
        $data['model'] = ContactBank::find($id)[0];
        return view('admin.contacts.banks.form')->with($data);

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
        if(!auth()->user()->can('Delete Contact Banks'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactBank::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Bank deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
}
