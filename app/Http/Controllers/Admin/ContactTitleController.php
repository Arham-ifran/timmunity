<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactTag;
use App\Models\ContactTitle;
use Doctrine\DBAL\Schema\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;
use File;
class ContactTitleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Titles Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = ContactTitle::all();
            $datatable = Datatables::of($data);

            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Delete Contact Titles','Edit Contact Titles'])) {
                $actions .=auth()->user()->can('Edit Contact Titles') ?  '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/contacts-titles/". Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Contact Titles')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/contacts-titles', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit','class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                }
             }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['name','action']);
            return $datatable->make(true);
        }
            return view('admin.contacts.titles.index',$data);


    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Contact Titles'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';

        return view('admin.contacts.titles.form')->with($data);
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
            $model = ContactTitle::findOrFail($id)[0];
            $this->validate($request, [
                'title' => ['required', 'string','max:50'],
                "abbreviation"=> ['required', 'string','max:30'],
                "salutation"=> ['required', 'string','max:30'],
            ]);

            $model->update($input);

            Alert::success(__('Success'), __('Contact Title updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'title' => ['required', 'string', 'max:50'],
                "abbreviation"=> ['required', 'string', 'max:30'],
                "salutation"=> ['required', 'string', 'max:30'],
            ]);

            $model = new ContactTitle();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Contact Title added successfully!'))->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/contacts-titles');
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
        if(!auth()->user()->can('Edit Contact Titles'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';

        $data['model'] = ContactTitle::find($id)[0];

        return view('admin.contacts.titles.form')->with($data);
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
        if(!auth()->user()->can('Delete Contact Titles'))
        access_denied();
        $id = Hashids::decode($id);
        $model = ContactTitle::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Contact Title deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
}
