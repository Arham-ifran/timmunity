<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use DataTables;
use Hashids;
use Alert;
use Form;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        if ($request->ajax()) {
            $data = EmailTemplate::all();
            $datatable = Datatables::of($data);
            $datatable->addColumn('title',function($row){
                return $row->title;
            });
            $datatable->addColumn('email_template_type', function ($row) {
                return $row->email_template_type == 1 ? __('Welcome Email') : __('Signup Email');
            });
            $datatable->addColumn('status',function($row){
                return $row->status == 0 ? __('In Active') : __('Active');
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . route("admin.email-templates.edit", Hashids::encode($row->id) ) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>';

                $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => [route("admin.email-templates.destroy", Hashids::encode($row->id) )],
                        'style' => 'display:inline'
                    ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                return $actions;
            });
            $datatable = $datatable->rawColumns(['action']);
            return $datatable->make(true);
        }
        return view('admin.email-templates.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data = [];
        $data['action'] = 'Add';
        return view('admin.email-templates.form')->with($data);
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
        $input['header'] = str_replace("\n", "", $input['header']);
        $input['header'] = str_replace("\r", "", $input['header']);
        $input['header'] = str_replace("\t", "", $input['header']);
        $input['content'] = str_replace("\n", "", $input['content']);
        $input['content'] = str_replace("\r", "", $input['content']);
        $input['content'] = str_replace("\t", "", $input['content']);
        $input['footer'] = str_replace("\n", "", $input['footer']);
        $input['footer'] = str_replace("\r", "", $input['footer']);
        $input['footer'] = str_replace("\t", "", $input['footer']);
        $input['welcome_content'] = str_replace("\n", "", $input['welcome_content']);
        $input['welcome_content'] = str_replace("\t", "", $input['welcome_content']);
        $input['welcome_content'] = str_replace("\r", "", $input['welcome_content']);

        if ($input['action'] == 'Edit') {
            $id = Hashids::decode($input['id']);
            $model = EmailTemplate::findOrFail($id)[0];

            $model->update($input);

            Alert::success(__('Success'), __('Email Template updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {



            $model = new EmailTemplate();
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Email Template added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect()->route('admin.email-templates.index');
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
    public function edit($id, Request $request)
    {
        $data = [];
        $data['action'] = 'Edit';
        $id = Hashids::decode($id)[0];

        $data['model'] = EmailTemplate::where('id',$id)->first();
        return view('admin.email-templates.form')->with($data);
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
        $id = Hashids::decode($id)[0];

        EmailTemplate::where('id',$id)->delete();

        Alert::success(__('Success'), __('Email Template deleted successfully!'))->persistent('Close')->autoclose(5000);


        return redirect()->route('admin.email-templates.index');
    }

    public function get_template_detail($id)
    {
        $template_details = EmailTemplate::where('id', $id)->first();
        if($template_details){
            $template_details->header = translation( $template_details->id,14,app()->getLocale(),'header',$template_details->header);
            $template_details->content = translation( $template_details->id,14,app()->getLocale(),'content',$template_details->content);
        }
        return $template_details ? $template_details : '' ;
    }
}
