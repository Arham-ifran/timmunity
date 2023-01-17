<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUsQueries;
use Yajra\DataTables\DataTables;
use Hashids;
use Form;
use Alert;

class ContactQueries extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Contact Us Queries Listing'))
        access_denied();
        $data = [];
        if ($request->ajax()) {
            $data = ContactUsQueries::orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('name', function ($row) {
                $name = ucwords($row->name);
                return $name;
            });
            $datatable->editColumn('subject', function ($row) {

                $subject = translation($row->id,28,app()->getLocale(),'subject',$row->subject);
                return $subject;
            });
            $datatable->editColumn('status', function ($row) {
                return ($row->status) ? '<span class="badge badge-success">'.__('Completed').'</span>' : '<span class="badge badge-danger">'.__('Pending').'</span>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if(auth()->user()->can('Edit Contact Us Query')){
                    $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/website/contact-us-queries/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>';
                    $actions .= '&nbsp;' . Form::open([
                        'method' => 'DELETE',
                        'url' => [route('admin.contact-us-queries.destroy',Hashids::encode($row->id))],
                        'style' => 'display:inline'
                    ]);

                    $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                    $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                    $actions .= Form::close();
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['name','subject','status', 'action']);
            return $datatable->make(true);
        }
        return view('admin.website.contact_us_queries.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $model = ContactUsQueries::findOrFail($id)[0];
            $model->update($input);
            Alert::success(__('Success'), __('Contact us query updated successfully!'))->persistent('Close')->autoclose(5000);
        }
        return redirect('admin/website/contact-us-queries');
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
        if(!auth()->user()->can('Edit Contact Us Query'))
        access_denied();
        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = ContactUsQueries::find($id)[0];
        return view('admin.website.contact_us_queries.form')->with($data);
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
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = ContactUsQueries::where('id',$id)->delete();
        return view('admin.website.contact_us_queries.index')->with($data);
    }
}
