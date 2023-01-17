<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplateLabel;
use App\Models\EmailTemplate;
use Yajra\DataTables\DataTables;
use Session;
use Hashids;
use Form;
use Alert;

class EmailTemplateLabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if(!auth()->user()->can('Email Template Labels Listing'))
        access_denied();
        $data = [];
        $data['email_templates'] = EmailTemplate::where('status',1)->get();
        if($request->ajax())
        {
            if($request->get('email_template_id'))
               $email_template_id = Hashids::decode($request->get('email_template_id'))[0];
            else
               $email_template_id = null;
            $data = EmailTemplateLabel::where('status', 1)
                ->where(function ($data) use ($email_template_id) {
                    if (isset($email_template_id) && $email_template_id != "") {
                        $data->where('email_template_id', $email_template_id);
                    }
                })
            ->get();
            $datatable = Datatables::of($data);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->addColumn('email_template', function($row)
            {
                return $row->emailTemplate->subject;
            });
            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Email Template Labels',' Delete Email Template Labels'])) {
                $actions .= auth()->user()->can('Edit Email Template Labels') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/settings/email-template-labels/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                if(auth()->user()->can('Delete Email Template Labels')) {
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => ['admin/settings/email-template-labels', Hashids::encode($row->id)],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
              }
            }
                return $actions;
            });

            $datatable = $datatable->rawColumns(['action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.settings.email-template-labels.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(!auth()->user()->can('Add New Email Template Labels'))
        access_denied();
        $data['model'] = new EmailTemplateLabel();
        $data['action'] = "Add";
        $data['email_templates'] = EmailTemplate::where('status',1)->get();
        return view('admin.settings.email-template-labels.form')->with($data);
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
        $this->validate($request, [
            'email_template_id' => 'required',
            'label' => 'required',
            'value' => 'required'
        ]);

        if($input['action'] == 'Add')
        {
            $values = $input['value'];
            foreach($input['label'] as $key => $label)
            {
                $value = $values[$key];

                if($label != NULL && $value != NULL)
                {

                    $input['label']  = $label;
                    $input['value']  = $value;

                    $model = new EmailTemplateLabel();
                    $model->fill($input);
                    $model->save();
                }
            }

            Alert::success(__('Success'), __('Email Template Labels have been created successfully.'))->persistent('Close')->autoclose(5000);
        }
        else
        {
            $input['label']  = $input['label'][0];
            $input['value']  = $input['value'][0];
            $id = Hashids::decode($input['id']);
            $model = EmailTemplateLabel::findOrFail($id)[0];
            $model->update($input);
            Alert::success(__('Success'), __('Email Template Label has been updated successfully.'))->persistent('Close')->autoclose(5000);
        }

        return redirect('admin/settings/email-template-labels');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if(!auth()->user()->can('Edit Email Template Labels'))
        access_denied();
        if(!isset(Hashids::decode($id)[0]))
            abort(404);

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['email_templates'] = EmailTemplate::where('status',1)->get();
        $data['model'] = EmailTemplateLabel::findOrFail($id);
        return view('admin.settings.email-template-labels.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Email Template Labels'))
        access_denied();
        $id = Hashids::decode($id)[0];
        EmailTemplateLabel::destroy($id);
        Alert::success(__('Success'), __('Email Template Label has been deleted successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/settings/email-template-labels');
    }
}
