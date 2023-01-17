<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tax;
use DataTables;
use Hashids;
use Alert;
use Form;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Taxes Listing'))
        access_denied();

        $data = [];
        if ($request->ajax()) {
            $data = Tax::orderBy('id','desc')->get();

            $datatable = Datatables::of($data);
            $datatable->editColumn('type',function($row){
                switch ($row->type) {
                    case 0:
                        return __('None');
                        break;
                    case 1:
                        return __('Sales');
                        break;
                    case 2:
                        return __('Purchase');
                        break;
                }
            });
            $datatable->editColumn('computation',function($row){
                switch ($row->computation) {
                    case 0:
                        return __('Fixed');
                        break;
                    case 1:
                        return __('Percentage');
                        break;
                }
            });
            $datatable->editColumn('applicable_on',function($row){
                switch ($row->applicable_on) {
                    case 0:
                        return __('Customers');
                        break;
                    case 1:
                        return __('Vendors');
                        break;
                }
            });
            $datatable->editColumn('is_active',function($row){
                return ($row->is_active)? '<span class="badge badge-success">'.__('Active').'</span>' : '<span class="badge badge-danger">'.__('Inactive').'</span>';
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Tax','Delete Tax']))
                {
                    $actions .= auth()->user()->can('Edit Tax') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/sales-management/configuration/taxes/" . Hashids::encode($row->id) . '/edit') . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                    if(auth()->user()->can('Delete Tax')) {
                        $actions .= '&nbsp;' . Form::open([
                                'method' => 'DELETE',
                                'url' => ['admin/sales-management/configuration/taxes', Hashids::encode($row->id)],
                                'style' => 'display:inline'
                            ]);
                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['type' => 'submit', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                        $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    }
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['is_active','action']);
            return $datatable->make(true);
        }
        return view('admin.taxes.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add Tax'))
        access_denied();

        $data = [];
        $data['action'] = 'Add';
        return view('admin.taxes.form')->with($data);
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
            $model = Tax::findOrFail($id)[0];
            $this->validate($request, [
                'id' => 'required',
                'name' => 'required|string|max:100',
                'type' => 'required',
                'computation' => 'required',
                'applicable_on' => 'required',
                'amount' => 'required',
            ]);
            $input['is_active'] = 1;
            $model->update($input);

            Alert::success(__('Success'), __('Tax updated successfully!'))->persistent('Close')->autoclose(5000);
        } else {

            $this->validate($request, [
                'name' => 'required|string|max:100',
                'type' => 'required',
                'computation' => 'required',
                'applicable_on' => 'required',
                'amount' => 'required',
            ]);

            $model = new Tax();
            $input['is_active'] = 1;
            $model->fill($input)->save();


            Alert::success(__('Success'), __('Tax added successfully!'))->persistent('Close')->autoclose(5000);
        }


        return redirect('admin/sales-management/configuration/taxes');
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
        if(!auth()->user()->can('Edit Tax'))
        access_denied();

        $data = [];
        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['model'] = Tax::find($id)[0];
        return view('admin.taxes.form')->with($data);
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
        if(!auth()->user()->can('Delete Tax'))
        access_denied();

        $id = Hashids::decode($id);
        $model = Tax::find($id)[0];
        $model->delete();
        Alert::success(__('Success'), __('Tax Deleted Successfully.'))->persistent('Close')->autoclose(5000);
        return redirect('admin/sales-management/configuration/taxes');
    }
}
