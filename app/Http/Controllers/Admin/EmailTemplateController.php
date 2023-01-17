<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Email Templates Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = EmailTemplate::orderBy('created_at','ASC')->get();
            // dd(json_encode($data,true));
            $datatable = Datatables::of($data);
            $datatable->editColumn('name', function ($row) {
                $name = ucwords(str_replace('_', ' ', $row->type));
                return $name;
            });
            $datatable->editColumn('subject', function ($row) {

                $subject = translation($row->id,14,app()->getLocale(),'subject',$row->subject);
                return $subject;
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';

                $actions .= auth()->user()->can('Edit Email Templates') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/settings/email-templates/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                return $actions;
            });
            $datatable = $datatable->rawColumns(['name','subject', 'action']);
            return $datatable->make(true);
        }
        return view('admin.settings.email-templates.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add New Email Templates'))
        access_denied();
        $data = [];
        $data['action'] = 'Add';
        return view('admin.settings.email-templates.form')->with($data);
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
        $input['status'] = 1;
        if($input['action'] == 'Edit')
        {
            $id = Hashids::decode($input['id']);
            $this->validate($request, [
                'subject' => 'required|string|max:250',
                'type' => 'max:250|unique:email_templates,type,'.$id[0],
                'content' => 'required|string',
            ]);
            $model = EmailTemplate::findOrFail($id)[0];
            $model->update($input);
            Alert::success(__('Success'), __('Email Template has been updated successfully.'))->persistent('Close')->autoclose(5000);
        }
        else {

            $this->validate($request, [
                'subject' => 'required|string|max:250',
                'type' => 'max:250|unique:email_templates',
                'content' => 'required|string',
            ]);
            $model = new EmailTemplate();
            $model->fill($input)->save();
            Alert::success(__('Success'), __('Email Template has been added successfully.'))->persistent('Close')->autoclose(5000);
        }
        return redirect('admin/settings/email-templates');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if(!auth()->user()->can('Edit Email Templates'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = EmailTemplate::find($id)[0];
        return view('admin.settings.email-templates.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Email Templates'))
        access_denied();
        $id = Hashids::decode($id);
        $model = EmailTemplate::find($id)[0];
        $model->delete();

        Alert::success(__('Success'), __('Email Template has been deleted successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/setings/email-templates');
    }
}
